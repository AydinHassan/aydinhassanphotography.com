<script>
import Camera from "./icons/Camera.vue";
import Fade from "./Fade.vue";
import Date from "./icons/Date.vue";
import Info from "./icons/Info.vue";
import debounce from "../utils/debounce.js";

export default {
    components: {
        Fade,
        Date,
        Camera,
        Info,
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
            if (this.image === null) {
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
    props: {
        image: {
            type: Object,
            required: true,
        },
        nextImage: {
            type: Object,
            required: false,
        },
        previousImage: {
            type: Object,
            required: false,
        },
    },
    data() {
        return {
            showTitle: false,
            showTitleTimer: null,
            showInfo: false,
            imageLoading: false,
            imgInfoStyles: {},
        }
    },
    watch: {
        image: {
            handler(newImage) {
                this.showTitle = false;
                this.imageLoading = true;
            },
            immediate: true
        },
    },
    computed: {
        titleClasses() {
            return {
                'underline underline-offset-4 decoration-1 decoration-orange-300/50': this.showInfo,
            }
        },
        imgStyles() {
            return {
                '--ratio': this.image.ratio,
            }
        },
    },
    methods: {
        photoSourceMain(img) {
            if (img.orientation === 'landscape') {
                return '/photos/' + img.src + '?nf_resize=fit&w=2000';
            }
            return '/photos/' + img.src + '?nf_resize=fit&h=1500';
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
            }, 100)();
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
        closeImage() {
            if (this.image === null) {
                return;
            }

            this.$emit('closeImage');
        },
        selectPreviousImage() {
            if (!this.image || !this.previousImage) {
                return;
            }

            this.cancelHideTitleTimer();
            this.$emit('previousImage');

        },
        selectNextImage() {
            if (!this.image || !this.nextImage) {
                return;
            }

            this.cancelHideTitleTimer();
            this.$emit('nextImage');
        },

        imageLoaded() {
            this.imageLoading = false;

            this.$emit('imageLoaded');

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
                bottom: containerRect.bottom - rect.bottom + 'px',
                left: rect.left,
                width: rect.width + 'px',
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
        },
        cancelHideTitleTimer() {
            if (this.showTitleTimer) {
                clearTimeout(this.showTitleTimer);
                this.showTitleTimer = null;
            }
        },
    }
}
</script>

<template>
    <div ref="imgOverlay" @mousemove="showTitleNow" @click="outsideImage" class="fixed inset-0 z-40 w-screen h-full bg-black/80 flex justify-center items-center portrait:py-32 landscape:py-8 sm:py-20 md:py-12">
        <button class="fixed z-30 top-2 right-4 text-white text-5xl hover:text-orange-400 origin-bottom hover:-rotate-12 transition duration-500" @click.stop="closeImage">&times;</button>
        <button class="fixed z-30 top-2 left-1 text-4xl hover:text-orange-400 origin-bottom transition duration-500 m-3" :class="{'text-orange-400': showInfo, 'text-white': !showInfo}" @click.stop="toggleInfo"><Info class="w-8 h-8"/></button>
        <div class="w-[33px] mx-4 fixed md:static left-1 sm:left-0 my-auto hover:text-orange-400 hover:-translate-x-1 transition duration-300 z-30 text-white text-4xl">
            <button @click.stop="selectPreviousImage" v-show="previousImage">&larr;</button>
        </div>
        <div ref="imgContainer" class="flex-1 inline-flex justify-center items-center w-full h-full relative p-3 flex flex-col">

            <div :style="imgStyles" style="aspect-ratio: var(--ratio)" class="border-4 border-white bg-black/80 max-h-full max-w-auto flex flex-col">
                <div v-show="imageLoading" class="inline-flex h-full w-full justify-center items-center">
                    <svg aria-hidden="true" class="m-10 w-16 h-16 text-white animate-spin fill-orange-400" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                </div>
                <img v-show="!imageLoading" ref="img" class="h-full w-full" @load="imageLoaded"
                     :src="photoSourceMain(image)"
                     :alt="image.title"
                />
            </div>

            <Fade>
                <div ref="info" v-show="showTitle || showInfo" class="hidden sm:flex bg-black/70 z-50 text-white font-bungee-hairline text-lg absolute justify-between" :style="imgInfoStyles">
                    <div class="flex-1 flex flex-col">
                        <h2 :class="titleClasses" class="px-2 py-1 w-full text-sm xl:text-base text-white">{{image.title}}</h2>
                        <Fade><p v-if="showInfo" class="flex-1 px-2 w-full text-sm xl:text-base text-white">{{image.description}}</p></Fade>
                    </div>

                    <Fade>
                        <ul v-if="showInfo" class="pr-3 px-2 text-right text-xs xl:text-sm border-l border-orange-300/50 pl-2 sm:pl-6 my-2">
                            <template v-for="(label, key) in image.exif">
                                <li v-if="key === 'Date Taken'" class="flex items-center justify-end text-white"><Date class="w-3 h-3 mr-1"/> {{label}}</li>
                                <li v-else-if="key === 'Model'" class="flex items-center justify-end text-white"><Camera class="w-3 h-3 mr-1"/> {{label}}</li>
                                <li v-else>{{key}}: {{label}}</li>
                            </template>
                        </ul>
                    </Fade>
                </div>
            </Fade>
            <Fade>
                <div ref="infoMobile" class="flex sm:hidden bg-black z-[99] text-white font-bungee-hairline font-bold text-lg fixed p-1 bottom-0 left-0 w-full flex justify-between">
                    <div class="flex-1 flex flex-col">
                        <h2 v-if="image.title" :class="titleClasses" class="px-2 py-1 w-full text-sm text-white">{{image.title}}</h2>
                        <p v-if="showInfo" class="flex-1 px-2 w-full text-xs text-white">{{image.description}}</p>
                    </div>

                    <ul v-if="showInfo" class="pr-3 px-2 text-right text-xs border-l border-orange-300/50 pl-2 sm:pl-6 my-2">
                        <template v-for="(label, key) in image.exif">
                            <li v-if="key === 'Date Taken'" class="flex items-center justify-end text-white"><Date class="w-3 h-3 mr-1"/> {{label}}</li>
                            <li v-else-if="key === 'Model'" class="flex items-center justify-end text-white"><Camera class="w-3 h-3 mr-1"/> {{label}}</li>
                            <li v-else>{{key}}: {{label}}</li>
                        </template>
                    </ul>
                </div>
            </Fade>
        </div>
        <div class="w-[33px] mx-4 max-4 fixed md:static right-1 sm:right-0 my-auto hover:text-orange-400 hover:translate-x-1 transition duration-300 z-30  text-white text-4xl">
            <button @click.stop="selectNextImage" v-show="nextImage">&rarr;</button>
        </div>
    </div>
</template>