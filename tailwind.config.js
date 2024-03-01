/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require("@tailwindcss/typography"), require("daisyui")
  ],
  daisyui: {
    themes: [
      {
        light: {
          "primary": "#003466",
          "secondary": "#FDE428",
          "accent": "#004080",
          "neutral": "#E7E7E7",
          "base-100": "#F2F2F2",
          "info": "#003466",
          "success": "#22c55e",
          "warning": "#fde047",
          "error": "#be123c",
        },
      },
    ],
  },
}

