<table class="expo">
<?php global $post; foreach ($arrayChunks as $gposts) : ?>
	<tr>	
	<?php foreach ($gposts as $post) : setup_postdata($post);?>
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
				
				($d && $proportions) ? $ratio = ($d/100) : $ratio = .5;

				$cadres = get_field('cadre_en_option',$post);
				$cadreSelect = get_field('cadre_presentation',$post);

				$classes = (in_array($cadreSelect,["15","35","25"])) ? "frame" : "";
				$classes .= ($cadreSelect == "25") ? " borderless" : "";

			if (is_array($img)) :?>
				<figure class="<?php echo $classes ?>" id="frame_<?php echo $post->ID ?>" style="width:<?php echo $img[1] * $ratio ?>px;">
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
            <?php endif; ?>
            </td>
            <?php if($details) :?>
				<td>
				    <form id="form_<?php the_ID(); ?>"class="form" action="" method="post" enctype="multipart/form-data">
					<h4>
						<?php the_title();?>
					</h4>
					<p>
						<?php the_excerpt();?>
					</p>
					<?php if(get_field('dimension')) : ?>
					<div class="entry-date">
							<strong><?php echo ('Dimensions') ?> : </strong>
							<em><?php the_field('dimension'); ?></em>
					</div>
					<?php endif ?>
					<?php if(get_field('papier')) : ?>
					<div class="entry-date">
							<strong><?php echo ('Papier') ?> : </strong>
							<em><?php the_field('papier'); ?></em>
					</div>
					<?php endif ?>
				</td> 
			<?php endif ?>
    <?php endforeach; ?>
        </tr>
<?php endforeach; ?>
</table>
