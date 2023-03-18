<script>
import images from  '../images.json';

export default {
    name:  'Countries',
    data() {
        return {
            albums: images.countries
        }
    },
    methods: {
        photoSource: name => '/src/photos/' + name,
    },
}
</script>

<template>
    <div  class="bg-stone-900 w-full flex-1 ">
        <div  class="container lg:w-3/4 mx-auto justify-center flex-wrap overflow-hidden mb-10">
            <h1 class="text-white text-center text-5xl p-4 font-bungee-hairline font-bold my-10">Countries</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 auto-rows-fr gap-8  p-4">
                <div v-for="(album, albumId) in albums" class="flex flex-col items-center justify-end group hover:cursor-pointer ">
                    <template v-if="album.disabled === undefined || !album.disabled">
                        <div class="flex-grow flex items-center">
                            <h3 class="text-center text-2xl text-white font-bungee-hairline p-3 group-hover:text-orange-300"><router-link :to="{ name: 'album', params: {'id' : albumId}}">{{ album.title }}</router-link></h3>
                        </div>
                        <router-link :to="{ name: 'album', params: {'id' : albumId}}">
                            <img :src="photoSource(album.cover)" :alt="album.title" class="aspect-square object-cover opacity-1 transition-opacity duration-200 ease-in group-hover:opacity-60">
                        </router-link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>