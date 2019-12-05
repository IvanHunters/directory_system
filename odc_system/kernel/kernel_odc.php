<?php
function array2ul($array,$pdir,$no_ignore_ul = true) {
	global $dird;
    if($no_ignore_ul)$out="<ul id='dir".str_replace(array($_SERVER['CONTEXT_DOCUMENT_ROOT'].$dird,"/"),array("","-"),$pdir)."'>";

	foreach($array as $key => $elem){
        if(!is_array($elem)){
                $out=$out."<li class='file' size = '". round((filesize($key) / 1024 / 1024),1) ."' title='".str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'].$dird,"",$key)."'>$elem</li>";
        }
        else $out=$out."<li class='folder' title='".str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'].$dird,"",$pdir)."'>".str_replace(array($pdir."/",$pdir),array("",""),$key)."</li>".array2ul($elem,$key)."";
    }
    if($no_ignore_ul)$out=$out."</ul>";
    return $out;
}


function delFolder($dir)
{
$files = array_diff(scandir($dir), array('.','..'));
foreach ($files as $file) {
(is_dir("$dir/$file")) ? delFolder("$dir/$file") : unlink("$dir/$file");
}
return rmdir($dir);
}

function kscandir($path,$sort_by = "name"){
    $list = array();
	$ignore_files = array("..",".","cgi-bin");
	if (is_dir($path)) {
		$dir = opendir($path);
		while($file = readdir($dir)){
			if (!in_array($file,$ignore_files)) {

				switch($sort_by){
					case "time":
						$sortt = filectime($path . $file) . ',' . $file;
						break;
					default:
						$sortt =  $file;
						break;
				}
				if(!is_link($path . $file)){
					if(!is_dir($path . $file)){
						$list["1".$sortt] = $file;
					}else{
						$list["0".$sortt] = $file;
					}
				}
			}
		}
		closedir($dir);
		ksort($list);
		return $list;
	}
}

function kdirlist($dir){
	$list = array();
	$dir = str_replace("//","/",$dir);
	$wlist = kscandir($dir);
	$c = 0;
	foreach ($wlist as &$file){
		if(is_dir($dir.$file)){
		$list[$dir.$file] = kdirlist($dir.urldecode($file)."/");
		}else{
			$c++;
			if($c < 100)$list[$dir.$file] = urldecode($file);
		}
	}
	return $list;
}

function decode($encoded, $key){//расшифровываем
$strofsym="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM=";//Символы, с которых состоит base64-ключ
$x=0;
while ($x++<= strlen($strofsym)) {//Цикл
$tmp = md5(md5($key.$strofsym[$x-1]).$key);//Хеш, который соответствует символу, на который его заменят.
$encoded = str_replace($tmp[3].$tmp[6].$tmp[1].$tmp[2], $strofsym[$x-1], $encoded);//Заменяем №3,6,1,2 из хеша на символ
}
return base64_decode($encoded);//Вертаем расшифрованную строку
}

?>
