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

function autolink($string){
    // force http: on www.
    $string = str_ireplace( "www.", "http://www.", $string );
    // eliminate duplicates after force
    $string = str_ireplace( "http://http://www.", "http://www.", $string );
    $string = str_ireplace( "https://http://www.", "https://www.", $string );

    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    // Check if there is a url in the text

$m = preg_match_all($reg_exUrl, $string, $match); 

if ($m) { 
$links=$match[0]; 
for ($j=0;$j<$m;$j++) { 

    if(substr($links[$j], 0, 18) == 'http://www.youtube'){

    $string=str_replace($links[$j],'<a href="'.$links[$j].'" rel="nofollow" target="_blank">'.$links[$j].'</a>',$string).'<br /><iframe title="YouTube video player" class="youtube-player" type="text/html" width="320" height="185" src="http://www.youtube.com/embed/'.substr($links[$j], -11).'" frameborder="0" allowFullScreen></iframe><br />';


    }else{

    $string=str_replace($links[$j],'<a href="'.$links[$j].'" rel="nofollow" target="_blank">'.$links[$j].'</a>',$string);

        } 

    } 
} 




               return ($string);
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
					$kask=$yhendus->prepare("SELECT id, laulu_id FROM m_videod");
					$kask->bind_result($id, $laulu_id);
					$kask->execute();
					while($kask->fetch()){
						echo "<li><a href='?id=$id'>".
							htmlspecialchars($laulu_id)."</a></li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa video</a>
		</div>
		<div id="sisukiht">		
			<?php
				if(isSet($_REQUEST["id"])){ // videode detailne kuvamine pärast peale klikkimist
					$kask=$yhendus->prepare("SELECT id, laulu_id, link FROM m_videod WHERE id=?");
					$kask->bind_param("i", $_REQUEST["id"]);
					$kask->bind_result($id, $laulu_id, $link);
					$kask->execute();
					if($kask->fetch()){
						echo "Laulu pealkiri (id): ".htmlspecialchars($laulu_id)."</br>";
						echo "Video: ".htmlspecialchars($link)."</br>";
						
						//echo "Esitaja (id): ".htmlspecialchars($esitaja_id)."</br>";
						//echo "Album (id): ".htmlspecialchars($albumi_id)."</br>";
						//echo "Helilooja: ".htmlspecialchars($helilooja)."</br>";
						//echo "Sõnade autor: ".htmlspecialchars($sonade_autor)."</br>";-->

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
								
								<dt>Laulu pealkiri (id):</dt>
								<dt>
									<select name="laulu_id">
										<?php
											$laulud=array("Smooth Criminal", "Toru-Jüri", "The Best");
											$valiku_nr=1;
											if(isSet($_REQUEST["laulu_id"])){$valiku_nr=intval($_REQUEST["laulu_id"]);} //intval teeb numbriks
											for($laulu_nr=0; $laulu_nr<count($laulud); $laulu_nr++){
												echo "<option value='$laulu_nr' >$laulud[$laulu_nr]</option>\n";
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