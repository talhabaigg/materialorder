/** @type {import('tailwindcss').Config} */

const colors = require('tailwindcss/colors')

module.exports = {
  content: [
    './resources/**/*.blade.php',
        './app/Filament/**/*.php',
        './app/Http/Livewire/**/*.php',
        './vendor/filament/**/*.blade.php',
        './node_modules/flowbite/**/*.js'
    
    
  ],
  theme: {
    extend: {
      colors: {
        primary: colors.blue,
      },
    },
  },
  plugins: [require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('flowbite/plugin')
  ],
}
