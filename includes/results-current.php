<?php
function bsp_current_results_shortcode($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    $competition_id = intval($atts['id']);
    $user_id = get_current_user_id();
    if (!$user_id) return '<p>Моля, влезте в системата, за да видите своите резултати.</p>';

    $results = get_posts([
        'post_type' => 'result',
        'posts_per_page' => -1,
        'meta_query' => [
            ['key' => 'user_id', 'value' => $user_id],
            ['key' => 'competition_id', 'value' => $competition_id]
        ]
    ]);

    if (!$results) return '<p class=\"text-gray-600 mt-6 text-center\">Все още няма въведени резултати.</p>';

    ob_start();
    echo '<div class=\"mt-10\">';
    echo '<h3 class=\"text-xl font-semibold mb-4 text-center\">Твоите въведени резултати</h3>';
    echo '<table class=\"min-w-full border border-gray-300 text-sm\">';
    echo '<thead><tr>
        <th class=\"px-3 py-2 border\">Боулдър</th>
        <th class=\"px-3 py-2 border\">Зона</th>
        <th class=\"px-3 py-2 border\">Топ</th>
        <th class=\"px-3 py-2 border\">Опити (зона/топ)</th>
        <th class=\"px-3 py-2 border\">Точки</th>
    </tr></thead><tbody>';

    foreach ($results as $res) {
        $boulder_id = get_post_meta($res->ID, 'boulder_id', true);
        $boulder_title = get_the_title($boulder_id);
        $zone = get_post_meta($res->ID, 'zone', true) ? '✔️' : '–';
        $top = get_post_meta($res->ID, 'top', true) ? '✔️' : '–';
        $zone_attempts = get_post_meta($res->ID, 'zone_attempts', true);
        $top_attempts = get_post_meta($res->ID, 'top_attempts', true);
        $score = get_post_meta($res->ID, 'total_score', true);

        echo "<tr>
            <td class='px-3 py-2 border'>" . esc_html($boulder_title) . "</td>
            <td class='px-3 py-2 border text-center'>$zone</td>
            <td class='px-3 py-2 border text-center'>$top</td>
            <td class='px-3 py-2 border text-center'>" . esc_html($zone_attempts) . " / " . esc_html($top_attempts) . "</td>
            <td class='px-3 py-2 border text-right'>" . round($score, 1) . "</td>
        </tr>";
    }

    echo '</tbody></table></div>';
    return ob_get_clean();
}
add_shortcode('bsp_current_results', 'bsp_current_results_shortcode');
