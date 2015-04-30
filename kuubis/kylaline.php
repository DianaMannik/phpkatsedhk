<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
	
	if(isSet($_REQUEST["uus_kommentaar"])){ //kommentaari lisamine
		$kask=$yhendus->prepare(
			"INSERT INTO m_kommentaarid (albumi_id, kommentaar, kasutajanimi) VALUES (?, ?, ?)");
		$kommentaarilisa="\n".$_REQUEST["kommentaar"]." ".date('Y-m-d H:i:s')."\n";
		$kask->bind_param("iss", $_REQUEST["albumi_id"], $kommentaarilisa,  $_REQUEST["kasutajanimi"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]");
		$yhendus->close();
		exit();
	}
?>

<!doctype html>
<html>
	<head>
		<title>Külastaja</title>
		<style type="text/css">
			#sisukiht{
				margin-left: 50px;
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
		<h2>Tere tulemast muusikahurmuri Hr Svenssoni külaliste lehele!</h2></br>
		<ul>
			<?php
				$kask=$yhendus->prepare("SELECT albumi_id, kommentaar, kasutajanimi FROM m_kommentaarid");
				$kask->bind_result($albumi_id, $kommentaar, $kasutajanimi);
				$kask->execute();
			?>
        </ul>
		<div id="sisukiht">
			<h3>Palun jätke oma kommentaar mõnele albumile:</h3>
				<form action='?'>
					<input type="hidden" name="uus_kommentaar" value="jah" />
					<div id="tabel">
						<td>Kasutajanimi:</td><br>
						<td>
							<input type="text" name="kasutajanimi" /><br>
						</td>
					
						<td>Vali album:</td><br>
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
						
						<td>Teie kommentaar:</td><br>
						<td>
							<textarea rows="10" name="kommentaar"></textarea><br>
						</td>
					</div>
					<input type="submit" value="Sisesta kommentaar">
				</form>
				
				<?php
				if(isSet($_REQUEST["vaata"])){ // kommentaaride vaatamine
				?>
						<h3>Teiste kommentaarid:</h3>
						<?php
							$kask=$yhendus->prepare(
								"SELECT id, albumi_id, kommentaar, kasutajanimi FROM m_kommentaarid");
							$kask->bind_result($id, $albumi_id, $kommentaar, $kasutajanimi);
							$kask->execute();
							while($kask->fetch()){
								$albumi_id=htmlspecialchars($albumi_id);
								$kasutajanimi=nl2br(htmlspecialchars($kasutajanimi));
								$kommentaar=nl2br(htmlspecialchars($kommentaar));
								echo "<tr>
								  <td>$albumi_id</td>
								  <td>$kasutajanimi</td>
								  <td>$kommentaar</td>
								</tr>";
							}

				}
				?>
				
		</div>
		<div id="jalusekiht">
			<br><br><br><a href='?vaata=jah'>Vaata teiste jäetud kommentaare</a>
			<br><br><br><a href="login.php">Tagasi</a>
		</div>
	</body>
</html>
<?php
	$yhendus->close();
?>