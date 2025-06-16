jQuery(document).ready(function($) {
    // Set hover color from settings
    const style = document.createElement('style');
    style.textContent = `
        :root {
            --hover-color: ${cstSettings.hover_color || '#e67e22'};
        }
    `;
    document.head.appendChild(style);

    // Toggle side tab
    $('#side-tab-toggle').on('click', function() {
        $('#custom-side-tab').toggleClass('collapsed');
    });

    // Close side tab when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#custom-side-tab').length) {
            $('#custom-side-tab').addClass('collapsed');
        }
    });
}); 