<?php

if(!function_exists('cms_widget_register')) return;

add_action('widgets_init', 'register_instagram_widget');
function register_instagram_widget() {
    cms_widget_register('CMS_instagram_Widget');
}


class CMS_Instagram_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'cms_instagram_widget', // Base ID
            __('CMS Instagram', 'foldery' ), // Name
            array('description' => esc_html__('CMS Instagram Widget', 'foldery' ),) // Args
        );
        add_action('wp_enqueue_scripts', array($this, 'widget_scripts'));
    }
    function widget_scripts() {
        wp_enqueue_style('widget_cms_instagram_scripts', get_template_directory_uri() . '/inc/widgets/cms_instagram.css');
    }
    
    function widget($args, $instance) {      
        extract($args);
		$title       = apply_filters('widget_title', $instance['title']);
		$username    = $instance['username'];
		$id          = $instance['id'];
		$api         = $instance['api'];
		$limit       = $instance['number'];
		$columns     = $instance['columns'];
		$size        = $instance['size'];
		$target      = $instance['target'];
		$link        = $instance['link'];
		$extra_class = $instance['extra_class'];
		switch ($columns) {
			case 1:
	            $span = "col-xs-12 col-sm-12 col-md-12 col-lg-12 nopaddingall";
	            break;
	        case 2:
	            $span = "col-xs-6 col-sm-6 col-md-6 col-lg-6 nopaddingall";
	            break;
			case 3:
	            $span = "col-xs-4 col-sm-4 col-md-4 col-lg-4 nopaddingall";
	            break;
	        case 4:
	            $span = "col-xs-3 col-sm-3 col-md-3 col-lg-3 nopaddingall";
	            break;
	        default:
	            $span = "col-xs-4 col-sm-4 col-md-4 col-lg-4 nopaddingall";
	    }
        cms_allowed_html($before_widget);

        if (!empty($title))
            cms_allowed_html($before_title . $title . $after_title);
        if ($link != '') {
			?><div class="user"><a href="//instagram.com/<?php echo trim($username); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>"><?php echo esc_attr($link); ?> @<?php echo trim($username); ?></a></div><?php
		}
        if ($id != '') {

			$media_array = $this->scrape_instagram($id, $api, $limit);

			if ( is_wp_error($media_array) ) {

			   cms_allowed_html($media_array->get_error_message());

			} else {

				// filter for images only?
				if ( $images_only = apply_filters( 'cs_images_only', FALSE ) )
					$media_array = array_filter( $media_array, array( $this, 'images_only' ) );

				?><div class="cs-instagram-pics clearfix <?php echo esc_attr($extra_class);?>"><?php
				foreach ($media_array as $item) {
					echo '<div class="instagram-item '.$span.'"><a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'"><img src="'. esc_url($item[$size]['url']) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'" style="width:100%; max-width:100%;"/></a></div>';
				}
				?></div><?php
			}
		}
        cms_allowed_html($after_widget);
    }         
    
    function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['title']       = strip_tags($new_instance['title']);
		$instance['username']    = trim(strip_tags($new_instance['username']));
		$instance['id']          = trim(strip_tags($new_instance['id']));
		$instance['api']         = trim(strip_tags($new_instance['api']));
		$instance['number']      = $new_instance['number'];
		$instance['columns']     = $new_instance['columns'];
		$instance['size']        = $new_instance['size'];
		$instance['target']      = $new_instance['target'];
		$instance['link']        = strip_tags($new_instance['link']);
		$instance['extra_class'] = $new_instance['extra_class'];
         
         return $instance;
    }
    
    function form( $instance ) {
		$instance    = wp_parse_args( (array) $instance, array( 
				'title'       => esc_html__('Instagram', 'foldery'), 
				'username'    => 'zooka.studio', 
				'id'          => '7649855718', 
				'api'         => '7649855718.1677ed0.8af377c900424e75a331caef49a74baf', 
				'link'        => esc_html__('Follow Us', 'foldery'), 
				'number'      => 9,
				'columns'     => 3, 
				'size'        => 'thumbnail',
				'target'      => '_self',
				'extra_class' => ''
			) 
		);
		$title       = $instance['title'];
		$username    = $instance['username'];
		$id          = $instance['id'];
		$api         = $instance['api'];
		$number      = absint($instance['number']);
		$columns     = absint($instance['columns']);
		$size        = $instance['size'];
		$target      = $instance['target'];
		$link        = $instance['link'];
		$extra_class = $instance['extra_class'];
        ?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'foldery'); ?>: <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php esc_html_e('User ID', 'foldery'); ?>: <a target="_blank" href="www.instagram.com/zooka.studio">www.instagram.com/zooka.studio</a> Get "zooka.studio". <input class="widefat" id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php echo esc_attr($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($username); ?>" placeholder="zooka.studio" /></label></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('api')); ?>"><?php esc_html_e('Access Token', 'foldery'); ?>: <a target="_blank" href="http://instagram.pixelunion.net/">Generate Instagram Access Token</a> <input class="widefat" id="<?php echo esc_attr($this->get_field_id('api')); ?>" name="<?php echo esc_attr($this->get_field_name('api')); ?>" type="text" value="<?php echo esc_attr($api); ?>" placeholder="7649855718.1677ed0.8af377c900424e75a331caef49a74baf" /></label></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('id')); ?>"><?php esc_html_e('Client ID', 'foldery'); ?>: Get numbers before dot from Access Token. <input class="widefat" id="<?php echo esc_attr($this->get_field_id('id')); ?>" name="<?php echo esc_attr($this->get_field_name('id')); ?>" type="text" value="<?php echo esc_attr($id); ?>" placeholder="7649855718" /></label></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of photos', 'foldery'); ?>: <input class="widefat" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text" value="<?php echo esc_attr($number); ?>" /></label></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e('Columns', 'foldery'); ?>: <input class="widefat" id="<?php echo esc_attr($this->get_field_id('columns')); ?>" name="<?php echo esc_attr($this->get_field_name('columns')); ?>" type="text" value="<?php echo esc_attr($columns); ?>" /></label></p>

		<p><label for="<?php echo esc_attr($this->get_field_id('size')); ?>"><?php esc_html_e('Photo size', 'foldery'); ?>:</label>
			<select id="<?php echo esc_attr($this->get_field_id('size')); ?>" name="<?php echo esc_attr($this->get_field_name('size')); ?>" class="widefat">
				<option value="thumbnail" <?php selected('thumbnail', $size) ?>><?php esc_html_e('Thumbnail', 'foldery'); ?></option>
				<option value="large" <?php selected('large', $size) ?>><?php esc_html_e('Large', 'foldery'); ?></option>
			</select>
		</p>
		<p><label for="<?php echo esc_attr($this->get_field_id('target')); ?>"><?php esc_html_e('Open links in', 'foldery'); ?>:</label>
			<select id="<?php echo esc_attr($this->get_field_id('target')); ?>" name="<?php echo esc_attr($this->get_field_name('target')); ?>" class="widefat">
				<option value="_self" <?php selected('_self', $target) ?>><?php esc_html_e('Current window (_self)', 'foldery'); ?></option>
				<option value="_blank" <?php selected('_blank', $target) ?>><?php esc_html_e('New window (_blank)', 'foldery'); ?></option>
			</select>
		</p>
		<p><label for="<?php echo esc_attr($this->get_field_id('link')); ?>"><?php esc_html_e('Link text', 'foldery'); ?>: <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link')); ?>" name="<?php echo esc_attr($this->get_field_name('link')); ?>" type="text" value="<?php echo esc_attr($link); ?>" /></label></p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('extra_class')); ?>">Extra Class:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('extra_class')); ?>" name="<?php echo esc_attr($this->get_field_name('extra_class')); ?>" value="<?php echo esc_attr($extra_class); ?>" />
		</p>
         <?php
         
    } 
    function scrape_instagram($id, $api, $slice = 9) {
		if (false === ($instagram = get_transient('instagram-media-'.sanitize_title_with_dashes($id)))) {

			$remote = wp_remote_get("https://api.instagram.com/v1/users/".$id."/media/recent/?access_token=".$api."&count=".$slice, true);

			if (is_wp_error($remote))
	  			return new WP_Error('site_down', __('Unable to communicate with Instagram.', 'foldery'));

  			if ( 200 != wp_remote_retrieve_response_code( $remote ) )
  				return new WP_Error('invalid_response', __('Instagram did not return a 200.', 'foldery'));

			$insta_array = json_decode($remote['body'], TRUE);

			if (!$insta_array)
	  			return new WP_Error('bad_json', __('Instagram has returned invalid data.', 'foldery'));

			$images = $insta_array['data'];

			$instagram = array();

			foreach ($images as $image) {
					$image['link']                          = preg_replace( "/^http:/i", "", $image['link'] );
					$image['images']['thumbnail']           = preg_replace( "/^http:/i", "", $image['images']['thumbnail'] );
					$image['images']['standard_resolution'] = preg_replace( "/^http:/i", "", $image['images']['standard_resolution'] );

					$instagram[] = array(
						'description'   => $image['caption']['text'],
						'link'          => $image['link'],
						'time'          => $image['created_time'],
						'comments'      => $image['comments']['count'],
						'likes'         => $image['likes']['count'],
						'thumbnail'     => $image['images']['thumbnail'],
						'large'         => $image['images']['standard_resolution'],
						'type'          => $image['type']
					);
			}
			$instagram = base64_ef3_encode( serialize( $instagram ) );

			set_transient('instagram-media-'.sanitize_title_with_dashes($id), $instagram, apply_filters('cs_instagram_cache_time', HOUR_IN_SECONDS*2));
		}

		$instagram = unserialize( base64_ef3_decode( $instagram ) );

		return array_slice($instagram, 0, $slice);
	}
	function images_only($media_item) {

		if ($media_item['type'] == 'image')
			return true;

		return false;
	}
	function getInstaID($username, $client_id)
	{

	    $username = strtolower($username); // sanitization
	    $url = "https://api.instagram.com/v1/users/search?q=".$username."&client_id=".$client_id;
	    $get = wp_remote_get($url);
	    if (is_wp_error($get))
			return new WP_Error('site_down', __('Unable to communicate with Instagram.', 'foldery'));

		if ( 200 != wp_remote_retrieve_response_code( $get ) )
			return new WP_Error('invalid_response', __('Instagram did not return a 200.', 'foldery'));
	    $json = json_decode($get['body']);

	    foreach($json->data as $user)
	    {
	        if($user->username == $username)
	        {
	            return $user->id;
	        }
	    }

	    return '00000000'; // return this if nothing is found
	}
}