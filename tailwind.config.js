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
                // Matches the Flutter app's AppTheme (lib/constance/theme.dart) —
                // deep navy primary + teal accent, swapped in from the old
                // 0xFF4C6FFF blue / 0xFFF39200 orange.
                navy: '#16243E',
                primary: '#16243E',
                accent: '#0EA5A4',
            },
        },
    },

    plugins: [forms],
};
