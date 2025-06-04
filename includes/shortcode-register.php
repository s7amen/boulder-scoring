<?php
function bsp_register_competitor_shortcode($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    $competition_id = intval($atts['id']);
    $user_id = get_current_user_id();

    if (!$user_id) {
        return '<p>Моля, <a href="/wp-login.php">влезте в профила си</a>, за да се регистрирате.</p>';
    }

    // Проверка за вече съществуваща регистрация
    $existing = get_posts([
        'post_type' => 'bsp_registration',
        'posts_per_page' => 1,
        'meta_query' => [
            ['key' => 'user_id', 'value' => $user_id],
            ['key' => 'competition_id', 'value' => $competition_id]
        ]
    ]);
    if (!empty($existing)) {
        return '<p>Вече сте регистриран за това състезание.</p>';
    }

    // Обработка на формуляра
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bsp_category'])) {
        $user = get_userdata($user_id);
        $competition_title = get_the_title($competition_id);
        $title = $user->first_name . ' ' . $user->last_name . ' - ' . $competition_title;

        $post_id = wp_insert_post([
            'post_type' => 'bsp_registration',
            'post_status' => 'publish',
            'post_title' => $title,
            'meta_input' => [
                'user_id' => $user_id,
                'competition_id' => $competition_id,
                'category' => sanitize_text_field($_POST['bsp_category'])
            ]
        ]);

        return '<p class="text-green-600">Успешно се регистрирахте!</p>';
    }

    ob_start(); ?>
    <form method="POST" class="max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mt-6">
        <h2 class="text-xl font-semibold mb-4">Регистрация за състезанието</h2>
        <label class="block mb-2">
            <input type="radio" name="bsp_category" value="male" required> Мъж
        </label>
        <label class="block mb-4">
            <input type="radio" name="bsp_category" value="female" required> Жена
        </label>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Регистрирай ме
        </button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('bsp_register_competitor', 'bsp_register_competitor_shortcode');
