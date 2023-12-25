/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // Existing Eve Online theme colors
                // TODO: Fix security colors
                'eve-dark': '#0b0f14',
                'eve-light': '#d8d8d8',
                'eve-accent': '#1778ff',
                'eve-secondary': '#ff4b00',
                // Security status colors
                'sec-10': '#2FEFEF', // 1.0 Security
                'sec-09': '#48F0C0', // 0.9 Security
                'sec-08': '#00EF47', // 0.8 Security
                'sec-07': '#00F000', // 0.7 Security
                'sec-06': '#8FEF2F', // 0.6 Security
                'sec-05': '#EFEF00', // 0.5 Security
                'sec-04': '#D77700', // 0.4 Security
                'sec-03': '#F06000', // 0.3 Security
                'sec-02': '#F04800', // 0.2 Security
                'sec-01': '#D73000', // 0.1 Security
                'sec-00': '#F00000', // 0.0 Security
                'sec-low': '#D70000', // Low Security
                'sec-null': '#F00000', // Null Security
                // Add more colors if necessary
            },
        },
    },
    plugins: [],
}

