<div class="vc_row wpb_row vc_row-fluid">
    <div class="stack-wrapper stack-wrapper">
    <?php
        global $post; // Le array_chunk nous oblige à utiliser cette syntaxe
            // l'identifiant des colonnes pour Bootstrap
        $bootstrapColWidth = 12 / $col ;
            // Couper le tableau en deux niveau de profondeur, suivant le nombre de colonnes demandées
        $arrayChunks = array_chunk($the_query->posts, $col);
            // Un peu crasse  régler +/_ la alrgur de l'image pour qu'elle soit homogène avec la largeur de colonne.
        echo '<style>.stack img {max-width: ' . (1200 / ($col+1))-50 . 'px}</style>';
        foreach ($arrayChunks as $posts) : 
            echo '<div class="row">';
            foreach($posts as $post) :
                setup_postdata($post);
            	// Get the ID attribute from the post
            	// Et aller chercher toutes les images contenue dans le Folder
                if($id_gallery = foldery_rml_folder_id(get_field('id_gallerie',$post))) :
                   $imagesIDs =  wp_rml_get_attachments($id_gallery);
                   if(count($imagesIDs) === 0){
                        $folder = wp_rml_get_object_by_id($id_gallery);
                        $children = is_rml_folder($folder) ? $folder->getChildren() : array();
                        if(count($children)) {
//                          while(count($imagesIDs) < 1 || $i < count($children)) {
                            foreach ($children as $child) {
                                if($child->getCnt()) {
                                    $imagesIDs = wp_rml_get_attachments($child->getId());
                                    break;
                                }
                            } 
                            
                        }
            	   }
            	    array_reverse($imagesIDs);
            	 endif;
                    ?>
                    <div class='vc_col-sm-<?php echo $bootstrapColWidth ?>'>
                            <a href="<?php the_permalink(); ?>" class="stack-link">
                                <h5><?php the_title()?></h5>
                                <figure class="stack stack-sidegrid stack-randomrot active">
                                    <?php 
                                    // les trois images sont les première du folder
                                    if (is_array($imagesIDs)) :
                                        for ($i=0;$i<3;$i++) :
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
