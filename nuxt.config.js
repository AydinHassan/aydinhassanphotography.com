// https://nuxt.com/docs/api/configuration/nuxt-config
import portfolio from  './images.json';

export default defineNuxtConfig({
    runtimeConfig: {
        public: {
            siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'https://www.aydinhassanphotography.com',
        }
    },
    components: [
        { path: '~/components/icons', prefix: 'Icon' },
        '~/components'
    ],
    css: ['~/assets/style.css'],
    postcss: {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
    },
    modules: [
        '@nuxtjs/google-fonts',
        'nuxt-simple-sitemap',
        '@nuxtjs/robots',
        //'@nuxtus/nuxt-localtunnel'
    ],
    googleFonts: {
        families: {
            'Bungee+Hairline':  {
                wght: [400],
            },
        },
        display: 'swap',
        preload: true
    }
})
