(function($) {
    'use strict';

    // Initialize color pickers
    function initColorPickers() {
        $('.esm-color-picker').wpColorPicker({
            change: function(event, ui) {
                updatePreview();
            }
        });
    }

    // Initialize sortable functionality
    function initSortable() {
        $('.esm-sortable').sortable({
            handle: '.handle',
            update: function(event, ui) {
                updatePreview();
            }
        });
    }

    // Update preview when settings change
    function updatePreview() {
        const iconSize = $('input[name="esm_settings[icon_size]"]:checked').val();
        const iconColor = $('#esm_icon_color').val();
        const hoverColor = $('#esm_hover_color').val();
        
        $('.esm-preview').attr('data-size', iconSize);
        
        // Update colors
        const style = document.createElement('style');
        style.textContent = `
            .esm-preview .esm-icon { color: ${iconColor}; }
            .esm-preview .esm-icon:hover { color: ${hoverColor}; }
        `;
        
        $('head').append(style);
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initColorPickers();
        initSortable();
        
        // Update preview when radio buttons change
        $('input[name="esm_settings[icon_size]"]').change(function() {
            updatePreview();
        });
        
        // Initial preview update
        updatePreview();
    });

})(jQuery);
