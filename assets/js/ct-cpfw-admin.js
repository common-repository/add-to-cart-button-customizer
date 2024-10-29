/**
 * All of the code for your public-facing JavaScript source.
 * should reside in this file.
 *
 * Note: It has been assumed you will write jQuery code here, so the.
 * $ function reference has been prepared for usage within the scope.
 * of this function.
 *
 * This enables you to define handlers, for when the DOM is ready:.
 *
 * $(function() {.
 *
 * });.
 *
 * @package Add To Cart Button Customizer.
 *
 * ...and/or other possibilities.
 *
 * Ideally, it is not considered best practise to attach more than a.
 * single DOM-ready or window-load handler for a particular page.
 * Although scripts in the WordPress core, Plugins and Themes may be.
 * practising this, we should strive to set a better example in our own work.
 */

jQuery( 'document' ).ready(
	function($) {
		'use strict';


		$(document).on('click','.ct-premium-module-link',function(e){
			e.preventDefault();
			if ( $(this).data('class_name') ) {
				$('.'+$(this).data('class_name')).fadeIn('slow');
			}
		});

		$(document).on('click','.ct-premium-plugin-cross-btn',function(e){
			e.preventDefault();
			if ( $(this).data('class_name') ) {
				$('.'+$(this).data('class_name')).fadeOut('slow');
			}
		});


		$( document ).on(
			'click',
			'.ct-cpfw-upload-icon',
			function (e) {
				e.preventDefault();

				var Upload_Files = wp.media(
					{
						title: 'Upload Icon',

						multiple: false,
					}
				);

				Upload_Files.on(
					'select',
					function(){

						var attachments = Upload_Files.state().get( 'selection' ).map(
							function( attachment ) {

								attachment.toJSON();
								return attachment;

							}
						);

						// loop through the array and do things with each attachment.

						if (attachments[0] && attachments[0].attributes && attachments[0].attributes.url ) {

							var url = attachments[0].attributes.url;

							jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ct_catcbfw_add_to_crt_btn_uploaded_icon]' ).val( url );
							Check_Image();

						}

					}
				);

				Upload_Files.open();
			}
		);
		Check_Image();
		jQuery( document ).on(
			'click',
			'.ct-cpfw-remove-icon',
			function(){
				jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ct_catcbfw_add_to_crt_btn_uploaded_icon]' ).val( null );
				Check_Image();

			}
		);
		Ct_Catcbfw_Add_To_Cart_Button_Icon()
		$( document ).on(
			'click',
			'input[name="ct_catcbfw_add_to_cart_button_icon"]' ,
			function(){
				Ct_Catcbfw_Add_To_Cart_Button_Icon();
			}
		)

		Discount_Type();

		jQuery( document ).on(
			'change',
			'.selected_prd_discounted_type',
			function(){
				Discount_Type();

			}
		);

		Ct_Catcbfw_Add_To_Cart_Style();

		jQuery( document ).on(
			'click',
			'.check_custom_the_styling',
			function(){
				Ct_Catcbfw_Add_To_Cart_Style();

			}
		);

		jQuery( document ).on(
			'click',
			'.ct_add_chain_prd',
			function() {
				var selected_options = [];

				$( this ).closest( 'td' ).find( 'select.ct_catcbfw_selected_chain_prd' ).children( 'option:selected' ).each(
					function(){
						if ( $( this ).val() ) {
							selected_options[ $( this ).val() ] = $( this ).val();
						}
					}
				);

				selected_options = selected_options.filter( Boolean );

				jQuery.ajax(
					{
						url: php_var.admin_url,
						type: 'POST',
						data: {
							action 		: 'ct_add_chain_prd',
							nonce 		: php_var.nonce,
							rule_id 	: $( this ).data( 'rule_id' ),
							product_ids : selected_options,
						},
						success: function(response){

							if ( response["new_html"] ) {

								jQuery( '.ct-cp-woo-product-bundle-table table tbody' ).append( response["new_html"] );

							}
							Discount_Type();

						}

					}
				);

			}
		);

		jQuery( document ).on(
			'click',
			'.ct-remove-woo-product-bundle',
			function() {

				var current_btn = $( this );

				jQuery.ajax(
					{
						url: php_var.admin_url,
						type: 'POST',
						data: {
							action 		: 'ct_delete_woo_bundle_product',
							nonce 		: php_var.nonce,
							rule_id 	: $( this ).data( 'main_product_id' ),
							product_id : $( this ).data( 'product_id' ),
						},
						success: function(response){

							if ( response["delete"] ) {

								current_btn.closest( 'tr' ).remove();

							}

						}

					}
				);

			}
		);

		// ------------------------------- Delete Image ---------------------------------------------------.

		$( '#select_user_from_switch' ).select2(
			{
				multiple 	: 	true,
				placeholder : 'Select User Roles',
			}
		);
		$( '.ct_catcbfw_email_to_order_status' ).select2(
			{
				multiple 	: 	true,
				placeholder : 'Select Order Statuses',
			}
		);
		$( '.ct_live_Search' ).select2(
			{
				multiple 	: 	true,
			}
		);
		$( '.ct_catcbfw_countries' ).select2(
			{
				multiple 	: 	true,
				placeholder : 'Select Countries',
			}
		);

		var ajaxurl = php_var.admin_url;
		var nonce   = php_var.nonce;

		// product search.
		jQuery( '.ct_catcbfw_product_live_search' ).select2(
			{
				ajax: {
					url: ajaxurl, // AJAX URL is predefined in WordPress admin.
					dataType: 'json',
					type: 'POST',
					delay: 20, // Delay in ms while typing when to perform a AJAX search.
					data: function (params) {
						return {
							q: params.term, // search query.
							action: 'ct_catcbfw_product_search', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file).
							nonce: nonce // AJAX nonce for admin-ajax.php.
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
							// data is the array of arrays, and each of them contains ID and the Label of the option.
							$.each(
								data,
								function ( index, text ) {
									// do not forget that "index" is just auto incremented value.
									options.push( { id: text[0], text: text[1]  } );
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose Products',
			}
		);
		jQuery( '.ct_catcbfw_category_live_search' ).select2(
			{
				ajax: {
					url: ajaxurl, // AJAX URL is predefined in WordPress admin.
					dataType: 'json',
					type: 'POST',
					delay: 20, // Delay in ms while typing when to perform a AJAX search.
					data: function (params) {
						return {
							q: params.term, // search query.
							action: 'ct_catcbfw_category_search', // AJAX action for admin-ajax.php aftaxsearchUsers(is function name which isused in adminn file).
							nonce: nonce // AJAX nonce for admin-ajax.php.
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
							// data is the array of arrays, and each of them contains ID and the Label of the option.
							jQuery.each(
								data,
								function ( index, text ) {
									// do not forget that "index" is just auto incremented value.
									options.push( { id: text[0], text: text[1]  } );
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose category',
			}
		);

	}
);




function Discount_Type(){

	jQuery( '.selected_prd_discounted_type' ).each(
		function() {

			jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'readonly',true );
			jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'min' , '0' );

			if ( jQuery( this ).children( 'option:selected' ).val() === 'percentage_discount' || jQuery( this ).children( 'option:selected' ).val() === 'fixed_discount' ) {

				jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'readonly' , false );
				jQuery( this ).closest( 'tr' ).find( '.selected_prd_detail_discount_amount' ).prop( 'min' , '1' );

			}

		}
	);

}




function Ct_Catcbfw_Add_To_Cart_Style() {

	jQuery( '.check_custom_the_styling' ).each(
		function(){

			if ( jQuery( this ).data( 'hide_class' ) ) {

				var hide_class = jQuery( this ).data( 'hide_class' );
				jQuery( '.' + hide_class ).closest( 'tr' ).hide();
			}

		}
	);

	jQuery( '.check_custom_the_styling' ).each(
		function(){

			if ( jQuery( this ).is( ':checked' ) && jQuery( this ).data( 'hide_class' ) ) {

				var hide_class = jQuery( this ).data( 'hide_class' );
				jQuery( '.' + hide_class ).closest( 'tr' ).show();
			}

		}
	);

}
function Ct_Catcbfw_Add_To_Cart_Button_Icon() {

	jQuery( 'input[name=ct_catcbfw_add_to_crt_btn_icon_class]' ).closest( 'tr' ).hide();
	jQuery( 'input[name=ct_catcbfw_add_to_crt_btn_uploaded_icon]' ).closest( 'tr' ).hide();

	if ( 'custom_icon_class' == jQuery( 'input[name="ct_catcbfw_add_to_cart_button_icon"]:checked' ).val()) {
		jQuery( 'input[name=ct_catcbfw_add_to_crt_btn_icon_class]' ).closest( 'tr' ).show();

	} else if ( 'upload_custom_icon' == jQuery( 'input[name="ct_catcbfw_add_to_cart_button_icon"]:checked' ).val()) {
		jQuery( 'input[name=ct_catcbfw_add_to_crt_btn_uploaded_icon]' ).closest( 'tr' ).show();

	}

}


function Check_Image() {

	jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).prop( 'src','' );
	jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).fadeOut( 'slow' );

	if ( jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ct_catcbfw_add_to_crt_btn_uploaded_icon]' ).val() ) {

		var url = jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'input[name=ct_catcbfw_add_to_crt_btn_uploaded_icon]' ).val();

		jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).prop( 'src',url );
		jQuery( '.ct-cpfw-upload-icon' ).closest( 'tr' ).find( 'img' ).fadeIn( 'slow' );

	}

}
