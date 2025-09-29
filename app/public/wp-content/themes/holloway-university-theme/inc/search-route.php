<?php

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch() {
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'universitySearchResults',
    ));
}

function universitySearchResults($data) {
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
        's' => sanitize_text_field($data['term']),
    ));

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array(),
    );

    while($mainQuery->have_posts()) {
        $mainQuery->the_post();

        if (get_post_type() == 'post' || get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                'postType' => get_post_type(),
                'title' => get_the_title(),
                'link' => get_the_permalink(),
                'authorName' => get_the_author(),
            ));
        }
        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'postType' => get_post_type(),
                'title' => get_the_title(),
                'link' => get_the_permalink(),
                'authorName' => get_the_author(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
            ));
        }
        if (get_post_type() == 'program') {
            array_push($results['programs'], array(
                'postType' => get_post_type(),
                'title' => get_the_title(),
                'link' => get_the_permalink(),
                'authorName' => get_the_author(),
            ));
        }
        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'postType' => get_post_type(),
                'title' => get_the_title(),
                'link' => get_the_permalink(),
                'authorName' => get_the_author(),
            ));
        }

        $eventDate = new DateTime(get_field('event_date'));
        $description = null;
        if (has_excerpt()) {
            $description = get_the_excerpt();
        } else {
            $description = wp_trim_words(get_the_content(), 18);
        }

        if (get_post_type() == 'event') {
            array_push($results['events'], array(
                'postType' => get_post_type(),
                'title' => get_the_title(),
                'link' => get_the_permalink(),
                'authorName' => get_the_author(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description,
            ));
        }
    }

    wp_reset_postdata();
    return $results;
}