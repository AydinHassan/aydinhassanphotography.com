<script>

import Info from "./icons/Info.vue";
import Camera from "./icons/Camera.vue";
import Date from "./icons/Date.vue";
import debounce from "../utils/debounce.js";
import Fade from "./Fade.vue";
import Lightbox from "./Lightbox.vue";

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
        Lightbox,
        Fade,
        Date,
        Camera,
        Info,
    },
    props: {
        'galleryRoute': {
            type: Object,
            required: true
        },
        'imageRoute': {
            type: Object,
            required: true
        },
        'images': {
            type: Array,
            required: true
        },
    },
    created() {
        this.$watch(
            () => this.$route,
            (toRoute, previousRoute) => {
                if (toRoute.name === this.imageRoute.name) {
                    const name = toRoute.params.image;
                    this.imageRouteChanged(name);
                }
            },
            { immediate: true }
        )
    },
    mounted() {
        this.animateRowObserver = new IntersectionObserver(
            this.animateInImageRow,
            {
                root: null,
                threshold: 0,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        for (let i = 0; i < this.$refs.rows.length; i++) {
            this.animateRowObserver.observe(this.$refs.rows[i]);
        }
    },
    data() {
        return {
            selectedImageIndex: null,
            selectedImage: null,
            previousImage: null,
            nextImage: null,
            imageElements: [],
        }
    },
    computed: {
        imagesWithIndexes() {
            return this.images.map((img, i) => {
                img.index = i
                return img;
            });
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

            const route = this.imageRoute;

            if (route.params === undefined) {
                route.params = {};
            }

            route.params.image = img.name;

            this.$router.push(route);
        },
        prefetchImages(indexes) {
            indexes.forEach(index => {
                if (index in this.images) {
                    this.prefetchImage(this.images[index]);
                }
            });
        },
        prefetchImage(image) {
            const img = new Image();
            img.src = this.photoSourceMain(image);
        },
        selectPreviousImage() {
            this.selectImage(this.previousImage, this.selectedImageIndex - 1);
        },
        selectNextImage() {
            this.selectImage(this.nextImage, this.selectedImageIndex + 1);
        },
        imageLoaded() {
            //prefetch some images
            const index = this.selectedImageIndex;
            this.prefetchImages([index - 1, index + 1, index + 2]);
        },
        closeImage() {
            const image = this.selectedImage;

            this.selectedImage = null;
            this.selectedImageIndex = null;
            this.previousImage = null;
            this.nextImage = null;

            const img = this.imageElements[image.index];

            this.$router.push(this.galleryRoute);

            scrollElementIntoView(img);
        },
        imageRouteChanged(imageName) {
            const image = this.images.find(img => img.name === imageName);
            if (image) {
                this.selectImage(image, image.index);
            }
        },
        animateInImageRow(entries) {
            entries.forEach(({ target, isIntersecting, intersectionRatio}) => {
                if (isIntersecting) {
                    target.classList.add('transform-none', 'opacity-100');
                    target.classList.remove('opacity-0');
                    this.animateRowObserver.unobserve(target);
                }
            });
        },
    },
}
</script>

<template>
    <div class="container lg:w-3/4 mx-auto flex justify-center flex-wrap relative mb-10">
        <slot name="title"></slot>
        <div ref="rows" v-for="row in rows" class="opacity-0 transition-[opacity,transform] translate-y-[50px] duration-1000 ease-in w-full flex flex-nowrap gap-4 m-2">
            <template v-for="img in row">
                <div @click="selectImage(img, img.index)" class=" w-full md:w-auto item relative group hover:cursor-pointer basis-0 grow-[calc(var(--ratio))] aspect-[var(--ratio)]" :style="'--ratio: ' + img.ratio + ';'">
                    <img :ref="(el) => (imageElements[img.index] = el)" class="w-full h-auto block transition-scale  duration-300 ease-in opacity-1 group-hover:scale-[1.01] group-hover:opacity-60" loading="lazy" :src="photoSource(img)" :alt="img.title"/>
                    <p class="text-white font-bungee-hairline font-bold text-stone-100 text-center text-sm lg:text-base absolute hidden group-hover:flex left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2">{{img.title}}</p>
                </div>
            </template>
        </div>
        <Teleport to="body">
            <Fade>
                <Lightbox v-if="selectedImage"
                   :image="selectedImage"
                   :next-image="nextImage"
                   :previous-image="previousImage"
                   @next-image="selectNextImage"
                   @previous-image="selectPreviousImage"
                   @image-loaded="imageLoaded"
                   @close-image="closeImage"
                />
            </Fade>
        </Teleport>
    </div>
</template>

