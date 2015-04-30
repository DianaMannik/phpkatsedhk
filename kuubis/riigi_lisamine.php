<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusRiik"])){ //uue riigi salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_riigid
			(riigikood, riik) VALUES (?, ?)");
		$kask->bind_param("ss", $_REQUEST["riigikood"], $_REQUEST["riik"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}

?>

<!doctype html>
<html>
	<head>
		<title>Riigid</title>
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
					echo "Riik lisatud!";
				}
			?>
			<h2>Sisestatud:</h2>
			<ul>
				<?php
					$kask=$yhendus->prepare("SELECT riik FROM m_riigid");
					$kask->bind_result($riik);
					$kask->execute();
					while($kask->fetch()){
						echo "<li>".htmlspecialchars($riik)."</li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa</a>
		</div>
		<div id="sisukiht">		
			<?php		 
				if(isSet($_REQUEST["uus"])){ // uue riigi sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusRiik" value="jah" />
							<h2>Riigi lisamine</h2>
							
							<dt>Riigi nimi:</dt>
								<dt>
									<input type="text" name="riik" />
								</dt>
							<dt>Riigikood:</dt>
								<dt>
									<input type="text" name="riigikood" />
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