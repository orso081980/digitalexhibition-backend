<?php
/**
 * The template for displaying meta box in page/post
 *
 * This adds Select Sidebar, Header Featured Image Options, Single Page/Post Image Layout
 * This is only for the design purpose and not used to save any content
 *
 * @package Chique
 */



/**
 * Class to Renders and save metabox options
 *
 * @since Chique Pro 1.0
 */
class Chique_Metabox {
	private $meta_box;

	private $fields;

	/**
	* Constructor
	*
	* @since Chique Pro 1.0
	*
	* @access public
	*
	*/
	public function __construct( $meta_box_id, $meta_box_title, $post_type ) {

		$this->meta_box = array (
			'id' 		=> $meta_box_id,
			'title' 	=> $meta_box_title,
			'post_type' => $post_type,
		);

		$this->fields = array(
			'chique-header-image',
			'chique-sidebar-option',
			'chique-featured-image',
		);


		// Add metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add' ) );

		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	* Add Meta Box for multiple post types.
	*
	* @since Chique Pro 1.0
	*
	* @access public
	*/
	public function add( $post_type ) {
		add_meta_box( $this->meta_box['id'], $this->meta_box['title'], array( $this, 'show' ), $post_type, 'side', 'high' );
	}

	/**
	* Renders metabox
	*
	* @since Chique Pro 1.0
	*
	* @access public
	*/
	public function show() {
		global $post;

		$sidebar_options = array(
			'default-sidebar'        => esc_html__( 'Default Sidebar', 'chique-pro' ),
			'optional-sidebar-one'   => esc_html__( 'Optional Sidebar One', 'chique-pro' ),
			'optional-sidebar-two'   => esc_html__( 'Optional Sidebar Two', 'chique-pro' ),
			'optional-sidebar-three' => esc_html__( 'Optional Sidebar three', 'chique-pro' ),
		);

		$header_image_options 	= array(
			'default' => esc_html__( 'Default', 'chique-pro' ),
			'enable'  => esc_html__( 'Enable', 'chique-pro' ),
			'disable' => esc_html__( 'Disable', 'chique-pro' ),
		);

		$featured_image_options	= array(
			'disabled'                 => esc_html__( 'Disabled', 'chique-pro' ),
			'default'                  => esc_html__( 'Default', 'chique-pro' ),
			'post-thumbnail'           => esc_html__( 'Post Thumbnail (1060x596)', 'chique-pro' ),
			'chique-featured' => esc_html__( 'Featured (664x373)', 'chique-pro' ),
			'full'                     => esc_html__( 'Original Image Size', 'chique-pro' ),
		);


		// Use nonce for verification
		wp_nonce_field( basename( __FILE__ ), 'chique_custom_meta_box_nonce' );

		// Begin the field table and loop  ?>
		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="chique-sidebar-option"><?php esc_html_e( 'Select Sidebar', 'chique-pro' ); ?></label></p>
		<select class="widefat" name="chique-sidebar-option" id="chique-sidebar-option">
			 <?php
				$meta_value = get_post_meta( $post->ID, 'chique-sidebar-option', true );
				
				if ( empty( $meta_value ) ){
					$meta_value='default';
				}
				
				foreach ( $sidebar_options as $field =>$label ) {	
				?>
					<option value="<?php echo esc_attr( $field ); ?>" <?php selected( $meta_value, $field ); ?>><?php echo esc_html( $label ); ?></option>
				<?php
				} // end foreach
			?>
		</select>
		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="chique-header-image"><?php esc_html_e( 'Header Featured Image Options', 'chique-pro' ); ?></label></p>
		<select class="widefat" name="chique-header-image" id="chique-header-image">
			 <?php
				$meta_value = get_post_meta( $post->ID, 'chique-header-image', true );
				
				if ( empty( $meta_value ) ){
					$meta_value='default';
				}
				
				foreach ( $header_image_options as $field =>$label ) {	
				?>
					<option value="<?php echo esc_attr( $field ); ?>" <?php selected( $meta_value, $field ); ?>><?php echo esc_html( $label ); ?></option>
				<?php
				} // end foreach
			?>
		</select>

		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="chique-featured-image"><?php esc_html_e( 'Single Page/Post Image', 'chique-pro' ); ?></label></p>
		<select class="widefat" name="chique-featured-image" id="chique-featured-image">
			 <?php
				$meta_value = get_post_meta( $post->ID, 'chique-featured-image', true );
				
				if ( empty( $meta_value ) ){
					$meta_value='default';
				}
				
				foreach ( $featured_image_options as $field =>$label ) {	
				?>
					<option value="<?php echo esc_attr( $field ); ?>" <?php selected( $meta_value, $field ); ?>><?php echo esc_html( $label ); ?></option>
				<?php
				} // end foreach
			?>
		</select>
		<?php
	} 

	/**
	 * Save custom metabox data
	 *
	 * @action save_post
	 *
	 * @since Chique Pro 1.0
	 *
	 * @access public
	 */
	public function save( $post_id ) {
		global $post_type;

		$post_type_object = get_post_type_object( $post_type );

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                      // Check Autosave
		|| ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )        // Check Revision
		|| ( ! in_array( $post_type, $this->meta_box['post_type'] ) )                  // Check if current post type is supported.
		|| ( ! check_admin_referer( basename( __FILE__ ), 'chique_custom_meta_box_nonce') )    // Check nonce - Security
		|| ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) )  // Check permission
		{
		  return $post_id;
		}

		foreach ( $this->fields as $field ) {
			$new = $_POST[ $field ];

			delete_post_meta( $post_id, $field );

			if ( '' == $new || array() == $new ) {
				return;
			} else {
				if ( ! update_post_meta ( $post_id, $field, sanitize_key( $new ) ) ) {
					add_post_meta( $post_id, $field, sanitize_key( $new ), true );
				}
			}
		} // end foreach
	}
}

$chique_metabox = new Chique_Metabox(
	'chique-options', 					//metabox id
	esc_html__( 'Chique Pro Options', 'chique-pro' ), //metabox title
	array( 'page', 'post' )				//metabox post types
);
