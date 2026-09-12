<?php
/**
 * Plugin Name: Custom Dashboard
 * Description: Sostituisce la dashboard di WordPress con una personalizzata e migliora l'editing ACF.
 * Version: 1.3
 * Author: Paolo (con revisioni)
 */
if (!defined("ABSPATH")) {
	exit(); // Exit if accessed directly.
}

// --- PLUGIN CONSTANTS ---
define("CD_PLUGIN_DIR", plugin_dir_path(__FILE__));
define("CD_PLUGIN_URL", plugin_dir_url(__FILE__));
define("CD_TEXTDOMAIN", "custom-dashboard");

// ACF Field Keys & Names Configuration
define("CD_ACF_GROUP_KEY_GENERAL_INFO", "field_6830ba527fd59"); // Key for "General Information" Group
define("CD_ACF_SUBFIELD_NAME_TITLE", "name_required"); // Sub-field name for Title
define("CD_ACF_SUBFIELD_NAME_DESCRIPTION", "description"); // Sub-field name for Description
define("CD_ACF_FIELD_NAME_TEMPLATE_CHOICE", "scelta_template"); // Field name for ACF Radio Template Choice
define("CD_ACF_REPEATER_NAME_TEMPLATE_LAYOUTS", "template"); // Field name for ACF Repeater (for shortcode)

define("CD_ACF_GROUP_KEY", "field_6830ba527fd59"); // Chiave del GRUPPO di campi
define("CD_ACF_TITLE_FIELD_KEY", "field_6830b9ab7fd58");

define("CD_ACF_GROUP_NAME", "general_information"); // Il nome del gruppo di campi
define("CD_ACF_FIELD_KEY_TITLE", "field_6830b9ab7fd58"); // La CHIAVE del campo titolo
define("CD_ACF_FIELD_NAME_TITLE", "name_required");

// Elementor Template IDs Configuration (for shortcode)
define("CD_ELEMENTOR_TEMPLATE_ID_STANDARD", 16198);
define("CD_ELEMENTOR_TEMPLATE_ID_DOCUMENTS", 16227);
define("CD_ELEMENTOR_TEMPLATE_ID_CAROUSEL", 16231);
define("CD_ELEMENTOR_TEMPLATE_ID_CUSTOM", 16234);

// Meta keys for new post options (defined here to be globally available if needed elsewhere)
define("CD_META_KEY_SHOW_PAGE", "_cd_show_page_option");
define("CD_META_KEY_HIGHLIGHT", "_cd_highlight_option");

// Helper function for conditional logging
function cd_error_log($message)
{
	if (defined("WP_DEBUG") && WP_DEBUG === true) {
		error_log("[CUSTOM DASHBOARD DEBUG] " . $message);
	}
}
//https://digitalexhibition.arch.tue.nl/wp-admin/post.php?post=16227&action=edit
// --- CUSTOM DASHBOARD WIDGETS ---
function cd_remove_default_dashboard_widgets()
{
	remove_meta_box("dashboard_incoming_links", "dashboard", "normal");
	remove_meta_box("dashboard_plugins", "dashboard", "normal");
	remove_meta_box("dashboard_primary", "dashboard", "side");
	remove_meta_box("dashboard_secondary", "dashboard", "normal");
	remove_meta_box("dashboard_quick_press", "dashboard", "side");
	remove_meta_box("dashboard_recent_drafts", "dashboard", "side");
	remove_meta_box("dashboard_recent_comments", "dashboard", "normal");
	remove_meta_box("dashboard_right_now", "dashboard", "normal");
	remove_meta_box("dashboard_activity", "dashboard", "normal");
	remove_meta_box("dashboard_site_health", "dashboard", "normal");
	remove_action("welcome_panel", "wp_welcome_panel");
}
add_action("wp_dashboard_setup", "cd_remove_default_dashboard_widgets", 999);

function cd_custom_dashboard_cta_widget()
{
	wp_add_dashboard_widget(
		"cd_custom_cta_widget",
		esc_html__("Quick Actions", CD_TEXTDOMAIN),
		"cd_render_custom_cta_widget"
	);
}
add_action("wp_dashboard_setup", "cd_custom_dashboard_cta_widget", 1);

function cd_render_custom_cta_widget()
{
	$new_project_url = admin_url("post-new.php?post_type=post");
	$add_media_url = admin_url("media-new.php");
	$templates_cpt_url = admin_url("edit.php?post_type=template");
	$page_url = admin_url("edit.php?post_type=page");
	// Assuming 'template' is a CPT
	?>

    <div class="cd-custom-cta-buttons">
        <a href="<?php echo esc_url(
        	$new_project_url
        ); ?>" class="button button-primary">
            <?php esc_html_e("Add new project", CD_TEXTDOMAIN); ?>
        </a>
        <a href="<?php echo esc_url(
        	$add_media_url
        ); ?>" class="button button-secondary">
            <?php esc_html_e("Add media", CD_TEXTDOMAIN); ?>
        </a>
        <a href="<?php echo esc_url(
        	$templates_cpt_url
        ); ?>" class="button button-secondary">
            <?php esc_html_e("Templates", CD_TEXTDOMAIN); ?>
        </a>
        <a href="<?php echo esc_url(
        	$page_url
        ); ?>" class="button button-secondary">
            <?php esc_html_e("Page overviews", CD_TEXTDOMAIN); ?>
        </a>
    </div>

    <?php
}

function cd_move_custom_cta_widget_to_top()
{
	global $wp_meta_boxes;
	if (
		isset($wp_meta_boxes["dashboard"]["normal"]["core"]["cd_custom_cta_widget"])
	) {
		$custom_widget =
			$wp_meta_boxes["dashboard"]["normal"]["core"]["cd_custom_cta_widget"];
		unset(
			$wp_meta_boxes["dashboard"]["normal"]["core"]["cd_custom_cta_widget"]
		);
		$wp_meta_boxes["dashboard"]["normal"]["core"] = array_merge(
			["cd_custom_cta_widget" => $custom_widget],
			$wp_meta_boxes["dashboard"]["normal"]["core"]
		);
	}
}
add_action("wp_dashboard_setup", "cd_move_custom_cta_widget_to_top", 9999);

function cd_my_custom_dashboard_another_widget()
{
	wp_add_dashboard_widget(
		"cd_my_custom_another_widget",
		esc_html__("Other Custom Widget", CD_TEXTDOMAIN),
		"cd_render_my_custom_another_widget"
	);
}
add_action("wp_dashboard_setup", "cd_my_custom_dashboard_another_widget", 10);

function cd_render_my_custom_another_widget()
{
	echo "<p>" .
		esc_html__(
			"Contenuto del mio altro widget personalizzato.",
			CD_TEXTDOMAIN
		) .
		"</p>";
}

function cd_add_dashboard_widgets()
{
	wp_add_dashboard_widget(
		"cd_dashboard_analytics", // ID univoco del widget.
		"Analytics", // Titolo del widget mostrato nella bacheca.
		"cd_dashboard_widget_display" // Funzione di callback per renderizzare l'HTML del widget.
	);
}
add_action("wp_dashboard_setup", "cd_add_dashboard_widgets");

function cd_dashboard_widget_display()
{
	// --- Annotazione Chiave ---
	$image_url = plugins_url("images/analyst.png", __FILE__);

	echo '<div style="text-align: center;">';
	echo '<img src="' .
		esc_url($image_url) .
		'" alt="Analyst Image" style="max-width: 100%; height: auto;">';
	echo "</div>";
}

// --- ACF DEPENDENCY CHECK & POST EDITOR MODIFICATIONS ---
add_action(
	"init",
	function () {
		if (!function_exists("get_field")) {
			add_action("admin_notices", function () {
				echo '<div class="notice notice-error"><p>' .
					esc_html__(
						'The Custom Dashboard plugin requires Advanced Custom Fields (ACF) to work properly with editor modifications.',
						CD_TEXTDOMAIN
					) .
					"</p></div>";
			});
			return;
		}
	},
	0
);

// Disable Gutenberg for 'post' post type
add_filter(
    "use_block_editor_for_post_type",
    function ($use_block_editor, $post_type) {
        $disabled_post_types = ["post", "courses", "seminars", "archiprix"]; // [2026-03-18 - Added archiprix CPT] // [2026-03-18 - Added seminars: disable Gutenberg, same as post and courses]

        if (in_array($post_type, $disabled_post_types, true)) {
            return false;
        }

        return $use_block_editor;
    },
    10,
    2
);
// Hide standard editor elements for 'post' CPT
add_action("admin_head", function () {
    $screen = get_current_screen();
    $post_types_to_modify = ["post", "courses", "seminars", "archiprix"]; // [2026-03-18 - Added archiprix CPT] // [2026-03-18 - Added seminars: hide title/editor divs same as post and courses]

    if ($screen && in_array($screen->post_type, $post_types_to_modify, true) && $screen->base === "post") { ?>
        <style>
            #titlediv,
            #postdivrich { /* Nasconde il box del titolo e dell'editor classico */
                display: none !important;
            }
            /* Slug box repositioned below ACF title field */
            #cd-slug-box-wrapper {
                margin-top: 4px;
                font-size: 13px;
                color: #50575e;
            }
            #cd-slug-box-wrapper #edit-slug-box {
                display: block !important;
            }
        </style>
        <?php }
});

// [2026-03-18 - Inject custom slug field below ACF title field (field_6830b9ab7fd58), works on new and existing posts]
add_action('acf/input/admin_footer', function() {
    $screen = get_current_screen();
    $post_types_to_modify = ["post", "courses", "seminars", "archiprix"];
    if ( ! $screen || ! in_array($screen->post_type, $post_types_to_modify, true) || $screen->base !== 'post' ) return;

    global $post;
    $current_slug = ( $post && ! empty( $post->post_name ) ) ? $post->post_name : '';
    ?>
    <style>
        #cd-slug-field-wrapper {
            margin-top: 6px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13px;
            color: #50575e;
            width: 100%;
            margin-bottom: 20px;
            margin-left: 16px;
            flex-direction: column;
        }
        #cd-slug-field-wrapper label {
            font-weight: 600;
            white-space: nowrap;
        }
        #cd-slug-input {
            flex: 1;
            max-width: 320px;
            padding: 4px 8px;
            font-size: 13px;
            border: 1px solid #8c8f94;
            border-radius: 3px;
            color: #1d2327;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        function cd_inject_slug_field() {
            var $target = $('[data-key="field_6830b9ab7fd58"]');
            if (!$target.length) return;
            if ($('#cd-slug-field-wrapper').length) return;

            var currentSlug = $('#post_name').val() || <?php echo wp_json_encode( $current_slug ); ?>;

            var $wrapper = $(
                '<div id="cd-slug-field-wrapper">' +
                    '<label for="cd-slug-input">Slug:</label>' +
                    '<input type="text" id="cd-slug-input" value="' + currentSlug + '" />' +
                '</div>'
            );

            $target.after($wrapper);

            // Sync typing into WP hidden #post_name field
            $('#cd-slug-input').on('input', function() {
                var sanitized = $(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
                $('#post_name').val(sanitized);
                $(this).val(sanitized);
            });
        }

        cd_inject_slug_field();
        if (typeof acf !== 'undefined') {
            acf.addAction('ready', cd_inject_slug_field);
        }
    });
    </script>
    <?php
});

add_action(
    "acf/save_post",
    function ($post_id) {
        $allowed_post_types = ["post", "courses", "seminars", "archiprix"]; // [2026-03-18 - Added archiprix CPT] // [2026-03-18 - Added seminars: sync ACF title field on save]
        if (!in_array(get_post_type($post_id), $allowed_post_types, true)) {
            return;
        }

        // Controlli di sicurezza standard
        if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
            return;
        }
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Prevenzione loop infinito
        static $is_running = false;
        if ($is_running) {
            return;
        }
        $is_running = true;

        // Legge il nuovo titolo usando il percorso CORRETTO (gruppo > campo)
        $new_title_from_acf = null;
        if (isset($_POST["acf"][CD_ACF_GROUP_KEY][CD_ACF_TITLE_FIELD_KEY])) {
            $new_title_from_acf = sanitize_text_field(
                $_POST["acf"][CD_ACF_GROUP_KEY][CD_ACF_TITLE_FIELD_KEY]
            );
        }

        // Se non abbiamo trovato un titolo, non facciamo nulla.
        if ($new_title_from_acf === null) {
            $is_running = false;
            return;
        }

        // Ottiene il titolo attuale del post per confrontarlo
        $current_post_title = get_the_title($post_id);

        // Se i titoli sono diversi, aggiorna solo il titolo — lo slug NON viene toccato
        if ($current_post_title !== $new_title_from_acf) {
            wp_update_post([
                "ID" => $post_id,
                "post_title" => $new_title_from_acf,
            ]);
        }

        $is_running = false;
    },
    20
);

/**
 * Funzione 2: Popola il campo ACF con il titolo del post quando si carica l'editor.
 * Questo assicura che il campo ACF mostri sempre il valore aggiornato.
 */
add_filter(
     "acf/load_value/key=" . CD_ACF_TITLE_FIELD_KEY,
     function ($value, $post_id, $field) {
         $allowed_post_types = ["post", "courses", "seminars", "archiprix"]; // [2026-03-18 - Added archiprix CPT] // [2026-03-18 - Added seminars: load ACF title from post_title on editor open]
         if (!in_array(get_post_type($post_id), $allowed_post_types, true)) {
             return $value;
         }

         if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
             return $value;
         }

         $post_obj = get_post($post_id);
         return $post_obj ? $post_obj->post_title : $value;
     },
     10,
     3
 );

/**
 * Funzioni di supporto per le colonne admin e la notifica (invariate)
 */
add_filter("manage_post_posts_columns", function ($columns) {
	$new_columns = [];
	foreach ($columns as $key => $title) {
		if ($key == "title") {
			$new_columns["cd_acf_title"] = "Project Title (from ACF)";
		} else {
			$new_columns[$key] = $title;
		}
	}
	return $new_columns;
});

add_action(
	"manage_post_posts_custom_column",
	function ($column, $post_id) {
		if ($column !== "cd_acf_title") {
			return;
		}
		$field_name = get_field_object(CD_ACF_TITLE_FIELD_KEY)["name"];
		$display_title = get_field($field_name, $post_id);
		if (empty($display_title)) {
			$display_title = get_the_title($post_id);
		}
		$edit_link = get_edit_post_link($post_id);
		echo '<strong><a class="row-title" href="' .
			esc_url($edit_link) .
			'">' .
			esc_html($display_title) .
			"</a></strong>";
	},
	10,
	2
);

add_action("edit_form_after_title", function () {
    $screen = get_current_screen();
    $allowed_post_types = ["post", "courses", "seminars", "archiprix"]; // [2026-03-18 - Added archiprix CPT] // [2026-03-18 - Added seminars: show ACF title notice after title field]

    if ($screen && in_array($screen->post_type, $allowed_post_types, true) && $screen->base === "post") { ?>
        <div class="notice notice-info inline" style="margin-top: 10px; margin-bottom:10px;">
            <p><strong>Note:</strong> Use the ACF field below to manage the title. The standard WordPress title will be automatically synchronized.</p>
        </div>
        <?php }
});

// --- REMOVE QUICK EDIT FUNCTIONALITY ---
add_filter("post_row_actions", "cd_remove_quick_edit_link", 10, 2);
function cd_remove_quick_edit_link($actions, $post)
{
    $allowed_post_types = ["post", "courses", "seminars", "archiprix"]; // [2026-03-18 - Added archiprix CPT] // [2026-03-18 - Added seminars: remove Quick Edit from row actions]
    if (in_array($post->post_type, $allowed_post_types, true)) {
        unset($actions["inline hide-if-no-js"]);
    }
    return $actions;
}

// --- ACF RADIO BUTTON WITH IMAGE PREVIEW ---
global $cd_acf_template_images_global_var;
$cd_acf_template_images_global_var = [];

add_action(
	"init",
	function () {
		global $cd_acf_template_images_global_var;
		$plugin_url = CD_PLUGIN_URL;
		$cd_acf_template_images_global_var = [
			"1" => $plugin_url . "images/template_1.png",
			"2" => $plugin_url . "images/template_2.png",
			"3" => $plugin_url . "images/template_3.png",
			"4" => $plugin_url . "images/template_4.png",
		];

		if (current_user_can("manage_options") && defined("WP_DEBUG") && WP_DEBUG) {
			$plugin_dir = CD_PLUGIN_DIR;
			foreach ($cd_acf_template_images_global_var as $key => $url) {
				if (empty($url)) {
					continue;
				}
				$relative_path = str_replace($plugin_url, "", $url);
				$file_path = $plugin_dir . $relative_path;
				if (!file_exists($file_path)) {
					cd_error_log(
						sprintf(
							"ACF preview image file not found for template. %s. Path: %s",
							$key,
							$file_path
						)
					);
				}
			}
		}
	},
	5
);

add_action("admin_menu", function () {
	if (
		!(defined("WP_DEBUG") && WP_DEBUG && current_user_can("manage_options"))
	) {
		return;
	}

	add_submenu_page(
		"tools.php",
		"Debug Immagini Template (CD)",
		"Debug Immagini (CD)",
		"manage_options",
		"cd-debug-template-images",
		function () {
			global $cd_acf_template_images_global_var;
			$plugin_dir = CD_PLUGIN_DIR;
			$plugin_url = CD_PLUGIN_URL;
			?>
            <div class="wrap"><h1>Debug Immagini Template Anteprima ACF (Custom Dashboard)</h1>
            <p>Le immagini devono trovarsi nella cartella: <code><?php echo esc_html(
            	trailingslashit(str_replace(ABSPATH, "", $plugin_dir)) . "images/"
            ); ?></code></p>
            <table class="widefat fixed striped">
                <thead><tr><th>Chiave</th><th>URL</th><th>Path Relativo</th><th>Path Assoluto</th><th>Esiste?</th><th>Anteprima</th></tr></thead>
                <tbody>
                <?php if (!empty($cd_acf_template_images_global_var)):
                	foreach ($cd_acf_template_images_global_var as $key => $url):

                		if (empty($url)) {
                			continue;
                		}
                		$relative_path = str_replace($plugin_url, "", $url);
                		$absolute_path = $plugin_dir . $relative_path;
                		$exists = file_exists($absolute_path);
                		?>
                <tr>
                    <td>Template <?php echo esc_html($key); ?></td>
                    <td><code><?php echo esc_url($url); ?></code></td>
                    <td><code>images/<?php echo esc_html(
                    	basename($relative_path)
                    ); ?></code></td>
                    <td><code><?php echo esc_html(
                    	$absolute_path
                    ); ?></code></td>
                    <td><?php echo $exists
                    	? '<span style="color:green;">✓ SI</span>'
                    	: '<span style="color:red;">✗ NO</span>'; ?></td>
                    <td><?php if ($exists): ?><img src="<?php echo esc_url(
	$url
); ?>" style="max-width:150px;height:auto;border:1px solid #ddd;"><?php else: ?><em>N/A</em><?php endif; ?></td>
                </tr>
                <?php
                	endforeach;
                else:
                	 ?>
                <tr><td colspan="6"><?php esc_html_e(
                	"Not image set",
                	CD_TEXTDOMAIN
                ); ?></td></tr>
                <?php
                endif; ?>
                </tbody>
            </table>
            </div>
            <?php
		}
	);
});

add_action("acf/input/admin_head", function () {
    global $cd_acf_template_images_global_var, $post;
    if (
        !function_exists("get_field") ||
        !is_array($cd_acf_template_images_global_var) ||
        empty($cd_acf_template_images_global_var)
    ) {
        return;
    }

    $current_post_id_for_js = 0; if (isset($post->ID)) { $current_post_id_for_js = $post->ID; } elseif (isset($_GET["post"]) && is_numeric($_GET["post"])) { $current_post_id_for_js = intval($_GET["post"]);
  }

    $elementor_edit_url = "";
    if ($current_post_id_for_js && class_exists("\Elementor\Plugin")) {
        $document = \Elementor\Plugin::$instance->documents->get(
            $current_post_id_for_js
        );
        if ($document) {
            $elementor_edit_url = $document->get_edit_url();
        }
    }
	?>
    <style>
        body.elementor-editor-inactive #elementor-switch-mode, .notice-info, .e-notice {
            /* display: none; */
        }
        .acf-field[data-name="<?php echo esc_attr(CD_ACF_FIELD_NAME_TEMPLATE_CHOICE); ?>"] .acf-radio-list {
            display: flex !important; flex-wrap: wrap !important; margin:0 !important; padding: 10px 0 !important;justify-content: space-between;
        }
        .acf-field[data-name="<?php echo esc_attr(CD_ACF_FIELD_NAME_TEMPLATE_CHOICE); ?>"] .acf-radio-list li {
            flex: 1 1 calc(25% - 27px) !important; max-width: calc(25% - 27px) !important;
            min-width: 190px !important; margin: 0 !important; padding: 0 !important; float: none !important; list-style-type: none !important;
        }
        .acf-field[data-name="<?php echo esc_attr(CD_ACF_FIELD_NAME_TEMPLATE_CHOICE); ?>"] .acf-radio-list li label {
            display: block !important; padding:0 !important; margin:0 !important; font-weight:normal !important; width:100%;
        }
        .template-preview-wrapper {
            position:relative; border:3px solid #ccd0d4; border-radius:6px; overflow:hidden; cursor:pointer;
            transition: all 0.2s ease-in-out; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.07);
            height:100%; display:flex; flex-direction:column;
        }
        .template-preview-wrapper:hover { border-color:#b0b5b9; transform:translateY(-2px); box-shadow:0 4px 8px rgba(0,0,0,0.1); }
        .template-preview-wrapper.is-selected { border-color:#007cba; box-shadow:0 0 0 1px #007cba, 0 2px 5px rgba(0,124,186,0.25); }
        .template-preview-wrapper input[type="radio"] {
            position:absolute !important; clip:rect(1px,1px,1px,1px) !important; padding:0 !important; border:0 !important;
            height:1px !important; width:1px !important; overflow:hidden !important; opacity:0 !important;
        }
        .template-preview-image { width:100%; height:260px; background-color:#e8eaeb; position:relative; overflow:hidden; }
        .template-preview-image img { width:100%; height:100%; object-fit:contain; display:block; }
        .template-preview-placeholder {
            width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background: #f0f0f1;
        }
        .template-preview-placeholder .dashicons { font-size:40px; width:40px; height:40px; color:#94999d; margin-bottom:8px; }
        .template-preview-placeholder .preview-text { font-size:10px; text-transform:uppercase; letter-spacing:0.5px; color:#757b80; }
        .template-preview-info { padding:12px 15px; background:#fff; border-top:1px solid #dcdcde; flex-grow:1; display:flex; flex-direction:column; }
        .template-preview-name { font-weight:600; font-size:13px; color:#1d2327; margin-bottom:5px; }
        .template-preview-desc { font-size:12px; color:#44494e; line-height:1.45; margin:0; flex-grow:1; }
        .template-preview-check {
            position:absolute; top:8px; right:8px; width:22px; height:22px; background:#007cba; border-radius:50%;
            display:none; align-items:center; justify-content:center; color:#fff; z-index:10;
        }
        .template-preview-wrapper.is-selected .template-preview-check { display:flex; }

        #cd-elementor-confirm-modal {
            position: fixed; z-index: 160000; left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; display: none; align-items: center; justify-content: center;
            -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
        }
        #cd-elementor-confirm-modal.cd-modal-active { display: flex !important; }
        .cd-modal-overlay {
            position: fixed; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.65);
        }
        .cd-modal-content {
            background-color: #fff; margin: auto; padding: 25px 30px; border: none; width: 90%; max-width: 480px;
            border-radius: 4px; box-shadow: 0 5px 20px rgba(0,0,0,0.25); position: relative; z-index: 160001; font-size: 14px;
        }
        .cd-modal-content h4 {
            margin-top: 0; margin-bottom:15px; font-size: 1.25em; font-weight:600; color:#1d2327;
            padding-bottom: 10px; border-bottom: 1px solid #ddd;
        }
        .cd-modal-content p { margin-bottom: 12px; line-height: 1.6; color: #3c434a; }
        .cd-modal-actions { margin-top: 25px; text-align: right; display:flex; justify-content: flex-end; gap: 10px;}

        @media (max-width: 1600px) {
            #template_blocchi .acf-radio-list {
            justify-content: center;
            gap: 0px;
            }
            #template_blocchi ul li {
              flex: auto !important;
              max-width: calc(50% - 10px) !important;
              flex-basis: 50% !important;
              padding: 2px !important;
            }
        }
        @media (max-width: 1200px) {
            .acf-field[data-name="<?php echo esc_attr(
            	CD_ACF_FIELD_NAME_TEMPLATE_CHOICE
            ); ?>"] .acf-radio-list li {
                flex-basis: calc(50% - 20px) !important; max-width: calc(50% - 20px) !important;
            }

        }
        @media (max-width: 782px) {
            .acf-field[data-name="<?php echo esc_attr(
            	CD_ACF_FIELD_NAME_TEMPLATE_CHOICE
            ); ?>"] .acf-radio-list li {
                flex-basis: 100% !important; max-width: 100% !important;
            }
            .template-preview-image { height: 150px; }
            .cd-modal-content { width: 90%; padding: 20px; }
            .cd-modal-content h4 { font-size: 1.15em; }
            .cd-modal-actions { flex-direction: column-reverse; gap:12px; }
            .cd-modal-actions .button { width:100%; margin-left:0; text-align:center;}
        }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {

       if (sessionStorage.getItem('cd_scroll_to_template')) {
           setTimeout(function() {
               var $target = $('.acf-field[data-name="scelta_template"]');
               if ($target.length) {
                   $('html, body').animate({
                       scrollTop: $target.offset().top - 42 // Offset per la barra di amministrazione
                   }, 400);
               }
               sessionStorage.removeItem('cd_scroll_to_template');
           }, 150);
       }


        var acfRadioFieldName = <?php echo wp_json_encode(
        	CD_ACF_FIELD_NAME_TEMPLATE_CHOICE
        ); ?>;
        var acfPreviewImageData = <?php echo wp_json_encode(
        	$cd_acf_template_images_global_var
        ); ?>;
        var currentPostId = <?php echo wp_json_encode(
        	$current_post_id_for_js
        ); ?>;
        var elementorEditUrl = <?php echo wp_json_encode(
        	$elementor_edit_url
        ); ?>;

        var acfRadioTemplateConfigs = {
            '1': { name: <?php echo wp_json_encode(
            	__("Template Standard", CD_TEXTDOMAIN)
            ); ?>, desc: <?php echo wp_json_encode(__("Introduction, Gallery", CD_TEXTDOMAIN)); ?>, icon: 'dashicons-format-gallery', image: acfPreviewImageData['1'] || '' },
            '2': { name: <?php echo wp_json_encode(
            	__("Template Documents", CD_TEXTDOMAIN)
            ); ?>, desc: <?php echo wp_json_encode(__("Introduction, Gallery, PDF and video.", CD_TEXTDOMAIN)); ?>, icon: 'dashicons-media-document', image: acfPreviewImageData['2'] || '' },
            '3': { name: <?php echo wp_json_encode(
            	__("Template Carousel", CD_TEXTDOMAIN)
            ); ?>, desc: <?php echo wp_json_encode(__("Introduction, Text, Carousel", CD_TEXTDOMAIN)); ?>, icon: 'dashicons-media-document', image: acfPreviewImageData['3'] || '' },
            '4': { name: <?php echo wp_json_encode(
            	__("Elementor ", CD_TEXTDOMAIN)
            ); ?>, desc: <?php echo wp_json_encode(__("Custom Layout", CD_TEXTDOMAIN)); ?>, icon: 'dashicons-layout', image: acfPreviewImageData['4'] || '' }
        };

        var isProcessingTemplateSwitch = false;
        var originalSelectedTemplate = null;
        var radioChangeHandler = null; // To store and re-attach the handler

        function escHtmlJs(text) { if (typeof text !== 'string') return ''; var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }; return text.replace(/[&<>"']/g, function(m) { return map[m]; });}
        function escUrlJs(url) { return typeof url === 'string' ? encodeURI(url) : ''; }
        function escAttrJs(text) { return typeof text === 'string' ? text.replace(/[^a-zA-Z0-9\s_-]/g, '') : ''; }


        if ($('#cd-elementor-confirm-modal').length === 0) {
            $('body').append(
                '<div id="cd-elementor-confirm-modal" style="display:none;">' +
                '<div class="cd-modal-overlay"></div>' +
                '<div class="cd-modal-content">' +
                '<h4><?php echo esc_js(
                	__("Conferma Azione", CD_TEXTDOMAIN)
                ); ?></h4>' +
                '<p id="cd-modal-message"></p>' +
                '<div class="cd-modal-actions">' +
                '<button id="cd-modal-cancel" class="button button-secondary"><?php echo esc_js(
                	__("Annulla", CD_TEXTDOMAIN)
                ); ?></button>' +
                '<button id="cd-modal-confirm" class="button button-primary"><?php echo esc_js(
                	__("Conferma", CD_TEXTDOMAIN)
                ); ?></button>' +
                '</div></div></div>'
            );
        }

        var $modal = $('#cd-elementor-confirm-modal');
        var $modalMessage = $('#cd-modal-message');
        var confirmCallback = null;
        var cancelCallback = null;

        function cd_manage_elementor_fields_visibility() {
            var isElementorActive = $('body').hasClass('elementor-editor-active');
            var isTemplate3Selected = $('.acf-field[data-name="' + acfRadioFieldName + '"] input[type="radio"][value="4"]').is(':checked');

            // Elenco dei campi da nascondere/mostrare
            var fieldsToToggle = $([
                '.acf-field[data-name="description"]',
                '.acf-field[data-name="teachers"]',
                '.acf-field[data-name="coordination"]',
                '.acf-field[data-name="students"]',
                '.acf-field[data-name="other_infomation"]'
            ].join(', '));

            if (isElementorActive && isTemplate3Selected) {
                console.log('[CD] Elementor active on template 4. Hiding standard fields.');
                fieldsToToggle.hide();
            } else {
                console.log('[CD] Not Elementor on template 4. Showing standard fields.');
                fieldsToToggle.show();
            }
        }

        $('#cd-modal-confirm').on('click', function() {
            if (typeof confirmCallback === 'function') confirmCallback();
            $modal.removeClass('cd-modal-active');
        });
        $('#cd-modal-cancel, .cd-modal-overlay').on('click', function() {
            if (typeof cancelCallback === 'function') cancelCallback();
            $modal.removeClass('cd-modal-active');
        });

        function showConfirmationModal(message, onConfirm, onCancel) {
            $modalMessage.html(message);
            confirmCallback = onConfirm;
            cancelCallback = onCancel; // Store cancel callback
            $modal.addClass('cd-modal-active');
        }

        function applyInitialRadioState($radioList) {
              var $checkedInput = $radioList.find('input[type="radio"]:checked');

        if (!$checkedInput.length && $radioList.find('input[type="radio"]').length) {
                        $checkedInput = $radioList.find('input[type="radio"]').first();


                        $checkedInput.prop('checked', true).trigger('change');
                    }

            if (!$checkedInput.length) {
                originalSelectedTemplate = null;
                return;
            }

            originalSelectedTemplate = $checkedInput.val();
            console.log('[CD] Initial selected template:', originalSelectedTemplate);

            $radioList.find('.template-preview-wrapper').removeClass('is-selected');
            $checkedInput.closest('.template-preview-wrapper').addClass('is-selected');

            if ($('body').hasClass('elementor-editor-active')) {
                console.log('[CD] Elementor editor is active. Forcing selection to Template 4.');
                // Deseleziona qualsiasi opzione attualmente selezionata per sicurezza
                $radioList.find('input[type="radio"]:checked').prop('checked', false);
                // Seleziona programmaticamente il radio button per il template 3 (il cui valore è '3')
                $radioList.find('input[type="radio"][value="4"]').prop('checked', true);
            }

            var $checkedInput = $radioList.find('input[type="radio"]:checked');

            if (!$checkedInput.length && $radioList.find('input[type="radio"]').length) {
                $checkedInput = $radioList.find('input[type="radio"]').first();
                $checkedInput.prop('checked', true);
            }

            if (!$checkedInput.length) {
                originalSelectedTemplate = null;
                return;
            }

           originalSelectedTemplate = $checkedInput.val();
               console.log('[CD] Initial selected template:', originalSelectedTemplate);

               $radioList.find('.template-preview-wrapper').removeClass('is-selected');
               $checkedInput.closest('.template-preview-wrapper').addClass('is-selected');

               if (originalSelectedTemplate === '4') {
                   $('#elementor-switch-mode-button').show();
                   if ($('body').hasClass('elementor-editor-active')) {
                       $('.elementor-editor').show();
                   } else {
                       // If not active, Elementor UI should be hidden by default or by Elementor itself
                       $('.elementor-editor').hide();
                   }
               } else {
                   $('#elementor-switch-mode-button, #post-body-content').hide();
                   $('.elementor-editor').hide();
               }

               cd_manage_elementor_fields_visibility();
           }

        radioChangeHandler = function() {
            cd_manage_elementor_fields_visibility();
            var newSelectedTemplate = $(this).val();

            if (isProcessingTemplateSwitch && newSelectedTemplate === originalSelectedTemplate) {
                // This case handles when a revert action programmatically re-selects the original template.
                // We don't want to re-process actions for it, just ensure UI is correct.
                console.log('[CD] Reverted to original template, no further action:', newSelectedTemplate);
                isProcessingTemplateSwitch = false; // Allow next genuine user click
                return;
            }

            if (isProcessingTemplateSwitch) {
                console.log('[CD] Template switch already in progress for:', newSelectedTemplate, 'from:', originalSelectedTemplate, '. Ignoring.');
                return;
            }

            var $currentWrap = $(this).closest('.template-preview-wrapper');
            var $radioList = $(this).closest('.acf-radio-list');

            console.log('[CD] User/programmatic change to:', newSelectedTemplate, 'from:', originalSelectedTemplate);

            // Immediate UI updates based on new selection
            $radioList.find('.template-preview-wrapper').removeClass('is-selected');
            $currentWrap.addClass('is-selected');

            if (newSelectedTemplate === '1' || newSelectedTemplate === '2' || newSelectedTemplate === '3') {
                console.log('[CD] Hiding Elementor UI for Template 1/2');
                $('.elementor-editor').hide();
                $('#elementor-switch-mode-button').hide();
            } else if (newSelectedTemplate === '4') {
                console.log('[CD] Showing Elementor button for Template 4');
                $('#elementor-switch-mode-button').show();
                // If body.elementor-editor-active, Elementor's UI should be visible.
                // We don't force .show() here as Elementor might not be "ready" or "active" yet.
                // The action to switch TO elementor will handle making it visible.
            }

            if (newSelectedTemplate === originalSelectedTemplate) {
                console.log('[CD] Template value effectively unchanged, no action needed.');
                // isProcessingTemplateSwitch = false; // Should already be false or will be reset by caller.
                return;
            }

            isProcessingTemplateSwitch = true;

            var revertSelection = function() {
                console.log('[CD] Reverting selection to:', originalSelectedTemplate);

                // Detach handler to prevent loop during programmatic change
                $radioList.off('change.acfpreview', 'input[type="radio"]');

                $radioList.find('input[type="radio"][value="' + originalSelectedTemplate + '"]').prop('checked', true);

                // Manually update UI for reverted selection
                $radioList.find('.template-preview-wrapper').removeClass('is-selected');
                var $originalWrap = $radioList.find('input[type="radio"][value="' + originalSelectedTemplate + '"]').closest('.template-preview-wrapper');
                $originalWrap.addClass('is-selected');

                if (originalSelectedTemplate === '1' || originalSelectedTemplate === '2' || originalSelectedTemplate === '3') {
                    $('.elementor-editor').hide();
                    $('#elementor-switch-mode-button').hide();
                } else if (originalSelectedTemplate === '4') {
                    $('#elementor-switch-mode-button').show();
                    if ($('body').hasClass('elementor-editor-active')) { // Only show if Elementor is truly active
                        $('.elementor-editor').show();
                    } else {
                        $('.elementor-editor').hide();
                    }
                }

                cd_manage_elementor_fields_visibility();
                isProcessingTemplateSwitch = false; // Allow new interactions

                // Re-attach the handler
                $radioList.on('change.acfpreview', 'input[type="radio"]', radioChangeHandler);
            };

            var confirmAndSaveChanges = function(callbackAfterSave) {
                console.log('[CD] Saving post (trigger #publish)');
                sessionStorage.setItem('cd_scroll_to_template', 'true'); // <-- Ecco la riga aggiunta
                $('#publish').trigger('click');
                setTimeout(function() {
                    console.log('[CD] Post save potentially complete.');
                    if (typeof callbackAfterSave === 'function') {
                        callbackAfterSave();
                    }
                    originalSelectedTemplate = newSelectedTemplate; // Update state AFTER successful action
                    isProcessingTemplateSwitch = false;
                }, 1500); // Delay for save action to complete
            };

            // --- Action Logic ---
            // 1. Switching TO Elementor (Template 3) from 1 or 2
            if (newSelectedTemplate === '4' && (originalSelectedTemplate === '1' || originalSelectedTemplate === '2' || originalSelectedTemplate === '3' || originalSelectedTemplate === null) ) {
                showConfirmationModal(
                    '<?php echo esc_js(
                    	__(
                    		"To use the Elementor Template, the post must be saved. Would you like to save and proceed to activate Elementor?",
                    		CD_TEXTDOMAIN
                    	)
                    ); ?>',
                    function() { // On Confirm
                        confirmAndSaveChanges(function() {
                            if (!$('body').hasClass('elementor-editor-active') && elementorEditUrl) {
                                console.log('[CD] Redirecting to Elementor editor:', elementorEditUrl);
                                window.location.href = elementorEditUrl;
                            } else {
                                console.log('[CD] Elementor already active or URL not available. Ensuring UI is visible.');
                                $('.elementor-editor').show(); // Make sure it's visible
                                $('#elementor-switch-mode-button').show();
                            }
                        });
                    },
                    revertSelection // On Cancel
                );
            // 2. Switching FROM Elementor (Template 3) to 1 or 2
            } else if ((newSelectedTemplate === '1' || newSelectedTemplate === '2' || newSelectedTemplate === '3') && originalSelectedTemplate === '4') {
                if ($('body').hasClass('elementor-editor-active')) {
                    showConfirmationModal(
                        '<?php echo esc_js(
                        	__(
                        		'You are about to exit Elementor. Do you want to save the post and return to the standard editor?',
                        		CD_TEXTDOMAIN
                        	)
                        ); ?>',
                        function() { // On Confirm
                            confirmAndSaveChanges(function() {
                                if ($('body').hasClass('elementor-editor-active')) {
                                    console.log('[CD] Attempting to click Elementor switch-off button.');
                                    if ($('#elementor-switch-mode-button .elementor-switch-mode-on').length) {
                                         $('#elementor-switch-mode-button .elementor-switch-mode-on').trigger('click');
                                    } else {
                                        console.warn('[CD] Elementor switch-off button not found, reloading page.');
                                        window.location.reload();
                                    }
                                }
                                // UI for 1/2 (hiding Elementor) already handled at the start of this function.
                            });
                        },
                        revertSelection // On Cancel
                    );
                } else { // Was template 3, but Elementor UI not active. Just save.
                    console.log('[CD] Switching from Template 3 (Elementor UI not active). Saving.');
                    confirmAndSaveChanges(function() {
                        // UI for 1/2 (hiding Elementor) already handled.
                    });
                }
            // 3. Switching between non-Elementor templates (1 and 2)
            } else if ((newSelectedTemplate === '1' || newSelectedTemplate === '2' || newSelectedTemplate === '3') && (originalSelectedTemplate === '1' || originalSelectedTemplate === '2' || originalSelectedTemplate === '3' || originalSelectedTemplate === null)) {
                 console.log('[CD] Switching between/to non-Elementor templates. Saving.');
                 confirmAndSaveChanges(function(){
                    // UI for 1/2 (hiding Elementor) already handled.
                 });
            // 4. Re-selecting Template 3 when it's already Template 3 (no real change, but ensure UI)
            } else if (newSelectedTemplate === '4' && originalSelectedTemplate === '4') {
                console.log('[CD] Template 4 re-selected. Ensuring UI.');
                 $('#elementor-switch-mode-button').show();
                 if ($('body').hasClass('elementor-editor-active')) {
                    $('.elementor-editor').show();
                 } else {
                    // If not active, don't force show, user needs to click "Edit with Elementor"
                 }
                isProcessingTemplateSwitch = false; // No action, so reset flag
            } else {
                console.warn("[CD] Unhandled template switch condition. new:", newSelectedTemplate, "orig:", originalSelectedTemplate);
                isProcessingTemplateSwitch = false; // Reset flag for safety
            }
        };


        function setupAcfRadioPreview() {
            $('.acf-field[data-name="' + acfRadioFieldName + '"]').each(function() {
                var $field = $(this);
                if ($field.hasClass('cd-acf-preview-processed')) {
                    // If re-running on an already processed field, ensure initial state is re-applied
                    // This can happen with ACF conditional logic / repeater updates.
                    applyInitialRadioState($field.find('.acf-radio-list'));
                    return;
                }
                $field.addClass('cd-acf-preview-processed');
                var $radioList = $field.find('.acf-radio-list');

                $radioList.find('li').each(function() {
                    var $li = $(this);
                    var $label = $li.find('label');
                    var $input = $label.find('input[type="radio"]');
                    if (!$input.length) return;

                    var val = $input.val();
                    var cfg = acfRadioTemplateConfigs[val] || { name: 'Template ' + escHtmlJs(val), desc: '...', icon: 'dashicons-admin-page', image: '' };
                    var imageHtml = cfg.image ? '<img src="' + escUrlJs(cfg.image) + '" alt="' + escHtmlJs(cfg.name) + '">' : '<div class="template-preview-placeholder"><span class="dashicons ' + escAttrJs(cfg.icon) + '"></span><div class="preview-text">' + escHtmlJs("Anteprima") + '</div></div>';
                    var html = '<div class="template-preview-wrapper">' + $input[0].outerHTML + '<div class="template-preview-check"><span class="dashicons dashicons-yes-alt"></span></div>' + '<div class="template-preview-image">' + imageHtml + '</div>' + '<div class="template-preview-info"><div class="template-preview-name">' + escHtmlJs(cfg.name) + '</div><div class="template-preview-desc">' + escHtmlJs(cfg.desc) + '</div></div></div>';
                    $label.empty().html(html);
                });

                applyInitialRadioState($radioList);

                $radioList.on('click', '.template-preview-wrapper', function(e) {
                    e.preventDefault();
                    var $inp = $(this).find('input[type="radio"]');
                    if (!$inp.is(':checked')) {
                        $inp.prop('checked', true).trigger('change');
                    }
                });

                // Detach any previous handler before attaching
                $radioList.off('change.acfpreview', 'input[type="radio"]');
                // Attach the single, unified handler
                $radioList.on('change.acfpreview', 'input[type="radio"]', radioChangeHandler);
            });
        }

        setupAcfRadioPreview();
        if (typeof acf !== 'undefined') {
            acf.removeAction('ready_field/type=radio', setupAcfRadioPreview);
            acf.removeAction('append_field/type=radio', setupAcfRadioPreview);
            acf.addAction('ready_field/type=radio', setupAcfRadioPreview);
            acf.addAction('append_field/type=radio', setupAcfRadioPreview);
        }




    });
    </script>
    <?php
});

// --- SHORTCODE FOR DYNAMIC ELEMENTOR TEMPLATE ---
add_shortcode(
	"mio_template_dinamico_acf",
	"cd_funzione_shortcode_template_dinamico_acf"
);
function cd_funzione_shortcode_template_dinamico_acf()
{
	if (!is_singular()) {
		cd_error_log("Shortcode: Non è una pagina singola.");
		return "";
	}

	$post_id = get_queried_object_id();
	if (
		!function_exists("have_rows") ||
		!have_rows(CD_ACF_REPEATER_NAME_TEMPLATE_LAYOUTS, $post_id)
	) {
		cd_error_log(
			"Shortcode: Repeater '" .
				CD_ACF_REPEATER_NAME_TEMPLATE_LAYOUTS .
				"' non trovato per post ID {$post_id}."
		);
		return "";
	}

	$template_choice = "";
	while (have_rows(CD_ACF_REPEATER_NAME_TEMPLATE_LAYOUTS, $post_id)):
		the_row();
		$template_choice = get_sub_field(CD_ACF_FIELD_NAME_TEMPLATE_CHOICE);
		break;
	endwhile;

	reset_rows();

	if (empty($template_choice)) {
		cd_error_log(
			"Shortcode: Nessuna scelta di template valida nel repeater per post ID {$post_id}."
		);
		return "";
	}
	$template_choice = (string) $template_choice;

	$template_map = [
		"1" => CD_ELEMENTOR_TEMPLATE_ID_STANDARD,
		"2" => CD_ELEMENTOR_TEMPLATE_ID_DOCUMENTS,
		"3" => CD_ELEMENTOR_TEMPLATE_ID_CAROUSEL,
		"4" => CD_ELEMENTOR_TEMPLATE_ID_CUSTOM,
	];

	if (!isset($template_map[$template_choice])) {
		cd_error_log(
			"Shortcode: Valore '{$template_choice}' non mappato a un Elementor Template ID. Post ID {$post_id}."
		);
		return "";
	}
	$elementor_template_id = (int) $template_map[$template_choice];

	if (get_post_status($elementor_template_id) !== "publish") {
		cd_error_log(
			"Shortcode: Elementor Template ID {$elementor_template_id} non pubblicato o inesistente. Post ID {$post_id}."
		);
		return "";
	}

	if (
		class_exists("\Elementor\Plugin") &&
		\Elementor\Plugin::$instance->frontend
	) {
		cd_error_log(
			"Shortcode: Carico contenuto per Elementor template ID {$elementor_template_id}. Post ID {$post_id}."
		);
		return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display(
			$elementor_template_id
		);
	}

	cd_error_log(
		"Shortcode: Elementor non attivo o frontend non disponibile. Post ID {$post_id}."
	);
	return "";
}

// --- VIEW POST METABOX ---
if (!function_exists("mio_aggiungi_meta_box_visualizza_post_cta")) {
    function mio_aggiungi_meta_box_visualizza_post_cta()
    {
        $tipi_post = ["post", "courses"];
        foreach ($tipi_post as $tipo_post) {
            add_meta_box(
                "mio_visualizza_post_cta_metabox",
                "Quick Action",
                "mio_contenuto_meta_box_visualizza_post_cta",
                $tipo_post,
                "side",
                "high"
            );
        }
    }
}
add_action("add_meta_boxes", "mio_aggiungi_meta_box_visualizza_post_cta");

if (!function_exists("mio_contenuto_meta_box_visualizza_post_cta")) {
	function mio_contenuto_meta_box_visualizza_post_cta($post)
	{
		if ($post->post_status == "publish") {
			$link_visualizzazione = get_permalink($post->ID);
			echo '<p><a href="' .
				esc_url($link_visualizzazione) .
				'" class="button button-primary button-large" target="_blank" style="width: 100%; text-align: center;">View Post</a></p>';
		} else {
			$link_anteprima = get_preview_post_link($post->ID);
			if ($link_anteprima) {
				echo '<p><a href="' .
					esc_url($link_anteprima) .
					'" class="button button-secondary button-large" target="_blank" style="width: 100%; text-align: center;">Preview Changes</a></p>';
			}
			echo '<p style="text-align: center;">The post has not been published yet.</p>';
		}
	}
}

// cs_enqueue_acf_interaction_script rimosso: la logica di visibilità campi e
// gestione classi body è già gestita interamente da cd_manage_elementor_fields_visibility
// e applyInitialRadioState nel blocco acf/input/admin_head. Il doppio script
// causava un conflitto: aggiungeva elementor-editor-active al body artificialmente,
// interferendo con la visibilità di #elementor-switch-mode-button su template 4.


add_filter( 'acf/load_field/name=study_level', 'force_acf_taxonomy_context_robust' );

function force_acf_taxonomy_context_robust( $field ) {

    // Inizializziamo l'ID a 0
    $post_id = 0;

    // 1. Controllo prioritario: Chiamata AJAX di ACF (succede spesso con i Repeater/Gruppi)
    if ( isset( $_POST['post_id'] ) ) {
        $post_id = intval( $_POST['post_id'] );
    }
    // 2. Controllo secondario: Pagina di edit standard (Classic Editor o caricamento iniziale)
    elseif ( isset( $_GET['post'] ) ) {
        $post_id = intval( $_GET['post'] );
    }
    // 3. Controllo terziario: Fase di salvataggio
    elseif ( isset( $_POST['post_ID'] ) ) {
        $post_id = intval( $_POST['post_ID'] );
    }

    // Determiniamo il Post Type dall'ID recuperato
    $post_type = '';

    if ( $post_id ) {
        $post_type = get_post_type( $post_id );
    }

    // 4. Caso speciale: "Aggiungi Nuovo" (l'ID non esiste ancora)
    // Se non abbiamo trovato un ID, guardiamo se c'è il parametro nell'URL
    if ( ! $post_id && isset( $_GET['post_type'] ) ) {
        $post_type = sanitize_text_field( $_GET['post_type'] );
    }

    // --- LOGICA DI ASSEGNAZIONE ---

    // [2026-03-18 - Added seminars: map seminar_category for study_level field, same logic as courses]
    $taxonomy_map = [
        'courses'   => 'course_category',
        'seminars'  => 'seminar_category',
        'archiprix' => 'archiprix_category', // [2026-03-18 - Added archiprix CPT]
    ];

    if ( isset( $taxonomy_map[ $post_type ] ) ) {
        $field['taxonomy'] = $taxonomy_map[ $post_type ];
    } else {
        $field['taxonomy'] = 'category';
    }

    return $field;
}

require_once CD_PLUGIN_DIR . "includes/style.php";
require_once CD_PLUGIN_DIR . "includes/extra.php";
require_once CD_PLUGIN_DIR . "includes/shortcode.php";
require_once CD_PLUGIN_DIR . "includes/sceltaposizione.php";
require_once CD_PLUGIN_DIR . "includes/ruolo.php";
require_once CD_PLUGIN_DIR . "includes/cpt.php";
// fnpdf.php added a "Create New FlipPDF" button next to the pdf_post
// repeater's shortcode_pdf field, opening a modal to create a dflip CPT
// post and copy its shortcode back in. That whole workflow is gone now
// that pdf_post uses a plain ACF File field (pdf_file) instead — editors
// just pick a PDF from the media library. File kept for reference.
require_once CD_PLUGIN_DIR . "includes/switch-post-type.php";
