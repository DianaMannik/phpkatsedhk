<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusVideo"])){ //uue video salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_videod
			(laulu_id, link) VALUES (?, ?)");
		$kask->bind_param("is", $_REQUEST["laulu_id"], $_REQUEST["link"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
	
	if(isSet($_REQUEST["kustuta"])){ //video kustutamine
		$kask=$yhendus->prepare("DELETE FROM m_videod WHERE id=?");
		$kask->bind_param("i", $_REQUEST["kustuta"]);
		$kask->execute();    
	}
  
	if(isSet($_REQUEST["salvestus_nupp"])){ // muutuste salvestamine
		$kask=$yhendus->prepare("UPDATE m_videod SET laulu_id=?, link=? WHERE id=?");
		$kask->bind_param("isi",  $_REQUEST["laulu_id"], $_REQUEST["link"], $_REQUEST[muutmise_salvestus_id]); //tänu muutmise_salvestus_id'le teab, millist lehe muutused salvestada
		$kask->execute();
	}
	
?>

<!doctype html>
<html>
	<head>
		<title>Videod</title>
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
					echo "Video lisatud!";
				}
			?>
			<h2>Sisestatud videod:</h2>
			<ul>
				<?php // videode lühike kuvamine
					$kask=$yhendus->prepare("SELECT m_videod.id, laulu_id, laulu_nimi FROM m_videod JOIN m_laulud ON m_videod.laulu_id=m_laulud.id");
					$kask->bind_result($id, $laulu_id, $laulu_nimi);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($laulu_nimi)."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa video</a>
		</div>
		<div id="sisukiht">
			<?php
				$urlAlgus = htmlspecialchars('https://www.youtube.com/embed/');
				if(isSet($_REQUEST["id"])){ // videode detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT id, link FROM m_videod WHERE id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $link);
					$kask->execute();
					if($kask->fetch()){
						$videoKuva = htmlspecialchars($urlAlgus.$link);
						?>
							<!--<iframe width="420" height="315" src= frameborder="0" allowfullscreen></iframe> -->
						<?php
						
						echo "Video: <br /><a href='$videoKuva'>".$videoKuva. "</a></br>";

						echo "<br /><a href='?kustuta=$id'>Kustuta</a>"; //kustutamise link
						echo "<br /><a href='?muutmise_alustus_id=$id'>Muuda</a>"; //muutmise alustamise link

					}
					else {
						echo "Vigased andmed.";
					}
				} 
		 
				if(isSet($_REQUEST["muutmise_alustus_id"])){ // video andmete muutmine
					$kask=$yhendus->prepare("SELECT id, laulu_id, link FROM m_videod WHERE id=?");
					$kask->bind_param("i", $_REQUEST["muutmise_alustus_id"]); 
					$kask->bind_result($id, $laulu_id, $link);
					$kask->execute();
					if($kask->fetch()){
						echo "<form action='?'>";
						echo "<input type='hidden' name='muutmise_salvestus_id' value='$id' />";
						echo "Laulu pealkiri (id):<br /> <input type='text' name='laulu_id' value='".
													htmlspecialchars($laulu_id)."' /><br />";
						echo "Video link:<br /> <input type='text' name='link' value='".
													htmlspecialchars($link)."' /><br />";
						//echo "Esitaja (id):<br /> <input type='text' name='esitaja_id' value='".
													//htmlspecialchars($esitaja_id)."' /><br />";
						//echo "Album (id):<br /> <input type='text'  name='albumi_id' value='".
													//htmlspecialchars($albumi_id)."' /><br />";
						//echo "Helilooja:<br /> <input type='text'  name='helilooja' value='".
													//htmlspecialchars($helilooja)."' /><br />";
						//echo "Sõnade autor:<br /> <input type='text'  name='sonade_autor' value='".
													//htmlspecialchars($sonade_autor)."' /><br />";
													
						echo "<input type='submit' name='salvestus_nupp' value='Salvesta' />";
						echo "<input type='submit' name='katkestus_nupp' value='Katkesta' />";
						echo "</form>";
					}
					else {
						echo "Vigased andmed.";
					}
				}
		 
				if(isSet($_REQUEST["uus"])){ // uue video sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusVideo" value="jah" />
							<h2>Video lisamine</h2>
							<dl>
								
								<dt>Laulu pealkiri:</dt>
								<dt>
									<select name="laulu_id">
										<?php
											$kask=$yhendus->prepare("SELECT id, laulu_nimi FROM m_laulud");
											$kask->bind_result($id, $laulu_nimi);
											$kask->execute();
											echo $yhendus->error;
											while($kask->fetch()){
												echo "<option value='$id' >$laulu_nimi</option>\n";
											}
										?>
									</select><br>
								</dt>
								
								<dt>Video link:</dt>
								<dt>
									<input type="text" name="link" />
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