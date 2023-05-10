/** @type {import('tailwindcss').Config} */

const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./nuxt.config.{js,ts}",
    "./app.vue",
    "./error.vue",
  ],
  future: {
    hoverOnlyWhenSupported: true,
  },
  theme: {
    extend: {
      transitionProperty: {
        width: "width"
      },
      fontFamily: {
        'bungee-hairline': ['"Bungee Hairline"', ...defaultTheme.fontFamily.sans],
      },
      animation: {
        'flash': 'flash 1.5s',
      },
      keyframes: {
        flash: {
          '0%': { opacity: .4 },
          '100%': { opacity: 1 },
        }
      }
    },
  },
  plugins: [
    require('tailwind-scrollbar')({ nocompatible: true }),
  ],
}