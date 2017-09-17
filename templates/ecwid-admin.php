<script type="text/javascript">
    jQuery(document.body).addClass('ecwid-wp-closed');
    jQuery('#ecwid-menu-collapse').click(function() {
        jQuery('body').toggleClass('ecwid-wp-closed');
    });

    jQuery('#ecwid-overlay').click(function() {
        jQuery('body').toggleClass('ecwid-wp-closed');
    });

</script>

<script type='text/javascript'>//<![CDATA[
	jQuery(document).ready(function() {
		document.body.className += ' ecwid-no-padding';
		$ = jQuery;
		// Create IE + others compatible event handler
		var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
		var eventer = window[eventMethod];
		var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

		// Listen to message from child window
		eventer(messageEvent,function(e) {
			$('#ecwid-frame').css('height', e.data.height + 'px');
		},false);

		$('#ecwid-frame').attr('src', '<?php echo $iframe_src; ?>');
		ecwidSetPopupCentering('#ecwid-frame');
	});
	//]]>

</script>

<div id="ecwid-overlay"></div>

<div id="ecwid-iframe-wrap"><iframe seamless id="ecwid-frame" frameborder="0" width="100%" height="700" scrolling="yes"></iframe></div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery.ajax(
           {
               url: ajaxurl + '?action=<?php echo Ecwid_Store_Page::WARMUP_ACTION; ?>'
           }
       );
    });


    jQuery('#ecwid-overlay').click(function() {
        jQuery('body').toggleClass('ecwid-wp-closed');
    });
    
    function ecwidSetMenuState() {
        var viewportWidth = getViewportWidth() || 961;

        if ( viewportWidth <= 782  ) {
            menuState = 'responsive';
        } else {
            menuState = 'open';
        }
        
        $document.trigger( 'wp-menu-state-set', { state: menuState } );
    }

    // Set the menu state when the window gets resized.
    $document.on( 'wp-window-resized.set-menu-state', ecwidSetMenuState );

</script>
<?php require_once ECWID_PLUGIN_DIR . 'templates/admin-footer.php'; ?>