<?php
global $masonry_filter,$cms_meta;
$page_category = isset($cms_meta->_cms_portfolio_categories)?$cms_meta->_cms_portfolio_categories:'';

if (empty($page_category)) {
    $args = array(
        'parent' => 0
    );
} else {
	$slugs = explode(',', $page_category);
    $args = array(
        'slug' => $slugs
    );
}
$categories = get_terms('portfolio_cat', $args);
if ($masonry_filter && count($categories) > 0) {	?>
	<div class="cms-grid-filter playfairdisplay clearfix">
        <ul class="list-unstyled list-inline cms-filter-category">
            <li><a class="active" href="#" data-filter=""><?php esc_html_e('All', 'foldery' ); ?></a></li>
            <?php foreach($categories as $category):?>
                <li><a href="#" data-filter=".portfolio-<?php echo esc_attr($category->slug);?>">
                        <?php echo esc_html($category->name );?>
                    </a>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
<?php  
}
?>
