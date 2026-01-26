import defaultTheme from 'tailwindcss/defaultTheme';
import typography from '@tailwindcss/typography';
import daisyui from 'daisyui';
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [typography, daisyui],
    daisyui: {
        styled: true,
        themes: [
            {
                dark: {
                    /* Botones principales */
                    "primary": "#3b82f6",
                    "primary-focus": "#2563eb",
                    "primary-content": "#ffffff",

                    "secondary": "#f000b8",
                    "secondary-focus": "#bd0091",
                    "secondary-content": "#ffffff",

                    "accent": "#37cdbe",
                    "accent-focus": "#2aa79b",
                    "accent-content": "#ffffff",

                    "neutral": "#3d4451",
                    "neutral-focus": "#2a2e37",
                    "neutral-content": "#ffffff",

                    "info": "#2094f3",
                    "info-focus": "#0c7cd5",
                    "info-content": "#ffffff",

                    "success": "#009485",
                    "success-focus": "#00766c",
                    "success-content": "#ffffff",

                    "warning": "#ff9900",
                    "warning-focus": "#cc7a00",
                    "warning-content": "#ffffff",

                    "error": "#ff5724",
                    "error-focus": "#cc4520",
                    "error-content": "#ffffff",

                    /* Fondo base */
                    "base-100": "#111827",
                    "base-content": "#e5e7eb",
                },
            },
        ],
        darkTheme: "dark",
        base: false,
        utils: true,
        logs: false,
    }
};
