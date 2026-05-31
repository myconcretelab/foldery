(function($){
    $(document).ready(function(){
      var $container = $('.cms-isotope-masonry-post');
      $('.cms-isotope-masonry-post').each(function(){
       		$this = $(this);
          $filter = $this.parent().find('.cms-grid-filter');
       		$this.imagesLoaded(function(){
       			$this.isotope({
		           itemSelector:'.cms-grid-item',
		        });
       		});
          $filter.find('a').click(function(e){
              e.preventDefault();
              $filter.find("a").removeClass('active');
              $(this).addClass('active');
              var data_filter = $(this).data('filter');
              $this.isotope({
                  filter: data_filter
              });
          });
          $('#cms-portfolio-masonry-sort a').on('click', function(){
            $('#cms-portfolio-masonry-sort a').removeClass('active');
            $(this).addClass('active');
            var cols = parseInt($(this).attr('id').replace('columns',''));
            var item_class = '';
            switch (cols){
              case 2:
                item_class = 'col-xs-12 col-sm-12 col-md-6 col-lg-6';
                break;
              case 3:
              default:
                item_class = 'col-xs-12 col-sm-12 col-md-4 col-lg-4'
                break;
            }
            $this.find('.cms-grid-item').removeClass(function (index, css){
              return (css.match (/(^|\s)col-\S+/g) || []).join(' ');
            });
            $this.find('.cms-grid-item').addClass(item_class);
            $this.isotope({
              itemSelector:'.cms-grid-item'
            });
          })
      });
    });

    $(document).ready(function(){
       $('.cms-isotope-grid-post').each(function(){
          $this = $(this);
          $filter = $this.parent().find('.cms-grid-filter');
          $this.imagesLoaded(function(){
            $this.isotope({
               itemSelector:'.cms-grid-item',
               layoutMode: 'fitRows',
           });
          });

          $filter.find('a').click(function(e){
              e.preventDefault();
              $filter.find("a").removeClass('active');
              $(this).addClass('active');
              var data_filter = $(this).data('filter');
              $this.isotope({
                  filter: data_filter
              });
          });
       });  
    });
})(jQuery);