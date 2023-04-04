import { createApp, h } from 'vue'
import './style.css'
import App from './App.vue'
import { createRouter, createWebHistory, RouterView } from 'vue-router'
import { createHead } from "@vueuse/head"

import HomeGallery from './components/HomeGallery.vue'
import Albums from './components/Albums.vue'
import Album from './components/Album.vue'
import About from './components/About.vue'
import NotFound from './components/NotFound.vue'

const routes = [
    { path: '/', component: HomeGallery, name: 'homeGallery', alias: '/gallery'},
    {
        path: '/gallery/i/:image',
        component: HomeGallery,
        name: 'homeGalleryImage',
    },
    {
        path: '/albums',
        component: { render: () => h(RouterView) },
        children: [
            {
                path: '',
                component: Albums,
                name: 'albums',
            },
            {
                path: '/album/:name',
                component: Album,
                name: 'album',
                props: true,
            },
            {
                path: '/album/:name/i/:image',
                component: Album,
                name: 'albumImage',
                props: true,
            }
        ]
    },
    { path: '/about', component: About, name: 'about' },
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

const app = createApp(App);
app.use(router)

const head = createHead()
app.use(head)
app.mount('#app')

