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
		header("Location: login.php"); //lahkumisel viib tagasi esilehele
	}
?>
<!doctype html>
<html>
	<head>
		<title>Muusikamogul olen</title>
	</head>
	<body>
		<?php if(isSet($_SESSION["kasutajanimi"])): ?>
			Tere, <?php echo $_SESSION["roll"]." ".$_SESSION["kasutajanimi"]; ?><br /><br />
			<ul>
				<?php if($_SESSION["roll"]=="boss"): ?>
					<li><a href="artisti_lisamine.php">Lisa artist</a></li> <!--igast jura mida muuta saab  -->
					<li><a href="albumi_lisamine.php">Lisa album</a></li>
					<li><a href="laulu_lisamine.php">Lisa laul</a></li>
					<li><a href="video_lisamine.php">Lisa video</a></li>
					<li><a href="auhinna_lisamine.php">Lisa auhind</a></li>
				<?php endif ?>
			</ul>
		<?php endif ?>
	</body>
	<div id="jalusekiht">
		</br></br></br></br></br></br><a href="?lahku=jah">Lahku</a>
	</div>
</html>
<?php
	$yhendus->close();
?>
