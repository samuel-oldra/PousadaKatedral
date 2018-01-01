jQuery(document).ready(function($) {
    $('#wppg_upload_album_thumb_button').click(function() {
        tb_show('Select An Album Thumbnail Image', 'media-upload.php?referer=wppg_album&amp;TB_iframe=true&amp;post_id=0', false);
        return false;
    });

    window.send_to_editor = function(html) {
        var fileelement = $(html);
        thumburl = fileelement.attr('href');
        $('#wppg_upload_album_thumb').val(thumburl);
        tb_remove();
    }
});