<?php

add_action('init', 'custom_content_init');
/**
 * Funzione unica per registrare tutti i CPT e le tassonomie.
 * Questo migliora la leggibilità e assicura un unico punto di registrazione.
 */
function custom_content_init()
{
		// --- REGISTRAZIONE DEI CUSTOM POST TYPES ---
		// [config: singular, menu_position, plural (optional - defaults to singular + 's')]
		$types = [
				"courses"     => ["Course",   10],
				"seminars"    => ["Seminar",   21],
				// [2026-03-18 - Added archiprix CPT: mirrors post/courses/seminars rules. ACF option page kept intact and hidden - do not remove it]
				"archiprix"   => ["Archiprix", 22, "Archiprix"],
		];

		foreach ($types as $slug => $config) {

		$singular = $config[0];
		$position = $config[1];
		$plural   = isset($config[2]) ? $config[2] : $singular . 's';

				register_post_type($slug, [
						'labels'            => [
								'name'               => $plural,
								'singular_name'      => $singular,
								'add_new_item'       => 'Add New ' . $singular,
								'edit_item'          => 'Edit ' . $singular,
								'new_item'           => 'New ' . $singular,
								'view_item'          => 'View ' . $singular,
								'search_items'       => 'Search ' . $plural,
								'not_found'          => 'No ' . $plural . ' found',
								'not_found_in_trash' => 'No ' . $plural . ' in Trash',
								'all_items'          => 'All ' . $plural,
								'menu_name'          => $plural,
								'name_admin_bar'     => $singular,
						],
						'public'            => true,
						'show_in_menu'      => true,
						'show_in_admin_bar' => true,
						'hierarchical'      => true,
						'menu_position'     => $position, // <-- Applica la posizione univoca
						'supports'          => ['title', 'editor', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields'], // [2026-03-18 - Added 'custom-fields': required for ACF to show fields on seminars and all CPTs in this loop
						'has_archive'       => false,
						'rewrite'           => ['slug' => $slug],
						'show_in_rest'      => true, // Fondamentale per Gutenberg e API
				]);
		}

		// --- REGISTRAZIONE TASSONOMIA: Course Category ---
		register_taxonomy('course_category', ['courses'], [
				'labels'            => [
						'name'              => _x('Type', 'taxonomy general name', 'cs-textdomain'),
						'singular_name'     => _x('Type Course', 'taxonomy singular name', 'cs-textdomain'),
						'search_items'      => __('Find Type', 'cs-textdomain'),
						'all_items'         => __('All Type', 'cs-textdomain'),
						'parent_item'       => __('Parent Type', 'cs-textdomain'),
						'parent_item_colon' => __('Type:', 'cs-textdomain'),
						'menu_name'         => __('Type', 'cs-textdomain'),
				],
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'has_archive'       => true,
				'show_in_nav_menus' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => ['slug' => 'course-category'],
		]);

		// --- REGISTRAZIONE TASSONOMIA: Archiprix Category --- [2026-03-18 - Added archiprix_category taxonomy, mirroring seminar/course_category pattern]
	register_taxonomy('archiprix_category', ['archiprix'], [
			'labels'            => [
					'name'              => _x('Type', 'taxonomy general name', 'cs-textdomain'),
					'singular_name'     => _x('Type Archiprix', 'taxonomy singular name', 'cs-textdomain'),
					'search_items'      => __('Find Type', 'cs-textdomain'),
					'all_items'         => __('All Type', 'cs-textdomain'),
					'parent_item'       => __('Parent Type', 'cs-textdomain'),
					'parent_item_colon' => __('Type:', 'cs-textdomain'),
					'menu_name'         => __('Type', 'cs-textdomain'),
			],
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'has_archive'       => false,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => ['slug' => 'archiprix-category'],
	]);

	// --- REGISTRAZIONE TASSONOMIA: Seminar Category --- [2026-03-18 - Added seminar_category taxonomy, mirroring course_category pattern]
	register_taxonomy('seminar_category', ['seminars'], [
			'labels'            => [
					'name'              => _x('Type', 'taxonomy general name', 'cs-textdomain'),
					'singular_name'     => _x('Type Seminar', 'taxonomy singular name', 'cs-textdomain'),
					'search_items'      => __('Find Type', 'cs-textdomain'),
					'all_items'         => __('All Type', 'cs-textdomain'),
					'parent_item'       => __('Parent Type', 'cs-textdomain'),
					'parent_item_colon' => __('Type:', 'cs-textdomain'),
					'menu_name'         => __('Type', 'cs-textdomain'),
			],
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'has_archive'       => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => ['slug' => 'seminar-category'],
	]);

	// --- REGISTRAZIONE TASSONOMIA: Year (per i Post standard) ---
		// SOLUZIONE: Ho cambiato lo slug per evitare conflitti con pagine come "projects"
		register_taxonomy('project_year', ['post', 'courses'], [ // Ho reso il nome tassonomia più specifico: 'project_year' invece di 'year'
				'labels'            => [
						'name'          => _x('Project Year', 'taxonomy general name', 'mysite'),
						'singular_name' => _x('Project Year', 'taxonomy singular name', 'mysite'),
						'menu_name'     => __('Project Years', 'mysite'),
						'all_items'     => __('All Years', 'mysite'),
						'edit_item'     => __('Edit Year', 'mysite'),
						'not_found'     => __('No year found.', 'mysite'),
				],
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
				'rewrite'           => [
						'slug'       => 'archive-projects/year', // SLUG MODIFICATO: non crea più conflitto
						'with_front' => false,
				],
				'query_var'         => true, // query_var è meglio se unico, es. 'project_year_filter'
		]);

		// --- REGISTRAZIONE TASSONOMIA: Unit (per Post e Courses) ---
		register_taxonomy('unit', ['post', 'courses'], [
				'labels'            => [
						'name'          => _x('Unit', 'taxonomy general name', 'mysite'),
						'singular_name' => _x('Unit', 'taxonomy singular name', 'mysite'),
						'menu_name'     => __('Unit', 'mysite'),
						'all_items'     => __('All Units', 'mysite'),
						'edit_item'     => __('Edit Unit', 'mysite'),
						'not_found'     => __('No unit found.', 'mysite'),
				],
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => [
						'slug'       => 'unit',
						'with_front' => false,
				],
				'query_var'         => 'unit',
		]);
}

// L'hook di ACF può rimanere separato perché si aggancia a un'azione diversa ('acf/init')
add_action('acf/init', function () {
		if (function_exists('acf_add_options_page')) {
				// [2026-03-18 - Hidden from menu: archiprix is now a CPT. Option page kept for backward compatibility, do not remove]
		acf_add_options_page([
						'page_title'  => 'ArchiPrix',
						'menu_slug'   => 'archiprix',
						'position'    => '8',
						'redirect'    => false,
						'icon_url'    => 'dashicons-admin-generic',
						'show_in_menu' => false,
				]);
		}
});



add_filter('manage_courses_posts_columns', function($columns) {
		$columns['course_author'] = 'Utente';
		return $columns;
});

add_action('manage_courses_posts_custom_column', function($column, $post_id) {
		if ($column === 'course_author') {
				$author_id = get_post_field('post_author', $post_id);
				if ($author_id) {
						$user = get_userdata($author_id);
						echo esc_html($user->display_name);
				} else {
						echo '-';
				}
		}
}, 10, 2);




/**
 * Aggiunge link di export in strumenti
 */
add_action('admin_menu', function () {
		add_management_page(
				'Export Published Content',
				'Export Published Content',
				'manage_options',
				'export-published-content-trigger',
				'pb_export_page_screen'
		);
});

/**
 * Schermata con pulsante di export
 */
function pb_export_page_screen() {
		if (!current_user_can('manage_options')) {
				wp_die('Non autorizzato');
		}

		echo '<div class="wrap"><h2>Export contenuti pubblicati</h2>
		<p>Scarica CSV con post, courses e seminars pubblicati.</p>
		<a href="' . admin_url('admin-post.php?action=pb_export_published_content') . '" class="button button-primary">Scarica CSV</a>
		</div>';
}

/**
 * Export CSV correttamente senza stampare HTML
 */
add_action('admin_post_pb_export_published_content', function () {

		if (!current_user_can('manage_options')) {
				wp_die('Non autorizzato');
		}

		// Pulisce event. output precedente
		if (ob_get_length()) ob_end_clean();

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=published-content-' . date('Ymd-His') . '.csv');
		header("Pragma: no-cache");
		header("Expires: 0");

		$output = fopen('php://output', 'w');

		fputcsv($output, ['titolo', 'slug', 'data_pubblicazione', 'categoria']);

		$query = new WP_Query([
				'post_type'      => ['post', 'courses', 'seminars'],
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
		]);

		if ($query->have_posts()) {
				foreach ($query->posts as $post_id) {

						$title = get_the_title($post_id);
						$slug  = get_post_field('post_name', $post_id);
						$date  = get_post_field('post_date', $post_id);

						$terms = get_the_terms($post_id, 'category');
						if (!is_wp_error($terms) && !empty($terms)) {
								$cats_string = implode(' | ', wp_list_pluck($terms, 'name'));
						} else {
								$cats_string = '';
						}

						fputcsv($output, [$title, $slug, $date, $cats_string]);
				}
		}

		fclose($output);
		exit;
});





/**
 * Registra la pagina di amministrazione "Student" al livello 23
 */
function register_student_admin_page() {
		add_menu_page(
				'Lista Studenti',       // Titolo della pagina (<title>)
				'Student',              // Testo nel menu
				'edit_posts',           // Capability richiesta (chi può vederla)
				'student-list-page',    // Slug della pagina
				'render_student_view',  // Funzione che stampa l'HTML
				'dashicons-groups',     // Icona (ho scelto un'icona adatta a studenti/gruppi)
				23                      // Posizione menu
		);
}
add_action( 'admin_menu', 'register_student_admin_page' );

/**
 * Funzione di rendering della pagina
 * Esegue la query e mostra i dati dei repeater
 */
function render_student_view() {
		?>
		<div class="wrap">
				<h1>Student</h1>


				<div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; max-width: 800px;">
						<ul>
						<?php
						// 1. Query per recuperare tutti i corsi
						$args = array(
								'post_type'      => array ('courses', 'post'), // Assicurati che lo slug sia corretto
								'posts_per_page' => -1,        // Tutti i post
								'post_status'    => 'publish',
						);

						$courses_query = new WP_Query( $args );

						if ( $courses_query->have_posts() ) :
								while ( $courses_query->have_posts() ) : $courses_query->the_post();

										// Salviamo il titolo del corso per riferimento
										$course_title = get_the_title();

										// 2. Entriamo nel primo livello: general_information
										if ( have_rows( 'general_information' ) ) :
												while ( have_rows( 'general_information' ) ) : the_row();

														// 3. Entriamo nel secondo livello: students_list
														if ( have_rows( 'students_list' ) ) :
																while ( have_rows( 'students_list' ) ) : the_row();

																		// RECUPERO DATI
																		// Nota: Ho inserito 'name_student' qui dentro perché per fare una lista
																		// devi recuperare il campo per ogni riga del repeater.
																		$student_name = get_sub_field( 'name_student' );

																		if ( $student_name ) : ?>
																				<li style="padding: 5px 0; border-bottom: 1px solid #eee;">
																						<strong><?php echo esc_html( $student_name ); ?></strong>
																						<span style="color: #666; font-size: 0.9em;">
																								— subscribe in: <a href="<?php echo get_edit_post_link(); ?>"><?php echo esc_html( $course_title ); ?></a>
																						</span>
																				</li>
																		<?php endif;

																endwhile; // Fine students_list
														endif;

												endwhile; // Fine general_information
										endif;

								endwhile; // Fine ciclo post
								wp_reset_postdata();
						else :
								echo '<p>No course or student found.</p>';
						endif;
						?>
						</ul>
				</div>
		</div>
		<?php
}
