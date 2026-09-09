<?php
/**
 * The template for displaying featured content
 *
 * @package Chique
 */
?>

<?php
$enable = get_theme_mod( 'chique_contact_option', 'disabled' );
$form_type     = get_theme_mod( 'chique_contact_form_type', 'custom' ); 

if ( ! chique_check_section( $enable ) ) {
	// Bail if featured content is disabled.
	return;
}

$type          = get_theme_mod( 'chique_contact_type', 'category' );
$title         = get_theme_mod( 'chique_contact_title', esc_html__( 'Say Hello', 'chique-pro' ) );
$description   = get_theme_mod( 'chique_contact_description' );
$phone_label   = get_theme_mod( 'chique_contact_phone_label', 'Phone' );
$phone         = get_theme_mod( 'chique_contact_phone', '123-456-7890' );
$email_label   = get_theme_mod( 'chique_contact_email_label', 'Email' );
$email         = get_theme_mod( 'chique_contact_email', 'someone@somewhere.com' );
$address_label = get_theme_mod( 'chique_contact_address_label', 'Location' );
$address       = get_theme_mod( 'chique_contact_address', 'Boston, MA, USA' );
$map           = get_theme_mod( 'chique_contact_map' );
$contact_form = get_theme_mod( 'chique_display_contact_form', 1);

$classes[] = '';
if( ( ! $title && ! $description && ! $phone_label && ! $phone && ! $email_label && ! $email && ! $address_label && ! $address ) || ! $contact_form  ) {
	$classes[]	= 'single-section';
}

$contact_show = false;
$items        = 0;
$layout       = '';

if ( $phone || $email || $address ) {
	$contact_show = true;
}

if ( $contact_show ) {
	if ( $phone ) {
		$items++;
	}

	if ( $email ) {
		$items++;
	}

	if ( $address ) {
		$items++;
	}

	if ( 1 === $items ) {
		$layout = 'one-column';
	} elseif ( 2 === $items ) {
		$layout = 'two-columns';
	} elseif ( 3 === $items ) {
		$layout = 'three-columns';
	}
}

$class = 'layout-one';

if ( ( $phone || $email || $address ) && $map ) {
	$class = 'layout-two';
}

$map_link   = get_theme_mod( 'chique_contact_map_link' );
$map_target = get_theme_mod( 'chique_contact_map_target' ) ? '_blank' : '_self';
?>

<div id="contact-section" class="contact-section section<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<div class="section-content-wrap">
			<article class="hentry">
				<div class="hentry-inner">
					<div class="entry-container-wrapper">
						<?php if( $title || $description || $phone_label || $phone || $email_label || $email || $address_label || $address ) : ?>
							<div class="entry-container">
								<?php if ( $title || $description ) : ?>
									<header class="entry-header">
										<h2 class="entry-title">
											<?php if ( $title ) : ?>
												<?php echo wp_kses_post( $title ); ?>
											<?php endif; ?>

										<?php if ( $description ) : ?>
											<span>
												<?php echo esc_html( $description ); ?>
											</span><!-- .section-description -->
										<?php endif; ?>

										</h2>
									</header><!-- .entry-header -->
								<?php endif; ?>

								<div class="entry-content">
									<?php if ( $contact_show ) : ?>
										<ul class="contact-details <?php echo esc_attr( $layout ? ' ' . $layout : '' ); ?>">

											<?php if ( $email || $email_label ) : ?>
												<li>
												<?php if ( $email ) : ?>
													<span class="envelop">

														<i class="contact-icon fa fa-envelope"></i>

														<span class="contact-wrap">
															<?php if ( $email_label ) : ?>
																<span class="contact-label"><?php echo esc_html( $email_label ); ?></span>
															<?php endif; ?>
															<a target="_blank" title="<?php echo esc_attr( antispambot( $email ) ); ?>" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
																<span><?php echo esc_html( antispambot( $email ) ); ?></span>
															</a>
														</span> <!-- .contact-wrap -->
													</span>
												<?php endif; ?>
												</li><!-- #contact-item -->
											<?php endif; ?>

											<?php if ( $phone || $phone_label ) : ?>
											<li>
												<?php if ( $phone ) : ?>
													<span class="contact">
															<i class="fa fa-phone"></i>

															<span class="contact-wrap">
																<?php if ( $phone_label ) : ?>
																	<span class="contact-label"><?php echo esc_html( $phone_label ); ?></span>
																<?php endif; ?>
																<a target="_blank" title="<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>">
																	<span><?php echo esc_html( $phone ); ?></span>
																</a>
															</span> <!-- .contact-wrap -->
													</span>
												<?php endif; ?>
											</li><!-- #contact-item -->
											<?php endif; ?>

											<?php if ( $address || $address_label ) : ?>
											<li>
												<?php if ( $address ) : ?>
													<span class="address">

															<i class="fa fa-map-marker" aria-label="Icon Address"></i>

															<span class="contact-wrap">
																<?php if ( $address_label ) : ?>
																	<span class="contact-label"><?php echo esc_html( $address_label ); ?></span>
																<?php endif; ?>

																<?php
														$address_link = get_theme_mod( 'chique_contact_address_link' );

														if ( $address_link ) :
															$address_target = get_theme_mod( 'chique_contact_address_target' ) ? '_blank' : '_self';
														?>
														<a target="<?php echo $address_target; // WPCS ok. ?>" href="<?php echo esc_url( $address_link ); ?>">
														<?php endif; ?>

																<span><?php echo esc_html( $address ); ?></span>
														<?php if ( $address_link ) : ?>
														</a>
															</span> <!-- .contact-wrap -->
														<?php endif; ?>
													</span>
												<?php endif; ?>
											</li><!-- #contact-item -->
											<?php endif; ?>
										</ul>
									<?php endif; ?>

									<?php if ( has_nav_menu( 'social-contact' ) ) : ?>
										<div class="social-contact">
											<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'chique-pro' ); ?>">
											<?php
												wp_nav_menu( array(
													'theme_location'  => 'social-contact',
													'container'       => 'div',
													'container_class' => 'menu-social-container',
													'depth'           => 1,
													'link_before'     => '<span class="screen-reader-text">',
													'link_after'      => '</span>'
												) );
											?>
											</nav><!-- .social-navigation -->
										</div> <!-- site-social -->
									<?php endif; ?>
								</div><!-- .entry-content -->
							</div><!-- .entry-container -->
						<?php endif; ?>	

						<?php if ( $contact_form) : ?>
							<div class="contact-form <?php echo esc_attr( $form_type ); ?>">
							<?php
									$inner_content = '';

									if ('post' === $form_type && 'publish' === get_post_status( get_theme_mod( 'chique_contact_form_post' ) ) ) {
										$post_object = get_post( get_theme_mod( 'chique_contact_form_post' ) );

										$inner_content = apply_filters( 'the_content', $post_object->post_content );
									} elseif ( 'page' === $form_type  && 'publish' === get_post_status( get_theme_mod( 'chique_contact_form_page' ) ) ) {
										$post_object = get_post( get_theme_mod( 'chique_contact_form_page' ) );

										$inner_content = apply_filters( 'the_content', $post_object->post_content );
									} elseif ( 'custom' === $form_type ) {

										$inner_content = get_theme_mod( 'chique_contact_form_custom' );
									}
									?>

									<div class="entry-content">
										<?php echo do_shortcode( $inner_content );?> 
									</div>			

							</div>
						<?php endif; ?>	
					</div>	

					<?php if ( $map ) : ?>
					<div class="post-thumbnail contact-map">
						<a href="<?php echo esc_url( $map_link ); ?>" target="<?php echo esc_url( $map_target ); ?>">
							<img src="<?php echo esc_url( $map )?>">
						</a>
					</div><!-- .contact-map -->
					<?php endif; ?>
				</div>
			</article> <!-- article -->

		</div><!-- .section-content-wrap -->
	</div><!-- .wrapper -->
</div> <!-- #contact-section -->
