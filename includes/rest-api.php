<?php
add_action('rest_api_init', function () {
    register_rest_route('bsp/v1', '/submit', [
        'methods' => 'POST',
        'callback' => 'bsp_submit_result',
        'permission_callback' => function () { return is_user_logged_in(); }
    ]);

    register_rest_route('bsp/v1', '/boulders/(?P<competition_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'bsp_get_boulders_by_competition',
        'permission_callback' => '__return_true'
    ]);
});

function bsp_submit_result($request) {
    $params = $request->get_json_params();
    $user_id = get_current_user_id();
    $competition_id = intval($params['competition_id'] ?? 0);
    $category = get_user_meta($user_id, 'bsp_category', true);

    $score = 0;
    $zone = !empty($params['zone']);
    $top = !empty($params['top']);
    $zone_attempts = min(10, intval($params['zone_attempts'] ?? 10));
    $top_attempts = min(10, intval($params['top_attempts'] ?? 10));

    if ($top) {
        $score = 25 - ($top_attempts * 0.1);
    } elseif ($zone) {
        $score = 10 - ($zone_attempts * 0.1);
    }

    $post_id = wp_insert_post([
        'post_type' => 'result',
        'post_status' => 'publish',
        'post_title' => "User $user_id - Boulder {$params['boulder']}",
        'meta_input' => [
            'user_id' => $user_id,
            'boulder_id' => $params['boulder'],
            'competition_id' => $competition_id,
            'zone' => $zone,
            'top' => $top,
            'zone_attempts' => $zone_attempts,
            'top_attempts' => $top_attempts,
            'total_score' => $score,
            'category' => $category,
        ]
    ]);

    return ['success' => true, 'post_id' => $post_id];
}

function bsp_get_boulders_by_competition($request) {
    $competition_id = $request['competition_id'];
    $boulders = get_posts([
        'post_type' => 'boulder',
        'posts_per_page' => -1,
        'meta_key' => 'competition_id',
        'meta_value' => $competition_id
    ]);

    $data = [];
    foreach ($boulders as $boulder) {
        $data[] = [
            'id' => $boulder->ID,
            'title' => get_the_title($boulder)
        ];
    }
    return $data;
}
