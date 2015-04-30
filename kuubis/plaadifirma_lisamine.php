<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusFirma"])){ //uue plaadifirma salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_plaadifirmad
			(id, firma_nimi) VALUES (?, ?)");
		$kask->bind_param("ss", $_REQUEST["id"], $_REQUEST["firma_nimi"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}

?>

<!doctype html>
<html>
	<head>
		<title>Plaadifirmad</title>
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
	</head>
	<body>
		<div id="menyykiht">
			<?php
				if(isSet($_REQUEST["teade"])){
					echo "Plaadifirma lisatud!";
				}
			?>
			<h2>Sisestatud:</h2>
			<ul>
				<?php
					$kask=$yhendus->prepare("SELECT firma_nimi FROM m_plaadifirmad");
					$kask->bind_result($firma_nimi);
					$kask->execute();
					while($kask->fetch()){
						echo "<li>".htmlspecialchars($firma_nimi)."</li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa</a>
		</div>
		<div id="sisukiht">		
			<?php		 
				if(isSet($_REQUEST["uus"])){ // uue firma sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusFirma" value="jah" />
							<h2>Plaadifirma lisamine</h2>
								<dt>
									<input type="text" name="firma_nimi" />
								</dt>
							<input type="submit" value="Sisesta">
						</form>
					<?php
				}
			?>
			
			
		</div>
		<div id="jalusekiht">
			<br><br><br><br><a href="boss.php">Tagasi</a>
		</div>
	</body>
</html>
<?php
  $yhendus->close();
?>