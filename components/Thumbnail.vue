<script setup>

import { thumbHashToDataURL, thumbHashToApproximateAspectRatio } from "thumbhash";

const props = defineProps({
    img: Object,
    src: String,
    hash: String,
});

const emit = defineEmits(['click-image'])

const loaded = ref(false);
const element = ref(null);

const hashImage = computed(() => {
    return thumbHashToDataURL(props.img.hash.split(','));
});

const classes = computed(() => {
    if (!loaded.value) {
        return ['opacity-0'];
    }

    return ['group-hover:opacity-60', 'group-hover:scale-[1.02]', 'opacity-1'];
});

const hashClasses = computed(() => {
    if (!loaded.value) {
        return ['opacity-1'];
    }

    return ['opacity-0'];
});



function loadImage() {
    const img = new Image();
    img.src = props.src;
    img.onload = () => {
        loaded.value = true;
    }
}

defineExpose({
    loadImage,
    element,
});

function click() {
    if (!loaded.value) {
        return;
    }

    emit('click-image');

    return false;
}
</script>

<template>
    <NuxtLink :to="'/image/' + img.name" :custom="true">
        <a ref="element" :href="'/image/' + img.name" :data-id="img.index" @click.prevent="click" class="relative w-full md:w-auto item overflow-hidden relative group hover:cursor-pointer basis-0 grow-[calc(var(--ratio))] aspect-[var(--ratio)]" :style="'--ratio: ' + img.ratio + ';'">
            <img :src="hashImage" class="w-full h-auto block aspect-[var(--ratio)] transition-opacity duration-1000 ease-in-out" :class="hashClasses" :style="'--ratio: ' + img.ratio + ';'" :alt="img.title" />
            <img :data-src="src"
                 class="absolute left-0 top-0 w-full h-auto block aspect-[var(--ratio)] ease-in-out [transition:transform_1s,opacity_1s]"
                 :class="classes"
                 :style="'--ratio: ' + img.ratio + ';'"
                 :src="loaded ? src : hashImage"
                 :alt="img.title"/>
            <p class="text-white font-bungee-hairline font-bold text-stone-100 text-center text-sm lg:text-base absolute hidden group-hover:flex left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2">{{img.title}}</p>
        </a>
    </NuxtLink>
</template>