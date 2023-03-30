<script>

import Info from "./icons/Info.vue";
import Camera from "./icons/Camera.vue";
import Date from "./icons/Date.vue";
import debounce from "../utils/debounce.js";
import Fade from "./Fade.vue";
import Lightbox from "./Lightbox.vue";
import Thumbnail from "./Thumbnail.vue";

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
        Thumbnail,
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

        this.loadThumbnailObserver = new IntersectionObserver(
            this.loadThumbnail,
            {
                root: null,
                threshold: 0,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        for (let i = 0; i < this.thumbnails.length; i++) {
            this.loadThumbnailObserver.observe(this.thumbnails[i].$el);
        }
    },
    data() {
        return {
            selectedImageIndex: null,
            selectedImage: null,
            previousImage: null,
            nextImage: null,
            thumbnails: [],
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

            this.$emit('imageSelected', img);
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

            const img = this.thumbnails[image.index].$el;

            this.$router.push(this.galleryRoute);

            this.$emit('imageClosed', img);

            scrollElementIntoView(img);
        },
        imageRouteChanged(imageName) {
            const image = this.imagesWithIndexes.find(img => img.name === imageName);
            if (image) {
                this.selectImage(image, image.index);
            }
        },
        animateInImageRow(entries) {
            entries.forEach(({ target, isIntersecting, intersectionRatio}) => {
                if (isIntersecting) {
                    target.classList.add('transform-none', 'opacity-100');
                    target.classList.remove('opacity-30');
                    this.animateRowObserver.unobserve(target);
                }
            });
        },
        loadThumbnail(entries) {
            entries.forEach(({ target, isIntersecting, intersectionRatio}) => {
                if (isIntersecting) {
                    const id = target.dataset.id;
                    const thumb = this.thumbnails[id];
                    thumb.loadImage();

                    this.loadThumbnailObserver.unobserve(target);
                }
            });
        },
    },
}
</script>

<template>
    <div class="container lg:w-3/4 mx-auto flex justify-center flex-wrap relative mb-10">
        <slot name="title"></slot>
        <div ref="rows" v-for="row in rows" class="opacity-30 transition-[opacity,transform] translate-y-[50px] duration-1000 ease-in w-full flex flex-nowrap gap-4 m-2">
            <template v-for="img in row">
                <Thumbnail :ref="(el) => (thumbnails[img.index] = el)" :data-id="img.index" @clickImage="selectImage(img, img.index)" :src="photoSource(img)" :img="img"></Thumbnail>
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

