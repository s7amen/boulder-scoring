<?php
function bsp_enqueue_scripts() {
    wp_enqueue_style('bsp-style', plugin_dir_url(__FILE__) . '../assets/style.css');
    wp_enqueue_script('alpine', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], null, true);
    wp_enqueue_script('bsp-form', plugin_dir_url(__FILE__) . '../assets/form.js', ['alpine'], null, true);

    // Добавяме nonce за REST API
    wp_localize_script('bsp-form', 'bspData', [
        'nonce' => wp_create_nonce('wp_rest')
    ]);
}
add_action('wp_enqueue_scripts', 'bsp_enqueue_scripts', 100);
