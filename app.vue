<script setup>

const router = useRouter()

useHead({
  titleTemplate: 'Aydin Hassan Photography | %s',
  meta: [
    { name: 'description', content: 'Travel & Landscape photography from Austria and around the world'},
    { name: 'keywords', content: 'Photography, Landscape, Travel, Austria, World, Camping, Hiking, Mountains, Forest, Fog'},
    { name: 'author', content: 'Aydin Hassan'},
    { name: 'viewport', content: 'width=device-width, initial-scale=1.0'},
    { name: 'og:type', content: 'website'},
    { name: 'og:title', content: 'Travel & Landscape photography from Austria and around the world by Aydin Hassan'},
    { name: 'og:url', content: 'https://www.aydinhassanphotography.com'}
  ],
  link: [
    { rel: 'icon', type: 'image/svg+xml', href: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 100 100"><rect width="100" height="100" rx="50" fill="%23000000"></rect><path d="M31.10 77L23.00 77L23.00 32.75Q23.00 28.70 25.85 25.85Q28.70 23 32.75 23L32.75 23L67.18 23Q71.22 23 74.11 25.85Q77 28.70 77 32.75L77 32.75L77 77L68.83 77L68.83 58.10L31.10 58.10L31.10 77ZM31.10 33.88L31.10 50L68.83 50L68.83 33.88Q68.83 32.23 68.30 31.66Q67.78 31.10 66.05 31.10L66.05 31.10L33.88 31.10Q32.23 31.10 31.66 31.66Q31.10 32.23 31.10 33.88L31.10 33.88Z" fill="#f7b05e"></path></svg>'}
  ],
  htmlAttrs: {
    class: 'h-full',
    lang: 'en'
  },
  bodyAttrs: {
    class: 'h-full'
  }
})

const scrollY = ref(0);
let scrollTimer = null;

function debounceScroll(scrollTop) {
  if (scrollTimer) {
    return;
  }

  scrollTimer = setTimeout(() => {
    scrollY.value = scrollTop;

    clearTimeout(scrollTimer);
    scrollTimer = 0;
  }, 100);
};

const content = ref(null)

function getScrollElement() {
  return content.value;
}

function toTop() {
  getScrollElement().scrollTo({top: 0, behavior: 'smooth'});
  nextTick(() => {
    scrollY.value = 0;
  });
}

function handleScroll(event) {
  debounceScroll(event.target.scrollTop);
};

watch(
    () => router.currentRoute.value,
    (to, from) => {
      scrollY.value = 0;
      if (from.name !== undefined) {
        nextTick(() => {
          getScrollElement().scrollTo({ top: 0 });
        });
      }
    }
);

</script>

<template>
  <div class="h-full" >
    <div class="bg-stone-900 h-full overflow-hidden overflow-y-auto flex flex-col">
      <Header />
      <div ref="content" @scroll="handleScroll" class="bg-stone-900 w-full flex-1 overflow-x-hidden overflow-y-hidden overflow-y-scroll scroll-smooth scrollbar-thin scrollbar-thumb-orange-400 scrollbar-track-none scrollbar-thumb-rounded-full" style="perspective: 10px">
        <NuxtPage  />
      </div>

      <Transition enter-active-class="transition-opacity duration-500 ease-in"
                  leave-active-class="transition-opacity duration-500 ease-in" enter-from-class="opacity-0"
                  enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-show="scrollY > 300" class="fixed p-4 right-0 bottom-1 flex justify-center items-center text-white hover:text-orange-300 hover:cursor-pointer hover:-translate-y-1 transition duration-300" @click="toTop">
          <IconUp class="w-6 h-6"/>
        </div>
      </Transition>
    </div>
  </div>
</template>

<style>
#__nuxt {
  height: 100%;
}

.router-link-active {
  @apply text-orange-200;
}
</style>