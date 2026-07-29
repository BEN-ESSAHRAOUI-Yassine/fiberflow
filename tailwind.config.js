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
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
                heading: ['"Manrope"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#EFF4FF',
                    100: '#DBEAFE',
                    200: '#BFDBFE',
                    300: '#A9C3FF',
                    400: '#6B94FF',
                    500: '#3B6CFF',
                    600: '#2456F5',
                    700: '#1844D8',
                    800: '#1537B0',
                    900: '#102A8A',
                    950: '#0A1A54',
                },
                surface: {
                    0: '#FFFFFF',
                    50: '#F9FAFB',
                    100: '#F3F4F6',
                    200: '#E5E7EB',
                    300: '#D1D5DB',
                },
                fiber: {
                    dark: '#0C1222',
                    mid: '#1A2332',
                    light: '#243044',
                },
            },
            boxShadow: {
                'surface': '0 1px 2px 0 rgb(0 0 0 / 0.03)',
                'surface-md': '0 2px 4px -1px rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.03)',
                'surface-lg': '0 4px 8px -2px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.03)',
            },
        },
    },

    plugins: [forms],
};
