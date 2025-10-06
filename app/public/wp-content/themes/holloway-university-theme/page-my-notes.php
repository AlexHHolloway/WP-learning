<?php 

    if (!is_user_logged_in()) {
        wp_redirect(esc_url(site_url('/')));
        exit;
    }

    get_header();

    while(have_posts()) {
        the_post();
        pageBanner(array(
            'photo' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=2373&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
        ));
        ?>

        <div class="container container--narrow page-section">
            <div class="create-note">
                <h2 class="headline headline--medium">Add New Note</h2>
                <input class="new-note-title" placeholder="Title" type="text">
                <textarea class="new-note-body" placeholder="Your note here..."></textarea>
                <span class="submit-note btn btn--blue btn--small">Create Note <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
            </div>

            <ul class="min-list link-list" id="my-notes">
                <?php
                    $userNotes = new WP_Query(array(
                        'post_type' => 'note',
                        'posts_per_page' => -1,
                        'author' => get_current_user_id(),
                        'post_status' => 'private',
                    ));

                    while($userNotes->have_posts()) {
                        $userNotes->the_post(); ?>
                        <li data-id="<?php the_ID(); ?>">
                            <input readonly class="note-title-field" value="<?php echo esc_attr(get_the_title()); ?>" type="text">
                            <span class="edit-note"><i class="fa fa-pencil" aria-hidden=true></i> Edit</span>
                            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden=true></i> Delete</span>
                            <textarea readonly class="note-body-field" name="" id=""><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>
                            <span class="update-note btn btn--blue btn--small">Save <i class="fa fa-arrow-right" aria-hidden=true></i></span>
                        </li>
                    <?php }
                ?>
            </ul>
        </div>
        
    <?php }
?>

<?php get_footer(); ?>