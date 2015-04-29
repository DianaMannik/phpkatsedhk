<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
	session_start();
	if(isSet($_REQUEST["kasutajanimi"])){
		$kask=$yhendus->prepare(
		"SELECT roll FROM m_kasutajad WHERE knimi=? AND paroolir2si=PASSWORD(?)");
		$knimiparool=$_REQUEST["kasutajanimi"]."_".$_REQUEST["parool"];
		$kask->bind_param("ss", $_REQUEST["kasutajanimi"], $knimiparool);
		$kask->bind_result($roll);
		$kask->execute();
		if($kask->fetch()){
			$_SESSION["kasutajanimi"]=$_REQUEST["kasutajanimi"];
			$_SESSION["roll"]=$roll;
			$kask->close();
		}
	}
	if(isSet($_REQUEST["lahku"])){
		unset($_SESSION["kasutajanimi"]);
		unset($_SESSION["roll"]);
	}
?>
<!doctype html>
<html>
	<head>
		<title>Isiku tuvastus</title>
		<h2>Isiku kontroll</h2>
	</head>
	<body>
		<?php if(isSet($_SESSION["kasutajanimi"])): ?>
			<meta http-equiv="refresh" content="0; URL='http://tigu.hk.tlu.ee/~elisa-rael.tonnov/PHP/Kodused/05-Halduslehestik%20kuubis/boss.php' "/>
		<?php else:?>
			<form action="?" method="post">
				<dl>
					<dt style="margin-left: 40px;">Kasutajanimi:</dt>
					<dd><input type="text" name="kasutajanimi" /></dd>
					<dt style="margin-left: 40px;">Parool:</dt>
					<dd><input type="password" name="parool" /></dd>
					<dd><input type="submit" value="Sisesta" /></dd></br></br>
					<a href="kylaline.php" style="margin-left: 40px;">Sisene külalisena ja jäta kommentaar</a>
				</dl>
			</form>
		<?php endif ?>
	</body>
</html>
<?php
	$yhendus->close();
?>
