(function($) {
    'use strict';

    // Lazy load the social media widget
    function initSocialWidget() {
        const widget = $('.esm-widget');
        
        // Add loading class
        widget.addClass('loading');
        
        // Remove loading class after a small delay
        setTimeout(() => {
            widget.removeClass('loading');
        }, 300);
        
        // Initialize intersection observer for lazy loading
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.src = entry.target.dataset.src;
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            // Observe all SVG images
            widget.find('img[data-src]').each(function() {
                observer.observe(this);
            });
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initSocialWidget();
    });

})(jQuery);
