<script type="text/javascript" src="js/DataTables-1.10.15/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/media/js/dataTables.bootstrap.min.js"></script>

<script>

$('.data-table').dataTable({
	 			  "sDom": "<'row'<'col-sm-12'<'table-scrollable'tr>>>" +
					        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
					"oLanguage": {                    
             "oPaginate": {
                 "sPrevious": "<i class=\"fa fa-angle-left\"></i>",
                 "sNext": "<i class=\"fa fa-angle-right\"></i>"
             },             
             "sInfo": "<?php echo sprintf(TEXT_DISPLAY_NUMBER_OF_ITEMS, '_START_', '_END_', '_TOTAL_') ?>"
         }
});
					 
</script>