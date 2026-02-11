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
                sans: ['Outfit', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'health-good': '#10b981', // emerald-500
                'health-warning': '#f59e0b', // amber-500
                'health-critical': '#e11d48', // rose-600
                'cyber-dark': '#0f172a', // slate-900
                'cyber-card': '#1e293b', // slate-800
            },
        },
    },

    plugins: [forms],
};
