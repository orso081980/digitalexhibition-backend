<?php
if (!defined('ABSPATH')) exit; // Sicurezza

 // --- 1. DEFINIZIONE DELLE COSTANTI ---
 // Ho mantenuto tutte le costanti originali, incluse quelle per ACF.


 // --- 2. PAGINA DI AMMINISTRAZIONE ---

// Sposta Manage News prima di Audit Content via JS DOM (admin_head gira dopo il build del menu)
add_action('admin_head', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var manageNews = document.getElementById('toplevel_page_cd-manage-news');
        var auditContent = document.getElementById('toplevel_page_content-audit');
        if (manageNews && auditContent) {
            auditContent.parentNode.insertBefore(manageNews, auditContent);
        }
    });
    </script>
    <?php
});

add_action('admin_menu', function() {
		 add_menu_page(
				 'Manage News',           // 1. Titolo della pagina (tag <title>)
				 'Manage News',           // 2. Testo nel menu
				 'edit_posts',            // 3. Capability richiesta
				 'cd-manage-news',        // 4. Slug del menu (univoco)
				 'cd_render_manage_news_page', // 5. Funzione di callback per il rendering
				 'dashicons-visibility',  // 6. Icona
				 23                       // 7. Posizione (dopo Archiprix a 22)
		 );
 });

 // Funzione che disegna la pagina "Manage News"
 function cd_render_manage_news_page() {
		 ?>
		 <div class="wrap">
				 <h1>Manage News Section</h1>
				 <p>Toggle visibility and highlight status. Changes are saved automatically.</p>
				 <div id="cd-feedback-panel" style="padding:10px; margin: 10px 0; border-radius: 4px; display: none;"></div>

				 <?php
				 $shown_posts = get_posts([
				     'post_type'   => array('post', 'courses', 'archiprix', 'seminars'),
				     'numberposts' => -1,
				     'post_status' => array('publish', 'draft', 'pending', 'private'),
				     'meta_key'    => CD_META_KEY_SHOW_PAGE,
				     'meta_value'  => '1',
				     'orderby'     => 'title',
				     'order'       => 'ASC',
				 ]);
				 $shown_count = count($shown_posts);
				 $shown_titles = [];
				 foreach ($shown_posts as $sp) {
				     $t = $sp->post_title;
				     if (function_exists('get_field')) {
				         $grp = get_field(CD_ACF_GROUP_KEY_GENERAL_INFO, $sp->ID);
				         if (!empty($grp[CD_ACF_SUBFIELD_NAME_TITLE])) $t = $grp[CD_ACF_SUBFIELD_NAME_TITLE];
				     }
				     if (empty($t)) $t = '(no title)';
				     $shown_titles[] = esc_html($t);
				 }
				 ?>
				 <div style="display:flex; gap:16px; margin:14px 0 18px; align-items:stretch;">
				     <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:12px 18px; min-width:130px; text-align:center; display:flex; flex-direction:column; justify-content:center;">
				         <div style="font-size:38px; font-weight:700; line-height:1; color:#1d2327;"><?php echo $shown_count; ?></div>
				         <div style="margin-top:4px; font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.4px;">on homepage</div>
				     </div>
				     <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:10px 14px; flex:1; max-height:72px; overflow-y:auto;">
				         <span style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#888; margin-right:8px;">Shown:</span>
				         <?php if ($shown_titles): ?>
				             <?php foreach ($shown_titles as $title): ?>
				                 <span style="display:inline-block; background:#f0f0f0; border-radius:3px; font-size:12px; padding:2px 7px; margin:2px 3px 2px 0; color:#1d2327;"><?php echo $title; ?></span>
				             <?php endforeach; ?>
				         <?php else: ?>
				             <span style="color:#999; font-size:12px;">No articles selected yet.</span>
				         <?php endif; ?>
				     </div>
				 </div>

				 <table class="wp-list-table widefat fixed striped">
						 <thead>
								 <tr>
										 <th style="width: 40%;">Article Title</th>
										 <th>Type / Taxonomy</th>
										 <th>Year</th>
										 <th>Show on Homepage</th>
								 </tr>
						 </thead>
						 <tbody id="cd-news-list-body">
								 <?php
								 $all_posts = get_posts(['post_type' => array('post' , 'courses' , 'archiprix', 'seminars'), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => array('publish', 'draft', 'pending', 'private')]);
								 if ($all_posts) {
										 foreach ($all_posts as $p) {
												 // Logica per recuperare il titolo da ACF, se esiste
												 $display_title = $p->post_title;
												 if (function_exists('get_field')) {
														 $group = get_field(CD_ACF_GROUP_KEY_GENERAL_INFO, $p->ID);
														 if (!empty($group[CD_ACF_SUBFIELD_NAME_TITLE])) {
																 $display_title = $group[CD_ACF_SUBFIELD_NAME_TITLE];
														 }
												 }
												 if (empty($display_title)) $display_title = '(no title)';

												 $is_shown = get_post_meta($p->ID, CD_META_KEY_SHOW_PAGE, true);
												 $is_highlighted = get_post_meta($p->ID, CD_META_KEY_HIGHLIGHT, true);

												 // Taxonomy: usa la tassonomia principale per post_type
												 $tax_map = [
												 	'post'      => 'category',
												 	'courses'   => 'course_category',
												 	'seminars'  => 'seminar_category',
												 	'archiprix' => 'archiprix_category',
												 ];
												 $tax_slug = isset($tax_map[$p->post_type]) ? $tax_map[$p->post_type] : '';
												 $tax_terms = $tax_slug ? get_the_terms($p->ID, $tax_slug) : false;
												 $tax_label = (!is_wp_error($tax_terms) && !empty($tax_terms))
												 	? implode(', ', wp_list_pluck($tax_terms, 'name'))
												 	: '—';

												 // Year: tassonomia project_year (solo post e courses)
												 $year_terms = get_the_terms($p->ID, 'project_year');
												 $year_label = (!is_wp_error($year_terms) && !empty($year_terms))
												 	? implode(', ', wp_list_pluck($year_terms, 'name'))
												 	: '—';
												 ?>
												 <tr data-post-id="<?php echo esc_attr($p->ID); ?>">
														 <td>
																 <strong><?php echo esc_html($display_title); ?></strong>
																 <span style="color:#999; font-size:11px; display:block;"><?php echo esc_html($p->post_type); ?></span>
																 <div class="row-actions"><a href="<?php echo get_edit_post_link($p->ID); ?>" target="_blank">Modifica</a></div>
														 </td>
														 <td><?php echo esc_html($tax_label); ?></td>
														 <td><?php echo esc_html($year_label); ?></td>
														 <td>
																 <label class="cd-switch"><input type="checkbox" class="cd-option-toggle" data-option="show_page" <?php checked($is_shown, 1); ?>><span class="cd-slider"></span></label>
														 </td>
												 </tr>
												 <?php
										 }
								 } else {
										 echo '<tr><td colspan="3">No articles found.</td></tr>';
								 }
								 ?>
						 </tbody>
				 </table>
		 </div>
		 <?php
 }

 // --- 3. META BOX (per la pagina di modifica del singolo post) ---


 function cd_render_metabox_content($post) {
		 wp_nonce_field('cd_save_nonce_action', 'cd_nonce_field');
		 $show_page = get_post_meta($post->ID, CD_META_KEY_SHOW_PAGE, true);
		 $highlight = get_post_meta($post->ID, CD_META_KEY_HIGHLIGHT, true);

		 echo '<p><label><input type="checkbox" name="cd_show_page_field" value="1" ' . checked($show_page, 1, false) . ' /> Show on Homepage </label></p>';
		 echo '<p><label><input type="checkbox" name="cd_highlight_field" value="1" ' . checked($highlight, 1, false) . ' /> Highlight </label></p>';
		 echo '<p><small>Only one item can be highlighted.</small></p>';
 }

 // --- 4. SALVATAGGIO DATI (dal singolo post) ---
 add_action('save_post_post', function($post_id) {
		 if (!isset($_POST['cd_nonce_field']) || !wp_verify_nonce($_POST['cd_nonce_field'], 'cd_save_nonce_action')) return;
		 if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		 if (!current_user_can('edit_post', $post_id)) return;

		 // Salva "Show"
		 $show_page = isset($_POST['cd_show_page_field']) ? 1 : 0;
		 update_post_meta($post_id, CD_META_KEY_SHOW_PAGE, $show_page);

		 // Salva "Highlight" (con logica di unicità)
		 $is_highlighting = isset($_POST['cd_highlight_field']);
		 if ($is_highlighting) {
				 // Rimuove l'highlight da tutti gli altri post
				 global $wpdb;
				 $wpdb->update($wpdb->postmeta, ['meta_value' => 0], ['meta_key' => CD_META_KEY_HIGHLIGHT, 'post_id' => $post_id], ['%d'], ['%s', '%d']);
				 // Imposta l'highlight su questo
				 update_post_meta($post_id, CD_META_KEY_HIGHLIGHT, 1);
		 } else {
				 update_post_meta($post_id, CD_META_KEY_HIGHLIGHT, 0);
		 }
 }, 20);

 // --- 5. REST API (per il salvataggio dalla pagina "Manage News") ---
 add_action('rest_api_init', function () {
		 register_rest_route('cd/v1', '/update-meta', [
				 'methods' => 'POST',
				 'callback' => 'cd_rest_update_callback',
				 'permission_callback' => fn() => current_user_can('edit_posts'),
		 ]);
 });

 function cd_rest_update_callback(WP_REST_Request $request) {
		 $post_id = $request->get_param('post_id');
		 $option = $request->get_param('option_name');
		 $value = filter_var($request->get_param('option_value'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

		 if ($option === 'show_page') {
				 update_post_meta($post_id, CD_META_KEY_SHOW_PAGE, $value);
		 } elseif ($option === 'highlight') {
				 if ($value) { // Se sto attivando un highlight
						 // Rimuovo l'highlight da tutti gli altri post
						 global $wpdb;
						 $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = 0 WHERE meta_key = %s", CD_META_KEY_HIGHLIGHT));
				 }
				 update_post_meta($post_id, CD_META_KEY_HIGHLIGHT, $value);
		 } else {
				 return new WP_Error('invalid_option', 'Opzione non valida.', ['status' => 400]);
		 }

		 return new WP_REST_Response(['success' => true], 200);
 }

 // --- 6. ASSETS (CSS & JS per la pagina "Manage News") ---
 add_action('admin_enqueue_scripts', function($hook_suffix) {
		 if ($hook_suffix !== 'toplevel_page_cd-manage-news') return;

		 // Stile per gli interruttori (toggle switch)
		 echo '<style>.cd-switch{position:relative;display:inline-block;width:44px;height:24px}.cd-switch input{opacity:0;width:0;height:0}.cd-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;transition:.4s;border-radius:34px}.cd-slider:before{position:absolute;content:"";height:16px;width:16px;left:4px;bottom:4px;background-color:white;transition:.4s;border-radius:50%}input:checked+.cd-slider{background-color:#2196F3}input:checked+.cd-slider:before{transform:translateX(20px)}</style>';

		 // JavaScript per la comunicazione REST API
		 ?>
		 <script type="text/javascript">
				 document.addEventListener('DOMContentLoaded', () => {
						 const listBody = document.getElementById('cd-news-list-body');
						 if (!listBody) return;

						 listBody.addEventListener('change', e => {
								 if (!e.target.classList.contains('cd-option-toggle')) return;

								 const checkbox = e.target;
								 const row = checkbox.closest('tr');
								 const feedback = document.getElementById('cd-feedback-panel');

								 feedback.textContent = 'Salvataggio...';
								 feedback.style.display = 'block';
								 feedback.style.backgroundColor = '#f0f0f1';

								 const data = {
										 post_id: row.dataset.postId,
										 option_name: checkbox.dataset.option,
										 option_value: checkbox.checked
								 };

								 fetch('<?php echo esc_url_raw(rest_url('cd/v1/update-meta')); ?>', {
										 method: 'POST',
										 headers: {
												 'Content-Type': 'application/json',
												 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
										 },
										 body: JSON.stringify(data)
								 })
								 .then(res => res.json())
								 .then(result => {
										 if (result.success) {
												 feedback.textContent = 'Post ' + data.post_id + ' aggiornato!';
												 feedback.style.backgroundColor = '#d1e7dd';
												 // Se abbiamo attivato un highlight, deselezioniamo tutti gli altri
												 if (data.option_name === 'highlight' && data.option_value) {
															document.querySelectorAll('.cd-option-toggle[data-option="highlight"]').forEach(box => {
																 if (box !== checkbox) box.checked = false;
														 });
												 }
										 } else {
												 feedback.textContent = 'Errore: ' + (result.message || 'Sconosciuto.');
												 feedback.style.backgroundColor = '#f8d7da';
												 checkbox.checked = !checkbox.checked; // Annulla la modifica visiva
										 }
								 })
								 .catch(err => {
										 feedback.textContent = 'Errore di connessione: ' + err;
										 feedback.style.backgroundColor = '#f8d7da';
								 });
						 });
				 });
		 </script>
		 <?php
 });

 // --- 7. SHORTCODE ---
 // Mantenuto lo shortcode originale per mostrare il post in evidenza.
 add_shortcode("mostra_post_highlight", function($atts) {
		 ob_start();
		 // [2026-06-03 - Punto 4: immagine hero randomizzata tra tutti i post con "show on homepage"]
		 $args = [
				 "post_type" => "post",
				 "posts_per_page" => 1,
				 "orderby" => "rand",
				 "meta_key" => CD_META_KEY_SHOW_PAGE,
				 "meta_value" => "1",
		 ];
		 $query = new WP_Query($args);
		 if ($query->have_posts()) {
				 while ($query->have_posts()) {
						 $query->the_post();
						 echo '<div id="random-blok" class="random-blok-post">';
						 echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
						 if ( has_post_thumbnail() ) {
								 echo '<a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">';
								 the_post_thumbnail('large');
								 echo '</a>';
						 }
						 the_excerpt();
						 echo '</div>';
				 }
				 wp_reset_postdata();
		 }
		 return ob_get_clean();
 });

 // --- 8. FILTRO PER ELEMENTOR ---
 add_action( 'elementor/query/news_home', function( $query ) {
		 $meta_query = $query->get( 'meta_query' );
		 if ( ! is_array( $meta_query ) ) {
				 $meta_query = [];
		 }
		 $meta_query[] = [
				 'key'     => CD_META_KEY_SHOW_PAGE,
				 'value'   => 1,
				 'compare' => '=',
				 'type'    => 'NUMERIC',
		 ];
		 $query->set( 'meta_query', $meta_query );
		 $query->set( 'post_type', array( 'post', 'courses', 'archiprix', 'seminars' ) );
 }, 20 );
