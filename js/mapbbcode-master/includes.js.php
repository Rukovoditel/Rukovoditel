<script src="js/mapbbcode-master/dist/lib/leaflet.js"></script>
<script src="js/mapbbcode-master/dist/lib/leaflet.draw.js"></script>

<script src="js/mapbbcode-master/src/MapBBCode.js"></script>
<script src="js/mapbbcode-master/src/MapBBCodeUI.js"></script>
<script src="js/mapbbcode-master/src/MapBBCodeUI.Editor.js"></script>
<script src="js/mapbbcode-master/src/images/EditorSprites.js"></script>
<script src="js/mapbbcode-master/src/controls/FunctionButton.js"></script>
<script src="js/mapbbcode-master/src/controls/LetterIcon.js"></script>
<script src="js/mapbbcode-master/src/controls/PopupIcon.js"></script>
<script src="js/mapbbcode-master/src/controls/Leaflet.Search.js"></script>
<script src="js/mapbbcode-master/src/handlers/Handler.Text.js"></script>
<script src="js/mapbbcode-master/src/handlers/Handler.Color.js"></script>
<script src="js/mapbbcode-master/src/handlers/Handler.Length.js"></script>

<?php if(is_file('js/mapbbcode-master/dist/lang/' . APP_LANGUAGE_SHORT_CODE . '.js')){ ?>
	<script src="js/mapbbcode-master/dist/lang/<?php echo APP_LANGUAGE_SHORT_CODE ?>.js"></script>
<?php }else{ ?>
	<script src="js/mapbbcode-master/dist/lang/en.js"></script>
<?php } ?>
