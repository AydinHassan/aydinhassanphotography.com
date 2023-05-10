<template>
    <header class="sticky top-0 left-0 z-30 w-full px-2 py-2 md:py-4 bg-stone-900 sm:px-4 shadow-xl">
        <div class="flex flex-col items-center">
            <a href="/"><h1 class="text-white text-center text-lg md:text-4xl p-4 font-bungee-hairline font-bold">Aydin Hassan Photography</h1></a>
            <ul class="flex justify-center font-bungee-hairline font-bold">
                <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><nuxt-link to="/">Home</nuxt-link></li>
                <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><nuxt-link to="/albums">Albums</nuxt-link></li>
                <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><nuxt-link to="/countries">Countries</nuxt-link></li>
                <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><nuxt-link to="/about">About</nuxt-link></li>
            </ul>
        </div>
    </header>
</template>