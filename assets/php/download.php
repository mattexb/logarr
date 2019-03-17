<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('functions.php');
include("auth_check.php");

$file = $_GET['file'];

// check if log file exists in config.json:

if (in_array_recursive($file, $logs)) {
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . basename($file) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		//ob_clean();
		flush();
		readfile($file);
		appendLog(
			$logentry = "Downloading log file: " . $file
		);
		exit;
	} else {
		echo 'file: ' . $file . ' does not exist.';

		echo "<script type='text/javascript'>";
		echo "console.log('ERROR: file: '" . $file . "' does not exist.');";
		echo "</script>";

		appendLog(
			$logentry = "ERROR: Downloading log file: " . $file . " does NOT exist"
		);
	}
} // Deny access if log file does NOT exist in config.json:

else {
	echo 'ERROR: Illegal File';

	echo "<script type='text/javascript'>";
	echo "console.log('ERROR:  Illegal File');";
	echo "</script>";

	appendLog(
		$logentry = "ERROR: Downloading log file: Illegal File"
	);
}

