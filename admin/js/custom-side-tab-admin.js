jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.color-picker').wpColorPicker();

    // Handle media uploads
    $(document).on('click', '.upload-icon', function(e) {
        e.preventDefault();
        var button = $(this);
        var frame = wp.media({
            title: 'Select or Upload Icon',
            button: {
                text: 'Use this icon'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            button.prev('.icon-url').val(attachment.url);
        });

        frame.open();
    });

    // Add new item
    $('#add-new-item').on('click', function() {
        var index = $('.tab-item').length;
        var template = `
            <div class="tab-item">
                <h4>Tab Item ${index + 1}</h4>
                <p>
                    <label>Icon:</label><br>
                    <input type="text" name="cst_tab_items[${index}][icon]" 
                           value="" class="regular-text icon-url" />
                    <button type="button" class="button upload-icon">Upload Icon</button>
                </p>
                <p>
                    <label>Text:</label><br>
                    <input type="text" name="cst_tab_items[${index}][text]" 
                           value="" class="regular-text" />
                </p>
                <p>
                    <label>Link:</label><br>
                    <input type="text" name="cst_tab_items[${index}][link]" 
                           value="" class="regular-text" />
                </p>
                <p>
                    <label>Open in:</label><br>
                    <select name="cst_tab_items[${index}][target]">
                        <option value="_self">Same Window</option>
                        <option value="_blank">New Window</option>
                    </select>
                </p>
                <button type="button" class="button remove-item">Remove Item</button>
            </div>
        `;
        $('#tab-items-container').append(template);
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.tab-item').remove();
        // Reindex remaining items
        $('.tab-item').each(function(index) {
            $(this).find('h4').text('Tab Item ' + (index + 1));
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                }
            });
        });
    });
}); 