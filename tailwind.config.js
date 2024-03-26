/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      typography: {
        DEFAULT: {
          css: {
            maxWidth: '100%', // add required value here
          }
        }
      }
    },
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
        dark: {
          ...require("daisyui/src/theming/themes")["dark"],
          "primary": "#003466",
          "secondary": "#FDE428",
          "accent": "#004080",
          "info": "#003466",
          "success": "#22c55e",
          "warning": "#fde047",
          "error": "#be123c",
        },
      },
    ],
  },
}

