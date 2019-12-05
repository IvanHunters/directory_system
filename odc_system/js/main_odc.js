var editor;
var files_list = JSON.parse[""] || {};
var last_file = "";
var saved = true;
var last_lang = "ace/mode/php";
function open_folder(_folder){
	var xmlhttp = new XMLHttpRequest(); 
	xmlhttp.open('GET', '/odc.php'+_folder+"/?od=true&rand="+Math.random(), false);
	xmlhttp.send(editor.getValue());
	if(xmlhttp.status == 200) {
		document.getElementById("dir"+(_folder).replace(/\//g,"-")).innerHTML = xmlhttp.responseText;
		explorer_addListeners();
	}
}
function explorer_addListeners(){
	folders = document.getElementsByClassName("folder");
	for(var i = 0;i < folders.length;i++){
		folders[i].ondblclick = function(){ 
			open_folder(this.title+this.innerText);
		}
		folders[i].onclick = function(){
			if(this.className.search("opened") == -1){
				this.className += " opened";
			}else{
				this.className = this.className.replace(" opened","");
			}
		};
	}
	files = document.getElementsByClassName("file");
	for(var i = 0;i < files.length;i++){
		files[i].onclick = function(){openf(this.title, false, this.getAttribute('size'));};
	}

}
window.addEventListener("load",function(){
	explorer_addListeners();
	editor = ace.edit("editor");
	editor.$blockScrolling = Infinity;
    var date = new Date;
    var hours = date.getHours();
    if(hours > 18)   editor.setTheme("ace/theme/terminal"); // настройка интерфейса
    editor.setFontSize(15);
    editor.getSession().setMode(last_lang);
    editor.getSession().setUseWrapMode(true);
	ace.require("ace/ext/language_tools");
	editor.setOptions({
        enableBasicAutocompletion: false,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });
	editor.setShowPrintMargin(false);
	editor.commands.addCommand({
	name: "showKeyboardShortcuts",
	bindKey: {win: "Ctrl-Alt-h", mac: "Command-Alt-h"},
	exec: function(editor) {
		ace.config.loadModule("ace/ext/keybinding_menu", function(module) {
			module.init(editor);
			editor.showKeyboardShortcuts()
		})
	}
	});
	editor.on("change",function(){
		if(!last_file)return;
		saved = false;
		document.getElementById("save_button").style.color = "#F00";
		});
	editor.setShowPrintMargin(false);
	editor.commands.addCommand({name: "Сохранить",bindKey: {win: "Ctrl-s", mac: "Command-s"},exec: function(editor) {
		if(!saved)savef(location.hash.replace("#",""));
		}});
			editor.commands.addCommand({name: "Удалить файл",bindKey: {win: "Ctrl-d", mac: "Command-d"},exec: function(editor) {
			    	var filename = prompt("Напишите:\ny-удалить файл\nn-не удалять\nc-закрыть\ndir-удалить папку\nbackup-сделать копию, но удалить родительский файл","y");
			    	var path = last_file.split("/").slice(0,-1).join("/")+"/";
		if(filename=="y"){ deletef(last_file);}
		if(filename=="dir")deleteDirf(path);
		if(filename=="c")remove_file(location.hash.replace(/^./,""));
		if(filename=="backup"){backupf(location.hash.replace(/^./,"")); deletef(location.hash.replace(/^./,""));}
	}});

    editor.commands.addCommand({name: "Закрыть вкладку",bindKey: {win: "Esc", mac: "Esc"},exec: function(editor) {
    remove_file(last_file)
  }});

		editor.commands.addCommand({name: "Сделать Бэкап",bindKey: {win: "Ctrl-b", mac: "Command-b"},exec: function(editor) {
		backupf(location.hash.replace("#",""));
	}});
	editor.commands.addCommand({name: "Новый файл",bindKey: {win: "Alt-n", mac: "Alt-n"},exec: function(editor) {
		var path = last_file.split("/").slice(0,-1).join("/")+"/";
		var filename = prompt("Напишите имя файла: "+path,"file.php");
		if(filename)openf(path+filename);
	}});
	editor.commands.addCommand({name: "Обновить код",bindKey: {win: "Alt-r", mac: "Alt-r"},exec: function(editor) {
		var path = last_file.split("/").join("/");
		openf(path);
	}});

	editor.commands.addCommand({name: "Запустить код",bindKey: {win: "Ctrl-r", mac: "Сommand-r"},exec: function(editor) {
		var path = last_file.split("/").join("/");
	var paramer =	prompt("Параметры?","?");
	if(paramer)	runf(path+paramer);
	else runf(path);
	}});
	if(location.hash)openf(location.hash.replace("#",""));
	//window.addEventListener("hashchange",function(){if(location.hash)openf(location.hash.replace("#",""));});
});
function remove_file(_file){
	saves();
	if(!files_list[_file]["saved"]){
		if(!confirm("Файл не сохранен. Вы действительно хотите его закрыть ?"))return;
	}
	delete files_list[_file];
	if(last_file !== _file){
		show_files(last_file);
	}else{
		show_files(last_file);
		for (var firstelm in files_list) {opens(firstelm,true);return;}
			location.hash = "";
			last_file = "";
			saved = true;
			editor.setValue("");
			document.getElementById("save_button").style.color = "#333";
			clearTimeout(timer1);
			document.getElementById("text_state").innerHTML = " ";
	}
}
function show_files(_active){
	document.getElementById("files_list").style.display = "none";
	document.getElementById("files_list").innerHTML = "";
	for(var index in files_list) {
		document.getElementById("files_list").innerHTML += "<a class='button ' style='background: darkseagreen; left:0px;"+((index == _active)?"font-weight:bold;":"")+"' onclick='if(event.which == 2){remove_file(this.title);return false;}else{opens(this.title); console.log(this);}' href='#"+index+"' title='"+index+"'>"+sub_right(index,"/")+"</a>";
		document.getElementById("files_list").style.display = "inline-block";
	};
}
var timer1;

function savef(_f){
		document.getElementById("text_state").innerHTML = "Сохранен : "+_f;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', '/odc.php'+_f+"?save=true&rand="+Math.random(), false);
		xmlhttp.setRequestHeader('Content-Type', 'text/plain');
		xmlhttp.send(editor.getValue());
		if(xmlhttp.status == 200) {
		console.log(xmlhttp.responseText);
		  document.getElementById("text_state").innerHTML = xmlhttp.responseText;
		  document.getElementById("save_button").style.color = "#333";
		  saved = true;
		  timer1 = setTimeout(function() {document.getElementById("text_state").innerHTML = last_file;}, 2000);
		}
	};
	
	function deletef(_f){
	    if(confirm("Вы хотите удалить файл "+_f+" ?")){
	    if(_f == "./odc.php"){
		document.getElementById("text_state").innerHTML = "Нельзя удалить файл: "+_f;
	}else{
	    document.getElementById("text_state").innerHTML = "Файл удален: "+_f;

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', '/odc.php'+_f+"?delete=true&rand="+Math.random(), false);
		xmlhttp.setRequestHeader('Content-Type', 'text/plain');
		xmlhttp.send(editor.getValue());
    if(xmlhttp.status == 200) {
		console.log(xmlhttp.responseText);
    remove_file(_f);
    }
  }
	}};

	function deleteDirf(_f){
	    if(_f!='/'){
		document.getElementById("text_state").innerHTML = "Каталог удален: "+_f;
			var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', '/odc.php'+_f+"?delete_dir=true&rand="+Math.random(), false);
		xmlhttp.setRequestHeader('Content-Type', 'text/plain');
		xmlhttp.send(editor.getValue());
		}else{
		document.getElementById("text_state").innerHTML = "Нельзя удалить корень!";
	}};
	
	function updateFoldersf(_f){
	    document.getElementById("text_state").innerHTML = "Файлы обновлены";
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', '/odc.php'+_f+"?update_folder=true&rand="+Math.random(), false);
		xmlhttp.setRequestHeader('Content-Type', 'text/plain');
		xmlhttp.send(editor.getValue());
		document.getElementById("explorer").innerHTML = xmlhttp.responseText;
	};
	
	function backupf(_f){
		document.getElementById("text_state").innerHTML = "Бэкап сохранен: "+_f;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', '/odc.php'+_f+"?backup=true&rand="+Math.random(), false);
		xmlhttp.setRequestHeader('Content-Type', 'text/plain');
		xmlhttp.send(editor.getValue());
	};
	function runf(_f){
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', _f, false);
		xmlhttp.setRequestHeader('Content-Type', 'text/plain');
		xmlhttp.send(editor.getValue());
			if(xmlhttp.status == 200) {
			    console.clear();
		console.log(xmlhttp.responseText);
			}
	};
function saves(){
	files_list[last_file] = {"CursorPosition":editor.getCursorPosition(),"file":editor.getValue(),"FirstVisibleRow":editor.getFirstVisibleRow(),"lang":last_lang,"saved":saved};
	return last_file;
	};
function opens(_file,_dont_save){
    document.getElementById("text_state").innerHTML = _file;
	if(!_dont_save)saves();
	clearTimeout(timer1);
	show_files(_file);
	editor.setValue(files_list[_file]["file"],files_list[_file]["CursorPosition"]);
	editor.moveCursorTo(files_list[_file]["CursorPosition"]["row"],files_list[_file]["CursorPosition"]["column"]);
	editor.navigateTo(files_list[_file]["CursorPosition"]["row"],files_list[_file]["CursorPosition"]["column"]);
	editor.scrollToLine(files_list[_file]["FirstVisibleRow"]);
	document.getElementById("lang").value = files_list[_file]["lang"];
	last_lang = files_list[_file]["lang"];
	editor.getSession().setMode(files_list[_file]["lang"]);
	saved = files_list[_file]["saved"];
	document.getElementById("save_button").style.color = (saved?"#333":"#F00");
	location.hash = _file;
	last_file = _file;
	};
function openf(_f,_dont_save, size){
  if(size > 10){ alert("Файл слишком большой для открытия\nОн весит "+size+"Mb"); return;}
	if(last_file && !_dont_save)saves();
	location.hash = _f;
	clearTimeout(timer1);
	document.getElementById("text_state").innerHTML = "��������: "+_f;
	var xmlhttp = getXmlHttp();
	xmlhttp.open('GET', '/odc.php'+_f+"?open=true&rand="+Math.random(), false);
	xmlhttp.send(null);
	if(xmlhttp.status == 200) {
	  editor.setValue(xmlhttp.responseText);
	  editor.navigateFileStart();
	  document.getElementById("text_state").innerHTML = _f;
	}
	last_file = _f;
	saved = true;
	document.getElementById("save_button").style.color = "#333";
	saves();
	show_files(_f);
	};


function sub_right(a,b){
	a = a.split(b);
	return a[a.length-1];
}
function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}
function toggle_class(a,b){	a.className = ((a.className.search(b) == -1)?a.className+" "+b:a.className.replace(" "+b,""));}