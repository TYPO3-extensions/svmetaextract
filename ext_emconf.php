<?php

########################################################################
# Extension Manager/Repository config file for ext "svmetaextract".
#
# Auto generated 04-10-2011 17:07
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'DAM Metadata extraction services',
	'description' => 'Services to extract metadata from files.',
	'category' => 'services',
	'author' => 'The DAM development team',
	'author_email' => 'typo3-project-dam@lists.netfielders.de',
	'shy' => '',
	'dependencies' => 'static_info_tables',
	'conflicts' => 'cc_metaexif,cc_metaexec,cc_meta_xmp',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.0dev',
	'constraints' => array(
		'depends' => array(
			'static_info_tables' => '',
		),
		'conflicts' => array(
			'cc_metaexif' => '',
			'cc_metaexec' => '',
			'cc_meta_xmp' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:12:{s:9:"ChangeLog";s:4:"cc1c";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"999b";s:14:"ext_tables.php";s:4:"d4e6";s:19:"doc/wizard_form.dat";s:4:"280a";s:20:"doc/wizard_form.html";s:4:"5c66";s:34:"lib/class.tx_svmetaextract_lib.php";s:4:"7fc0";s:34:"sv1/class.tx_svmetaextract_sv1.php";s:4:"5702";s:34:"sv2/class.tx_svmetaextract_sv2.php";s:4:"f7e7";s:34:"sv3/class.tx_svmetaextract_sv3.php";s:4:"5b8b";s:34:"sv4/class.tx_svmetaextract_sv4.php";s:4:"1668";s:34:"sv5/class.tx_svmetaextract_sv5.php";s:4:"6b7b";}',
	'suggests' => array(
	),
);

?>