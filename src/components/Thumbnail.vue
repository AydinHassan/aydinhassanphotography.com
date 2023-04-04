<script>

import { thumbHashToDataURL, thumbHashToApproximateAspectRatio } from "thumbhash";

export default {
    props: {
        img: Object,
        src: String,
        hash: String
    },
    data() {
        return {
            loaded: false,
        }
    },
    computed: {
        hashImage() {
            return thumbHashToDataURL(this.img.hash.split(','));
        },
        classes() {
            if (!this.loaded) {
                return [];
            }

            return ['group-hover:opacity-60', 'group-hover:scale-[1.01]'];
        }
    },
    methods: {
        loadImage() {
            const img = new Image();
            img.src = this.src;
            img.onload = () => {
                this.loaded = true;
            }
        },
        click() {
            if (!this.loaded) {
                return;
            }

            this.$emit('click-image');
        }
    }
}

</script>

<template>
    <div @click="click" class=" w-full md:w-auto item relative group hover:cursor-pointer basis-0 grow-[calc(var(--ratio))] aspect-[var(--ratio)]" :style="'--ratio: ' + img.ratio + ';'">
        <img :data-src="src"
             class="w-full h-auto block opacity-1 transition-all duration-300 ease-in aspect-[var(--ratio)]"
             :class="classes"
             :style="'--ratio: ' + img.ratio + ';'"
             :src="loaded ? src : hashImage"
             :alt="img.title"/>
        <p class="text-white font-bungee-hairline font-bold text-stone-100 text-center text-sm lg:text-base absolute hidden group-hover:flex left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2">{{img.title}}</p>
    </div>
</template>