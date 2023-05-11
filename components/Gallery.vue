<script setup>

import seedrandom from "seedrandom";

const emit = defineEmits(['imageSelected', 'imageClosed'])

const props = defineProps({
    images: {
        type: Array,
        required: true
    },
    openImage: {
        type: String,
        default: null
    },
    galleryRoute: {
        type: String,
        required: true
    },
})

const imagesWithIndexes = computed(() => {
    return props.images.map((img, i) => {
        img.index = i
        return img;
    });
})

const imageRows = computed(() => {
    const rows = [];

    const images = imagesWithIndexes;

    let i = 0;
    while (i < images.value.length) {
        let imageCount = randomIntBetween(2, 4);

        if (i + imageCount > images.value.length) {
            //try 2 again
            imageCount = 2;
            if (i + imageCount > images.value.length) {
                //not even 2 images to fill last row
                break;
            }
        }

        if (imageCount === 2) {
            //let's check if there is some portrait images, if so, go for 3 (if there is 3)
            if (images.value[i].ratio === '2 / 3' || images.value[i + 1].ratio === '2 / 3' && images.value[i + 2] !== undefined) {
                imageCount = 3;
            }
        }

        let rowImages = images.value.slice(i, i + imageCount);

        rows.push(rowImages);

        i += imageCount;
    }

    return rows;
})

const thumbnails = ref([]);
const rows = ref([]);
const selectedImage = ref(null);
const selectedImageIndex = ref(null);
const previousImage = ref(null);
const nextImage = ref(null);

onMounted(() => {
    const animateRowObserver = new IntersectionObserver(
        function animateInImageRow(entries, observer) {
            entries.forEach(({ target, isIntersecting, intersectionRatio}) => {
                if (isIntersecting) {
                    target.classList.add('transform-none', 'opacity-100');
                    target.classList.remove('opacity-30');

                    observer.unobserve(target);
                }
            });
        },
        {
            root: null,
            threshold: 0,
            rootMargin: '0px 0px -50px 0px'
        }
    );

    const loadThumbnailObserver = new IntersectionObserver(
        function loadThumbnail(entries, observer) {
            entries.forEach(({ target, isIntersecting, intersectionRatio}) => {
                if (isIntersecting) {
                    const id = target.dataset.id;
                    const thumb = thumbnails.value[id];
                    thumb.loadImage();

                    observer.unobserve(target);
                }
            });
        },
        {
            root: null,
            threshold: 0,
            rootMargin: '0px 0px -50px 0px'
        }
    );

    for (let i = 0; i < rows.value.length; i++) {
        animateRowObserver.observe(rows.value[i]);
    }

    for (let i = 0; i < thumbnails.value.length; i++) {
        loadThumbnailObserver.observe(thumbnails.value[i].element);
    }

    const imageName = props.openImage;

    if (imageName !== null) {
        const image = imagesWithIndexes.value.find(img => img.name === imageName);
        if (image) {
            setTimeout(() => {
                scrollElementIntoView(thumbnails.value[image.index].element);
            }, 300);
            selectImage(image, image.index);
        } else {
            throw createError({statusCode: 404, statusMessage: 'Image not found'});
        }
    }
})

const scrollElementIntoView = (element) => {
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        });
    }
}

const rng = seedrandom(useRuntimeConfig().public.galleryOrderSeed);

function randomIntBetween(min, max) {
    return min + Math.floor(rng() * (max - min + 1));
}

async function prefetchImage(image) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.src = photoSourceMain(image);
        img.onload = () => resolve();
    });
}

async function prefetchImages(indexes) {
    for (const index of indexes) {
        if (index in props.images) {
            await prefetchImage(props.images[index]);
        }
    }
}

function selectImage(img, index) {
    selectedImageIndex.value = index;
    selectedImage.value = img;

    previousImage.value = null;

    if ((index + -1) in props.images) {
        previousImage.value = props.images[index - 1];
    }

    nextImage.value = null;
    if ((index + 1) in props.images) {
        nextImage.value = props.images[index + 1];
    }

    window.history.pushState({}, "", '/image/' + img.name);

    emit('imageSelected', img);
}

function selectPreviousImage() {
    selectImage(previousImage.value, selectedImageIndex.value - 1);
}

function selectNextImage() {
    selectImage(nextImage.value, selectedImageIndex.value + 1);
}

function imageLoaded() {
    //prefetch some images
    const index = selectedImageIndex.value;
    prefetchImages([index + 1, index + 2, index - 1]);
}

function closeImage() {
    const image = selectedImage.value;

    selectedImage.value = null;
    selectedImageIndex.value = null;
    previousImage.value = null;
    nextImage.value = null;

    const img = thumbnails.value[image.index].element;

    emit('imageClosed', img);

    window.history.pushState({}, "", props.galleryRoute);

    scrollElementIntoView(img);
}
</script>

<template>
    <div class="container lg:w-3/4 mx-auto flex justify-center flex-wrap relative mb-10">
        <slot name="title"></slot>
        <div ref="rows" v-for="row in imageRows" class="opacity-30 transition-[opacity,transform] translate-y-[50px] duration-1000 ease-in w-full flex flex-nowrap gap-4 m-2">
            <template v-for="img in row">
                <Thumbnail :ref="(el) => (thumbnails[img.index] = el)" @clickImage="selectImage(img, img.index)" :src="photoSourceThumb(img)" :img="img"></Thumbnail>
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

