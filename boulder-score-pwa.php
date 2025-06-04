<?php
/*
Plugin Name: Boulder Score PWA
Description: Стабилна PWA система за боулдър състезания с регистрации и точкуване.
Version: 1.3
Author: OpenAI
*/

defined('ABSPATH') or die('No script kiddies please!');

function bsp_load_plugin_files() {
    $includes = [
        'includes/cpt.php',
        'includes/admin-controls.php',
        'includes/enqueue.php',
        'includes/form-shortcode.php',
        'includes/rest-api.php',
        'includes/results-table.php',
    ];

    foreach ($includes as $file) {
        $path = plugin_dir_path(__FILE__) . $file;
        if (file_exists($path)) {
            require_once $path;
        }
    }
}
add_action('plugins_loaded', 'bsp_load_plugin_files');

require_once plugin_dir_path(__FILE__) . 'includes/results-current.php';

require_once plugin_dir_path(__FILE__) . 'includes/leaderboard.php';

require_once plugin_dir_path(__FILE__) . 'includes/cpt-registration.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode-register.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode-participants.php';