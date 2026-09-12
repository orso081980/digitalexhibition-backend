<?php
/**
 * Modifiche del 2026-03-20 - custom_dashboard/includes/shortcode.php
 * - Raggruppamento per anno esteso a tutti i filtri tranne ALL (projects-page)
 * - Label anni sostituiti con Year 1, Year 2 ecc. (Year 1 = più recente)
 * - archiprix_projects shortcode: rimossa selezione manuale ACF, ora carica
 *   tutti i CPT archiprix pubblicati raggruppati per project_year
 *
 * Modifiche del 2026-03-20 - custom_dashboard/includes/switch-post-type.php
 * - Aggiunto archiprix alla lista dei post type switchabili e ai label
 */

if (!function_exists('mio_progetto_completo_shortcode')) {

    function mio_progetto_completo_shortcode($atts) {
        // Iniziamo a catturare tutto l'output HTML
        ob_start();
        // [DEBUG 2026-04-30] remove after verify
        echo '<!-- shortcode-version: 2026-04-30 -->';


        $project_timeline_html = ''; // Inizializziamo la variabile per sicurezza.
        $project_siblings      = []; // Inizializziamo per sicurezza (usato anche per cd-student-projects).

        $current_post_id = get_the_ID();

        if ( $current_post_id ) {
            // Rilevamento dinamico della tassonomia corretta.
            $current_post_type = get_post_type( $current_post_id );
            // [2026-03-18 - Added seminars and archiprix to taxonomy map]
            $taxonomy_map      = [ 'courses' => 'course_category', 'seminars' => 'seminar_category', 'archiprix' => 'archiprix_category' ];
            $taxonomy_to_query = isset( $taxonomy_map[ $current_post_type ] ) ? $taxonomy_map[ $current_post_type ] : 'category';

            // Unit: discriminante stabile tra edizioni (la categoria può cambiare)
            $unit_ids = wp_get_post_terms( $current_post_id, 'unit', ['fields' => 'ids'] );

            // Slug prefix: rimuove solo suffisso anno (4 cifre finali)
            // [2026-04-30-v2 - Fix: era /-\d+(-\d+)?$/ che rimuoveva anche il numero progressivo
            //   es. "project-structure-architecture-1-2024" → base "project-structure-architecture"
            //   causando falsi siblings tra corsi distinti con stesso anno. Ora rimuove solo -YYYY]
            $current_slug     = get_post_field( 'post_name', $current_post_id );
            $project_base_key = preg_replace( '/-\d{4}$/', '', $current_slug );

            // Leggi group_years e study_level del post corrente per confronto siblings
            $current_gi            = get_field( 'general_information', $current_post_id );
            $current_gi            = ( is_array( $current_gi ) && ! empty( $current_gi ) ) ? $current_gi[0] : [];
            $current_group_years   = ! empty( $current_gi['group_years'] )  ? (array) $current_gi['group_years']  : [];
            $current_study_levels  = ! empty( $current_gi['study_level'] )  ? wp_list_pluck( (array) $current_gi['study_level'], 'term_id' ) : [];
            sort( $current_group_years );
            sort( $current_study_levels );

            if ( ! empty( $project_base_key ) ) {
                // Filtra per unit se disponibile, altrimenti per categoria
                // project_year NON filtra: è la dimensione del carousel
                $tax_query_acf = [ 'relation' => 'AND' ];
                if ( ! empty( $unit_ids ) && ! is_wp_error( $unit_ids ) ) {
                    $tax_query_acf[] = [ 'taxonomy' => 'unit', 'field' => 'term_id', 'terms' => $unit_ids ];
                } elseif ( ! empty( $current_term_ids ) && ! is_wp_error( $current_term_ids ) ) {
                    $tax_query_acf[] = [ 'taxonomy' => $taxonomy_to_query, 'field' => 'term_id', 'terms' => $current_term_ids ];
                }

                $query_args = [
                    'post_type'      => $current_post_type,
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'tax_query'      => $tax_query_acf,
                ];
                $potential_siblings = get_posts( $query_args );

                // Filtro PHP: slug base identico + stessi group_years + stessi study_level
                // [2026-04-30 - Aggiunto confronto ACF per evitare falsi siblings tra corsi distinti]
                // [2026-04-30-DEBUG]
                echo '<!-- DEBUG base_key: ' . esc_html($project_base_key) . ' | slug: ' . esc_html($current_slug) . ' -->';
                $project_siblings = [];
                foreach ( $potential_siblings as $post_item ) {
                    $item_base = preg_replace( '/-\d{4}$/', '', $post_item->post_name );
                    echo '<!-- DEBUG sibling: ' . esc_html($post_item->post_name) . ' | item_base: ' . esc_html($item_base) . ' | match: ' . ($item_base === $project_base_key ? 'YES' : 'NO') . ' -->';
                    if ( $item_base !== $project_base_key ) continue;

                    $item_gi            = get_field( 'general_information', $post_item->ID );
                    $item_gi            = ( is_array( $item_gi ) && ! empty( $item_gi ) ) ? $item_gi[0] : [];
                    $item_group_years   = ! empty( $item_gi['group_years'] )  ? (array) $item_gi['group_years']  : [];
                    $item_study_levels  = ! empty( $item_gi['study_level'] )  ? wp_list_pluck( (array) $item_gi['study_level'], 'term_id' ) : [];
                    sort( $item_group_years );
                    sort( $item_study_levels );

                    if ( $item_group_years !== $current_group_years ) continue;
                    if ( $item_study_levels !== $current_study_levels ) continue;

                    $project_siblings[] = $post_item;
                }

                if ( count( $project_siblings ) > 0 ) {
                    // Preparazione dei dati per la timeline.
                    $timeline_data = [];
                    foreach ( $project_siblings as $post_item ) {
                        $terms = get_the_terms( $post_item->ID, 'project_year' );
                        if ( empty( $terms ) || is_wp_error( $terms ) ) continue;

                        $year_term_name = $terms[0]->name;
                        preg_match( '/^\d{4}/', $year_term_name, $year_match );
                        $display_year = ! empty( $year_match ) ? $year_match[0] : '';
                        if ( empty( $display_year ) ) continue;

                        // Deduplica per anno: il post corrente ha priorità, altrimenti il primo trovato
                        $year_key = (int) $display_year;
                        if ( ! isset( $timeline_data[ $year_key ] ) || $post_item->ID == $current_post_id ) {
                            $timeline_data[ $year_key ] = [
                                'sorting_year' => $year_key,
                                'post_id'      => $post_item->ID,
                                'label'        => $display_year
                            ];
                        }
                    }

                    if ( ! empty( $timeline_data ) ) {
                        // Ordina i dati per anno, decrescente.
                        usort( $timeline_data, function( $a, $b ) {
                            return $b['sorting_year'] <=> $a['sorting_year'];
                        });

                        // Costruzione dell'output HTML finale.
                        $total_items = count( $timeline_data );
                        $carousel_class = 'project-timeline-carousel' . ( $total_items > 5 ? ' has-navigation' : '' );

                        $timeline_output = "<div class='{$carousel_class}' role='region' aria-label='Timeline del progetto'>";
                        $timeline_output .= "<div class='carousel-viewport'><ul class='carousel-track'>";

                        foreach ( $timeline_data as $data ) {
                            $is_current = ( $data['post_id'] == $current_post_id );
                            $css_class  = $is_current ? ' class="current"' : '';
                            $aria_tag   = $is_current ? ' aria-current="page"' : '';
                            $permalink  = get_permalink( $data['post_id'] );
                            $timeline_output .= sprintf( '<li%s><a href="%s"%s>%s</a></li>', $css_class, esc_url( $permalink ), $aria_tag, esc_html( $data['label'] ) );
                        }

                        $timeline_output .= "</ul></div>";

                        if ( $total_items > 5 ) {
                            $timeline_output .= "<button class='carousel-button prev' aria-label='Anno precedente'><span aria-hidden='true'>&#x2190;</span></button>";
                            $timeline_output .= "<button class='carousel-button next' aria-label='Anno successivo'><span aria-hidden='true'>&#x2192;</span></button>";
                        }

                        $timeline_output .= "</div>";

                        // Assegniamo l'HTML generato alla nostra variabile.
                        $project_timeline_html = $timeline_output;
                    }
                }
            }
        }

        //======================================================================
        // PARTE 1: LOGICA PER LE INFORMAZIONI GENERALI (DA ACF)
        //======================================================================
        if (have_rows("general_information")): ?>
            <div class="acf-general-information-wrapper">
                <?php while (have_rows("general_information")): the_row(); ?>
                    <div class="entry-meta">
                        <span class="category-links" style="text-transform: uppercase;">
                            <?php
                            $output_parts = [];

                            // Parte 1: Categorie o Tassonomie — con suffisso CPT nel nome del termine
                            // [2026-03-18 - Added seminars and archiprix to taxonomy map for category display]
                            // [2026-04-23 - Appended CPT label to each term name]
                            $category_list = '';
                            $cpt_tax_map    = [ 'courses' => 'course_category', 'seminars' => 'seminar_category', 'archiprix' => 'archiprix_category' ];
                            $cpt_label_map  = [ 'post' => 'Project', 'courses' => 'Course', 'seminars' => 'Seminar', 'archiprix' => 'Archiprix' ];
                            $current_pt     = get_post_type();
                            $cpt_label      = isset( $cpt_label_map[ $current_pt ] ) ? $cpt_label_map[ $current_pt ] : '';

                            if ( 'seminars' === $current_pt ) {
                                $category_list = esc_html( $cpt_label );
                            } else {
                                if ( isset( $cpt_tax_map[ $current_pt ] ) ) {
                                    $terms_for_list = get_the_terms( get_the_ID(), $cpt_tax_map[ $current_pt ] );
                                } else {
                                    $terms_for_list = get_the_category();
                                }

                                if ( ! empty( $terms_for_list ) && ! is_wp_error( $terms_for_list ) ) {
                                    $term_links = [];
                                    foreach ( $terms_for_list as $t ) {
                                        $term_url     = get_term_link( $t );
                                        $display_name = esc_html( $t->name ) . ( $cpt_label ? ' ' . esc_html( $cpt_label ) : '' );
                                        $term_links[] = '<a href="' . esc_url( $term_url ) . '">' . $display_name . '</a>';
                                    }
                                    $category_list = implode( ', ', $term_links );
                                }
                            }

                            if ( ! empty( $category_list ) ) {
                                $output_parts[] = $category_list;
                            }

                            // Parte 2: Unit (da campo ACF)
                            $term_id = get_sub_field('unit');
                            if ( $term_id && ! get_sub_field('disable_unit') ) {
                                $term = get_term( $term_id, 'unit' );
                                if ( $term && ! is_wp_error( $term ) ) {
                                    $output_parts[] = esc_html( $term->slug );
                                }
                            }

                            // ANNOTAZIONE: Uniamo le parti trovate usando il separatore.
                            // Se l'array contiene solo un elemento, non verrà aggiunto nessun separatore.
                            // Se è vuoto, non stamperà nulla.
                            echo implode(' | ', $output_parts);
                            ?>
                        </span>

                    </div>

                    <?php
                    /**
                     * ANNOTAZIONE: Parte 2 - Inserimento della Timeline.
                     *
                     * Stampiamo qui il contenuto della variabile $project_timeline_html,
                     * che abbiamo preparato in precedenza. In questo modo, il carosello
                     * viene visualizzato dopo i metadati ma prima degli altri dettagli ACF.
                     */
                    echo $project_timeline_html;
                    ?>



                    <div class="acf-general-information-item">
                        <?php if ($description = get_sub_field("description")): ?>
                            <div class="description">
                                <p><?php echo wp_kses_post($description); ?></p>
                            </div>
                        <?php endif; ?>

                       <?php
                       $terms = get_sub_field('study_level');

                       if (!empty($terms) && is_array($terms)) :

                           $links_array = [];

                           foreach ($terms as $term) {
                               $links_array[] = sprintf(
                                   '<a href="%s">%s</a>',
                                   esc_url(get_term_link($term)),
                                   esc_html($term->name)
                               );
                           }
                       ?>
                           <strong> Type: </strong>
                           <?php echo implode(', ', $links_array); ?>

                       <?php
                       endif;
                       ?>

                        <?php if ( $teachers = get_sub_field( 'teachers' ) ) : ?>
                           <p> <strong> Teachers: </strong><?php echo esc_html( $teachers ); ?> </p>
                        <?php endif; ?>

                        <?php if ($coordination = get_sub_field("coordination")): ?>
                            <p><strong><?php esc_html_e("Coordination:", "your-text-domain"); ?></strong> <?php echo esc_html($coordination); ?></p>
                        <?php endif; ?>

                    <?php
                       if ( have_rows( 'students_list' ) ) :
                            $student_names = array();
                            while ( have_rows( 'students_list' ) ) : the_row();
                               if ( $name_student = get_sub_field( 'name_student' ) ) {
                                   $student_names[] = esc_html( $name_student );
                                }
                            endwhile;
                            if ( ! empty( $student_names ) ) : ?>
                               <div class="group-student">
                                    <p> <strong> Students: </strong>
                                        <?php
                                        echo implode( ', ', $student_names );
                                       ?>
                                    </p>
                                </div>
                            <?php endif;
                        endif;
                        ?>


                       <?php if ( have_rows( 'other_infomation' ) ) : ?>
                           <div class="group-info">
                           <p>

                           <?php while ( have_rows( 'other_infomation' ) ) :
                               the_row(); ?>

             <?php if ( $other_info = get_sub_field( 'other_info' ) ) : ?>
              <strong>   <?php echo esc_html( $other_info ); ?>: </strong>
                               <?php endif; ?>

                 <?php if ( $other_list = get_sub_field( 'other_list' ) ) : ?>
                                   <?php echo esc_html( $other_list ); ?> <br>
                               <?php endif; ?>

                           <?php endwhile; ?>
                           </p>
                           </div>
                       <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                <style>
                    .acf-general-information-item .description {margin-top: 20px; margin-bottom: 20px;}
                  
                </style>
            </div>
        <?php else:
            echo "";
        endif;

        // Griglia progetti studenti: usa i siblings già calcolati (stessa unit/categoria + slug prefix)
        // Filtra ulteriormente per stesso project_year del post corrente
        $current_year_terms = get_the_terms( $current_post_id, 'project_year' );
        $current_year_ids   = ( ! empty( $current_year_terms ) && ! is_wp_error( $current_year_terms ) )
            ? wp_list_pluck( $current_year_terms, 'term_id' )
            : array();

        if ( ! empty( $project_siblings ) ) {

            $sibling_ids = array_map( function( $p ) { return $p->ID; }, $project_siblings );
            $sibling_ids = array_diff( $sibling_ids, array( $current_post_id ) );

            if ( ! empty( $sibling_ids ) ) {
                $query_args = array(
                    'post_type'      => $current_post_type,
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'post__in'       => array_values( $sibling_ids ),
                );

                // Restringe allo stesso anno se disponibile
                if ( ! empty( $current_year_ids ) ) {
                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'project_year',
                            'field'    => 'term_id',
                            'terms'    => $current_year_ids,
                        ),
                    );
                }

                $students_query = new WP_Query( $query_args );
            }
        }

        if ( ! empty( $students_query ) ) {

            if ( $students_query->have_posts() ) : ?>
                <div class="cd-student-projects">
                    <?php while ( $students_query->have_posts() ) : $students_query->the_post(); ?>
                        <article id="sp-<?php the_ID(); ?>" class="cd-student-card">
                            <a href="<?php the_permalink(); ?>" class="cd-student-card-link">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="cd-student-thumbnail">
                                        <?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="cd-student-info">
                                    <h3 class="cd-student-title"><?php the_title(); ?></h3>
                                    <?php $excerpt = get_the_excerpt();
                                    if ( $excerpt ) : ?>
                                        <p class="cd-student-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <style>
                    .cd-student-projects {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 20px;
                        margin-top: 40px;
                    }
                    .cd-student-card { background: #fff; }
                    .cd-student-card-link { display: block; text-decoration: none; color: inherit; }
                    .cd-student-thumbnail img { width: 100%; height: 200px; object-fit: cover; display: block; }
                    .cd-student-info { padding: 15px; }
                    .cd-student-title { font-size: 16px; margin: 0 0 8px; line-height: 1.3; }
                    .cd-student-excerpt {
                        font-size: 13px; color: #666; margin: 0; line-height: 1.5;
                        display: -webkit-box; -webkit-line-clamp: 3;
                        -webkit-box-orient: vertical; overflow: hidden;
                    }
                    @media (max-width: 992px) { .cd-student-projects { grid-template-columns: repeat(2, 1fr); } }
                    @media (max-width: 600px)  { .cd-student-projects { grid-template-columns: 1fr; } }
                </style>
            <?php endif;
        }

        // Restituisce tutto l'HTML catturato
        return ob_get_clean();
    }

    // Registra il NUOVO e UNICO shortcode
    add_shortcode('mostra_info_generali', 'mio_progetto_completo_shortcode');
}

// Inietta il project-timeline-carousel nei post Elementor via wp_footer
function cd_inject_timeline_elementor() {
    if ( ! is_singular( array( 'post', 'courses', 'seminars', 'archiprix' ) ) ) return; // [2026-03-18 - Extended to courses, seminars, archiprix]

    $post_id = get_the_ID();

    // Skip se usa già [mostra_info_generali] (già gestito dallo shortcode)
    $content = get_post_field( 'post_content', $post_id );
    if ( has_shortcode( $content, 'mostra_info_generali' ) ) return;

    // Richiede template Elementor
    $elementor_data = get_post_meta( $post_id, '_elementor_data', true );
    if ( empty( $elementor_data ) ) return;

    // Match siblings: stessa category (o taxonomy CPT) + unit + project_year + slug prefix identico
    // [2026-04-30 - Fix: regex cambiato da /-\d+(-\d+)?$/ a /-\d{4}$/ per non rimuovere numero progressivo corso]
    $current_slug     = get_post_field( 'post_name', $post_id );
    $project_base_key = preg_replace( '/-\d{4}$/', '', $current_slug );

    // Taxonomy categoria dinamica per post type
    $post_type    = get_post_type( $post_id );
    $cat_tax_map  = array( 'courses' => 'course_category', 'seminars' => 'seminar_category', 'archiprix' => 'archiprix_category' );
    $cat_taxonomy = isset( $cat_tax_map[ $post_type ] ) ? $cat_tax_map[ $post_type ] : 'category';

    $cat_ids  = wp_get_post_terms( $post_id, $cat_taxonomy,    array( 'fields' => 'ids' ) );
    $unit_ids = wp_get_post_terms( $post_id, 'unit',           array( 'fields' => 'ids' ) );
    $year_ids = wp_get_post_terms( $post_id, 'project_year',   array( 'fields' => 'ids' ) );

    if ( empty( $project_base_key ) ) return;

    // Filtra per unit se disponibile (stabile tra edizioni), altrimenti per categoria
    // project_year NON filtra: è la dimensione del carousel
    $tax_query = array( 'relation' => 'AND' );
    if ( ! empty( $unit_ids ) && ! is_wp_error( $unit_ids ) ) {
        $tax_query[] = array( 'taxonomy' => 'unit', 'field' => 'term_id', 'terms' => $unit_ids );
    } elseif ( ! empty( $cat_ids ) && ! is_wp_error( $cat_ids ) ) {
        $tax_query[] = array( 'taxonomy' => $cat_taxonomy, 'field' => 'term_id', 'terms' => $cat_ids );
    } else {
        return; // nessun discriminante disponibile
    }

    $potential_siblings = get_posts( array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => $tax_query,
    ) );

    $project_siblings = array();
    foreach ( $potential_siblings as $post_item ) {
        // [2026-04-30 - Fix: stesso regex corretto, solo anno 4 cifre]
        $item_base = preg_replace( '/-\d{4}$/', '', $post_item->post_name );
        if ( $item_base === $project_base_key ) {
            $project_siblings[] = $post_item;
        }
    }

    if ( empty( $project_siblings ) ) return;

    // Costruisce i dati per la timeline
    // Anno: prima prova dalla taxonomy project_year, poi dallo slug (post Elementor)
    $timeline_data = array();
    foreach ( $project_siblings as $post_item ) {
        $display_year = '';
        $terms = get_the_terms( $post_item->ID, 'project_year' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            preg_match( '/^\d{4}/', $terms[0]->name, $year_match );
            $display_year = ! empty( $year_match ) ? $year_match[0] : '';
        }
        if ( empty( $display_year ) ) {
            preg_match( '/(\d{4})/', $post_item->post_name, $slug_year_match );
            $display_year = ! empty( $slug_year_match[1] ) ? $slug_year_match[1] : '';
        }
        if ( empty( $display_year ) ) continue;
        $timeline_data[] = array(
            'sorting_year' => (int) $display_year,
            'post_id'      => $post_item->ID,
            'label'        => $display_year,
        );
    }

    if ( empty( $timeline_data ) ) return;

    usort( $timeline_data, function( $a, $b ) {
        return $b['sorting_year'] <=> $a['sorting_year'];
    });

    $total_items    = count( $timeline_data );
    $carousel_class = 'project-timeline-carousel' . ( $total_items > 5 ? ' has-navigation' : '' );

    $html  = "<div class='{$carousel_class}' role='region' aria-label='Timeline del progetto'>";
    $html .= "<div class='carousel-viewport'><ul class='carousel-track'>";
    foreach ( $timeline_data as $data ) {
        $is_current = ( $data['post_id'] == $post_id );
        $css_class  = $is_current ? ' class="current"' : '';
        $aria_tag   = $is_current ? ' aria-current="page"' : '';
        $html      .= sprintf(
            '<li%s><a href="%s"%s>%s</a></li>',
            $css_class,
            esc_url( get_permalink( $data['post_id'] ) ),
            $aria_tag,
            esc_html( $data['label'] )
        );
    }
    $html .= "</ul></div>";
    if ( $total_items > 5 ) {
        $html .= "<button class='carousel-button prev' aria-label='Anno precedente'><span aria-hidden='true'>&#x2190;</span></button>";
        $html .= "<button class='carousel-button next' aria-label='Anno successivo'><span aria-hidden='true'>&#x2192;</span></button>";
    }
    $html .= "</div>";

    $html_json = wp_json_encode( $html );
    ?>
    <script>
    (function() {
        var carousel = <?php echo $html_json; ?>;
        var targetWrap = null;

        // Tentativo 1: inner-section Elementor con un heading (layout a colonne)
        document.querySelectorAll('.elementor-inner-section').forEach(function(innerSection) {
            if ( targetWrap ) return;
            if ( innerSection.querySelectorAll('.elementor-heading-title').length > 0 ) {
                targetWrap = innerSection.querySelector('.elementor-column .elementor-widget-wrap');
            }
        });

        // Tentativo 2: prima sezione Elementor con heading (template a sezione intera, senza inner-section)
        if ( ! targetWrap ) {
            document.querySelectorAll('.elementor-section, .e-con').forEach(function(section) {
                if ( targetWrap ) return;
                if ( section.querySelectorAll('.elementor-heading-title').length > 0 ) {
                    var wrap = section.querySelector('.elementor-widget-wrap, .e-con-inner');
                    if ( wrap ) targetWrap = wrap;
                }
            });
        }

        // Tentativo 3: fallback generico - primo heading h1/h2 nel contenuto della pagina
        if ( ! targetWrap ) {
            var heading = document.querySelector('.entry-content h1, .entry-content h2, article h1, article h2');
            if ( heading && heading.parentNode ) targetWrap = heading.parentNode;
        }

        if ( ! targetWrap ) return;

        // Evita duplicati
        if ( targetWrap.querySelector('.project-timeline-carousel') ) return;

        var wrapper = document.createElement('div');
        wrapper.innerHTML = carousel;
        var carouselNode = wrapper.firstChild;

        // Inserisce dopo il primo heading trovato nel targetWrap, oppure in append
        var firstHeading = targetWrap.querySelector('.elementor-heading-title, h1, h2');
        if ( firstHeading && firstHeading.parentNode && firstHeading.parentNode !== targetWrap ) {
            firstHeading.parentNode.after( carouselNode );
        } else if ( firstHeading ) {
            firstHeading.after( carouselNode );
        } else {
            targetWrap.appendChild( carouselNode );
        }

        // Inizializza prev/next se presenti
        var prevBtn = carouselNode.querySelector('.carousel-button.prev');
        var nextBtn = carouselNode.querySelector('.carousel-button.next');
        if ( prevBtn && nextBtn ) {
            var track    = carouselNode.querySelector('.carousel-track');
            var viewport = carouselNode.querySelector('.carousel-viewport');
            var items    = Array.from( track.children );
            var index    = 0;

            var maxOffset = function() {
                return Math.max( 0, track.scrollWidth - viewport.offsetWidth );
            };
            var updateTrack = function() {
                var mo = maxOffset();
                if ( mo === 0 ) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    track.style.transform = 'translateX(0px)';
                    return;
                }
                var targetOffset = Math.min( items[index].offsetLeft, mo );
                track.style.transform = 'translateX(-' + targetOffset + 'px)';
                prevBtn.disabled = index === 0;
                nextBtn.disabled = targetOffset >= mo;
            };

            prevBtn.addEventListener('click', function() {
                if ( index > 0 ) { index--; updateTrack(); }
            });
            nextBtn.addEventListener('click', function() {
                if ( index < items.length - 1 && items[index].offsetLeft < maxOffset() ) { index++; updateTrack(); }
            });

            updateTrack();
        }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'cd_inject_timeline_elementor' );

// Riscrive i link di categoria nei post singoli verso la pagina listing con ?filter=
// Lo slug grezzo viene estratto dall'href e passato come ?cd_filter= alla listing page.
// Nella listing page, l'auto-trigger fa match fuzzy contro i data-slug dei bottoni.
// Per aggiungere un CPT: inserire una voce in $cpt_map.
function cd_inject_filter_link_script() {
    if ( ! is_singular() ) return;
    $post_type = get_post_type();

    // url_segment  : segmento dell'href che precede lo slug del termine
    // listing_path : path della pagina listing (null = usa il path dell'href originale)
    $cpt_map = array(
        'post'    => array( 'url_segment' => 'category', 'listing_path' => '/projects/' ),
        'courses' => array( 'url_segment' => 'course-category', 'listing_path' => '/courses/' ),
        'seminars'  => array( 'url_segment' => 'seminar-category', 'listing_path' => '/seminars/', 'filterable' => false ),
        // 'archiprix' => array( 'url_segment' => 'archiprix-category', 'listing_path' => '/archiprix/' ),
        // TODO: per attivare seminars/archiprix (o nuovi CPT):
        //   1. Sblocca la riga sopra con il listing_path corretto
        //   2. Verifica che .category-links a esista nel singolo post del CPT
        //   3. Verifica i data-slug dei bottoni filtro nella listing page
        //   4. Controlla che il token-match funzioni: split('-'), token > 3 chars
        //      es. 'bachelor-seminar' → token 'bachelor' deve matchare data-slug dei bottoni
        //   5. Se la listing page usa un shortcode diverso da projects/corsi-filtri-integrato,
        //      aggiungere il blocco auto-trigger ?cd_filter= anche lì
    );

    if ( ! isset( $cpt_map[ $post_type ] ) ) return;
    $cpt_config = $cpt_map[ $post_type ];

    $unit_terms = get_the_terms( get_queried_object_id(), 'unit' );
    $unit_slug  = ( $unit_terms && ! is_wp_error( $unit_terms ) ) ? $unit_terms[0]->slug : '';
    ?>
    <script>
    (function() {
        var cfg      = <?php echo wp_json_encode( $cpt_config ); ?>;
        var unitSlug = <?php echo wp_json_encode( $unit_slug ); ?>;

        document.querySelectorAll('.elementor-heading-title a, .category-links a').forEach(function(link) {
            var parsedHref = new URL( link.href, window.location.href );
            if ( parsedHref.pathname.indexOf( '/' + cfg.url_segment + '/' ) === -1 ) return;

            var re = new RegExp( '\\/' + cfg.url_segment.replace(/-/g, '\\-') + '\\/([^\\/]+)' );
            var match = parsedHref.pathname.match( re );
            var slug = match ? match[1] : parsedHref.searchParams.get( 'cd_filter' );
            if ( ! slug ) return;

            var dest = new URL( cfg.listing_path, window.location.origin );
            if ( cfg.filterable !== false ) dest.searchParams.set( 'cd_filter', slug );
            if ( unitSlug ) dest.searchParams.set( 'cd_unit', unitSlug );
            link.href = dest.toString();
        });
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'cd_inject_filter_link_script' );

if (!function_exists("acf_list_pdf_shortocde")) {
    function acf_list_pdf_shortocde($atts)
    {
        // Inizia il buffering dell'output
        ob_start();

        // Attributi predefiniti (se necessari in futuro)
        // $atts = shortcode_atts( array(
        // 	'some_attribute' => 'default_value',
        // ), $atts, 'acf_list_pdf' );

        if (have_rows("pdf_post")): ?>

<style>
    .acf-pdf-list-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 40px;
        margin-bottom: 40px;
        margin-left: -10px;
        margin-right: -10px;
    }

    .acf-pdf-list-item {
        box-sizing: border-box; /* Include padding e border nel calcolo della larghezza/altezza */
        width: 50%;
        padding: 10px;
    }

    @media (max-width: 768px) {
        .acf-pdf-list-item {
            width: 100%;
        }
    }
</style>
            <div class="acf-pdf-list-container">
                <?php while (have_rows("pdf_post")):
                    the_row();
                    $nome_pdf = get_sub_field("nome_pdf");
                    $pdf_url  = get_sub_field("pdf_file"); // ACF File field, return_format=url — already an R2 URL once offloaded
                    if (!$pdf_url) {
                        continue; // nothing to show for this row yet
                    }
                    ?>
                    <div class="acf-pdf-list-item">
                        <?php if ($nome_pdf): ?>
                            <div class="acf-pdf-label"><?php echo esc_html($nome_pdf); ?></div>
                        <?php endif; ?>
                        <figure class="acf-pdf-item" data-pdf="<?php echo esc_url($pdf_url); ?>">
                            <a class="acf-pdf-open" href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener">
                                <?php esc_html_e('Open PDF', 'custom_dashboard'); ?>
                            </a>
                        </figure>
                    </div><?php
                endwhile; ?>
            </div><?php else: ?>
            <?php endif;

        // Termina il buffering e restituisci il contenuto catturato
        $output = ob_get_clean();
        return $output;
    }
}
add_shortcode("acf_list_pdf", "acf_list_pdf_shortocde");

function cd_video_gallery_shortcode()
{
    ob_start();
    echo '<style type="text/css">
        .cd-video-gallery-wrapper {
            display: grid;
            grid-template-columns: repeat(2, [col] 50%);
            grid-gap: 30px;
        }
        .cd-video-gallery-wrapper .embed-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            max-width: 100%;
        }

        .embed-container iframe,
        .embed-container object,
        .embed-container embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
    ';
    if (have_rows("list_video")):
        echo '<div class="cd-video-gallery-wrapper">';
        while (have_rows("list_video")):
            the_row();
            echo '<div class="embed-container">';
            the_sub_field("embed_video");
            echo "</div>";
        endwhile;

        echo "</div>";
    else:
    endif;

    // Cattura l'output e pulisci il buffer.
    $output = ob_get_clean();

    // Restituisci l'HTML raccolto.
    return $output;
}
add_shortcode("cd_video_gallery", "cd_video_gallery_shortcode");

/**
 * Registra lo shortcode per il carosello.
 */
add_action("init", "cs_register_carousel_shortcode");
function cs_register_carousel_shortcode(): void
{
    add_shortcode("mio_carosello_acf", "cs_render_carousel_shortcode");
}

/**
 * Renderizza l'HTML dello shortcode e imposta un flag per caricare gli asset.
 *
 * @param array $atts Attributi dello shortcode.
 * @return string HTML del carosello.
 */
function cs_render_carousel_shortcode($atts): string
{
    if (!function_exists("get_field") || !have_rows("block_carousel")) {
        return "";
    }

    $GLOBALS["cs_carousel_shortcode_used"] = true;

    ob_start();

    while (have_rows("block_carousel")):

        the_row();

        $instance_id = "cs-carousel-" . uniqid();
        ?>
         <div id="<?php echo esc_attr(
             $instance_id
         ); ?>" class="cs-carousel-component">
             <?php if (
                 $content_carousel = get_sub_field("content_carousel")
             ): ?>
                 <div class="cs-carousel__content">
                     <?php echo wp_kses_post($content_carousel); ?>
                 </div>
             <?php endif; ?>

             <?php if ($carousel_images = get_sub_field("carousel_images")): ?>
                 <div class="cs-carousel__swiper-container">
                     <div class="swiper">
                         <div class="swiper-wrapper">
                            <?php foreach ($carousel_images as $image): ?>
                                <a href="<?php echo esc_url($image["url"]); ?>"
                                   class="swiper-slide"
                                   data-elementor-open-lightbox="yes"
                                   data-elementor-lightbox-slideshow="<?php echo esc_attr($instance_id); ?>">
                                    <img src="<?php echo esc_url($image["sizes"]["large"]); ?>" alt="<?php echo esc_attr($image["alt"]); ?>" loading="lazy" />
                                </a>
                            <?php endforeach; ?>
                         </div>
                         <div class="swiper-pagination"></div>
                         <div class="swiper-button-prev"></div>
                         <div class="swiper-button-next"></div>
                     </div>
                 </div>
             <?php endif; ?>
         </div>
         <?php
    endwhile;

    return ob_get_clean();
}

/**
 * Stampa CSS e JS nel footer se lo shortcode è stato usato.
 */
add_action("wp_footer", "cs_print_carousel_assets");
function cs_print_carousel_assets(): void
{
    if (empty($GLOBALS["cs_carousel_shortcode_used"])) {
        return;
    } 
    wp_enqueue_style('swiper');    // handle registrato da Elementor (v8)
    wp_enqueue_style('e-swiper');  // stili frecce/navigazione lightbox, come in produzione
    wp_enqueue_script('swiper');   // niente più CDN v11
    ?>

     <style id="cs-carousel-css">
        .elementor-lightbox,
        .cs-carousel-component {
          --swiper-preloader-color: #000;
        }
         .cs-carousel-component { display: flex; flex-wrap: wrap; align-items: center; gap: 2rem; margin-bottom: 2.5rem; }
         .cs-carousel__content { flex: 1 1 300px; font-size: 13px; font-weight: 300; font-style: normal; line-height: 1.6em; letter-spacing: 0.1px; color: #000000; }
         .cs-carousel__swiper-container { flex: 1 1 50%; min-width: 0; }
         /* MODIFICA: Ora la slide è un link, manteniamo l'aspetto corretto */
         .cs-carousel__swiper-container .swiper-slide { display: block; aspect-ratio: 450 / 300; cursor: pointer; }
         .cs-carousel__swiper-container .swiper-slide img { display: block; width: 100%; height: 100%; object-fit: cover; }
         .cs-carousel__swiper-container .swiper-button-next, .cs-carousel__swiper-container .swiper-button-prev { color: #000; }
         .cs-carousel__swiper-container .swiper-button-next::after, .cs-carousel__swiper-container .swiper-button-prev::after { font-size: 22px; font-weight: bold; }
         .cs-carousel__swiper-container .swiper-pagination-bullet { background: #000; opacity: 0.2; }
         .cs-carousel__swiper-container .swiper-pagination-bullet-active { background: #000; opacity: 1; }
         @media (max-width: 767px) { .cs-carousel-component { flex-direction: row; } }
     </style>

     <script id="cs-carousel-js">
     (function() {
         'use strict';

         function initCsCarousels() {
             if (typeof Swiper === 'undefined') {
                 console.error('Swiper library is not loaded.');
                 return;
             }

             const carousels = document.querySelectorAll('.cs-carousel-component:not([data-initialized="true"])');

             carousels.forEach((carousel) => {
                 carousel.setAttribute('data-initialized', 'true');

                 const swiperContainer = carousel.querySelector('.swiper');
                 if (!swiperContainer) return;


                 const swiperInstance = new Swiper(swiperContainer, {
                     loop: true,
                     slidesPerView: 1,
                     spaceBetween: 10,
                     pagination: { el: swiperContainer.querySelector('.swiper-pagination'), clickable: true },
                     navigation: { nextEl: swiperContainer.querySelector('.swiper-button-next'), prevEl: swiperContainer.querySelector('.swiper-button-prev') },
                     breakpoints: { 768: { slidesPerView: 2, spaceBetween: 20 } },
                     // NOTA: La vecchia logica 'on.click' è stata rimossa.
                 });
                 carousel.querySelectorAll('.swiper-slide-duplicate').forEach(function (dup) {
                     dup.removeAttribute('data-elementor-open-lightbox');
                     dup.removeAttribute('data-elementor-lightbox-slideshow');
                     dup.addEventListener('click', function (e) {
                         e.preventDefault();
                         e.stopPropagation();
                         var idx = dup.getAttribute('data-swiper-slide-index');
                         var orig = carousel.querySelector('.swiper-slide:not(.swiper-slide-duplicate)[data-swiper-slide-index="' + idx + '"]');
                         if (orig) orig.click();
                     }, true);
                 });
                 // --- NUOVA INIZIALIZZAZIONE LIGHTBOX (INDIPENDENTE) ---
               
             });
         }

         window.addEventListener('load', initCsCarousels);

         if (typeof jQuery !== 'undefined') {
             jQuery(document).ready(initCsCarousels);
         }

         if (typeof elementorFrontend !== 'undefined' && typeof elementorFrontend.hooks !== 'undefined') {
             elementorFrontend.hooks.addAction('frontend/element_ready/global', function() {
                 setTimeout(initCsCarousels, 100);
             });
         }

     })();
     </script>
     <?php
}

add_shortcode('corsi_masonry_integrato', 'crea_shortcode_corsi_integrato');
function crea_shortcode_corsi_integrato() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('masonry');
    wp_enqueue_script('imagesloaded', 'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js', array('jquery', 'masonry'), '5.0.0', true);

    ob_start();

    $course_categories = get_terms(array('taxonomy' => 'course_category', 'hide_empty' => true));
    $unit_terms = get_terms(array('taxonomy' => 'unit', 'hide_empty' => true));
    ?>

    <style>
        /* Mantengo i tuoi stili originali con l'aggiunta della transizione fluida */
        .filtri-container { text-align: center; margin-bottom: 0px; display: flex; font-weight: 400; flex-wrap: wrap; margin-left: 147px; position: relative; top: -60px; align-items: center; }
        .filtro-btn { padding: 4px 10px; margin: 5px; font-size: 14px; font-weight: 400; cursor: pointer; transition: background-color 0.3s, color 0.3s; border-radius: 0px; background-color: transparent; color: #000; position: relative; display: flex; flex-direction: column; border: none; letter-spacing: 0px; }
        button.filtro-btn::after { content:''; height: 3px; left: 0; width: 100%; transform: scale(1); background-color: #fff; position: relative; transition: background-color 0.3s; }
        button.filtro-btn:hover::after, .filtro-btn.active::after { background-color: var(--e-global-color-2c056159); }
        .filtro-btn:hover, .filtro-btn.active { color: #000!important; background-color: #fff!important; }

        #corsi-grid-integrato { margin: 0 auto; transition: opacity 0.3s ease; }
        .grid-sizer-integrato { width: 32.1212%; }
        .grid-item-integrato { width: 32.1212%; margin-bottom: 20px; background: #fff; }
        .year-group-wrapper { width: 100%; margin-bottom: 40px; }
        .year-group-header { font-size: 13px; font-weight: 400; color: #666; padding: 8px 0; letter-spacing: 0.5px; }
        #corsi-grid-integrato .year-subgrid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; position: static; }
        #corsi-grid-integrato .year-subgrid .grid-sizer-integrato { display: none; }
        #corsi-grid-integrato .year-subgrid .grid-item-integrato { width: auto; margin-bottom: 0; position: static; }
        #corsi-grid-integrato .year-subgrid .course-thumbnail img { width: 100%; height: 264.7px; object-fit: cover; display: block; }
        @media (max-width: 800px) { #corsi-grid-integrato .year-subgrid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { #corsi-grid-integrato .year-subgrid { grid-template-columns: 1fr; } }

        /* ALL: CSS grid — righe allineate */
        #corsi-grid-integrato.css-grid-mode {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            position: static !important;
            height: auto !important;
            margin-top: -20px;
        }
        #corsi-grid-integrato.css-grid-mode .grid-item-integrato {
            position: static !important;
            width: auto !important;
            top: auto !important;
            left: auto !important;
            margin-bottom: 0;
        }
        #corsi-grid-integrato.css-grid-mode .grid-sizer-integrato { display: none; }
        #corsi-grid-integrato.css-grid-mode .project-thumbnail img,
        #corsi-grid-integrato.css-grid-mode .course-thumbnail img { width: 100%; height: 264.7px; object-fit: cover; display: block; }

        @media (max-width: 800px) {
            #corsi-grid-integrato.css-grid-mode { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            #corsi-grid-integrato.css-grid-mode { grid-template-columns: 1fr; }
        }

        /* Safety net: con year groups il container non deve mai stare in css-grid-mode */
        #corsi-grid-integrato.css-grid-mode:has(.year-group-wrapper) { display: block !important; }

    </style>

    <div id="corsi-masonry-container-integrato">
        <div id="corsi-filtri-integrato" class="filtri-container">
            <?php if (!empty($course_categories)) : ?>
                <button class="filtro-btn active" data-slug="all">ALL</button>
                <?php foreach ($course_categories as $term) : ?>
                    <?php if ($term->slug === 'uncategorized') continue; ?>
                    <button class="filtro-btn" data-slug="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></button>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="filtri-wrapper">
                <button id="unit-filtro-toggle">
                   <svg width="26" height="16" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="5" y1="15" x2="21" y2="15" stroke="black" stroke-width="2"/>
                        <line x1="2" y1="8" x2="24" y2="8" stroke="black" stroke-width="2"/>
                        <line y1="1" x2="26" y2="1" stroke="black" stroke-width="2"/>
                   </svg>
   <span> Units </span>
                </button>

                <div id="unit-filtri-container">
                    <?php foreach ($unit_terms as $term) : ?>
                        <label><input type="checkbox" class="unit-filtro-checkbox" value="<?php echo esc_attr($term->slug); ?>"> <?php echo esc_html($term->name); ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="corsi-grid-integrato">
            <div class="grid-sizer-integrato"></div>
            <div class="loader-container-integrato"><div class="loader-integrato"></div></div>
            <?php
            // Caricamento iniziale dei corsi
            filtra_corsi_callback_integrato(true);
            ?>
        </div>
    </div>

    <script>
    (function($) {
        $(document).ready(function() {
            var $grid = $('#corsi-grid-integrato');
            var loader = $('.loader-container-integrato');
            var ajaxRequest;

            function switchToGridCorsi() {
                if ($grid.data('masonry')) { $grid.masonry('destroy'); }
                $grid.css({ position: '', height: '' });
                $grid.find('.grid-item-integrato').css({ position: '', top: '', left: '', width: '' });
                $grid.addClass('css-grid-mode');
            }

            function switchToMasonryCorsi() {
                $grid.removeClass('css-grid-mode');
                $grid.css({ position: 'relative' });
            }

            // Init caricamento iniziale (ALL) - skip se nel frattempo un filtro ha caricato year groups
            $grid.imagesLoaded(function() {
                if ($grid.find('.year-group-wrapper').length === 0) { switchToGridCorsi(); }
            });

            function fetchCourses() {
                if (ajaxRequest) ajaxRequest.abort();

                var category = $('#corsi-filtri-integrato .filtro-btn.active').data('slug');
                var units = $('.unit-filtro-checkbox:checked').map(function() { return $(this).val(); }).get();

                ajaxRequest = $.ajax({
                    url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
                    type: 'POST',
                    data: {
                        action: 'filtra_corsi_integrato',
                        category: category,
                        units: units,
                        security: '<?php echo wp_create_nonce("corsi-nonce-integrato"); ?>'
                    },
                    beforeSend: function() {
                        loader.show();
                        $grid.css('opacity', '0.4');
                    },
                    success: function(response) {
                        var isAll = (category === 'all' || !category);
                        if ($grid.data('masonry')) { $grid.masonry('destroy'); }
                        $grid.find('.grid-item-integrato, .year-group-wrapper, .no-results-integrato').remove();

                        var html = response.trim();
                        if (html !== '') {
                            $grid.append($(html));
                            var $subgrids = $grid.find('.year-subgrid');

                            if (isAll || $subgrids.length === 0) {
                                switchToGridCorsi();
                            } else {
                                switchToMasonryCorsi();
                            }
                        } else {
                            $grid.append('<p class="no-results-integrato">No courses found.</p>');
                        }
                    },
                    complete: function() {
                        $grid.css('opacity', '1');
                        loader.hide();
                        ajaxRequest = null;
                    }
                });
            }

            // Auto-trigger da URL: ?cd_filter=bachelor (match fuzzy su data-slug)
            var urlFilterCorsi = new URLSearchParams(window.location.search).get('cd_filter');
            if ( urlFilterCorsi ) {
                var $targetCorsi = null;
                $('#corsi-filtri-integrato .filtro-btn').each(function() {
                    var slug = $(this).data('slug');
                    if ( slug !== 'all' && ( slug.indexOf(urlFilterCorsi) !== -1 || urlFilterCorsi.indexOf(slug) !== -1 ) ) {
                        $targetCorsi = $(this);
                        return false;
                    }
                });
                if ( $targetCorsi && $targetCorsi.length ) {
                    $('.filtro-btn').removeClass('active');
                    $targetCorsi.addClass('active');
                    fetchCourses();
                }
            }

            // Auto-trigger da URL: ?cd_unit=<slug>
            var urlUnitFilter = new URLSearchParams(window.location.search).get('cd_unit');
            if ( urlUnitFilter ) {
                $('#unit-filtri-container').show();
                $('.unit-filtro-checkbox').each(function() {
                    if ( $(this).val() === urlUnitFilter ) {
                        $(this).prop('checked', true);
                        fetchCourses();
                        return false;
                    }
                });
            }

            $('#corsi-filtri-integrato').on('click', '.filtro-btn', function(e) {
                e.preventDefault();
                $('.filtro-btn').removeClass('active');
                $(this).addClass('active');
                fetchCourses();
            });

            $('#unit-filtro-toggle').on('click', function() { $('#unit-filtri-container').slideToggle(200); });
            $('.unit-filtro-checkbox').on('change', function() { fetchCourses(); });
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
// [2026-06-04] Label leggibile per il value di group_years (es. "years_1" -> "Years 1").
// Prova a leggere la label definita nel campo ACF; fallback: formatta lo slug.
function cd_group_years_pretty_label( $value, $post_id ) {
    static $choices = null;
    if ( $choices === null ) {
        $choices = array();
        if ( function_exists( 'get_field_object' ) ) {
            $field = get_field_object( 'general_information', $post_id );
            if ( ! empty( $field['sub_fields'] ) ) {
                foreach ( $field['sub_fields'] as $sub ) {
                    if ( $sub['name'] === 'group_years' && ! empty( $sub['choices'] ) ) {
                        $choices = $sub['choices'];
                        break;
                    }
                }
            }
        }
    }
    $label = isset( $choices[ $value ] ) ? $choices[ $value ] : ucwords( str_replace( array( '_', '-' ), ' ', $value ) );

    // Normalizza varianti sporche: "Year1", "year 1", "Years 1" -> "Year 1"
    if ( preg_match( '/^years?\s*_?\s*(\d+)$/i', trim( $label ), $m ) ) {
        $label = 'Year ' . (int) $m[1];
    }
    return $label;
}

// [2026-06-04] Converte anno accademico (es. "2025-2026") in label relativa "Year N".
// Year 1 = anno accademico corrente (inizio a settembre). Mapping rolling: si aggiorna da solo ogni anno.
function cd_academic_year_to_relative_label( $term_name ) {
    if ( ! preg_match( '/(\d{4})\s*[-\/]\s*\d{2,4}/', $term_name, $m ) ) {
        return $term_name;
    }
    $start_year   = (int) $m[1];
    $current_year = (int) current_time( 'Y' );
    if ( (int) current_time( 'n' ) < 9 ) {
        $current_year--;
    }
    $n = $current_year - $start_year + 1;
    return ( $n >= 1 ) ? 'Year ' . $n : $term_name;
}

add_action('wp_ajax_filtra_corsi_integrato', 'filtra_corsi_callback_integrato');
add_action('wp_ajax_nopriv_filtra_corsi_integrato', 'filtra_corsi_callback_integrato');

function filtra_corsi_callback_integrato($initial = false) {
    if (!$initial) {
        check_ajax_referer('corsi-nonce-integrato', 'security');
    }

    $tax_slug    = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    $unit_slugs  = isset($_POST['units']) && is_array($_POST['units']) ? array_map('sanitize_text_field', $_POST['units']) : array();

    // [2026-06-03 - Punto 5: match parziale su "bachelor" per gestire slug varianti (bachelor, bachelors, bachelor-programs, ecc.)]
    $group_by_year = ( strpos( $tax_slug, 'bachelor' ) !== false );

    $args = array(
        'post_type'              => 'courses',
        'posts_per_page'         => -1,
        'post_status'            => 'publish',
        'no_found_rows'          => true,
        'tax_query'              => array('relation' => 'AND'),
    );

    if ($tax_slug !== 'all') {
        $args['tax_query'][] = array(
            'taxonomy' => 'course_category',
            'field'    => 'slug',
            'terms'    => $tax_slug,
        );
    } else {
        // [2026-06-03 - Punto 2: "all" esclude corsi uncategorized o senza course_category]
        $args['tax_query'][] = array(
            'taxonomy' => 'course_category',
            'field'    => 'slug',
            'terms'    => array( 'uncategorized' ),
            'operator' => 'NOT IN',
        );
    }

    if (!empty($unit_slugs)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'unit',
            'field'    => 'slug',
            'terms'    => $unit_slugs,
            'operator' => 'IN'
        );
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_reset_postdata();
        if (!$initial) wp_die();
        return;
    }

    // Deduplica per group_years: per ogni valore checkbox, tieni solo il post più recente
    $all_ids = wp_list_pluck( $query->posts, 'ID' );
    $filtered_ids = cd_filter_group_years_dedup( $all_ids );
    $filtered_ids_flip = array_flip( $filtered_ids );

    if ($group_by_year) {
        // [2026-06-04: raggruppa per general_information['group_years'] (checkbox ACF, es. "Years 1"). Label mostrata com'è.]
        $groups = array(); // key = group_years value, value = array di post

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            if ( ! isset( $filtered_ids_flip[ $post_id ] ) ) continue;

            $general_info = get_field('general_information', $post_id);
            $gy           = ! empty( $general_info['group_years'] ) ? (array) $general_info['group_years'] : array();
            $gy_first     = ! empty( $gy ) ? reset( $gy ) : '';
            if ( is_array( $gy_first ) ) {
                $gy_first = isset( $gy_first['label'] ) ? $gy_first['label'] : reset( $gy_first );
            }
            if ( $gy_first === '' ) continue; // post senza group_years: non mostrare
            $year_label   = cd_group_years_pretty_label( (string) $gy_first, $post_id );

            $post_data = array(
                'id'        => $post_id,
                'permalink' => get_permalink(),
                'title'     => get_the_title(),
                'excerpt'   => get_the_excerpt(),
                'thumb_id'  => get_post_thumbnail_id(),
            );

            $groups[ $year_label ][] = $post_data;
        }

        // Ordina: numero estratto dal valore asc (Years 1 in alto), '—' in fondo
        uksort( $groups, function( $a, $b ) {
            if ( $a === '—' ) return 1;
            if ( $b === '—' ) return -1;
            preg_match( '/\d+/', $a, $ma );
            preg_match( '/\d+/', $b, $mb );
            return ( isset($ma[0]) ? (int) $ma[0] : 999 ) <=> ( isset($mb[0]) ? (int) $mb[0] : 999 );
        } );

        foreach ($groups as $gval => $posts) {
            $label = $gval;

            echo '<div class="year-group-wrapper">';
            echo '<div class="year-group-header">' . esc_html($label) . '</div>';
            echo '<div class="year-subgrid">';
            echo '<div class="grid-sizer-integrato"></div>';

            foreach ($posts as $p) {
                echo '<article id="post-' . esc_attr($p['id']) . '" class="post-' . esc_attr($p['id']) . ' post type-post status-publish format-standard grid-item-integrato">';
                echo '<div class="course-item-inner">';

                if ($p['thumb_id']) {
                    $img_data = wp_get_attachment_image_src($p['thumb_id'], 'medium_large');
                    if ($img_data) {
                        $https_url = set_url_scheme($img_data[0], 'https');
                        echo '<div class="course-thumbnail">';
                        echo '<a href="' . esc_url($p['permalink']) . '">';
                        echo '<img src="' . esc_url($https_url) . '" width="' . esc_attr($img_data[1]) . '" height="' . esc_attr($img_data[2]) . '" alt="' . esc_attr($p['title']) . '" loading="lazy">';
                        echo '</a></div>';
                    }
                }

                echo '<div class="course-content">';
                echo '<h2 class="course-title"><a href="' . esc_url($p['permalink']) . '">' . esc_html($p['title']) . '</a></h2>';
                echo '<div class="course-excerpt"><p>' . wp_kses_post($p['excerpt']) . '</p></div>';
                echo '</div>';
                echo '</div>';
                echo '</article>';
            }

            echo '</div>'; // .year-subgrid
            echo '</div>'; // .year-group-wrapper
        }
    } else {
        while ($query->have_posts()) : $query->the_post();
            if ( ! isset( $filtered_ids_flip[ get_the_ID() ] ) ) continue; ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('grid-item-integrato'); ?>>
                <div class="course-item-inner">
                    <?php if (has_post_thumbnail()) :
                        $thumb_id = get_post_thumbnail_id();
                        $img_data = wp_get_attachment_image_src($thumb_id, 'medium_large');
                        if ($img_data) :
                            $https_thumb_url = set_url_scheme($img_data[0], 'https');
                            ?>
                            <div class="course-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($https_thumb_url); ?>"
                                         width="<?php echo $img_data[1]; ?>"
                                         height="<?php echo $img_data[2]; ?>"
                                         alt="<?php echo esc_attr(get_the_title()); ?>"
                                         loading="lazy">
                                </a>
                            </div>
                        <?php endif;
                    endif; ?>
                    <div class="course-content">
                        <h2 class="course-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="course-excerpt"><?php the_excerpt(); ?></div>
                    </div>
                </div>
            </article>
        <?php endwhile;
    }

    wp_reset_postdata();

    if (!$initial) {
        wp_die();
    }
}


/* Project  */
add_shortcode('projects_masonry_integrato', 'crea_shortcode_projects_integrato');
function crea_shortcode_projects_integrato() {

    // Carica gli script necessari solo quando lo shortcode è usato.
    wp_enqueue_script('jquery');
    wp_enqueue_script('masonry');
    wp_enqueue_script('imagesloaded', 'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js', array('jquery', 'masonry'), '5.0.0', true);

    // Inizia l'output buffering per catturare tutto l'HTML.
    ob_start();

    // Recupera i termini della tassonomia 'category'.
    $categories = get_terms(array(
        'taxonomy'   => 'category',
        'hide_empty' => true,
    ));

    // Recupera i termini della tassonomia 'unit'.
    $unit_terms = get_terms(array(
        'taxonomy'   => 'unit',
        'hide_empty' => true,
    ));
    ?>

   <style>
        /* Stili esistenti aggiornati a project */
        .filtri-container { text-align: center; margin-bottom: 30px; display: flex; font-weight: 400; flex-wrap: wrap; margin-left: 147px; position: relative; top: -58px; align-items: center; }
        .filtro-btn { padding: 4px 10px; margin: 5px; font-size: 14px; font-weight: 400; cursor: pointer; transition: background-color 0.3s, color 0.3s; border-radius: 0px; background-color: transparent; color: #000; position: relative; display: flex; flex-direction: column; border: none;       letter-spacing: 0px; }
        button.filtro-btn:hover { color: #000; background-color: transparent; }
        button.filtro-btn::after { content:'';
        height: 3px;
        left: 0;
        width: 100%;
        transform: scale(1);
        background-color: #fff;
         position: relative; transition: background-color 0.3s; }
        button.filtro-btn:hover::after, .filtro-btn.active::after { background-color: var(--e-global-color-2c056159); }
        .filtro-btn.active { color: #000; background-color: transparent; }

        /* ALL: CSS grid — righe allineate */
        #projects-grid-integrato.css-grid-mode {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            position: static !important;
            height: auto !important;
            margin-top: -50px;
        }
        #projects-grid-integrato.css-grid-mode .grid-item-integrato {
            position: static !important;
            width: auto !important;
            top: auto !important;
            left: auto !important;
            margin-bottom: 0;
        }
        #projects-grid-integrato.css-grid-mode .grid-sizer-integrato { display: none; }
        #projects-grid-integrato.css-grid-mode .project-thumbnail img { width: 100%; height: 264.7px; object-fit: cover; display: block; }

        @media (max-width: 800px) {
            #projects-grid-integrato.css-grid-mode { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            #projects-grid-integrato.css-grid-mode { grid-template-columns: 1fr; }
        }

        /* Year group layout */
        .year-group-wrapper { width: 100%; margin-bottom: 40px; }
        .year-group-header { font-size: 13px; font-weight: 400; color: #666; padding: 8px 0; letter-spacing: 0.5px; }
        #projects-grid-integrato .year-subgrid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; position: static; }
        /* Safety net: con year groups il container non deve mai stare in css-grid-mode */
        #projects-grid-integrato.css-grid-mode:has(.year-group-wrapper) { display: block !important; }
        #projects-grid-integrato .year-subgrid .grid-sizer-integrato { display: none; }
        #projects-grid-integrato .year-subgrid .grid-item-integrato { width: auto; margin-bottom: 0; position: static; }
        #projects-grid-integrato .year-subgrid .project-thumbnail img { width: 100%; height: 264.7px; object-fit: cover; display: block; }
        @media (max-width: 800px) { #projects-grid-integrato .year-subgrid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { #projects-grid-integrato .year-subgrid { grid-template-columns: 1fr; } }

    </style>

    <div id="projects-masonry-container-integrato">
        <div id="projects-filtri-integrato" class="filtri-container">
            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                <button class="filtro-btn active" data-slug="all">ALL</button>
                <?php
                $allowed_ids = array( 270, 271, 272 );
                // Itera su allowed_ids per rispettare l'ordine definito
                $categories_map = array();
                foreach ( $categories as $term ) {
                    $categories_map[ $term->term_id ] = $term;
                }
                foreach ( $allowed_ids as $tid ) :
                    if ( ! isset( $categories_map[ $tid ] ) ) continue;
                    $term = $categories_map[ $tid ];
                    ?>
                  <button class="filtro-btn" data-slug="category-<?php echo esc_attr($term->slug); ?>">
                      <?php echo esc_html($term->name); ?>
                  </button>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($unit_terms) && !is_wp_error($unit_terms)) : ?>
            <div class="filtri-wrapper">
                <button id="unit-filtro-toggle" aria-label="Filtra per unità">
                   <svg width="26" height="16" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <line x1="5" y1="15" x2="21" y2="15" stroke="black" stroke-width="2"/>
                   <line x1="2" y1="8" x2="24" y2="8" stroke="black" stroke-width="2"/>
                   <line y1="1" x2="26" y2="1" stroke="black" stroke-width="2"/>
                   </svg>
                  <span> UNITS </span>
                </button>
                <div id="unit-filtri-container">
                    <?php foreach ($unit_terms as $term) : ?>
                        <label>
                            <input type="checkbox" class="unit-filtro-checkbox" value="<?php echo esc_attr($term->slug); ?>">
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div id="projects-grid-integrato" class="grid">
            <div class="grid-sizer-integrato"></div>
            <div class="loader-container-integrato"><div class="loader-integrato"></div></div>
            <?php
            $args = array('post_type' => 'post', 'posts_per_page' => -1, 'post_status' => 'publish');
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    // HTML render iniziale
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('grid-item-integrato'); ?>>
                        <div class="project-item-inner">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="project-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('seminar-size'); ?></a></div>
                            <?php endif; ?>
                            <div class="project-content">
                                <h2 class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <div class="project-excerpt">
                                <p><?php $e = strip_tags(get_the_excerpt()); echo esc_html( mb_strlen($e) > 150 ? mb_substr($e, 0, 150) . '...' : $e ); ?></p></div>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
            else :
                echo '<p class="no-results-integrato">No projects found.</p>';
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <script type="text/javascript">
   (function($) {
       $(document).ready(function() {
           var $grid = $('#projects-grid-integrato');
           var loader = $('.loader-container-integrato');
           var ajaxRequest;

           // ALL = CSS grid (righe allineate). Filtered = Masonry per subgrid.
           function switchToGrid() {
               if ($grid.data('masonry')) { $grid.masonry('destroy'); }
               $grid.css({ position: '', height: '' });
               $grid.find('.grid-item-integrato').css({ position: '', top: '', left: '', width: '' });
               $grid.addClass('css-grid-mode');
           }

           function switchToMasonry() {
               $grid.removeClass('css-grid-mode');
               $grid.css({ position: 'relative' });
           }

           // Init caricamento iniziale (ALL) - skip se nel frattempo un filtro ha caricato year groups
           $grid.imagesLoaded(function() {
               if ($grid.find('.year-group-wrapper').length === 0) { switchToGrid(); }
           });

           function fetchProjects() {
               if (ajaxRequest) {
                   ajaxRequest.abort();
               }

               var category = $('#projects-filtri-integrato .filtro-btn.active').data('slug');
               var units = $('.unit-filtro-checkbox:checked').map(function() {
                   return $(this).val();
               }).get();

               ajaxRequest = $.ajax({
                   url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
                   type: 'POST',
                   data: {
                       action: 'filtra_projects_integrato',
                       category: category,
                       units: units,
                       security: '<?php echo wp_create_nonce("projects-nonce-integrato"); ?>'
                   },
                   beforeSend: function() {
                       loader.show();
                       // Invece di far sparire tutto, diamo un feedback visivo di caricamento
                       $grid.stop().animate({ opacity: 0.3 }, 200);
                   },
                   success: function(response) {
                       var html = response.trim();
                       var isAll = (category === 'all' || !category);

                       // Distruggi masonry esistenti
                       if ($grid.data('masonry')) { $grid.masonry('destroy'); }

                       $grid.find('.grid-item-integrato, .year-group-wrapper, .no-results-integrato').remove();

                       if (html !== '') {
                           $grid.append($(html));
                           var $subgrids = $grid.find('.year-subgrid');

                           if (isAll || $subgrids.length === 0) {
                               // ALL: CSS grid, righe allineate
                               switchToGrid();
                           } else {
                               // Filtro attivo: year groups in CSS grid
                               switchToMasonry();
                           }

                           $grid.stop().animate({ opacity: 1 }, 200);
                       } else {
                           $grid.append('<p class="no-results-integrato">No projects found.</p>');
                           $grid.stop().animate({ opacity: 1 }, 200);
                       }
                   },
                   error: function(jqXHR, textStatus) {
                       if (textStatus !== 'abort') {
                           $grid.stop().animate({ opacity: 1 }, 200);
                           console.error("Errore AJAX:", textStatus);
                       }
                   },
                   complete: function() {
                       loader.hide();
                       ajaxRequest = null;
                   }
               });
           }

           // Auto-trigger da URL: ?cd_filter=bachelor-projects (token match)
           var urlFilter = new URLSearchParams(window.location.search).get('cd_filter');
           if ( urlFilter ) {
               var filterTokens = urlFilter.split('-');
               var $target = null;
               $('#projects-filtri-integrato .filtro-btn').each(function() {
                   var slug = $(this).data('slug');
                   if ( slug === 'all' ) return;
                   var slugTokens = slug.split('-');
                   var matched = filterTokens.some(function(ft) {
                       return ft.length > 3 && slugTokens.indexOf(ft) !== -1;
                   });
                   if ( matched ) { $target = $(this); return false; }
               });
               if ( $target && $target.length ) {
                   $('.filtro-btn').removeClass('active');
                   $target.addClass('active');
                   fetchProjects();
               }
           }

           // Auto-trigger da URL: ?cd_unit=<slug>
           var urlUnitFilterProjects = new URLSearchParams(window.location.search).get('cd_unit');
           if ( urlUnitFilterProjects ) {
               $('#unit-filtri-container').show();
               $('.unit-filtro-checkbox').each(function() {
                   if ( $(this).val() === urlUnitFilterProjects ) {
                       $(this).prop('checked', true);
                       fetchProjects();
                       return false;
                   }
               });
           }

           // Listener Eventi
           $('#projects-filtri-integrato').on('click', '.filtro-btn', function(e) {
               e.preventDefault();
               var $this = $(this);
               if ($this.hasClass('active')) return;
               $('.filtro-btn').removeClass('active');
               $this.addClass('active');
               fetchProjects();
           });

           $('#unit-filtro-toggle').on('click', function() {
               $('#unit-filtri-container').slideToggle(200);
           });

           $('#unit-filtri-container').on('change', '.unit-filtro-checkbox', function() {
               fetchProjects();
           });
       });
   })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Filtra un array di post_id rimuovendo i duplicati per group_years + slug base.
 *
 * Due post sono considerati "la stessa edizione" solo se hanno:
 *   - stesso slug base (rimuove suffisso numerico finale, es. -2025 o -1-2)
 *   - stesso valore del checkbox group_years (es. year1, year2)
 *
 * Per ogni coppia (slug_base, group_year_value) viene tenuto solo il post
 * con project_year più recente. I post senza group_years passano invariati.
 *
 * @param  int[] $post_ids
 * @return int[]
 */
function cd_filter_group_years_dedup( $post_ids ) {
    // [2026-06-03 - Punto 6: dedup basata su general_information['year'] (ACF taxonomy) invece di group_years checkbox]
    // Mappa: "{slug_base}::{year_term_id}" => [ 'numeric_year' => int, 'post_id' => int ]
    // Per ogni coppia (slug_base, year) tiene solo il post più recente (project_year numerico).
    // Post senza general_information['year'] passano invariati.
    $by_group = array();
    $no_group = array();

    foreach ( $post_ids as $post_id ) {
        $general_info = get_field( 'general_information', $post_id );
        $year_field   = ! empty( $general_info['year'] ) && is_array( $general_info['year'] ) ? $general_info['year'] : array();

        if ( empty( $year_field ) ) {
            $no_group[] = $post_id;
            continue;
        }

        // Slug base: rimuove suffisso anno 4 cifre finale
        $raw_slug  = get_post_field( 'post_name', $post_id );
        $slug_base = preg_replace( '/-\d{4}(-\d+)?$/', '', $raw_slug );

        // Anno numerico: prima dal term ACF year, fallback su taxonomy project_year
        $numeric_year = 0;
        $year_term    = get_term( (int) $year_field[0] );
        if ( $year_term && ! is_wp_error( $year_term ) && preg_match( '/^\d{4}/', $year_term->name, $ym ) ) {
            $numeric_year = (int) $ym[0];
        }
        if ( ! $numeric_year ) {
            $year_terms = get_the_terms( $post_id, 'project_year' );
            if ( ! empty( $year_terms ) && ! is_wp_error( $year_terms ) ) {
                preg_match( '/^\d{4}/', $year_terms[0]->name, $m );
                $numeric_year = ! empty( $m ) ? (int) $m[0] : 0;
            }
        }

        // [2026-06-04] Dedup globale per slug_base: stesso corso/progetto in anni diversi -> resta solo il più recente
        $key = $slug_base;
        if ( ! isset( $by_group[ $key ] ) || $numeric_year > $by_group[ $key ]['numeric_year'] ) {
            $by_group[ $key ] = array( 'numeric_year' => $numeric_year, 'post_id' => $post_id );
        }
    }

    $deduped = array_values( array_column( $by_group, 'post_id' ) );
    return array_merge( $no_group, $deduped );
}

add_action('wp_ajax_filtra_projects_integrato', 'ajax_filtra_projects_integrato_handler');
add_action('wp_ajax_nopriv_filtra_projects_integrato', 'ajax_filtra_projects_integrato_handler');

function ajax_filtra_projects_integrato_handler() {
    check_ajax_referer('projects-nonce-integrato', 'security');

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    $units    = isset($_POST['units']) ? array_map('sanitize_text_field', $_POST['units']) : array();

    $group_by_year = ( $category !== 'all' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array('relation' => 'AND'),
    );

    if ($category !== 'all') {
        $category_slug = str_replace('category-', '', $category);
        $args['tax_query'][] = array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $category_slug,
        );
    }

    if (!empty($units)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'unit',
            'field'    => 'slug',
            'terms'    => $units,
            'operator' => 'IN',
        );
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_reset_postdata();
        wp_die();
    }

    // Deduplica per group_years: per ogni valore checkbox, tieni solo il post più recente
    $all_ids = wp_list_pluck( $query->posts, 'ID' );
    $filtered_ids = cd_filter_group_years_dedup( $all_ids );
    $filtered_ids_flip = array_flip( $filtered_ids );

    if ($group_by_year) {
        // [2026-06-04: raggruppa per general_information['group_years'] (checkbox ACF). Label mostrata com'è.]
        $groups = array();

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            if ( ! isset( $filtered_ids_flip[ $post_id ] ) ) continue;

            $general_info = get_field('general_information', $post_id);
            $gy           = ! empty( $general_info['group_years'] ) ? (array) $general_info['group_years'] : array();
            $gy_first     = ! empty( $gy ) ? reset( $gy ) : '';
            if ( is_array( $gy_first ) ) {
                $gy_first = isset( $gy_first['label'] ) ? $gy_first['label'] : reset( $gy_first );
            }
            // [2026-06-04] Solo su bachelor i post senza group_years vengono nascosti.
            // Altri filtri (master, graduation): finiscono in un gruppo senza header.
            $is_bachelor = ( strpos( $category, 'bachelor' ) !== false );
            if ( $gy_first === '' && $is_bachelor ) continue;
            $year_label = $gy_first !== '' ? cd_group_years_pretty_label( (string) $gy_first, $post_id ) : '';

            $post_data = array(
                'id'        => $post_id,
                'permalink' => get_permalink(),
                'title'     => get_the_title(),
                'excerpt'   => get_the_excerpt(),
                'thumb_id'  => get_post_thumbnail_id(),
            );

            $groups[ $year_label ][] = $post_data;
        }

        // Ordina: numero estratto dal valore asc (Year 1 in alto), gruppo senza label in fondo
        uksort( $groups, function( $a, $b ) {
            if ( $a === '' ) return 1;
            if ( $b === '' ) return -1;
            preg_match( '/\d+/', $a, $ma );
            preg_match( '/\d+/', $b, $mb );
            return ( isset($ma[0]) ? (int) $ma[0] : 999 ) <=> ( isset($mb[0]) ? (int) $mb[0] : 999 );
        } );

        foreach ($groups as $gval => $posts) {
            $label = $gval;

            echo '<div class="year-group-wrapper">';
            echo '<div class="year-group-header">' . ( $label !== '' ? esc_html($label) : '&nbsp;' ) . '</div>';
            echo '<div class="year-subgrid">';
            echo '<div class="grid-sizer-integrato"></div>';

            foreach ($posts as $p) {
                echo '<article id="post-' . esc_attr($p['id']) . '" class="post-' . esc_attr($p['id']) . ' post type-post status-publish format-standard grid-item-integrato">';
                echo '<div class="project-item-inner">';

                if ($p['thumb_id']) {
                    $image_src = wp_get_attachment_image_src($p['thumb_id'], 'seminar-size');
                    if ($image_src) {
                        $https_url = set_url_scheme($image_src[0], 'https');
                        echo '<div class="project-thumbnail">';
                        echo '<a href="' . esc_url($p['permalink']) . '">';
                        echo '<img src="' . esc_url($https_url) . '" width="' . esc_attr($image_src[1]) . '" height="' . esc_attr($image_src[2]) . '" alt="' . esc_attr($p['title']) . '">';
                        echo '</a></div>';
                    }
                }

                echo '<div class="project-content">';
                echo '<h2 class="project-title"><a href="' . esc_url($p['permalink']) . '">' . esc_html($p['title']) . '</a></h2>';
                $e = strip_tags($p['excerpt']); echo '<div class="project-excerpt"><p>' . esc_html( mb_strlen($e) > 150 ? mb_substr($e, 0, 150) . '...' : $e ) . '</p></div>';
                echo '</div>';
                echo '</div>';
                echo '</article>';
            }

            echo '</div>'; // .year-subgrid
            echo '</div>'; // .year-group-wrapper
        }
    } else {
        while ($query->have_posts()) : $query->the_post();
            if ( ! isset( $filtered_ids_flip[ get_the_ID() ] ) ) continue; ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('grid-item-integrato'); ?>>
                <div class="project-item-inner">
                    <?php if (has_post_thumbnail()) {
                        $thumbnail_id = get_post_thumbnail_id();
                        $image_src    = wp_get_attachment_image_src($thumbnail_id, 'seminar-size');
                        if ($image_src) {
                            $https_url = set_url_scheme($image_src[0], 'https');
                            echo '<div class="project-thumbnail">';
                            echo '<a href="' . get_permalink() . '">';
                            echo '<img src="' . esc_url($https_url) . '" width="' . $image_src[1] . '" height="' . $image_src[2] . '" alt="' . get_the_title() . '">';
                            echo '</a></div>';
                        }
                    } ?>
                    <div class="project-content">
                        <h2 class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="project-excerpt"><?php $e = strip_tags(get_the_excerpt()); echo '<p>' . esc_html( mb_strlen($e) > 150 ? mb_substr($e, 0, 150) . '...' : $e ) . '</p>'; ?></div>
                    </div>
                </div>
            </article>
        <?php endwhile;
    }

    wp_reset_postdata();
    wp_die();
}





// [2026-03-18] Shortcode: category page with masonry grid grouped by year.
// Usage: [cd_category_projects category="bachelor-project"]
// Mirrors the projects_masonry_integrato logic but standalone - no AJAX, no filter bar.
add_shortcode('cd_category_projects', 'cd_category_projects_shortcode');
function cd_category_projects_shortcode($atts) {
    $atts = shortcode_atts(['category' => ''], $atts, 'cd_category_projects');
    $category_slug = sanitize_text_field($atts['category']);
    if (empty($category_slug)) {
        return '<p>No category specified.</p>';
    }

    $query_args = [
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => [[
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $category_slug,
        ]],
    ];
    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        wp_reset_postdata();
        return '<p class="no-results-integrato">No projects found.</p>';
    }

    $groups = [];
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $year    = '';

        $general_info = get_field('general_information', $post_id);
        if (!empty($general_info) && is_array($general_info)) {
            $year_field = isset($general_info['year']) ? $general_info['year'] : null;
            if (!empty($year_field) && is_array($year_field)) {
                $term = get_term($year_field[0]);
                if ($term && !is_wp_error($term)) {
                    $year = $term->name;
                }
            }
        }

        $key = !empty($year) ? $year : '—';
        $groups[$key][] = [
            'id'        => $post_id,
            'permalink' => get_permalink(),
            'title'     => get_the_title(),
            'excerpt'   => get_the_excerpt(),
            'thumb_id'  => get_post_thumbnail_id(),
        ];
    }
    wp_reset_postdata();

    $sorted_groups = [];
    $empty_group   = [];
    foreach ($groups as $label => $posts) {
        if ($label === '—') {
            $empty_group[$label] = $posts;
        } else {
            $sorted_groups[$label] = $posts;
        }
    }
    krsort($sorted_groups);
    $groups = $sorted_groups + $empty_group;

    $grid_id = 'cd-cat-grid-' . sanitize_html_class($category_slug);

    ob_start(); ?>
    <style>
        #<?php echo $grid_id; ?> { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 10px; }
        #<?php echo $grid_id; ?> .cd-year-header { grid-column: 1 / -1; padding: 8px 0; font-size: 13px; font-weight: 400; letter-spacing: 0.5px; margin-top: 10px; }
        #<?php echo $grid_id; ?> article { display: flex; flex-direction: column; }
        #<?php echo $grid_id; ?> .project-item-inner { display: flex; flex-direction: column; flex: 1; }
        #<?php echo $grid_id; ?> .project-thumbnail { aspect-ratio: 4/3; overflow: hidden; }
        #<?php echo $grid_id; ?> .project-thumbnail img { width: 100%; height: 100%; object-fit: cover; display: block; }
        #<?php echo $grid_id; ?> .project-content { padding: 12px 0; flex: 1; }
        #<?php echo $grid_id; ?> .project-title { font-size: 15px; margin: 0 0 6px; line-height: 1.3; }
        #<?php echo $grid_id; ?> .project-title a { text-decoration: none; color: inherit; }
        #<?php echo $grid_id; ?> .project-excerpt p { font-size: 13px; color: #666; margin: 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        @media (max-width: 992px) { #<?php echo $grid_id; ?> { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { #<?php echo $grid_id; ?> { grid-template-columns: 1fr; } }
    </style>
    <div id="<?php echo $grid_id; ?>">
        <?php foreach ($groups as $year_label => $posts) : ?>
            <div class="cd-year-header"><?php echo esc_html($year_label); ?></div>
            <?php foreach ($posts as $p) : ?>
                <article id="post-<?php echo esc_attr($p['id']); ?>">
                    <div class="project-item-inner">
                        <?php if ($p['thumb_id']) :
                            $image_src = wp_get_attachment_image_src($p['thumb_id'], 'seminar-size');
                            if ($image_src) :
                                $https_url = set_url_scheme($image_src[0], 'https'); ?>
                                <div class="project-thumbnail">
                                    <a href="<?php echo esc_url($p['permalink']); ?>">
                                        <img src="<?php echo esc_url($https_url); ?>" width="<?php echo esc_attr($image_src[1]); ?>" height="<?php echo esc_attr($image_src[2]); ?>" alt="<?php echo esc_attr($p['title']); ?>" loading="lazy">
                                    </a>
                                </div>
                        <?php endif; endif; ?>
                        <div class="project-content">
                            <h2 class="project-title"><a href="<?php echo esc_url($p['permalink']); ?>"><?php echo esc_html($p['title']); ?></a></h2>
                            <div class="project-excerpt"><p><?php echo wp_kses_post($p['excerpt']); ?></p></div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// [2026-06-04] Stesso layout di projects/courses (year-group-wrapper + grid 3 colonne), senza filtri.
function archiprix_grouped_by_year_shortcode($atts) {
    $query = new WP_Query([
        'post_type'      => 'archiprix',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ]);

    if (!$query->have_posts()) {
        wp_reset_postdata();
        return '<p>No projects found.</p>';
    }

    $groups = [];

    while ($query->have_posts()) {
        $query->the_post();
        $pid = get_the_ID();

        // Raggruppa per campo ACF "year", stampa il valore del term
        $general_info = get_field('general_information', $pid);
        $year_label   = '';
        if (!empty($general_info['year'])) {
            $yv = $general_info['year'];
            if (is_array($yv)) $yv = reset($yv);
            $term = is_numeric($yv) ? get_term((int) $yv) : null;
            if ($term && !is_wp_error($term)) {
                $year_label = $term->name;
            } elseif (is_string($yv)) {
                $year_label = $yv;
            }
        }
        if ($year_label === '') continue; // senza year: non mostrare

        $groups[$year_label][] = [
            'id'        => $pid,
            'permalink' => get_permalink(),
            'title'     => get_the_title(),
            'excerpt'   => get_the_excerpt(),
            'thumb_id'  => get_post_thumbnail_id(),
        ];
    }
    wp_reset_postdata();

    // Ordina per anno desc (più recente in alto)
    krsort($groups);

    ob_start();
    ?>
    <style>
        #archiprix-grid-integrato .year-group-wrapper { width: 100%; margin-bottom: 40px; }
        #archiprix-grid-integrato .year-group-header { font-size: 13px; font-weight: 400; color: #666; padding: 8px 0; letter-spacing: 0.5px; }
        #archiprix-grid-integrato .year-subgrid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        #archiprix-grid-integrato .grid-item-integrato { width: auto; margin-bottom: 0; background: #fff; }
        #archiprix-grid-integrato .project-thumbnail img { width: 100%; height: 264.7px; object-fit: cover; display: block; }
        #archiprix-grid-integrato .project-content { padding: 24px 0 0; }
        #archiprix-grid-integrato .project-title { margin: 0 0 8px; }
        @media (max-width: 800px) { #archiprix-grid-integrato .year-subgrid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { #archiprix-grid-integrato .year-subgrid { grid-template-columns: 1fr; } }
    </style>
    <div id="archiprix-grid-integrato">
    <?php
    foreach ($groups as $gval => $posts) {
        $label = $gval;

        echo '<div class="year-group-wrapper">';
        echo '<div class="year-group-header">' . esc_html($label) . '</div>';
        echo '<div class="year-subgrid">';

        foreach ($posts as $p) {
            echo '<article id="post-' . esc_attr($p['id']) . '" class="post-' . esc_attr($p['id']) . ' grid-item-integrato">';
            echo '<div class="project-item-inner">';

            if ($p['thumb_id']) {
                $image_src = wp_get_attachment_image_src($p['thumb_id'], 'seminar-size');
                if ($image_src) {
                    $https_url = set_url_scheme($image_src[0], 'https');
                    echo '<div class="project-thumbnail">';
                    echo '<a href="' . esc_url($p['permalink']) . '">';
                    echo '<img src="' . esc_url($https_url) . '" width="' . esc_attr($image_src[1]) . '" height="' . esc_attr($image_src[2]) . '" alt="' . esc_attr($p['title']) . '" loading="lazy">';
                    echo '</a></div>';
                }
            }

            echo '<div class="project-content">';
            echo '<h2 class="project-title"><a href="' . esc_url($p['permalink']) . '">' . esc_html($p['title']) . '</a></h2>';
            $e = strip_tags($p['excerpt']);
            echo '<div class="project-excerpt"><p>' . esc_html(mb_strlen($e) > 150 ? mb_substr($e, 0, 150) . '...' : $e) . '</p></div>';
            echo '</div>';
            echo '</div>';
            echo '</article>';
        }

        echo '</div>'; // .year-subgrid
        echo '</div>'; // .year-group-wrapper
    }
    ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('archiprix_projects', 'archiprix_grouped_by_year_shortcode');


/* Projects by year - parsing Elementor heading */
add_shortcode('projects_by_year', 'cd_projects_by_year_shortcode');
function cd_projects_by_year_shortcode($atts) {

    $atts = shortcode_atts(array(
        'year' => '',
        'limit' => -1,
    ), $atts);

    // 1. Determina l'anno: da attributo esplicito oppure parsing Elementor
    $year_slug = sanitize_text_field($atts['year']);

    if (empty($year_slug)) {
        $post_id = get_the_ID();
        $elementor_data = get_post_meta($post_id, '_elementor_data', true);

        if (!empty($elementor_data)) {
            $data = json_decode($elementor_data, true);

            // Attraversa ricorsivamente i widget Elementor cercando heading con anno
            $year_slug = cd_extract_year_from_elementor($data);
        }
    }

    if (empty($year_slug)) {
        return '<p class="cd-no-year">Academic year not found.</p>';
    }

    // 2. WP_Query filtrata per project_year
    $query = new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => intval($atts['limit']),
        'tax_query'      => array(
            array(
                'taxonomy' => 'project_year',
                'field'    => 'slug',
                'terms'    => $year_slug,
            ),
        ),
        'orderby' => 'title',
        'order'   => 'ASC',
    ));

    if (!$query->have_posts()) {
        return '<p class="cd-no-results">No projects found. ' . esc_html($year_slug) . '.</p>';
    }

    // 3. Render
    ob_start();
    ?>
    <div class="cd-projects-by-year" data-year="<?php echo esc_attr($year_slug); ?>">
        <div class="cd-projects-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('cd-project-card'); ?>>
                    <a href="<?php the_permalink(); ?>" class="cd-project-card-link">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="cd-project-thumbnail">
                                <?php the_post_thumbnail('medium_large', array('loading' => 'lazy')); ?>
                            </div>
                        <?php endif; ?>
                        <div class="cd-project-info">
                            <h3 class="cd-project-title"><?php the_title(); ?></h3>
                            <?php
                            $excerpt = get_the_excerpt();
                            if ($excerpt) :
                            ?>
                                <p class="cd-project-excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <style>
        .cd-projects-by-year { margin: 40px 0; }
        .cd-projects-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .cd-project-card { background: #fff; }
        .cd-project-card-link { display: block; text-decoration: none; color: inherit; }
        .cd-project-thumbnail img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .cd-project-info { padding: 15px; }
        .cd-project-title { font-size: 16px; margin: 0 0 8px; line-height: 1.3; }
        .cd-project-excerpt { font-size: 13px; color: #666; margin: 0; line-height: 1.5;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        @media (max-width: 992px) { .cd-projects-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .cd-projects-grid { grid-template-columns: 1fr; } }
    </style>
    <?php
    return ob_get_clean();
}

function cd_extract_year_from_elementor($elements) {
    if (!is_array($elements)) return '';

    foreach ($elements as $element) {
        // Controlla se è un widget heading con anno nel titolo
        if (
            isset($element['elType']) && $element['elType'] === 'widget' &&
            isset($element['widgetType']) && strpos($element['widgetType'], 'heading') !== false &&
            isset($element['settings']['title'])
        ) {
            preg_match('/(\d{4}-\d{4})/', $element['settings']['title'], $matches);
            if (!empty($matches[1])) {
                return $matches[1]; // es. "2024-2025"
            }
        }

        // Ricorsione su elementi figli (sections, columns, containers)
        if (!empty($element['elements'])) {
            $found = cd_extract_year_from_elementor($element['elements']);
            if (!empty($found)) return $found;
        }
    }

    return '';
}
