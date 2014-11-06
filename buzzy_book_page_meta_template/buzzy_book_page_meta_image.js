/*
 * Attaches the image uploader to the input field
 */
jQuery(document).ready(function($){
    // Instantiates the variable that holds the media library frame.
    var meta_image_frame,
        $this,
        $image,
        $imageInput;
    // Runs when the image button is clicked.
    // $('.add-image').click(function(e){
    $('#meta_inner').on('click', function(e){
        
        if ( $(e.target).is('.add-image') ) {
            $this = $(e.target);
            $image = $this.parent().find('.flex-image-thumb');
            $imageInput = $image.next('input');
            console.log($imageInput);
        } else {
            return;
        } 
        // Prevents the default action from occuring.
        e.preventDefault();
        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }
 
        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });
        
        // Runs when an image is selected.
        meta_image_frame.on('select', function(){
            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
            // Sends the attachment URL to our custom image input field.
            $image.html('<img src="' +  media_attachment.url + '" />');
            $imageInput.val(media_attachment.url);
        });
 
        // Opens the media library frame.
        meta_image_frame.open();
        
    });
});