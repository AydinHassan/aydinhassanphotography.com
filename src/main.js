import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createRouter, createWebHashHistory } from 'vue-router'


import HomeGallery from './components/HomeGallery.vue'
import Albums from './components/Albums.vue'
import Album from './components/Album.vue'
import About from './components/About.vue'

const routes = [
    { path: '/', component: HomeGallery, name: 'home' },
    { path: '/albums', component: Albums, name: 'albums' },
    { path: '/album/:id', component: Album, name: 'album' },
    { path: '/about', component: About, name: 'about' },
]

const router = createRouter({
    history: createWebHashHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return { el: "#content", top: 0 }
    },
})

const app = createApp(App);
app.use(router)
app.mount('body')

