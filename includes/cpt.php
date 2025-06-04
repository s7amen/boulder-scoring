<?php
function bsp_register_cpts() {
    register_post_type('boulder', [
        'label' => 'Боулдъри',
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail']
    ]);
    register_post_type('result', [
        'label' => 'Резултати',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 30,
        'menu_icon' => 'dashicons-clipboard',
        'supports' => ['title', 'custom-fields']
    ]);
    register_post_type('bsp_competition', [
        'label' => 'Състезания',
        'public' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor']
    ]);
    register_post_type('child_registration', [
        'label' => 'Регистрирани деца',
        'public' => false,
        'show_ui' => true,
        'supports' => ['custom-fields']
    ]);
}
add_action('init', 'bsp_register_cpts');
