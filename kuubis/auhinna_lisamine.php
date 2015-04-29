<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusAuhind"])){ //uue albumi salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_auhinnad
			(auhind, saaja_id, kommentaar) VALUES (?, ?, ?)");
		$kask->bind_param("sis", $_REQUEST["auhind"], $_REQUEST["saaja_id"], $_REQUEST["kommentaar"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
  
	if(isSet($_REQUEST["salvestus_nupp"])){ // muutuste salvestamine
		$kask=$yhendus->prepare("UPDATE m_auhinnad SET auhind=?, saaja_id=?, kommentaar=? WHERE id=?");
		$kask->bind_param("sisi",
			$_REQUEST["auhind"], $_REQUEST["saaja_id"],  $_REQUEST["kommentaar"], $_REQUEST[muutmise_salvestus_id]); //tänu muutmise_salvestus_id'le teab, millist lehe muutused salvestada
		$kask->execute();
	}
?>

<!doctype html>
<html>
	<head>
		<title>Auhinnad</title>
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
					echo "Auhind salvestatud!";
				}
			?>
			<h2>Sisestatud auhinnad:</h2>
			<ul>
				<?php // auhindade lühike kuvamine
					$kask=$yhendus->prepare("SELECT id, auhind, saaja_id, kommentaar FROM m_auhinnad");
					$kask->bind_result($id, $auhind, $saaja_id, $kommentaar);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($auhind)."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa auhind</a>
		</div>
		<div id="sisukiht">
			<?php
				if(isSet($_REQUEST["id"])){ // auhindade detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT id, auhind, saaja_id, kommentaar FROM m_auhinnad WHERE id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $auhind, $saaja_id, $kommentaar);
					$kask->execute();
					if($kask->fetch()){
						echo "Auhinna nimetus: ".htmlspecialchars($auhind)."</br>";
						echo "Saaja: ".htmlspecialchars($saaja_id)."</br>";
						echo "Kommentaar: ".htmlspecialchars($kommentaar)."</br>";
						
						echo "<br /><a href='?muutmise_alustus_id=$id'>Muuda</a>"; //muutmise alustamise link

					}
					else {
						echo "Vigased andmed.";
					}
				} 
		 
				if(isSet($_REQUEST["muutmise_alustus_id"])){ // auhinna andmete muutmine
					$kask=$yhendus->prepare("SELECT id, auhind, saaja_id, kommentaar FROM m_auhinnad WHERE id=?");
					$kask->bind_param("i", $_REQUEST["muutmise_alustus_id"]); 
					$kask->bind_result($id, $auhind, $saaja_id, $kommentaar);
					$kask->execute();
					if($kask->fetch()){
						echo "<form action='?'>";
						echo "<input type='hidden' name='muutmise_salvestus_id' value='$id' />";
						echo "Auhinna nimetus:<br /> <input type='text' name='auhind' value='".
													htmlspecialchars($auhind)."' /><br />";
						echo "Saaja:<br /> <input type='text' name='saaja_id' value='".
													htmlspecialchars($saaja_id)."' /><br />";
						echo "Kommentaar:<br /> <input type='text'  name='kommentaar' value='".
													htmlspecialchars($kommentaar)."' /><br />";
						echo "<input type='submit' name='salvestus_nupp' value='Salvesta' />";
						echo "<input type='submit' name='katkestus_nupp' value='Katkesta' />";
						echo "</form>";
					}
					else {
						echo "Vigased andmed.";
					}
				}
		 
				if(isSet($_REQUEST["uus"])){ // uue auhinna sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusAuhind" value="jah" />
							<h2>Auhinna lisamine</h2>
							<dl>
								<dt>Auhinna nimetus:</dt>
								<dt>
									<input type="text" name="auhind" />
								</dt>
								
								<dt>Saaja:</dt>
								<dt>
									<select name="saaja_id">
										<?php
											$esitajad=array("Vali saaja", "Üllar Jörberg", "Michael Jackson", "Freddie Mercury", "Elton John", "Tina Turner");
											$valiku_nr=0;
											if(isSet($_REQUEST["saaja_id"])){$valiku_nr=intval($_REQUEST["saaja_id"]);} //intval teeb numbriks
											for($esitaja_nr=0; $esitaja_nr<count($esitajad); $esitaja_nr++){
												echo "<option value='$esitaja_nr' >$esitajad[$esitaja_nr]</option>\n";
											}
										?> 
									</select><br>
								</dt>
								
								<dt>Kommentaar:</dt>
								<dt>
									<textarea rows="10" name="kommentaar"></textarea><br>
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