<?php
 $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
// always load alternative config file for examples
require_once("$rootDir/config/tcpdf_config_alt.php");

// Include the main TCPDF library (search the library on the following directories).
$tcpdf_include_dirs = array(
	realpath('tcpdf.php'),
	"$rootDir/config/tcpdf.php"
);
foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
	if (@file_exists($tcpdf_include_path)) {
		require_once($tcpdf_include_path);
		break;
	}
}

//============================================================+
// END OF FILE
//============================================================+
