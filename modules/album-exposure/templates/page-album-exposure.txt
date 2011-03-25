<?php
/**
 * Template Name: Album Exposure
 */
$blogsite_album_exposure_settings = get_option('blogsite_album_exposure_settings');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo $blogsite_album_exposure_settings['meta_title']; ?></title>
		<meta name="description" content="<?php echo $blogsite_album_exposure_settings['meta_title']; ?>" /> 
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>

		<?php endwhile; ?>
		<?php wp_footer(); ?>
	</body>
</html>