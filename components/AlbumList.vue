<script setup>
import imageData from  '../images.json';

const props = defineProps({
    albumKey: {
        type: String,
        required: true
    },
    route: {
        type: String,
        required: true
    }
})

const albums = ref(imageData[props.albumKey]);
</script>

<template>
    <div class="grid grid-cols-2 md:grid-cols-3 auto-rows-fr gap-8  p-4">
        <template v-for="(album, albumId) in albums">
            <div v-if="album.disabled === undefined || !album.disabled"  class="flex flex-col justify-end group hover:cursor-pointer ">
                <div class="flex-grow flex">
                    <h3 class="text-2xl text-white font-bungee-hairline py-3 group-hover:text-orange-300">
                        <nuxt-link :to="'/' + route + '/' + albumId">{{ album.title }}</nuxt-link>
                    </h3>
                </div>
                <nuxt-link :to="'/' + route + '/' + albumId">
                    <img :src="photoSourceAlbum(album.cover)" :alt="album.title" class="aspect-square object-cover opacity-1 transition-opacity duration-200 ease-in group-hover:opacity-60">
                </nuxt-link>
            </div>
        </template>
    </div>
</template>