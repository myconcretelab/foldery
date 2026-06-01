<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
          <div class="row">
            <div class="col-md-3">
		        <h2><?php the_title(); ?></h2>
            </div>
            <div class="col-md-9">
                <?php echo get_field('description');?>
            </div>
          </div>	    
	</div>
</article>
<div class="vc-zigzag-inner" style="width: 100%;min-height: 14px;background: 0 repeat-x url('data:image/svg+xml;utf-8,%3C%3Fxml%20version%3D%221.0%22%20encoding%3D%22utf-8%22%3F%3E%3C%21DOCTYPE%20svg%20PUBLIC%20%22-%2F%2FW3C%2F%2FDTD%20SVG%201.1%2F%2FEN%22%20%22http%3A%2F%2Fwww.w3.org%2FGraphics%2FSVG%2F1.1%2FDTD%2Fsvg11.dtd%22%3E%3Csvg%20width%3D%2214px%22%20height%3D%2212px%22%20viewBox%3D%220%200%2018%2015%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cpolygon%20id%3D%22Combined-Shape%22%20fill%3D%22%23ebebeb%22%20points%3D%228.98762301%200%200%209.12771969%200%2014.519983%209%205.40479869%2018%2014.519983%2018%209.12771969%22%3E%3C%2Fpolygon%3E%3C%2Fsvg%3E');"></div>

<?php
$id_gallery = foldery_media_folder_id(get_field('id_gallerie'));
$first = 0;
if ($id_gallery) :
    switch (get_field('presentation_type')) :
        case 'gallery' :
            $folder = foldery_media_get_folder($id_gallery);
            // On affiche la galerie avec les images
            if ( function_exists('foldery_lightbox_activate') && $folder->getCnt()) :
                echo foldery_lightbox_activate(do_shortcode('[folder-gallery folder_id="' . $id_gallery . '" size="medium" orderby="folder_order"]'));
                $first = 1;
            endif;            
            // On va tester si le dossier à des enfants
            if (foldery_is_media_folder($folder)) {
                $children = $folder->getChildren();
                if(is_array($children)) {
                    // Si oui, on va créer une gallerie pour chaque enfant
                    foreach ($children as $child) {
                        if($child->getCnt()) {
                            echo $first == 0 ?  "" :  "<div class=\"vc-zigzag-inner\" style=\"width: 100%;min-height: 14px;background: 0 repeat-x url('data:image/svg+xml;utf-8,%3C%3Fxml%20version%3D%221.0%22%20encoding%3D%22utf-8%22%3F%3E%3C%21DOCTYPE%20svg%20PUBLIC%20%22-%2F%2FW3C%2F%2FDTD%20SVG%201.1%2F%2FEN%22%20%22http%3A%2F%2Fwww.w3.org%2FGraphics%2FSVG%2F1.1%2FDTD%2Fsvg11.dtd%22%3E%3Csvg%20width%3D%2214px%22%20height%3D%2212px%22%20viewBox%3D%220%200%2018%2015%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cpolygon%20id%3D%22Combined-Shape%22%20fill%3D%22%23ebebeb%22%20points%3D%228.98762301%200%200%209.12771969%200%2014.519983%209%205.40479869%2018%2014.519983%2018%209.12771969%22%3E%3C%2Fpolygon%3E%3C%2Fsvg%3E');\"></div>";;
                            echo '<h2 class="padding">' . $child->getName() . '</h2>';
                            $meta = foldery_media_get_folder_meta($child->getId());
                            echo isset($meta['description']) ? '<p>' . str_replace("\n", "</p>\n<p>", $meta['description'][0]) . '</p><p>&nbsp;</p>' : '';
                            if ( function_exists('foldery_lightbox_activate') ) :
                                echo foldery_lightbox_activate(do_shortcode('[folder-gallery folder_id="' . $child->getId() . '" size="medium" orderby="folder_order"]'));
                            endif;
                            $first = 1;
                        }
                    }
                }
            }
            break;
        case 'custom' :
            the_content();
            break;
        default :    
            do_shortcode('[last_pics source="dir_id" id="'. $id_gallery . '" limit="-1" col="1" details="1" proportions="0"] ');
            break;
    endswitch;    
endif;

/**
 * The default template for displaying single portfolio
 *
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */
//$portfolio_meta = foldery_post_meta_data();

/* aller chercher tous les fichiers donc la propriété
* "reproduction disponible est "true"
* Créer une gallerie d'images et d'infos

$id_gallery = get_field('id_gallerie');
if ($id_gallery) :
    // appeller le shortcode [last_pics source="dir_id" id="$id_gallery" limit="-1" col="2" details="1" proportions="0"] 
    $imagesIDs =  foldery_media_get_attachments($id_gallery);
endif;
$attachments = get_posts( array(
	'post_type' => 'attachment',
	'numberposts' => -1,
	 'post__in' => $imagesIDs
	
) );        

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content<?php //echo !empty($cms_meta->_cms_one_page_full) ? ' full-page' : ''; ?>">
		<?php the_content(); ?>
		
		<?php if ($attachments) : ?>
			
		<table class="expo">
			<?php foreach ($attachments as $post) : setup_postdata($post);?>
				<tr>	
					<td>
						<?php
						// Taille de la miniature
						$thumbSize = "medium";
						// Cette fonction permet de recupérer les dimensions de l'image
						$img = wp_get_attachment_image_src($post->ID,$thumbSize);
						$imgtag = wp_get_attachment_link($post,$thumbSize);
        				// Se baser sur les dimensions de l'oeuvre pour qu'elle soit proportionelle
        				$d = get_field('dimension',$post,false);
        				($d) ? $ratio = ($d/100) : $ratio = .5;


						$cadres = get_field('cadre_en_option');
						$cadreSelect = get_field('cadre_presentation');

						$classes = (in_array($cadreSelect,["15","35","25"])) ? "frame" : "";
						$classes .= ($cadreSelect == "25") ? " borderless" : "";
						if (is_array($img)) :
						?>
						<figure class="<?php echo $classes?>" id="frame_<?php echo $post->ID ?>" style="width:<?php echo $img[1] * $ratio ?>px;">
						<?php
							// Display full-size image in lightbox when clicked.
							if ( function_exists('foldery_lightbox_activate') ) {
								echo foldery_lightbox_activate($imgtag );
							}
						?>
						</figure>						
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
						<?php endif ?>
					</td>					
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
				</tr>
			<?php endforeach ?>
		</table>


		<?php endif ?>


		
	</div><!-- .entry-content -->
</article><!-- #post -->
*/?>

<?php get_footer(); ?>
