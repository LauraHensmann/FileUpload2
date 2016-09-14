<?php

if (!$_FILES['file']['name']){
	$error2 = 1;	
	header("Location: index.php?error2=".$error2);
	exit();
}

function checkFile(){
	if (!exif_imagetype($_FILES['file']['tmp_name'])){ // liest die ersten bytes des Bildes aus und überprüft die Signatur -> gibt "false" aus wenn nicht
		$error = 1;
		
		header("Location: index.php?error=".$error);
		exit();
	}
}

function checkFileExtension() {
	$allowedExts = array("png", "jpg", "jpeg", "gif");
	$extension = strtolower(end(explode(".", $_FILES["file"]["name"]))); // nutzt den Dateinamen um die Dateiendung zu bestimmen. Um Fehler zu vermeiden, wird die Dateiendung mit strtolower zu Kleinbuchstaben umgewandelt.
	// $imagename = str_replace("/","_","$imagename");  // (FUNKTIONIERT wurde durch preg_match ersetzt)
			
	$pattern = '/([^\w\d._-])/i';
	$replace = '_';

	preg_match($pattern,$imagename);
	$imagename = preg_replace($pattern, $replace, $imagename);
	
	if (in_array($extension, $allowedExts)){	// prüft, ob sich die Dateiendung in dem Array der erlaubten Dateiendungen existiert.
		if ($_FILES["file"]["error"] > 0){		// prüft, ob der Upload-Vorgang fehlerfrei ausgeführt wurde.
			echo "Return Code: " . $_FILES["file"]["error"] . "<br />";	// andernfalls wird eine Fehlermeldung ausgegeben.
		}
	}
	return $extension;
}

// set image name
if(isset($_POST['imagename']) && !empty($_POST['imagename'])) { // Prüft ob Variable gesetzt und nicht leer ist. (! negiert die Anweisung)
	$imagename = escapeshellcmd($_POST['imagename']);
} else {
	$imagename = escapeshellcmd($_FILES['file']['name']);
}
	
$extension = checkFileExtension(); // eingentlich nicht nötig
checkFile();

echo "Upload: " . $_FILES["file"]["name"] . "<br />";
echo "Type: " . $_FILES["file"]["type"] . "<br />";
echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
	
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