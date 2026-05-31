<?php get_header();?><?php 

// Je tente de prendre une page qui porterait le nom de la série

$serie_page = new WP_Query( array(
    'post_type' => 'page',
    'name' => $wp_query->query['serie'] ));

if($serie_page->have_posts()) :
    
    // on va pouvoir retirer les attribut réglant l'affichage
    $col = get_field('col');
    $masonry = get_field('masonry');
    $display = 'gallery' ; 
    
    // J'utilise la technique officielle pour affciher le contenu.
    // Même si dans ce ca si il n'y a qu'une page
    while ( $serie_page->have_posts() ) : $serie_page->the_post();
        the_content();
    endwhile;
    
    echo '<br/>';

endif;

// reset post data (important mais ne semble pas fonctionner)
wp_reset_postdata();

// On recupère l'objet $folder via l'url ou via l'attribut de page

    $folder = get_field('folder');
    if (!is_rml_folder($folder)) {
        $id_gallery = get_query_var('serie_id');
        $folder = wp_rml_get_object_by_id($id_gallery);
    } else {
        // l'attribut nous envoie un objet, on recupère donc son ID
         $id_gallery = $folder->getId();
    }

    
// Si on a un folder à afficher, on commence son analyse    
    
    if (is_rml_folder($folder)) :
        
        // $meta contient la description du dossier et son image miniature (si définies)
        $meta = get_media_folder_meta($folder->getId());
    ?>
        <article id="post-<?php echo $id_gallery; ?>">
        	<div class="entry-content">
                  <div class="row">
                    <div class="col-md-3">
        		        <h2><?php echo $folder->getName(); ?></h2>
                    </div>
                    <div class="col-md-9">
                        
                        <?php echo isset($meta['description']) ? '<p>' . str_replace("\n", "</p>\n<p>", $meta['description'][0]) . '</p><p>&nbsp;</p>' : '';?>
                    </div>
                  </div>	    
        	</div>
        </article>
        <div class="vc-zigzag-inner" style="width: 100%;min-height: 14px;background: 0 repeat-x url('data:image/svg+xml;utf-8,%3C%3Fxml%20version%3D%221.0%22%20encoding%3D%22utf-8%22%3F%3E%3C%21DOCTYPE%20svg%20PUBLIC%20%22-%2F%2FW3C%2F%2FDTD%20SVG%201.1%2F%2FEN%22%20%22http%3A%2F%2Fwww.w3.org%2FGraphics%2FSVG%2F1.1%2FDTD%2Fsvg11.dtd%22%3E%3Csvg%20width%3D%2214px%22%20height%3D%2212px%22%20viewBox%3D%220%200%2018%2015%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cpolygon%20id%3D%22Combined-Shape%22%20fill%3D%22%23ebebeb%22%20points%3D%228.98762301%200%200%209.12771969%200%2014.519983%209%205.40479869%2018%2014.519983%2018%209.12771969%22%3E%3C%2Fpolygon%3E%3C%2Fsvg%3E');"></div>
        
        <?php

        $first = 0;    
        // On affiche la galerie avec les images
        if ( function_exists('slb_activate') && $folder->getCnt()) :
            echo slb_activate(do_shortcode('[masonry fid="' . $id_gallery . '"]'));
            $first = 1;
    endif;            

    // On va tester si le dossier à des enfants
    $children = $folder->getChildren();
    
    if(is_array($children)) :
        // Si oui, on va créer une gallerie pour chaque enfant
        ob_start(); 
        foreach ($children as $child) :
            if($child->getCnt()) :
                echo $first == 0 ?  "" :  "<div class=\"vc-zigzag-inner\" style=\"width: 100%;min-height: 14px;background: 0 repeat-x url('data:image/svg+xml;utf-8,%3C%3Fxml%20version%3D%221.0%22%20encoding%3D%22utf-8%22%3F%3E%3C%21DOCTYPE%20svg%20PUBLIC%20%22-%2F%2FW3C%2F%2FDTD%20SVG%201.1%2F%2FEN%22%20%22http%3A%2F%2Fwww.w3.org%2FGraphics%2FSVG%2F1.1%2FDTD%2Fsvg11.dtd%22%3E%3Csvg%20width%3D%2214px%22%20height%3D%2212px%22%20viewBox%3D%220%200%2018%2015%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cpolygon%20id%3D%22Combined-Shape%22%20fill%3D%22%23ebebeb%22%20points%3D%228.98762301%200%200%209.12771969%200%2014.519983%209%205.40479869%2018%2014.519983%2018%209.12771969%22%3E%3C%2Fpolygon%3E%3C%2Fsvg%3E');\"></div>";;
                echo '<h2 class="padding">' . $child->getName() . '</h2>';
                $meta = get_media_folder_meta($child->getId());
                echo isset($meta['description']) ? '<p>' . str_replace("\n", "</p>\n<p>", $meta['description'][0]) . '</p><p>&nbsp;</p>' : '';
                echo do_shortcode('[masonry fid="' . $child->getId() . '"]') . "\n";
                $first = 1;
            endif;
        endforeach;
        $out = ob_get_clean();
        // Activate links in output.
        if (function_exists('slb_activate')) $out = slb_activate($out); 
        // Display output.
        echo $out;
    endif;
    endif;
    get_footer();
    
