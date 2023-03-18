<script>
import images from  '../images.json';

export default {
    name:  'Albums',
    data() {
        return {
            albums: images.albums
        }
    },
    methods: {
        photoSource: name => '/photos/' + name,
    },
}
</script>

<template>
    <div  class="bg-stone-900 w-full flex-1 ">
        <div  class="container lg:w-3/4 mx-auto justify-center flex-wrap overflow-hidden mb-10">
            <h1 class="text-white text-center text-5xl p-4 font-bungee-hairline font-bold my-10">Albums</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 auto-rows-fr gap-8  p-4">
                <template v-for="(album, albumId) in albums">
                    <div v-if="album.disabled === undefined || !album.disabled"  class="flex flex-col items-center justify-end group hover:cursor-pointer ">
                        <div class="flex-grow flex items-center">
                            <h3 class="text-center text-2xl text-white font-bungee-hairline p-3 group-hover:text-orange-300"><router-link :to="{ name: 'album', params: {'id' : albumId}}">{{ album.title }}</router-link></h3>
                        </div>
                        <router-link :to="{ name: 'album', params: {'id' : albumId}}">
                            <img loading="lazy" :src="photoSource(album.cover)" :alt="album.title" class="aspect-square object-cover opacity-1 transition-opacity duration-200 ease-in group-hover:opacity-60">
                        </router-link>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>