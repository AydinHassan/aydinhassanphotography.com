<template>
    <Transition name="fade" enter-active-class="transition-opacity duration-300 ease-in"
                leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <slot></slot>
    </Transition>
</template>
<script setup>
</script>