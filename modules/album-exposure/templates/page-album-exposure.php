<?php
/**
 * Template Name: Album Exposure
 */
$blogsite_album_exposure_settings = get_option('blogsite_album_exposure_settings');

update_option ('blogsite_connect_current_template_version','BLOGSITE_CONNECT_PLUGIN_VERSION');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo $blogsite_album_exposure_settings['meta_title']; ?></title>
		<meta name="description" content="<?php echo $blogsite_album_exposure_settings['meta_title']; ?>" /> 
		<?php do_action ('blogsite_before_stylesheet'); ?>
		<link rel="stylesheet" href="<?php echo get_bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<?php do_action ('blogsite_fix_stylesheet'); ?>

		<?php wp_head(); ?>
	</head>
	<body id="<?php echo BLOGSITE_CONNECT_PLUGIN_VERSION; ?>" <?php body_class(); ?>>
		<?php do_action ('album_exposure_menu'); ?>
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>

		<?php endwhile; ?>
		<?php wp_footer(); ?>
	</body>
</html>