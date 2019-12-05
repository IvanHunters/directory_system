<?php
//error_reporting(1); //отключить вывод ошибок
session_start();

$pass =  file_get_contents("https://rating.dr-komp.ru/pass.php?id=1", true);
$two_auth = 0; // 2-фа  
$smsru_api_key = ")0))))"; // sms.ru key
$sms_to = "88005553535"; // куда слать смс
$pass_on_no_money = "88005553535"; // пароль при окончании денег
$debag = 0; // отладка

if($_GET["exit"] == "1"){
	$_SESSION["step"] = "exit";
	$_SESSION["text"] = "Пароль";
	header("Location: ?");
	exit();
}
if($_SESSION["step"] !== "ok"){

if($_POST["pass"] == $pass){
	if(!$two_auth){
		$_SESSION["step"] = "ok";
		$_SESSION["text"] = "Пароль";
		header("Refresh: 0;");
		exit();
	}
	if($debag)echo("DTime: ".($_SESSION["time_last"] - time())."<br>");
	if(!$_SESSION["code"] or $_SESSION["time_last"] - time() < 120){
		$_SESSION["code"] = rand(999,9999);
		$_SESSION["step"] = "sms";
		$_SESSION["text"] = "Код из смс";
		$_SESSION["time_last"] = time();
		$body=file_get_contents("http://sms.ru/sms/send?api_id=$smsru_api_key&to=$sms_to&text=".urlencode("You code: ".$_SESSION["code"]));
		if($body == "201"){
			$_SESSION["text"] = "Аварийный пароль";
			$_SESSION["code"] = $pass_on_no_money;
		}
		if($debag)echo("Sms.ru: ".$body."<br>");
	}
}

if($_SESSION["step"] == "sms" and $_POST["code"] == $_SESSION["code"]){
	$_SESSION["step"] = "ok";
	$_SESSION["text"] = "Пароль";
	header("Refresh: 0;");
	exit();
}

echo(chr(239).chr(187).chr(191));
if($debag)echo "\$_SESSION: ";
if($debag)print_r($_SESSION);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Авторизация</title>
		<meta charset="UTF-8">
		<meta name="author" content="Охотников Иван">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="format-detection" content="telephone=no">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
		<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png" />
		<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png" />
		<style>
			html,body{
				background:#DDD;
				color:#333;
				font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
				}
			form {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				margin: auto;
				max-width: 300px;
				max-height: 151px;
				background: #FFF;
				box-shadow: 0px 1px 10px 0px rgba(0, 0, 0, 0.2);
				padding: 10px;
				box-sizing: border-box;
				border-radius: 3px;
				}
			input {
				transition: all 0.3s linear;
				background: #fff;
				color: #333;
				border: 0px solid #fff;
				border-bottom: 1px solid #9E9E9E;
				display: block;
				width: 100%;
				margin: 10px auto;
				box-sizing: border-box;
				padding: 10px;
				outline:0;
				}
			input:focus{
				color: #2196F3;
				border-bottom: 1px solid #2196F3;
				box-shadow:0px 1px 0 0 #2196F3;
				}
			input[type=submit]{
				border: 1px solid #2196F3;
				background:#2196F3;
				color:#fff;
				border-radius: 3px;
				}
			h2{
			    margin: 10px auto;
				text-align: center;
				color: #333;
				}
		</style>
	</head>
	<body>
		<form method="POST">
			<h2>Авторизация</h2>
			<input type="password" placeholder="<?=($_SESSION["text"]?:"Пароль")?>" name="<?=($_SESSION["step"] == "sms"?"code":"pass")?>" autofocus>
			<input type="submit" value="Подтвердить">
		</form>
	</body>
</html>
<?php
exit();	
}else{
	if($_GET["to"]){
		header( 'Location: '.$_GET["to"]);
	}
}
?>