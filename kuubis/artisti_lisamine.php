<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusEsineja"])){ //uue esineja salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_esitajad
			(esitaja_nimi, syndinud, surnud, kodakondsus) VALUES (?, ?, ?, ?)");
		$kask->bind_param("siis", $_REQUEST["esitaja_nimi"], $_REQUEST["syndinud"], $_REQUEST["surnud"], $_REQUEST["kodakondsus"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
	
	if(isSet($_REQUEST["kustuta"])){ //esineja kustutamine
		$kask=$yhendus->prepare("DELETE FROM m_esitajad WHERE id=?");
		$kask->bind_param("i", $_REQUEST["kustuta"]);
		$kask->execute();    
	}
  
	if(isSet($_REQUEST["salvestus_nupp"])){ // muutuste salvestamine
		$kask=$yhendus->prepare("UPDATE m_esitajad SET surnud=?, kodakondsus=? WHERE id=?");
		$kask->bind_param("isi",
			$_REQUEST["surnud"], $_REQUEST["kodakondsus"], $_REQUEST[muutmise_salvestus_id]); //tänu muutmise_salvestus_id'le teab, millist lehe muutused salvestada
		$kask->execute();
	}
?>

<!doctype html>
<html>
	<head>
		<title>Artistid</title>
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
					echo "Esineja salvestatud!";
				}
			?>
			<h2>Sisestatud artistid:</h2>
			<ul>
				<?php // artistide lühike kuvamine
					$kask=$yhendus->prepare("SELECT id, esitaja_nimi FROM m_esitajad ORDER BY esitaja_nimi ASC");
					$kask->bind_result($id, $esitaja_nimi);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($esitaja_nimi)."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Uus artist</a>
		</div>
		<div id="sisukiht">
			<?php
				if(isSet($_REQUEST["id"])){ // esinejate detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT id, esitaja_nimi, syndinud, surnud, kodakondsus FROM m_esitajad WHERE id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $esitaja_nimi, $syndinud, $surnud, $kodakondsus);
					$kask->execute();
					if($kask->fetch()){
						echo "ID nr: ".htmlspecialchars($id)."</br>";
						echo "Nimi: ".htmlspecialchars($esitaja_nimi)."</br>";
						echo "Sünniaasta: ".htmlspecialchars($syndinud)."</br>";
						echo "Surmaaasta: ".htmlspecialchars($surnud)."</br>";
						echo "Kodakondsus: ".htmlspecialchars($kodakondsus)."</br>";
						
						echo "<br /><a href='?kustuta=$id'>Kustuta</a>"; //kustutamise link
						
						if($surnud==NULL){
							echo "<br /><a href='?muutmise_alustus_id=$id'>Muuda</a>"; //muutmise alustamise link
						};
					}
					else {
						echo "Vigased andmed.";
					}
				} 
		 
				if(isSet($_REQUEST["muutmise_alustus_id"])){ // andmete muutmine
					$kask=$yhendus->prepare("SELECT id, surnud, kodakondsus FROM m_esitajad WHERE id=?");
					$kask->bind_param("i", $_REQUEST["muutmise_alustus_id"]); 
					$kask->bind_result($id, $surnud, $kodakondsus);
					$kask->execute();
					if($kask->fetch()){
						echo "<form action='?'>";
						echo "<input type='hidden' name='muutmise_salvestus_id' value='$id' />";
						echo "Surnud:<br /> <input type='text' name='surnud' value='".
													htmlspecialchars($surnud)."' /><br />";
						echo "Kodakondsus:<br /> <input type='text' name='kodakondsus' value='".
													htmlspecialchars($kodakondsus)."' /><br />";
						echo "<input type='submit' name='salvestus_nupp' value='Salvesta' />";
						echo "<input type='submit' name='katkestus_nupp' value='Katkesta' />";
						echo "</form>";
					}
					else {
						echo "Vigased andmed.";
					}
				}
		 
				if(isSet($_REQUEST["uus"])){ // uue esineja sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusEsineja" value="jah" />
							<h2>Uus artist</h2>
							<dl>
								<dt>Nimi:</dt>
								<dt>
									<input type="text" name="esitaja_nimi" />
								</dt>
								
								<dt>Sündinud:</dt>
								<dt>
									<input type="text" name="syndinud" />
								</dt>
								
								<dt>Surnud:</dt>
								<dt>
									<input type="text" name="surnud" />
								</dt>
								
								<dt>Kodakondsus:</dt>
								<dt>
									<select name="kodakondsus">
										<?php
											$kask=$yhendus->prepare("SELECT riigikood, riik FROM m_riigid");
											$kask->bind_result($riigikood, $riik);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$riigikood' >$riik</option>\n";
											}
										?>
									</select><br>
								</dt>
								
							</dl>
							<input type="submit" value="Sisesta andmebaasi">
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