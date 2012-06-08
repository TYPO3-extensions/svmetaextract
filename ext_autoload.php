<?php
$extensionClassesPath = t3lib_extMgm::extPath('svmetaextract');
return array(
	't3lib_svbase' => PATH_t3lib . 'class.t3lib_svbase.php',
	'tx_svmetaextract_lib' => $extensionClassesPath . 'lib/class.tx_svmetaextract_lib.php',
);
?>