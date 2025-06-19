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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Custom palette berdasarkan gambar Anda
                sage: {
                    50: '#f7f9f7',   // Very light sage
                    100: '#eeefe0',  // #EEEFE0
                    200: '#e1e5d1',  // Light sage
                    300: '#d1d8be',  // #D1D8BE
                    400: '#b8c5a0',  // Medium sage
                    500: '#a7c1a8',  // #A7C1A8
                    600: '#819a91',  // #819A91
                    700: '#6b8a7a',  // Darker sage
                    800: '#5a7565',  // Dark sage
                    900: '#4a6355',  // Very dark sage
                },
                primary: {
                    50: '#f7f9f7',
                    100: '#eeefe0',
                    200: '#e1e5d1',
                    300: '#d1d8be',
                    400: '#b8c5a0',
                    500: '#a7c1a8',  // Main color
                    600: '#819a91',
                    700: '#6b8a7a',
                    800: '#5a7565',
                    900: '#4a6355',
                }
            }
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/line-clamp'),
    ],
};