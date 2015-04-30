<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusAlbum"])){ //uue albumi salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_albumid
			(albumi_pealkiri, esitaja_id, aasta, zanr, plaadifirma_id, hinnang) VALUES (?, ?, ?, ?, ?, ?)");
		$kask->bind_param("siisis", $_REQUEST["albumi_pealkiri"], $_REQUEST["esitaja_id"], $_REQUEST["aasta"],
		$_REQUEST["zanri_nimetus"], $_REQUEST["plaadifirma_id"], $_REQUEST["hinnangu_id"]);
		$kask->execute();
		echo $yhendus->error;
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
	
	if(isSet($_REQUEST["kustuta"])){ //albumi kustutamine
		$kask=$yhendus->prepare("DELETE FROM m_albumid WHERE id=?");
		$kask->bind_param("i", $_REQUEST["kustuta"]);
		$kask->execute();
		$yhendus->close();
		exit();
	}
  
	if(isSet($_REQUEST["salvestus_nupp"])){ // muutuste salvestamine
		$kask=$yhendus->prepare("UPDATE m_albumid SET albumi_pealkiri=?, esitaja_id=?, aasta=?, zanr=?, plaadifirma_id=?, hinnang=? WHERE id=?");
		$kask->bind_param("siisisi", $_REQUEST["albumi_pealkiri"], $_REQUEST["esitaja_id"], $_REQUEST["aasta"],
			$_REQUEST["zanri_nimetus"], $_REQUEST["plaadifirma_id"], $_REQUEST["hinnangu_id"], $_REQUEST["muutmise_salvestus_id"]); //tänu muutmise_salvestus_id'le teab, millist lehe muutused salvestada
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?message=muudetud");
		$yhendus->close();
		exit();
	}
?>

<!doctype html>
<html>
	<head>
		<title>Albumid</title>
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
		</style>
	</head>
	<body>
		<div id="menyykiht">
			<?php
				if(isSet($_REQUEST["teade"])){
					echo "Album salvestatud!";
				}
				if(isSet($_REQUEST["message"])){
					echo "Album muudetud!";
				}
			?>
			<h2>Sisestatud albumid:</h2>
			<ul>
				<?php // albumid lühike kuvamine
					$kask=$yhendus->prepare("SELECT m_albumid.id, albumi_pealkiri, esitaja_id, aasta, esitaja_nimi FROM m_albumid JOIN m_esitajad ON m_albumid.esitaja_id=m_esitajad.id ORDER BY aasta ASC");
					$kask->bind_result($id, $albumi_pealkiri, $esitaja_id, $aasta, $esitaja_nimi);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($aasta)." ".htmlspecialchars($albumi_pealkiri)." (".htmlspecialchars($esitaja_nimi).")"."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa album</a>
		</div>
		<div id="sisukiht">
			<?php
				if(isSet($_REQUEST["id"])){ // albumite detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT m_albumid.id, albumi_pealkiri, esitaja_id, aasta, zanr, hinnang, esitaja_nimi, firma_nimi FROM m_albumid
					JOIN m_plaadifirmad ON m_albumid.plaadifirma_id=m_plaadifirmad.id JOIN m_esitajad ON m_albumid.esitaja_id=m_esitajad.id WHERE m_albumid.id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $albumi_pealkiri, $esitaja_id, $aasta, $zanr, $hinnang, $esitaja_nimi, $firma_nimi);
					$kask->execute();
					if($kask->fetch()){
						echo "Albumi pealkiri: ".htmlspecialchars($albumi_pealkiri)."</br>";
						echo "Esitaja: ".htmlspecialchars($esitaja_nimi)."</br>";
						echo "Aasta: ".htmlspecialchars($aasta)."</br>";
						echo "Zanr: ".htmlspecialchars($zanr)."</br>";
						echo "Plaadifirma: ".htmlspecialchars($firma_nimi)."</br>";
						echo "Hinnang: ".htmlspecialchars($hinnang)."</br>";
						
						echo "<br /><a href='?kustuta=$id'>Kustuta</a>"; //kustutamise link
						echo "<br /><a href='?muutmise_alustus_id=$id'>Muuda</a>"; //muutmise alustamise link

					}
					else {
						echo "Vigased andmed.";
					}
				} 
			?>			
			<?php
				if(isSet($_REQUEST["muutmise_alustus_id"])){ // albumi andmete muutmine
					echo"<form action='?'>";
					echo"<input type='hidden' name='muutmise_salvestus_id' value='$id' />";
				?>

							<h2>Albumi muutmine</h2>
							<dl>
								<dt>Albumi pealkiri:</dt>
								<dt>
									<input type="text" name="albumi_pealkiri" />
								</dt>
								
								<dt>Esitaja:</dt>
								<dt>
									<select name="esitaja_id">
										<?php
											$kask=$yhendus->prepare("SELECT id, esitaja_nimi FROM m_esitajad");
											$kask->bind_result($id, $esitaja_nimi);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$id' >$esitaja_nimi</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Aasta:</dt>
								<dt>
									<input type="text" name="aasta" />
								</dt>
								
								<dt>Zanr:</dt>
								<dt>
									<select name="zanri_nimetus">
										<?php
											$kask=$yhendus->prepare("SELECT nimetus FROM m_zanrid");
											$kask->bind_result($nimetus);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$nimetus' >$nimetus</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Plaadifirma:</dt>
								<dt>
									<select name="plaadifirma_id">
										<?php
											$kask=$yhendus->prepare("SELECT id, firma_nimi FROM m_plaadifirmad");
											$kask->bind_result($id, $firma_nimi);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$id' >$firma_nimi</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Hinnang:</dt>
								<dt>
									<select name="hinnangu_id">
										<?php
											$kask=$yhendus->prepare("SELECT hinnang FROM m_hinnangud");
											$kask->bind_result($hinnang);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$hinnang' >$hinnang</option>\n";
											}
										?> 
									</select><br>
								</dt>
	
							</dl>
							<input type="submit" name="salvestus_nupp" value="Salvesta muudatused" />
							<input type="submit" name="katkestus_nupp" value="Katkesta" />
						</form>
					<?php
				}
			?>
		 <?php
				if(isSet($_REQUEST["uus"])){ // uue albumi sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusAlbum" value="jah" />
							<h2>Albumi lisamine</h2>
							<dl>
								<dt>Albumi pealkiri:</dt>
								<dt>
									<input type="text" name="albumi_pealkiri" />
								</dt>
								
								<dt>Esitaja:</dt>
								<dt>
									<select name="esitaja_id">
										<?php
											$kask=$yhendus->prepare("SELECT id, esitaja_nimi FROM m_esitajad");
											$kask->bind_result($id, $esitaja_nimi);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$id' >$esitaja_nimi</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Aasta:</dt>
								<dt>
									<input type="text" name="aasta" />
								</dt>
								
								<dt>Zanr:</dt>
								<dt>
									<select name="zanri_nimetus">
										<?php
											$kask=$yhendus->prepare("SELECT nimetus FROM m_zanrid");
											$kask->bind_result($nimetus);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$nimetus' >$nimetus</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Plaadifirma:</dt>
								<dt>
									<select name="plaadifirma_id">
										<?php
											$kask=$yhendus->prepare("SELECT id, firma_nimi FROM m_plaadifirmad");
											$kask->bind_result($id, $firma_nimi);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$id' >$firma_nimi</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Hinnang:</dt>
								<dt>
									<select name="hinnangu_id">
										<?php
											$kask=$yhendus->prepare("SELECT hinnang FROM m_hinnangud");
											$kask->bind_result($hinnang);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$hinnang' >$hinnang</option>\n";
											}
										?> 
									</select><br>
								</dt>
	
							</dl>
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