<?php
/**
 * Template Name: Gallerie de série (stack)
 * @package CMSSuperHeroes
 * @subpackage ZK Theme
 * @since 1.0.0
 * @author Seb
 */
?>
<?php get_header();?>
<?php the_content();?>
<?php 
// Le champs folder peut renvoyer un objet IFolder ou l'ID du dossier.
$folder = foldery_rml_resolve_folder(get_field('folder'));

if (is_rml_folder($folder) ): 
    $series = $folder->getChildren();
    if (count($series)) {
        print_serie($series);
    }


 endif; ?>
<?php get_footer(); ?>
