<script>

import {useHead} from "@unhead/vue";

export default {
    setup() {
        useHead({
            title: '404',
        })
    },
    data() {
        return{
            bgSrc: '/public/photos/skazka-canyon.jpg'
        }
    },
}

</script>
<template>
    <div class="bg-stone-900 w-full flex-1 overflow-y-hidden overflow-y-scroll">
        <div class="container lg:w-3/4 mx-auto flex flex-col items-center mb-10">
            <h1 class="text-white text-center text-5xl p-4 font-bungee-hairline font-bold mt-10">404 Not found</h1>
            <p class="text-white text-center text-base p-4 font-bungee-hairline font-bold mb-4">You've strayed off the path. Good things usually come to those who do. In this instance, just go <router-link class="text-orange-400 hover:underline" to="/">home</router-link>.</p>
            <img class="w-2/3" alt="Not found" :src="bgSrc"/>
        </div>
    </div>
</template>
<script setup>
</script>