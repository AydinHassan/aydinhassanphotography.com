<script>
import Camera from "./icons/Camera.vue";
import Fade from "./Fade.vue";
import Date from "./icons/Date.vue";
import Info from "./icons/Info.vue";
import debounce from "../utils/debounce.js";
import { thumbHashToDataURL, thumbHashToApproximateAspectRatio } from "thumbhash";

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
            loadingState: 'loadingHash',
            showTitle: false,
            showTitleTimer: null,
            showInfo: false,
            imgSrc: thumbHashToDataURL(this.image.hash.split(',')),
            imgDownloader: null,
        }
    },
    watch: {
        image: {
            handler(newImage) {
                this.cancelFetchImage();
                this.resetLoadingState();
                this.imgSrc = thumbHashToDataURL(newImage.hash.split(','));
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
        isImageCached(src) {
            const img = new Image();
            img.src = src;
            const complete = img.complete;
            img.src = "";
            return complete;
        },
        imageLoaded() {
            if (this.loadingState === 'loadingHash') {
                //if we already have the cached large image, just use that
                if (this.isImageCached(this.photoSourceMain(this.image))) {
                    this.loadingState = 'loadingMain';
                    this.fetchImage(this.photoSourceMain(this.image));
                    return;
                }

                //otherwise download thumbnail first
                this.loadingState = 'loadingThumbnail';
                this.fetchImage(this.photoSource(this.image));
                return;
            }

            if (this.loadingState === 'loadingThumbnail') {
                this.loadingState = 'loadingMain';
                this.fetchImage(this.photoSourceMain(this.image));
                return;
            }

            if (this.loadingState === 'loadingMain') {
                this.loadingState = 'loaded';
            }

            this.$emit('imageLoaded');

            this.$nextTick(() => {
                setTimeout(() => {
                    this.calculateAndShowTitle();
                }, 60);
            });
        },
        fetchImage(src) {
            this.imgDownloader = new Image();
            this.imgDownloader.src = src;
            this.imgDownloader.onload = () => {
                this.imgSrc = src;
            };
        },
        cancelFetchImage() {
            if (this.imgDownloader !== null) {
                this.imgDownloader.src = "";
                this.imgDownloader.onload = null;
            }
        },
        resetLoadingState() {
            this.loadingState = 'loadingHash';
        },
        photoSource(img) {
            if (img.orientation === 'landscape') {
                return '/photos/' + img.src + '?nf_resize=fit&w=1000';
            }
            return '/photos/' + img.src + '?nf_resize=fit&h=500';
        },
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
                this.closeImage();
                return;
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
            this.hideInfo();
            this.$refs.imgParent.classList.add('translate-x-[calc(100vw)]');

            setTimeout(() => {
                this.$refs.imgParent.classList.remove('translate-x-[calc(100vw)]');
                this.$refs.imgParent.classList.add('hidden', '-translate-x-[calc(100vw)]');
                setTimeout(() => {
                    this.$refs.imgParent.classList.remove('hidden');

                    setTimeout(() => {
                        this.$refs.imgParent.classList.remove('-translate-x-[calc(100vw)]');
                        this.$emit('previousImage');
                    }, 20);

                }, 20);
            }, 300);
        },
        selectNextImage() {
            if (!this.image || !this.nextImage) {
                return;
            }

            this.cancelHideTitleTimer();
            this.hideInfo();

            this.$refs.imgParent.classList.add('-translate-x-[calc(100vw)]');

            setTimeout(() => {
                this.$refs.imgParent.classList.remove('-translate-x-[calc(100vw)]');
                this.$refs.imgParent.classList.add('hidden', 'translate-x-[calc(100vw)]');
                setTimeout(() => {
                    this.$refs.imgParent.classList.remove('hidden');

                    setTimeout(() => {
                        this.$refs.imgParent.classList.remove('translate-x-[calc(100vw)]');
                        this.$emit('nextImage');
                    }, 20);

                }, 20);
            }, 300);
        },
        calculateAndShowTitle() {
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
    <div ref="imgOverlay" @mousemove="showTitleNow" @click.stop="outsideImage" class="fixed inset-0 z-40 w-screen h-full bg-black/80 flex justify-center items-center portrait:py-32 landscape:py-8 sm:py-20 md:py-12">
        <button class="fixed z-30 top-2 right-4 text-white text-5xl hover:text-orange-400 origin-bottom hover:-rotate-12 transition duration-500" @click.stop="closeImage">&times;</button>
        <button class="fixed z-30 top-2 left-1 text-4xl hover:text-orange-400 origin-bottom transition duration-500 m-3" :class="{'text-orange-400': showInfo, 'text-white': !showInfo}" @click.stop="toggleInfo"><Info class="w-8 h-8"/></button>
        <div class="w-[33px] mx-4 fixed md:static left-1 sm:left-0 my-auto hover:text-orange-400 hover:-translate-x-1 transition duration-300 z-30 text-white text-4xl">
            <button @click.stop="selectPreviousImage" v-show="previousImage">&larr;</button>
        </div>
        <div ref="imgContainer" class="flex-1 inline-flex justify-center items-center w-full h-full relative p-3 flex flex-col">
            <div ref="imgParent" :style="imgStyles" style="aspect-ratio: var(--ratio)" class="border-4 border-white bg-black/80 max-h-full max-w-auto flex flex-col transition-transform transform duration-300 ease-in-out">
                <img ref="img"
                     style="aspect-ratio: var(--ratio)"
                     class="h-full" :class="['loadingThumbnail', 'loadingHash', 'loadingMain'].includes(this.loadingState) ? 'w-[3000px] object-cover' : 'w-full'"
                     @load="imageLoaded"
                     :src="imgSrc"
                     :alt="image.title"
                />
                <Fade>
                    <div ref="info" v-show="showTitle || showInfo" class="hidden sm:flex bg-black/70 z-50 text-white font-bungee-hairline text-lg absolute justify-between bottom-0 w-full">
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
            </div>


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