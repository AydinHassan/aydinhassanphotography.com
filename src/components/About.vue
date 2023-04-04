<script>
import config from '../config.js'
import {useHead} from "@unhead/vue";

export default{
    components: {},
    setup() {
        useHead({
            title: 'About',
        })
    },
    data() {
        return{
            links: config.links,
        }
    },
}
</script>
<template>
    <div  class="bg-stone-900 w-full flex-1 ">
        <div  class="container lg:w-3/4 mx-auto justify-center flex-wrap overflow-hidden mb-10">
            <h1 class="text-white text-center text-5xl p-4 font-bungee-hairline font-bold my-10">About</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 md:auto-rows-fr gap-8 p-4">
                <div class="flex flex-col md:pr-20 items-center">
                    <div class="max-w-xs px-2.5 lg:max-w-none"><img alt="" src="/src/assets/aydin.jpeg" class="aspect-square rounded-full object-cover bg-stone-900 border-4 border-orange-400" loading="lazy"></div>
                    <div class="flex mt-10 bg-stone-900 py-4 justify-center ">
                        <a :href="links.twitter" target="_blank"><svg class="w-6 h-6 mr-4 fill-white transition hover:fill-orange-400 hover:cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg></a>
                        <a :href="links.github" target="_blank"><svg class="w-6 h-6 mr-4 fill-white transition hover:fill-orange-400 hover:cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"/></svg></a>
                        <a :href="links.unsplash" target="_blank"><svg class="w-6 h-6 mr-4 fill-white transition hover:fill-orange-400 hover:cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M448,230.17V480H0V230.17H141.13V355.09H306.87V230.17ZM306.87,32H141.13V156.91H306.87Z"/></svg></a>
                        <a :href="links.instagram" target="_blank"><svg class="w-6 h-6 mr-4 fill-white transition hover:fill-orange-400 hover:cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg></a>
                        <a :href="links.email" target="_blank"><svg class="w-6 h-6 fill-white transition hover:fill-orange-400 hover:cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg></a>
                    </div>
                </div>

                <div>
                    <h1 class="text-white text-3xl font-bungee-hairline underline decoration-orange-300 decoration-1 underline-offset-4">Hey! I'm Aydin, a photographer, software engineer and avid traveller.</h1>

                    <div class="mt-6 space-y-7 text-base text-stone-400">
                        <p>My love for the outdoors began at a young age, exploring the Great British countryside with my family. Adventures, terrible weather, disasters and close-calls were the norm.</p>
                        <p>After working as a Chef for numerous years, studying at University and graduating with a degree in Computer Science, I worked on eCommerce platforms and open source software, building up my technical engineering skills. But something was missing.</p>
                        <p>A desire to explore the world began to burn and so I embarked on an interrailing journey across Europe that would change my life forever. It was during this time that I met my future wife, and together we set out on a life of adventure that we still pursue to this day. With it, my love for photography was born.</p>
                        <p>Many of my photographs were created during our travels, hitchhiking and camping through Europe to Morocco, multi-day long distance hikes, driving and car camping through Turkey, Georgia and Central Asia along the Silk Road.</p>

                        <p>Despite working full-time as a Software Engineer, I always make time to pursue my love for exploration and photographing the beauty of Austria and beyond.</p>
                        <p>I love waking up early in the mountains and witnessing first hand the magic of nature, I live to explore the unknown and push myself to the limits and share the beauty I've witnessed with the world. I've built this website from scratch to present my best work and to maybe inspire a few others.</p>

                        <hr class="w-48 h-[1px] mx-auto my-4 bg-orange-300 border-0  md:my-10">

                        <p>If you're looking to use or purchase any of my images then you have a few options:</p>
                        <ul class="list-disc list-inside">
                            <li>Download a few select images for free from <a class="text-orange-400 hover:underline" :href="links.unsplash">Unsplash.</a></li>
                            <li>Purchase from <a class="text-orange-400 hover:underline" :href="links.shutterstock">Shutterstock.</a></li>
                            <li><a class="text-orange-400 hover:underline" :href="links.email">E-mail me</a> to discuss.</li>
                        </ul>

                        <p>If you're interested in the adventures behind the photographs, you can explore mine and my wife's travel blog <a class="text-orange-400 hover:underline" :href="links.wildandwithout_blog">Wild & Without</a> and our <a class="text-orange-400 hover:underline" :href="links.wildandwithout_instagram">Instagram page.</a></p>

                        <p>If you want to hire me, then you can use one of the above social media links to contact me. <a class="text-orange-400 hover:underline" :href="links.email">Or E-mail me.</a></p>

                        <hr class="w-48 h-[1px] mx-auto my-4 bg-orange-300 border-0  md:my-10">

                        <p>For programming related topics, you can explore the following links:</p>
                        <ul class="list-disc list-inside">
                            <li><a class="text-orange-400 hover:underline" :href="links.code_home">My portfolio website</a></li>
                            <li><a class="text-orange-400 hover:underline" :href="links.github">My GitHub</a></li>
                        </ul>

                        <p>This website is designed and programmed by me from scratch, naturally it's also open source, you can find the source code <a class="text-orange-400 hover:underline" href="https://github.com/AydinHassan/aydinhassanphotography.com">here.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
</script>