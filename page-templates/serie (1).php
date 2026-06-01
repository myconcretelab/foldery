<?php
/**
 * Template Name: Gallerie de série (stack)
 * @package Foldery
 * @subpackage ZK Theme
 * @since 1.0.0
 * @author Seb
 */
?>
<?php get_header();

// Le champs folder peut renvoyer un objet IFolder ou l'ID du dossier.
$folder = foldery_media_resolve_folder(get_field('folder'));
$col = 3;
$thumbSize = 'medium';
if (foldery_is_media_folder($folder) ): 
    $series = $folder->getChildren();
    if (count($series)) :
            // l'identifiant des colonnes pour Bootstrap
        $bootstrapColWidth = 12 / $col ;
            // Couper le tableau en deux niveau de profondeur, suivant le nombre de colonnes demandées
        $arrayChunks = array_chunk($series, $col);
        $permalink =  'serie/'; //get_permalink(1378); // Lien vers la page "serie-detail"
?>

<div class="row">
    <div class="vc_col-sm-12">
        <?php the_content()?>
    </div>
</div>
<div class="vc_row wpb_row vc_row-fluid">
    <div class="stack-wrapper stack-wrapper">
    <?php
            // Un peu crasse  régler +/_ la alrgur de l'image pour qu'elle soit homogène avec la largeur de colonne.
        echo '<style>.stack img {max-width: ' . (1200 / ($col+1))-50 . 'px}</style>';
        foreach ($arrayChunks as $rows) : 
            echo '<div class="row">';
            foreach($rows as $child) :
            	   $imagesIDs =  $child->read();
            	   if(count($imagesIDs) === 0){
                        $children = $child->getChildren();
                        if(count($children)) {
                            foreach ($children as $c) {
                                if($c->getCnt()) {
                                    $imagesIDs = $c->read();
                                    break;
                                }
                            } 
                        }
            	   }
                    ?>
<?php 
/*
echo '<pre>';
   print_r($imagesIDs);
echo '</pre>';
*/
?>                    
                    <div class='vc_col-sm-<?php echo $bootstrapColWidth ?>'>
                            <a href="<?php echo $permalink . sanitize_title($child->getName()) . '/' . $child->getId(); ?>" class="stack-link">
                                <h5><?php echo $child->getName()?></h5>
                                <figure class="stack stack-sidegrid stack-randomrot active">
                                    <?php 
                                    // les trois images sont les première du folder
                                    if (is_array($imagesIDs)) :
                                        for ($i=0;$i<3;$i++) :
                                            if (array_key_exists($i,$imagesIDs))
                                                echo wp_get_attachment_image($imagesIDs[$i],$thumbSize);
                                        endfor;
                                    endif;                    
                                    ?>            	
                                </figure>
                            </a>
                    </div>
                <?php // endwhile ?>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>                
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php get_footer(); ?>
