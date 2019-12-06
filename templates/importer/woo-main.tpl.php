
<?/*
<div class="wrap ecwid-importer state-<?php echo $this->_is_token_ok() ? 'woo-initial' : 'no-token'; ?>">
    <h1><?php echo sprintf( __( 'Import your products from WooCommerce to %s', 'ecwid-shopping-cart' ), Ecwid_Config::get_brand() ); ?></h1>

    <p><?php echo sprintf( __( 'This import will copy your WooCommerce products and categories to your %s store.', 'ecwid-shopping-cart' ), Ecwid_Config::get_brand() ); ?></p>

    <?php if ( !Ecwid_Config::is_wl() ): ?>
    <p><?php echo sprintf( __( '<b>Important note:</b> import creates new products.  please mind the maximum number of products and categories you can add to your store. This import tool will automatically stop when you reach the limit. To learn the current store limit or increase it, please see the "<a %s>Billing & Plans</a>" page in your store control panel. ', 'ecwid-shopping-cart' ), 'href="admin.php?page=ec-store-admin-billing"' ); ?></p>
    <?php endif; ?>
    
    <h2><?php _e( 'Import summary.', 'ecwid-shopping-cart' ); ?></h2>
    <p>
		<?php
			_e( 'Your WooCommerce store has ', 'ecwid-shopping-cart' );
            echo $this->_get_products_categories_message(
                Ecwid_Importer::count_woo_products(),
                Ecwid_Importer::count_woo_categories()
            );
		?>
    </p>
    <p>
		<?php
		printf(
			__( 'Your %s store has ', 'ecwid-shopping-cart' ),
			Ecwid_Config::get_brand()
		);
		echo $this->_get_products_categories_message(
			Ecwid_Importer::count_ecwid_products(),
			Ecwid_Importer::count_ecwid_categories()
        );		
		?>
    </p>
    <p>
		<?php
		echo sprintf(
			__( 'After import, your %s store will have ', 'ecwid-shopping-cart' ),
			Ecwid_Config::get_brand()
		);
		echo $this->_get_products_categories_message(
			Ecwid_Importer::count_ecwid_products() + Ecwid_Importer::count_woo_products(),
			Ecwid_Importer::count_ecwid_categories() + Ecwid_Importer::count_woo_categories()
		);
		?>
    </p>

	<?php if ( count( Ecwid_Importer::get_ecwid_demo_products() ) > 0 && Ecwid_Importer::count_ecwid_demo_products() < Ecwid_Importer::count_ecwid_products() ): ?>
    <h2><?php _e( 'Import settings.', 'ecwid-shopping-cart' ); ?></h2>
    <p>
		<label><input type="checkbox" class="import-settings" name="<?php echo Ecwid_Importer::SETTING_DELETE_DEMO; ?>"><?php _e( 'Remove demo products', 'ecwid-shopping-cart' ); ?></label>
    </p>
    <?php endif; ?>

	<?php if ( Ecwid_Importer::count_ecwid_demo_products() < Ecwid_Importer::count_ecwid_products() ): ?>
    <p>
        <label><input type="checkbox" class="import-settings" name="<?php echo Ecwid_Importer::SETTING_UPDATE_BY_SKU; ?>"><?php _e( 'Overwrite existing products with matching SKU', 'ecwid-shopping-cart' ); ?></label>
    </p>
	<?php endif; ?>

    <div class="importer-state importer-state-woo-initial">
		<?php require __DIR__ . '/woo-initial.tpl.php'; ?>
	</div>

    <div class="importer-state importer-state-no-token">
		<?php require __DIR__ . '/import-no-token.tpl.php'; ?>
    </div>

    <div class="importer-state importer-state-woo-in-progress">
		<?php require __DIR__ . '/woo-in-progress.tpl.php'; ?>
	</div>

	<div class="importer-state importer-state-woo-complete">
		<?php require __DIR__ . '/woo-complete.tpl.php'; ?>
	</div>
</div>*/?>

<script type='text/javascript'>//<![CDATA[
    jQuery(document.body).addClass('ecwid-no-padding');
    jQuery(document.body).css({
    	'font-size': '13px'
    });
    jQuery('#wpbody').css({
    	'background-color': 'rgb(240, 242, 244)'
    });
//]]>
</script>

<div class="settings-page">
	<div class="settings-page__header">
		<div class="settings-page__titles settings-page__titles--left">
			<h1 class="settings-page__title"><?php echo sprintf( __( 'Import your products from WooCommerce to %s', 'ecwid-shopping-cart' ), Ecwid_Config::get_brand() ); ?></h1>
			<div class="settings-page__subtitle"><?php echo sprintf( __( 'This import will copy your WooCommerce products and categories to your %s store.', 'ecwid-shopping-cart' ), Ecwid_Config::get_brand() ); ?></div>
		</div>


		<div class="a-card-stack">

			<div class="a-card a-card--normal">
				<div class="a-card__paddings">
					<div class="cta-block">
						<div class="cta-block__central">
							<div class="cta-block__title"><?php _e( 'Import summary', 'ecwid-shopping-cart' ); ?></div>
							<div class="cta-block__content">

							    <div><?php
								_e( 'Your WooCommerce store has ', 'ecwid-shopping-cart' );
					            echo $this->_get_products_categories_message(
					                Ecwid_Importer::count_woo_products(),
					                Ecwid_Importer::count_woo_categories()
					            );
								?></div>
							
								<div><?php
								echo sprintf(
									__( 'After import, your %s store will have ', 'ecwid-shopping-cart' ),
									Ecwid_Config::get_brand()
								);
								echo $this->_get_products_categories_message(
									Ecwid_Importer::count_ecwid_products() + Ecwid_Importer::count_woo_products(),
									Ecwid_Importer::count_ecwid_categories() + Ecwid_Importer::count_woo_categories()
								);
								?></div>

							</div>
						</div>
						<div class="cta-block__cta">
							<button type="button" class="btn btn-primary btn-medium" id="ecwid-importer-woo-go">
								<span><?php _e( 'Start import', 'ecwid-shopping-cart' ); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="a-card a-card--normal">
				<div class="a-card__paddings">
					<div class="cta-block">
						<div class="cta-block__central">
							<div class="cta-block__title"><?php _e( 'Import settings', 'ecwid-shopping-cart' ); ?></div>
							<div class="cta-block__content">

								<?php //if ( count( Ecwid_Importer::get_ecwid_demo_products() ) > 0 && Ecwid_Importer::count_ecwid_demo_products() < Ecwid_Importer::count_ecwid_products() ): ?>
									<div class="custom-checkbox">
										<label>
											<input 
												type="checkbox" 
												value="on" 
												tabindex="0" 
												class="custom-checkbox__input" 
												name="<?php echo Ecwid_Importer::SETTING_DELETE_DEMO; ?>"
											/>
											<span class="custom-checkbox__label"></span>
											<span><?php _e( 'Remove demo products', 'ecwid-shopping-cart' ); ?></span>
										</label>
									</div>
								<?php //endif; ?>

								<?php //if ( Ecwid_Importer::count_ecwid_demo_products() < Ecwid_Importer::count_ecwid_products() ): ?>
									<div class="custom-checkbox">
										<label>
											<input 
												type="checkbox" 
												value="on" 
												tabindex="0" 
												class="custom-checkbox__input" 
												name="<?php echo Ecwid_Importer::SETTING_UPDATE_BY_SKU; ?>"
											/>
											<span class="custom-checkbox__label"></span>
											<span><?php _e( 'Overwrite existing products with matching SKU', 'ecwid-shopping-cart' ); ?></span>
										</label>
									</div>
								<?php //endif; ?>

							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="a-card a-card--normal">
				<div class="a-card__paddings">
					<?php if ( !Ecwid_Config::is_wl() ): ?>
				    <div><?php echo sprintf( __( '<b>Important note:</b> import creates new products. Please mind the maximum number of products and categories you can add to your store. This import tool will automatically stop when you reach the limit. To learn the current store limit or increase it, please see the "<a %s>Billing & Plans</a>" page in your store control panel. ', 'ecwid-shopping-cart' ), 'href="admin.php?page=ec-store-admin-billing"' ); ?></div>
				    <?php endif; ?>
				</div>
			</div>
			
		</div>

	</div>
</div>
