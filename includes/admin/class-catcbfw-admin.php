<?php
/**
 * Admin file.
 *
 * @package Add To Cart Button Customizer
 *
 * @version 2.0.0
 */

/**
 * Restrict for direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check class Exist or not.
 */
if ( ! class_exists( 'Catcbfw_Admin' ) ) {

	/**
	 * Admin Class.
	 */
	class Catcbfw_Admin {


		/**
		 * Constructor .
		 */
		public function __construct() {

			add_filter( 'manage_edit-ct_cstm_add_t_c_btn_columns', array( $this, 'cloud_catcbfw_adding_coulm_data' ) );
			add_action( 'manage_ct_cstm_add_t_c_btn_posts_custom_column', array( $this, 'cloud_catcbfw_adding_coulm' ) );

			add_action( 'add_meta_boxes', array( $this, 'cloud_catcbfw_create_metabox' ) );

			add_action( 'save_post_ct_cstm_add_t_c_btn', array( $this, 'cloud_catcbfw_save_meta_values' ) );
			add_action( 'save_post_product', array( $this, 'cloud_catcbfw_save_meta_values' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'cloud_catcbfw_enqueue_scripts' ) );

			add_action( 'wp_ajax_ct_catcbfw_product_search', array( $this, 'cloud_catcbfw_product_search' ) );
			add_action( 'wp_ajax_ct_catcbfw_category_search', array( $this, 'cloud_catcbfw_category_search' ) );
			add_action( 'all_admin_notices', array($this, 'ct_catcbc_tabs'), 5);
			add_action( 'admin_menu', array( $this, 'ct_catcbc_admin_menu' ), 10 );
		}

		/**
		 * submenu
		 */
		public function ct_catcbc_admin_menu() {
			global $pagenow, $typenow;


			add_submenu_page(
			'woocommerce', // define post type.
			'Add To Cart Button Customizer Setting', // Page title.
			esc_html__( 'Add To Cart Button Customizer', 'add-to-cart-button-customizer ' ), // Title.
			'manage_options', // Capability.
			'ct_catcbc_setting', // slug.
			array(
				$this,
				'ct_catcbc_setting_callback',
			));

			if (( 'edit.php' === $pagenow && 'ct_cstm_add_t_c_btn' === $typenow )
				|| ( 'post.php' === $pagenow && isset($_GET['post']) && 'ct_cstm_add_t_c_btn' === get_post_type( sanitize_text_field( $_GET['post'] ) ) ) ) 
			{
				remove_submenu_page('woocommerce', 'ct_catcbc_setting');

			} elseif ( ( 'admin.php' === $pagenow && isset($_GET['page']) && 'ct_catcbc_setting' === sanitize_text_field( $_GET['page'] ) ) ) 
			{

				remove_submenu_page('woocommerce', 'edit.php?post_type=ct_cstm_add_t_c_btn');

			} else 
			{
				remove_submenu_page('woocommerce', 'edit.php?post_type=ct_cstm_add_t_c_btn');
			}

		}

		/**
		 * submenu callback
		 */
		public function ct_catcbc_setting_callback() {


			$active_tab 			= 'general_setting';

			if (  in_array( 'tab', array_keys( $_GET ) , true ) ) {
				$active_tab 	= sanitize_text_field( $_GET['tab'] );
			}

			if ( 'ct_catcbc_premium_plugin' == $active_tab ) {
				$this->ct_catcbc_premium_plugin();
			}	
			?>
			<form method="post" action="options.php">

			</form>
			<?php


		}

		/**
		 * creating tabs 
		 */
		public function ct_catcbc_tabs() {

			global $post, $typenow;
			$screen = get_current_screen();
			if ($screen && in_array($screen->id, $this->get_tab_screen_ids(), true)) {

				$tabs = array(
					'rules' => array(
						'title' => __('Rules', 'add-to-cart-button-customizer'),
						'url' => admin_url('edit.php?post_type=ct_cstm_add_t_c_btn'),
					),
					'ct_catcbc_premium_plugin' => array(
						'title' => __(' Our Premium Plugin ', 'add-to-cart-button-customizer'),
						'url' => admin_url('admin.php?page=ct_catcbc_setting&tab=ct_catcbc_premium_plugin'),
					),
				);


				if (is_array($tabs)) { 
					?>
					<div class="wrap woocommerce">
						<div id="message" class="success">
							<strong>
								<?php echo esc_html__('Certainly! Here is a more user-friendly and inviting version of your contact message','add-to-cart-button-customizer');

								?> <br> <?php

								echo esc_html__('Encountering issues, looking to customize, or interested in purchasing our premium plugin at an exclusive discount? We are here to help!','add-to-cart-button-customizer');
								?> <br> <?php


								echo esc_html__('Feel free to reach out to us anytime at','add-to-cart-button-customizer');
								?> <b style="color:blue;"><?php echo esc_html__('cloudtechnologiesofficial@gmail.com.','add-to-cart-button-customizer'); ?></b> <?php

								echo esc_html__('Your questions and feedback are valuable to us, and we are eager to assist you.','add-to-cart-button-customizer');

								?>
							</strong>
						</div>
						<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
							<?php

							$current_tab = $this->get_current_tab();
							if ( isset($_GET['tab'])) {

								$current_tab = sanitize_text_field( $_GET['tab'] );

							}

							foreach ($tabs as $id => $tab_data) {


								$class = $id === $current_tab ? array('nav-tab', 'nav-tab-active') : array('nav-tab');

								printf('<a href="%1$s" class="%2$s">%3$s</a>', esc_url($tab_data['url']), implode(' ', array_map('sanitize_html_class', $class)), esc_html($tab_data['title'] ));
							}
							?>
						</h2>
					</div>
					<?php
				}
			}
		}

		/**
		 * Adding columns in custom post type table .
		 * Current Screens
		 */

		public function get_current_tab() {



			$screen = get_current_screen();

			$active_tab = $screen->id;

			switch ( $active_tab ) {
				case 'woocommerce_page_ct_catcbc_setting':
				return 'general_setting';
				case 'ct_cstm_add_t_c_btn':
				case 'edit-ct_cstm_add_t_c_btn':
				return 'rules';
			}
		}
		/**
		 * Adding columns in custom post type table .
		 * All Screens
		 */
		public function get_tab_screen_ids() {
			$tabs_screens = array(
				'woocommerce_page_ct_catcbc_setting',
				'edit-ct_cstm_add_t_c_btn',
				'ct_cstm_add_t_c_btn',
			);

			return $tabs_screens;
		}

		public function ct_catcbc_premium_plugin() {
			$apiUrl = 'https://cloudtechnologies.store/wp-json/custom-woocommerce-api/v1/products/woocommerce';

			$curl = curl_init($apiUrl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			curl_close($curl);

			if ($response) {

				$products_details = json_decode($response, true);
				?>
				<table class="wp-list-table widefat fixed striped table-view-list customers">
					<thead>
						<th><?php echo esc_html__('image','add-to-cart-button-customizer'); ?></th>
						<th><?php echo esc_html__('Name','add-to-cart-button-customizer'); ?></th>
						<th><?php echo esc_html__('Price','add-to-cart-button-customizer'); ?></th>
						<th><?php echo esc_html__('Description','add-to-cart-button-customizer'); ?></th>
						<th><?php echo esc_html__('Action','add-to-cart-button-customizer'); ?></th>

					</thead>
					<tbody>	
						<?php foreach ($products_details as $key => $current_product_detail):

							if ( ! is_array( $current_product_detail ) ) {
								continue;
							}
							$product_name   = isset( $current_product_detail['product_name'] ) ? $current_product_detail['product_name'] : '';
							$product_url    = isset( $current_product_detail['product_url'] ) ? $current_product_detail['product_url'] : '';
							$regular_price  = isset( $current_product_detail['regular_price'] ) ? $current_product_detail['regular_price'] : '';
							$sale_price     = isset( $current_product_detail['sale_price'] ) ? $current_product_detail['sale_price'] : '';
							$review     = isset( $current_product_detail['review'] ) ? $current_product_detail['review'] : '';
							$short_description  = isset( $current_product_detail['short_description'] ) ? $current_product_detail['short_description'] : '';
							$image  = isset( $current_product_detail['image_url'] ) ? $current_product_detail['image_url'] : '';


							?>
							<tr>

								<td class="ct-premium-plugin-image">
									<?php if ( !empty( $image ) ): ?>
										<img style="width: 25%;" src="<?php echo esc_url( $image ); ?>">

									<?php endif ?>
								</td>
								<td class="ct-premium-plugin-name-url">
									<h4>
										<a target="_blank" href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_attr( $product_name ); ?></a>
									</h4>
								</td>

								<td class="" style="width:10%;">
									<?php if ( !empty( $sale_price )  ){  ?>
										<del>$<?php echo esc_attr($regular_price); ?></del>
										<span class="ct-premium-plugin-sale-price">$<?php echo esc_attr($sale_price); ?></span>
									<?php } else  { ?>
										<span class="ct-premium-plugin-sale-price">$<?php echo esc_attr($regular_price); ?></span>

									<?php } ?>

								</td>
								<td class="ct-premium-plugin-short-description">
									<p><?php echo wp_kses_post( $short_description ); ?>.</p>
								</td>
								<td style="text-align:right;width: 12%;" >
									<a class="button button-primary btn" target="_blank" href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html__( 'Buy Now' , 'cloud_tech_psbscac' ); ?></a>
								</td>
							</tr>

						<?php endforeach ?>
					</tbody>
				</table>

				<?php
			}
		}

		/**
		 * Adding columns in custom post type table .
		 *
		 * @param array $columns its an array where we will set index and value .
		 */
		public function cloud_catcbfw_adding_coulm_data( $columns ) {

			unset( $columns['date'] );

			$columns['add_to_cart'] = esc_html__( 'Add to Cart Text', 'add-to-cart-button-customizer' );
			$columns['applied_on']  = esc_html__( 'Applied', 'add-to-cart-button-customizer' );
			$columns['rule_status'] = esc_html__( 'Rule Status', 'add-to-cart-button-customizer' );
			$columns['date']        = esc_html__( 'Publish Date', 'add-to-cart-button-customizer' );

			return $columns;
		}

		/**
		 * Get columns of custom post type table.
		 *
		 * @param string $column The name of the current column.
		 *
		 * @version 2.0.0
		 */
		public function cloud_catcbfw_adding_coulm( $column ) {

			global $post;

			$post_id = $post->ID;

			if ( 'add_to_cart' === (string) $column ) {

				echo esc_attr( get_post_meta( $post_id, 'ct_catcbfw_add_to_cart_button_text', true ) );

			}

			if ( 'rule_status' === (string) $column ) {

				echo esc_attr( ucfirst( get_post_status( $post_id ) ) );

			}

			if ( 'applied_on' === (string) $column ) {

				$applied_on = '';

				if ( get_post_meta( get_the_ID(), 'ct_catcbfw_product_included_list', true ) ) {

					$applied_on = ' Product ';

				}
				if ( get_post_meta( get_the_ID(), 'ct_catcbfw_included_category', true ) ) {

					$applied_on = ' -- Categories ';

				}
				if ( get_post_meta( get_the_ID(), 'ct_catcbfw_product_tags', true ) ) {

					$applied_on = ' -- Tag ';

				}
				$applied_on = ! empty( $applied_on ) ? $applied_on : 'For All Product';
				echo esc_attr( $applied_on );

			}
		}

		/**
		 * Adding MEta Box.
		 */
		public function cloud_catcbfw_create_metabox() {

			add_meta_box(
				'ct_catcbfw_restrictions',
				esc_html__( 'Restrictions', 'add-to-cart-button-customizer' ),
				array( $this, 'restriction' ),
				'ct_cstm_add_t_c_btn'
			);

			add_meta_box(
				'ct_catcbfw_add_to_cart_styling',
				esc_html__( 'Add To Cart Styling', 'add-to-cart-button-customizer' ),
				array( $this, 'add_to_cart_styling' ),
				'ct_cstm_add_t_c_btn'
			);

			add_meta_box(
				'ct_catcbfw_add_to_cart_styling',
				esc_html__( 'Add To Cart Styling', 'add-to-cart-button-customizer' ),
				array( $this, 'add_to_cart_styling' ),
				'product',
			);

		}

		/**
		 * Restrict callback.
		 */
		public function restriction() {

			global $wp_roles;

			wp_nonce_field( 'ct_catcbfw_nonce', 'ct_catcbfw_nonce' );

			$countries_obj              = new WC_Countries();
			$countries                  = $countries_obj->__get( 'countries' );
			$af_a_and_va_s_product_tags = get_terms( array( 'taxonomy' => 'product_tag' ) );

			$included_product    = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_product_included_list', true );
			$excluded_products   = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_product_exclusion_list', true );
			$selected_categories = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_included_category', true );
			$ct_selected_tags    = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_product_tags', true );

			$add_cart_styling_setting   = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_add_to_cart_styling', true );
			$switch_from_roles          = $wp_roles->get_names();
			$switch_from_roles['guest'] = 'Guest';
			$kselect_user_roles         = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_select_user_from_switch', true );
			$ct_catcbfw_select_pages    = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_select_pages', true );
			$switch_pages               = array(
				'shop'          => 'Shop',
				'product_page'  => 'Single/ Product Page',
				'category_page' => 'Category Page',
				'tag_page'      => 'Tag Page',
			);

			?>
			<table>
				<tbody>



					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Select Users Roles', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select id="select_user_from_switch" style="width: 350px;" name="ct_catcbfw_select_user_from_switch[]" class="select_user_from_switch"  multiple>
								<?php
								foreach ( $switch_from_roles as $key => $from_switch_role ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php echo in_array( (string) $key, $kselect_user_roles, true ) ? esc_attr( 'selected' ) : ''; ?> />
										<?php echo esc_attr( $from_switch_role ); ?>
									</option>
								<?php } ?>
							</select>
							<p>
								<i>
									<?php echo esc_html__( 'Select user roles to apply rule setting. Leave empty to enable for all users.', 'add-to-cart-button-customizer' ); ?>
								</i>
							</p>
						</td>
					</tr>

					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Select Pages', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select id="select_user_from_switch" style="width: 350px;" name="ct_catcbfw_select_pages[]" class="ct_live_Search"  multiple>
								<?php
								foreach ( $switch_pages as $key => $page ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php echo in_array( (string) $key, $ct_catcbfw_select_pages, true ) ? esc_attr( 'selected' ) : ''; ?> />
										<?php echo esc_attr( $page ); ?>
									</option>
								<?php } ?>
							</select>
							<p>
								<i>
									<?php echo esc_html__( 'Select pages on which you want to show custom add to cart Button . Leave empty to enable for all users.', 'add-to-cart-button-customizer' ); ?>
								</i>
							</p>
						</td>
					</tr>
					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Included Products', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select  class="ct_catcbfw_product_live_search" name="ct_catcbfw_product_included_list[]" multiple style="width: 50%;">
								<?php
								foreach ( $included_product as $product_id ) {

									if ( ! empty( $product_id ) ) {

										$product = wc_get_product( $product_id );

										?>
										<option value="<?php echo esc_attr( $product_id ); ?>" selected>
											<?php echo esc_attr( $product->get_name() ); ?>
										</option>
										<?php

									}
								}
								?>

							</select>
						</td>
					</tr>

					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Excluded Products', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select  class="ct_catcbfw_product_live_search" name="ct_catcbfw_product_exclusion_list[]" multiple style="width: 50%;">
								<?php
								foreach ( $excluded_products as $product_id ) {

									if ( ! empty( $product_id ) ) {
										$product = wc_get_product( $product_id );
										?>
										<option value="<?php echo esc_attr( $product_id ); ?>" selected>
											<?php echo esc_attr( $product->get_name() ); ?>
										</option>
										<?php
									}
								}
								?>

							</select>
						</td>
					</tr>

					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Select Categories', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select class="ct_catcbfw_category_live_search" name="ct_catcbfw_included_category[]" multiple style="width: 50%;">

								<?php
								foreach ( $selected_categories as $cat_id ) {
									if ( ! empty( $cat_id ) ) {
										$category = get_term( $cat_id, 'product_cat' );
										?>
										<option value="<?php echo esc_attr( $cat_id ); ?>" selected>
											<?php echo esc_attr( $category->name ); ?>
										</option>
										<?php
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr>

						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Product Tag', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select width="100%" name="ct_catcbfw_product_tags[]" id="af_a_and_va_s_product_tags" data-placeholder="Choose Tags..." class="ct_live_Search" multiple="multiple" tabindex="-1" >;
								<?php foreach ( $af_a_and_va_s_product_tags as $product_tag ) { ?>

									<option value="<?php echo esc_html( $product_tag->term_id ); ?>"
										<?php
										if ( in_array( (string) $product_tag->term_id, (array) $ct_selected_tags, true ) ) {
											echo 'selected'; }
											?>
											><?php echo esc_html( $product_tag->name ); ?>

										</option>
										<?php
									}
									?>
								</select>

								<p><?php echo esc_html__( 'Select Tags on which you want to show notification', 'add-to-cart-button-customizer' ); ?></p>
								<p><?php echo esc_html__( 'Leave empty to apply rule on all products.', 'add-to-cart-button-customizer' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php

			}

		/**
		 * Add to cart button setting and styling.
		 */
		public function add_to_cart_styling() {
			wp_nonce_field( 'ct_catcbfw_nonce', 'ct_catcbfw_nonce' );
			global $wp_roles;

			$add_cart_styling_setting   = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_add_to_cart_styling', true );
			$switch_from_roles          = $wp_roles->get_names();
			$switch_from_roles['guest'] = 'Guest';
			$kselect_user_roles         = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_select_user_from_switch', true );
			$ct_catcbfw_select_pages    = (array) get_post_meta( get_the_ID(), 'ct_catcbfw_select_pages', true );
			$switch_pages               = array(
				'shop'          => 'Shop',
				'product_page'  => 'Single/ Product Page',
				'category_page' => 'Category Page',
				'tag_page'      => 'Tag Page',
			);

			?>
			<table>
				<?php if ( 'product' === (string) get_post_type( get_the_ID() ) ) { ?>

					<tr>
						<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Add to cart button Setting', 'add-to-cart-button-customizer' ); ?></th>
						<td class="ct_catcbfw_table_content">
							<input value="yes" type="checkbox"   name="ct_catcbfw_enable_add_to_cart_styling" <?php echo esc_attr( get_post_meta( get_the_ID(), 'ct_catcbfw_enable_add_to_cart_styling', true ) ? 'checked' : '' ); ?>>

							<p>
								<i>
									<?php echo esc_html__( 'Enable checkbox to set Set Add to cart button Setting.', 'add-to-cart-button-customizer' ); ?>
								</i>
							</p>

						</td>
					</tr>


					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Select Users Roles', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select id="select_user_from_switch" style="width: 350px;" name="ct_catcbfw_select_user_from_switch[]" class="select_user_from_switch"  multiple>
								<?php
								foreach ( $switch_from_roles as $key => $from_switch_role ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php echo in_array( (string) $key, $kselect_user_roles, true ) ? esc_attr( 'selected' ) : ''; ?> />
										<?php echo esc_attr( $from_switch_role ); ?>
									</option>
								<?php } ?>
							</select>
							<p>
								<i>
									<?php echo esc_html__( 'Select user roles to apply rule setting. Leave empty to enable for all users.', 'add-to-cart-button-customizer' ); ?>
								</i>
							</p>
						</td>
					</tr>

					<tr>
						<th class="ct_catcbfw_table_heading">
							<?php echo esc_html__( 'Select Pages', 'add-to-cart-button-customizer' ); ?>
						</th>

						<td class="ct_catcbfw_table_content">
							<select id="select_user_from_switch" style="width: 350px;" name="ct_catcbfw_select_pages[]" class="ct_live_Search"  multiple>
								<?php
								foreach ( $switch_pages as $key => $page ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php echo in_array( (string) $key, $ct_catcbfw_select_pages, true ) ? esc_attr( 'selected' ) : ''; ?> />
										<?php echo esc_attr( $page ); ?>
									</option>
								<?php } ?>
							</select>
							<p>
								<i>
									<?php echo esc_html__( 'Select pages on which you want to show custom add to cart Button . Leave empty to enable for all users.', 'add-to-cart-button-customizer' ); ?>
								</i>
							</p>
						</td>
					</tr>

				<?php } ?>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Add To Cart Button Text', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">
						<input type="text" name="ct_catcbfw_add_to_cart_button_text" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ct_catcbfw_add_to_cart_button_text', true ) ); ?>" min="1">

						<p>
							<i>
								<?php echo esc_html__( 'Set add to cart button text. Leave empty to show default text. Use keyword {icon} to replace icon with text on any place  ', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>
					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Icon', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<?php
						$icon_classes = array( 'fa fa-cart-arrow-down', 'fa fa-cart-plus', 'fa fa-shopping-cart', 'fa fa-shopping-basket', 'custom_icon_class', 'upload_custom_icon' );

						foreach ( $icon_classes as $value ) {
							?>

							<input type="radio" name="ct_catcbfw_add_to_cart_button_icon" value="<?php echo esc_attr( $value ); ?>" <?php if ( get_post_meta( get_the_ID(), 'ct_catcbfw_add_to_cart_button_icon', true ) === (string) $value ) : ?>
							checked
							<?php endif ?>>

							<?php if ( 'custom_icon_class' !== (string) $value && 'upload_custom_icon' !== (string) $value ) { ?>

								<i class="<?php echo esc_attr( $value ); ?>" style="font-size: 24px;" ></i>
								<?php

							} else {
								echo esc_attr( ucfirst( str_replace( '_', ' ', $value ) ) );
							}
							?>
							<br>
							<?php
						}
						?>

					</td>
				</tr>
				<tr style="display:none;">
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( ' Set Icon Class', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="text"  name="ct_catcbfw_add_to_crt_btn_icon_class" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ct_catcbfw_add_to_crt_btn_icon_class', true ) ); ?>">

						<p>
							<i>
								<?php echo esc_html__( 'Set Class like fa fa fa-cart-plus you can find icon from font awesome by', 'add-to-cart-button-customizer' ); ?>
								<a href="https://fontawesome.com/" target="_blank" ><?php echo esc_html__( 'Click Here', 'add-to-cart-button-customizer' ); ?></a>
							</i>
						</p>
					</td>
				</tr>
				<tr style="display:none;">
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Upload Custom Icon', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<img src="" style="width:100px;height: 100px;"><br>

						<input type="text"  name="ct_catcbfw_add_to_crt_btn_uploaded_icon" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'ct_catcbfw_add_to_crt_btn_uploaded_icon', true ) ); ?>">

						<i class="fa fa-upload ct-cpfw-upload-icon"></i> <i class="ct-cpfw-remove-icon fa fa-trash"></i>
					</td>
				</tr>

				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Text And Background Color Styling', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input value="yes" type="checkbox" data-hide_class="enable_text_nd_bg_color" class="ct_catcbfw_add_to_cart_style check_custom_the_styling" name="ct_catcbfw_add_to_cart_styling[enable_text_nd_bg_color]" <?php echo esc_attr( isset( $add_cart_styling_setting['enable_text_nd_bg_color'] ) ? 'checked' : '' ); ?>>

						<p>
							<i>
								<?php echo esc_html__( 'Enable checkbox to set text and background color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Button Text Color', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input type="color" class="ct_catcbfw_add_to_cart_style enable_text_nd_bg_color" name="ct_catcbfw_add_to_cart_styling[button_text_color]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_color'] ) ? $add_cart_styling_setting['button_text_color'] : '' ); ?>" min="1">

						<p>
							<i>
								<?php echo esc_html__( 'Set button text color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>

				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Button Background Color', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="color" class="ct_catcbfw_add_to_cart_style enable_text_nd_bg_color" name="ct_catcbfw_add_to_cart_styling[button_text_bgcolor]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_bgcolor'] ) ? $add_cart_styling_setting['button_text_bgcolor'] : '' ); ?>" min="1">

						<p>
							<i><?php echo esc_html__( 'Set button background color.', 'add-to-cart-button-customizer' ); ?></i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Button Hover And Hove Background Color Styling', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input value="yes" type="checkbox" data-hide_class="enable_hover_nd_hover_bg_color" class="check_custom_the_styling ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[enable_hover_nd_hover_bg_color]" <?php echo esc_attr( isset( $add_cart_styling_setting['enable_hover_nd_hover_bg_color'] ) ? 'checked' : '' ); ?>>

						<p>
							<i>
								<?php echo esc_html__( 'Enable checkbox to set hover and hover background color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Button Hover Color', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="color" class="ct_catcbfw_add_to_cart_style enable_hover_nd_hover_bg_color" name="ct_catcbfw_add_to_cart_styling[button_text_hover_color]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_hover_color'] ) ? $add_cart_styling_setting['button_text_hover_color'] : '' ); ?>" min="1">

						<p>
							<i>
								<?php echo esc_html__( 'Set hover color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Button Hover Background-Color', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="color" class="ct_catcbfw_add_to_cart_style enable_hover_nd_hover_bg_color" name="ct_catcbfw_add_to_cart_styling[button_text_hover_bgcolor]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_text_hover_bgcolor'] ) ? $add_cart_styling_setting['button_text_hover_bgcolor'] : '' ); ?>" min="1">

						<p>
							<i>
								<?php echo esc_html__( 'Set hover color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Button Border Color', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input value="yes" type="checkbox" data-hide_class="btn_border_color" class="ct_catcbfw_add_to_cart_style check_custom_the_styling" name="ct_catcbfw_add_to_cart_styling[btn_border_color]" <?php echo esc_attr( isset( $add_cart_styling_setting['btn_border_color'] ) ? 'checked' : '' ); ?>>

						<p>
							<i>
								<?php echo esc_html__( 'Enable checkbox to button border color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Button Border Color', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="color" class="ct_catcbfw_add_to_cart_style btn_border_color" name="ct_catcbfw_add_to_cart_styling[button_border_color]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_color'] ) ? $add_cart_styling_setting['button_border_color'] : '' ); ?>" min="1">

						<p>
							<i>
								<?php echo esc_html__( 'Set button border color.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Font Size', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="number" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_font_size]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_font_size'] ) ? $add_cart_styling_setting['button_font_size'] : '14' ); ?>" min="0">

						<?php echo esc_html__( 'px', 'add-to-cart-button-customizer' ); ?>

						<p>
							<i>
								<?php echo esc_html__( 'Set font size.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Font Weight', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">
						<?php

						$weight = array( '100', '200', '300', '400', '500', '600', '700', '800', '900', 'bold', 'bolder', 'lighter', 'normal', 'initial', 'inherit', 'unset', 'revert' );

						$button_font_weight = isset( $add_cart_styling_setting['button_font_weight'] ) ? $add_cart_styling_setting['button_font_weight'] : '100';
						?>
						<select class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_font_weight]">

							<?php foreach ( $weight as $value ) : ?>

								<option value="<?php echo esc_attr( $value ); ?>" <?php if ( $value === (string) $button_font_weight ) { ?>
									selected
									<?php } ?> >
									<?php echo esc_attr( $value ); ?>
								</option>

							<?php endforeach ?>

						</select>

						<p>
							<i>
								<?php echo esc_html__( 'Set font weight.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Button Border And Border Radius', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input value="yes" type="checkbox" data-hide_class="enable_btn_border_nd_border_radius" class="ct_catcbfw_add_to_cart_style check_custom_the_styling" name="ct_catcbfw_add_to_cart_styling[enable_btn_border_nd_border_radius]" <?php echo esc_attr( isset( $add_cart_styling_setting['enable_btn_border_nd_border_radius'] ) ? 'checked' : '' ); ?>>

						<p>
							<i>
								<?php echo esc_html__( 'Enable checkbox to set button border and border radius .', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Border', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="number" class="ct_catcbfw_add_to_cart_style enable_btn_border_nd_border_radius" name="ct_catcbfw_add_to_cart_styling[button_border]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border'] ) ? $add_cart_styling_setting['button_border'] : '' ); ?>" min="0">

						<?php echo esc_html__( 'px.', 'add-to-cart-button-customizer' ); ?>

						<p>
							<i>
								<?php echo esc_html__( 'Set border.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Border Radius', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<input type="number" class="ct_catcbfw_add_to_cart_style enable_btn_border_nd_border_radius" name="ct_catcbfw_add_to_cart_styling[button_border_radius]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_radius'] ) ? $add_cart_styling_setting['button_border_radius'] : '' ); ?>" min="0">

						<p>
							<i>
								<?php echo esc_html__( 'Set border radius.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Padding', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input value="yes" type="checkbox" data-hide_class="enable_padding" class="ct_catcbfw_add_to_cart_style check_custom_the_styling" name="ct_catcbfw_add_to_cart_styling[enable_padding]" <?php echo esc_attr( isset( $add_cart_styling_setting['enable_padding'] ) ? 'checked' : '' ); ?>>

						<p>
							<i>
								<?php echo esc_html__( 'Enable checkbox to set padding.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Padding', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">

						<ul class="ct_catcbfw_ul" style="list-style-type:none;display: inline-flex;">
							<li>

								<label><?php echo esc_html__( 'Left', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style enable_padding" name="ct_catcbfw_add_to_cart_styling[button_border_padding_left]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_left'] ) ? $add_cart_styling_setting['button_border_padding_left'] : '' ); ?>">
							</li>

							<li>

								<label><?php echo esc_html__( 'Top', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_border_padding_top]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_top'] ) ? $add_cart_styling_setting['button_border_padding_top'] : '' ); ?>">
							</li>

							<li>

								<label><?php echo esc_html__( 'Bottom', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_border_padding_bottom]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_bottom'] ) ? $add_cart_styling_setting['button_border_padding_bottom'] : '' ); ?>">
							</li>
							<li>
								<label><?php echo esc_html__( 'Right ', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_border_padding_right]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_padding_right'] ) ? $add_cart_styling_setting['button_border_padding_right'] : '' ); ?>">

							</li>
						</ul>





						<p>
							<i>
								<?php echo esc_html__( 'Set Padding.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
				<tr>
					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Enable Margin', 'add-to-cart-button-customizer' ); ?></th>
					<td class="ct_catcbfw_table_content">
						<input value="yes" type="checkbox" data-hide_class="enable_margin" class="ct_catcbfw_add_to_cart_style check_custom_the_styling" name="ct_catcbfw_add_to_cart_styling[enable_margin]" <?php echo esc_attr( isset( $add_cart_styling_setting['enable_margin'] ) ? 'checked' : '' ); ?>>

						<p>
							<i>
								<?php echo esc_html__( 'Enable checkbox to set margin.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>

				<tr>

					<th class="ct_catcbfw_table_heading"><?php echo esc_html__( 'Margin', 'add-to-cart-button-customizer' ); ?></th>

					<td class="ct_catcbfw_table_content">
						<ul style="list-style-type:none;display: inline-flex;" class="ct_catcbfw_ul">
							<li>
								<label><?php echo esc_html__( 'Top', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style enable_margin" name="ct_catcbfw_add_to_cart_styling[button_border_margin_top]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_top'] ) ? $add_cart_styling_setting['button_border_margin_top'] : '' ); ?>">

							</li>

							<li>

								<label><?php echo esc_html__( 'Left', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_border_margin_left]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_left'] ) ? $add_cart_styling_setting['button_border_margin_left'] : '' ); ?>">
							</li>

							<li>

								<label><?php echo esc_html__( 'Bottom', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_border_margin_bottom]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_bottom'] ) ? $add_cart_styling_setting['button_border_margin_bottom'] : '' ); ?>">
							</li>

							<li>

								<label><?php echo esc_html__( 'Right ', 'add-to-cart-button-customizer' ); ?></label>

								<input type="text" class="ct_catcbfw_add_to_cart_style" name="ct_catcbfw_add_to_cart_styling[button_border_margin_right]" value="<?php echo esc_attr( isset( $add_cart_styling_setting['button_border_margin_right'] ) ? $add_cart_styling_setting['button_border_margin_right'] : '' ); ?>">
							</li>
						</ul>






						<p>
							<i>
								<?php echo esc_html__( 'Set margin.', 'add-to-cart-button-customizer' ); ?>
							</i>
						</p>

					</td>
				</tr>
			</table>
			<?php

		}

		/**
		 * Update post data .
		 *
		 * @param int $post_id post id of current post.
		 */
		public function cloud_catcbfw_save_meta_values( $post_id ) {

				// Bail if we're doing an auto save.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

				// if our current user can't edit this post, bail.
			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}

				// For custom post type.
			$exclude_statuses = array(
				'auto-draft',
				'trash',
			);

			$ka_notification_plugin_action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

			if ( ! in_array( (string) get_post_status( $post_id ), $exclude_statuses, true ) && ! is_ajax() && 'untrash' !== (string) $ka_notification_plugin_action ) {

				$nonce = isset( $_POST['ct_catcbfw_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_nonce'] ) ) : '';

				if ( ! wp_verify_nonce( $nonce, 'ct_catcbfw_nonce' ) ) {
					wp_die( esc_html__( 'Security Violate!', 'add-to-cart-button-customizer' ) );
				}

				$select_pages = isset( $_POST['ct_catcbfw_select_pages'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_select_pages'] ) ) : array();

				update_post_meta( $post_id, 'ct_catcbfw_select_pages', $select_pages );

				$ct_catcbfw_add_to_cart_button_icon = isset( $_POST['ct_catcbfw_add_to_cart_button_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_add_to_cart_button_icon'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_add_to_cart_button_icon', $ct_catcbfw_add_to_cart_button_icon );

				$ct_catcbfw_enable_add_to_cart_button_styling = isset( $_POST['ct_catcbfw_enable_add_to_cart_button_styling'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_enable_add_to_cart_button_styling'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_enable_add_to_cart_button_styling', $ct_catcbfw_enable_add_to_cart_button_styling );

				$add_to_cart_button_text = isset( $_POST['ct_catcbfw_add_to_cart_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_add_to_cart_button_text'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_add_to_cart_button_text', $add_to_cart_button_text );

				$ct_catcbfw_enable_add_to_cart_styling = isset( $_POST['ct_catcbfw_enable_add_to_cart_styling'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_enable_add_to_cart_styling'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_enable_add_to_cart_styling', $ct_catcbfw_enable_add_to_cart_styling );

				$ct_catcbfw_add_to_cart_button_icon = isset( $_POST['ct_catcbfw_add_to_cart_button_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_add_to_cart_button_icon'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_add_to_cart_button_icon', $ct_catcbfw_add_to_cart_button_icon );

				$ct_catcbfw_can_user_get_this_bundle_again = isset( $_POST['ct_catcbfw_can_user_get_this_bundle_again'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_can_user_get_this_bundle_again'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_can_user_get_this_bundle_again', $ct_catcbfw_can_user_get_this_bundle_again );

				$ct_catcbfw_add_to_crt_btn_icon_class = isset( $_POST['ct_catcbfw_add_to_crt_btn_icon_class'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_add_to_crt_btn_icon_class'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_add_to_crt_btn_icon_class', $ct_catcbfw_add_to_crt_btn_icon_class );

				$ct_catcbfw_add_to_crt_btn_uploaded_icon = isset( $_POST['ct_catcbfw_add_to_crt_btn_uploaded_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_add_to_crt_btn_uploaded_icon'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_add_to_crt_btn_uploaded_icon', $ct_catcbfw_add_to_crt_btn_uploaded_icon );

				$enable_add_to_cart_button_styling = isset( $_POST['ct_catcbfw_enable_add_to_cart_button_styling'] ) ? sanitize_text_field( wp_unslash( $_POST['ct_catcbfw_enable_add_to_cart_button_styling'] ) ) : '';

				update_post_meta( $post_id, 'ct_catcbfw_enable_add_to_cart_button_styling', $enable_add_to_cart_button_styling );

				$ct_catcbfw_add_to_cart_styling = isset( $_POST['ct_catcbfw_add_to_cart_styling'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_add_to_cart_styling'] ) ) : array();

				update_post_meta( $post_id, 'ct_catcbfw_add_to_cart_styling', $ct_catcbfw_add_to_cart_styling );

				$ct_catcbfw_add_to_cart_styling = isset( $_POST['ct_catcbfw_add_to_cart_styling'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_add_to_cart_styling'] ) ) : array();

				update_post_meta( $post_id, 'ct_catcbfw_add_to_cart_styling', $ct_catcbfw_add_to_cart_styling );

				$user_role = isset( $_POST['ct_catcbfw_select_user_from_switch'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_select_user_from_switch'] ) ) : array();
				update_post_meta( $post_id, 'ct_catcbfw_select_user_from_switch', $user_role );

				$ct_catcbfw_product_included_list = isset( $_POST['ct_catcbfw_product_included_list'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_product_included_list'] ) ) : array();
				update_post_meta( $post_id, 'ct_catcbfw_product_included_list', $ct_catcbfw_product_included_list );

				$ct_catcbfw_product_exclusion_list = isset( $_POST['ct_catcbfw_product_exclusion_list'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_product_exclusion_list'] ) ) : array();
				update_post_meta( $post_id, 'ct_catcbfw_product_exclusion_list', $ct_catcbfw_product_exclusion_list );

				$excluded_product = isset( $_POST['ct_catcbfw_included_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_included_category'] ) ) : array();
				update_post_meta( $post_id, 'ct_catcbfw_included_category', $excluded_product );

				$included_product_tags = isset( $_POST['ct_catcbfw_product_tags'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_product_tags'] ) ) : array();
				update_post_meta( $post_id, 'ct_catcbfw_product_tags', $included_product_tags );

				$selected_categories = isset( $_POST['ct_catcbfw_included_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ct_catcbfw_included_category'] ) ) : array();
				update_post_meta( $post_id, 'ct_catcbfw_included_category', $selected_categories );
			}
		}

		/**
		 * Register the JavaScript for the admin area.
		 */
		public function cloud_catcbfw_enqueue_scripts() {

			wp_enqueue_media();

			wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', false, '4.7.0', false );

			wp_enqueue_style( 'admin_css', CT_CATCBFW_URL . '/assets/css/ct-cpfw-admin.css', false, '1.1.0' );

			wp_enqueue_script( 'admin_js', CT_CATCBFW_URL . '/assets/js/ct-cpfw-admin.js', array( 'jquery' ), '1.1.0', false );

			wp_enqueue_style( 'select2-css', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), '5.7.2' );

			wp_enqueue_script( 'select2-js', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );

			$order_renew = array(
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'ct_catcbfw_nonce' ),

			);
			wp_localize_script( 'admin_js', 'php_var', $order_renew );

		}

		/**
		 * Product Live Search.
		 */
		public function ct_catcbfw_product_search() {
			$nonce = isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
			if ( ! wp_verify_nonce( $nonce, 'ct_catcbfw_nonce' ) ) {
				die( 'Failed ajax security check!' );
			}
			if ( isset( $_POST['q'] ) && ! empty( $_POST['q'] ) ) {

				$pro = sanitize_text_field( wp_unslash( $_POST['q'] ) );
			} else {
				$pro = '';
			}
			$data_array = array();
			$args       = array(
				'post_type'   => array( 'product', 'product_variation' ),
				'post_status' => 'publish',
				'numberposts' => -1,
				's'           => $pro,
				'type'        => array( 'simple', 'variable' ),
				'orderby'     => 'relevance',
				'order'       => 'ASC',

			);
			$pros = wc_get_products( $args );

			if ( ! empty( $pros ) ) {
				foreach ( $pros as $proo ) {
					$title        = ( mb_strlen( $proo->get_name() ) > 50 ) ? mb_substr( $proo->get_name(), 0, 49 ) . '...' : $proo->get_name();
					$data_array[] = array( $proo->get_id(), $title ); // array( Post ID, Post Title ).
				}
			}
			echo wp_json_encode( $data_array );
			die();
		}

		/**
		 * Category live search.
		 */
		public function cloud_catcbfw_category_search() {
			$nonce = isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
			if ( ! wp_verify_nonce( $nonce, 'ct_catcbfw_nonce' ) ) {
				die( 'Failed ajax security check!' );
			}
			if ( isset( $_POST['q'] ) && ! empty( $_POST['q'] ) ) {

				$pro = sanitize_text_field( wp_unslash( $_POST['q'] ) );
			} else {
				$pro = '';
			}
			$data_array = array();
			$orderby    = 'name';
			$order      = 'asc';
			$hide_empty = false;
			$cat_args   = array(
				'orderby'    => $orderby,
				'order'      => $order,
				'hide_empty' => $hide_empty,
				'name__like' => $pro,
			);

			$product_categories = get_terms( 'product_cat', $cat_args );

			if ( ! empty( $product_categories ) ) {
				foreach ( $product_categories as $proo ) {

					$pro_front_post = ( mb_strlen( $proo->name ) > 50 ) ? mb_substr( $proo->name, 0, 49 ) . '...' : $proo->name;

					$data_array[] = array( $proo->term_id, $pro_front_post ); // array( Post ID, Post Title ).

				}
			}
			echo wp_json_encode( $data_array );
			die();
		}


	}

	/**
	 * Object of Class.
	 */
	new Catcbfw_Admin();

}
