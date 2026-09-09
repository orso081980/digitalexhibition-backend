<?php
/**
 * The template for displaying featured content
 *
 * @package Chique
 */
?>

<?php
$enable_desc = get_theme_mod( 'chique_reservation_info_option', 'disabled' );
$enable_form = get_theme_mod( 'chique_reservation_option', 'disabled' );
$highlight_text = get_theme_mod( 'chique_reservation_highlight_text', wp_kses_post( __( 'or call us at  <span>+123456789</span>', 'chique-pro' ) ) );


if ( ! chique_check_section( $enable_desc ) && ! chique_check_section( $enable_form ) ) {
	// Bail if featured content is disabled.
	return;
}

$layout = ' layout-one';
$reservation_align = ' ' . get_theme_mod( 'chique_reservation_align', 'reservation-aligned-right' );

if ( chique_check_section( $enable_desc ) && chique_check_section( $enable_form ) ) {
	$layout = ' layout-two';
	$reservation_align = '';
}
?>

<div class="reserve-content-wrapper section<?php echo esc_attr( $reservation_align ); ?>">
	<div class="wrapper">
		<div class="section-content-wrap<?php echo esc_attr( $layout ); ?>">
			<?php
			if ( chique_check_section( $enable_desc ) ) :
				$desc_title    = get_theme_mod( 'chique_reservation_info_title', esc_html__( 'Time', 'chique-pro' ) );
				$desc_subtitle = get_theme_mod( 'chique_reservation_info_subtitle', esc_html__( 'Open', 'chique-pro' ) );

				// Weekdays Settings.
				$weekdays_title = get_theme_mod( 'chique_reservation_weekdays_title', esc_html__( 'Mon-Fri', 'chique-pro' ) );
				$weekdays_desc  = get_theme_mod( 'chique_reservation_weekdays_desc', esc_html__( '7AM - 11 AM (Breakfast 11AM - 10PM (Lunch/Dinner)', 'chique-pro' ) );

				// Weekends Settings.
				$weekends_title = get_theme_mod( 'chique_reservation_weekends_title', esc_html__( 'Sat-Sun', 'chique-pro' ) );
				$weekends_desc  = get_theme_mod( 'chique_reservation_weekends_desc', esc_html__( '8AM - 1PM (Brunch) 1PM - 9PM (Lunch/Dinner)', 'chique-pro' ) );

				// Contact Info Settings.
				$contact_info  = get_theme_mod( 'chique_reservation_contact_info', esc_html__( '+1 3234 567 8901', 'chique-pro' ) );
			?>
			<article class="hentry contact-description">
				<div class="entry-container">
					<?php if ( $desc_title || $desc_subtitle ) : ?>
						<header class="entry-header">
							<h2 class="entry-title ">
								<?php if ( $desc_title ) : ?>
									<?php echo esc_html( $desc_title ); ?>
								<?php endif; ?>

								<?php if ( $desc_subtitle ) : ?>
									<span><?php echo esc_html( $desc_subtitle ); ?></span>
								<?php endif; ?>
							</h2>
						</header><!-- .entry-header -->
					<?php endif; ?>

					<div class="entry-content">
						<?php if ( $weekdays_title ) : ?>
						<strong><?php echo esc_html( $weekdays_title ); ?></strong>
						<?php endif; ?>

						<?php if ( $weekdays_desc ) : ?>
						<div class="description">
							<?php echo wp_kses_post( str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $weekdays_desc ) ) ); ?>
						</div>
						<?php endif; ?>

						<?php if ( $weekends_title ) : ?>
						<strong><?php echo esc_html( $weekends_title ); ?></strong>
						<?php endif; ?>

						<?php if ( $weekends_desc ) : ?>
						<div class="description">
							<?php echo wp_kses_post( str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $weekends_desc ) ) ); ?>
						</div>
						<?php endif; ?>

						<?php if ( $contact_info ) : ?>
						<div class="info">
							<?php echo wp_kses_post( str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $contact_info ) ) ); ?>
						</div>
						<?php endif; ?>
					</div><!-- .entry-content -->
				</div><!-- .entry-container -->
			</article>
			<?php endif; ?>

			<?php
			if ( chique_check_section( $enable_form ) ) :
				$type     = get_theme_mod( 'chique_reservation_type', 'post' );
				$title    = get_theme_mod( 'chique_reservation_title', esc_html__( 'Reservation', 'chique-pro' ) );
				$subtitle = get_theme_mod( 'chique_reservation_subtitle');
				?>
			<article class="hentry reservation-form">
				<div class="entry-container">
					<?php if ( $title || $subtitle ) : ?>
						<header class="entry-header">
							<h2 class="entry-title ">
								<?php if ( $title ) : ?>
									<?php echo esc_html( $title ); ?>
								<?php endif; ?>

								<?php if ( $subtitle ) : ?>
									<span><?php echo esc_html( $subtitle ); ?></span>
								<?php endif; ?>
							</h2>
						</header><!-- .entry-header -->
					<?php endif; ?>

					<?php
					$inner_content = '';

					if ('post' === $type && 'publish' === get_post_status( get_theme_mod( 'chique_reservation_post' ) ) ) {
						$post_object = get_post( get_theme_mod( 'chique_reservation_post' ) );

						$inner_content = apply_filters( 'the_content', $post_object->post_content );
					} elseif ( 'page' === $type  && 'publish' === get_post_status( get_theme_mod( 'chique_reservation_page' ) ) ) {
						$post_object = get_post( get_theme_mod( 'chique_reservation_page' ) );

						$inner_content = apply_filters( 'the_content', $post_object->post_content );
					} elseif ( 'custom' === $type ) {

						$inner_content = get_theme_mod( 'chique_reservation_custom' );
					}
					?>

					<div class="entry-content">
						<?php echo do_shortcode( $inner_content );?>
					</div><!-- .entry-content -->

					<?php if ( $highlight_text ) : ?>
						<p class="reservation-highlight-text"> <?php echo wp_kses_post( $highlight_text ); ?> </p>
					<?php endif; ?>

				</div><!-- .entry-container -->
			</article> <!-- article -->
			<?php endif; ?>
		</div><!-- .section-content-wrap -->
	</div><!-- .wrapper -->
</div> <!-- reserve-content-wrapper -->
