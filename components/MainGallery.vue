<script setup>
import images from  '../images.json';
const portfolio = ref(images);

defineProps({
    openImage: {
        type: String,
        default: null
    }
})
</script>
<template>
    <div class="bg-stone-900 w-full">
        <Gallery :images="portfolio.images" :open-image="openImage" gallery-route="/">
            <template v-slot:title>
                <h1 class="text-white text-center text-3xl p-4 font-bungee-hairline">Recent shoots</h1>
            </template>
        </Gallery>
    </div>
</template>