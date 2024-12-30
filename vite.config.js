import { defineConfig } from "vite";
import laravel, { refreshPaths } from "laravel-vite-plugin";
import livewire from "@defstudio/vite-livewire-plugin"; // import plugin

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/filament/admin/theme.css",
            ],
            refresh: false,
        }),

        livewire({
            refresh: [
                ...refreshPaths, // Tracking changes wherever app.js is called
                "app/Http/Livewire/**", // To monitor LiveWire components (if applicable)
                "app/Custom/Path/**", // You can show the path to the files you want the vite tool to follow.
            ],
        }),
    ],
});
