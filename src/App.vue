<script>
import Up from "./components/icons/Up.vue";

export default {
    components: {
        Up
    },
    data() {
        return {
            scrollTimer: 0,
            scrollY: 0,
        }
    },
    methods: {
        handleScroll: function (event) {
            this.debounceScroll(event.target.scrollTop);
        },
        debounceScroll(scrollTop) {
            if (this.scrollTimer) {
                return;
            }

            this.scrollTimer = setTimeout(() => {
                this.scrollY = scrollTop;

                clearTimeout(this.scrollTimer);
                this.scrollTimer = 0;
            }, 100);
        },
        toTop: function () {
            this.getScrollElement().scrollTo({top: 0, behavior: 'smooth'});
            this.$nextTick(() => {
                this.scrollY = 0;
            });
        },
        getScrollElement() {
            if (this.$route.name === 'home') {
                return this.$refs.content.$el;
            }

            return this.$refs.app;
        }
    },
    watch: {
        $route(to, from) {
            this.scrollY = 0;

            if (from.name !== undefined) {
                this.getScrollElement().scrollTo({top: 0});
            }
        }
    }
}
</script>
<template>
    <div @scroll="handleScroll" ref="app" class="bg-stone-900 h-full overflow-hidden overflow-y-auto flex flex-col">
        <header class="sticky top-0 left-0 z-30 w-full px-2 py-2 md:py-4 bg-stone-900 sm:px-4 shadow-xl">
            <div class="flex flex-col items-center">
                <h1 class="text-white text-center text-lg md:text-4xl p-4 font-bungee-hairline font-bold">Aydin Hassan Photography</h1>
                <ul class="flex justify-center font-bungee-hairline font-bold">
                    <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><router-link to="/">Home</router-link></li>
                    <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><router-link to="/albums">Albums</router-link></li>
                    <li class="text-sm md:text-md text-gray-300 hover:text-orange-200 p-2 border-t border-orange-300"><router-link to="/about">About</router-link></li>
                </ul>
            </div>
        </header>
        <router-view @scroll="handleScroll" ref="content" id="content"></router-view>

        <Transition enter-active-class="transition-opacity duration-500 ease-in"
                    leave-active-class="transition-opacity duration-500 ease-in" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="scrollY > 300" class="fixed right-4 bottom-5 flex justify-center items-center text-white hover:text-orange-300 hover:cursor-pointer hover:-translate-y-1 transition duration-300" @click="toTop">
                <up class="w-6 h-6"/>
            </div>
        </Transition>
    </div>
</template>

