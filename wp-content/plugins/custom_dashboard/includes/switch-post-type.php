<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Module: Switch Post Type
 * Al cambio del dropdown trigga automaticamente Save Draft + redirect all'editor del nuovo CPT.
 */

function cd_switch_allowed_types() {
    return [ 'post', 'courses', 'seminars', 'archiprix' ];
}

function cd_switch_type_label( $type ) {
    $labels = [
        'post'      => 'Projects',
        'courses'   => 'Courses',
        'seminars'  => 'Seminars',
        'archiprix' => 'Archiprix',
    ];
    return $labels[ $type ] ?? ucfirst( $type );
}

add_action( 'add_meta_boxes',         'cd_switch_post_type_meta_box' );
add_action( 'save_post',              'cd_switch_post_type_on_save', 10, 2 );
add_action( 'admin_notices',          'cd_switch_post_type_notice' );
add_filter( 'redirect_post_location', 'cd_switch_post_type_redirect', 10, 2 );

function cd_switch_post_type_meta_box() {
    foreach ( cd_switch_allowed_types() as $post_type ) {
        add_meta_box(
            'cd_switch_post_type',
            'Switch Post Type',
            'cd_switch_post_type_render',
            $post_type,
            'side',
            'default'
        );
    }
}

function cd_switch_post_type_render( $post ) {
    $current_type = $post->post_type;
    $options      = array_filter( cd_switch_allowed_types(), fn( $t ) => $t !== $current_type );

    wp_nonce_field( 'cd_switch_post_type_nonce', '_cd_switch_nonce' );
    ?>
    <p style="margin: 8px 0; font-size: 12px; color: #555;">
        Select a different post type to move this post. It will be saved as a draft in the selected type.
    </p>
    <select id="cd_switch_select" name="_switch_to_post_type" style="width: 100%; margin-top: 4px;">
        <option value="">— Select target type —</option>
        <?php foreach ( $options as $type ) : ?>
            <option value="<?php echo esc_attr( $type ); ?>">
                <?php echo esc_html( cd_switch_type_label( $type ) ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <script>
    (function() {
        var select = document.getElementById('cd_switch_select');
        if ( ! select ) return;

        select.addEventListener('change', function() {
            if ( this.value === '' ) return;

            // Forza il pulsante "Save Draft" nel form
            var saveDraft = document.getElementById('save-post');
            if ( ! saveDraft ) return;

            saveDraft.click();
        });
    })();
    </script>
    <?php
}

function cd_switch_post_type_on_save( $post_id, $post ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['_cd_switch_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['_cd_switch_nonce'], 'cd_switch_post_type_nonce' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $target = sanitize_key( $_POST['_switch_to_post_type'] ?? '' );

    if ( empty( $target ) || ! in_array( $target, cd_switch_allowed_types(), true ) ) {
        return;
    }

    if ( $target === $post->post_type ) {
        return;
    }

    remove_action( 'save_post', 'cd_switch_post_type_on_save', 10 );

    wp_update_post( [
        'ID'          => $post_id,
        'post_type'   => $target,
        'post_status' => 'draft',
    ] );

    add_action( 'save_post', 'cd_switch_post_type_on_save', 10, 2 );

    set_transient( 'cd_switch_target_' . get_current_user_id(), [
        'post_id' => $post_id,
        'target'  => $target,
    ], 60 );

    set_transient(
        'cd_switch_notice_' . get_current_user_id(),
        sprintf(
            'Post moved to <strong>%s</strong> and saved as draft.',
            esc_html( cd_switch_type_label( $target ) )
        ),
        60
    );
}

function cd_switch_post_type_redirect( $location, $post_id ) {
    $user_id = get_current_user_id();
    $data    = get_transient( 'cd_switch_target_' . $user_id );

    if ( ! $data || (int) $data['post_id'] !== (int) $post_id ) {
        return $location;
    }

    delete_transient( 'cd_switch_target_' . $user_id );

    return add_query_arg(
        [ 'post' => $post_id, 'action' => 'edit' ],
        admin_url( 'post.php' )
    );
}

function cd_switch_post_type_notice() {
    $user_id = get_current_user_id();
    $key     = 'cd_switch_notice_' . $user_id;
    $message = get_transient( $key );

    if ( ! $message ) {
        return;
    }

    delete_transient( $key );

    echo '<div class="notice notice-success is-dismissible"><p>' . wp_kses_post( $message ) . '</p></div>';
}
