import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:  ['Manrope', ...defaultTheme.fontFamily.sans],
                serif: ['Zilla Slab', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                moss: {
                    900: '#1D3520',
                    700: '#2C4E2E',
                    500: '#3E683F',
                    100: '#E2ECDF',
                },
                wheat: {
                    DEFAULT: '#B5822A',
                    100: '#F6ECD7',
                },
                clay: {
                    DEFAULT: '#A34F30',
                    100: '#F5E3DB',
                },
                ink: {
                    DEFAULT: '#191D14',
                    2: '#5C6352',
                },
                ground: '#EDF0E5',
                paper:  '#F8FCF7',
            },
        },
    },

    plugins: [forms],
};
