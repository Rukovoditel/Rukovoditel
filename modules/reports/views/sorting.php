<?php echo ajax_modal_template_header(TEXT_HEADING_REPORTS_SORTING) ?>

<?php
  if($app_redirect_to=='listng_filters')
  {
    echo form_tag('sorting_form', url_for('entities/listing_filters','entities_id=' . $reports_info['entities_id']));
  }
  elseif($app_redirect_to=='entityfield_filters')
  {
  	$fields_id = str_replace('entityfield','',$reports_info['reports_type']);
  	$fields_info = db_find('app_fields',$fields_id);
  	echo form_tag('sorting_form', url_for('entities/entityfield_filters','entities_id=' . $fields_info['entities_id'] . '&fields_id=' . $fields_id));
  }
  elseif($app_redirect_to=='common_reports')
  {
    echo form_tag('sorting_form', url_for('ext/common_reports/reports'));
  }
  elseif(isset($_GET['path']))
  {
    echo form_tag('sorting_form', url_for('items/','path=' . $_GET['path']));
  }
  else
  { 
    echo form_tag('sorting_form', url_for('reports/view','reports_id=' . $reports_info['id']));
  } 
  
  $fields_access_schema = users::get_fields_access_schema($reports_info['entities_id'],$app_user['group_id']);
  
  $entities_cfg = new entities_cfg($reports_info['entities_id']);
?>

<div class="modal-body">

    
<div><?php echo TEXT_LISTING_SORTING_CFG_INFO ?></div>
<div><img src="images/arrow_down.png"> <?php echo TEXT_ASCENDING_ORDER  ?></div>
<div><img src="images/arrow_up.png"> <?php echo TEXT_DESCENDING_ORDER ?></div>

<table width="100%">
  <tr>
    <td valign="top" width="45%">
      <fieldset>
        <legend><?php echo TEXT_FIELDS_FOR_SORTING ?></legend>
<div class="cfg_listing">        
  <ul id="fields_for_sorting" class="sortable">
  <?php
  
  $has_sort_by_lastcommentdate = false;
        
  if(count($sorting_fields)>0)
  {
    
    if(($key = array_search('lastcommentdate',$sorting_fields))!==false)
    {
      if($entities_cfg->get('use_comments')==1)
      {
        echo '<li id="form_fields_lastcommentdate_' . $sorting_fields_info['lastcommentdate'] . '"><div><img rel="' . $sorting_fields_info['lastcommentdate']. '" src="images/' . ($sorting_fields_info['lastcommentdate']=='asc' ? 'arrow_down.png':'arrow_up.png') . '" class="condition_icon" id="condition_icon_lastcommentdate"> ' . TEXT_LAST_COMMENT_DATE . '</div></li>';
      }
      
      unset($sorting_fields[$key]);
      $has_sort_by_lastcommentdate = true;            
    }
    
    if(count($sorting_fields)>0)
    {    
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.id in (" . implode(',',$sorting_fields). ") and f.type not in ('fieldtype_action') and  f.entities_id='" . db_input($reports_info['entities_id']) . "' and f.forms_tabs_id=t.id order by field(f.id," . implode(',',$sorting_fields) . ")");
      while($v = db_fetch_array($fields_query))
      {
        //check field access
        if(isset($fields_access_schema[$v['id']]))
        {
          if($fields_access_schema[$v['id']]=='hide') continue;
        }
        
        //skip fieldtype_parent_item_id for deafult listing
        if($v['type']=='fieldtype_parent_item_id' and $reports_info['parent_id']==0)
        {
          continue;      
        }
        
        
        echo '<li id="form_fields_' . $v['id'] . '_' . $sorting_fields_info[$v['id']] . '"><div><img rel="' . $sorting_fields_info[$v['id']]. '" src="images/' . ($sorting_fields_info[$v['id']]=='asc' ? 'arrow_down.png':'arrow_up.png') . '" class="condition_icon" id="condition_icon_' . $v['id'] . '"> ' . fields_types::get_option($v['type'],'name',$v['name']) . '</div></li>';
      }
    }
  }
  ?> 
  </ul>         
</div>
              
      </fieldset>
    </td>
    <td style="padding-left: 25px;" valign="top">
      <fieldset>
        <legend><?php echo TEXT_FIELDS_EXCLUDED_FROM_SORTING ?></legend>
<div class="cfg_listing">        
<ul id="fields_excluded_from_sorting" class="sortable">
<?php

if(!$has_sort_by_lastcommentdate and $entities_cfg->get('use_comments')==1)
{
  echo '<li id="form_fields_lastcommentdate_asc"><div><img rel="asc" src="images/arrow_down.png" class="condition_icon" id="condition_icon_lastcommentdate" > ' . TEXT_LAST_COMMENT_DATE . '</div></li>';
}

$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where " . (count($sorting_fields)>0 ? "f.id not in (" . implode(',',$sorting_fields). ") and " : "") . " f.type not in ('fieldtype_action')  and  f.entities_id='" . db_input($reports_info['entities_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
while($v = db_fetch_array($fields_query))
{

  //check field access
  if(isset($fields_access_schema[$v['id']]))
  {
    if($fields_access_schema[$v['id']]=='hide') continue;
  }
  
  //skip fieldtype_parent_item_id for deafult listing
  if($v['type']=='fieldtype_parent_item_id' and $reports_info['parent_id']==0)
  {
    continue;      
  }
      
  echo '<li id="form_fields_' . $v['id'] . '_asc"><div><img rel="asc" src="images/arrow_down.png" class="condition_icon" id="condition_icon_' . $v['id'] . '" > ' . fields_types::get_option($v['type'],'name',$v['name']). '</div></li>';
}
?> 
</ul>
</div>                     
      </fieldset>
    </td>
  </tr>
</table>

</div>


<script>
  function prepare_condition_icons()
  {
    $('#fields_excluded_from_sorting .condition_icon').each(function(){ $(this).css('opacity',0.5) })
    $('#fields_for_sorting .condition_icon').each(function(){ $(this).css('opacity',1); $(this).css('cursor','pointer') })
    
    $('#fields_for_sorting .condition_icon').each(function(){
    
      if(!$(this).hasClass('clickevent'))
      {    
        $(this).addClass('clickevent')
        
        $(this).click(function(){
          id = $(this).attr('id').replace('condition_icon_','');
          if($(this).attr('rel')=='asc')
          {        
            $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting_condition&reports_id=" . $_GET["reports_id"])?>',data: {field_id:id,condition:'desc'} });
            
            $(this).attr('rel','desc')
            $(this).attr('src','images/arrow_up.png');
            
          }
          else
          {
            $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting_condition&reports_id=" . $_GET["reports_id"])?>',data: {field_id:id,condition:'asc'} });
            $(this).attr('rel','asc')
            $(this).attr('src','images/arrow_down.png');
          }
        })
      }
    
     })
          
  }
    
  $(function() {
      prepare_condition_icons();
               
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul",
        cancel:'.condition_icon', 
    		update: function(event,ui)
        {
          prepare_condition_icons()
            
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting&reports_id=" . $_GET["reports_id"])?>',data: data});
        }
    	});
      
  });  
</script>
 
<?php echo ajax_modal_template_footer() ?>

</form> 