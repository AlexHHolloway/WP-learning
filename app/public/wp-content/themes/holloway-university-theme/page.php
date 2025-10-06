<!-- this is the blog page -->

<?php get_header(); ?>

<?php 
    while(have_posts()) {
        the_post();
        pageBanner(array(
        'photo' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=2373&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
        ));
        ?>

        <div class="container container--narrow page-section">
            <?php 
                $theParent = wp_get_post_parent_id(get_the_ID());
                if ($theParent) { ?>
                    <div class="metabox metabox--position-up metabox--with-home-link">
                        <p>
                            <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>">
                                <i class="fa fa-home" aria-hidden="true"></i> 
                                Back to <?php echo get_the_title($theParent); ?>
                            </a> 
                            <span class="metabox__main"><?php the_title(); ?></span>
                        </p>
                    </div>
                <?php }
            ?>
            
            <?php 
            $testArray = get_pages(array(
                'child_of' => get_the_ID()
            ));

            if ($theParent or $testArray) { ?>
                    <div class="page-links">
                        <h2 class="page-links__title">
                            <a href="<?php echo get_permalink($theParent) ?>">
                                <?php echo get_the_title($theParent) ?>
                            </a></h2>
                        <ul class="min-list">
                            <?php 
                                if ($theParent) {
                                    $findChildrenOf = $theParent;
                                } else {
                                    $findChildrenOf = get_the_ID();
                                }
                                wp_list_pages(array(
                                    'title_li' => NULL,
                                    'child_of' => $findChildrenOf,
                                    'sort_column' => 'menu_order',
                                )); 
                            ?>
                        </ul>
                    </div>
                <?php }
            ?>

            <div class="generic-content">
                <?php the_content(); ?>
            </div>
        </div>

    <?php }
?>

<?php get_footer(); ?>