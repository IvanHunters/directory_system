<?php
require_once "odc_system/kernel/kernel_odc.php";
require_once "odc_system/kernel/body_odc.php";
?>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link href="/odc_system/css/style_odc.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script async src="/odc_system/js/main_odc.js"></script>
<script src="//k-94.ru/assets/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="//k-94.ru/assets/src-min-noconflict/ext-language_tools.js"></script>
</head>
<body class=" explorer">
<pre id="editor"></pre>
<div id="statusbar"><div id="save_button" class="button" onclick='if(!saved){savef(location.hash.replace("#",""))};'>Save</div><select id="lang" onchange="last_lang = this.value;editor.getSession().setMode(this.value);saves();" class="button">
<option value="ace/mode/css">css</option>
<option value="ace/mode/html">html</option>
<option value="ace/mode/ini">ini</option>
<option value="ace/mode/java">java</option>
<option value="ace/mode/javascript">javascript</option>
<option value="ace/mode/json">json</option>
<option value="ace/mode/mysql">mysql</option>
<option value="ace/mode/php" selected="selected">php</option>
<option value="ace/mode/vbscript">vbscript</option>
<option value="ace/mode/xml">xml</option>
</select><div class="button" onclick='editor.execCommand("showSettingsMenu");'>Settings</div><div class="button" onclick='editor.execCommand("showKeyboardShortcuts");'>Help</div><div class="button" onclick='toggle_class(document.body,"explorer");editor.resize();'>Explorer</div><span id="files_list" style="display:none;height:100%;box-shadow: 1px 0px 0px 0px #F1F1F1;"></span><span id="text_state" class="button" style="float:right;box-shadow: -1px 0px 0px 0px #AAAAAB;"> </span>
</div><div id="explorer">
<?php
	echo(array2ul(kdirlist($dir),$dir,false));
?>
</div>
</body>
</html>
