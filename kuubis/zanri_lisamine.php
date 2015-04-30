<?php
	$yhendus=new mysqli("localhost", "elisaraeltonnov", "3PF71ojx", "elisaraeltonnov");
  
	if(isSet($_REQUEST["uusZanr"])){ //uue zanri salvestamine andmebaasi
		$kask=$yhendus->prepare("INSERT INTO m_zanrid
			(nimetus) VALUES (?)");
		$kask->bind_param("s", $_REQUEST["nimetus"]);
		$kask->execute();
		header("Location: $_SERVER[PHP_SELF]?teade=salvestatud");
		$yhendus->close();
		exit();
	}
	
?>

<!doctype html>
<html>
	<head>
		<title>Zanrid</title>
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
					echo "Zanr lisatud!";
				}
			?>
			<h2>Sisestatud:</h2>
			<ul>
				<?php // kuvamine
					$kask=$yhendus->prepare("SELECT nimetus FROM m_zanrid");
					$kask->bind_result($nimetus);
					$kask->execute();
					while($kask->fetch()){
						echo "<li>".htmlspecialchars($nimetus)."</li>";
					}
				?>
			</ul>
			<a href='?uus=jah'>Lisa</a>
		</div>
		<div id="sisukiht">		
			<?php		 
				if(isSet($_REQUEST["uus"])){ // uue zanri sisestamine
					?>
						<form action='?'>
							<input type="hidden" name="uusZanr" value="jah" />
							<h2>Zanri lisamine</h2>
							<dl>
								<dt>
									<input type="text" name="nimetus" />
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