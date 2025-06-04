<?php
function bsp_register_registration_cpt() {
    register_post_type('bsp_registration', [
        'label' => 'Регистрации',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'custom-fields'],
        'menu_icon' => 'dashicons-id'
    ]);
}
add_action('init', 'bsp_register_registration_cpt');
