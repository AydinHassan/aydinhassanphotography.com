<script>

import Info from "./icons/Info.vue";
import Camera from "./icons/Camera.vue";
import Date from "./icons/Date.vue";
import debounce from "../utils/debounce.js";

const scrollElementIntoView = (element) => {
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        });
    }
}

export default {
    components: {
        Date,
        Camera,
        Info
    },
    props: {
        'images': {
            type: Array,
            required: true
        }
    },
    mounted() {
        this.$el.keyUpEventHandler = evt => {
            if (evt.code === 'Escape') {
                this.closeImage();
            }

            if (evt.code === 'ArrowRight') {
                this.selectNextImage();
            }

            if (evt.code === 'ArrowLeft') {
                this.selectPreviousImage();
            }

            if (evt.code === 'KeyI') {
                this.toggleInfo();
            }
        };

        this.$el.resizeEventHandler = evt => {
            if (this.selectedImage === null) {
                return;
            }

            this.hideInfo();
            this.calculateAndShowTitle();
        };

        document.addEventListener('keyup', this.$el.keyUpEventHandler);
        window.addEventListener('resize', this.$el.resizeEventHandler);
    },
    unmounted() {
        document.removeEventListener('keyup', this.$el.keyUpEventHandler);
        window.removeEventListener('resize', this.$el.resizeEventHandler);
    },

    data() {
        return {
            selectedImageIndex: null,
            selectedImage: null,
            previousImage: null,
            nextImage: null,
            imageElements: [],
            showTitle: false,
            showTitleTimer: null,
            showInfo: false,
            imgInfoStyles: {}
        }
    },
    computed: {
        titleClasses() {
            return {
                'underline underline-offset-4 decoration-1 decoration-orange-300/50': this.showInfo,
            }
        },
        imagesWithIndexes() {
            return this.images.map((img, i) => {
                img.index = i
                return img;
            });
        },
        imgStyles() {
            return {
                '--ratio': this.selectedImage.ratio,
            }
        },
        rows() {
            const rows = [];

            const images = this.imagesWithIndexes;

            let i = 0;
            while (i < images.length) {
                let imageCount = this.randomIntBetween(2, 3);

                if (i + imageCount > images.length) {
                    //not enough images to fill last row
                    break;
                }

                if (imageCount === 2) {
                    //let's check if there is some portrait images, if so, go for 3
                    if (images[i].ratio === '2 / 3' || images[i + 1].ratio === '2 / 3') {
                        imageCount = 3;
                    }
                }

                let rowImages = images.slice(i, i + imageCount);

                if (rowImages.length < imageCount) {
                    break;
                }

                rows.push(rowImages);

                i += imageCount;
            }

            return rows;
        },
    },
    methods: {
        randomIntBetween: (min, max) => Math.floor(Math.random() * (max - min + 1) + min),
        photoSource: name => '/src/photos/' + name,
        selectImage(img, index) {
            this.selectedImageIndex = index;
            this.selectedImage = img;

            this.previousImage = null;
            if ((index + -1) in this.images) {
                this.previousImage = this.images[index - 1];
            }

            this.nextImage = null;
            if ((index + 1) in this.images) {
                this.nextImage = this.images[index + 1];
            }

            this.setTitleStyles()
        },
        selectPreviousImage() {
            if (!this.selectedImage || !this.previousImage) {
                return;
            }

            this.cancelHideTitleTimer();

            this.selectImage(this.previousImage, this.selectedImageIndex - 1);
        },
        selectNextImage() {
            if (!this.selectedImage || !this.nextImage) {
                return;
            }

            this.cancelHideTitleTimer();

            this.selectImage(this.nextImage, this.selectedImageIndex + 1);
        },
        closeImage() {
            if (this.selectedImage === null) {
                return;
            }

            const selectedImage = this.selectedImage;

            this.selectedImage = null;
            this.previousImage = null;
            this.nextImage = null;

            const img = this.imageElements[selectedImage.index];

            this.hideInfo();

            scrollElementIntoView(img);
        },
        outsideImage(event) {
            //if we are clicking inside the info box, do not close the image
            if (event.target === this.$refs.info ||
                this.$refs.info.contains(event.target) ||
                event.target === this.$refs.infoMobile ||
                this.$refs.infoMobile.contains(event.target)) {
                return;
            }

            if (event.target !== this.$refs.img) {
                this.closeImage()
            }

            const boundingRect = this.$refs.imgContainer.getBoundingClientRect();
            const clickY = event.clientY - boundingRect.top;
            const containerWidth = boundingRect.width;
            const containerHeight = boundingRect.height;

            const imageAspectRatio = this.$refs.img.naturalWidth / this.$refs.img.naturalHeight;

            if (imageAspectRatio > containerWidth / containerHeight) {
                // letterboxing on the top and bottom
                const letterboxedHeight = Math.abs((containerWidth / imageAspectRatio - containerHeight) / 2);
                if ((clickY > 0 && clickY < letterboxedHeight) || clickY > (containerHeight - letterboxedHeight) && clickY < containerHeight) {
                    this.closeImage()
                }
            }
        },
        setTitleStyles() {
            this.$nextTick(() => {
                setTimeout(() => {
                    this.calculateAndShowTitle();
                }, 60);
            });

        },
        calculateAndShowTitle() {
            const img = this.$refs.img;
            const container = this.$refs.imgContainer;

            const rect = img.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();

            this.imgInfoStyles = {
                bottom: containerRect.bottom - rect.bottom  + 4 + 'px',
                left: rect.left,
                width: (rect.width - 8) + 'px',
            }

            this.showTitle = true;

            if (!this.showInfo) {
                this.createHideTitleTimer();
            }
        },
        createHideTitleTimer() {
            this.showTitleTimer = setTimeout(() => {
                this.showTitle = false;
            }, 3000);
        },
        showTitleNow() {
            debounce(() => {
                if (this.showInfo) {
                    //we are already open, and therefore don't want to close after 2 seconds
                    return;
                }

                this.cancelHideTitleTimer();

                this.showTitle = true;

                this.createHideTitleTimer();
            }, 200)();
        },
        cancelHideTitleTimer() {
            if (this.showTitleTimer) {
                clearTimeout(this.showTitleTimer);
                this.showTitleTimer = null;
            }
        },
        toggleInfo() {
            if (this.showInfo) {
                this.showInfo = false;
                this.showTitle = false;

                return;
            }

            this.cancelHideTitleTimer();

            this.showInfo = true;
            this.showTitle = true;
        },
        hideInfo() {
            this.cancelHideTitleTimer();

            this.showInfo = false;
            this.showTitle = false;
        }
    }
}
</script>

<template>
    <div class="container lg:w-3/4 mx-auto flex justify-center flex-wrap relative mb-10">
        <slot name="title"></slot>
        <div v-for="row in rows" class="w-full flex flex-wrap md:flex-nowrap gap-4 m-2">
            <template v-for="img in row">
                <div @click="selectImage(img, img.index)" class="w-full md:w-auto item relative group hover:cursor-pointer md:basis-0 md:grow-[calc(var(--ratio))] aspect-[var(--ratio)]" :style="'--ratio: ' + img.ratio + ';'">
                    <img :ref="(el) => (imageElements[img.index] = el)" class="w-full h-auto block opacity-1 transition-opacity duration-200 ease-in opacity-1 group-hover:opacity-60" loading="lazy" :src="photoSource(img.src)" alt="img.title"/>
                    <p class="text-white font-bungee-hairline font-bold text-stone-100 text-center text-sm lg:text-base absolute hidden group-hover:flex left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2">{{img.title}}</p>
                </div>
            </template>

        </div>
        <Teleport to="body">
            <Transition enter-active-class="transition-opacity duration-300 ease-in"
                        leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div ref="imgOverlay" @mousemove="showTitleNow" @click="outsideImage" v-if="selectedImage" class="fixed inset-0 z-40 w-screen h-screen bg-black/80 flex justify-center items-center portrait:py-32 landscape:py-8 sm:py-20 md:py-12">
                    <button class="fixed z-30 top-2 right-4 text-white text-4xl hover:text-orange-400 origin-bottom hover:-rotate-12 transition duration-500" @click.stop="closeImage">&times;</button>
                    <button class="fixed z-30 top-5 left-4 text-4xl hover:text-orange-400 origin-bottom transition duration-500" :class="{'text-orange-400': showInfo, 'text-white': !showInfo}" @click.stop="toggleInfo"><Info class="w-6 h-6"/></button>
                    <div class="w-[33px] mx-4 fixed md:static left-1 sm:left-0 my-auto hover:text-orange-400 hover:-translate-x-1 transition duration-300 z-30 text-white text-4xl">
                        <button @click.stop="selectPreviousImage" v-show="previousImage">&larr;</button>
                    </div>
                    <div ref="imgContainer" class="flex-1 inline-flex justify-center items-center w-full h-full relative p-3 sm:p-0 flex flex-col">

                        <img ref="img" loading="lazy" class="border-4 border-white max-h-full w-auto"
                             :src="photoSource(selectedImage.src)"
                             :alt="selectedImage.title"
                             :style="imgStyles"
                             style="aspect-ratio: var(--ratio)"
                        />

                        <Transition enter-active-class="transition-opacity duration-300 ease-in"
                                    leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0"
                                    enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <div ref="info" v-show="showTitle || showInfo" class="hidden sm:flex bg-black/70 text-white font-bungee-hairline text-lg absolute justify-between" :style="imgInfoStyles">
                                <div class="flex-1 flex flex-col">
                                    <h2 v-if="selectedImage.title" :class="titleClasses" class="px-2 py-1 w-full text-sm xl:text-base">{{selectedImage.title}}</h2>
                                    <p v-if="showInfo" class="flex-1 px-2 w-full text-sm xl:text-base">{{selectedImage.description}}</p>
                                </div>

                                <ul v-if="showInfo" class="pr-3 px-2 text-right text-xs xl:text-sm border-l border-orange-300/50 pl-2 sm:pl-6 my-2">
                                    <template v-for="(label, key) in selectedImage.exif">
                                        <li v-if="key === 'Date Taken'" class="flex items-center justify-end"><Date class="w-3 h-3 mr-1"/> {{label}}</li>
                                        <li v-else-if="key === 'Model'" class="flex items-center justify-end"><Camera class="w-3 h-3 mr-1"/> {{label}}</li>
                                        <li v-else>{{key}}: {{label}}</li>
                                    </template>

                                </ul>
                            </div>
                        </Transition>
                        <Transition enter-active-class="transition-opacity duration-300 ease-in"
                                    leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0"
                                    enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <div ref="infoMobile" v-show="showTitle || showInfo" class="sm:hidden bg-black/90 text-white font-bungee-hairline text-lg fixed sm:absolute bottom-0 left-0 w-full flex justify-between">
                                <div class="flex-1 flex flex-col">
                                    <h2 v-if="showTitle && selectedImage.title" :class="titleClasses" class="px-2 py-1 w-full text-sm">{{selectedImage.title}}</h2>
                                    <p v-if="showInfo" class="flex-1 px-2 w-full text-xs">{{selectedImage.description}}</p>
                                </div>

                                <ul v-if="showInfo" class="pr-3 px-2 text-right text-xs border-l border-orange-300/50 pl-2 sm:pl-6 my-2">
                                    <template v-for="(label, key) in selectedImage.exif">
                                        <li v-if="key === 'Date Taken'" class="flex items-center justify-end"><Date class="w-3 h-3 mr-1"/> {{label}}</li>
                                        <li v-else-if="key === 'Model'" class="flex items-center justify-end"><Camera class="w-3 h-3 mr-1"/> {{label}}</li>
                                        <li v-else>{{key}}: {{label}}</li>
                                    </template>
                                </ul>
                            </div>
                        </Transition>
                    </div>
                    <div class="w-[33px] mx-4 max-4 fixed md:static right-1 sm:right-0 my-auto hover:text-orange-400 hover:translate-x-1 transition duration-300 z-30  text-white text-4xl">
                        <button @click.stop="selectNextImage" v-show="nextImage">&rarr;</button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

