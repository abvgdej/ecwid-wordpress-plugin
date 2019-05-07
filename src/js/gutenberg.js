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
    
    EcwidGutenberg.getWrapperId = function( blockId ) {
        return 'ec-store-block-' + blockId;
    };
    
    EcwidGutenberg.refresh = function() {
        var allBlocks = [];
        jQuery('.ec-store-dynamic-block:not([data-ec-store-rendered=true])').each(function() {
            
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
            var id = jQuery(this).attr('data-ec-store-block-id');
            
            var widgetId = 'ec-store-block-' + widget + '-' + id;
            
            var oldChildren = jQuery( '#ec-store-block-' + id ).children();

            if ( widget == 'productbrowser' ) {
                if ( jQuery(this).hasClass('ec-store-with-search') ) {
                    addWidget( 'ec-store-block-search-' + id, 'search', [] );    
                }
                
                if ( jQuery(this).hasClass('ec-store-with-categories') ) {
                    addWidget( 'ec-store-block-categories-' + id, 'categories', [] );
                }
                
                addWidget( 'ec-store-block-productbrowser-' + id, widget, args.split(',') );
                oldChildren.remove();
                
            } else if ( widget === 'product' ) {

                var atts = JSON.parse(jQuery(this).attr('data-attributes'));

                jQuery( '#ec-store-block-' + id ).find('.ecwid.loaded').remove();
                var oldChildren = jQuery( '#ec-store-block-' + id ).children();

                var oldProductContainer = oldChildren.eq(0);
                
                var clone = oldChildren.clone().show();
                var newProductContainer = clone.eq(0).attr('data-single-product-id', atts.id);
                
                newProductContainer.attr('id', 'ec-store-block-product-' + id);
                jQuery(this).append(clone);

                allBlocks[allBlocks.length] = {
                    widgetType: EcwidGutenberg.widgetsMap[widget],
                    id: widgetId,
                    arg: ["id=" + atts.id]
                };
                oldProductContainer
                    .removeClass('ecwid ecwid-Product ecwid-SingleProduct-v2')
                    .removeAttr('data-single-product-id').hide();
                /*
                addWidget( 'ec-store-block-product-' + id, widget, ["id=" + atts.id] );
                /*
                jQuery(this).append(
                    <div id={ "ec-store-block-product-" + blockId } className="ecwid ecwid-SingleProduct-v2 ecwid-Product" data-single-product-id={ atts.id } itemscope itemtype="https://schema.org/Product">
                        { atts.show_picture && <div itemprop="picture"></div> }
                        { atts.show_title && <div className="ecwid-title" itemprop="title"></div> }
                        { atts.show_price &&
                        <div itemtype="https://schema.org/Offer" itemscope itemprop="offers">
                            <div className="ecwid-productBrowser-price ecwid-price" itemprop="price"
                                 data-spw-price-location={ atts.show_price_on_button ? 'button' : '' }></div>
                            <div itemprop="priceCurrency"></div>
                        </div>
                        }
                        { atts.show_options && <div customprop="options"></div> }
                        { atts.show_qty && <div customprop="qty"></div> }
                        { atts.show_addtobag && <div customprop="addtobag"></div> }
                    </div>
                );
                allBlocks[allBlocks.length] = {
                    widgetType: EcwidGutenberg.widgetsMap[widget],
                    id: widgetId,
                    arg: ["id=" + jQuery('#' + id).attr('data-single-product-id')]
                };*/
//                addWidget( widgetId, widget, [] );
            }
            
            jQuery(this)
                .addClass('ec-store-rendered')
                .attr('data-ec-store-rendered', true)
                .removeClass('ec-store-block');
        });
        
        if (allBlocks.length > 0) {
            if (Ecwid && Ecwid.destroy) {
                Ecwid.destroy();
            }
            
            window._xnext_initialization_scripts = allBlocks;
            ecwid_onBodyDone();
        }
    }
});