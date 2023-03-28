import { createApp, h } from 'vue'
import './style.css'
import App from './App.vue'
import { createRouter, createWebHistory, RouterView } from 'vue-router'


import HomeGallery from './components/HomeGallery.vue'
import Albums from './components/Albums.vue'
import Album from './components/Album.vue'
import About from './components/About.vue'

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
        name: 'albums',
        children: [
            {
                path: '',
                component: Albums,
            },
            {
                path: '/album/:id',
                component: Album,
                name: 'album',
            },
            {
                path: '/album/:id/i/:image',
                component: Album,
                name: 'albumImage',
            }
        ]
    },
    { path: '/about', component: About, name: 'about' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

const app = createApp(App);
app.use(router)
app.mount('#app')

