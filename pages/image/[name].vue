<script setup>
const route = useRoute();
const name = ref(route.params.name);

const image = getImageByName(name.value);
</script>

<template>
    <Head>
        <Title>{{ image.title }}</Title>
        <Meta name="description" :content="image.description || image.title" />
        <Meta name="og:title" :content="image.title" />
        <Meta v-if="image.description" name="og:description" :content="image.description" />
        <Meta name="og:url" :content="'https://www.aydinhassanphotography.com/image/' + image.name" />
        <Meta name="og:image" :content="'https://www.aydinhassanphotography.com' + photoSourceThumb(image)" />
    </Head>
    <MainHero />
    <MainGallery :open-image="name"/>
</template>