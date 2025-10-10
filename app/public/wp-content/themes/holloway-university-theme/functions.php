<?php

require get_theme_file_path('./inc/search-route.php');
require get_theme_file_path('./inc/like-route.php');

// custom rest parameters
function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function () {
            return get_the_author();
        }
    ));
    register_rest_field('note', 'noteCount', array(
        'get_callback' => function () {
            return count_user_posts(get_current_user_id(), 'note');
        }
    ));
}

add_action('rest_api_init', 'university_custom_rest');

// page banner
function pageBanner($args = NULL) {

    if (!isset($args['title']) || !$args['title']) {
        $args['title'] = get_the_title();
    }
    if (!isset($args['subtitle']) || !$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!isset($args['photo'])) {
    if (get_field('page_banner_bg_image') AND !is_archive() AND !is_home() ) {
      $args['photo'] = get_field('page_banner_bg_image')['sizes']['pageBanner'];
    } else {
      $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }
}

    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(
            <?php 
                echo $args['photo'];
            ?>
        )">
        </div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
            <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
<?php }

// Enqueue theme stylesheet
function holloway_university_files() {
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);

    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

    wp_enqueue_style('holloway-university-style', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('holloway-university-extra-style', get_theme_file_uri('/build/index.css'));

    $current_user = wp_get_current_user();
    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest'),
        'userName' => is_user_logged_in() ? $current_user->user_login : '',
    ));
}

add_action('wp_enqueue_scripts', 'holloway_university_files');

function university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('campusPortrait', 500, 450, true);
    add_image_size('pageBanner', 1500, 350, true);

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

// Redirect sub accounts out of admin and to homepage
add_action('admin_init', 'redirectSubs');

function redirectSubs() {
    $currentUser = wp_get_current_user();

    if (count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

add_action('wp_loaded', 'noSubAdminBar');

function noSubAdminBar() {
    $currentUser = wp_get_current_user();

    if (count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

// Redirect to home page after logout
add_action('wp_logout', 'redirectAfterLogout');

function redirectAfterLogout() {
    wp_redirect(site_url('/'));
    exit;
}

// Customize login screen
add_filter('login_headerurl', 'customHeaderUrl');

function customHeaderUrl () {
    return esc_url(site_url('/'));
}

add_filter('login_headertext', 'customLoginHeaderText');

function customLoginHeaderText() {
    return get_bloginfo('name');
}

add_action('login_enqueue_scripts', 'customLoginCss');

function customLoginCss() {
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

    wp_enqueue_style('holloway-university-style', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('holloway-university-extra-style', get_theme_file_uri('/build/index.css'));
}

// force note posts to be private
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr) {
    if ($data['post_type'] == 'note' && $data['post_status'] != 'trash') {
        $data['post_status'] = "private";
    }
    // sanitize note data
    if ($data['post_type'] == 'note') {
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    return $data;
}

// Block note creation if user has reached limit
add_filter('rest_pre_insert_note', 'checkNoteLimit', 10, 2);

function checkNoteLimit($prepared_post, $request) {
    $noteCount = count_user_posts(get_current_user_id(), 'note');
    if ($noteCount >= 5) {
        return new WP_Error(
            'note_limit', 
            'You have reached your note limit of 5. Please delete an existing note to create a new one.', 
            array('status' => 403, 'noteCount' => $noteCount)
        );
    }
    return $prepared_post;
}

// Remove "Private: " from note titles
add_filter('private_title_format', 'removePrivatePrefix');

function removePrivatePrefix($title) {
    return '%s';
}
