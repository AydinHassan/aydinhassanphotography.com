<template>
    <Head>
        <Title>Albums</Title>
        <Meta name="description" content="Albums" />
    </Head>
    <div class="container lg:w-3/4 mx-auto flex flex-col mt-6 mb-10">
        <div class="flex items-start sm:items-center flex-col sm:flex-row">
            <div class="ml-4 md:ml-0 text-lg md:text-xl text-white font-bungee-hairline float-left hover:text-orange-300 hover:-translate-x-1 transition duration-300 jus">
                <router-link to="/" class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                    </svg>
                    <span>Home</span>
                </router-link>
            </div>
            <h1 class="text-white text-left sm:text-center text-3xl md:text-4xl p-4 font-bungee-hairline font-bold">Albums</h1>
        </div>
        <p class="mx-4 text-white font-bungee-hairline text-sm sm:text-base" >A few selected themes</p>
        <album-list route="albums" album-key="albums"/>
    </div>
</template>
<script setup lang="ts">
</script>