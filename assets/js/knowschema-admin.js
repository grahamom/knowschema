jQuery(document).ready(function($) {
    // Metabox Preview Handler
    var $previewBtn = $('<button type="button" class="button" id="knowschema-preview-btn" style="margin-top:10px;">Preview Schema</button>');
    var $previewContainer = $('<div id="knowschema-preview-container" style="margin-top:10px; display:none;"><textarea readonly style="width:100%; height:300px; font-family:monospace;"></textarea></div>');
    
    $('#knowschema_metabox .inside').append($previewBtn).append($previewContainer);

    $previewBtn.on('click', function() {
        var postId = $('#post_ID').val();
        var template = $('#knowschema_schema_template').val();
        var nonce = $('#knowschema_metabox_nonce').val(); // We might need a specific nonce for AJAX

        $previewBtn.prop('disabled', true).text('Generating...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'knowschema_preview_schema',
                post_id: postId,
                template: template,
                _ajax_nonce: knowschema_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $previewContainer.show().find('textarea').val(JSON.stringify(response.data, null, 2));
                } else {
                    alert('Error generating preview');
                }
            },
            complete: function() {
                $previewBtn.prop('disabled', false).text('Preview Schema');
            }
        });
    });
});
