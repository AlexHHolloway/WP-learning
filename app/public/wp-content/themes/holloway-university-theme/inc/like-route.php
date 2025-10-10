<?php 

add_action('rest_api_init', 'likeRoutes');

function likeRoutes () {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike',
    ));
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike',
    ));
};


function createLike($request) {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $professor_id = sanitize_text_field($request->get_param('liked_professor_id'));

        $likedQuery = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(
                array(
                    'key' => 'liked_professor_id',
                    'compare' => '=',
                    'value' => $professor_id,
                ),
            ),
        ));

        if ($likedQuery->found_posts() == 0) {
            return wp_insert_post(array(
            'post_type' => 'like',
            'post_status' => 'publish',
            'post_title' => 'Professor Like by: ' . $current_user->user_login,
            'meta_input' => array(
                'liked_professor_id' => $professor_id,
            ),
        ));
        } else {
            die("You have already liked this professor.");
        }
    }
}

function deleteLike ($request) {
    if (is_user_logged_in()) {
        $like_id = intval($request->get_param('like_id'));
        // Make sure the like post exists and belongs to the current user
        $like_post = get_post($like_id);
        if ($like_post && $like_post->post_type === 'like' && $like_post->post_author == get_current_user_id()) {
            wp_delete_post($like_id, true);
            return 'Like deleted.';
        } else {
            return new WP_Error('like_delete_error', 'Cannot delete this like.', array('status' => 403));
        }
    } else {
        return new WP_Error('not_logged_in', 'You must be logged in to delete a like.', array('status' => 403));
    }
}