<?php
if(!function_exists('cms_widget_register')) return;

add_action('widgets_init', 'register_cart_search_widget');
function register_cart_search_widget() {
    cms_widget_register('WC_Widget_Cart_Search');
}

class WC_Widget_Cart_Search extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'widget_cart_search', // Base ID
            esc_html__('CMS WooCommerce Cart', 'foldery' ), // Name
            array('description' => esc_html__("Display the user's Cart form in the sidebar.", 'foldery' ),) // Args
        );
        add_action('wp_enqueue_scripts', array($this, 'widget_scripts'));
    }
    function widget_scripts() {
        wp_enqueue_script('widget_cart_search_scripts', get_template_directory_uri() . '/inc/widgets/cms_cart_search.js');
        wp_enqueue_style('widget_cart_search_scripts', get_template_directory_uri() . '/inc/widgets/cms_cart_search.css');
    }
    function widget($args, $instance) {
        extract(shortcode_atts($instance,$args));
        $title = apply_filters('widget_title', empty( $instance['title'] ) ?'' : $instance['title'], $instance, $this->id_base );
        $show_cart_type = isset($instance['show_cart_type']) ? $instance['show_cart_type'] : 0;
        $hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;
        ob_start();
        echo isset($before_widget)?$before_widget:'';
        $before_title = isset($before_title)?$before_title:'';
        $after_title = isset($after_title)?$after_title:'';
        if ( $title ) cms_allowed_html($before_title . $title . $after_title);
        $total = 0;
        global $woocommerce;
        ?>
        <div class="widget_cart_search_wrap clearfix">
            <ul>
                <li>
                    <?php switch ($show_cart_type) {
                        case '0': ?>    
                            <a href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>" class="icon_cart_wrap">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="cart_total">(<?php cms_allowed_html($woocommerce->cart->get_cart_contents_count());?>)</span>
                            </a>
                    <?php   
                        break;
                        case '1':
                    ?>
                            <a href="javascript:void(0)" class="icon_cart_wrap" data-display=".shopping_cart_dropdown" data-no_display=".widget_searchform_content">
                                <i class="fa fa-shopping-cart cart-icon"></i>
                                <span class="cart_total"><?php cms_allowed_html($woocommerce->cart->get_cart_contents_count());?></span>
                            </a>
                    <?php    
                        break;
                    } ?>
                </li>
            </ul>
            <?php if($show_cart_type == '1') { ?>
            <div class="shopping_cart_dropdown">
                <div class="shopping_cart_dropdown_inner_wrap">
                <div class="shopping_cart_dropdown_inner">
                    <?php
                    $cart_is_empty = sizeof( $woocommerce->cart->get_cart() ) <= 0;
                    $list_class = array( 'cart_list', 'product_list_widget' );
                    ?>
                    <ul class="list-unstyled <?php echo implode(' ', $list_class); ?>">
                        <?php if ( !$cart_is_empty ) : ?>
                            <?php foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) :
                                $_product = $cart_item['data'];
                                // Only display if allowed
                                if ( ! $_product->exists() || $cart_item['quantity'] == 0 ) {
                                    continue;
                                }
                                // Get price
                                $product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
                                $product_price = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
                                ?>
                                <li class="cart-list clearfix">
                                    <div class="cart-img pull-left">
                                        <a class="cart-list-image" href="<?php echo get_permalink( $cart_item['product_id'] ); ?>">
                                            <?php cms_allowed_html($_product->get_image()); ?>  
                                        </a>
                                    </div>
                                    <div class="cart-item-info">
                                        <?php echo '<h6>'.apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product ).'</h6>'; ?>
                                        <?php cms_allowed_html($woocommerce->cart->get_item_data( $cart_item )); ?>
                                        <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <li class="cart-list clearfix"><?php esc_html_e( 'No products in the cart.', 'foldery' ); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                </div>
                <?php if ( sizeof( $woocommerce->cart->get_cart() ) <= 0 ) : ?>
                <?php endif; ?>
                <a href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>" class="btn btn-default left wc-forward"><?php esc_html_e( 'Cart', 'foldery' ); ?></a>
                <span class="total pull-right"><?php esc_html_e( 'Total', 'foldery' ); ?>:<span><?php echo ' '.$woocommerce->cart->get_cart_subtotal(); ?></span></span>
                <?php if ( sizeof( $woocommerce->cart->get_cart() ) <= 0 ) : ?>
                <?php endif; ?>
            </div>
            <?php } ?>
        </div>
        <?php
        echo isset($after_widget)?$after_widget:'';
        echo ob_get_clean();
    }
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['show_cart_type'] = $new_instance['show_cart_type'];
        return $instance;
    }
    function form( $instance ) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $show_cart_type = isset($instance['show_cart_type']) ? $instance['show_cart_type'] : 0;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'foldery' ); ?></label>
            <input id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_url($this->get_field_id('show_cart_type')); ?>"><?php esc_html_e( 'Cart Type', 'foldery' ); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('show_cart_type')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('show_cart_type')); ?>">
                <option value="0" <?php selected($show_cart_type,0); ?>><?php echo __('Link to cart page', 'foldery' ); ?></option>
                <option value="1" <?php selected($show_cart_type,1); ?>><?php echo __('Show dropdown cart', 'foldery' ); ?></option>
            </select>
        </p>
    <?php
    }
}

//
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment', 10, 1 );
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_content', 10, 1 );
if(!function_exists('woocommerce_header_add_to_cart_fragment')){
    function woocommerce_header_add_to_cart_fragment( $fragments ) {
        global $woocommerce;
        ob_start();
        ?>
        <span class="cart_total">(<?php echo esc_html($woocommerce->cart->cart_contents_count); ?>)</span>
        <?php
        $fragments['span.cart_total'] = ob_get_clean();
        return $fragments;
    }
}

if(!function_exists('woocommerce_header_add_to_cart_content')){
    function woocommerce_header_add_to_cart_content( $fragments ) {
    global $woocommerce;
    ob_start();
    ?>
    <div class="shopping_cart_dropdown">
        <div class="shopping_cart_dropdown_inner_wrap">
        <div class="shopping_cart_dropdown_inner">
            <?php
            $cart_is_empty = sizeof( $woocommerce->cart->get_cart() ) <= 0;
            $list_class = array( 'cart_list', 'product_list_widget' );
            ?>
            <ul class="list-unstyled <?php echo implode(' ', $list_class); ?>">
                <?php if ( !$cart_is_empty ) : ?>
                    <?php foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) :
                        $_product = $cart_item['data'];
                        // Only display if allowed
                        if ( ! $_product->exists() || $cart_item['quantity'] == 0 ) {
                            continue;
                        }
                        // Get price
                        $product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
                        $product_price = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
                        ?>
                        <li class="cart-list clearfix">
                            <div class="cart-img">
                                <a class="cart-list-image" href="<?php echo get_permalink( $cart_item['product_id'] ); ?>">
                                    <?php cms_allowed_html($_product->get_image()); ?>  
                                </a>
                            </div>
                            <div class="cart-info">
                                <?php echo '<h6>'.apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product ).'</h6>'; ?>
                                <?php cms_allowed_html($woocommerce->cart->get_item_data( $cart_item )); ?>
                                <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li class="cart-list clearfix"><?php esc_html_e( 'No products in the cart.', 'foldery' ); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        </div>
        <?php if ( sizeof( $woocommerce->cart->get_cart() ) <= 0 ) : ?>
        <?php endif; ?>
        <a href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>" class="btn btn-default left wc-forward"><?php esc_html_e( 'Cart', 'foldery' ); ?></a>
        <span class="total pull-right"><?php esc_html_e( 'Total', 'foldery' ); ?>:<span><?php echo ' '.$woocommerce->cart->get_cart_subtotal(); ?></span></span>
        <?php if ( sizeof( $woocommerce->cart->get_cart() ) <= 0 ) : ?>
        <?php endif; ?>
    </div>
    <?php
    $fragments['div.shopping_cart_dropdown'] = ob_get_clean();
    return $fragments;
    }
} 
?>