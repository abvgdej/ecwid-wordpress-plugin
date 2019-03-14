(function(){
    var originalAddClassMethod = jQuery.fn.addClass;
    jQuery.fn.addClass = function(){
        var result = originalAddClassMethod.apply( this, arguments );
        jQuery(this).trigger('change');
        return result;
    }

    var originalRemoveClassMethod = jQuery.fn.removeClass;
    jQuery.fn.removeClass = function(){
        var result = originalRemoveClassMethod.apply( this, arguments );
        jQuery(this).trigger('change');
        return result;
    }

})();

jQuery(document).ready(function() {
    if (typeof Ecwid == 'undefined') return;

    var scroll = '#scroll-to-top',
    	minicart = '.ec-minicart';

    if( jQuery( scroll + ':visible' ).length > 0 ) {
		Ecwid.OnCartChanged.add( function( cart ) {

			var overlap = false,
				overlap_init = false;

			if( !jQuery( minicart ).parent().data('showEmptyCart') && !cart.productsQuantity ) {
				jQuery( scroll ).unbind( 'change.Overlap' );
				return;
			}

			jQuery( scroll ).on( 'change.Overlap', function() {

				var rect1 = jQuery( this ).get(0).getBoundingClientRect(),
					rect2 = jQuery( minicart ).get(0).getBoundingClientRect();
				
				if( !overlap_init ) {
					overlap = !(rect1.right < rect2.left || 
		                rect1.left > rect2.right || 
		                rect1.bottom < rect2.top || 
		                rect1.top > rect2.bottom);
				}

				if( overlap ){
					if( jQuery( this ).hasClass( 'displayed' ) ){
						jQuery( minicart ).css({
							'bottom': rect1.height + parseInt( jQuery( this ).css( 'bottom' ) ) + 10
						});
						overlap_init = true;
					} else {
						jQuery( minicart ).css('bottom', jQuery( minicart ).parent().data( 'verticalIndent' ) );
					}
				}
			});
			
			setTimeout(function(){
				if( jQuery( minicart ).length ) jQuery( document ).trigger('scroll');
			}, 500);

		});
	}
});