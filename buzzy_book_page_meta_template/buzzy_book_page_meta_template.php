<?php
 
/*
Plugin Name: Book Pages
Description: Create a page for promoting a book
Author: Adam Kiryk
Version: 1.0
Author URI: http://akiryk.github.com
*/

/**
 * Adds a meta box to the post editing screen
 */
function buzzy_bookpage_meta() {
  // check for a template type
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
  $template_file = get_post_meta($post_id, '_wp_page_template', TRUE);

  if ($template_file == 'book.php') {
    add_meta_box( 'prfx_meta', __( 'Main Book Promotion', 'prfx-textdomain' ), 'buzzy_bookpage_meta_callback', 'page', 'normal', 'high' );
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
function buzzy_bookpage_image_enqueue() {
    global $typenow;
    if( $typenow == 'page' ) {
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'buzzy_book_page_meta_image', plugin_dir_url( __FILE__ ) . 'buzzy_book_page_meta_image.js', array( 'jquery' ) );
        wp_localize_script( 'buzzy_book_page_meta_image', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
                'button' => __( 'Use this image', 'prfx-textdomain' ),
            )
        );
        wp_enqueue_script( 'meta-box-image' );
    }
}
add_action( 'admin_enqueue_scripts', 'buzzy_bookpage_image_enqueue' );

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
    <label for="mb-landing-page-url" class="prfx-row-title"><?php _e( 'Link url', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-landing-page-url" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-landing-page-url'] ) ) echo $prfx_stored_meta['mb-landing-page-url'][0]; ?>" />
  </p>
  <p>
    <label for="mb-callout-primary" class="prfx-row-title"><?php _e( 'Callout', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-callout-primary" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-callout-primary'] ) ) echo $prfx_stored_meta['mb-callout-primary'][0]; ?>" />
  </p>
  <p>
    <span class="prfx-row-title"><?php _e( 'Callout Style', 'prfx-textdomain' )?></span>
    <div class="prfx-row-content">
        <label for="mb-callout-style-1">
            <input type="radio" name="mb-callout-style" id="mb-callout-style-1" value="callout-major" <?php if ( isset ( $prfx_stored_meta['mb-callout-style'] ) ) checked( $prfx_stored_meta['mb-callout-style'][0], 'callout-major' ); ?>>
            <?php _e( 'Style 1', 'prfx-textdomain' )?>
        </label>
        <label for="mb-callout-style-2">
            <input type="radio" name="mb-callout-style" id="mb-callout-style-2" value="callout-minor" <?php if ( isset ( $prfx_stored_meta['mb-callout-style'] ) ) checked( $prfx_stored_meta['mb-callout-style'][0], 'callout-minor' ); ?>>
            <?php _e( 'Style 2', 'prfx-textdomain' )?>
        </label>
    </div>
</p>
  <p>
    <label for="mb-callout-secondary" class="prfx-row-title"><?php _e( 'Secondary Callout', 'prfx-textdomain' )?></label>
    <input type="text" name="mb-callout-secondary" class="meta-text-input" value="<?php if ( isset ( $prfx_stored_meta['mb-callout-secondary'] ) ) echo $prfx_stored_meta['mb-callout-secondary'][0]; ?>" />
  </p>
  <div class="flex-container">
    <label for="meta-image" class="prfx-row-title"><?php _e( 'Image Upload', 'prfx-textdomain' )?></label>
    <span class="flex-image-thumb"><img src="<?php echo $prfx_stored_meta['meta-image'][0]; ?>" /></span>
    <input type="hidden" name="meta-image" id="meta-image" value="<?php if ( isset ( $prfx_stored_meta['meta-image'] ) ) echo $prfx_stored_meta['meta-image'][0]; ?>" />
    <input type="button" id="meta-image-button" class="button add-image" value="<?php _e( 'Choose or Upload an Image', 'prfx-textdomain' )?>" />
  </div>

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
  if( isset( $_POST[ 'mb-landing-page-url' ] ) ) {
    update_post_meta( $post_id, 'mb-landing-page-url', sanitize_text_field( $_POST[ 'mb-landing-page-url' ] ) );
  }
  if( isset( $_POST[ 'mb-callout-primary' ] ) ) {
    update_post_meta( $post_id, 'mb-callout-primary', sanitize_text_field( $_POST[ 'mb-callout-primary' ] ) );
  }
  if( isset( $_POST[ 'mb-callout-secondary' ] ) ) {
    update_post_meta( $post_id, 'mb-callout-secondary', sanitize_text_field( $_POST[ 'mb-callout-secondary' ] ) );
  }

  // Checkboxes
  // if( isset( $_POST[ 'mb-show-message-checkbox' ] ) ) {
  //   update_post_meta( $post_id, 'mb-show-message-checkbox', 'yes' );
  // } else {
  //   update_post_meta( $post_id, 'mb-show-message-checkbox', '' );
  // }

  // Checks for input and saves if needed
  if( isset( $_POST[ 'mb-callout-style' ] ) ) {
      update_post_meta( $post_id, 'mb-callout-style', $_POST[ 'mb-callout-style' ] );
  }

  // Save image
  if( isset( $_POST[ 'meta-image' ] ) ) {
    update_post_meta( $post_id, 'meta-image', $_POST[ 'meta-image' ] );
  }
}
add_action( 'save_post', 'buzzy_bookpage_meta_save' );
