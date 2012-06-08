<?php

########################################################################
# Extension Manager/Repository config file for ext "svmetaextract".
#
# Auto generated 08-06-2012 12:06
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'DAM metadata extraction services',
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
	'version' => '1.1.0dev',
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
	'_md5_values_when_last_written' => 'a:13:{s:9:"ChangeLog";s:4:"0659";s:16:"ext_autoload.php";s:4:"9dc5";s:12:"ext_icon.gif";s:4:"999b";s:14:"ext_tables.php";s:4:"aeea";s:14:"doc/manual.sxw";s:4:"5387";s:19:"doc/wizard_form.dat";s:4:"280a";s:20:"doc/wizard_form.html";s:4:"5c66";s:34:"lib/class.tx_svmetaextract_lib.php";s:4:"6b32";s:34:"sv1/class.tx_svmetaextract_sv1.php";s:4:"3755";s:34:"sv2/class.tx_svmetaextract_sv2.php";s:4:"9890";s:34:"sv3/class.tx_svmetaextract_sv3.php";s:4:"d1bd";s:34:"sv4/class.tx_svmetaextract_sv4.php";s:4:"2b13";s:34:"sv5/class.tx_svmetaextract_sv5.php";s:4:"8232";}',
	'suggests' => array(
	),
);

?>