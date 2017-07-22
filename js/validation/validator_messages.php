<script type="text/javascript">
  $.extend($.validator.messages, { 
    required: '<?php echo addslashes(TEXT_ERROR_REQUIRED) ?>',
    number: '<?php echo addslashes(TEXT_ERROR_REQUIRED_NUMBER) ?>',
    extension: '<?php echo addslashes(TEXT_ERROR_FILE_EXTENSION) ?>'
  });
</script>