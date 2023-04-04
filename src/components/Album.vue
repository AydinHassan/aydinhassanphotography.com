<script>
import images from  '../images.json';
import Gallery from "./Gallery.vue";
import Back from "./icons/Back.vue";
import { useHead } from '@unhead/vue';

export default {
    name:  'Albums',
    components: {
        Back,
        Gallery
    },
    mounted() {
        useHead({
            title: this.album.title,
        })
    },
    data() {
        return {
            albums: images.albums,
            images: images.images
        }
    },
    methods: {
        onImageSelected(image) {
            useHead({
                link: [{
                    key: 'canonical',
                    rel: 'canonical',
                    href: window.location.origin + '/gallery/i/' + image.name,
                }],
            });
        },
        onImageClosed(image) {
            useHead({
                link: [{
                    key: 'canonical',
                }],
            });
        },
    },
    computed: {
        galleryRoute() {
            return {
                name: 'album',
                params: {
                    id: this.$route.params.id
                }
            }
        },
        imageRoute() {
            return {
                name: 'albumImage',
                params: {
                    id: this.$route.params.id,
                }
            }
        },
        album() {
            return this.albums[this.$route.params.id]
        },
        albumImages() {
            return images.images.filter(image => image.albums !== undefined && image.albums.includes(this.$route.params.id));
        },
    },
}
</script>

<template>
    <div  class="bg-stone-900 w-full flex-1">
        <div class="container lg:w-3/4 mx-auto flex flex-col mt-6 mb-10">
            <div class="flex items-start sm:items-center flex-col sm:flex-row">
                <div class="ml-4 md:ml-0 text-lg md:text-xl text-white font-bungee-hairline float-left hover:text-orange-300 hover:-translate-x-1 transition duration-300 jus">
                    <router-link to="/albums" class="flex">
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
        <Gallery @imageSelected="onImageSelected" @imageClosed="onImageClosed" :images="albumImages" :gallery-route="galleryRoute" :image-route="imageRoute" >
        </Gallery>
        <div class="container lg:w-3/4 mx-auto flex flex-col mt-6 mb-10">
            <div class="flex items-center">
                <div class="ml-2 md:ml-0 text-lg md:text-xl text-white font-bungee-hairline float-left hover:text-orange-300 hover:-translate-x-1 transition duration-300 jus">
                    <router-link to="/albums" class="flex"><back class="w-6 h-6 mr-2"/><span>Back</span></router-link>
                </div>
            </div>
        </div>
    </div>
</template>