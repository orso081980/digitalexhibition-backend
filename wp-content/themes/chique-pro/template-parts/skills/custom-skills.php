<?php
/**
 * The template for displaying portfolio items
 *
 * @package Chique
 */

$image = get_theme_mod( 'chique_skills_image' );

if ( $image ) : ?>
<div class="about-me hentry">
	<img src="<?php echo esc_url( $image ); ?>" />
</div>
<?php endif; ?>

<div class="skill-bar-section hentry">
	<?php
	$title     = get_theme_mod( 'chique_skills_title', esc_html__( 'My Skills', 'chique-pro' ) );
	$sub_title = get_theme_mod( 'chique_skills_sub_title' );

	if ( $title || $sub_title ) : ?>
	<div class="skillbar-description">
		<header class="entry-header">
			<h2 class="entry-title ">
				<?php if ( $title ) : ?>
					<?php echo esc_html( $title ); ?>
				<?php endif; ?>

				<?php if ( $sub_title ) : ?>
					<span><?php echo esc_html( $sub_title ); ?></span>
				<?php endif; ?>
			</h2>
		</header><!-- .entry-header -->
	</div>
	<?php endif;

	for ( $i = 1; $i <= get_theme_mod( 'chique_skill_number', 4 ); $i++ ) {
		$title   = get_theme_mod( 'chique_skills_title_' . $i );
		$percent = get_theme_mod( 'chique_skills_percentage_' . $i );

		if ( $title || $percent ) :
		?>
		<div class="skillbar">
			<div class="skillbar-item" data-percent="90">
				<div class="skillbar-header">
					<?php if ( $title ) : ?>
					<h3 class="skillbar-title"><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>

					<?php if ( $percent ) : ?>
					<p class="skill-bar-percent"><?php echo esc_html( absint( $percent ) ); ?>%</p>
					<?php endif; ?>
				</div>

				<?php if ( $percent ) : ?>
				<div class="skillbar-content">
					<div class="skillbar-bar" style="width: <?php echo esc_attr( absint( $percent ) ); ?>%;"></div>
				</div><!-- .skillbar -->
				<?php endif; ?>
			</div><!-- .skillbar-item -->
		</div><!-- .skill-bar -->
		<?php
		endif;
	}
	?>

	<?php
	$more_text   = get_theme_mod( 'chique_skills_more_text' );
	$more_link   = get_theme_mod( 'chique_skills_more_link', '#' );
	$more_target = get_theme_mod( 'chique_skills_more_target' ) ? '_blank' : '_self' ;

	if ( $more_text ) : ?>
		<p class="view-all-button">
			<span class="more-button">
				<a class="more-link" href="<?php echo esc_url( $more_link ); ?>" target="<?php echo esc_attr( $more_target ); ?>"> <?php echo esc_html( $more_text ); ?> </a>
			</span>
		</p>
	<?php endif; ?>
</div><!-- .skill-bar-section -->
