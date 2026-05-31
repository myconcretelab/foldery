<?php
$attachments = get_posts( array(
	'post_type' => 'attachment',
	'numberposts' => 3,
	'meta_query' => array(
		array(
			'key'   => 'mise_en_avant',
			'value' => '1',
		)
	)
));
if ($attachments) : ?>
<div class="">	
<table class="expo">
		<tr>	
	<?php foreach ($attachments as $post) : setup_postdata($post);?>
			<td>
				<?php
				// Taille de la miniature
				$thumbSize = "medium";
				// Cette fonction permet de recupérer les dimensions de l'image
				$img = wp_get_attachment_image_src($post->ID,$thumbSize);
				$imgtag = wp_get_attachment_link($post,$thumbSize);
				// diviser la largeur de l'image pour qu'elle rentre dans le design
				// Se baser sur les dimensions de l'oeuvre pour qu'elle soit proportionelle
				$d = get_field('dimension',$post,false);
				
				($d) ? $ratio = ($d/100) : $ratio = .5;

				if (is_array($img)) :
				?>
				<figure class="frame" id="frame_<?php echo $post->ID ?>" style="width:<?php echo $img[1] * $ratio ?>px;">
				<?php
					// Display full-size image in lightbox when clicked.
					if ( function_exists( 'slb_activate' ) ) echo slb_activate($imgtag );
					
				?>
				</figure>
				<?php if($di = get_field('dimension',$post,true)) :?><h5><?php print_r( $di)?></h5><?php endif ?>
				<style>
					#frame_<?php echo $post->ID ?>:before { 
							width:<?php echo $img[1] * $ratio + 40 ?>px; 
							height:<?php echo $img[2] * $ratio + 40 ?>px;
						}
						#frame_<?php echo $post->ID ?>:after {
							width:<?php echo $img[1] * $ratio + 60 ?>px; 
							height:<?php echo $img[2] * $ratio + 60 ?>px;									
						}
					#frame_<?php echo $post->ID ?>.borderless:before { 
							width:<?php echo $img[1] * $ratio  ?>px; 
							height:<?php echo $img[2] * $ratio  ?>px;
						}
						#frame_<?php echo $post->ID ?>.borderless:after {
							width:<?php echo $img[1] * $ratio + 20 ?>px; 
							height:<?php echo $img[2] * $ratio + 20 ?>px;									
						}
				</style>

            </td>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
        </tr>
</table></div>