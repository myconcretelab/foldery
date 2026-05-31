<?php // VIEW file for shortcode "serie" ?>

<div class="stack-wrapper" data-massonry='{ "itemSelector": ".stack-item", "columnWidth": ".stack-item", "gutter": 30 }'>
<?php
    $thumbSize = 'medium';
    foreach($series as $child) :
	   $imagesIDs =  $child->read();
	   if(count($imagesIDs) === 0):
            $children = $child->getChildren();
            if(count($children)) {
                foreach ($children as $c) {
                    if($c->getCnt()) {
                        $imagesIDs = $c->read();
                        break;
                    }
                } 
            }
        endif;
        if (count($imagesIDs)) :
			$img = wp_get_attachment_image_src($imagesIDs[0],$thumbSize);
			$w = $img[1];
			$h = $img[2];
?>
            <div class="stack-item">
            <a href="<?php echo $permalink . sanitize_title($child->getName()) . '/' . $child->getId(); ?>" class="stack-link">
                <h5><?php echo $child->getName()?></h5>
                <figure class="img-area" id="img-area-<?php echo $imagesIDs[0] ?>" style="width:<?php echo $w/2?>px; height:<?php echo $h/2?>px">
                    <?php 
                    // les trois images sont les première du folder
                        echo wp_get_attachment_image($imagesIDs[0],$thumbSize);
                    ?>            	
                </figure>
                <style>#img-area-<?php echo $imagesIDs[0] ?>:after,#img-area-<?php echo $imagesIDs[0] ?>:before{width:<?php echo $w/2?>px; height:<?php echo $h/2?>px}</style>
            </a>
            </div>
        <?php
        endif;
       endforeach; //$series as $child ?>        
        