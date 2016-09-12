<?php
if(!$_FILES){
	header("Location: index.php");
	exit;
}

	$allowedExts = array("png", "jpg", "jpeg", "gif");
	$extension = strtolower(end(explode(".", $_FILES["file"]["name"]))); // nutzt den Dateinamen um die Dateiendung zu bestimmen. Um Fehler zu vermeiden, wird die Dateiendung mit strtolower zu Kleinbuchstaben umgewandelt.

	// set image name
	if(isset($_POST['imagename']) && !empty($_POST['imagename'])) { // Prüft ob Variable gesetzt und nicht leer ist. (! negiert die Anweisung)
		$imagename = mysql_real_escape_string($_POST['imagename']);
	} else {
		$imagename = mysql_real_escape_string($_FILES['file']['name']);
	}
	
		// $imagename = str_replace("/","_","$imagename");  // (FUNKTIONIERT wurde durch preg_match ersetzt)
				
		$pattern = '/([^\w\d._-])/i';
		$replace = '_';
	
		preg_match($pattern,$imagename);
		$imagename = preg_replace($pattern, $replace, $imagename);
		
	
	if (in_array($extension, $allowedExts)){	// prüft, ob sich die Dateiendung in dem Array der erlaubten Dateiendungen existiert.
		echo "Invalid file";
		header("Location: index.php");
		exit;
	}
	
	if ($_FILES["file"]["error"] > 0){		// prüft, ob der Upload-Vorgang fehlerfrei ausgeführt wurde.

		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";	// andernfalls wird eine Fehlermeldung ausgegeben.
		header("Location: index.php");
		exit;
	}
			
		echo "Upload: " . $_FILES["file"]["name"] . "<br />";
		echo "Type: " . $_FILES["file"]["type"] . "<br />";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
		echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
		//if (file_exists("upload/" . $imagename)){		// prüft, ob die Datei unter diesem Dateinamen bereits existiert.
		//	echo $_FILES["file"]["name"] . " already exists. ";
		//}else{
			//move_uploaded_file($_FILES["file"]["tmp_name"],
			//"upload/" . $_FILES["file"]["name"]);
		//	move_uploaded_file($_FILES["file"]["tmp_name"],"uploads/{$imagename}.{$extension}");	// verschiebt die hochgeladene Datei vom Temporären Ordner nach uploads/
		//	echo "Stored in: " . "uploads/" . $_FILES["file"]["name"];
		//	header ("Location: index.php");				// leitet den "Besucher" zurück auf die Startseite
		//}
			
		$deployOK = 0; $deployTries = 1;
		while($deployOK == 0){

		if(file_exists("uploads/" . $imagename.".".$extension)) {
				if(strpos($imagename, '_'.$deployTries) !== false) {  //Falls der Ordner bereits bereits "x" enthält, entferne und versuche erneut
					$imagename = rtrim($imagename,"_".$deployTries);
					$deployTries++;									// erhöht Anzahl der Speicherversuche
					$imagename = $imagename."_".$deployTries;		// setze _$Versuche hinter Bildname
				}else{						
					$imagename = $imagename."_".$deployTries;		// Falls erster Versuch setze _1 hinter Bildname
				}
				continue;
			} 
				
			$deployOK = 1;										//Falls noch nicht vorhanden, speichern
			move_uploaded_file($_FILES["file"]["tmp_name"],"uploads/{$imagename}.{$extension}");
			echo "Stored in: uploads/". $_FILES["file"]["name"];
			header("Location: index.php");
		}
?>