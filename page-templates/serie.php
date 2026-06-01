<?php
/**
 * Template Name: Gallerie de série (stack)
 * @package Foldery
 * @subpackage ZK Theme
 * @since 1.0.0
 * @author Seb
 */
?>
<?php get_header();?>
<?php the_content();?>
<?php 
// Le champs folder peut renvoyer un objet IFolder ou l'ID du dossier.
$folder = foldery_media_resolve_folder(get_field('folder'));

if (foldery_is_media_folder($folder) ): 
    $series = $folder->getChildren();
    if (count($series)) {
        print_serie($series);
    }


 endif; ?>
<?php get_footer(); ?>
