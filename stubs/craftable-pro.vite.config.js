import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import { resolve } from "path";
import tailwindcss from "tailwindcss";
import { defineConfig, splitVendorChunkPlugin } from "vite";

export default defineConfig({
  plugins: [
    splitVendorChunkPlugin(),
    laravel({
      input: [
        "resources/js/craftable-pro/index.ts",
        "resources/css/craftable-pro.css",
      ],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  css: {
    postcss: {
      plugins: [
        tailwindcss({
          config: "./craftable-pro.tailwind.config.js",
        }),
      ],
    },
  },
  resolve: {
    alias: {
      "@": resolve(__dirname, "./resources/js"),
      "craftable-pro": resolve(
        __dirname,
        "./vendor/jaydeepukani/craftable-pro/resources/js"
      ),
      ziggy: resolve(__dirname, "./vendor/tightenco/ziggy"),
    },
  },
});
