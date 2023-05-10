<script setup>

import { thumbHashToDataURL, thumbHashToApproximateAspectRatio } from "thumbhash";

const emit = defineEmits(['imageLoaded', 'closeImage', 'nextImage', 'previousImage'])

const props = defineProps({
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
})

const loadingState = ref('loadingHash');
const showTitle = ref(false);
let showTitleTimer = null;
const showInfo = ref(false);
let imgSrc = ref(thumbHashToDataURL(props.image.hash.split(',')));
let imgDownloader = null;
const imgOverlay = ref(null);
const imgParent = ref(null);
const info = ref(null);
const infoMobile = ref(null);
const img = ref(null);
const imgContainer = ref(null);

const titleClasses = computed(() => {
    return {
        'underline underline-offset-4 decoration-1 decoration-orange-300/50': showInfo.value,
    }
});

const imgStyles = computed(() => {
    return {
        '--ratio': props.image.ratio,
    }
});

const keyUpEventHandler = evt => {
    if (evt.code === 'Escape') {
        closeImage();
    }

    if (evt.code === 'ArrowRight') {
        selectNextImage();
    }

    if (evt.code === 'ArrowLeft') {
        selectPreviousImage();
    }

    if (evt.code === 'KeyI') {
        toggleInfo();
    }
};

const resizeHandler = evt => {
    if (props.image === null) {
        return;
    }

    hideInfo();
    calculateAndShowTitle();
};

onMounted(() => {
    document.addEventListener('keyup', keyUpEventHandler);
    window.addEventListener('resize', resizeHandler);
});

onUnmounted(() => {
    document.removeEventListener('keyup', keyUpEventHandler);
    window.removeEventListener('resize', resizeHandler);
});

watch(() => props.image, async (newImage) => {
    cancelFetchImage();
    resetLoadingState();
    imgSrc.value = thumbHashToDataURL(newImage.hash.split(','));
}, { immediate: true });

function isImageCached(src)  {
    const img = new Image();
    img.src = src;
    const complete = img.complete;
    img.src = "";
    return complete;
}

function fetchImage(src) {
    imgDownloader = new Image();
    imgDownloader.src = src;
    imgDownloader.onload = () => {
        imgSrc.value = src;
    };
}

function cancelFetchImage() {
    if (imgDownloader !== null) {
        imgDownloader.src = "";
        imgDownloader.onload = null;
    }
}

function resetLoadingState() {
    loadingState.value = 'loadingHash';
}

function photoSource(img) {
    if (img.orientation === 'landscape') {
        return '/photos/' + img.src + '?nf_resize=fit&w=1000';
    }
    return '/photos/' + img.src + '?nf_resize=fit&h=500';
}

function photoSourceMain(img) {
    if (img.orientation === 'landscape') {
        return '/photos/' + img.src + '?nf_resize=fit&w=2000';
    }
    return '/photos/' + img.src + '?nf_resize=fit&h=1500';
}

function imageLoaded() {
    if (loadingState.value === 'loadingHash') {
        //if we already have the cached large image, just use that
        if (isImageCached(photoSourceMain(props.image))) {
            loadingState.value = 'loadingMain';
            fetchImage(photoSourceMain(props.image));
            return;
        }

        //otherwise download thumbnail first
        loadingState.value = 'loadingThumbnail';
        fetchImage(photoSource(props.image));
        return;
    }

    if (loadingState.value === 'loadingThumbnail') {
        loadingState.value = 'loadingMain';
        fetchImage(photoSourceMain(props.image));
        return;
    }

    if (loadingState.value === 'loadingMain') {
        loadingState.value = 'loaded';
    }

    emit('imageLoaded');

    nextTick(() => {
        setTimeout(() => {
            calculateAndShowTitle();
        }, 60);
    });
}

function showTitleNow() {
    debounce(() => {
        if (showInfo.value) {
            //we are already open, and therefore don't want to close after 2 seconds
            return;
        }

        cancelHideTitleTimer();

        showTitle.value = true;

        createHideTitleTimer();
    }, 100)();
}

function closeImage() {
    if (props.image === null) {
        return;
    }

    emit('closeImage');
}

function createHideTitleTimer() {
    showTitleTimer = setTimeout(() => {
        showTitle.value = false;
    }, 3000);
}

function cancelHideTitleTimer() {
    if (showTitleTimer) {
        clearTimeout(showTitleTimer);
        showTitleTimer = null;
    }
}

function hideInfo() {
    cancelHideTitleTimer();

    showInfo.value = false;
    showTitle.value = false;
}

function toggleInfo() {
    if (showInfo.value) {
        showInfo.value = false;
        showTitle.value = false;

        return;
    }

    cancelHideTitleTimer();

    showInfo.value = true;
    showTitle.value = true;
}

function calculateAndShowTitle() {
    showTitle.value = true;

    if (!showInfo.value) {
        createHideTitleTimer();
    }
}

function selectNextImage() {
    if (!props.image || !props.nextImage) {
        return;
    }

    cancelHideTitleTimer();
    hideInfo();

    imgParent.value.classList.add('-translate-x-[calc(100vw)]');

    setTimeout(() => {
        imgParent.value.classList.remove('-translate-x-[calc(100vw)]');
        imgParent.value.classList.add('hidden', 'translate-x-[calc(100vw)]');
        setTimeout(() => {
            imgParent.value.classList.remove('hidden');

            setTimeout(() => {
                imgParent.value.classList.remove('translate-x-[calc(100vw)]');
                emit('nextImage');
            }, 20);

        }, 20);
    }, 300);
}

function selectPreviousImage() {
    if (!props.image || !props.previousImage) {
        return;
    }

    cancelHideTitleTimer();
    hideInfo();
    imgParent.value.classList.add('translate-x-[calc(100vw)]');

    setTimeout(() => {
        imgParent.value.classList.remove('translate-x-[calc(100vw)]');
        imgParent.value.classList.add('hidden', '-translate-x-[calc(100vw)]');
        setTimeout(() => {
            imgParent.value.classList.remove('hidden');

            setTimeout(() => {
                imgParent.value.classList.remove('-translate-x-[calc(100vw)]');
                emit('previousImage');
            }, 20);

        }, 20);
    }, 300);
}

function outsideImage(event) {
    //if we are clicking inside the info box, do not close the image
    if (event.target === info.value ||
        info.value.contains(event.target) ||
        event.target === infoMobile.value ||
        infoMobile.value.contains(event.target)) {
        return;
    }

    if (event.target !== img.value) {
        closeImage();
        return;
    }

    const boundingRect = imgContainer.value.getBoundingClientRect();
    const clickY = event.clientY - boundingRect.top;
    const containerWidth = boundingRect.width;
    const containerHeight = boundingRect.height;

    const imageAspectRatio = img.value.naturalWidth / img.value.naturalHeight;

    if (imageAspectRatio > containerWidth / containerHeight) {
        // letterboxing on the top and bottom
        const letterboxedHeight = Math.abs((containerWidth / imageAspectRatio - containerHeight) / 2);
        if ((clickY > 0 && clickY < letterboxedHeight) || clickY > (containerHeight - letterboxedHeight) && clickY < containerHeight) {
            closeImage()
        }
    }
}
</script>

<template>
    <div ref="imgOverlay" @mousemove="showTitleNow" @click.stop="outsideImage" class="fixed inset-0 z-40 w-screen h-full bg-black/80 flex justify-center items-center portrait:py-32 landscape:py-8 sm:py-20 md:py-12">
        <Head>
            <Title>{{ image.title }}</Title>
            <Meta name="description" :content="image.description || image.title" />
            <Meta name="og:title" :content="image.title" />
            <Meta v-if="image.description" name="og:description" :content="image.description" />
            <Meta name="og:url" :content="'https://www.aydinhassanphotography.com/image/' + image.name" />
            <Meta name="og:image" :content="'https://www.aydinhassanphotography.com' + photoSource(image)" />
        </Head>
        <button class="fixed z-30 top-2 right-4 text-white text-5xl hover:text-orange-400 origin-bottom hover:-rotate-12 transition duration-500" @click.stop="closeImage">&times;</button>
        <button class="fixed z-30 top-2 left-1 text-4xl hover:text-orange-400 origin-bottom transition duration-500 m-3" :class="{'text-orange-400': showInfo, 'text-white': !showInfo}" @click.stop="toggleInfo"><IconInfo class="w-8 h-8"/></button>
        <div class="w-[33px] mx-4 fixed md:static left-1 sm:left-0 my-auto hover:text-orange-400 hover:-translate-x-1 transition duration-300 z-30 text-white text-4xl">
            <button @click.stop="selectPreviousImage" v-show="previousImage">&larr;</button>
        </div>
        <div ref="imgContainer" class="flex-1 inline-flex justify-center items-center w-full h-full relative p-3 flex flex-col">
            <div ref="imgParent" :style="imgStyles" style="aspect-ratio: var(--ratio)" class="border-4 border-white bg-black/80 max-h-full max-w-auto flex flex-col transition-transform transform duration-300 ease-in-out">
                <img ref="img"
                     style="aspect-ratio: var(--ratio)"
                     class="h-full" :class="['loadingThumbnail', 'loadingHash', 'loadingMain'].includes(loadingState.value) ? 'w-[3000px] object-cover' : 'w-full'"
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
                                    <li v-if="key === 'Date Taken'" class="flex items-center justify-end text-white"><IconDate class="w-3 h-3 mr-1"/> {{label}}</li>
                                    <li v-else-if="key === 'Model'" class="flex items-center justify-end text-white"><IconCamera class="w-3 h-3 mr-1"/> {{label}}</li>
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
                        <h1 v-if="image.title" :class="titleClasses" class="px-2 py-1 w-full text-sm text-white">{{image.title}}</h1>
                        <p v-if="showInfo" class="flex-1 px-2 w-full text-xs text-white">{{image.description}}</p>
                    </div>

                    <ul v-if="showInfo" class="pr-3 px-2 text-right text-xs border-l border-orange-300/50 pl-2 sm:pl-6 my-2">
                        <template v-for="(label, key) in image.exif">
                            <li v-if="key === 'Date Taken'" class="flex items-center justify-end text-white"><IconDate class="w-3 h-3 mr-1"/> {{label}}</li>
                            <li v-else-if="key === 'Model'" class="flex items-center justify-end text-white"><IconCamera class="w-3 h-3 mr-1"/> {{label}}</li>
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