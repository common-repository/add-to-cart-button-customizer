<?php
/**
 * Front File .
 *
 * @package Add To Cart Button Customizer
 *
 * @version 2.0.0
 */

	/**
	 * Check if someone direct access do not run code.
	 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


	/**
	 * Check class Exist or not.
	 */

if ( ! class_exists( 'Catcbfw_Front' ) ) {

	/**
	 * Define class.
	 */
	class Catcbfw_Front {

		/**
		 * Constructor.
		 */
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'cloud_catcfw_enque_scripts' ) );

			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'cloud_catcfw_woocommerce_add_to_cart_button_text' ), 10, 2 );
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'cloud_catcfw_filter_loop_add_to_cart_link' ), 10, 2 );

		}

		/**
		 * Add js and css file.
		 */
		public function cloud_catcfw_enque_scripts() {

			wp_enqueue_style( 'ka_up_upload_files_link_ty', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', false, '1.0', false );

			wp_enqueue_style( 'front_css1', CT_CATCBFW_URL . '/assets/css/ct-cpfw-front.css', false, '1.1.0' );

			wp_enqueue_script( 'front_js', CT_CATCBFW_URL . '/assets/js/ct-cpfw-front.js', array( 'jquery' ), '1.1.0', false );

			$_scripts = array(
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'ct_catcbfw_nonce' ),
			);

			wp_localize_script( 'front_js', 'php_var', $_scripts );

		}

		/**
		 * Adding columns in custom post type table .
		 *
		 * @param string $button button of current product.
		 * @param object $product object of current product.
		 */
		public function cloud_catcfw_filter_loop_add_to_cart_link( $button, $product ) {

			$bundle_detail = $this->cloud_catcfw_check_product_match_all_rule_or_not( $product->get_id() );

			if ( isset( $bundle_detail['rule_id'] ) ) {

				$rule_id = $bundle_detail['rule_id'];

				$icon = '';

				if ( ! empty( get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) ) ) {

					$icon = '<i  class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) ) . '"></i>';

					if ( 'upload_custom_icon' === (string) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ) {

						$icon = '<img   src="' . esc_url( get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ) . '" >';

					}

					if ( 'custom_icon_class' === (string) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ) {
						$icon = '<i  class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ) . '"></i>';

					}
				}

				$button_text = str_replace( '{icon}', $icon, get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_text', true ) );

				$ct_catcbfw_add_to_cart_styling = (array) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_styling', true );

				$button_text_color = isset( $ct_catcbfw_add_to_cart_styling['button_text_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_color'] : '';

				$button_text_bgcolor = isset( $ct_catcbfw_add_to_cart_styling['button_text_bgcolor'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_bgcolor'] : '';

				$button_text_hover_color   = isset( $ct_catcbfw_add_to_cart_styling['button_text_hover_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_hover_color'] : '';
				$button_text_hover_bgcolor = isset( $ct_catcbfw_add_to_cart_styling['button_text_hover_bgcolor'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_hover_bgcolor'] : '';

				$button_border_color = isset( $ct_catcbfw_add_to_cart_styling['button_border_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_color'] : '';

				$button_font_size = isset( $ct_catcbfw_add_to_cart_styling['button_font_size'] ) ? $ct_catcbfw_add_to_cart_styling['button_font_size'] : 14;

				$button_font_weight = isset( $ct_catcbfw_add_to_cart_styling['button_font_weight'] ) ? $ct_catcbfw_add_to_cart_styling['button_font_weight'] : '100';

				$button_border_radius = isset( $ct_catcbfw_add_to_cart_styling['button_border_radius'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_radius'] : '';

				$button_border = isset( $ct_catcbfw_add_to_cart_styling['button_border'] ) ? $ct_catcbfw_add_to_cart_styling['button_border'] : '';

				$button_border_padding_left = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_left'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_left'] : '';

				$button_border_padding_top = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_top'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_top'] : '';

				$button_border_padding_bottom = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_bottom'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_bottom'] : '';

				$button_border_padding_right = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_right'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_right'] : '';

				$button_border_margin_left = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_left'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_left'] : '';

				$button_border_margin_top = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_top'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_top'] : '';

				$button_border_margin_bottom = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_bottom'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_bottom'] : '';

				$button_border_margin_right = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_right'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_right'] : '';

				?>
					<style type="text/css">
						.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?> {

						<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_text_nd_bg_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_text_nd_bg_color'] ) ) { ?>

								color: <?php echo esc_attr( $button_text_color ); ?>;
								background-color: <?php echo esc_attr( $button_text_bgcolor ); ?>;

							<?php } ?>


						<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_padding'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_padding'] ) ) { ?>

								padding: <?php echo esc_attr( $button_border_padding_top . ' ' . $button_border_padding_right . ' ' . $button_border_padding_bottom . ' ' . $button_border_padding_left ); ?>;

							<?php } ?>


						<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_margin'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_margin'] ) ) { ?>

								margin: <?php echo esc_attr( $button_border_margin_top . ' ' . $button_border_margin_right . ' ' . $button_border_margin_bottom . ' ' . $button_border_margin_left ); ?>;

							<?php } ?>


						<?php if ( isset( $ct_catcbfw_add_to_cart_styling['btn_border_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['btn_border_color'] ) ) { ?>

								border-color: <?php echo esc_attr( $button_border_color ); ?>px;


							<?php } ?>

						<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_btn_border_nd_border_radius'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_btn_border_nd_border_radius'] ) ) { ?>

								border: <?php echo esc_attr( $button_border ); ?>px;
								border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;

							<?php } ?>

							font-size: <?php echo esc_attr( $button_font_size ); ?>px;
							font-weight: <?php echo esc_attr( $button_font_weight ); ?>;

						}
					<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_hover_nd_hover_bg_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_hover_nd_hover_bg_color'] ) ) { ?>

							.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:hover,
							.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:active {

								color: <?php echo esc_attr( $button_text_hover_color ); ?>;
								background-color: <?php echo esc_attr( $button_text_hover_bgcolor ); ?>;
							}

						<?php } ?>
					</style>
					<?php

					$button_text = '<i data-hover_color="' . esc_attr( $button_text_hover_color ) . '" data-rule_id="' . esc_attr( $rule_id ) . '" class="ct-cpfw-add-to-cart-custom-button"  >'
					. esc_attr( $button_text ) . '</i>';

					$button = '<a href="?add-to-cart' . $product->get_id() . '=" data-rule_id="' . esc_attr( $rule_id ) . '" data-quantity="1" class="ct-cpfw-add-to-cart-custom-button button catcb_css product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product->get_id() . '" data-product_sku="' . $product->get_sku() . '" aria-label="Add “Album” to your cart" rel="nofollow">' . $button_text . '</a>';

			}

			if ( 'variable' === (string) $product->get_type() ) {

				foreach ( $product->get_children() as $product_id ) {

					$bundle_detail = $this->cloud_catcfw_check_product_match_all_rule_or_not( $product_id );

					if ( isset( $bundle_detail['rule_id'] ) ) {

						$rule_id = $bundle_detail['rule_id'];

						$icon = '';

						if ( ! empty( get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) ) ) {
							$icon = '<i  class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) ) . '"></i>';

							if ( 'upload_custom_icon' === (string) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ) {

								$icon = '<img   src="' . esc_url( get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ) . '" >';

							}

							if ( 'custom_icon_class' === (string) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ) {
								$icon = '<i  class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ) . '"></i>';

							}
						}

						$button_text = str_replace( '{icon}', $icon, get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_text', true ) );

						$ct_catcbfw_add_to_cart_styling = (array) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_styling', true );

						$button_text_color = isset( $ct_catcbfw_add_to_cart_styling['button_text_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_color'] : '';

						$button_text_bgcolor = isset( $ct_catcbfw_add_to_cart_styling['button_text_bgcolor'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_bgcolor'] : '';

						$button_text_hover_color   = isset( $ct_catcbfw_add_to_cart_styling['button_text_hover_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_hover_color'] : '';
						$button_text_hover_bgcolor = isset( $ct_catcbfw_add_to_cart_styling['button_text_hover_bgcolor'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_hover_bgcolor'] : '';

						$button_border_color = isset( $ct_catcbfw_add_to_cart_styling['button_border_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_color'] : '';

						$button_font_size = isset( $ct_catcbfw_add_to_cart_styling['button_font_size'] ) ? $ct_catcbfw_add_to_cart_styling['button_font_size'] : 14;

						$button_font_weight = isset( $ct_catcbfw_add_to_cart_styling['button_font_weight'] ) ? $ct_catcbfw_add_to_cart_styling['button_font_weight'] : '100';

						$button_border_radius = isset( $ct_catcbfw_add_to_cart_styling['button_border_radius'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_radius'] : '';

						$button_border = isset( $ct_catcbfw_add_to_cart_styling['button_border'] ) ? $ct_catcbfw_add_to_cart_styling['button_border'] : '';

						$button_border_padding_left = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_left'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_left'] : '';

						$button_border_padding_top = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_top'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_top'] : '';

						$button_border_padding_bottom = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_bottom'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_bottom'] : '';

						$button_border_padding_right = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_right'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_right'] : '';

						$button_border_margin_left = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_left'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_left'] : '';

						$button_border_margin_top = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_top'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_top'] : '';

						$button_border_margin_bottom = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_bottom'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_bottom'] : '';

						$button_border_margin_right = isset( $ct_catcbfw_add_to_cart_styling['button_border_margin_right'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_margin_right'] : '';

						?>
							<style type="text/css">
								.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?> {

								<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_text_nd_bg_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_text_nd_bg_color'] ) ) { ?>

										color: <?php echo esc_attr( $button_text_color ); ?>;
										background-color: <?php echo esc_attr( $button_text_bgcolor ); ?>;

									<?php } ?>


								<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_padding'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_padding'] ) ) { ?>


										padding: <?php echo esc_attr( $button_border_padding_top . ' ' . $button_border_padding_right . ' ' . $button_border_padding_bottom . ' ' . $button_border_padding_left ); ?>;
									<?php } ?>


								<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_margin'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_margin'] ) ) { ?>

										margin: <?php echo esc_attr( $button_border_margin_top . ' ' . $button_border_margin_right . ' ' . $button_border_margin_bottom . ' ' . $button_border_margin_left ); ?>;

									<?php } ?>


								<?php if ( isset( $ct_catcbfw_add_to_cart_styling['btn_border_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['btn_border_color'] ) ) { ?>

										border-color: <?php echo esc_attr( $button_border_color ); ?>px;


									<?php } ?>

								<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_btn_border_nd_border_radius'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_btn_border_nd_border_radius'] ) ) { ?>

										border: <?php echo esc_attr( $button_border ); ?>px;
										border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;

									<?php } ?>

									font-size: <?php echo esc_attr( $button_font_size ); ?>px;
									font-weight: <?php echo esc_attr( $button_font_weight ); ?>;

								}
							<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_hover_nd_hover_bg_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_hover_nd_hover_bg_color'] ) ) { ?>

									.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:hover,
									.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:active {

										color: <?php echo esc_attr( $button_text_hover_color ); ?>;
										background-color: <?php echo esc_attr( $button_text_hover_bgcolor ); ?>;
									}

								<?php } ?>
							</style>
							<?php
							$button_text = '<i data-hover_color="' . esc_attr( $button_text_hover_color ) . '" data-rule_id="' . esc_attr( $rule_id ) . '" class="ct-cpfw-add-to-cart-custom-button " >'
							. esc_attr( $button_text ) . '</i>';

							$button = '<a data-rule_id="' . esc_attr( $rule_id ) . '" href="?add-to-cart' . $product->get_id() . '=" data-quantity="1" class="button catcb_css product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product->get_id() . '" data-product_sku="' . $product->get_sku() . '" aria-label="Add “Album” to your cart" rel="nofollow">' . $button_text . '</a>';

							break;
					}
				}
			}

			return $button;
		}

		/**
		 * Text With Styling.
		 *
		 * @param      string $text          set here button.
		 */
		public function cloud_catcfw_woocommerce_add_to_cart_button_text( $text ) {

			global $product;

			$bundle_detail = $this->cloud_catcfw_check_product_match_all_rule_or_not( $product->get_id() );

			if ( isset( $bundle_detail['rule_id'] ) ) {

				$rule_id = $bundle_detail['rule_id'];
				ob_start();

				echo wp_kses_post( $this->catcfw_get_add_cart_button_text( $bundle_detail['rule_id'] ) );

				$text = ob_get_clean();

			}

			if ( 'variable' === (string) $product->get_type() ) {

				foreach ( $product->get_children() as $product_id ) {

					$bundle_detail = $this->cloud_catcfw_check_product_match_all_rule_or_not( $product_id );

					if ( isset( $bundle_detail['rule_id'] ) ) {

						$rule_id = $bundle_detail['rule_id'];

						ob_start();

						echo wp_kses_post( $this->catcfw_get_add_cart_button_text( $bundle_detail['rule_id'] ) );

						$text = ob_get_clean();

						break;
					}
				}
			}

			return $text;
		}

		/**
		 * Get Text of Add to cart button.
		 *
		 * @param     Int $rule_id    Active rule id.
		 */
		public function catcfw_get_add_cart_button_text( $rule_id ) {

			$icon = '';

			if ( ! empty( get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) ) ) {

				$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) ) . '"></i>';

				if ( 'upload_custom_icon' === get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ) {

					$icon = '<img  src="' . esc_url( get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ) . '">';

				}

				if ( 'custom_icon_class' === get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_icon', true ) && get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ) {
					$icon = '<i class=" fa ' . esc_attr( get_post_meta( $rule_id, 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ) . '"></i>';

				}
			}

			$button_text = str_replace( '{icon}', $icon, get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_button_text', true ) );

			$ct_catcbfw_add_to_cart_styling = (array) get_post_meta( $rule_id, 'ct_catcbfw_add_to_cart_styling', true );

			$button_text_color = isset( $ct_catcbfw_add_to_cart_styling['button_text_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_color'] : '';

			$button_text_bgcolor = isset( $ct_catcbfw_add_to_cart_styling['button_text_bgcolor'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_bgcolor'] : '';

			$button_text_hover_color   = isset( $ct_catcbfw_add_to_cart_styling['button_text_hover_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_hover_color'] : '';
			$button_text_hover_bgcolor = isset( $ct_catcbfw_add_to_cart_styling['button_text_hover_bgcolor'] ) ? $ct_catcbfw_add_to_cart_styling['button_text_hover_bgcolor'] : '';

			$button_border_color = isset( $ct_catcbfw_add_to_cart_styling['button_border_color'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_color'] : '';

			$button_font_size = isset( $ct_catcbfw_add_to_cart_styling['button_font_size'] ) ? $ct_catcbfw_add_to_cart_styling['button_font_size'] : 14;

			$button_font_weight = isset( $ct_catcbfw_add_to_cart_styling['button_font_weight'] ) ? $ct_catcbfw_add_to_cart_styling['button_font_weight'] : '100';

			$button_border_radius = isset( $ct_catcbfw_add_to_cart_styling['button_border_radius'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_radius'] : '';
			$button_border        = isset( $ct_catcbfw_add_to_cart_styling['button_border'] ) ? $ct_catcbfw_add_to_cart_styling['button_border'] : '';

			$button_border_padding_left = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_left'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_left'] : '';

			$button_border_padding_top = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_top'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_top'] : '';

			$button_border_padding_bottom = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_bottom'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_bottom'] : '';

			$button_border_padding_right = isset( $ct_catcbfw_add_to_cart_styling['button_border_padding_right'] ) ? $ct_catcbfw_add_to_cart_styling['button_border_padding_right'] : '';

			?>
				<style type="text/css">
					.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?> {

					<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_text_nd_bg_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_text_nd_bg_color'] ) ) { ?>

							color: <?php echo esc_attr( $button_text_color ); ?>;
							background-color: <?php echo esc_attr( $button_text_bgcolor ); ?>;

						<?php } ?>


					<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_padding'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_padding'] ) ) { ?>


							padding: <?php echo esc_attr( $button_border_padding_top . ' ' . $button_border_padding_right . ' ' . $button_border_padding_bottom . ' ' . $button_border_padding_left ); ?>;
						<?php } ?>


					<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_margin'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_margin'] ) ) { ?>

							margin: <?php echo esc_attr( $button_border_margin_top . ' ' . $button_border_margin_right . ' ' . $button_border_margin_bottom . ' ' . $button_border_margin_left ); ?>;

						<?php } ?>


					<?php if ( isset( $ct_catcbfw_add_to_cart_styling['btn_border_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['btn_border_color'] ) ) { ?>

							border-color: <?php echo esc_attr( $button_border_color ); ?>px;


						<?php } ?>

					<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_btn_border_nd_border_radius'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_btn_border_nd_border_radius'] ) ) { ?>

							border: <?php echo esc_attr( $button_border ); ?>px;
							border-radius: <?php echo esc_attr( $button_border_radius ); ?>px;

						<?php } ?>

						font-size: <?php echo esc_attr( $button_font_size ); ?>px;
						font-weight: <?php echo esc_attr( $button_font_weight ); ?>;

					}
				<?php if ( isset( $ct_catcbfw_add_to_cart_styling['enable_hover_nd_hover_bg_color'] ) && ! empty( $ct_catcbfw_add_to_cart_styling['enable_hover_nd_hover_bg_color'] ) ) { ?>

						.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:hover,
						.ct-cpfw-add-to-cart-custom-button-<?php echo esc_attr( $rule_id ); ?>:active {

							color: <?php echo esc_attr( $button_text_hover_color ); ?>;
							background-color: <?php echo esc_attr( $button_text_hover_bgcolor ); ?>;
						}

					<?php } ?>
				</style>
				<?php

				$button_text = '<i data-hover_color="' . esc_attr( $button_text_hover_color ) . '" data-rule_id="' . esc_attr( $rule_id ) . '" class="ct-cpfw-add-to-cart-custom-button">'
				. esc_attr( $button_text ) . '</i>';

				return $button_text;
		}

		/**
		 * Rule is applicable on current product or not.
		 *
		 * @param int $product_id The ID of the current product.
		 */
		public function cloud_catcfw_check_product_match_all_rule_or_not( $product_id ) {
			$user_role = is_user_logged_in() ? (string) current( wp_get_current_user()->roles ) : 'guest';

			$old_selected_prd = (array) get_post_meta( $product_id, 'ct_catcbfw_selected_chain_prd', true );
			$old_selected_prd = array_filter( $old_selected_prd );

			$flag = false;

			if ( ! empty( get_post_meta( $product_id, 'ct_catcbfw_add_to_cart_button_text', true ) ) && ! empty( get_post_meta( $product_id, 'ct_catcbfw_enable_add_to_cart_styling', true ) ) ) {

				$selected_user_role = get_post_meta( $product_id, 'ct_catcbfw_select_user_from_switch', true ) ? (array) get_post_meta( $product_id, 'ct_catcbfw_select_user_from_switch', true ) : array( $user_role );

				if ( ! in_array( (string) $user_role, $selected_user_role, true ) ) {

					return;
				}

				$selected_pages = (array) get_post_meta( $product_id, 'ct_catcbfw_select_pages', true );
				$selected_pages = array_filter( $selected_pages );
				if ( count( $selected_pages ) >= 1 ) {

					if ( is_shop() && ! in_array( 'shop', $selected_pages, true ) ) {
						returnarray();
					}
					if ( is_single() && ! in_array( 'product_page', $selected_pages, true ) ) {
						returnarray();
					}
					if ( is_product_category() && ! in_array( 'category_page', $selected_pages, true ) ) {
						returnarray();
					}
					if ( is_product_tag() && ! in_array( 'tag_page', $selected_pages, true ) ) {
						returnarray();
					}
				}

				return array( 'rule_id' => $product_id );

			} else {

				$get_rules = (array) self::cloud_get_post( 'ct_cstm_add_t_c_btn' );

				if ( count( $get_rules ) >= 1 ) {
					foreach ( $get_rules as $current_rule_id ) {

						if ( $current_rule_id ) {

							if ( empty( get_post_meta( $current_rule_id, 'ct_catcbfw_add_to_cart_button_text', true ) ) ) {

								continue;
							}

							$selected_pages = (array) get_post_meta( $current_rule_id, 'ct_catcbfw_select_pages', true );

							$selected_pages = array_filter( $selected_pages );

							if ( count( $selected_pages ) >= 1 ) {

								if ( is_shop() && ! in_array( 'shop', $selected_pages, true ) ) {
									continue;
								}
								if ( is_single() && ! in_array( 'product_page', $selected_pages, true ) ) {
									continue;
								}
								if ( is_product_category() && ! in_array( 'category_page', $selected_pages, true ) ) {
									continue;
								}
								if ( is_product_tag() && ! in_array( 'tag_page', $selected_pages, true ) ) {
									continue;
								}
							}

							$selected_user_role = get_post_meta( $current_rule_id, 'ct_catcbfw_select_user_from_switch', true ) ? (array) get_post_meta( $current_rule_id, 'ct_catcbfw_select_user_from_switch', true ) : array( $user_role );

							$excluded_product = (array) get_post_meta( $current_rule_id, 'ct_catcbfw_product_exclusion_list', true );
							$excluded_product = array_filter( $excluded_product );

							$included_product = (array) get_post_meta( $current_rule_id, 'ct_catcbfw_product_included_list', true );
							$included_product = array_filter( $included_product );

							$selected_categorie = (array) get_post_meta( $current_rule_id, 'ct_catcbfw_included_category', true );
							$selected_categorie = array_filter( $selected_categorie );

							$selected_tags = (array) get_post_meta( $current_rule_id, 'ct_catcbfw_product_tags', true );
							$selected_tags = array_filter( $selected_tags );

							if ( count( $excluded_product ) >= 1 && in_array( (string) $product_id, $excluded_product, true ) ) {

								continue;
							}

							if ( ! in_array( (string) $user_role, $selected_user_role, true ) ) {

								continue;
							}

							if ( count( $selected_categorie ) < 1 && count( $included_product ) < 1 && count( $selected_tags ) < 1 ) {

								$flag = true;
							}

							if ( in_array( (string) $product_id, $included_product, true ) ) {

								$flag = true;
							}

							foreach ( $selected_categorie as $cat_id ) {

								if ( $cat_id && has_term( $cat_id, 'product_cat', $product_id ) ) {

									$flag = true;

								}
							}

							if ( count( $selected_tags ) >= 1 && has_term( $selected_tags, 'product_tag', $product_id ) ) {

								$flag = true;

							}

							if ( $flag ) {

								return array( 'rule_id' => $current_rule_id );
							}
						}
					}
				}
			}

			return array();

		}

		/**
		 * Getting post of current post type.
		 *
		 * @param string $post_type post type to get ids of this post type.
		 */
		public static function cloud_get_post( $post_type ) {

			return get_posts(
				array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
		}


	}

	/**
	 * Class Object .
	 */
	new Catcbfw_Front();
}
