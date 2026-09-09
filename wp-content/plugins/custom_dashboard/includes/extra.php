<?php

/**
 * Modifica il nome della voce di menu principale da "Articoli" a "Projects".
 */
add_action("init", "custom_change_post_object_label");
function custom_change_post_object_label()
{
	global $wp_post_types;
	$labels = &$wp_post_types["post"]->labels;
	$labels->name = "Projects";
	$labels->singular_name = "Project";
	$labels->menu_name = "Project";
	$labels->name_admin_bar = "Project";
	$labels->add_new = "Add New";
	$labels->add_new_item = "Add New Project";
	$labels->edit_item = "Edit Project";
	$labels->new_item = "New Project";
	$labels->view_item = "View Project";
	$labels->search_items = "Search Projects";
	$labels->not_found = "No Projects found";
	$labels->not_found_in_trash = "No Projects found in Trash";
}



add_action('init', 'custom_rename_category_to_unit');
function custom_rename_category_to_unit()
{
		global $wp_taxonomies;
		if (!isset($wp_taxonomies['category'])) {
				return;
		}
		$labels = &$wp_taxonomies['category']->labels;
		$labels->name = 'Type';
		$labels->singular_name = 'Type';
		$labels->menu_name = 'Type';
		$labels->all_items = 'All Type';
		$labels->edit_item = 'Edit Type';
		$labels->view_item = 'View Type';
		$labels->update_item = 'Update Type';
		$labels->add_new_item = 'Add New Type';
		$labels->new_item_name = 'New Unit Name';
		$labels->search_items = 'Search Type';
		$labels->popular_items = 'Popular Type';
		$labels->not_found = 'No Unit found';
		$labels->parent_item = 'Parent Type';
		$labels->parent_item_colon = 'Parent Type:';
}


add_action("admin_menu", "custom_change_posts_menu_label");
function custom_change_posts_menu_label()
{
	global $menu, $submenu;
	foreach ($menu as $key => $item) {
		if ("index.php" === $item[2]) {
			$menu[$key][0] = "Home";
		}
	}
	if (isset($submenu["index.php"])) {
		$submenu["index.php"][0][0] = "Home";
	}

	$menu[5][0] = "Projects";
	$submenu["edit.php"][5][0] = "All Projects";
	$submenu["edit.php"][10][0] = "Add Project";
}








add_action("admin_bar_menu", "replace_wp_logo", 999);
function replace_wp_logo($wp_admin_bar)
{
	$logo_url = plugins_url("../images/logo_tue.png", __FILE__);
	$wp_admin_bar->add_node([
		"id" => "wp-logo",
		"meta" => [
			"html" =>
				'<img src="' .
				esc_url($logo_url) .
				'" style="max-height:35px; width:auto; display:block;">',
		],
	]);
}
add_action("admin_head", "custom_wp_admin_bar_css");
function custom_wp_admin_bar_css()
{
	$url = plugins_url("../images/logo_tue.png", __FILE__);
	echo '<style>
    #wp-admin-bar-wp-logo {
        width: 180px!important;
        display: flex!important;
    }

         #wp-admin-bar-wp-logo .ab-icon {
             background-image: url("' .
		$url .
		'")!important;
             background-repeat: no-repeat!important;
             background-position: center center!important;
             background-size: contain!important;
             width:20px!important;
             height:20px!important;
             display:inline-block!important;
         }

     </style>';
}

add_action("login_enqueue_scripts", "custom_login_logo");
function custom_login_logo()
{
	$url = plugins_url("../images/logo_tue.png", __FILE__);
	echo '<style>

         #login h1 a {
             background-image: url("' .
		$url .
		'") !important;
             background-size: contain !important;
             width: 280px !important;
             height: 80px !important;
         }

     </style>';
}

function cd_add_inline_js_for_menu_fix()
{
	// Il nostro codice JavaScript salvato in una variabile PHP.
	$custom_js = "
        jQuery(document).ready(function($) {
            // Selezioniamo il link genitore tramite lo slug che abbiamo definito.
            var \$newsParentLink = $('#toplevel_page_cd-news-parent > a');

            // Applichiamo le modifiche per disabilitare il link.
            if (\$newsParentLink.length) {
                \$newsParentLink.css('cursor', 'default');
                \$newsParentLink.on('click', function(e) {
                    e.preventDefault();
                });
            }
        });
    ";

	wp_add_inline_script("jquery-core", $custom_js);
}
add_action("admin_enqueue_scripts", "cd_add_inline_js_for_menu_fix");

add_action("wp_head", "custom_wp_head_bar_css");
function custom_wp_head_bar_css()
{
	echo '<style>
     #wp-admin-bar-wp-logo {
          width: 180px!important;
          display: flex!important;
      }
      </style>';
}

function cd_filter_dashboard_title_text($translated_text, $text, $domain)
{
	// Eseguiamo la logica solo per la stringa "Dashboard"
	if ("Dashboard" === $text && "default" === $domain) {
		if (is_admin() && function_exists("get_current_screen")) {
			$screen = get_current_screen();
			if ($screen && "dashboard" === $screen->id) {
				$current_user = wp_get_current_user();
				return "Welcome, " . esc_html($current_user->display_name);
			}
		}
	}

	return $translated_text;
}
add_filter("gettext", "cd_filter_dashboard_title_text", 20, 3);

function cd_register_seo_field_group()
{
	if (function_exists("acf_add_local_field_group")) {
		acf_add_local_field_group([
			"key" => "group_seo_cd",
			"title" => "SEO Settings",
			"fields" => [
				[
					"key" => "field_cd_meta_title",
					"label" => "Meta Title",
					"name" => "cd_meta_title",
					"type" => "text",
					"instructions" =>
						"Enter an optimized title. If left blank, the post title will be used.",
					"placeholder" => "SEO Title Custom",
				],
				[
					"key" => "field_cd_meta_description",
					"label" => "Meta Description",
					"name" => "cd_meta_description",
					"type" => "textarea",
					"instructions" =>
						"Enter a description for search engines and social media (max 160 characters).",
					"maxlength" => 160,
					"rows" => 3,
				],
			],
			"location" => [
				[
					[
						"param" => "post_type",
						"operator" => "!=",
						"value" => "acf-field-group",
					],
				],
			],
			"menu_order" => 0,
			"position" => "side", // Cambiato in 'side' per maggiore pulizia
			"style" => "default",
			"label_placement" => "top",
			"instruction_placement" => "label",
			"hide_on_screen" => "",
			"active" => true,
			"description" => 'Fields for on-page SEO optimization.',
		]);
	}
}
add_action("acf/init", "cd_register_seo_field_group");

/**
 * Aggiunge i tag Open Graph all'head con la logica corretta.
 */
function cd_add_opengraph_tags()
{
	// Esegui solo su post/pagine/cpt singoli o sulla home page
	if (!is_singular() && !is_front_page()) {
		return;
	}

	$post_id = get_queried_object_id();

	// --- ANNOTAZIONE (CORREZIONE 1) ---
	// Recupero il campo usando il nome corretto: 'cd_meta_title' invece di 'title'.
	$meta_title = get_field("cd_meta_title", $post_id);
	$og_title = $meta_title ? $meta_title : get_the_title($post_id);

	// --- ANNOTAZIONE (NUOVA LOGICA) ---
	// Prima provo a prendere la descrizione dal campo SEO dedicato.
	$og_desc = get_field("cd_meta_description", $post_id);

	// Se è vuota, applico il nuovo fallback richiesto.
	if (empty($og_desc)) {
		// Recupero il gruppo ACF 'general_information'.
		$general_info_group = get_field("general_information", $post_id);

		// Se il gruppo e il suo sotto-campo 'description' esistono, uso quel valore.
		if (
			!empty($general_info_group) &&
			!empty($general_info_group["description"])
		) {
			$og_desc = $general_info_group["description"];
		}
	}

	// Pulizia finale della descrizione (essenziale!)
	$og_desc_clean = "";
	if (!empty($og_desc)) {
		$og_desc_clean = wp_strip_all_tags($og_desc);
		$og_desc_clean = wp_trim_words($og_desc_clean, 25, "...");
	}

	// Recupero degli altri dati (queste parti erano già corrette)
	$og_image = has_post_thumbnail($post_id)
		? get_the_post_thumbnail_url($post_id, "full")
		: "";
	$og_url = is_front_page() ? home_url("/") : get_permalink($post_id);
	$og_type = is_singular("post") ? "article" : "website";

	// Stampa i tag in modo pulito
	echo "\n\n";
	echo '<meta property="og:title" content="' .
		esc_attr($og_title) .
		'" />' .
		"\n";
	echo '<meta property="og:url" content="' . esc_url($og_url) . '" />' . "\n";
	echo '<meta property="og:type" content="' .
		esc_attr($og_type) .
		'" />' .
		"\n";
	if (!empty($og_desc_clean)) {
		echo '<meta property="og:description" content="' .
			esc_attr($og_desc_clean) .
			'" />' .
			"\n";
	}
	if (!empty($og_image)) {
		echo '<meta property="og:image" content="' .
			esc_url($og_image) .
			'" />' .
			"\n";
	}
	echo "\n\n";
}
add_action("wp_head", "cd_add_opengraph_tags");



// [2026-03-18] Disabled: was filtering search results to post_date year = current year,
// causing zero results since posts are published in previous years.
// function limit_frontend_search_to_current_year( $query ) {
// 		if ( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
// 				$query->set( 'year', date( 'Y' ) );
// 		}
// }
// add_action( 'pre_get_posts', 'limit_frontend_search_to_current_year' );



/**
	ARCHIPRIX PAGE DISPLAY POST
 */

class Elementor_ACF_Query_Bridge {

		private static $project_ids = null;

		public static function init() {
				// 1. Recupero dati (Frontend + Backend)
				// Usiamo acf/init perché è sicuro e avviene prima del rendering delle tabelle admin.
				add_action( 'acf/init', [ self::class, 'fetch_data_from_acf' ] );

				// 2. Elementor Query (Frontend)
				add_action( 'elementor/query/Architectural-Engineering', [ self::class, 'modify_elementor_query' ] );

				// 3. Admin Columns (Backend)
				// Aggiungiamo le colonne per i post type 'post', 'courses' e 'seminars'.
				add_action( 'admin_init', [ self::class, 'register_admin_columns' ] );
		}

		public static function fetch_data_from_acf() {
				// Evitiamo di ricaricare se già fatto (cache in memoria della classe)
				if ( self::$project_ids !== null ) {
						return;
				}

				$selected_posts = get_field( 'graduation_projects_selection', 'option' );

				if ( ! empty( $selected_posts ) && is_array( $selected_posts ) ) {
						// Gestione robusta: controlliamo se ACF restituisce oggetti (se impostato su Post Object) o ID.
						if ( isset( $selected_posts[0] ) && is_object( $selected_posts[0] ) && isset( $selected_posts[0]->ID ) ) {
								self::$project_ids = wp_list_pluck( $selected_posts, 'ID' );
						} else {
								self::$project_ids = $selected_posts; // Già array di ID
						}
				} else {
						self::$project_ids = [];
				}
		}

		public static function modify_elementor_query( $query ) {
				if ( self::$project_ids !== null && ! empty( self::$project_ids ) ) {
						$query->set( 'post__in', self::$project_ids );
						$query->set( 'post_type', 'any' );
						$query->set( 'orderby', 'post__in' );
				}
		}

		/* ---------------------------------------------------------
		 * GESTIONE COLONNE ADMIN
		 * --------------------------------------------------------- */

		public static function register_admin_columns() {
				// Definiamo i post type su cui agire
				$post_types = [ 'post', 'courses', 'seminars', 'archiprix' ]; // [2026-03-18 - Added archiprix CPT]

				foreach ( $post_types as $pt ) {
						// Aggiunge l'intestazione della colonna
						add_filter( "manage_{$pt}_posts_columns", [ self::class, 'add_archiprix_column_head' ] );
						// Gestisce il contenuto della colonna
						add_action( "manage_{$pt}_posts_custom_column", [ self::class, 'render_archiprix_column_content' ], 10, 2 );
				}
		}

		public static function add_archiprix_column_head( $columns ) {
				// Inseriamo la colonna alla fine (o dove preferisci)
				$columns['archiprix_status'] = '<span title="Selezionato in Archiprix Option Page">Archiprix</span>';
				return $columns;
		}

		public static function render_archiprix_column_content( $column_name, $post_id ) {
				if ( 'archiprix_status' !== $column_name ) {
						return;
				}

				// Assicuriamoci che i dati siano caricati (caso limite se acf/init non fosse scattato)
				if ( self::$project_ids === null ) {
						self::fetch_data_from_acf();
				}

				// Controllo se l'ID corrente è nell'array dei selezionati
				if ( in_array( $post_id, self::$project_ids ) ) {
						// Icona Checkbox Spuntato (Verde scuro per visibilità)
						echo '<span class="dashicons dashicons-yes-alt" style="color: #46b450; font-size: 25px;"></span>';
						echo '<span class="screen-reader-text">Selezionato</span>';
				} else {
						// Icona vuota o trattino per non fare rumore visivo
						echo '<span class="dashicons dashicons-minus" style="color: #ccc;"></span>';
				}
		}

}

Elementor_ACF_Query_Bridge::init();









// [rimosso] sdv_add_courses_link_updater_script: vecchio hack che riscriveva gli
// h2 a da /courses/ a /course-category/ in DOMContentLoaded, sovrascrivendo
// cd_inject_filter_link_script (sistema cd_filter). Superato.


function global_script() { ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
		// Seleziona gli elementi con le NUOVE CLASSI
		var searchButton = document.querySelector('.custom-search-submit-button');
		var searchContainer = document.querySelector('.custom-search-wrapper');
		var searchField = document.querySelector('.custom-search-field-input');
		var searchForm = document.querySelector('.custom-search-form');

		if (searchButton && searchContainer && searchField && searchForm) {

				searchButton.addEventListener('click', function(event) {
						// Usa la nuova classe di stato 'expanded-custom'
						if (!searchContainer.classList.contains('expanded-custom')) {
								event.preventDefault();
								searchContainer.classList.add('expanded-custom'); // Espandi
								searchField.focus(); // Metti il focus
						} else {
								// Se è già espanso, invia il form
								if (searchField.value.length > 0) {
										searchForm.submit();
								}
						}
				});

				// Gestione tasto Invio
				searchField.addEventListener('keydown', function(event) {
						if (event.key === 'Enter') {
								searchForm.submit();
						}
				});

				// Chiudi la ricerca cliccando fuori dall'area
				document.addEventListener('click', function(event) {
						var isClickInsideForm = searchForm.contains(event.target) || searchButton.contains(event.target);
						if (!isClickInsideForm && searchContainer.classList.contains('expanded-custom')) {
								searchContainer.classList.remove('expanded-custom');
						}
				});

		} else {
				console.error("Errore script: Impossibile trovare i nuovi elementi HTML della ricerca.");
		}
});
</script>


<?php }

add_action( 'wp_footer', 'global_script' );

function vb_add_sticky_bottom_actions() {
		// SECURITY CHECK:
		// We strictly check if we are on the 'post' screen (edit or new).
		// Removed the 'is_block_editor' check to support Classic Editor as well.
		$screen = get_current_screen();
		if ( ! $screen || $screen->base !== 'post' ) {
				return;
		}

		?>
		<style>
				/* Container styling */
				#vb-sticky-actions {
						position: fixed;
						bottom: -100px; /* Hidden by default */
						left: 0;
						width: 100%;
						background: #ffffff;
						border-top: 1px solid #e0e0e0;
						padding: 15px 20px;
						box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
						z-index: 99999; /* High z-index to overlay WP footer */
						display: flex;
						justify-content: center;
						gap: 20px;
						transition: bottom 0.3s ease-in-out;
						box-sizing: border-box;
				}

				/* Active state class */
				#vb-sticky-actions.is-visible {
						bottom: 0;
				}

				/* Button Styling */
				.vb-action-btn {
						font-weight: 600;
						padding: 10px 24px;
						border-radius: 4px;
						cursor: pointer;
						font-size: 14px;
						border: none;
						transition: all 0.2s ease;
						text-transform: uppercase;
						letter-spacing: 0.5px;
				}

				.vb-btn-update {
						background-color: var(--blue-light) !important;
						color: white;
				}
				.vb-btn-update:hover {
						background-color: var(--blue-light) !important;
				}
				.vb-btn-update:disabled {
						background-color: #a7aaad;
						cursor: not-allowed;
				}

				.vb-btn-preview {
						background-color: #f0f0f1;
						color: #2c3338;
						border: 1px solid #c3c4c7;
				}
				.vb-btn-preview:hover {
						background-color: #ffffff;
						border-color: var(--blue-light) !important;
						color: var(--blue-light) !important;
				}

				/* Responsive adjustments for Admin Sidebar */
				@media (min-width: 783px) {
						#vb-sticky-actions {
								left: 160px;
								width: calc(100% - 160px);
						}
				}
				body.folded #vb-sticky-actions {
						left: 36px;
						width: calc(100% - 36px);
				}
		</style>

		<div id="vb-sticky-actions">
				<button id="vb-btn-update" class="vb-action-btn vb-btn-update">Update</button>
				<button id="vb-btn-preview" class="vb-action-btn vb-btn-preview">Preview Changes</button>
		</div>

		<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", function() {
						const stickyBar = document.getElementById('vb-sticky-actions');
						const btnUpdate = document.getElementById('vb-btn-update');
						const btnPreview = document.getElementById('vb-btn-preview');

						// 1. SCROLL LOGIC
						// ------------------------------------------------
						window.addEventListener('scroll', function() {
								const scrollPosition = window.scrollY || document.documentElement.scrollTop;
								if (scrollPosition > 200) {
										stickyBar.classList.add('is-visible');
								} else {
										stickyBar.classList.remove('is-visible');
								}
						});

						// 2. BUTTON ACTIONS (Priority Check: Classic DOM -> Gutenberg API)
						// ------------------------------------------------

						// UPDATE ACTION
						btnUpdate.addEventListener('click', function(e) {
								e.preventDefault();

								// STEP 1: Check for Classic Editor elements strictly first.
								// The snippet you provided shows id="publish", so we look for that.
								const classicPublishBtn = document.getElementById('publish') || document.getElementById('save-post');

								if (classicPublishBtn) {
										// --- CLASSIC EDITOR LOGIC ---
										btnUpdate.innerText = 'Reloading...';
										// Trigger native click on the actual hidden/top button
										classicPublishBtn.click();
										return; // Exit function to avoid running Gutenberg code
								}

								// STEP 2: Only if Classic button is missing, try Gutenberg.
								// We add a safety check: verifying if 'core/editor' store actually exists.
								if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
										const editorDispatch = wp.data.dispatch('core/editor');

										if (editorDispatch) {
												// --- GUTENBERG LOGIC ---
												const originalText = btnUpdate.innerText;
												btnUpdate.innerText = 'Updating...';
												btnUpdate.disabled = true;

												editorDispatch.savePost().then(() => {
														btnUpdate.innerText = 'Saved!';
														setTimeout(() => {
																btnUpdate.innerText = originalText;
																btnUpdate.disabled = false;
														}, 2000);
												}).catch(() => {
														btnUpdate.innerText = 'Error';
														btnUpdate.disabled = false;
												});
												return;
										}
								}

								// Fallback
								alert('Update button not found.');
						});

						// PREVIEW ACTION
						btnPreview.addEventListener('click', function(e) {
								e.preventDefault();

								// STEP 1: Check for Classic Preview Button
								const classicPreviewBtn = document.getElementById('post-preview');

								if (classicPreviewBtn) {
										// --- CLASSIC EDITOR LOGIC ---
										const originalTarget = classicPreviewBtn.target;
										classicPreviewBtn.target = '_blank';

										// Prefer href if available for cleaner new tab opening
										if (classicPreviewBtn.href && classicPreviewBtn.href !== '#') {
												window.open(classicPreviewBtn.href, '_blank');
										} else {
												classicPreviewBtn.click();
										}

										classicPreviewBtn.target = originalTarget;
										return;
								}

								// STEP 2: Try Gutenberg Preview
								if (typeof wp !== 'undefined' && wp.data && wp.data.select) {
										const editorSelect = wp.data.select('core/editor');
										if (editorSelect) {
												 const previewLink = editorSelect.getEditedPostPreviewLink();
												 if (previewLink) {
														window.open(previewLink, '_blank');
														return;
												 }
										}
								}

								alert('Preview button not found.');
						});
				});
		</script>
		<?php
}
add_action('admin_footer', 'vb_add_sticky_bottom_actions');





add_action('wp_footer', function() {
		global $post;

		// Configurazione IDs
		$current_page_id = $post ? $post->ID : get_the_ID();
		$template_id = 16198;

		// Variabili di stato
		$target_column = false;
		$source_found = 'none';
		$debug_rows = [];

		// Funzione helper per analizzare un repeater
		$check_repeater = function($post_id, $context_name) use (&$debug_rows) {
				$rows = get_field('template', $post_id);

				if (!$rows) return false;

				// --- NORMALIZZAZIONE DATI ---
				if (isset($rows['scelta_template']) || isset($rows['column_number'])) {
						$rows = array($rows);
				}

				foreach ($rows as $i => $row) {
						$scelta = isset($row['scelta_template']) ? $row['scelta_template'] : 'missing';
						$colonne = isset($row['column_number']) ? $row['column_number'] : 'missing';

						$debug_rows[] = "[$context_name Row $i] Scelta: " . json_encode($scelta) . " | Cols: " . $colonne;

						if ($scelta == 1) {
								if (!empty($colonne)) {
										return intval($colonne);
								}
						}
				}
				return false;
		};

		// 1. PRIMO TENTATIVO: Pagina Corrente
		$target_column = $check_repeater($current_page_id, 'Page ' . $current_page_id);
		if ($target_column) {
				$source_found = 'Page (' . $current_page_id . ')';
		}
		// 2. SECONDO TENTATIVO: Template
		else {
				$target_column = $check_repeater($template_id, 'Template ' . $template_id);
				if ($target_column) {
						$source_found = 'Template (' . $template_id . ')';
				}
		}

		// DEBUG INITIAL
		?>
		<script>

		</script>
		<?php

		if ($target_column) {
				?>
				<script>
				(function($) {
						'use strict';

						const targetColumns = '<?php echo $target_column; ?>'; // Valore numerico per lo style
						const targetClass =  'column-' + targetColumns ;       // Nome classe per CSS
						const selector = '.elementor-widget-gallery';

						function applyClass($element) {
								const el = $element instanceof jQuery ? $element[0] : $element;
								if (!el) return;

								let applied = false;

								// Helper per forzare lo stile inline
								const setColumns = (node) => {
										if (node) {
												// Sovrascrive la variabile CSS inline di Elementor
												node.style.setProperty('--columns', targetColumns);
												if (!node.classList.contains(targetClass)) {
														node.classList.add(targetClass);
												}
												return true;
										}
										return false;
								};

								// CASE A: Wrapper principale (elementor-widget-gallery)
								if (el.classList && el.classList.contains('elementor-widget-gallery')) {
										// Aggiungi classe al wrapper per sicurezza
										if (!el.classList.contains(targetClass)) el.classList.add(targetClass);

										// Cerca la griglia interna e applica STYLE + CLASSE
										const innerGrid = el.querySelector('.e-gallery-grid');
										if (innerGrid) {
												if (setColumns(innerGrid)) applied = true;
										}
								}
								// CASE B: Fallback griglia diretta (e-gallery-grid)
								else if (el.classList && el.classList.contains('e-gallery-grid')) {
										if (setColumns(el)) applied = true;
								}

								if (applied) console.log('✅ Updated --columns to ' + targetColumns + ' for:', el);
						}

						// Hook Elementor
						$(window).on('elementor/frontend/init', function() {
								elementorFrontend.hooks.addAction('frontend/element_ready/gallery.default', function($scope) {
										applyClass($scope);
								});
						});

						// Observer
						const observer = new MutationObserver((mutations) => {
								let shouldCheck = false;
								for(let mutation of mutations) {
										if (mutation.addedNodes.length > 0) {
												shouldCheck = true;
												break;
										}
								}

								if (shouldCheck) {
										setTimeout(() => {
												const found = document.querySelectorAll(selector);
												if(found.length > 0) found.forEach(node => applyClass(node));
										}, 150);
								}
						});

						observer.observe(document.body, { childList: true, subtree: true });

						// DOM Ready
						$(document).ready(function() {
								document.querySelectorAll(selector).forEach(node => applyClass(node));
						});

				})(jQuery);
				</script>
				<?php
		}
}, 99);






class Content_Audit_Dashboard {

	private static $target_cpts = [ 'post', 'courses', 'seminars', 'archiprix', 'page' ];
	private static $posts_limit = 200;
	private static $dedicated_tax = [ 'project_year', 'unit' ];

	// Palette colori per i cluster (sfondo, bordo) - 12 colori distinti
	private static $cluster_palette = [
		[ '#fff8e1', '#f9a825' ], [ '#e8f5e9', '#2e7d32' ], [ '#e3f2fd', '#1565c0' ],
		[ '#fce4ec', '#c62828' ], [ '#f3e5f5', '#6a1b9a' ], [ '#e0f2f1', '#00695c' ],
		[ '#fff3e0', '#e65100' ], [ '#e8eaf6', '#283593' ], [ '#fafafa', '#616161' ],
		[ '#e1f5fe', '#0277bd' ], [ '#f9fbe7', '#558b2f' ], [ '#fbe9e7', '#bf360c' ],
	];

	public static function init() {
		add_action( 'admin_menu',              [ self::class, 'register_page' ] );
		add_action( 'admin_enqueue_scripts',   [ self::class, 'enqueue_assets' ] );
		add_action( 'wp_ajax_cad_save_slug',    [ self::class, 'ajax_save_slug' ] );
		add_action( 'wp_ajax_cad_save_terms',   [ self::class, 'ajax_save_terms' ] );
		add_action( 'wp_ajax_cad_switch_type',  [ self::class, 'ajax_switch_type' ] );
	}

	public static function register_page() {
		add_menu_page(
			'Audit Content',
			'Audit Content',
			'manage_options',
			'content-audit',
			[ self::class, 'render' ],
			'dashicons-chart-bar',
			3
		);
	}

	// -------------------------------------------------------------------------
	// CLUSTERING: genera una "firma" per ogni post e raggruppa i simili.
	// Firma = titolo normalizzato (senza anno finale) + unit IDs + taxonomy IDs.
	// Due post con firma identica sono "gemelli di serie".
	// -------------------------------------------------------------------------
	// Primo segmento dello slug (prima del primo trattino).
	// "infographics-maps-2021" → "infographics" | "bau-studio-2" → "bau"
	private static function slug_first_segment( string $slug ): string {
		$pos = strpos( $slug, '-' );
		return $pos !== false ? substr( $slug, 0, $pos ) : $slug;
	}

	// Due slug base sono "simili" se condividono lo stesso primo segmento.
	private static function slugs_are_similar( string $a, string $b ): bool {
		if ( $a === $b ) return true;
		return self::slug_first_segment( $a ) === self::slug_first_segment( $b );
	}

	private static function build_clusters( array $posts ) {
		$data = []; // [ post_id => [ slug_base, unit_ids, tax_ids ] ]

		foreach ( $posts as $post ) {
			// Richiede project_year compilato — senza anno non ha senso clusterizzare
			$py = get_the_terms( $post->ID, 'project_year' );
			if ( ! $py || is_wp_error( $py ) || empty( $py ) ) continue;

			// Slug base: rimuove suffisso anno in coda (-2021, -2021-2022, -21-22, ecc.)
			$slug_base = preg_replace( '/-\d{2,4}(-\d{2,4})?$/', '', $post->post_name );
			$slug_base = trim( $slug_base, '-' );

			// Unit IDs
			$unit_ids = [];
			$ut = get_the_terms( $post->ID, 'unit' );
			if ( $ut && ! is_wp_error( $ut ) ) $unit_ids = wp_list_pluck( $ut, 'term_id' );
			sort( $unit_ids );

			// Taxonomy IDs (escluse le dedicated: project_year, unit)
			$tax_ids = [];
			$all_tax = get_object_taxonomies( $post->post_type );
			foreach ( $all_tax as $tax_slug ) {
				if ( in_array( $tax_slug, self::$dedicated_tax, true ) ) continue;
				$t = get_the_terms( $post->ID, $tax_slug );
				if ( $t && ! is_wp_error( $t ) ) {
					foreach ( $t as $term ) $tax_ids[] = $term->term_id;
				}
			}
			sort( $tax_ids );

			$data[ $post->ID ] = [
				'slug_base' => $slug_base,
				'unit_ids'  => $unit_ids,
				'tax_ids'   => $tax_ids,
			];
		}

		// Due post sono compatibili se:
		// 1. Stesso primo segmento dello slug base
		// 2. Stessa unit (identica)
		// 3. Almeno un term in comune nelle tassonomie
		$compatible = function( array $a, array $b ): bool {
			if ( $a['unit_ids'] !== $b['unit_ids'] ) return false;
			if ( ! self::slugs_are_similar( $a['slug_base'], $b['slug_base'] ) ) return false;
			if ( empty( $a['tax_ids'] ) && empty( $b['tax_ids'] ) ) return true; // entrambi senza tax → ok
			return ! empty( array_intersect( $a['tax_ids'], $b['tax_ids'] ) );
		};

		// Union-Find
		$parent = [];
		$find = function( int $x ) use ( &$parent, &$find ): int {
			if ( ! isset( $parent[$x] ) ) $parent[$x] = $x;
			if ( $parent[$x] !== $x ) $parent[$x] = $find( $parent[$x] );
			return $parent[$x];
		};
		$union = function( int $a, int $b ) use ( &$parent, &$find ) {
			$parent[ $find($a) ] = $find($b);
		};

		$ids = array_keys( $data );
		$n   = count( $ids );
		for ( $i = 0; $i < $n; $i++ ) {
			for ( $j = $i + 1; $j < $n; $j++ ) {
				$id_a = $ids[$i];
				$id_b = $ids[$j];
				if ( $compatible( $data[$id_a], $data[$id_b] ) ) {
					$union( $id_a, $id_b );
				}
			}
		}

		// Raggruppa gli ID per radice comune e assegna indice cluster
		$groups = [];
		foreach ( $data as $id => $_ ) {
			$root = $find( $id );
			$groups[ $root ][] = $id;
		}

		$cluster_index = [];
		$idx = 0;
		foreach ( $groups as $root => $ids ) {
			if ( count( $ids ) < 2 ) continue;
			foreach ( $ids as $id ) {
				$cluster_index[ $id ] = $idx;
			}
			$idx++;
		}

		return $cluster_index;
	}

	public static function enqueue_assets( $hook ) {
		if ( 'toplevel_page_content-audit' !== $hook ) return;

		$css = "
			.cad-wrap { max-width: 100%; margin: 20px 0; }

			/* Tabs */
			.cad-tabs-nav { display: flex; gap: 0; border-bottom: 2px solid #2271b1; margin-bottom: 24px; flex-wrap: wrap; }
			.cad-tab-btn {
				padding: 10px 22px; font-size: 14px; font-weight: 600; cursor: pointer;
				background: #f0f0f1; border: 1px solid #c3c4c7; border-bottom: none;
				color: #50575e; margin-right: 3px; margin-bottom: -2px; border-radius: 4px 4px 0 0;
				transition: background 0.15s, color 0.15s;
			}
			.cad-tab-btn:hover { background: #fff; color: #2271b1; }
			.cad-tab-btn.is-active { background: #fff; color: #2271b1; border-bottom: 2px solid #fff; z-index: 1; }
			.cad-tab-count { display: inline-block; background: #2271b1; color: #fff; border-radius: 10px; font-size: 11px; padding: 1px 7px; margin-left: 6px; font-weight: 700; }
			.cad-tab-btn.is-active .cad-tab-count { background: #135e96; }
			.cad-tab-panel { display: none; }
			.cad-tab-panel.is-active { display: block; }

			/* Toolbar */
			.cad-toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
			.cad-sort-group { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #50575e; }
			.cad-sort-select { padding: 5px 10px; border: 1px solid #c3c4c7; border-radius: 3px; font-size: 13px; }

			/* Table */
			.cad-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
			.cad-table th { background: #f6f7f7; padding: 10px 12px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #50575e; border-bottom: 2px solid #e0e0e0; white-space: nowrap; }
			.cad-table td { padding: 9px 12px; border-bottom: 1px solid #f0f0f1; font-size: 13px; vertical-align: top; }
			.cad-table tbody tr:hover td { background: rgba(0,0,0,.02); }
			.cad-table .col-id { width: 45px; color: #999; font-size: 12px; }
			.cad-table .col-cluster { width: 56px; text-align: center; }
			.cad-table .col-title { width: 17%; }
			.cad-table .col-slug { width: 15%; }
			.cad-table .col-tax { width: 13%; }
			.cad-table .col-year { width: 10%; }
			.cad-table .col-unit { width: 11%; }
			.cad-table .col-date { width: 80px; white-space: nowrap; color: #666; font-size: 12px; }
			.cad-table .col-view { width: 32px; text-align: center; }
			.cad-view-link { font-size: 15px; color: #1a73e8; text-decoration: none; line-height: 1; }
			.cad-view-link:hover { color: #0d47a1; }
			.cad-view-draft { color: #ccc; font-size: 12px; }
			.cad-table .col-switch { width: 110px; }
			.cad-switch-select { font-size: 11px; padding: 2px 4px; border: 1px solid #ddd; border-radius: 4px; color: #555; background: #fafafa; cursor: pointer; max-width: 100%; }
			.cad-switch-select:hover { border-color: #2271b1; }
			.cad-switch-feedback { font-size: 11px; margin-left: 4px; }
			.cad-switch-feedback.ok { color: #2e7d32; }
			.cad-switch-feedback.err { color: #c62828; }

			/* Cluster badge (B) */
			.cad-cluster-badge {
				display: inline-flex; align-items: center; justify-content: center;
				width: 28px; height: 28px; border-radius: 50%;
				font-size: 11px; font-weight: 700; cursor: pointer;
				border: 2px solid transparent; transition: transform 0.1s;
			}
			.cad-cluster-badge:hover { transform: scale(1.15); }
			.cad-cluster-badge.is-active { outline: 3px solid #2271b1; outline-offset: 2px; }
			tr.cad-cluster-row td { border-left: 4px solid transparent; }
			tr.cad-cluster-row.is-highlighted td { border-left-width: 4px; }
			tr.cad-cluster-row.is-dimmed { opacity: 0.3; }

			/* Editable cell (slug + tax) */
			.cad-editable-cell { position: relative; }
			.cad-display { cursor: pointer; }
			.cad-slug-display { font-family: monospace; font-size: 12px; color: #2271b1; border-bottom: 1px dashed #2271b1; word-break: break-all; }
			.cad-slug-display:hover { color: #135e96; }
			.cad-inline-form { display: none; }
			.cad-inline-form input[type=text] { width: 100%; box-sizing: border-box; padding: 4px 6px; font-size: 12px; font-family: monospace; border: 1px solid #2271b1; border-radius: 3px; }
			.cad-inline-actions { margin-top: 4px; display: flex; gap: 5px; align-items: center; }
			.cad-btn-save, .cad-btn-cancel { font-size: 11px; padding: 3px 9px; cursor: pointer; border-radius: 3px; border: none; line-height: 1.4; }
			.cad-btn-save { background: #2271b1; color: #fff; }
			.cad-btn-save:hover { background: #135e96; }
			.cad-btn-cancel { background: #f0f0f1; color: #333; border: 1px solid #ccc; }
			.cad-feedback { font-size: 11px; margin-top: 3px; min-height: 14px; }
			.cad-feedback.ok { color: #46b450; }
			.cad-feedback.err { color: #d63638; }

			/* Tax editable */
			.cad-tax-display { cursor: pointer; }
			.cad-tax-display:hover .cad-badge:not(.is-empty) { border-color: #2271b1; }
			.cad-tax-display .cad-edit-hint { font-size: 10px; color: #bbb; display: none; margin-top: 2px; }
			.cad-tax-display:hover .cad-edit-hint { display: block; }
			.cad-tax-select { display: none; }
			.cad-tax-select select { width: 100%; min-width: 120px; font-size: 12px; border: 1px solid #2271b1; border-radius: 3px; padding: 3px; }

			/* Badges */
			.cad-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #f0f0f1; color: #3c434a; font-size: 11px; margin: 2px 2px 2px 0; border: 1px solid #dcdcde; }
			.cad-badge.is-cat { background: #e5f5fa; color: #005a87; border-color: #bce3eb; }
			.cad-badge.is-empty { color: #d63638; font-style: italic; background: none; border: none; padding-left: 0; }

			/* Families tab (C) */
			.cad-family-block { border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 24px; overflow: hidden; }
			.cad-family-header {
				padding: 10px 16px; display: flex; align-items: center; gap: 10px;
				font-weight: 600; font-size: 13px;
			}
			.cad-family-dot { width: 14px; height: 14px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
			.cad-family-meta { font-size: 12px; color: #666; font-weight: 400; margin-left: auto; }
			.cad-family-table { width: 100%; border-collapse: collapse; }
			.cad-family-table th { background: #fafafa; padding: 8px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: #888; border-bottom: 1px solid #eee; }
			.cad-family-table td { padding: 8px 12px; border-bottom: 1px solid #f5f5f5; font-size: 13px; vertical-align: top; }
			.cad-family-table tbody tr:last-child td { border-bottom: none; }
			.cad-family-table .col-year { width: 12%; }
			.cad-family-table .col-slug { width: 22%; }
			.cad-family-table .col-title { width: 28%; }
			.cad-family-table .col-tax { width: 20%; }
			.cad-family-table .col-date { width: 80px; white-space: nowrap; color: #999; font-size: 12px; }
		";

		wp_register_style( 'cad-admin', false, [], null );
		wp_enqueue_style( 'cad-admin' );
		wp_add_inline_style( 'cad-admin', $css );

		$js = <<<'JS'
		(function() {
			'use strict';

			// TABS
			document.querySelectorAll('.cad-tab-btn').forEach(function(btn) {
				btn.addEventListener('click', function() {
					var t = this.dataset.tab;
					document.querySelectorAll('.cad-tab-btn').forEach(function(b){ b.classList.remove('is-active'); });
					document.querySelectorAll('.cad-tab-panel').forEach(function(p){ p.classList.remove('is-active'); });
					this.classList.add('is-active');
					var panel = document.getElementById('cad-panel-' + t);
					if (panel) panel.classList.add('is-active');
				}.bind(btn));
			});

			// SORT (frontend only)
			document.querySelectorAll('.cad-sort-select').forEach(function(sel) {
				sel.addEventListener('change', function() {
					var tbody = this.closest('.cad-tab-panel').querySelector('tbody');
					if (!tbody) return;
					var rows = Array.from(tbody.querySelectorAll('tr'));
					var v = this.value;
					rows.sort(function(a, b) {
						if (v === 'title-asc' || v === 'title-desc') {
							return v === 'title-asc'
								? (a.dataset.title||'').localeCompare(b.dataset.title||'')
								: (b.dataset.title||'').localeCompare(a.dataset.title||'');
						}
						if (v === 'date-asc' || v === 'date-desc') {
							return v === 'date-asc'
								? parseInt(a.dataset.date||0) - parseInt(b.dataset.date||0)
								: parseInt(b.dataset.date||0) - parseInt(a.dataset.date||0);
						}
						return 0;
					});
					rows.forEach(function(r){ tbody.appendChild(r); });
				});
			});

			// CLUSTER HIGHLIGHT (B): click sul badge evidenzia/filtra il gruppo
			var activeCluster = null;
			document.addEventListener('click', function(e) {
				var badge = e.target.closest('.cad-cluster-badge');
				if (!badge) return;
				var panel = badge.closest('.cad-tab-panel');
				var cid   = badge.dataset.cluster;

				if (activeCluster === cid) {
					// Secondo click: reset
					activeCluster = null;
					panel.querySelectorAll('.cad-cluster-row').forEach(function(r) {
						r.classList.remove('is-highlighted', 'is-dimmed');
					});
					panel.querySelectorAll('.cad-cluster-badge').forEach(function(b) {
						b.classList.remove('is-active');
					});
				} else {
					activeCluster = cid;
					panel.querySelectorAll('.cad-cluster-row').forEach(function(r) {
						if (r.dataset.cluster === cid) {
							r.classList.add('is-highlighted');
							r.classList.remove('is-dimmed');
						} else if (r.dataset.cluster) {
							r.classList.remove('is-highlighted');
							r.classList.add('is-dimmed');
						}
					});
					panel.querySelectorAll('.cad-cluster-badge').forEach(function(b) {
						b.classList.toggle('is-active', b.dataset.cluster === cid);
					});
				}
			});

			// AJAX helper
			function cadAjax(data, callback) {
				var fd = new FormData();
				Object.keys(data).forEach(function(k){ fd.append(k, data[k]); });
				fetch(cadData.ajaxUrl, { method: 'POST', body: fd })
					.then(function(r){ return r.json(); })
					.then(callback)
					.catch(function(){ callback({ success: false, data: 'Network error.' }); });
			}

			// SLUG: open
			document.addEventListener('click', function(e) {
				var display = e.target.closest('.cad-slug-display');
				if (!display) return;
				var cell = display.closest('.cad-editable-cell');
				display.style.display = 'none';
				cell.querySelector('.cad-inline-form').style.display = 'block';
				cell.querySelector('input[type=text]').focus();
			});
			// SLUG: cancel
			document.addEventListener('click', function(e) {
				var cancel = e.target.closest('.cad-btn-cancel[data-type=slug]');
				if (!cancel) return;
				var cell = cancel.closest('.cad-editable-cell');
				cell.querySelector('.cad-inline-form').style.display = 'none';
				cell.querySelector('.cad-slug-display').style.display = '';
				cell.querySelector('.cad-feedback').textContent = '';
			});
			// SLUG: save
			document.addEventListener('click', function(e) {
				var save = e.target.closest('.cad-btn-save[data-type=slug]');
				if (!save) return;
				var cell   = save.closest('.cad-editable-cell');
				var postId = cell.dataset.postId;
				var input  = cell.querySelector('input[type=text]');
				var fb     = cell.querySelector('.cad-feedback');
				var slug   = input.value.trim().toLowerCase()
					.replace(/[^a-z0-9\-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
				if (!slug) { fb.textContent = 'Invalid slug.'; fb.className = 'cad-feedback err'; return; }
				save.disabled = input.disabled = true;
				fb.textContent = 'Saving…'; fb.className = 'cad-feedback';
				cadAjax({ action: 'cad_save_slug', post_id: postId, slug: slug, nonce: cadData.nonce }, function(res) {
					save.disabled = input.disabled = false;
					if (res.success) {
						fb.textContent = '✓ Saved'; fb.className = 'cad-feedback ok';
						input.value = res.data.slug;
						// Update all slug displays for this post (table + families tab)
						document.querySelectorAll('.cad-editable-cell[data-post-id="'+postId+'"] .cad-slug-display').forEach(function(d){ d.textContent = res.data.slug; });
						document.querySelectorAll('.cad-editable-cell[data-post-id="'+postId+'"] input[type=text]').forEach(function(i){ i.value = res.data.slug; });
						setTimeout(function() {
							cell.querySelector('.cad-inline-form').style.display = 'none';
							cell.querySelector('.cad-slug-display').style.display = '';
							fb.textContent = '';
						}, 1200);
					} else {
						fb.textContent = res.data || 'Error.'; fb.className = 'cad-feedback err';
					}
				});
			});

			// TAX: open
			document.addEventListener('click', function(e) {
				var display = e.target.closest('.cad-tax-display');
				if (!display) return;
				var cell = display.closest('.cad-editable-cell');
				display.style.display = 'none';
				cell.querySelector('.cad-tax-select').style.display = 'block';
			});
			// TAX: cancel
			document.addEventListener('click', function(e) {
				var cancel = e.target.closest('.cad-btn-cancel[data-type=tax]');
				if (!cancel) return;
				var cell = cancel.closest('.cad-editable-cell');
				cell.querySelector('.cad-tax-select').style.display = 'none';
				cell.querySelector('.cad-tax-display').style.display = '';
				cell.querySelector('.cad-feedback').textContent = '';
			});
			// TAX: save
			document.addEventListener('click', function(e) {
				var save = e.target.closest('.cad-btn-save[data-type=tax]');
				if (!save) return;
				var cell     = save.closest('.cad-editable-cell');
				var postId   = cell.dataset.postId;
				var taxonomy = cell.dataset.taxonomy;
				var select   = cell.querySelector('select');
				var fb       = cell.querySelector('.cad-feedback');
				var selected = Array.from(select.selectedOptions).map(function(o){ return o.value; });
				save.disabled = true;
				fb.textContent = 'Saving…'; fb.className = 'cad-feedback';
				cadAjax({ action: 'cad_save_terms', post_id: postId, taxonomy: taxonomy, terms: JSON.stringify(selected), nonce: cadData.nonce }, function(res) {
					save.disabled = false;
					if (res.success) {
						fb.textContent = '✓ Saved'; fb.className = 'cad-feedback ok';
						var badgesHtml = '';
						if (res.data.terms.length === 0) {
							badgesHtml = '<span class="cad-badge is-empty">— unassigned</span>';
						} else {
							res.data.terms.forEach(function(t) {
								badgesHtml += '<span class="cad-badge'+(t.is_cat?' is-cat':'')+'">'+t.name+'</span>';
							});
						}
						// Update all matching cells (table row + families row)
						document.querySelectorAll('.cad-editable-cell[data-post-id="'+postId+'"][data-taxonomy="'+taxonomy+'"] .cad-tax-display').forEach(function(d) {
							d.innerHTML = badgesHtml + '<span class="cad-edit-hint">✎ edit</span>';
						});
						setTimeout(function() {
							cell.querySelector('.cad-tax-select').style.display = 'none';
							cell.querySelector('.cad-tax-display').style.display = '';
							fb.textContent = '';
						}, 1000);
					} else {
						fb.textContent = res.data || 'Error.'; fb.className = 'cad-feedback err';
					}
				});
			});
		})();

		// SWITCH POST TYPE
		(function() {
			document.addEventListener('change', function(e) {
				var sel = e.target.closest('.cad-switch-select');
				if (!sel) return;
				var target = sel.value;
				if (!target) return;
				var postId   = sel.dataset.postId;
				var feedback = sel.parentNode.querySelector('.cad-switch-feedback');
				var row      = sel.closest('tr');
				sel.disabled = true;
				if (feedback) { feedback.textContent = '…'; feedback.className = 'cad-switch-feedback'; }
				cadAjax({ action: 'cad_switch_type', post_id: postId, target_type: target, nonce: cadData.nonce }, function(res) {
					sel.disabled = false;
					if (res.success) {
						if (feedback) { feedback.textContent = '✓'; feedback.className = 'cad-switch-feedback ok'; }
						// Rimuovi la riga — il post ora appartiene a un altro tab
						setTimeout(function() {
							if (row) row.style.opacity = '0.3';
							setTimeout(function() { if (row) row.remove(); }, 600);
						}, 800);
					} else {
						sel.value = '';
						if (feedback) { feedback.textContent = res.data || 'Error'; feedback.className = 'cad-switch-feedback err'; }
					}
				});
			});
		})();
JS;

		// Registra uno script placeholder con dipendenza jQuery, poi inietta inline
		wp_register_script( 'cad-admin', false, [ 'jquery' ], null, true );
		wp_enqueue_script( 'cad-admin' );
		wp_localize_script( 'cad-admin', 'cadData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'cad_actions' ),
		]);
		// Wrappa in DOMContentLoaded per garantire che il DOM sia pronto
		$js_wrapped = 'document.addEventListener("DOMContentLoaded", function() {' . "\n" . $js . "\n" . '});';
		wp_add_inline_script( 'cad-admin', $js_wrapped );
	}

	// AJAX: save slug
	public static function ajax_save_slug() {
		check_ajax_referer( 'cad_actions', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Insufficient permissions.' );
		$post_id = absint( $_POST['post_id'] ?? 0 );
		$slug    = sanitize_title( $_POST['slug'] ?? '' );
		if ( ! $post_id || ! $slug ) wp_send_json_error( 'Missing data.' );
		$post = get_post( $post_id );
		if ( ! $post ) wp_send_json_error( 'Post not found.' );
		$unique = wp_unique_post_slug( $slug, $post_id, $post->post_status, $post->post_type, $post->post_parent );
		wp_update_post( [ 'ID' => $post_id, 'post_name' => $unique ] );
		wp_send_json_success( [ 'slug' => $unique ] );
	}

	// AJAX: switch post type mantenendo lo status originale
	public static function ajax_switch_type() {
		check_ajax_referer( 'cad_actions', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Insufficient permissions.' );
		$post_id = absint( $_POST['post_id'] ?? 0 );
		$target  = sanitize_key( $_POST['target_type'] ?? '' );
		if ( ! $post_id || ! $target ) wp_send_json_error( 'Missing data.' );
		if ( ! in_array( $target, cd_switch_allowed_types(), true ) ) wp_send_json_error( 'Invalid post type.' );
		$post = get_post( $post_id );
		if ( ! $post ) wp_send_json_error( 'Post not found.' );
		if ( $post->post_type === $target ) wp_send_json_error( 'Already this type.' );
		// Mantieni lo status originale — non forza draft come fa il metabox
		wp_update_post( [
			'ID'          => $post_id,
			'post_type'   => $target,
			'post_status' => $post->post_status,
		] );
		wp_send_json_success( [ 'new_type' => $target ] );
	}

	// AJAX: save taxonomy terms
	public static function ajax_save_terms() {
		check_ajax_referer( 'cad_actions', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Insufficient permissions.' );
		$post_id  = absint( $_POST['post_id'] ?? 0 );
		$taxonomy = sanitize_key( $_POST['taxonomy'] ?? '' );
		$raw      = $_POST['terms'] ?? '[]';
		$term_ids = json_decode( stripslashes( $raw ), true );
		if ( ! $post_id || ! $taxonomy ) wp_send_json_error( 'Missing data.' );
		if ( ! taxonomy_exists( $taxonomy ) ) wp_send_json_error( 'Invalid taxonomy.' );
		$term_ids = array_map( 'intval', (array) $term_ids );
		$result   = wp_set_object_terms( $post_id, $term_ids, $taxonomy );
		if ( is_wp_error( $result ) ) wp_send_json_error( $result->get_error_message() );
		$updated = get_the_terms( $post_id, $taxonomy );
		$out = [];
		if ( $updated && ! is_wp_error( $updated ) ) {
			foreach ( $updated as $t ) {
				$out[] = [ 'id' => $t->term_id, 'name' => $t->name, 'is_cat' => ( $taxonomy === 'category' ) ];
			}
		}
		wp_send_json_success( [ 'terms' => $out ] );
	}

	public static function render() {
		$all_tabs   = array_merge( self::$target_cpts, [ 'families' ] );
		$active_tab = isset( $_GET['cad_tab'] ) ? sanitize_key( $_GET['cad_tab'] ) : self::$target_cpts[0];
		if ( ! in_array( $active_tab, $all_tabs, true ) ) $active_tab = self::$target_cpts[0];
		?>
		<div class="wrap cad-wrap">
			<h1 style="margin-bottom:20px;">Content Audit</h1>

			<div class="cad-tabs-nav">
				<?php foreach ( self::$target_cpts as $pt ) :
					$obj = get_post_type_object( $pt );
					if ( ! $obj ) continue;
					$count = wp_count_posts( $pt );
					$n     = isset( $count->publish ) ? (int) $count->publish : 0;
				?>
				<button class="cad-tab-btn <?php echo $pt === $active_tab ? 'is-active' : ''; ?>" data-tab="<?php echo esc_attr( $pt ); ?>">
					<?php echo esc_html( $obj->labels->name ); ?>
					<span class="cad-tab-count"><?php echo $n; ?></span>
				</button>
				<?php endforeach; ?>
				<button class="cad-tab-btn <?php echo 'families' === $active_tab ? 'is-active' : ''; ?>" data-tab="families" style="margin-left:auto; border-color:#9c27b0; color:<?php echo 'families' === $active_tab ? '#6a1b9a' : '#7b1fa2'; ?>;">
					🔗 Families
				</button>
			</div>

			<?php foreach ( self::$target_cpts as $pt ) :
				echo '<div id="cad-panel-' . esc_attr( $pt ) . '" class="cad-tab-panel ' . ( $pt === $active_tab ? 'is-active' : '' ) . '">';
				self::render_panel( $pt );
				echo '</div>';
			endforeach; ?>

			<div id="cad-panel-families" class="cad-tab-panel <?php echo 'families' === $active_tab ? 'is-active' : ''; ?>">
				<?php self::render_families(); ?>
			</div>
		</div>
		<?php
	}

	private static function render_panel( $post_type ) {
		$pt_obj = get_post_type_object( $post_type );
		if ( ! $pt_obj ) return;

		$posts = get_posts([
			'post_type'      => $post_type,
			'posts_per_page' => self::$posts_limit,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		]);

		$all_taxonomies   = get_object_taxonomies( $post_type, 'objects' );
		$all_terms_by_tax = [];
		foreach ( $all_taxonomies as $tax_slug => $tax_obj ) {
			$terms = get_terms( [ 'taxonomy' => $tax_slug, 'hide_empty' => false ] );
			$all_terms_by_tax[ $tax_slug ] = ( $terms && ! is_wp_error( $terms ) ) ? $terms : [];
		}

		// Build cluster map for this panel
		$cluster_map = self::build_clusters( $posts );
		$palette     = self::$cluster_palette;

		$manage_url = $post_type === 'post' ? admin_url( 'edit.php' ) : admin_url( 'edit.php?post_type=' . $post_type );
		?>
		<div class="cad-toolbar">
			<div class="cad-sort-group">
				<label>Sort by:</label>
				<select class="cad-sort-select">
					<option value="title-asc">Title A→Z</option>
					<option value="title-desc">Title Z→A</option>
					<option value="date-desc">Newest first</option>
					<option value="date-asc">Oldest first</option>
				</select>
			</div>
			<a href="<?php echo esc_url( $manage_url ); ?>" class="button button-small">
				Manage <?php echo esc_html( $pt_obj->labels->name ); ?>
			</a>
		</div>

		<?php if ( empty( $posts ) ) : ?>
			<p style="color:#666; padding:10px 0;">No published content found.</p>
		<?php return; endif; ?>

		<table class="cad-table">
			<thead>
				<tr>
					<th class="col-id">ID</th>
					<th class="col-cluster" title="Similar content cluster — click to highlight group">Family</th>
					<th class="col-title">Title</th>
					<th class="col-slug">Slug</th>
					<th class="col-tax">Taxonomy</th>
					<th class="col-year">Project Year</th>
					<th class="col-unit">Unit</th>
					<th class="col-date">Date</th>
				<th class="col-view"></th>
				<th class="col-switch">Type</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $posts as $post ) :
				$ts          = strtotime( $post->post_date );
				$cluster_idx = $cluster_map[ $post->ID ] ?? null;
				$has_cluster = $cluster_idx !== null;
				$color       = $has_cluster ? ( $palette[ $cluster_idx % count( $palette ) ] ) : null;

				$tax_col_terms = [];
				$year_col_ids  = [];
				$unit_col_ids  = [];

				foreach ( $all_taxonomies as $tax_slug => $tax_obj ) {
					$current = get_the_terms( $post->ID, $tax_slug );
					$ids     = ( $current && ! is_wp_error( $current ) ) ? wp_list_pluck( $current, 'term_id' ) : [];
					$names   = ( $current && ! is_wp_error( $current ) ) ? wp_list_pluck( $current, 'name' ) : [];
					if ( $tax_slug === 'project_year' )    { $year_col_ids = $ids; }
					elseif ( $tax_slug === 'unit' )         { $unit_col_ids = $ids; }
					elseif ( ! in_array( $tax_slug, self::$dedicated_tax, true ) ) {
						$tax_col_terms[ $tax_slug ] = [ 'ids' => $ids, 'names' => $names, 'is_cat' => ( $tax_slug === 'category' ) ];
					}
				}

				$row_style = $has_cluster ? 'border-left: 4px solid ' . esc_attr( $color[1] ) . ';' : '';
			?>
			<tr class="<?php echo $has_cluster ? 'cad-cluster-row' : ''; ?>"
				data-cluster="<?php echo $has_cluster ? esc_attr( $cluster_idx ) : ''; ?>"
				data-title="<?php echo esc_attr( strtolower( $post->post_title ) ); ?>"
				data-date="<?php echo esc_attr( $ts ); ?>"
				style="<?php echo $row_style; ?>">

				<!-- ID -->
				<td class="col-id"><?php echo esc_html( $post->ID ); ?></td>

				<!-- CLUSTER BADGE (B) -->
				<td class="col-cluster">
					<?php if ( $has_cluster ) :
						$slug_seg = strtok( $post->post_name, '-' ); // primo segmento slug
					?>
						<span class="cad-cluster-badge"
							data-cluster="<?php echo esc_attr( $cluster_idx ); ?>"
							style="background:<?php echo esc_attr( $color[0] ); ?>; border-color:<?php echo esc_attr( $color[1] ); ?>; color:<?php echo esc_attr( $color[1] ); ?>;"
							title="Potential family #<?php echo esc_attr( $cluster_idx + 1 ); ?> — same slug root «<?php echo esc_attr( $slug_seg ); ?>» — click to highlight all members">
							#<?php echo esc_html( $cluster_idx + 1 ); ?>
						</span>
						<span class="cad-cluster-root" style="display:block; font-size:10px; color:<?php echo esc_attr( $color[1] ); ?>; margin-top:2px; font-family:monospace; opacity:0.8;">~<?php echo esc_html( $slug_seg ); ?></span>
					<?php else : ?>
						<span style="color:#e0e0e0; font-size:11px;">—</span>
					<?php endif; ?>
				</td>

				<!-- TITLE -->
				<td class="col-title">
					<a href="<?php echo get_edit_post_link( $post->ID ); ?>" style="font-weight:600;">
						<?php echo esc_html( $post->post_title ); ?>
					</a>
				</td>

				<!-- SLUG (editable) -->
				<td class="col-slug">
					<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
						<span class="cad-slug-display cad-display"><?php echo esc_html( $post->post_name ); ?></span>
						<div class="cad-inline-form">
							<input type="text" value="<?php echo esc_attr( $post->post_name ); ?>" />
							<div class="cad-inline-actions">
								<button class="cad-btn-save" data-type="slug">Save</button>
								<button class="cad-btn-cancel" data-type="slug">✕</button>
							</div>
							<div class="cad-feedback"></div>
						</div>
					</div>
				</td>

				<!-- TAXONOMY (editable) -->
				<td class="col-tax">
					<?php foreach ( $tax_col_terms as $tax_slug => $tdata ) :
						$all_t = $all_terms_by_tax[ $tax_slug ] ?? [];
					?>
					<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $post->ID ); ?>" data-taxonomy="<?php echo esc_attr( $tax_slug ); ?>" style="margin-bottom:4px;">
						<div class="cad-tax-display cad-display">
							<?php if ( empty( $tdata['names'] ) ) : ?>
								<span class="cad-badge is-empty">— unassigned</span>
							<?php else : ?>
								<?php foreach ( $tdata['names'] as $n ) : ?>
									<span class="cad-badge <?php echo $tdata['is_cat'] ? 'is-cat' : ''; ?>"><?php echo esc_html( $n ); ?></span>
								<?php endforeach; ?>
							<?php endif; ?>
							<span class="cad-edit-hint">✎ edit</span>
						</div>
						<div class="cad-tax-select">
							<select multiple size="<?php echo min( 6, max( 3, count( $all_t ) ) ); ?>">
								<?php foreach ( $all_t as $term ) : ?>
									<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $tdata['ids'] ) ? 'selected' : ''; ?>>
										<?php echo esc_html( $term->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="cad-inline-actions" style="margin-top:4px;">
								<button class="cad-btn-save" data-type="tax">Save</button>
								<button class="cad-btn-cancel" data-type="tax">✕</button>
							</div>
							<div class="cad-feedback"></div>
						</div>
					</div>
					<?php endforeach; ?>
					<?php if ( empty( $tax_col_terms ) ) : ?>
						<span style="color:#bbb; font-size:11px;">—</span>
					<?php endif; ?>
				</td>

				<!-- PROJECT YEAR (editabile) -->
				<td class="col-year">
					<?php $all_years = $all_terms_by_tax['project_year'] ?? []; ?>
					<?php if ( ! empty( $all_years ) ) : ?>
					<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $post->ID ); ?>" data-taxonomy="project_year">
						<div class="cad-tax-display cad-display">
							<?php $yterms = get_the_terms( $post->ID, 'project_year' ); ?>
							<?php if ( $yterms && ! is_wp_error( $yterms ) ) : ?>
								<?php foreach ( $yterms as $yt ) : ?>
									<span class="cad-badge"><?php echo esc_html( $yt->name ); ?></span>
								<?php endforeach; ?>
							<?php else : ?>
								<span class="cad-badge is-empty">—</span>
							<?php endif; ?>
							<span class="cad-edit-hint">✎</span>
						</div>
						<div class="cad-tax-select">
							<select multiple size="<?php echo min( 5, max( 3, count( $all_years ) ) ); ?>">
								<?php foreach ( $all_years as $term ) : ?>
									<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $year_col_ids ) ? 'selected' : ''; ?>>
										<?php echo esc_html( $term->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="cad-inline-actions" style="margin-top:4px;">
								<button class="cad-btn-save" data-type="tax">Salva</button>
								<button class="cad-btn-cancel" data-type="tax">✕</button>
							</div>
							<div class="cad-feedback"></div>
						</div>
					</div>
					<?php else : ?>
						<span style="color:#bbb; font-size:11px;">—</span>
					<?php endif; ?>
				</td>

				<!-- UNIT (editabile) -->
				<td class="col-unit">
					<?php $all_units = $all_terms_by_tax['unit'] ?? []; ?>
					<?php if ( ! empty( $all_units ) ) : ?>
					<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $post->ID ); ?>" data-taxonomy="unit">
						<div class="cad-tax-display cad-display">
							<?php $uterms = get_the_terms( $post->ID, 'unit' ); ?>
							<?php if ( $uterms && ! is_wp_error( $uterms ) ) : ?>
								<?php foreach ( $uterms as $ut ) : ?>
									<span class="cad-badge"><?php echo esc_html( $ut->name ); ?></span>
								<?php endforeach; ?>
							<?php else : ?>
								<span class="cad-badge is-empty">—</span>
							<?php endif; ?>
							<span class="cad-edit-hint">✎</span>
						</div>
						<div class="cad-tax-select">
							<select multiple size="<?php echo min( 5, max( 3, count( $all_units ) ) ); ?>">
								<?php foreach ( $all_units as $term ) : ?>
									<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $unit_col_ids ) ? 'selected' : ''; ?>>
										<?php echo esc_html( $term->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="cad-inline-actions" style="margin-top:4px;">
								<button class="cad-btn-save" data-type="tax">Save</button>
								<button class="cad-btn-cancel" data-type="tax">✕</button>
							</div>
							<div class="cad-feedback"></div>
						</div>
					</div>
					<?php else : ?>
						<span style="color:#bbb; font-size:11px;">—</span>
					<?php endif; ?>
				</td>

				<!-- DATE -->
				<td class="col-date"><?php echo get_the_date( 'd/m/Y', $post ); ?></td>

				<!-- VIEW FRONTEND -->
				<td class="col-view">
					<?php if ( get_post_status( $post->ID ) === 'publish' ) : ?>
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" target="_blank" rel="noopener" class="cad-view-link" title="View on site">↗</a>
					<?php else : ?>
						<span class="cad-view-draft" title="<?php echo esc_attr( ucfirst( get_post_status( $post->ID ) ) ); ?>">—</span>
					<?php endif; ?>
				</td>

				<!-- SWITCH POST TYPE -->
				<td class="col-switch">
					<?php $other_types = array_filter( cd_switch_allowed_types(), fn( $t ) => $t !== $post->post_type ); ?>
					<?php if ( ! empty( $other_types ) ) : ?>
					<select class="cad-switch-select" data-post-id="<?php echo esc_attr( $post->ID ); ?>" title="Move to another post type">
						<option value="">⇄</option>
						<?php foreach ( $other_types as $t ) : ?>
							<option value="<?php echo esc_attr( $t ); ?>"><?php echo esc_html( cd_switch_type_label( $t ) ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="cad-switch-feedback"></span>
					<?php endif; ?>
				</td>

			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	// -------------------------------------------------------------------------
	// TAB FAMILIES (C): mostra i cluster come card a sviluppo orizzontale.
	// Ogni "family" = gruppo di post con stessa base-titolo + unit + taxonomy.
	// Dentro ogni card, le righe sono ordinate per project_year ASC.
	// -------------------------------------------------------------------------
	private static function render_families() {
		// Carica tutti i post dei CPT target
		$all_posts = get_posts([
			'post_type'      => self::$target_cpts,
			'posts_per_page' => self::$posts_limit * count( self::$target_cpts ),
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		]);

		if ( empty( $all_posts ) ) {
			echo '<p style="color:#666; padding:10px 0;">No content found.</p>';
			return;
		}

		$cluster_map = self::build_clusters( $all_posts );

		// Raggruppa i post per cluster_idx
		$families = [];
		foreach ( $all_posts as $post ) {
			if ( isset( $cluster_map[ $post->ID ] ) ) {
				$families[ $cluster_map[ $post->ID ] ][] = $post;
			}
		}

		if ( empty( $families ) ) {
			echo '<p style="color:#666; padding:14px 0;">No similar content groups found. Groups appear when 2+ posts share the same base title, unit and taxonomy.</p>';
			return;
		}

		// Ordina le famiglie per nome del primo membro
		ksort( $families );

		$palette         = self::$cluster_palette;
		$all_year_terms  = get_terms( [ 'taxonomy' => 'project_year', 'hide_empty' => false ] );
		$year_terms_map  = [];
		if ( $all_year_terms && ! is_wp_error( $all_year_terms ) ) {
			foreach ( $all_year_terms as $yt ) $year_terms_map[ $yt->term_id ] = $yt->name;
		}
		?>
		<div style="margin-bottom:16px; font-size:13px; color:#666;">
			<?php echo count( $families ); ?> families found across all content types.
			Each group shares the same base title, unit and taxonomy.
		</div>

		<?php foreach ( $families as $cidx => $members ) :
			$color     = $palette[ $cidx % count( $palette ) ];
			$pt_labels = [];
			foreach ( $members as $m ) {
				$pto = get_post_type_object( $m->post_type );
				if ( $pto ) $pt_labels[ $m->post_type ] = $pto->labels->singular_name;
			}
			// Nome famiglia = titolo normalizzato del primo membro
			$family_name = preg_replace( '/\s*\d{4}(-\d{4})?\s*$/', '', $members[0]->post_title );

			// Ordina membri per project_year term name ASC
			usort( $members, function( $a, $b ) {
				$ya = get_the_terms( $a->ID, 'project_year' );
				$yb = get_the_terms( $b->ID, 'project_year' );
				$na = ( $ya && ! is_wp_error( $ya ) ) ? $ya[0]->name : '0000';
				$nb = ( $yb && ! is_wp_error( $yb ) ) ? $yb[0]->name : '0000';
				return strcmp( $na, $nb );
			});

			// Collect all taxonomies shown in this family
			$family_tax_slugs = [];
			foreach ( $members as $m ) {
				$taxes = get_object_taxonomies( $m->post_type );
				foreach ( $taxes as $ts ) {
					if ( ! in_array( $ts, self::$dedicated_tax, true ) ) {
						$family_tax_slugs[ $ts ] = true;
					}
				}
			}
			$family_tax_slugs = array_keys( $family_tax_slugs );
			// Pre-load all available terms for these taxonomies
			$fam_all_terms = [];
			foreach ( $family_tax_slugs as $fts ) {
				$t = get_terms( [ 'taxonomy' => $fts, 'hide_empty' => false ] );
				$fam_all_terms[ $fts ] = ( $t && ! is_wp_error( $t ) ) ? $t : [];
			}
			$all_year_t = get_terms( [ 'taxonomy' => 'project_year', 'hide_empty' => false ] );
			$all_year_t = ( $all_year_t && ! is_wp_error( $all_year_t ) ) ? $all_year_t : [];
		?>
		<div class="cad-family-block" style="border-color: <?php echo esc_attr( $color[1] ); ?>;">
			<div class="cad-family-header" style="background: <?php echo esc_attr( $color[0] ); ?>; border-bottom: 1px solid <?php echo esc_attr( $color[1] ); ?>;">
				<span class="cad-family-dot" style="background: <?php echo esc_attr( $color[1] ); ?>;"></span>
				<span><?php echo esc_html( $family_name ); ?></span>
				<span class="cad-family-meta">
					<?php echo count( $members ); ?> versions
					&nbsp;·&nbsp;
					<?php echo esc_html( implode( ', ', $pt_labels ) ); ?>
				</span>
			</div>
			<table class="cad-family-table">
				<thead>
					<tr>
						<th class="col-year">Year</th>
						<th class="col-title">Title</th>
						<th class="col-slug">Slug</th>
						<?php foreach ( $family_tax_slugs as $fts ) :
							$tax_obj_f = get_taxonomy( $fts );
							$tax_label = $tax_obj_f ? $tax_obj_f->labels->singular_name : $fts;
						?>
						<th class="col-tax"><?php echo esc_html( $tax_label ); ?></th>
						<?php endforeach; ?>
						<th class="col-date">Date</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $members as $member ) :
					$yterms = get_the_terms( $member->ID, 'project_year' );
					$year_ids  = ( $yterms && ! is_wp_error( $yterms ) ) ? wp_list_pluck( $yterms, 'term_id' ) : [];
					$year_name = ( $yterms && ! is_wp_error( $yterms ) ) ? implode( ', ', wp_list_pluck( $yterms, 'name' ) ) : '—';
				?>
				<tr>
					<!-- YEAR (editable) -->
					<td class="col-year">
						<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $member->ID ); ?>" data-taxonomy="project_year">
							<div class="cad-tax-display cad-display">
								<span class="cad-badge" style="background:<?php echo esc_attr( $color[0] ); ?>; border-color:<?php echo esc_attr( $color[1] ); ?>; color:<?php echo esc_attr( $color[1] ); ?>; font-weight:700;">
									<?php echo esc_html( $year_name ); ?>
								</span>
								<span class="cad-edit-hint">✎</span>
							</div>
							<div class="cad-tax-select">
								<select multiple size="<?php echo min( 5, max( 3, count( $all_year_t ) ) ); ?>">
									<?php foreach ( $all_year_t as $yt ) : ?>
										<option value="<?php echo esc_attr( $yt->term_id ); ?>" <?php echo in_array( $yt->term_id, $year_ids ) ? 'selected' : ''; ?>>
											<?php echo esc_html( $yt->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="cad-inline-actions" style="margin-top:4px;">
									<button class="cad-btn-save" data-type="tax">Save</button>
									<button class="cad-btn-cancel" data-type="tax">✕</button>
								</div>
								<div class="cad-feedback"></div>
							</div>
						</div>
					</td>

					<!-- TITLE -->
					<td class="col-title">
						<a href="<?php echo get_edit_post_link( $member->ID ); ?>" style="font-weight:600; font-size:13px;">
							<?php echo esc_html( $member->post_title ); ?>
						</a>
						<div style="font-size:11px; color:#999; margin-top:2px;">
							<?php echo esc_html( get_post_type_object( $member->post_type )->labels->singular_name ?? $member->post_type ); ?>
						</div>
					</td>

					<!-- SLUG (editable) -->
					<td class="col-slug">
						<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $member->ID ); ?>">
							<span class="cad-slug-display cad-display"><?php echo esc_html( $member->post_name ); ?></span>
							<div class="cad-inline-form">
								<input type="text" value="<?php echo esc_attr( $member->post_name ); ?>" />
								<div class="cad-inline-actions">
									<button class="cad-btn-save" data-type="slug">Save</button>
									<button class="cad-btn-cancel" data-type="slug">✕</button>
								</div>
								<div class="cad-feedback"></div>
							</div>
						</div>
					</td>

					<!-- TAXONOMY COLUMNS (editable) -->
					<?php foreach ( $family_tax_slugs as $fts ) :
						$current_t = get_the_terms( $member->ID, $fts );
						$curr_ids  = ( $current_t && ! is_wp_error( $current_t ) ) ? wp_list_pluck( $current_t, 'term_id' ) : [];
						$curr_names= ( $current_t && ! is_wp_error( $current_t ) ) ? wp_list_pluck( $current_t, 'name' ) : [];
						$is_cat    = ( $fts === 'category' );
						$avail_t   = $fam_all_terms[ $fts ] ?? [];
					?>
					<td class="col-tax">
						<div class="cad-editable-cell" data-post-id="<?php echo esc_attr( $member->ID ); ?>" data-taxonomy="<?php echo esc_attr( $fts ); ?>">
							<div class="cad-tax-display cad-display">
								<?php if ( empty( $curr_names ) ) : ?>
									<span class="cad-badge is-empty">— unassigned</span>
								<?php else : ?>
									<?php foreach ( $curr_names as $cn ) : ?>
										<span class="cad-badge <?php echo $is_cat ? 'is-cat' : ''; ?>"><?php echo esc_html( $cn ); ?></span>
									<?php endforeach; ?>
								<?php endif; ?>
								<span class="cad-edit-hint">✎ edit</span>
							</div>
							<div class="cad-tax-select">
								<select multiple size="<?php echo min( 5, max( 3, count( $avail_t ) ) ); ?>">
									<?php foreach ( $avail_t as $at ) : ?>
										<option value="<?php echo esc_attr( $at->term_id ); ?>" <?php echo in_array( $at->term_id, $curr_ids ) ? 'selected' : ''; ?>>
											<?php echo esc_html( $at->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="cad-inline-actions" style="margin-top:4px;">
									<button class="cad-btn-save" data-type="tax">Save</button>
									<button class="cad-btn-cancel" data-type="tax">✕</button>
								</div>
								<div class="cad-feedback"></div>
							</div>
						</div>
					</td>
					<?php endforeach; ?>

					<!-- DATE -->
					<td class="col-date"><?php echo get_the_date( 'd/m/Y', $member ); ?></td>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endforeach; ?>
		<?php
	}
}

// Avvio della classe
Content_Audit_Dashboard::init();




class Acf_Group_Taxonomy_Sync {

		private const FIELD_KEY = 'field_68347c1cce0bb';

		private $tax_map = [
				'post'      => 'category',
				'courses'   => 'course_category',
				'seminars'  => 'seminar_category',  // [2026-03-18 - Added seminars]
				'archiprix' => 'archiprix_category', // [2026-03-18 - Added archiprix CPT]
		];

		// Snapshot pre-update
		private static $original_db_state = [];

		private static $winner_data = [];

		private static $is_syncing = false;

		public function __construct() {
				// 0. CAPTURE STATE
				add_action('pre_post_update', [$this, 'capture_initial_state'], 10, 2);

				// 1. Load Value
				add_filter('acf/load_value/key=' . self::FIELD_KEY, [$this, 'sync_from_wp_to_acf_on_load'], 10, 3);

				// 2. Update Value (ARBITRO)
				add_filter('acf/update_value/key=' . self::FIELD_KEY, [$this, 'arbitrate_data_on_save'], 10, 3);

				// 3. Save Post (ESECUTORE)
				add_action('acf/save_post', [$this, 'finalize_term_sync'], 20);
		}

		private function log($message, $data = null) {
				if (defined('WP_DEBUG') && WP_DEBUG) {
						$entry = '[ACF_SYNC_V3] ' . $message;
						if ($data) $entry .= ' | DATA: ' . print_r($data, true);
						error_log($entry);
				}
		}

		public function capture_initial_state($post_id, $data) {
				if (wp_is_post_revision($post_id)) return;
				$post_type = isset($data['post_type']) ? $data['post_type'] : get_post_type($post_id);

				if (!isset($this->tax_map[$post_type])) return;

				$taxonomy = $this->tax_map[$post_type];
				$terms = get_the_terms($post_id, $taxonomy);
				$ids = (!empty($terms) && !is_wp_error($terms)) ? wp_list_pluck($terms, 'term_id') : [];
				$ids = array_map('intval', $ids);
				sort($ids);

				self::$original_db_state[$post_id] = $ids;
		}

		public function sync_from_wp_to_acf_on_load($value, $post_id, $field) {
				if (!is_numeric($post_id)) return $value;
				$post_type = get_post_type($post_id);
				if (!isset($this->tax_map[$post_type])) return $value;

				$taxonomy = $this->tax_map[$post_type];
				$terms = get_the_terms($post_id, $taxonomy);

				if (is_wp_error($terms) || empty($terms)) return [];

				return wp_list_pluck($terms, 'term_id');
		}

		public function arbitrate_data_on_save($value, $post_id, $field) {
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $value;
				if (wp_is_post_revision($post_id)) return $value;

				$post_type = get_post_type($post_id);
				if (!isset($this->tax_map[$post_type])) return $value;
				$taxonomy = $this->tax_map[$post_type];

				// --- 1. Recupera Input Sidebar ---
				$native_input_ids = $this->get_native_sidebar_input($taxonomy);
				$native_input_ids = array_map('intval', $native_input_ids);
				sort($native_input_ids);

				// --- 2. Recupera Snapshot ---
				if (isset(self::$original_db_state[$post_id])) {
						$original_db_ids = self::$original_db_state[$post_id];
				} else {
						$current_terms = get_the_terms($post_id, $taxonomy);
						$original_db_ids = (!empty($current_terms) && !is_wp_error($current_terms))
								? wp_list_pluck($current_terms, 'term_id') : [];
						$original_db_ids = array_map('intval', $original_db_ids);
						sort($original_db_ids);
				}

				// --- 3. Recupera Input ACF ---
				$acf_input_ids = is_array($value) ? array_map('intval', $value) : (empty($value) ? [] : [intval($value)]);
				sort($acf_input_ids);

				$this->log("ARBITRAGGIO START", [
						'Original' => $original_db_ids,
						'Native' => $native_input_ids,
						'ACF' => $acf_input_ids
				]);

				$final_value = $value; // Default: vince ACF/Input attuale

				// A. La Sidebar è diversa dall'originale? -> VINCE SIDEBAR
				if ($native_input_ids !== $original_db_ids) {
						$final_value = $native_input_ids;
				}
				// B. Sidebar uguale, ACF diverso? -> VINCE ACF
				elseif ($acf_input_ids !== $original_db_ids) {
						$final_value = $value;
				}
				self::$winner_data[$post_id] = $final_value;

				return $final_value;
		}

		public function finalize_term_sync($post_id) {
				if (self::$is_syncing) return;

				if (!isset(self::$winner_data[$post_id])) {
						return;
				}

				$post_type = get_post_type($post_id);
				if (!isset($this->tax_map[$post_type])) return;

				$winning_ids = self::$winner_data[$post_id];
				$taxonomy = $this->tax_map[$post_type];

				// Normalizzazione
				if (!is_array($winning_ids)) {
						$winning_ids = empty($winning_ids) ? [] : [$winning_ids];
				}
				$winning_ids = array_map('intval', $winning_ids);
				sort($winning_ids);

				// Controllo stato attuale WP
				$current_terms = get_the_terms($post_id, $taxonomy);
				$current_ids = !empty($current_terms) && !is_wp_error($current_terms)
						? wp_list_pluck($current_terms, 'term_id') : [];
				$current_ids = array_map('intval', $current_ids);
				sort($current_ids);

				// Se differiscono, forziamo l'aggiornamento
				if ($winning_ids !== $current_ids) {

						self::$is_syncing = true;
						$res = wp_set_object_terms($post_id, $winning_ids, $taxonomy);

						if (is_wp_error($res)) {

						}

						self::$is_syncing = false;
				} else {

				}

				// Pulizia memoria
				unset(self::$winner_data[$post_id]);
		}

		private function get_native_sidebar_input($taxonomy) {
				$data = [];
				if ($taxonomy === 'category') {
						if (isset($_POST['post_category']) && is_array($_POST['post_category'])) {
								$data = $_POST['post_category'];
								$data = array_filter($data, function($v) { return $v > 0; });
						}
				} elseif (isset($_POST['tax_input'][$taxonomy])) {
						$input = $_POST['tax_input'][$taxonomy];
						if (is_array($input)) {
								$data = array_filter($input, function($v) { return $v > 0; });
						} else {
								 $data = $input;
						}
				}
				return !empty($data) ? array_values($data) : [];
		}
}

// Aggiunge la classe 'blocco-custom' alla inner-section Elementor senza heading nei single post
function cd_inject_blocco_custom_class() {
    if ( ! is_singular() || is_page() ) return;
    ?>
    <script>
    window.addEventListener('load', function() {
        const sezione = document.querySelector('.wrapper .elementor-inner-section:last-child');
        if (sezione) {
            sezione.classList.add('blocco-custom');
        }

        const content = document.getElementById('content');
        if (content) {
            const firstBlocco = content.querySelector('.blocco-custom');
            if (firstBlocco) {
                firstBlocco.classList.add('blocco-custom-first');
            }
        }
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'cd_inject_blocco_custom_class' );

// Inietta CSS per .blocco-custom in tutti i CPT singoli tranne pages
function cd_inject_blocco_custom_css() {
    $cpt_list = array( 'post', 'courses', 'seminars', 'archiprix' );
    if ( ! is_singular( $cpt_list ) ) return;
    ?>
    <style>
    .blocco-custom-first .elementor-column:nth-child(1) {
        width: 350px !important;
        flex-basis: 350px !important;
        flex-grow: 0 !important;
        flex-shrink: 0 !important;
    }
    .blocco-custom-first .elementor-column:nth-child(2) {
        display: none !important;
    }
    </style>
    <?php
}
add_action( 'wp_head', 'cd_inject_blocco_custom_css' );

new Acf_Group_Taxonomy_Sync();

// Shortcode: [cd_study_unit] → "Bachelor | AUDE"
// study_level = termine dal gruppo ACF general_information (array di WP_Term)
// unit = tassonomia WP nativa, mostra lo slug in UPPERCASE
function cd_render_study_unit( $atts ) {
    if ( ! is_singular( [ 'post', 'courses' ] ) ) return '';

    $post_id = get_the_ID();
    $gi      = get_field( 'general_information', $post_id );

    $study_level_name = '';
    if ( ! empty( $gi['study_level'] ) ) {
        $terms = (array) $gi['study_level'];
        $first = reset( $terms );
        if ( $first instanceof WP_Term ) {
            $study_level_name = $first->name;
        }
    }

    $unit_slug = '';
    $unit_terms = get_the_terms( $post_id, 'unit' );
    if ( $unit_terms && ! is_wp_error( $unit_terms ) ) {
        $unit_slug = strtoupper( $unit_terms[0]->slug );
    }

    $parts = array_filter( [ $study_level_name, $unit_slug ] );
    if ( empty( $parts ) ) return '';

    return '<span class="cd-study-unit">' . esc_html( implode( ' | ', $parts ) ) . '</span>';
}
add_shortcode( 'cd_study_unit', 'cd_render_study_unit' );
// Inject unit link into .acf-general-information-wrapper .entry-meta
add_action( 'wp_footer', function() {
    if ( ! is_singular( [ 'post', 'courses' ] ) ) return;

    $post_id    = get_queried_object_id();
    $post_type  = get_post_type( $post_id );

    $gi = get_field( 'general_information', $post_id );
    if ( is_array( $gi ) && ! empty( $gi['disable_unit'] ) ) return;

    $unit_terms = get_the_terms( $post_id, 'unit' );
    if ( ! $unit_terms || is_wp_error( $unit_terms ) ) return;
    $unit_slug  = $unit_terms[0]->slug;
    $unit_label = strtoupper( $unit_slug );

    $type_tax   = ( $post_type === 'courses' ) ? 'course_category' : 'category';
    $type_terms = get_the_terms( $post_id, $type_tax );
    $type_slug  = ( $type_terms && ! is_wp_error( $type_terms ) ) ? $type_terms[0]->slug : '';

    $listing    = ( $post_type === 'courses' ) ? '/courses/' : '/projects/';
    $url        = home_url( $listing );
    if ( $type_slug ) $url = add_query_arg( 'cd_filter', $type_slug, $url );
    $url = add_query_arg( 'cd_unit', $unit_slug, $url );
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var unitHtml = ' | <a class="cd-unit-link" href="<?php echo esc_js( esc_url( $url ) ); ?>"><?php echo esc_js( $unit_label ); ?></a>';
        var meta = document.querySelector('.acf-general-information-wrapper .entry-meta');
        if (meta) {
            if (!meta.querySelector('.cd-unit-link')) {
                meta.insertAdjacentHTML('beforeend', unitHtml);
            }
        } else {
            var headings = document.querySelectorAll('.elementor-heading-title');
            for (var i = 0; i < headings.length; i++) {
                if (headings[i].querySelector('a[href*="cd_filter="]') && !headings[i].querySelector('.cd-unit-link')) {
                    headings[i].insertAdjacentHTML('beforeend', unitHtml);
                    break;
                }
            }
        }
    });
    </script>
    <?php
} );
