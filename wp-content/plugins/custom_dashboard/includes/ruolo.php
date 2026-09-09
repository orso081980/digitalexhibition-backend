<?php
function cd_add_persistent_school_role()
{
	// Controlla se il ruolo 'school' NON esiste già
	if (!get_role("school")) {
		// Se non esiste, clona le capacità (permessi) del ruolo 'Editor'
		$editor_caps = get_role("editor")->capabilities;

		add_role("school", __("School", CD_TEXTDOMAIN), $editor_caps);
	}
}
// Eseguiamo la funzione all'inizializzazione di WordPress
add_action("init", "cd_add_persistent_school_role");

add_action("admin_menu", "cd_modify_school_admin_menu", 999);
function cd_modify_school_admin_menu()
{
	// Controlla se l'utente corrente ha il ruolo 'school'
	$user = wp_get_current_user();
	if (in_array("school", (array) $user->roles)) {
		// Rimuovi la voce di menu della libreria Elementor
		remove_menu_page("edit.php?post_type=elementor_library");
	}
}

add_action("wp_head", "custom_hide_style", 999);
function custom_hide_style()
{
	$user = wp_get_current_user();
	if (in_array("school", (array) $user->roles)) {
		remove_menu_page("edit.php?post_type=elementor_library"); ?>
       <style>
            #wp-admin-bar-wp-logo .ab-item,
            #wp-admin-bar-wp-logo .ab-sub-wrapper,
            #wp-admin-bar-root-default #wp-admin-bar-comments, #screen-meta-links, .notice-info, #wp-admin-bar-elementor_edit_page
             {
                display:none!important;
            }
        </style>

    <?php
	}
}

add_action("admin_menu", "cs_reorder_admin_menu", 999);
function cs_reorder_admin_menu(): void
{
	// --- NUOVO: CONTROLLO DEL RUOLO UTENTE ---
	// 1. Ottieni i dati dell'utente corrente.
	$user = wp_get_current_user();

	// 2. Controlla se l'utente ha il ruolo 'school'.
	//    Se non lo ha, interrompi subito la funzione.
	if (!in_array("school", (array) $user->roles, true)) {
		return;
	}
	// --- FINE CONTROLLO ---

	// Il resto della funzione viene eseguito SOLO se l'utente ha il ruolo 'school'.
	global $menu;

	$desired_order = [
		"index.php", // Home
		"edit.php", // Projects
		"cd-manage-view-project", // Manage News
		"edit.php?post_type=courses", // Courses
		"edit.php?post_type=page", // Pages
		"edit.php?post_type=seminars", // Seminars
		"edit.php?post_type=archiprix", // ArchiPrix - [2026-03-18 - Updated from option page slug to CPT slug]
		"edit.php?post_type=departments", // Departments
		"separator", // Separatore
		"upload.php", // Media
		"edit.php?post_type=students", // Students
		"options-general.php", // Settings
		"plugins.php", // Plugins
		"edit.php?post_type=dflip",
		// 'slug-di-analytics'          // <-- Ancora in attesa dello slug per Analytics
	];

	$menu_map = [];
	foreach ($menu as $item) {
		if (isset($item[2])) {
			$menu_map[$item[2]] = $item;
		}
	}

	$new_menu = [];
	$separator_count = 0;

	foreach ($desired_order as $slug) {
		if ($slug === "separator") {
			$separator_count++;
			$new_menu[] = [
				"",
				"read",
				"separator{$separator_count}",
				"",
				"wp-menu-separator",
			];
		} elseif (isset($menu_map[$slug])) {
			$new_menu[] = $menu_map[$slug];
		}
	}

	$menu = $new_menu;
}

function cs_add_all_caps_to_school_role()
{
	// Ottieni l'oggetto del ruolo 'school'
	$school_role = get_role("school");

	// Controlla che il ruolo esista per evitare errori
	if (!empty($school_role)) {
		// Aggiungi capability per "Impostazioni" (se non c'è già)
		if (!$school_role->has_cap("manage_options")) {
			$school_role->add_cap("manage_options");
		}
		// Aggiungi capability per "Plugin" (se non c'è già)
		if (!$school_role->has_cap("activate_plugins")) {
			$school_role->add_cap("activate_plugins");
		}
	}
}

// Eseguiamo la funzione direttamente per forzare l'aggiornamento.
cs_add_all_caps_to_school_role();
