<?php
/**
 * Template Name: Listing Reproductions
 * @package Foldery
 * @subpackage ZK Theme
 * @since 1.0.0
 * @author Chinh Duong Manh
 */
?>

<?php //global $cms_meta; 
get_header();

/* aller chercher tous les fichiers donc la propriété
* "reproduction disponible est "true"
* Créer une gallerie d'images et d'infos
*/

$attachments = get_posts( array(
	'post_type' => 'attachment',
	'numberposts' => -1,
	'meta_query' => array(
		array(
			'key'   => 'reproductions_disponible',
			'value' => '1',
		)
	)
) );
echo 'to'
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
						// diviser la largeur de l'image pour qu'elle rentre dans le design
						$ratio = .5;

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
						<?php if(get_field('cadre_en_option')) :?>
						<div class="entry-date">
								<strong><?php echo ('Cadre (en option)') ?> : </strong><br>
						<?php
						// Il y a plusieurds choix de cadre possible
						if (count($cadres) > 1):
							foreach($cadres as $option ) :?>
								<input type="radio" name="encadrement" value="<?php echo $option['label']?>" onchange= "toggleClass(<?php echo $option['value']?>,<?php echo $post->ID?>,<?php echo get_field('prix',$post,false); ?>);" <?php echo ($option['value'] == $cadreSelect) ? 'checked' : '' ?> />
								<label for="encadrement"><?php echo $option['label']?></label><br>
							<?php endforeach;
						// Il n'y a qu'un choix de cadre
						elseif (count($cadres) == 1) :
							?>
								<input type="checkbox" name="encadrement" value="<?php echo $cadres[0]['value']?>" <?php echo ($option['value'] == $cadreSelect) ? 'checked' : '' ?> onchange= "toggleClass(<?php echo $cadres[0]['value']?>,<?php echo $post->ID?>,<?php echo get_field('prix',$post,false); ?>);" />
								<label for="encadrement"><?php echo $option['label']?></label>
							
						<?php endif ?>
						</div>
						<?php endif ?>
						<?php if(get_field('prix')) : ?>
						<br>
						<h3>
								<strong><?php echo ('Prix') ?> : </strong>
								<em id="prix_<?php the_ID() ?>" class="prix"><?php echo get_field('prix',$post,false) + $cadreSelect; ?></em> €
						</h3>
						<?php endif ?>
						<br>
						    <input class="form-control bccolor" placeholder="Votre Email" required="" value="" size="40" aria-required="true" aria-invalid="false" type="email">
                            <input class="submit" value="Je suis intéressé(e)" name="submit" type="submit">
                            <input type="hidden" name="oeuvre" value="<?php the_title(); ?>" />
                            <input type="hidden" name="lien" value="<?php echo $img[0]; ?>" />
						</form>

					</td>
				</tr>
			<?php endforeach ?>
		</table>


		<?php endif ?>


		
	</div><!-- .entry-content -->
</article><!-- #post -->
	<footer class="entry-meta">
<script>
jQuery( document ).ready(function() {
     jQuery('.form').submit(function(e){
    	
    	e.preventDefault();
        var data = { 
                action: 'send_email', 
                encadrement: (jQuery(this).find("input[name='encadrement']:checked").val()),
                email: jQuery(this).find("input[type='email']").val(),
                prix: jQuery(this).find('.prix').html(),
                oeuvre: jQuery(this).find("input[name='oeuvre']").val(),
                lien: jQuery(this).find("input[name='lien']").val()
            };

        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: "POST",
            cache: false,
            data: data,
            success:function(res){
               alert("Merci pour votre intéret, je vous recontacte rapidement." + res);
               }
        }); 
    });  
});
</script>
	</footer><!-- .entry-meta -->

<script>
	function toggleClass (val,id,base) {
		var frame = document.getElementById("frame_" + id);
		switch(val) {
			case 0 :
				frame.classList.remove("borderless","frame");
			break;
			case 15 :
				frame.classList.toggle("frame");
			break;
			case 25 :
				frame.classList.add("borderless","frame");				
			break;
			case 35 :
				frame.classList.remove("borderless");
				frame.classList.add('frame');
			break;
			default :
			break;
		}
		togglePrice (val,id,base);
		
	}
	function togglePrice (val,id,base) {
		document.getElementById("prix_" + id).innerHTML = base + val;

	}
</script>
<?php get_footer(); ?>