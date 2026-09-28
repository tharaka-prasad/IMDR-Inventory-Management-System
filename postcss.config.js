import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    400: '#60a5fa',
                    500: '#2563eb',
                    600: '#1d4ed8',
                    700: '#1e40af',
                },
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-16px)' },
                },
                'float-slow': {
                    '0%, 100%': { transform: 'translateY(0) rotate(-3deg)' },
                    '50%': { transform: 'translateY(-22px) rotate(3deg)' },
                },
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                gradient: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                blob: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '33%': { transform: 'translate(40px, -30px) scale(1.15)' },
                    '66%': { transform: 'translate(-30px, 30px) scale(0.9)' },
                },
                shimmer: {
                    '0%': { transform: 'translateX(-120%) skewX(-20deg)' },
                    '100%': { transform: 'translateX(220%) skewX(-20deg)' },
                },
                shake: {
                    '0%, 100%': { transform: 'translateX(0)' },
                    '20%, 60%': { transform: 'translateX(-8px)' },
                    '40%, 80%': { transform: 'translateX(8px)' },
                },
                'pulse-ring': {
                    '0%': { transform: 'scale(1)', opacity: '0.6' },
                    '100%': { transform: 'scale(2.2)', opacity: '0' },
                },
            },
            animation: {
                float: 'float 6s ease-in-out infinite',
                'float-slow': 'float-slow 9s ease-in-out infinite',
                'fade-up': 'fade-up 0.7s ease-out both',
                'fade-in': 'fade-in 1s ease-out both',
                gradient: 'gradient 14s ease infinite',
                blob: 'blob 16s ease-in-out infinite',
                shimmer: 'shimmer 2.6s ease-in-out infinite',
                shake: 'shake 0.45s ease-in-out',
                'pulse-ring': 'pulse-ring 2s ease-out infinite',
            },
        },
    },
    plugins: [forms],
};