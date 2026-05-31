(function($){
    $(document).bind('ready ajaxComplete',function(){
       setTimeout(function(){
        $('.cms-grid-wraper .cms-grid-masonry').each(function(){
          var $this = $(this);
          var $filter = $this.parents('.cms-grid-wraper').find('.cms-grid-filter');
          $this.imagesLoaded(function(){
            var $current = $this.parent().attr('data-current');
            $this.shuffle({
               itemSelector:'.cms-grid-item',
            });
            if($current != undefined){
              $this.shuffle('shuffle', $current );
            }
          });
          if($filter){
            $filter.find('a').click(function(e){
              e.preventDefault();
              // set active class
              $filter.find('a').removeClass('active');
              $(this).addClass('active');
                   
              // get group name from clicked item
              var groupName = $(this).attr('data-group');
              $this.parent().attr('data-current', groupName);
              if(groupName == undefined){
                $this.parent().attr('data-current', '');
              }
              // reshuffle grid
              $(this).parents('.cms-grid-wraper').find('.cms-grid-masonry').shuffle('shuffle', groupName );
              return false;
            });
          }
       }); 
      }, 1000) 
    });
})(jQuery);

