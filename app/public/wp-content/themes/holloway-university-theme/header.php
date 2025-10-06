<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="site-header">
      <div class="container">
        <h1 class="school-logo-text float-left">
          <a href="<?php echo site_url(); ?>"><strong>Holloway</strong> University</a>
        </h1>
        <span class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
        <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
        <div class="site-header__menu group">
          <nav class="main-navigation">
            <?php 
              wp_nav_menu(array(
                'theme_location' => 'headerMenuLocation',
              ));
            ?>
            <!-- <ul>
              <li><a href="<?php echo site_url('/about-us'); ?>">About Us</a></li>
              <li><a href="#">Programs</a></li>
              <li><a href="#">Events</a></li>
              <li><a href="#">Campuses</a></li>
              <li><a href="#">Blog</a></li>
            </ul> -->
          </nav>
          <div class="site-header__util">
            <?php if(is_user_logged_in()) { ?>
              <?php 
                $currentUser = wp_get_current_user();
                $hasAvatar = get_avatar_url($currentUser->ID);
                if ($hasAvatar) { ?>
                  <a href="<?php echo esc_url(site_url('/my-notes')) ?>" class="btn btn--small btn--orange float-left push-right">My Notes</a>
                  <a href="<?php echo wp_logout_url() ?>" class="btn btn--small btn--dark-orange float-left push-right btn--with-photo">
                    <span class="site-header__avatar"><?php echo get_avatar($currentUser, 60); ?></span>
                    <span class="btn__text">Log Out</span>
                  </a>
                <?php } else { ?>
                  <a href="<?php echo wp_logout_url() ?>" class="btn btn--small btn--dark-orange float-left">Log Out</a>
                <?php } ?>
              <?php } else { ?>
                <a href="<?php echo wp_login_url() ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
                <a href="<?php echo wp_registration_url() ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>
              <?php }?>
            
            <span class="search-trigger js-search-trigger">
              <i class="fa fa-search" aria-hidden="true"></i>
            </span>
          </div>
        </div>
      </div>
    </header>

    <main>