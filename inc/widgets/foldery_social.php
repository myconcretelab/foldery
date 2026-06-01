<?php
if(!function_exists('foldery_register_widget')) return;

add_action('widgets_init', 'register_social_widget');
function register_social_widget() {
    foldery_register_widget('Foldery_Social_Widget');
}


class Foldery_Social_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'foldery_social_widget', // Base ID
            __('Foldery Social', 'foldery' ), // Name
            array('description' => esc_html__('Foldery Social Widget', 'foldery' ),) // Args
        );
        add_action('wp_enqueue_scripts', array($this, 'widget_scripts'));
    }
    function widget_scripts() {
        wp_enqueue_style('widget_foldery_social_scripts', get_template_directory_uri() . '/inc/widgets/foldery_social.css');
    }
    function widget($args, $instance) {
        extract($args);
		if (!empty($instance['title'])) {
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Foldery Social', 'foldery' ) : $instance['title'], $instance, $this->id_base);
        }

        $style = 'default';
        if(!empty($instance['style'])){
            $style = $instance['style'];
        }
        $align = 'text-left';
        if(!empty($instance['align'])){
            $align = $instance['align'];
        }

        $icon_1 = $instance['icon_1'];
        $link_1 = $instance['link_1'];

        $icon_2 = $instance['icon_2'];
        $link_2 = $instance['link_2'];

        $icon_3 = $instance['icon_3'];
        $link_3 = $instance['link_3'];

        $icon_4 = $instance['icon_4'];
        $link_4 = $instance['link_4'];

        $icon_5 = $instance['icon_5'];
        $link_5 = $instance['link_5'];

        $icon_6 = $instance['icon_6'];
        $link_6 = $instance['link_6'];

        $icon_7 = $instance['icon_7'];
        $link_7 = $instance['link_7'];

        $icon_8 = $instance['icon_8'];
        $link_8 = $instance['link_8'];

        $icon_9 = $instance['icon_9'];
        $link_9 = $instance['link_9'];

        $icon_10 = $instance['icon_10'];
        $link_10 = $instance['link_10'];

        $icon_11 = $instance['icon_11'];
        $link_11 = $instance['link_11'];

        $icon_12 = $instance['icon_12'];
        $link_12 = $instance['link_12'];

        $icon_13 = $instance['icon_13'];
        $link_13 = $instance['link_13'];

        $extra_class = !empty($instance['extra_class']) ? $instance['extra_class'] : "";

        // no 'class' attribute - add one with the value of width
        if( strpos($before_widget, 'class') === false ) {
            $before_widget = str_replace('>', 'class="'. $extra_class . '"', $before_widget);
        }
        // there is 'class' attribute - append width value to it
        else {
            $before_widget = str_replace('class="', 'class="'. $extra_class . ' ', $before_widget);
        }
        foldery_allowed_html($before_widget);

        if (!empty($title))
            foldery_allowed_html($before_title . $title . $after_title);

        echo '<div class="cms-social '.$style.' '.$align.'">';

        if ($link_1 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_1).'"><i class="'.$icon_1.'"></i></a>';
        }

        if ($link_2 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_2).'"><i class="'.$icon_2.'"></i></a>';
        }

        if ($link_3 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_3).'"><i class="'.$icon_3.'"></i></a>';
        }

        if ($link_4 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_4).'"><i class="'.$icon_4.'"></i></a>';
        }

        if ($link_5 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_5).'"><i class="'.$icon_5.'"></i></a>';
        }

        if ($link_6 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_6).'"><i class="'.$icon_6.'"></i></a>';
        }

        if ($link_7 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_7).'"><i class="'.$icon_7.'"></i></a>';
        }

        if ($link_8 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_8).'"><i class="'.$icon_8.'"></i></a>';
        }

        if ($link_9 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_9).'"><i class="'.$icon_9.'"></i></a>';
        }

        if ($link_10 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_10).'"><i class="'.$icon_10.'"></i></a>';
        }

        if ($link_11 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_11).'"><i class="'.$icon_11.'"></i></a>';
        }

        if ($link_12 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_12).'"><i class="'.$icon_12.'"></i></a>';
        }

        if ($link_13 != '') {
            echo '<a target="_blank"  href="'.esc_url($link_13).'"><i class="'.$icon_13.'"></i></a>';
        }

        echo "</div>";

        echo foldery_allowed_html($after_widget);
    }

    function update( $new_instance, $old_instance ) {
         $instance = $old_instance;
         $instance['title'] = strip_tags($new_instance['title']);

         $instance['style'] = strip_tags($new_instance['style']);
         $instance['align'] = strip_tags($new_instance['align']);

         $instance['icon_1'] = strip_tags($new_instance['icon_1']);
         $instance['link_1'] = strip_tags($new_instance['link_1']);

         $instance['icon_2'] = strip_tags($new_instance['icon_2']);
         $instance['link_2'] = strip_tags($new_instance['link_2']);

         $instance['icon_3'] = strip_tags($new_instance['icon_3']);
         $instance['link_3'] = strip_tags($new_instance['link_3']);

         $instance['icon_4'] = strip_tags($new_instance['icon_4']);
         $instance['link_4'] = strip_tags($new_instance['link_4']);

         $instance['icon_5'] = strip_tags($new_instance['icon_5']);
         $instance['link_5'] = strip_tags($new_instance['link_5']);

         $instance['icon_6'] = strip_tags($new_instance['icon_6']);
         $instance['link_6'] = strip_tags($new_instance['link_6']);

         $instance['icon_7'] = strip_tags($new_instance['icon_7']);
         $instance['link_7'] = strip_tags($new_instance['link_7']);

         $instance['icon_8'] = strip_tags($new_instance['icon_8']);
         $instance['link_8'] = strip_tags($new_instance['link_8']);

         $instance['icon_9'] = strip_tags($new_instance['icon_9']);
         $instance['link_9'] = strip_tags($new_instance['link_9']);

         $instance['icon_10'] = strip_tags($new_instance['icon_10']);
         $instance['link_10'] = strip_tags($new_instance['link_10']);

         $instance['icon_11'] = strip_tags($new_instance['icon_11']);
         $instance['link_11'] = strip_tags($new_instance['link_11']);

         $instance['icon_12'] = strip_tags($new_instance['icon_12']);
         $instance['link_12'] = strip_tags($new_instance['link_12']);

         $instance['icon_13'] = strip_tags($new_instance['icon_13']);
         $instance['link_13'] = strip_tags($new_instance['link_13']);

         $instance['extra_class'] = $new_instance['extra_class'];

         return $instance;
    }

    function form( $instance ) {
         $title = isset($instance['title']) ? esc_attr($instance['title']) : '';

         $style = isset($instance['style']) ? esc_attr($instance['style']) : '';
         $align = isset($instance['align']) ? esc_attr($instance['align']) : '';

         $icon_1 = isset($instance['icon_1']) ? esc_attr($instance['icon_1']) : '';
         $link_1 = isset($instance['link_1']) ? esc_attr($instance['link_1']) : '';

         $icon_2 = isset($instance['icon_2']) ? esc_attr($instance['icon_2']) : '';
         $link_2 = isset($instance['link_2']) ? esc_attr($instance['link_2']) : '';

         $icon_3 = isset($instance['icon_3']) ? esc_attr($instance['icon_3']) : '';
         $link_3 = isset($instance['link_3']) ? esc_attr($instance['link_3']) : '';

         $icon_4 = isset($instance['icon_4']) ? esc_attr($instance['icon_4']) : '';
         $link_4 = isset($instance['link_4']) ? esc_attr($instance['link_4']) : '';

         $icon_5 = isset($instance['icon_5']) ? esc_attr($instance['icon_5']) : '';
         $link_5 = isset($instance['link_5']) ? esc_attr($instance['link_5']) : '';

         $icon_6 = isset($instance['icon_6']) ? esc_attr($instance['icon_6']) : '';
         $link_6 = isset($instance['link_6']) ? esc_attr($instance['link_6']) : '';

         $icon_7 = isset($instance['icon_7']) ? esc_attr($instance['icon_7']) : '';
         $link_7 = isset($instance['link_7']) ? esc_attr($instance['link_7']) : '';

         $icon_8 = isset($instance['icon_8']) ? esc_attr($instance['icon_8']) : '';
         $link_8 = isset($instance['link_8']) ? esc_attr($instance['link_8']) : '';

         $icon_9 = isset($instance['icon_9']) ? esc_attr($instance['icon_9']) : '';
         $link_9 = isset($instance['link_9']) ? esc_attr($instance['link_9']) : '';

         $icon_10 = isset($instance['icon_10']) ? esc_attr($instance['icon_10']) : '';
         $link_10 = isset($instance['link_10']) ? esc_attr($instance['link_10']) : '';

         $icon_11 = isset($instance['icon_11']) ? esc_attr($instance['icon_11']) : '';
         $link_11 = isset($instance['link_11']) ? esc_attr($instance['link_11']) : '';

         $icon_12 = isset($instance['icon_12']) ? esc_attr($instance['icon_12']) : '';
         $link_12 = isset($instance['link_12']) ? esc_attr($instance['link_12']) : '';

         $icon_13 = isset($instance['icon_13']) ? esc_attr($instance['icon_13']) : '';
         $link_13 = isset($instance['link_13']) ? esc_attr($instance['link_13']) : '';

		 $extra_class = isset($instance['extra_class']) ? esc_attr($instance['extra_class']) : '';
         ?>
         <p><label for="<?php echo esc_url($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

         <p><label for="<?php echo esc_url($this->get_field_id('style')); ?>"><?php esc_html_e( 'Style:', 'foldery' ); ?></label>
         <select class="widefat" id="<?php echo esc_attr( $this->get_field_id('style') ); ?>" name="<?php echo esc_attr( $this->get_field_name('style') ); ?>">
            <option value="default"<?php if( $style == 'default' ){ echo 'selected="selected"';} ?>><?php esc_html_e('Default', 'foldery' ); ?></option>
            <option value="rounded"<?php if( $style == 'rounded' ){ echo 'selected="selected"';} ?>><?php esc_html_e('Rounded', 'foldery' ); ?></option>
            <option value="circle"<?php if( $style == 'circle' ){ echo 'selected="selected"';} ?>><?php esc_html_e('Circle', 'foldery' ); ?></option>
         </select>
         </p>

         <p><label for="<?php echo esc_url($this->get_field_id('align')); ?>"><?php esc_html_e( 'Content Align:', 'foldery' ); ?></label>
         <select class="widefat" id="<?php echo esc_attr( $this->get_field_id('align') ); ?>" name="<?php echo esc_attr( $this->get_field_name('align') ); ?>">
            <option value="text-left"<?php if( $align == 'text-left' ){ echo 'selected="selected"';} ?>><?php esc_html_e('Left', 'foldery' ); ?></option>
            <option value="text-center"<?php if( $align == 'text-center' ){ echo 'selected="selected"';} ?>><?php esc_html_e('Center', 'foldery' ); ?></option>
            <option value="text-right"<?php if( $align == 'text-right' ){ echo 'selected="selected"';} ?>><?php esc_html_e('Right', 'foldery' ); ?></option>
         </select>
         </p>
         <p><label for="<?php echo esc_url($this->get_field_id('icon_1')); ?>"><?php esc_html_e( 'Icon 1:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_1') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_1') ); ?>" type="text" value="<?php echo esc_attr( $icon_1 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_1')); ?>"><?php esc_html_e( 'Link 1:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_1') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_1') ); ?>" type="text" value="<?php echo esc_attr( $link_1 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_2')); ?>"><?php esc_html_e( 'Icon 2:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_2') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_2') ); ?>" type="text" value="<?php echo esc_attr( $icon_2 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_2')); ?>"><?php esc_html_e( 'Link 2:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_2') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_2') ); ?>" type="text" value="<?php echo esc_attr( $link_2 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_3')); ?>"><?php esc_html_e( 'Icon 3:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_3') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_3') ); ?>" type="text" value="<?php echo esc_attr( $icon_3 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_3')); ?>"><?php esc_html_e( 'Link 3:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_3') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_3') ); ?>" type="text" value="<?php echo esc_attr( $link_3 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_4')); ?>"><?php esc_html_e( 'Icon 4:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_4') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_4') ); ?>" type="text" value="<?php echo esc_attr( $icon_4 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_4')); ?>"><?php esc_html_e( 'Link 4:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_4') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_4') ); ?>" type="text" value="<?php echo esc_attr( $link_4 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_5')); ?>"><?php esc_html_e( 'Icon 5:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_5') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_5') ); ?>" type="text" value="<?php echo esc_attr( $icon_5 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_5')); ?>"><?php esc_html_e( 'Link 5:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_5') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_5') ); ?>" type="text" value="<?php echo esc_attr( $link_5 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_6')); ?>"><?php esc_html_e( 'Icon 6:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_6') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_6') ); ?>" type="text" value="<?php echo esc_attr( $icon_6 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_6')); ?>"><?php esc_html_e( 'Link 6:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_6') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_6') ); ?>" type="text" value="<?php echo esc_attr( $link_6 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_7')); ?>"><?php esc_html_e( 'Icon 7:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_7') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_7') ); ?>" type="text" value="<?php echo esc_attr( $icon_7 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_7')); ?>"><?php esc_html_e( 'Link 7:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_7') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_7') ); ?>" type="text" value="<?php echo esc_attr( $link_7 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_8')); ?>"><?php esc_html_e( 'Icon 8:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_8') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_8') ); ?>" type="text" value="<?php echo esc_attr( $icon_8 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_8')); ?>"><?php esc_html_e( 'Link 8:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_8') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_8') ); ?>" type="text" value="<?php echo esc_attr( $link_8 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_9')); ?>"><?php esc_html_e( 'Icon 9:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_9') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_9') ); ?>" type="text" value="<?php echo esc_attr( $icon_9 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_9')); ?>"><?php esc_html_e( 'Link 9:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_9') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_9') ); ?>" type="text" value="<?php echo esc_attr( $link_9 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_10')); ?>"><?php esc_html_e( 'Icon 10:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_10') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_10') ); ?>" type="text" value="<?php echo esc_attr( $icon_10 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_10')); ?>"><?php esc_html_e( 'Link 10:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_10') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_10') ); ?>" type="text" value="<?php echo esc_attr( $link_10 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_11')); ?>"><?php esc_html_e( 'Icon 11:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_11') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_11') ); ?>" type="text" value="<?php echo esc_attr( $icon_11 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_11')); ?>"><?php esc_html_e( 'Link 11:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_11') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_11') ); ?>" type="text" value="<?php echo esc_attr( $link_11 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_12')); ?>"><?php esc_html_e( 'Icon 12:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_12') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_12') ); ?>" type="text" value="<?php echo esc_attr( $icon_12 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_12')); ?>"><?php esc_html_e( 'Link 12:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_12') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_12') ); ?>" type="text" value="<?php echo esc_attr( $link_12 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('icon_13')); ?>"><?php esc_html_e( 'Icon 13:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('icon_13') ); ?>" name="<?php echo esc_attr( $this->get_field_name('icon_13') ); ?>" type="text" value="<?php echo esc_attr( $icon_13 ); ?>" /></p>
         <p><label for="<?php echo esc_attr($this->get_field_id('link_13')); ?>"><?php esc_html_e( 'Link 13:', 'foldery' ); ?></label>
         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('link_13') ); ?>" name="<?php echo esc_attr( $this->get_field_name('link_13') ); ?>" type="text" value="<?php echo esc_attr( $link_13 ); ?>" /></p>

         <p><label for="<?php echo esc_attr($this->get_field_id('extra_class')); ?>">Extra Class:</label>
         <input class="widefat" id="<?php echo esc_attr($this->get_field_id('extra_class')); ?>" name="<?php echo esc_attr($this->get_field_name('extra_class')); ?>" value="<?php echo esc_attr($extra_class); ?>" /></p>

    <?php
    }
}