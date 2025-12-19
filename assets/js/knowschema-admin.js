jQuery(document).ready(function($) {
    // Metabox Preview Handler
    var $previewBtn = $('<button type="button" class="button" id="knowschema-preview-btn" style="margin-top:10px;">Preview Schema</button>');
    var $previewContainer = $('<div id="knowschema-preview-container" style="margin-top:10px; display:none;"><textarea readonly style="width:100%; height:300px; font-family:monospace;"></textarea></div>');
    
    $('#knowschema_metabox .inside').append($previewBtn).append($previewContainer);

    $('#knowschema_schema_template').on('change', function() {
        var template = $(this).val();
        $('.ks-group').hide();
        $('.ks-group-' + template).show();
    });

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
                    $previewContainer.show().find('textarea').val(JSON.stringify(response.data.graph, null, 2));
                    
                    // Update Badge
                    var readiness = response.data.readiness;
                    var $badge = $('#ks-readiness-badge');
                    var $status = $badge.find('.ks-badge');
                    var $list = $badge.find('.ks-missing-fields');
                    
                    $badge.show();
                    $list.empty();
                    
                    if (readiness.status === 'green') {
                        $status.text('Eligible for Rich Results').css('background', '#46b450');
                    } else if (readiness.status === 'amber') {
                        $status.text('Recommended Fields Missing').css('background', '#ffb900');
                        readiness.missing_recommended.forEach(function(f) {
                            $list.append('<li>Recommended: ' + f + '</li>');
                        });
                    } else {
                        $status.text('Incomplete (Missing Required Fields)').css('background', '#dc3232');
                        readiness.missing_required.forEach(function(f) {
                            $list.append('<li>Required: ' + f + '</li>');
                        });
                    }
                } else {
                    alert('Error generating preview');
                }
            },
            complete: function() {
                $previewBtn.prop('disabled', false).text('Preview Schema');
            }
        });
    });

    // Wikidata Edit Plan
    $('#ks-wikidata-plan-btn').on('click', function() {
        var $container = $('#ks-wikidata-plan-container');
        var $btn = $(this);
        var postId = $('#post_ID').val();

        $btn.prop('disabled', true).text('Generating Plan...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'knowschema_wikidata_plan',
                post_id: postId,
                _ajax_nonce: knowschema_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $container.show().find('textarea').val(response.data.plan);
                } else {
                    alert('Error generating plan');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).text('Export Edit Plan');
            }
        });
    });
});
