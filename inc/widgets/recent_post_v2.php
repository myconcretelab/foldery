<?php

if(!function_exists('foldery_register_widget')) return;

add_action('widgets_init', 'cs_recent_post_widgets');
function cs_recent_post_widgets() {
    foldery_register_widget('CS_Recent_Post_Widget_V2');
}

class CS_Recent_Post_Widget_V2 extends WP_Widget {

    function CS_Recent_Post_Widget_V2() {
        parent::__construct(
                'cs_recent_post_v2', __('CS Recent Posts', 'foldery' ), array('description' => esc_html__('Recent Posts Widget.', 'foldery' ),)
        );
    }

    function widget($args, $instance) {
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'foldery' ) : $instance['title'], $instance, $this->id_base);
        $show_date = (int) $instance['show_date'];
        $show_decs = (int) $instance['show_decs'];
        $number = (int) $instance['number'];

        foldery_allowed_html($before_widget);

        if($title) {
            foldery_allowed_html($before_title.$title.$after_title);
        }

        $sticky = get_option('sticky_posts');
        $args = array(
            'posts_per_page' => $number,
            'post_type' => 'post',
            'post_status' => 'publish',
            'post__not_in'  => $sticky,
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => 1
        );

        $wp_query = new WP_Query($args);
        $extra_class = !empty($instance['extra_class']) ? $instance['extra_class'] : "";

        // no 'class' attribute - add one with the value of width
        if( strpos($before_widget, 'class') === false ) {
            $before_widget = str_replace('>', 'class="'. $extra_class . '"', $before_widget);
        }
        // there is 'class' attribute - append width value to it
        else {
            $before_widget = str_replace('class="', 'class="'. $extra_class . ' ', $before_widget);
        }
        ?>
        <?php if ($wp_query->have_posts()){ ?>
                <div class="cms-recent-post">
                    <ul class="cms-recent-post-wrapper">
                        <?php while ($wp_query->have_posts()): $wp_query->the_post(); ?>
                            <li>
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="cms-recent-media">  
                                   <div class="image">
                                       <a class="post-featured-img" href="<?php the_permalink(); ?>">
                                          <?php the_post_thumbnail(array(50, 50)); ?>
                                       </a>
                                    </div>     
                                 </div>
                                <?php endif; ?> 
                                <div class="cms-recent-details <?php if(has_post_thumbnail() == '') {echo 'no-image';} ?>">
                                    <h3 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <?php if ($show_date) { ?>
                                    <div class="date">  
                                    <span><?php echo get_the_date('M d Y'); ?></span>
                                    <span class="author">by <?php echo get_the_author(); ?></span>
                                    </div>
                                    <?php }?>   
                                    <?php if ($show_decs) { ?>
                                        <div class="description"><?php echo cms_limit_words( strip_tags( get_the_excerpt() ),10)."..."; ?></div>
                                    <?php  } ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php } else { ?>
                <span class="notfound">No post found!</span>
            <?php
            }
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['show_date'] = $new_instance['show_date'];
        $instance['show_decs'] = $new_instance['show_decs'];
        $instance['number'] = (int) $new_instance['number'];
        $instance['extra_class'] = $new_instance['extra_class'];

        return $instance;
    }

    function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $show_date = isset($instance['show_date']) ? esc_attr($instance['show_date']) : '';
        $show_decs = isset($instance['show_decs']) ? esc_attr($instance['show_decs']) : '';
        if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
                     $number = 5;
        ?>
        <p>
            <label for="<?php echo ''.$this->get_field_id('title'); ?>"><?php esc_html_e( 'Title:', 'foldery' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo ''.$this->get_field_id('show_date'); ?>"><?php esc_html_e( 'Show date:', 'foldery' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('show_date') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_date') ); ?>" <?php if($show_date!='') echo 'checked="checked";' ?> type="checkbox" value="1"  />
        </p>
        <p>
            <label for="<?php echo ''.$this->get_field_id('show_decs'); ?>"><?php esc_html_e( 'Show Description:', 'foldery' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('show_decs') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_decs') ); ?>" <?php if($show_decs!='') echo 'checked="checked";' ?> type="checkbox" value="1" />
        </p>
        <p>
            <label for="<?php echo ''.$this->get_field_id('number'); ?>"><?php esc_html_e( 'Number of products to show:', 'foldery' ); ?></label>
            <input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
        </p>
        <p>
            <label for="<?php echo ''.$this->get_field_id('extra_class'); ?>">Extra Class:</label>
            <input class="widefat" id="<?php echo ''.$this->get_field_id('extra_class'); ?>" name="<?php echo esc_attr($this->get_field_name('extra_class')); ?>" value="<?php if(isset($instance['extra_class'])){echo esc_attr($instance['extra_class']);} ?>" />
        </p>
        <?php
    }
}
?>