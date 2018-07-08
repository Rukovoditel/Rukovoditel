/*
 * Author: Yves Van Broekhoven & Simon Menke
 * Created at: 2012-07-05
 *
 * Requirements:
 * - jQuery
 * - jQuery UI
 * - Chosen
 *
 * Version: 1.0.0
 */
(function($) {

  $.fn.chosenOrder = function() {
    var $this   = this.filter('.chosen-sortable[multiple]').first(),
        $chosen = $this.siblings('.chosen-container');

    return $($chosen.find('.chosen-choices li[class!="search-field"]').map( function() {
      if (!this) {
        return undefined;
      }
      text = $(this).html()
      text = text.replace(/<b class="group-name">(.*)<\/b>/,'').replace(/<\/?[^>]+(>|$)/g, "")
      //text = $(this).text()
      return $this.find('option:contains(' + text + ')')[0];
    }));
  };


  /*
   * Extend jQuery
   */
  $.fn.chosenSortable = function(){
    var $this = this.filter('.chosen-sortable[multiple]');

    $this.each(function(){
    	
    	chosen_order = $(this).attr('chosen_order').split(','); $(this).setSelectionOrder(chosen_order);
    	console.log(chosen_order)
    	
      var $select = $(this);
      var $chosen = $select.siblings('.chosen-container');

      // On mousedown of choice element,
      // we don't want to display the dropdown list
      $chosen.find('.chosen-choices').bind('mousedown', function(event){
        if ($(event.target).is('span')) {
          event.stopPropagation();
        }
      });

      // Initialize jQuery UI Sortable
      $chosen.find('.chosen-choices').sortable({
        'placeholder' : 'ui-state-highlight',
        'items'       : 'li:not(.search-field)',
        //'update'      : _update,
        'tolerance'   : 'pointer'
      });

      // Intercept form submit & order the chosens
      $select.closest('form').on('submit', function(){      	
        var $options = $select.chosenOrder();        
        $select.children().remove();
        $select.append($options);
      });

    });

  };

}(jQuery));