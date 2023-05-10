<script setup>
const route = useRoute();
const name = ref(route.params.name);

</script>

<template>
   <album-gallery album-list-route="/albums" :album-name="name" album-key="albums"></album-gallery>
</template>