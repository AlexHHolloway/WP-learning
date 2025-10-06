<?php 
  get_header();
  pageBanner(array(
    'title' => '404 - Page Not Found',
    'subtitle' => 'The page you are looking for does not exist.'
  ));
?>

<div class="container container--narrow page-section">
  <div class="generic-content">
    <p>Sorry, the page you were looking for cannot be found. It may have been moved or deleted.</p>
    <p>Here are some helpful links instead:</p>
    <ul>
      <li><a href="<?php echo site_url('/'); ?>">Home Page</a></li>
      <li><a href="<?php echo site_url('/about-us'); ?>">About Us</a></li>
      <li><a href="<?php echo get_post_type_archive_link('program'); ?>">Programs</a></li>
      <li><a href="<?php echo get_post_type_archive_link('event'); ?>">Events</a></li>
      <li><a href="<?php echo site_url('/blog'); ?>">Blog</a></li>
    </ul>
  </div>
</div>

<?php get_footer(); ?>
