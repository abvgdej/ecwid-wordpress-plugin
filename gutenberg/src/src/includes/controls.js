const {
    ButtonGroup,
    Button,
    BaseControl,
    Toolbar,
    ToggleControl,
} = wp.components;

const { __ } = wp.i18n;

import {EcwidIcons} from "../icons.js";
import { ColorControl } from "./color.js";

function EcwidControls(declaration, properties) {
    
    const attributes = properties.attributes;

    let buildButtonGroup = function(props, name, label, items) {
        return <BaseControl label={label}>
            <ButtonGroup>
                { items.map( function(item) {
                    return <Button isDefault isButton
                                   isPrimary={ attributes[name] === item.value }
                                   onClick={ () => props.setAttributes( { [name]: item.value } ) }>
                        { item.title }
                    </Button>
                } ) }
            </ButtonGroup>
        </BaseControl>;
    };
    
    let buildToggle = function(props, name, label) {
        return <ToggleControl
            label={ label }
            checked={ props.attributes[name] }
            onChange={ () => props.setAttributes( { [name]: ! props.attributes[name] } ) }
        />
    };

    let buildSelect = function(props, name, label, items) {
        return <BaseControl label={ label }>
            <select onChange={ (event) => { props.setAttributes( { [name]:event.target.value } ) } }>
                { items.map( function(item) {
                    return <option value={item.value} selected={ parseInt( props.attributes[name] ) == item.value }>{item.title}</option>
                })}
            </select>
        </BaseControl>;
    };

    let buildTextbox = function(props, name, label) {
        return <BaseControl label={ label }>
            <input type="text" value={props.attributes[name]} onChange={ (event) => { props.setAttributes( { [name]:event.target.value } ) } } />
        </BaseControl>;
    };
    
    let buildToolbar = function(props, name, label, items) {
        return <BaseControl label={label}>
            <Toolbar
                controls={ items.map( function(item) {
                    return {
                        icon: EcwidIcons[item.icon],
                        title: item.title,
                        isActive: props.attributes[name] === item.value,
                        className: 'ecwid-toolbar-icon',
                        onClick: () =>
                            props.setAttributes( { [name]: item.value } )
                    }
                } ) }
            />
        </BaseControl>;
    }
    
    return {
        buttonGroup: function(name) {
            const item = declaration[name];
            
            return buildButtonGroup( properties, item.name, item.title, item.values );
        },
        toggle: function(name) {
            const item = declaration[name];
            
            return buildToggle( properties, item.name, item.title );
        },
        select: function(name, title=null) {
            const item = declaration[name];
            
            return buildSelect( properties, item.name, title ? title : item.title, item.values );
        },
        textbox: function(name) {
            const item = declaration[name];

            return builtTextbox( properties, item.name, item.title );
        },
        toolbar: function(name) {
            const item = declaration[name];

            return buildToolbar( properties, item.name, item.title, item.values );
        },
        color: function(name) {
            return <ColorControl props={ properties } name={ name } title={ declaration[name].title } />
        },
        defaultCategoryId: function(name) {
            const item = declaration[name];

            if ( item.values && item.values.length > 1 ) {
                return buildSelect( properties, item.name, item.title, item.values );
            } else {
                return buildTextbox( properties, item.name, item.title );
            }            
        }
    }
    
}

function EcwidInspectorSubheader(title) {
    return <div className="ec-store-inspector-subheader-row">
        <label className="ec-store-inspector-subheader">
            { title }
        </label>
    </div>
};

function trackDynamicProperties( props, dynamicProps ) {
    
    const blockProps = props.props;
    const dynamicProperties = dynamicProps.split(' ');
    
    const blockId = blockProps.clientId;
    const wrapperId = '#ec-store-block-' + blockId;
    
    
    const storedData = jQuery(wrapperId).data('ec-store-block-stored-properties');
    
    let changed = false;

    let propValues = {};
    
    for ( let i = 0; i < dynamicProperties.length; i++ ) {
        let name = dynamicProperties[i];
        
        if ( !storedData || blockProps.attributes[name] != storedData[name] ) {
            changed = true;
        }
        
        propValues[name] = blockProps.attributes[name];
    }

    // this weird condition takes place only on second edit. First one creates block div, so there is no block yet
    // second call saves params, but there is no need to refresh, and at the same time stored props are not set
    // thus we return false but save params
    const secondEditCall = jQuery(wrapperId).length > 0 && !jQuery(wrapperId).data('ec-store-block-stored-properties');

    jQuery(wrapperId).data('ec-store-block-stored-properties', propValues);

    if ( secondEditCall ) {
        return false;
    }

    return changed;
}

function EcwidSingleProductBlock( props ) {
    const blockProps = props.props;
    const __attributeValues = blockProps.attributes;
    const __attributesDefinition = props.attributes;
    const blockId = blockProps.clientId;
    const widget = props.widget;
    const render = typeof props.render === 'undefined' ? true : props.render;
    
    const wrapperId = EcwidGutenberg.getWrapperId( blockId );
    let wrapperClassName = 'ec-store-block ec-store-dynamic-block';
    
    if ( !__attributeValues.id || !render ) {
        return <div id={ wrapperId } className={ wrapperClassName }>
            { props.children }
        </div>;
    }
    
    let atts = {};
    
    for ( let name in __attributesDefinition ) {
        if ( typeof (__attributeValues[name] ) === 'undefined' ) {
            atts[name] = __attributesDefinition[name].default;
        } else {
            atts[name] = __attributeValues[name];
        }
    }
    atts.id = __attributeValues.id;
    
    setTimeout( function() {
        EcwidGutenberg.refresh()
    });
    
    let widgetClassName = 'ecwid ecwid-SingleProduct-v2 ecwid-Product ecwid-Product-' + atts.id;

    return <div id={ wrapperId } className={ wrapperClassName } data-attributes={ JSON.stringify(atts)} data-ec-store-widget="product" data-ec-store-block-id={ blockId }>
        <div className={ widgetClassName } data-single-product-id={ atts.id } itemscope itemtype="https://schema.org/Product">
            { atts.show_picture && <div itemprop="picture"></div> }
            { atts.show_title && <div className="ecwid-title" itemprop="title"></div> }
            { atts.show_price &&
            <div itemtype="https://schema.org/Offer" itemscope itemprop="offers">
                <div className="ecwid-productBrowser-price ecwid-price" itemprop="price"
                     data-spw-price-location={ props.show_price_on_button ? 'button' : '' }></div>
                <div itemprop="priceCurrency"></div>
            </div>
            }
            { atts.show_options && <div customprop="options"></div> }
            { atts.show_qty && <div customprop="qty"></div> }
            { atts.show_addtobag && <div customprop="addtobag"></div> }
        </div>
    </div>
}

function EcwidProductBrowserBlock( props ) {
    
    const blockProps = props.props;
    const attributes = props.attributes;
    const blockId = blockProps.clientId;
    const showCats = blockProps.attributes.show_categories;
    const showSearch = blockProps.attributes.show_search;
    const render = typeof props.render === 'undefined' ? true : props.render;

    const wrapperId = EcwidGutenberg.getWrapperId( blockId );

    let widget = "productbrowser";
    
    let args = '';
    if ( blockProps.attributes.default_category_id ) {
        args = "defaultCategoryId=" + blockProps.attributes.default_category_id;
    } else if ( blockProps.attributes.default_product_id ) {
        args = "defaultProductId=" + blockProps.attributes.default_product_id;
    }
    
    let className = "ec-store-generic-block ec-store-dynamic-block";
    
    if ( !render || !document.getElementById(wrapperId) || !document.getElementById(wrapperId).getAttribute('data-ec-store-rendered') ) {
        className += " ec-store-block";
    }

    if ( showCats ) {
        className += " ec-store-with-categories";
    }

    if ( showSearch ) {
        className += " ec-store-with-search";
    }

    let changed = trackDynamicProperties( props, "default_product_id default_category_id show_search show_categories" );

    if ( render && changed ) {
        
        document.getElementById(wrapperId) 
            && document.getElementById(wrapperId).removeAttribute('data-ec-store-rendered');
        
        setTimeout( function() {
            EcwidGutenberg.refresh()
        });
    }

    window.ec.config.chameleon.colors = [];

    Object.keys( attributes ).map( (i) => {
        let attr = attributes[i];

        let value = typeof blockProps.attributes[attr.name] !== 'undefined' ? 
            blockProps.attributes[attr.name] : attributes.default;
        
        if ( i.indexOf('chameleon') !== -1 ) {
            if ( value ) {
                window.ec.config.chameleon.colors['color-' + i.substr(16)] = value;
            } 
        } else {
            window.ec.storefront[attr.name] = value;
        }
    });

    if ( Ecwid && Ecwid.refreshConfig ) {
        Ecwid.refreshConfig();
    }
    
    const alreadyRendered = jQuery('#' + wrapperId).attr('data-ec-')
    
    return <div 
            className={ className } 
            data-ec-store-widget={ widget } 
            data-ec-store-block-id={ blockId } 
            data-ec-store-args={ args }
            data-ec-store-with-search={ showSearch }
            data-ec-store-with-categories={ showCats }
            id={ wrapperId }>

            <div className="ec-store-block-header">
                { props.icon }
                { props.title }
            </div>
            <div className="ec-store-block-content">
                { props.children }
            </div>
            { props.showDemoButton &&
            <div>
                <a className="button button-primary" href="admin.php?page=ec-store">{ __( 'Set up your store', 'ecwid-shopping-cart') }</a>
            </div>
            }
    </div>
};

function EcwidImage( props ) {
    const url = EcwidGutenbergParams.imagesUrl + props.src;
    
    return <img src={ url } />
}

export { EcwidControls, EcwidInspectorSubheader, EcwidImage, EcwidProductBrowserBlock, EcwidSingleProductBlock };
