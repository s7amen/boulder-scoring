<?php
function bsp_leaderboard_shortcode($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    $competition_id = intval($atts['id']);

    // Взимаме всички резултати за това състезание
    $results = get_posts([
        'post_type' => 'result',
        'posts_per_page' => -1,
        'meta_query' => [
            ['key' => 'competition_id', 'value' => $competition_id]
        ]
    ]);

    if (!$results) return '<p>Все още няма резултати за това състезание.</p>';

    $users = [];

    foreach ($results as $res) {
        $user_id = get_post_meta($res->ID, 'user_id', true);
        if (!$user_id) continue;

        $top = get_post_meta($res->ID, 'top', true);
        $zone = get_post_meta($res->ID, 'zone', true);
        $score = floatval(get_post_meta($res->ID, 'total_score', true));
        $top_attempts = intval(get_post_meta($res->ID, 'top_attempts', true));
        $zone_attempts = intval(get_post_meta($res->ID, 'zone_attempts', true));

        if (!isset($users[$user_id])) {
            $users[$user_id] = [
                'username' => get_userdata($user_id)->user_login,
                'score' => 0,
                'tops' => 0,
                'zones' => 0,
                'top_attempts' => 0,
                'zone_attempts' => 0
            ];
        }

        $users[$user_id]['score'] += $score;
        if ($top) {
            $users[$user_id]['tops'] += 1;
            $users[$user_id]['top_attempts'] += $top_attempts;
        } elseif ($zone) {
            $users[$user_id]['zones'] += 1;
            $users[$user_id]['zone_attempts'] += $zone_attempts;
        }
    }

    // Сортиране по точки низходящо
    usort($users, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    ob_start();
    echo '<div class="mt-10">';
    echo '<h3 class="text-xl font-semibold mb-4 text-center">Класиране</h3>';
    echo '<table class="min-w-full border border-gray-300 text-sm">';
    echo '<thead><tr>
        <th class="px-3 py-2 border">Състезател</th>
        <th class="px-3 py-2 border text-right">Точки</th>
        <th class="px-3 py-2 border text-center">Топове</th>
        <th class="px-3 py-2 border text-center">Зони</th>
        <th class="px-3 py-2 border text-center">Опити (топ)</th>
        <th class="px-3 py-2 border text-center">Опити (зона)</th>
    </tr></thead><tbody>';

    foreach ($users as $u) {
        echo '<tr>
            <td class="px-3 py-2 border">' . esc_html($u['username']) . '</td>
            <td class="px-3 py-2 border text-right">' . round($u['score'], 1) . '</td>
            <td class="px-3 py-2 border text-center">' . $u['tops'] . '</td>
            <td class="px-3 py-2 border text-center">' . $u['zones'] . '</td>
            <td class="px-3 py-2 border text-center">' . $u['top_attempts'] . '</td>
            <td class="px-3 py-2 border text-center">' . $u['zone_attempts'] . '</td>
        </tr>';
    }

    echo '</tbody></table></div>';
    return ob_get_clean();
}
add_shortcode('bsp_leaderboard', 'bsp_leaderboard_shortcode');
