import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                // Cairo carries Arabic properly; Inter handles Latin. Listing
                // both means one stack works in either direction.
                sans: ['Inter', 'Cairo', ...defaultTheme.fontFamily.sans],
                display: ['Sora', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                },
                accent: {
                    50: '#eef6ff',
                    100: '#d9eaff',
                    200: '#bcdaff',
                    300: '#8ec4ff',
                    400: '#59a3ff',
                    500: '#337dff',
                    600: '#1d5ef5',
                    700: '#1749e1',
                    800: '#193db6',
                    900: '#1a388f',
                },
            },
            animation: {
                'fade-up': 'fadeUp .5s ease-out both',
            },
            keyframes: {
                fadeUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [forms, typography],
}
