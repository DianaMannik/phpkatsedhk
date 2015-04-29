<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusAlbum"])){ //uue albumi salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_albumid
			(albumi_pealkiri, esitaja_id, aasta, zanr, plaadifirma_id, hinnang) VALUES (?, ?, ?, ?, ?, ?)");
		$kask->bind_param("siisis", $_REQUEST["albumi_pealkiri"], $_REQUEST["esitaja_id"], $_REQUEST["aasta"],
		$_REQUEST["zanr"], $_REQUEST["plaadifirma_id"], $_REQUEST["hinnang"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
	
	if(isSet($_REQUEST["kustuta"])){ //albumi kustutamine
		$kask=$yhendus->prepare("DELETE FROM m_albumid WHERE id=?");
		$kask->bind_param("i", $_REQUEST["kustuta"]);
		$kask->execute();    
	}
  
	if(isSet($_REQUEST["salvestus_nupp"])){ // muutuste salvestamine
		$kask=$yhendus->prepare("UPDATE m_albumid SET albumi_pealkiri=?, esitaja_id=?, aasta=?, zanr=?, plaadifirma_id=?, hinnang=? WHERE id=?");
		$kask->bind_param("siisisi", $_REQUEST["albumi_pealkiri"], $_REQUEST["esitaja_id"], $_REQUEST["aasta"],
			$_REQUEST["zanr"], $_REQUEST["plaadifirma_id"], $_REQUEST["hinnang"], $_REQUEST[muutmise_salvestus_id]); //tänu muutmise_salvestus_id'le teab, millist lehe muutused salvestada
		$kask->execute();
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
			?>
			<h2>Sisestatud albumid:</h2>
			<ul>
				<?php // albumid lühike kuvamine
					$kask=$yhendus->prepare("SELECT id, albumi_pealkiri, aasta FROM m_albumid");
					$kask->bind_result($id, $albumi_pealkiri, $aasta);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($aasta)." ".htmlspecialchars($albumi_pealkiri)."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa album</a>
		</div>
		<div id="sisukiht">
			<?php
				if(isSet($_REQUEST["id"])){ // albumite detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT id, albumi_pealkiri, esitaja_id, aasta, zanr, plaadifirma_id, hinnang FROM m_albumid WHERE id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $albumi_pealkiri, $esitaja_id, $aasta, $zanr, $plaadifirma_id, $hinnang);
					$kask->execute();
					if($kask->fetch()){
						echo "Albumi pealkiri: ".htmlspecialchars($albumi_pealkiri)."</br>";
						echo "Esitaja: ".htmlspecialchars($esitaja_id)."</br>";
						echo "Aasta: ".htmlspecialchars($aasta)."</br>";
						echo "Zanr: ".htmlspecialchars($zanr)."</br>";
						echo "Plaadifirma: ".htmlspecialchars($plaadifirma_id)."</br>";
						echo "Hinnang: ".htmlspecialchars($hinnang)."</br>";
						
						echo "<br /><a href='?kustuta=$id'>Kustuta</a>"; //kustutamise link
						echo "<br /><a href='?muutmise_alustus_id=$id'>Muuda</a>"; //muutmise alustamise link

					}
					else {
						echo "Vigased andmed.";
					}
				} 
		 
				if(isSet($_REQUEST["muutmise_alustus_id"])){ // albumi andmete muutmine
					$kask=$yhendus->prepare("SELECT id, albumi_pealkiri, esitaja_id, aasta, zanr, plaadifirma_id, hinnang FROM m_albumid WHERE id=?");
					$kask->bind_param("i", $_REQUEST["muutmise_alustus_id"]); 
					$kask->bind_result($id, $albumi_pealkiri, $esitaja_id, $aasta, $zanr, $plaadifirma_id, $hinnang);
					$kask->execute();
					if($kask->fetch()){
						echo "<form action='?'>";
						echo "<input type='hidden' name='muutmise_salvestus_id' value='$id' />";
						echo "Albumi pealkiri:<br /> <input type='text' name='albumi_pealkiri' value='".
													htmlspecialchars($albumi_pealkiri)."' /><br />";
						echo "Esitaja:<br /> <input type='text' name='esitaja_id' value='".
													htmlspecialchars($esitaja_id)."' /><br />";
						echo "Aasta:<br /> <input type='text'  name='aasta' value='".
													htmlspecialchars($aasta)."' /><br />";
						echo "Zanr:<br /> <input type='text'  name='zanr' value='".
													htmlspecialchars($zanr)."' /><br />";
						echo "Plaadifirma:<br /> <input type='text'  name='plaadifirma_id' value='".
													htmlspecialchars($plaadifirma_id)."' /><br />";
						echo "Hinnang:<br /> <input type='text'  name='hinnang' value='".
													htmlspecialchars($hinnang)."' /><br />";
													
													
						echo "<input type='submit' name='salvestus_nupp' value='Salvesta' />";
						echo "<input type='submit' name='katkestus_nupp' value='Katkesta' />";
						echo "</form>";
					}
					else {
						echo "Vigased andmed.";
					}
				}
		 
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
											$esitajad=array("Vali esitaja", "Üllar Jörberg", "Michael Jackson", "Freddie Mercury", "Elton John", "Tina Turner");
											$valiku_nr=0;
											if(isSet($_REQUEST["esitaja_id"])){$valiku_nr=intval($_REQUEST["esitaja_id"]);} //intval teeb numbriks
											for($esitaja_nr=0; $esitaja_nr<count($esitajad); $esitaja_nr++){
												echo "<option value='$esitaja_nr' >$esitajad[$esitaja_nr]</option>\n";
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
									<select name="zanri_id">
										<?php
											$zanrid=array("folk", "hip-hop", "klassika", "metal", "pop");
											if(isSet($_REQUEST["zanri_id"])){$_REQUEST["zanri_id"];}
											for($zanri_nr=0; $zanri_nr<count($zanrid); $zanri_nr++){
												echo "<option value='$zanrid[$zanri_nr]' >$zanrid[$zanri_nr]</option>\n";
											}
										?> 
									</select><br>
								</dt>
								
								<dt>Plaadifirma (id):</dt>
								<dt>
									<select name="plaadifirma_id">
										<?php
											$firmad=array("Vali", "1", "2", "3", "4", "5", "6", "7", "8");
											$valiku_nr=0;
											if(isSet($_REQUEST["plaadifirma_id"])){$valiku_nr=intval($_REQUEST["plaadifirma_id"]);} //intval teeb numbriks
											for($firma_nr=0; $firma_nr<count($firmad); $firma_nr++){
												echo "<option value='$firma_nr' >$firmad[$firma_nr]</option>\n";
											}
										?> 
									</select><br>
								</dt>
								
								<dt>Hinnang:</dt>
								<dt>
									<select name="hinnangu_id">
										<?php
											$hinnangud=array("*", "* *", "* * *", "* * * *", "* * * * *");
											if(isSet($_REQUEST["hinnangu_id"])){$_REQUEST["hinnangu_id"];}
											for($hinnangu_nr=0; $hinnangu_nr<count($hinnangud); $hinnangu_nr++){
												echo "<option value='$hinnangud[$hinnangu_nr]' >$hinnangud[$hinnangu_nr]</option>\n";
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