<?php
/**
 * Add To Cart Button Customizer.
 *
 * @package Add To Cart Button Customizer
 * @since 2.0.0
 * @version 2.0.0
 */

/**
 * Plugin Name:       Add To Cart Button Customizer
 * Plugin URI:        https://cloudtechnologies.store/
 * Description:       Fully Customize your add to cart button.
 * Version:           2.0.0
 * Author:            Cloud Technologies.
 * Developed By:      Cloud Technologies.
 * Author URI:        https://cloudtechnologies.store/
 * Support:           https://cloudtechnologies.store/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt.
 * Domain Path:       /languages.
 * Text Domain:       add-to-cart-button-customizer
 */

/**
 * Restrict to direct access.
 */
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Check if WooCommerce is not installed and active.
 */
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {

    /**
     * Check if function not exist my_admin_notice.
     */
    if (!function_exists('my_admin_notice')) {


        /**
         * Check if WooCommerce is not installed and active.
         */
        function my_admin_notice()
        {

            // Deactivate the plugin.
            deactivate_plugins(__FILE__);

            echo wp_kses_post('<div id="message" class="error"><p><strong>Add To Cart Customizer module is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>');
        }

        add_action('admin_notices', 'my_admin_notice');
    }
} elseif (!class_exists('Custom_Add_To_Cart_Button_For_Woocommerce')) {

    /**
     * Main Class.
     */
    class Custom_Add_To_Cart_Button_For_Woocommerce
    {

        /**
         * Constructor Of Class.
         */
        public function __construct()
        {
            $this->catcbfw_constant_vars();

            add_action('wp_loaded', array($this, 'catcbfw_main_init'));
            add_action('woocommerce_init', array($this, 'catcbfw_custom_post'));

            if (is_admin()) {

                // include Admin Class.

                include CT_CATCBFW_PLUGIN_DIR . 'includes/admin/class-catcbfw-admin.php';
            } else {
                include CT_CATCBFW_PLUGIN_DIR . 'includes/public/class-catcbfw-front.php';
            }
        }


        /**
         * Loading text domain.
         */
        public function catcbfw_main_init()
        {
            if (function_exists('load_plugin_textdomain')) {
                load_plugin_textdomain('add-to-cart-button-customizer', false, dirname(plugin_basename(__FILE__)) . '/languages/');
            }
        }


        /**
         * Defining Variable.
         */
        public function catcbfw_constant_vars()
        {
            if (!defined('CT_CATCBFW_URL')) {
                define('CT_CATCBFW_URL', plugin_dir_url(__FILE__));
            }
            if (!defined('CT_CATCBFW_BASENAME')) {
                define('CT_CATCBFW_BASENAME', plugin_basename(__FILE__));
            }
            if (!defined('CT_CATCBFW_PLUGIN_DIR')) {
                define('CT_CATCBFW_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }
        }


        /**
         * Register custom post.
         */
        public function catcbfw_custom_post()
        {
            $supports = array('title', 'page-attributes');

            $labels = array(
                'name'           => __('Add To Cart Button Customizer Rule', 'add-to-cart-button-customizer'),
                'singular_name'  => __('Add To Cart Button Customizer Rule', 'add-to-cart-button-customizer'),
                'menu_name'      => __('Add To Cart Button Customizer Rule', 'add-to-cart-button-customizer'),
                'name_admin_bar' => __('Add To Cart Button Customizer', 'admin bar'),
                'edit_item'      => __('Edit Rule', 'add-to-cart-button-customizer'),
                'view_item'      => __('View Rule', 'add-to-cart-button-customizer'),
                'all_items'      => __('Add To Cart Button Customizer', 'add-to-cart-button-customizer'),
                'search_items'   => __('Search Add To Cart Button Customizer', 'add-to-cart-button-customizer'),
                'not_found'      => __('No Add To Cart Button Customizer found', 'add-to-cart-button-customizer'),
                'attributes'     => __('Priority', 'add-to-cart-button-customizer'),
            );
            $args   = array(
                'supports'          => $supports,
                'labels'            => $labels,
                'description'       => '',
                'public'            => true,
                'show_in_menu'      => 'woocommerce',
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => false,

                'can_export'        => true,
                'capability_type'   => 'post',
                'show_in_rest'      => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'ct_cstm_add_t_c_btn'),
                'has_archive'       => true,
                'hierarchical'      => false,
            );
            register_post_type('ct_cstm_add_t_c_btn', $args); // Register Post type.
        }
    }

    /**
     * Object Of Class.
     */
    new Custom_Add_To_Cart_Button_For_Woocommerce();
}

function add_to_cart_button_customizer_custom_plugin_action_links($links) {


    $apiUrl = 'https://cloudtechnologies.store/wp-json/custom-woocommerce-api/v1/products/woocommerce';

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    if ($response) {

        $products_details = json_decode($response, true);
        ?>
        <section class="ct-premium-plugin-popup add-to-cart-button-customizer-premium-popup"  style="display:none;">
            <div class="ct-premium-plugin-popup-box" >
                <div class="ct-premium-plugin-cross-btn" data-class_name="add-to-cart-button-customizer-premium-popup"> x</div>
                <div class="ct-premium-plugin-popup-box-header">
                    <h3><?php echo esc_html__('Buy Our Premium Extension' , 'cloud_tech_psbscac') ?></h3>
                </div>
                <div class="ct-premium-plugin-poppup-body">
                    <ul>
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
                            <li>
                                <div class="ct-premium-plugin-image">
                                    <?php if ( !empty( $image ) ): ?>
                                        <img style="width: 100%;" src="<?php echo esc_url( $image ); ?>">

                                    <?php endif ?>
                                </div>
                                <div class="ct-premium-plugin-name-url">
                                    <h4>
                                        <a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_attr( $product_name ); ?></a>
                                    </h4>
                                </div>

                                <div class="" style="width:10%;">
                                    <?php if ( !empty( $sale_price )  ){  ?>
                                        <del>$<?php echo esc_attr($regular_price); ?></del>
                                        <span class="ct-premium-plugin-sale-price">$<?php echo esc_attr($sale_price); ?></span>
                                    <?php } else  { ?>
                                        <span class="ct-premium-plugin-sale-price">$<?php echo esc_attr($regular_price); ?></span>

                                    <?php } ?>

                                </div>
                                <div class="ct-premium-plugin-short-description">
                                    <p><?php echo wp_kses_post( $short_description ); ?>.</p>
                                </div>
                                <div style="text-align:right;width: 12%;" >
                                    <a class="button button-primary btn" target="_blank" href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html__( 'Buy Now' , 'cloud_tech_psbscac' ); ?></a>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </section>
        <?php

    }
    $links[] = '<a href="" class="ct-premium-module-link" data-class_name="add-to-cart-button-customizer-premium-popup">Buy Our Premium Extension</a>';

    return $links;
}

// Hook the function into the plugin action links filter.
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_to_cart_button_customizer_custom_plugin_action_links');

