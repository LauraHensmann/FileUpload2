<?php
if(isset($_POST['filename']) && !empty($_POST['filename'])) {
	$fileToDelete = $_POST['filename'];
	if(file_exists(dirname(__FILE__).'/uploads/'.$fileToDelete)) {
		unlink(dirname(__FILE__).'/uploads/'.$fileToDelete);
	}
}

header ("Location: index.php");
?>		