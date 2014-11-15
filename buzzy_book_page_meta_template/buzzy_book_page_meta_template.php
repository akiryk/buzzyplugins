<?php
 
/*
Plugin Name: Book Pages
Description: Create a page for promoting a book
Author: Adam Kiryk
Version: 1.0
Author URI: http://akiryk.github.com
*/

/**
 * Move all "advanced" metaboxes above the default editor
 */
add_action('edit_form_after_title', function() {
    global $post, $wp_meta_boxes;
    do_meta_boxes(get_current_screen(), 'advanced', $post);
    unset($wp_meta_boxes[get_post_type($post)]['advanced']);
});

/**
 * Adds a meta box to the post editing screen
 */
function buzzy_bookpage_meta() {
  // check for a template type
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
  $template_file = get_post_meta($post_id, '_wp_page_template', TRUE);

  if ($template_file == 'book.php') {
    add_meta_box( 'prfx_meta', __( 'Main Book Promotion', 'prfx-textdomain' ), 'buzzy_bookpage_meta_callback', 'page', 'advanced', 'high' );
  }
}
add_action( 'add_meta_boxes', 'buzzy_bookpage_meta' );

/**
 * Adds the meta box stylesheet when appropriate
 */
function buzzy_bookpage_admin_styles(){
    global $typenow;
    if( $typenow == 'page' ) {
        wp_enqueue_style( 'prfx_meta_box_styles', plugin_dir_url( __FILE__ ) . 'buzzy_book_page_meta.css' );
    }
}
add_action( 'admin_print_styles', 'buzzy_bookpage_admin_styles' );


/**
 * Loads the image management javascript
 */
// function buzzy_bookpage_image_enqueue() {
//     global $typenow;
//     if( $typenow == 'page' ) {
//         wp_enqueue_media();
 
//         // Registers and enqueues the required javascript.
//         wp_register_script( 'buzzy_book_page_meta_image', plugin_dir_url( __FILE__ ) . 'buzzy_book_page_meta_image.js', array( 'jquery' ) );
//         wp_localize_script( 'buzzy_book_page_meta_image', 'meta_image',
//             array(
//                 'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
//                 'button' => __( 'Use this image', 'prfx-textdomain' ),
//             )
//         );
//         wp_enqueue_script( 'meta-box-image' );
//     }
// }
// add_action( 'admin_enqueue_scripts', 'buzzy_bookpage_image_enqueue' );

/**
 * Outputs the content of the meta box
 */
function buzzy_bookpage_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
  $prfx_stored_meta = get_post_meta( $post->ID );
  ?>

  <p>
    <label for="mb-book-subheading-1" class="prfx-row-title"><?php _e( 'Subtitle/Subheading', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-book-subheading-1" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-book-subheading-1'] ) ) echo $prfx_stored_meta['mb-book-subheading-1'][0]; ?>" />
  </p>
  <p>
    <label for="mb-purchase-callout" class="prfx-row-title"><?php _e( 'Promote Purchases', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-purchase-callout" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-purchase-callout'] ) ) echo $prfx_stored_meta['mb-purchase-callout'][0]; ?>" />
  </p>
  <p>
    <label for="mb-buy-button-text" class="prfx-row-title"><?php _e( 'Buy Button Text', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-buy-button-text" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-buy-button-text'] ) ) echo $prfx_stored_meta['mb-buy-button-text'][0]; ?>" />
  </p>
  <p>
    <span class="prfx-row-title"><?php _e( 'Buy Button Style', 'prfx-textdomain' )?></span>
    <div class="prfx-row-content">
        <label for="mb-buy-button-style-1">
            <input type="radio" name="mb-buy-button-style" id="mb-buy-button-style-1" value="button-normal" <?php if ( isset ( $prfx_stored_meta['mb-buy-button-style'] ) ) checked( $prfx_stored_meta['mb-buy-button-style'][0], 'button-normal' ); ?>>
            <?php _e( 'Normal', 'prfx-textdomain' )?>
        </label>
        <label for="mb-buy-button-style-2">
            <input type="radio" name="mb-buy-button-style" id="mb-buy-button-style-2" value="button-super" <?php if ( isset ( $prfx_stored_meta['mb-buy-button-style'] ) ) checked( $prfx_stored_meta['mb-buy-button-style'][0], 'button-super' ); ?>>
            <?php _e( 'Super', 'prfx-textdomain' )?>
        </label>
    </div>
</p>
  <p>
    <label class="prfx-row-title">Buy Link</label>
    <input type="url" name="mb-buy-link-url" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-buy-link-url'] ) ) echo $prfx_stored_meta['mb-buy-link-url'][0]; ?>" />
  </p>
  <?php
}

/**
 * Outputs the content of the secondary books callback
 */


/**
 * Saves the custom meta input
 */
function buzzy_bookpage_meta_save( $post_id ) {
 
  // Checks save status
  $is_autosave = wp_is_post_autosave( $post_id );
  $is_revision = wp_is_post_revision( $post_id );
  $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

  // Exits script depending on save status
  if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
      return;
  }

  // Checks for input and sanitizes/saves if needed
  if( isset( $_POST[ 'mb-book-subheading-1' ] ) ) {
    update_post_meta( $post_id, 'mb-book-subheading-1', sanitize_text_field( $_POST[ 'mb-book-subheading-1' ] ) );
  }
  if( isset( $_POST[ 'mb-purchase-callout' ] ) ) {
    update_post_meta( $post_id, 'mb-purchase-callout', sanitize_text_field( $_POST[ 'mb-purchase-callout' ] ) );
  }
  if( isset( $_POST[ 'mb-buy-button-primary' ] ) ) {
    update_post_meta( $post_id, 'mb-buy-button-primary', sanitize_text_field( $_POST[ 'mb-buy-button-primary' ] ) );
  }
  // Radio Buttons for Buy Style
  if( isset( $_POST[ 'mb-buy-button-style' ] ) ) {
    update_post_meta( $post_id, 'mb-buy-button-style', $_POST[ 'mb-buy-button-style' ] );
  } else {
    update_post_meta( $post_id, 'mb-buy-button-style', 'button-normal' );
  }
  if( isset( $_POST[ 'mb-buy-link-url' ] ) ) {
    update_post_meta( $post_id, 'mb-buy-link-url', sanitize_text_field( $_POST[ 'mb-buy-link-url' ] ) );
  }
  if( isset( $_POST[ 'mb-buy-button-text' ] ) ) {
    update_post_meta( $post_id, 'mb-buy-button-text', sanitize_text_field( $_POST[ 'mb-buy-button-text' ] ) );
  }
}
add_action( 'save_post', 'buzzy_bookpage_meta_save' );
