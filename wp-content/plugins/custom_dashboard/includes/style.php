<?php
function my_custom_admin_style()
{

	wp_enqueue_style(
			'custom-admin-styles',                                  // 1. Handle (ID univoco) dello stile
			plugins_url( '/css/custom-dashboard.css', __FILE__ ),                 // 2. Percorso al file CSS
			array(),                                                    // 3. Dipendenze (nessuna in questo caso)
			'1.0.0',                                                    // 4. Versione del file (utile per il caching)
			'all'                                                       // 5. Media type (all, screen, print)
	);

}
add_action("admin_enqueue_scripts", "my_custom_admin_style");
add_action("login_enqueue_scripts", "my_custom_admin_style");



function custom_enqueue_custom_styles() {

		// Registra e accoda il nostro foglio di stile
		wp_enqueue_style(
				'custom-style',                                  // 1. Handle (ID univoco) dello stile
				plugins_url( '/css/custom.css', __FILE__ ),                 // 2. Percorso al file CSS
				array(),                                                    // 3. Dipendenze (nessuna in questo caso)
				'1.0.0',                                                    // 4. Versione del file (utile per il caching)
				'all'                                                       // 5. Media type (all, screen, print)
		);

}

// Aggancia la nostra funzione all'azione corretta di WordPress
add_action( 'wp_enqueue_scripts', 'custom_enqueue_custom_styles' );
