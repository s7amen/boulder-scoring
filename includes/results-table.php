<?php
function bsp_results_table_shortcode($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    $competition_id = intval($atts['id']);
    if (get_option('bsp_results_published') !== '1' && !current_user_can('edit_posts')) {
        return "<p>Резултатите ще бъдат публикувани след края на състезанието.</p>";
    }

    $results = get_posts([
        'post_type' => 'result',
        'posts_per_page' => -1,
        'meta_key' => 'competition_id',
        'meta_value' => $competition_id
    ]);
    $users = [];
    foreach ($results as $post) {
        $user_id = get_post_meta($post->ID, 'user_id', true);
        $score = floatval(get_post_meta($post->ID, 'total_score', true));
        $category = get_post_meta($post->ID, 'category', true) ?: 'неизвестна';
        if (!isset($users[$user_id])) {
            $user = get_user_by('id', $user_id);
            $users[$user_id] = ['name' => $user ? $user->display_name : "ID $user_id", 'category' => $category, 'score' => 0];
        }
        $users[$user_id]['score'] += $score;
    }
    $categories = ['мъже' => [], 'жени' => [], 'неизвестна' => []];
    foreach ($users as $u) {
        $categories[strtolower($u['category'])][] = $u;
    }

    ob_start();
    foreach ($categories as $cat => $group) {
        if (empty($group)) continue;
        echo "<h3>" . ucfirst($cat) . "</h3><table class='bsp-results-table'><tr><th>Състезател</th><th>Точки</th></tr>";
        usort($group, fn($a, $b) => $b['score'] <=> $a['score']);
        foreach ($group as $row) echo "<tr><td>{$row['name']}</td><td>" . round($row['score'], 1) . "</td></tr>";
        echo "</table>";
    }
    return ob_get_clean();
}
add_shortcode('bsp_results_table', 'bsp_results_table_shortcode');
