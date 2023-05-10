<script setup>
const route = useRoute();
const name = ref(route.params.name);

</script>

<template>
    <album-gallery album-list-route="/countries" :album-name="name" album-key="countries"></album-gallery>
</template>