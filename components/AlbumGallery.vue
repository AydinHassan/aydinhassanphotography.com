<script setup>
import imageData from  '../images.json';

const props = defineProps({
    albumListRoute: {
        type: String,
        required: true
    },
    albumKey: {
        type: String,
        required: true
    },
    albumName: {
        type: String,
        required: true
    },
})

const albums = ref(imageData[props.albumKey]);
const images = ref(imageData.images);

const album = computed(() => {
    if (albums.value[props.albumName] === undefined) {
        throw createError({statusCode: 404, statusMessage: 'Album not found'});
    }

    return albums.value[props.albumName]
})

const albumImages = computed(() => {
    return images.value.filter(image => image.albums !== undefined && image.albums.includes(props.albumName));
})

</script>

<template>
    <Head>
        <Title>{{ album.title }}</Title>
        <Meta name="description" :content="album.title" />
    </Head>
    <div class="container lg:w-3/4 mx-auto flex flex-col mt-6 mb-10">
        <div class="flex items-start sm:items-center flex-col sm:flex-row">
            <div class="ml-4 md:ml-0 text-lg md:text-xl text-white font-bungee-hairline float-left hover:text-orange-300 hover:-translate-x-1 transition duration-300">
                <router-link :to="albumListRoute" class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                    </svg>
                    <span>Back</span>
                </router-link>
            </div>
            <h1 class="text-white text-left sm:text-center text-3xl md:text-4xl p-4 font-bungee-hairline font-bold">{{album.title}}</h1>
        </div>
        <p v-if="album.description" class="mx-4 text-white font-bungee-hairline text-sm sm:text-base" >{{album.description}}</p>
    </div>
    <Gallery :images="albumImages" :gallery-route="albumListRoute + '/' + albumName">
    </Gallery>
    <div class="container lg:w-3/4 mx-auto flex flex-col mt-6 mb-10">
        <div class="flex items-center">
            <div class="ml-2 md:ml-0 text-lg md:text-xl text-white font-bungee-hairline float-left hover:text-orange-300 hover:-translate-x-1 transition duration-300">
                <router-link :to="albumListRoute" class="flex"><IconBack class="w-6 h-6 mr-2"/><span>Back</span></router-link>
            </div>
        </div>
    </div>
</template>