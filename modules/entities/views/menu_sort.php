<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title"><?php echo TEXT_SORT ?></h4>
</div>

<?php echo form_tag('menu', url_for('entities/menu')) ?>
<div class="modal-body">
  

<div class="cfg_forms_fields">
<ul id="sort_items" class="sortable">
<?php
$groups_query = db_fetch_all('app_entities_menu','','sort_order, name');
while($v = db_fetch_array($groups_query))
{
  echo '
    <li id="item_' . $v['id'] .'"><div>' . $v['name'] . '</div></li>
  ';
}

?>
</ul>
</div>

</div>

<?php echo ajax_modal_template_footer() ?>

</form>

<script>
  $(function() {         
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul",
    		update: function(event,ui){  
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("entities/menu","action=sort")?>',data: data});
        }
    	});
      

  });  
</script> 