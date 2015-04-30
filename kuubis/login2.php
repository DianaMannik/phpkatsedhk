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
	
	if(isSet($_REQUEST["uusKasutaja"])){ //uue kasutaja salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_kasutajad
			(knimi, paroolir2si, roll) VALUES (?, PASSWORD(?), ?)");
		$kask->bind_param("sss", $_REQUEST["knimi"], $_REQUEST["paroolir2si"], $_REQUEST["roll"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
?>
<!doctype html>
<html>
	<head>
		<title>Isiku tuvastus</title>
				<style type="text/css">
		   #menyykiht{
			 padding-right: 30px;
			 float: left;
		   }
		   #sisukiht{
			 float:left;
			 margin-top: 70px;
			 margin-left: 100px;
		   }
		   #jalusekiht{
			 clear: left;
		   }
		   body{
			   background-image: url("http://tigu.hk.tlu.ee/~elisa-rael.tonnov/PHP/Muusika_lehestik/taust.jpg");			   
		   }
		</style>
		<h2>Isiku kontroll</h2>
	</head>
	<body>
		<?php
			if(isSet($_REQUEST["teade"])){
				echo "Kasutaja salvestatud!";
			}
		?>
			
		<?php if(isSet($_SESSION["kasutajanimi"])): ?>
			<meta http-equiv="refresh" content="0; URL='http://tigu.hk.tlu.ee/~elisa-rael.tonnov/PHP/Muusika_lehestik/boss.php' "/>
		<?php else:?>
			<form action="?" method="post">
				<dl>
					<dt style="margin-left: 40px;">Kasutajanimi:</dt>
					<dd><input type="text" name="kasutajanimi" /></dd>
					<dt style="margin-left: 40px;">Parool:</dt>
					<dd><input type="password" name="parool" /></dd>
					<dd><input type="submit" value="Sisesta" /></dd></br></br>

				</dl>
				</form>
					<a href='?uus=jah'>Tee kasutaja</a>
					<a href="kylaline.php" style="margin-left: 40px;">Sisene külalisena ja jäta kommentaar</a>

		<?php endif ?>
		
		<?php
		if(isSet($_REQUEST["uus"])){ // uue kasutaja sisestamine
		?>
						<form action='?'>
							<input type="hidden" name="uusKasutaja" value="jah" />
							<h2>Kasutaja lisamine</h2>
							<dl>
								<dt>Kasutajanimi:</dt>
								<dt>
									<input type="text" name="knimi" />
								</dt>
								
								<dt>Parool:</dt>
								<dt>
									<input type="password" name="paroolir2si" />
								</dt>
								<dt>
								<input type="hidden" name="roll" value="huviline" />
								</dt>
	
							</dl>
							<input type="submit" value="Sisesta">
						</form>
					<?php
				} ?>
	</body>
</html>
<?php
	$yhendus->close();
?>
