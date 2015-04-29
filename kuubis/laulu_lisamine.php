<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusLaul"])){ //uue laulu salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_laulud
			(laulu_nimi, esitaja_id, albumi_id, helilooja, sonade_autor) VALUES (?, ?, ?, ?, ?)");
		$kask->bind_param("siiss", $_REQUEST["laulu_nimi"], $_REQUEST["esitaja_id"], $_REQUEST["albumi_id"],
		$_REQUEST["helilooja"], $_REQUEST["sonade_autord"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
	
	if(isSet($_REQUEST["kustuta"])){ //laulu kustutamine
		$kask=$yhendus->prepare("DELETE FROM m_laulud WHERE id=?");
		$kask->bind_param("i", $_REQUEST["kustuta"]);
		$kask->execute();    
	}
  
	if(isSet($_REQUEST["salvestus_nupp"])){ // muutuste salvestamine
		$kask=$yhendus->prepare("UPDATE m_laulud SET laulu_nimi=?, esitaja_id=?, albumi_id=?, helilooja=?, sonade_autor=? WHERE id=?");
		$kask->bind_param("siissi",  $_REQUEST["laulu_nimi"], $_REQUEST["esitaja_id"], $_REQUEST["albumi_id"],
		$_REQUEST["helilooja"], $_REQUEST["sonade_autor"], $_REQUEST[muutmise_salvestus_id]); //tänu muutmise_salvestus_id'le teab, millist lehe muutused salvestada
		$kask->execute();
	}
?>

<!doctype html>
<html>
	<head>
		<title>Laulud</title>
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
					echo "Laul lisatud!";
				}
			?>
			<h2>Sisestatud laulud:</h2>
			<ul>
				<?php // laulude lühike kuvamine
					$kask=$yhendus->prepare("SELECT id, laulu_nimi FROM m_laulud");
					$kask->bind_result($id, $laulu_nimi);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($laulu_nimi)."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa laul</a>
		</div>
		<div id="sisukiht">
			<?php
				if(isSet($_REQUEST["id"])){ // laulude detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT id, laulu_nimi, esitaja_id, albumi_id, helilooja, sonade_autor FROM m_laulud WHERE id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $laulu_nimi, $esitaja_id, $albumi_id, $helilooja, $sonade_autor);
					$kask->execute();
					if($kask->fetch()){
						echo "Laulu pealkiri: ".htmlspecialchars($laulu_nimi)."</br>";
						echo "Esitaja (id): ".htmlspecialchars($esitaja_id)."</br>";
						echo "Album (id): ".htmlspecialchars($albumi_id)."</br>";
						echo "Helilooja: ".htmlspecialchars($helilooja)."</br>";
						echo "Sõnade autor: ".htmlspecialchars($sonade_autor)."</br>";

						echo "<br /><a href='?kustuta=$id'>Kustuta</a>"; //kustutamise link
						echo "<br /><a href='?muutmise_alustus_id=$id'>Muuda</a>"; //muutmise alustamise link

					}
					else {
						echo "Vigased andmed.";
					}
				} 
		 
				if(isSet($_REQUEST["muutmise_alustus_id"])){ // laulu andmete muutmine
					$kask=$yhendus->prepare("SELECT id, laulu_nimi, esitaja_id, albumi_id, helilooja, sonade_autor FROM m_laulud WHERE id=?");
					$kask->bind_param("i", $_REQUEST["muutmise_alustus_id"]); 
					$kask->bind_result($id, $laulu_nimi, $esitaja_id, $albumi_id, $helilooja, $sonade_autor);
					$kask->execute();
					if($kask->fetch()){
						echo "<form action='?'>";
						echo "<input type='hidden' name='muutmise_salvestus_id' value='$id' />";
						echo "Laulu pealkiri:<br /> <input type='text' name='laulu_nimi' value='".
													htmlspecialchars($laulu_nimi)."' /><br />";
						echo "Esitaja (id):<br /> <input type='text' name='esitaja_id' value='".
													htmlspecialchars($esitaja_id)."' /><br />";
						echo "Album (id):<br /> <input type='text'  name='albumi_id' value='".
													htmlspecialchars($albumi_id)."' /><br />";
						echo "Helilooja:<br /> <input type='text'  name='helilooja' value='".
													htmlspecialchars($helilooja)."' /><br />";
						echo "Sõnade autor:<br /> <input type='text'  name='sonade_autor' value='".
													htmlspecialchars($sonade_autor)."' /><br />";
													
						echo "<input type='submit' name='salvestus_nupp' value='Salvesta' />";
						echo "<input type='submit' name='katkestus_nupp' value='Katkesta' />";
						echo "</form>";
					}
					else {
						echo "Vigased andmed.";
					}
				}
		 
				if(isSet($_REQUEST["uus"])){ // uue laulu sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusAlbum" value="jah" />
							<h2>Laulu lisamine</h2>
							<dl>
								<dt>Laulu pealkiri:</dt>
								<dt>
									<input type="text" name="laulu_nimi" />
								</dt>
								
								<dt>Esitaja:</dt>
								<dt>
									<select name="esitaja_id">
										<?php
											$esitajad=array("Vali", "Üllar Jörberg", "Michael Jackson", "Freddie Mercury", "Elton John", "Tina Turner");
											$valiku_nr=0;
											if(isSet($_REQUEST["esitaja_id"])){$valiku_nr=intval($_REQUEST["esitaja_id"]);} //intval teeb numbriks
											for($esitaja_nr=0; $esitaja_nr<count($esitajad); $esitaja_nr++){
												echo "<option value='$esitaja_nr' >$esitajad[$esitaja_nr]</option>\n";
											}
										?> 
									</select><br>
								</dt>
								
								<dt>Vali album:</dt>
								<dt>
									<select name="albumi_id">
										<?php
											$albumid=array("Vali album", "Õnnelootus", "Bad", "The Road to El Dorado", "Foreign Affair");
											$valiku_nr=0;
											if(isSet($_REQUEST["albumi_id"])){$valiku_nr=intval($_REQUEST["albumi_id"]);} //intval teeb numbriks
											for($albumi_nr=0; $albumi_nr<count($albumid); $albumi_nr++){
												echo "<option value='$albumi_nr' >$albumid[$albumi_nr]</option>\n";
											}
										?> 
									</select><br>
								</dt>
								
								<dt>Helilooja:</dt>
								<dt>
									<input type="text" name="helilooja" />
								</dt>
								
								<dt>Sõnade autor:</dt>
								<dt>
									<input type="text" name="sonade_autor" />
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