/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      transitionProperty: {
        width: "width"
      },
      fontFamily: {
        'bungee-hairline': ['"Bungee Hairline"', 'cursive'],
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
  plugins: [],
}