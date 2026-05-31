<?php

/**
 * Transformer : detail-serie/?serie_id=12
 * en detail-serie/nom-de-la-serie/12 
 * Pour que ça soit plus joli
 * ici La page de détail à un ID fixe (1378), si on supprime ou chzange la page, 
 * ça ne marche plus
 */ 

function gp_rewrite() {

  add_rewrite_tag('%serie_id%','([^/]+)');

    // Si quelqu'un peut m'aider ici a ne faire plus qu'une regle pour les deux cas de figure..

  // https://sebastienj.com/nom-du-theme(nom de la page)/serie/nom-de-la-serie/id -> affiche single.php 
  add_rewrite_rule('^([^/]+)/serie/([^/]+)/([^/]+)','index.php?serie=$matches[2]&post_type=serie&serie_id=$matches[3]', 'top');
  // https://sebastienj.com/serie/nom-de-la-serie/id -> affiche single.php 
  add_rewrite_rule('serie/([^/]+)/([^/]+)','index.php?serie=$matches[1]&post_type=serie&serie_id=$matches[2]', 'top');
}
add_action( 'init', 'gp_rewrite' );

/**
 * Declarer les variable de query qui vont être utilisées
 * en argument entre serie et serie-detail pour passer des informations
 * @serie_id : l'ID du dossier qui représente la série
 * @display : Pas utilisé mais censé afficher les imlages sous différentes formes
 */
function sj_register_query_vars( $vars ) {
	$vars[] = 'serie_id';
	return $vars;
}
add_filter( 'query_vars', 'sj_register_query_vars' );


function sj_register_post_types() {
	
    $labels = array(
        'name' => 'Série',
        'all_items' => 'Toutes les séries',  // affiché dans le sous menu
        'singular_name' => 'Série',
        'add_new' => 'Ajouter une série',
        'add_new_item' => 'Ajouter une série',
        'edit_item' => 'Modifier la série',
        'menu_name' => 'Série'
    );

	$args = array(
        'labels' => $labels,
        'public' => true,
        'show_in_rest' => false,
        'has_archive' => true,
        //'query_var' =>  'serie_id',
        'supports' => array( 'title', 'editor','thumbnail' ),
        'menu_position' => 5, 
        'menu_icon' => 'dashicons-format-gallery',
	);

	register_post_type( 'serie', $args );
}
add_action( 'init', 'sj_register_post_types' ); // Le hook init lance la fonction


/**
 * Si la single serie demandée n'existe pas dans les pages 'serie', 
 * (comme dans la plupart des cas : la page n'a pas été créée dans 'serie')
 * Elle doit donc être créée dynamiquement :
 * Afficher la page-type single-serie qui recupère l'ID du folder
 */
function wpd_date_404_template( $template = '' ){
    global $wp_query;

    if(isset($wp_query->query['serie_id'])) {
        if ( isset( $wp_query->query['post_type'] ) ) {
            $located = locate_template( 'single-' . $wp_query->query['post_type'] . '.php', false );
            $template = $located !== '' ? $located : locate_template( 'single.php', false );
        }
    }
    return $template;
}
add_filter( '404_template', 'wpd_date_404_template' );



/**
 * Change le nom de la page pour les détail des Série
 * On réccupere l'ID du dossier passé en argument entre serie et serie-detail
 * Et affiche le nom du dossier comme nom de page
 */
function change_custom_post_type_archive_title( $title ) {
    if ( get_page_template( 'serie-details' ) ){
        $id_gallery = get_query_var('serie_id');
        $folder = wp_rml_get_object_by_id($id_gallery);
        if (is_rml_folder($folder)) {       
            $title = "Série " . $folder->getName();
            return $title;
        }
    }
    return '';
}
add_filter( 'pre_get_document_title', 'change_custom_post_type_archive_title' );



function print_serie ($series) {
    // afficher le fichier de Vue
    $permalink =  get_site_url() . '/serie/';	
    include(get_stylesheet_directory() . '/view/serie.php');
}

function serie_shortcode( $atts = [], $content = null, $tag = '' ) {

	// normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	extract (shortcode_atts(
		array(
			'fids' => 0,
		), $atts, $tag
	));
    
    
    if ($fids) {
        $fids = explode(',',$fids);
        if(count($fids)) {
            foreach ($fids as $fid) {
                $series[] = wp_rml_get_object_by_id($fid);
            }
            print_serie($series);
        }
    }

	
}


/**
 * The [post_list] shortcode.
 * Affiche un tas de 3 miniatures representant des pages Portfolio appartenant à une categorie donnée
 * Les 3 images sont piochées dans le dossier d'image associée à la page Portfolio via l'arribut 
 *
 */
function post_list_shortcode( $atts = [], $content = null, $tag = '' ) {
	// normalize attribute keys, lowercase
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// override default attributes with user attributes
	extract (shortcode_atts(
		array(
			'category' => 0,
			'limit' => -1,
			'template' => 'dessin', // pas d'autres 
			'col' => 3, // the number of columns
		), $atts, $tag
	));

    $the_query = new WP_Query ( array(
    	'post_type' => 'portfolio',
    	'numberposts' => $post_list_atts['limit'],
        'tax_query' => array(
            array(
                'taxonomy' => 'portfolio_cat',
                'field' => 'slug',
                'terms'    => $category,
            ),),
    	)) ;
	
	// Taille des miniatures suivant le nombre de colonnes demandées?
	$thumbSize = $col > 3 ? 'thumbnail' : 'medium';
	
	// enclosing tags
	if ( ! is_null( $content ) ) {
		$o .= apply_filters( 'the_content', $content );
	}
	
	// afficher le fichier de Vue
	 include(get_stylesheet_directory() . '/page_list-templates/cms_grid--' . $template . '.php');
	 wp_reset_postdata();
}

/**
 * The [last_pics source="dir_id" id="" limit="" col="" details="" proportions=""] shortcode.
 * Affiche une serie d'image avec ou sans cadre
 * Si l'attribut "source" est : 
 *  - "dir_id" on va chercher toutes les images contenue dans le dossier d'image en question
 *  - "attr" on va chercher les images dont l'attribut nomé par "attr" est reglé sur 1 
 * Affiche les détail de l'oeuvre si l'attribut detail est 1
 * 
 * Utilisé pour afficher les derniers travaux OU les travaux sur une page portfolio :
 * Dans le premier cas, "source" est 'attr' et 'id' est 'mise_en_avant'
 * Dans le second, "source" est 'dir_id' et 'id' est l'id du dossier image
 * Dans les deux cas, on untilise le fichier 'cms_grid--last-pictures.php' pour affciher les résultats
 */
function last_pics_shortcode( $atts = [], $content = null, $tag = '' ) {

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	extract(shortcode_atts(
		array(
			'source' => 'attr',
			'id' => 'mise_en_avant',
			'limit' => 3,
			'template' => 'last-pictures', 
			'proportions' => 0,
			'col' => 3,
			'details' => 0,
		), $atts, $tag
	));
	
    $attachments = get_attachements_from_source ($source,$id,$limit);
    
	// Taille des miniatures suivant le nombre de colonnes demandées?
	$thumbSize = $col > 3 ? 'thumbnail' : 'medium';    

    if ($attachments) :
        $arrayChunks = array_chunk($attachments, $col);
       // print_r($arrayChunks);
        include(get_stylesheet_directory() . '/page_list-templates/cms_grid--' . $template . '.php');
    endif;
}
function get_attachements_from_source ($source = 'attr', $field = 'mise_en_avant', $limit = -1) {
	// Construction de la requète :
	$q = array(
    	'post_type' => 'attachment',
    	'numberposts' => $limit,
    );
    // On fait une recherche par attribut
    if ($source == 'attr') {
        $q['meta_query'] = array(
    		array(
    			'key'   => $field,
    			'value' => '1',
    		)
    	);
    } elseif ($source == 'dir_id') {
        // Sinon, on va chercher les images presentent dans un certain dossier.
        $imagesIDs =  wp_rml_get_attachments($id);
        $q['post__in'] = $imagesIDs;
    }
    
    return get_posts($q); 
}

function masonry_shortcode( $atts = [], $content = null, $tag = '' ) {

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	extract(shortcode_atts(
		array(
			'folder' => false,
			'fid' => 0,
			'thumbSize' => 'medium',
			'r'=>0,
			'imagesIDs' => 0,
			'field' => 0,
		), $atts, $tag
	));
	//echo '<pre>';
	if ($field) {
	    
	}
	if ($imagesIDs) {
	    $imagesIDs = explode(',',$imagesIDs);
	}
	if($fid) :
        $folder = wp_rml_get_object_by_id($fid);
	    $imagesIDs =  $folder->read();
	    if(count($imagesIDs) === 0){
            $children = $folder->getChildren();
            if($children->getCnt()) {
                foreach ($children as $child) {
                    if($child->getCnt()) {
                        $imagesIDs[] = $child->read();
                    }
                } 
                
            }
	   }
    endif;
    if(count($imagesIDs)) {
       $r = '<div class="grid" data-masonry=\'{ "itemSelector": ".grid-item", "columnWidth": ".grid-item", "gutter": 20, "isFitWidth": true }\'>';
       foreach($imagesIDs as $id) {
            $r .= '<div class="grid-item ';
			// Cette fonction permet de recupérer les dimensions de l'image
			$img = wp_get_attachment_image_src($id,$thumbSize);
			$w = $img[1];
			$h = $img[2];
			$r .= $w == $h ? 'img-sq' : ($w > $h ? 'img-lg' : 'img-ht');
			$r .= ' w' . $w . ' h' . $h . '">';
			$r .= wp_get_attachment_link($id,$thumbSize);
			$r .= "\n</div>";
       }
       $r.= '</div>';
   }
   $r.= '<div class="clear"> </div>';

	   //print_r($r);
//	   echo '</pre>';
    return $r;	   

}
	

/**
 * Central location to create all shortcodes.
 */
function shortcodes_init() {
	add_shortcode( 'post_list', 'post_list_shortcode' );
	add_shortcode( 'last_pics', 'last_pics_shortcode' );
	add_shortcode( 'masonry', 'masonry_shortcode' );
	add_shortcode( 'serie', 'serie_shortcode' );
}

add_action( 'init', 'shortcodes_init' );


/**
 * Ajout de la feuille de style
 */
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	wp_enqueue_style(
		'foldery-child-style',
		get_template_directory_uri() . '/assets/css/foldery-child.css',
		array( 'monaco-style' ),
		'1.0.0'
	);
}

add_action( 'wp_enqueue_scripts', 'foldery_enqueue_masonry' );
function foldery_enqueue_masonry() {
	wp_enqueue_script( 'sj_masonry', get_template_directory_uri().'/js/masonry.pkgd.min.js', array(), '4.2.2', true );
}



/**
 * les fonction qui gerent l'envoi d'email.
 */

add_action( 'wp_ajax_send_email', 'callback_send_email' );
add_action( 'wp_ajax_nopriv_send_email', 'callback_send_email' );

function callback_send_email(){

    $name =  $_REQUEST['nom'];
    $email = $_REQUEST['email'];
    $prix = $_REQUEST['prix'];
	$encadrement = $_REQUEST['encadrement'];
	$lien = $_REQUEST['lien'];
    $subject = "Contact Form";
	$email_body = "<h3>Quelqu'un aimerais t'acheter une oeuvre !</h3><br>".
		"Email: $email. <br>".
		"$encadrement. <br><hr>".
		"<img src='$lien' /> <br> $lien";
      $to = "contact@sebastienj.com";
      $headers  = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: client <$email> \r\n";
      $headers .= "Reply-To: $email \r\n";
	  $mail = mail($to,$subject,$email_body,$headers);
		if($mail){
		      echo "Email Sent Successfully";
	        }
	   die();
} 
