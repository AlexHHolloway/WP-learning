<?php
// Enqueue theme stylesheet
function Holloway_university_styles() {
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);

    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

    wp_enqueue_style('Holloway-university-style', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('Holloway-university-extra-style', get_theme_file_uri('/build/index.css'));
}

add_action('wp_enqueue_scripts', 'Holloway_university_styles');

function university_features() {
    add_theme_support('title-tag');
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerExploreLocation', 'Footer Explore Location');
    register_nav_menu('footerLearnLocation', 'Footer Learn Location');
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query) {
    // Only modify the main query on event archive pages during the actual page request
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('program')) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('event') && !wp_doing_ajax()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric',
            )
        ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries');
