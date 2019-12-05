<?php
$dirx = str_replace("?".$_SERVER['QUERY_STRING'],"",$_SERVER['REQUEST_URI']);
$dirx = str_replace("odc.php/","",$dirx);
$dirx = str_replace("//","/",$dirx);
$dird = "";

include("auth2.php");

$dir = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . $dirx;
if($_GET["save"] == "true"){
	header("Content-type: text/plain");
    $file = basename($dir);
    $path = 	preg_replace("/".basename($dir)."/","",$dir);
    $path = explode("/",$path);
    unset($path[0]);
    unset($path[1]);
    unset($path[2]);
    unset($path[3]);
    unset($path[4]);
    unset($path[5]);

    $path= implode("/",$path);
    if (!is_dir($path))	mkdir($path);
		$HTTP_RAW_POST_DATA =  file_get_contents("php://input");
    file_put_contents($dir,$HTTP_RAW_POST_DATA);
    print_r("Сохранено");
    exit();
}

if($_GET["open"] == "true"){
    if(!is_dir($dir) and file_exists($dir)){
    	header("Content-type: text/plain");
    	readfile($dir);
    }
    exit();
}


if($_GET["delete_dir"] == "true"){
	header("Content-type: text/plain");
	if($dir=="/"){
	    print_r("Нельзя удалить корень");
	}else{
    	delFolder($dir);
    	print_r("$dir");
	}
	exit();
}

if($_GET['update_folder'] == true){
    echo(array2ul(kdirlist("/"),$dir,false));
}


if($_GET["delete"] == "true"){
    header("Content-type: text/plain");
    $filei = preg_replace("/(.+)html\//","",$dir);
    unlink($filei);
	print_r("Файл $filei удален");
	exit();
}
if($_GET["backup"] == "true"){
	header("Content-type: text/plain");
    file_put_contents($dir."_(".date("m.d.y_H:i").").php",$HTTP_RAW_POST_DATA);
	print_r("Бэкап сохранен");
	exit();
}

$return = array();
echo(chr(239).chr(187).chr(191));
$return["info"]["dir"] = $dir;

if($_GET["od"] == "true"){
	echo(array2ul(kdirlist($dir),$dir,false));
	exit();
}
?>
