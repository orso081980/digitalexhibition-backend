<?php
/**
 * Custom Controls
 *
 * @package Chique
 */

/**
 * Add Custom Controls
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_custom_controls( $wp_customize ) {
	// Custom control for Important Links.
	class Chique_Important_Links extends WP_Customize_Control {
		public $type = 'important-links';

		public function render_content() {
			// Add Theme instruction, Support Forum, Changelog, Donate link, Review, Facebook, Twitter, Google+, Pinterest links.
			$important_links = array(
				'theme_instructions' => array(
					'link'  => esc_url( 'https://catchthemes.com/themes/chique-pro/#theme-instructions' ),
					'text'  => esc_html__( 'Theme Instructions', 'chique-pro' ),
					),
				'support' => array(
					'link'  => esc_url( 'https://catchthemes.com/support/' ),
					'text'  => esc_html__( 'Support', 'chique-pro' ),
					),
				'changelog' => array(
					'link'  => esc_url( 'https://catchthemes.com/changelogs/chique-theme/' ),
					'text'  => esc_html__( 'Changelog', 'chique-pro' ),
					),
				'facebook' => array(
					'link'  => esc_url( 'https://www.facebook.com/catchthemes/' ),
					'text'  => esc_html__( 'Facebook', 'chique-pro' ),
					),
				'twitter' => array(
					'link'  => esc_url( 'https://twitter.com/catchthemes/' ),
					'text'  => esc_html__( 'Twitter', 'chique-pro' ),
					),
				'gplus' => array(
					'link'  => esc_url( 'https://plus.google.com/+Catchthemes/' ),
					'text'  => esc_html__( 'Google+', 'chique-pro' ),
					),
				'pinterest' => array(
					'link'  => esc_url( 'http://www.pinterest.com/catchthemes/' ),
					'text'  => esc_html__( 'Pinterest', 'chique-pro' ),
					),
			);

			foreach ( $important_links as $important_link ) {
				echo '<p><a target="_blank" href="' . $important_link['link'] . '" >' . $important_link['text'] . ' </a></p>'; // WPCS: XSS OK.
			}
		}
	}

	// Custom control for dropdown category multiple select.
	class Chique_Multi_Cat extends WP_Customize_Control {
		public $type = 'dropdown-categories';

		public function render_content() {
			$dropdown = wp_dropdown_categories(
				array(
					'name'             => $this->id,
					'echo'             => 0,
					'hide_empty'       => false,
					'show_option_none' => false,
					'hide_if_empty'    => false,
					'show_option_all'  => esc_html__( 'All Categories', 'chique-pro' ),
				)
			);

			$dropdown = str_replace( '<select', '<select multiple = "multiple" style = "height:150px;" ' . $this->get_link(), $dropdown );

			printf( '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',esc_html( $this->label ), $dropdown ); // WPCS: XSS OK.
			echo '<p class="description">' . esc_html__( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'chique-pro' ) . '</p>';
		}
	}

	// Custom control for dropdown category multiple select.
	class Chique_Multi_Categories_Control extends WP_Customize_Control {
		public $type = 'dropdown-categories';

		public function render_content() {
			$dropdown = wp_dropdown_categories(
				array(
					'name'             => $this->id,
					'echo'             => 0,
					'hide_empty'       => false,
					'show_option_none' => false,
					'hide_if_empty'    => false,
					'show_option_all'  => esc_html__( 'All Categories', 'chique-pro' ),
				)
			);

			$dropdown = str_replace( '<select', '<select multiple = "multiple" style = "height:150px;" ' . $this->get_link(), $dropdown );

			printf( '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',esc_html( $this->label ), $dropdown ); // WPCS: XSS OK.
			echo '<p class="description">' . esc_html__( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'chique-pro' ) . '</p>';
		}
	}

	// Custom control for any note, use label as output description.
	class Chique_Note_Control extends WP_Customize_Control {
		public $type = 'description';

		public function render_content() {
			echo '<h2 class="description">' . $this->label . '</h2>'; // WPCS: XSS OK.
		}
	}

	class Chique_Sortable_Custom_Control extends WP_Customize_Control {
		public $type = 'sortable';

		public $sortable_sections =array();

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @uses WP_Customize_Control::__construct()
		 *
		 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
		 * @param string               $id      Control ID.
		 * @param array                $args    Optional. Arguments to override class property defaults.
		 */
		public function __construct( $manager, $id, $args = array() ) {

			// Calls the parent __construct
			parent::__construct( $manager, $id, $args );

			// Set Sortable Sections
			$sortable_sections = chique_get_sortable_sections();
			$this->sortable_sections = apply_filters( 'customizer_sortable_sections', $sortable_sections, $id );

		}

		/**
		* Render the control's content.
		*/
		public function render_content() {
			$sortable_sections = $this->sortable_sections;
			$sortable_sections = array_merge( array_flip( explode( ',', $this->value() ) ), $sortable_sections );
		?>
			<ul class="custom-sortable">
				<?php
				foreach ( $sortable_sections as $key => $value ) {
					echo '<li id="' . esc_attr( $key ) . '" >';
					echo '<span class="label">' . esc_html( $value['label'] ) . '</span>';
					if ( isset( $value['section'] ) ) {
						echo '<a href="javascript:wp.customize.section( \'' . esc_attr( $value['section'] ) . '\' ).focus();">' . esc_html__( 'Edit', 'chique-pro' ) . '</a>';
					}
					echo '</li>';
				}
			    ?>
			</ul>

			<input id="chique_sortable_value" type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
		<?php
		}
	}

	class Chique_Toggle_Control extends WP_Customize_Control {
		public $type = 'light';

		/**
		 * Render the control's content.
		 */
		public function render_content() {
			?>
			<label>
				<div style="display:flex;flex-direction: row;justify-content: flex-start;">
					<span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php echo esc_html( $this->label ); ?></span>
					<input id="cb<?php echo $this->instance_number; ?>" type="checkbox" class="tgl tgl-<?php echo $this->type; ?>" value="<?php echo esc_attr( $this->value() ); ?>"
											<?php
											$this->link();
											checked( $this->value() );
											?>
					 />
					<label for="cb<?php echo $this->instance_number; ?>" class="tgl-btn"></label>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
			</label>
			<?php
		}
		/**
		 * Plugin / theme agnostic path to URL
		 *
		 * @see https://wordpress.stackexchange.com/a/264870/14546
		 * @param string $path  file path
		 * @return string       URL
		 */
		private function abs_path_to_url( $path = '' ) {
			$url = str_replace(
				wp_normalize_path( untrailingslashit( ABSPATH ) ),
				site_url(),
				wp_normalize_path( $path )
			);
			return esc_url_raw( $url );
		}
	}
}
add_action( 'customize_register', 'chique_custom_controls', 1 );
