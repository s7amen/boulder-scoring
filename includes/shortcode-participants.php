<?php
function bsp_participants_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 0,
        'category' => ''
    ], $atts);

    $competition_id = intval($atts['id']);
    $category = sanitize_text_field($atts['category']);

    $query_args = [
        'post_type' => 'bsp_registration',
        'posts_per_page' => -1,
        'meta_query' => [
            ['key' => 'competition_id', 'value' => $competition_id]
        ]
    ];

    if ($category) {
        $query_args['meta_query'][] = ['key' => 'category', 'value' => $category];
    }

    $registrations = get_posts($query_args);

    if (!$registrations) {
        return '<p>Няма регистрирани участници за тази категория.</p>';
    }

    $title = $category === 'male' ? 'Регистрирани - мъже' :
             ($category === 'female' ? 'Регистрирани - жени' : 'Регистрирани участници');

    ob_start();
    echo '<div class="mt-8">';
    echo '<h3 class="text-xl font-semibold mb-4 text-center">' . esc_html($title) . '</h3>';
    echo '<ul class="list-disc list-inside">';

    foreach ($registrations as $r) {
        $name_only = explode(' - ', $r->post_title)[0]; // взимаме само името
        echo '<li>' . esc_html($name_only) . '</li>';
    }

    echo '</ul></div>';
    return ob_get_clean();
}
add_shortcode('bsp_participants', 'bsp_participants_shortcode');
