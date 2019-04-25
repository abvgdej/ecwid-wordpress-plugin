EcwidGutenberg.refresh = function() {};
window.ec = window.ec || {};
window.ec.storefront = window.ec.storefront || {};
window.ec.config = window.ec.config || {};
window.ec.config.chameleon = window.ec.config.chameleon || {};
window.ec.config.chameleon.colors = window.ec.config.chameleon.colors || {};

jQuery(document).ready(function() {
    
    function disableProductBrowserBlocks() {
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

    window.ecwid_script_defer = true;
    window.ecwid_dynamic_widgets = true;
    
    EcwidGutenberg.refresh = function() {
        var allBlocks = [];
        jQuery('.ec-store-dynamic-block:not([data-ec-store-rendered])').each(function() {
            
            var $that = jQuery(this);
            addWidget = function(id, type, arg) {
                $that.append( 
                    '<div id="' + id + '" class=ecwid-shopping-cart-' + type + '"></div>'
                );
                
                allBlocks[allBlocks.length] = {
                    widgetType: EcwidGutenberg.widgetsMap[type],
                    id: id,
                    arg: arg
                };
            };
            var widget = jQuery(this).attr('data-ec-store-widget');
            var args = jQuery(this).attr('data-ec-store-args');
            var id = jQuery(this).attr('data-ec-store-id');
            
            var widgetId = 'ec-store-block-' + widget + '-' + id;
                
            var widgetType = EcwidGutenberg.widgetsMap[widget];
            
            var oldChildren = jQuery( '#ec-store-block-' + id ).children();

            if ( widget == 'productbrowser' ) {
                if ( jQuery(this).hasClass('ec-store-with-search') ) {
                    addWidget( 'ec-store-block-search-' + id, 'search', [] );    
                }
                
                if ( jQuery(this).hasClass('ec-store-with-categories') ) {
                    addWidget( 'ec-store-block-categories-' + id, 'categories', [] );
                }
                
                debugger;
                addWidget( 'ec-store-block-productbrowser-' + id, widget, args.split(',') );
            }
            
            oldChildren.remove();
            jQuery(this)
                .addClass('ec-store-rendered')
                .attr('data-ec-store-rendered', true)
                .removeClass('ec-store-block');
        });
        
        if (allBlocks.length > 0) {
            window._xnext_initialization_scripts = allBlocks;
            ecwid_onBodyDone();
        }
    }
});