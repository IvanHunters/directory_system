<?php

$config = file_get_contents(".dirconfig");
$array_config = explode("\n\n",$config);
foreach($array_config as $place)
{
    $place = str_replace("%empty%", "", $place);
    
    $explode_config = explode("\n",$place);
    $title = preg_replace("/\[|\]/imu", "", $explode_config[0]);
    unset($explode_config[0]);
    
    if($title == "CONFIGURATE")
    {
        foreach($explode_config as $varchar){
            $split_param = explode("=",$varchar);
            $config_data_row[$split_param[0]] = $split_param[1];
        }
        $explode_config = $config_data_row;
            
    }
    
    $config_data[$title] = $explode_config;
}

function array2ul($array,$pdir,$no_ignore_ul = true) {
	global $dird, $config_data;
	
    if($no_ignore_ul)$out="<ul id='dir".str_replace(array($_SERVER['DOCUMENT_ROOT'].$dird,"/"),array("","-"),$pdir)."'>";

	foreach($array as $key => $elem){
	    $normal_pdir = str_replace($_SERVER['DOCUMENT_ROOT']."/", "", $elem);
        if(!is_array($elem)){
            if(round((filesize($key) / 1024 / 1024),1) < 15)
                $out=$out."<li class='file' size = '". round((filesize($key) / 1024 / 1024),1) ."' title='".str_replace($_SERVER['DOCUMENT_ROOT'].$dird,"",$key)."'>$elem</li>";
        }
        else
        {
            $out=$out."<li class='folder' title='".str_replace($_SERVER['DOCUMENT_ROOT'].$dird,"",$pdir)."'>".str_replace(array($pdir."/",$pdir),array("",""),$key)."</li>".array2ul($elem,$key)."";
        }
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
    global $config_data;
    $list = array();
	$ignore_files = array("..",".","cgi-bin");
	if (is_dir($path)) {
	    $normal_pdir = str_replace($_SERVER['DOCUMENT_ROOT']."/", "", $path);
	    
    	$dir_chunks = explode("/",$path);
    	$last_chunk = $dir_chunks[count($dir_chunks) - 2];
    	if(in_array($normal_pdir, $config_data['IGNORE_DIR']) || $last_chunk == '.git')
    	{
    	    return false;
    	}
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
    global $config_data, $cpd;
    
    $max_files_on_dir = isset($config_data["CONFIGURATE"]["max_file_view_in_folder"])? $config_data["CONFIGURATE"]["max_file_view_in_folder"] : 100;
    $extentions_ignore = $config_data['HIDE_EXTENTIONS'];
    
	$list = array(); 
	$dir = str_replace("//","/",$dir);
	$wlist = kscandir($dir);
    $c = 0; 
    
	foreach ($wlist as &$file){
		if(is_dir($dir.$file)){
		    $kdir = kdirlist($dir.urldecode($file)."/");
		    if($kdir != false)
		    $list[$dir.$file] = $kdir;
		}else{
		    $c++;
		    preg_match("/\.\w+$/imu", $file, $extention);
		    
		    if(is_null($extention[0]))
		    {
		        $extention[0] = "";
		    }
		    
		    if(in_array($extention[0], $config_data['CLEAN_EXTENTIONS']))
		    {
		       unlink($dir.$file);
		    }
		    
		    if(!in_array($extention[0], $extentions_ignore))
		    {
    		    if($c < $max_files_on_dir)
    		    {
    		        $list[$dir.$file] = urldecode($file);
    		    }
		    }
		}
	}
	$cpd[$c] = $dir;
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