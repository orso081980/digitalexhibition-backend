<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Esci se si accede direttamente.
}
if ( ! function_exists( 'armb_enqueue_parent_page_assets' ) ) {
    /**
     * Carica gli script e gli stili necessari per la modale ThickBox
     * e per l'interazione con ACF nella pagina di modifica del post.
     *
     * @param string $hook_suffix L'identificativo della pagina di amministrazione corrente.
     */
    function armb_enqueue_parent_page_assets( $hook_suffix ) {
        // Esegui solo nelle pagine di creazione/modifica dei post.
        if ( 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) {
            return;
        }

        // Enqueue di ThickBox, la libreria modale nativa di WordPress.
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_script( 'thickbox' );
    }
    add_action( 'admin_enqueue_scripts', 'armb_enqueue_parent_page_assets' );
}

if ( ! function_exists( 'armb_add_repeater_button_script' ) ) {
    /**
     * Stampa lo script JavaScript nel footer della pagina di amministrazione
     * per aggiungere dinamicamente il pulsante "Crea Nuovo" alle righe del repeater.
     */
    function armb_add_repeater_button_script() {
        $screen = get_current_screen();
        // Assicuriamoci di essere in una pagina di modifica post prima di stampare lo script.
        if ( ! $screen || ($screen->base !== 'post' && $screen->base !== 'post-new') ) {
            return;
        }

        // Costruisci l'URL per la modale ThickBox.
        $modal_url = admin_url('post-new.php?post_type=dflip&TB_iframe=true&width=900&height=600');
        $escaped_modal_url = esc_js( $modal_url );
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            // Verifica che l'oggetto ACF sia disponibile.
            if (typeof acf === 'undefined') {
                return;
            }

            /**
             * Funzione per aggiungere il pulsante a una specifica riga del repeater.
             * @param {jQuery} $row L'oggetto jQuery che rappresenta la riga del repeater.
             */
            function addButtonToRow($row) {
                // Selettore specifico per il campo target.
                var $targetLabel = $row.find('.acf-field[data-name="shortcode_pdf"] .acf-label');

                // Aggiungi il pulsante solo se esiste il campo target e il pulsante non è già presente.
                if ($targetLabel.length && !$targetLabel.find('.btn-create-flipbook').length) {
                    var $flipbookButton = $(
                        '<a href="<?php echo $escaped_modal_url; ?>" class="button button-small btn-create-flipbook thickbox" title="Create New Flipbook" style="margin-left: 10px; vertical-align: middle;">' +
                            '<span class="dashicons dashicons-plus-alt" style="vertical-align: text-bottom; margin-right: 4px;"></span>' +
                            'Create New FlipPDF' +
                        '</a>'
                    );
                    $targetLabel.append($flipbookButton);
                }
            }

            // Hook di ACF: eseguito quando i campi sono pronti al caricamento della pagina.
            acf.add_action('ready', function() {
                // Itera su tutte le righe esistenti del repeater 'pdf_post'.
                $('.acf-field-repeater[data-name="pdf_post"] .acf-row:not(.acf-clone)').each(function() {
                    addButtonToRow($(this));
                });
            });

            // Hook di ACF: eseguito quando una nuova riga viene aggiunta a un repeater.
            acf.add_action('append', function($newRow) {
                // Controlla se la nuova riga appartiene al nostro repeater 'pdf_post'.
                if ($newRow.closest('.acf-field-repeater[data-name="pdf_post"]').length) {
                    addButtonToRow($newRow);
                }
            });
        });
        </script>
        <?php
    }
    add_action( 'admin_footer', 'armb_add_repeater_button_script' );
}

// =========================================================================
// BLOCCO #2: CODICE PER LA PAGINA NELLA MODALE (IFRAME)
// Questo blocco gestisce l'auto-pulizia dell'interfaccia quando la pagina
// di creazione 'dflip' viene caricata all'interno dell'iframe.
// QUI È STATA APPLICATA LA CORREZIONE.
// =========================================================================

if ( ! function_exists( 'armb_iframe_self_cleaner_assets' ) ) {
    /**
     * Aggiunge CSS e JS alla pagina di creazione 'dflip' per pulire la UI
     * se la pagina rileva di essere all'interno di un iframe.
     */
    function armb_iframe_self_cleaner_assets() {
        global $pagenow;

        // --- CORREZIONE APPLICATA QUI ---
        // Questa condizione è più affidabile di get_current_screen() per questo scenario.
        // Controlla direttamente il file e il parametro GET.
        if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'dflip' === $_GET['post_type'] ) {
            ?>
            <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                // Controllo standard e infallibile per rilevare un iframe.
                if ( window.self !== window.top ) {
                    // Se siamo in un iframe, aggiungiamo una classe al body.
                    // Questo ci permette di applicare stili CSS specifici.
                    document.body.classList.add('is-iframe-modal');
                }
            });
            </script>
            <style type="text/css">
                /* Gli stili si attivano solo se il body ha la nostra classe. */
                body.is-iframe-modal #wpadminbar,
                body.is-iframe-modal #adminmenumain,
                body.is-iframe-modal #wpfooter,
                body.is-iframe-modal .wrap .page-title-action, /* Pulsante "Aggiungi nuovo" vicino al titolo */
                body.is-iframe-modal #screen-meta-links, /* Tab "Impostazioni Schermata" in alto */
                body.is-iframe-modal .wp-heading-inline+.page-title-action,
                body.is-iframe-modal .notice:not(.inline) /* Nasconde le notifiche generali */
                {
                    display: none !important;
                }

                /* Riadatta i margini per occupare lo spazio lasciato libero dal menu. */
                body.is-iframe-modal #wpcontent {
                    margin-left: 0 !important;
                    padding-left: 20px !important;
                }
                body.is-iframe-modal #wpbody-content {
                    padding-bottom: 20px !important;
                }
            </style>
            <?php
        }
    }
    add_action( 'admin_head', 'armb_iframe_self_cleaner_assets' );
}
