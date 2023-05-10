<script setup>

defineProps(['error'])

const bgSrc = '/photos/skazka-canyon.jpg';

const handleError = () => clearError({ redirect: '/' })

useHead({
    htmlAttrs: {
        class: 'h-full'
    },
    bodyAttrs: {
        class: 'h-full'
    }
})

</script>
<template>
    <div class="h-full" >
        <div class="bg-stone-900 h-full overflow-hidden overflow-y-auto flex flex-col">
            <Header />
            <div class="bg-stone-900 w-full flex-1 overflow-y-hidden overflow-y-scroll mb-4">
                <div class="container lg:w-3/4 mx-auto flex flex-col items-center mb-10 h-full">
                    <h1 v-if="error.statusCode === 404" class="text-white text-center text-3xl sm:text-5xl p-4 font-bungee-hairline font-bold mt-10">404 Not found</h1>
                    <h1 v-else class="text-white text-center text-3xl sm:text-5xl p-4 font-bungee-hairline font-bold mt-10"> {{ error.statusCode }}</h1>
                    <p v-if="error.statusCode === 404" class="text-white text-center text-sm sm:text-base p-4 font-bungee-hairline font-bold mb-4">You've strayed off the path. Good things usually come to those who do. In this instance, just go <button class="text-orange-400 hover:underline" @click="handleError">home</button>.</p>
                    <p v-else class="text-white text-center text-base p-4 font-bungee-hairline font-bold mb-4"> {{error.message }}. <button class="text-orange-400 hover:underline" @click="handleError">Go home</button>.</p>
                    <img class="w-2/3" alt="Not found" :src="bgSrc"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
#__nuxt {
    height: 100%;
}
</style>
