debugger;
jQuery(document).ready(function() {
    
    function disableProductBrowserBlocks() {
        debugger;
        var found = false;
        for( var i = 0; i < EcwidGutenberg.productBrowserBlocks.length; i++) {
            var block = EcwidGutenberg.productBrowserBlocks[i];

            if ( jQuery('[data-type="' + block + '"').length > 0) {
                found = true;
                break;
            }
        }

        if ( found ) {
            for( var i = 0; i < EcwidGutenberg.productBrowserBlocks.length; i++) {
                var block = EcwidGutenberg.productBrowserBlocks[i];
                block = block.replace('/', '-');

                jQuery( '.editor-block-list-item-' + block )
                    .attr('disabled', 'disabled')
                    .css({
                        'pointer-events': 'none',
                        'user-select': 'none'
                    });
            }
        }
    }
    
    setTimeout(function() {
        jQuery( '.editor-inserter__toggle' ).click(function() {
            if ( jQuery(this).attr('aria-expanded') != "false" ) return;

            var hits = 0;
            var interval = setInterval(function() {
                if ( disableProductBrowserBlocks() ) {
                    jQuery( '.components-panel__body-toggle', '.editor-inserter__toggle' ).click(function() {
                        if ( jQuery(this).attr('aria-expanded') != true ) return;
                        
                        var sectionInterval = setInterval(function() {
                            if (disableProductBrowserBlocks()) {
                                clearInterval(sectionInterval);
                            }
                        },20);
                        
                    });
                    
                    clearInterval(interval);
                }
       }, 20);
    });
    }, 200);
});