<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addService($_EXTKEY,  'metaExtract',  'tx_svmetaextract_sv1',
		array(

			'title' => 'IPTC extraction',
			'description' => 'Get IPTC data from files by PHP function "iptcparse".',

			'subtype' => 'image:iptc',

			'available' => function_exists('getimagesize') AND function_exists('iptcparse'),
			'priority' => 60,
			'quality' => 50,

			'os' => '',
			'exec' => '',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv1/class.tx_svmetaextract_sv1.php',
			'className' => 'tx_svmetaextract_sv1',
		)
	);

t3lib_extMgm::addService($_EXTKEY,  'metaExtract',  'tx_svmetaextract_sv2',
		array(

			'title' => 'EXIF extraction',
			'description' => 'Extract EXIF data from images by PHP function "exif_read_data".',

			'subtype' => 'image:exif',

			'available' => function_exists('exif_read_data'),
			'priority' => 60,
			'quality' => 50,

			'os' => '',
			'exec' => '',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv2/class.tx_svmetaextract_sv2.php',
			'className' => 'tx_svmetaextract_sv2',
		)
	);

t3lib_extMgm::addService($_EXTKEY,  'metaExtract',  'tx_svmetaextract_sv3',
		array(

			'title' => 'EXIF extraction',
			'description' => 'Extract EXIF data from images using external program "exiftags".',

			'subtype' => 'image:exif',

			'available' => TRUE,
			'priority' => 50,
			'quality' => 50,

			'os' => '',
			'exec' => 'exiftags',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv3/class.tx_svmetaextract_sv3.php',
			'className' => 'tx_svmetaextract_sv3',
		)
	);

t3lib_extMgm::addService($_EXTKEY,  'metaExtract', 'tx_svmetaextract_sv4',
		array(

			'title' => 'EXIF/IPTC/XMP extraction',
			'description' => 'Extract EXIF/IPTC/XMP data from files using external program "exiftool".',

			'subtype' => 'image:exif, image:iptc, '.
							'acr, ai, aiff, aif, aifc, ape, arw, asf, avi, bmp, dib, cr2, crw, ciff, dcm, '.
							'dc3, dic, dicm, dng, doc, erf, flac, fpx, gif, html, htm, xhtml, icc, icm, jp2, '.
							'jpx, jpeg, jpg, m4a, mie, miff, mif, mos, mov, qt, mp3, mp4, mpc, mpeg, mpg, '.
							'mrw, nef, ogg, orf, pdf, pef, pict, pct, png, jng, mng, ppm, pbm, pgm, ppt, '.
							'ps, eps, epsf, psd, qtif, qti, qif, ra, raf, ram, rpm, raw, raw, riff, rif, '.
							'rm, rv, rmvb, sr2, srf, swf, thm, tiff, tif, vrd, wav, wdp, wma, wmv, x3f, xls, xmp',

			'available' => TRUE,
			'priority' => 60,
			'quality' => 60,

			'os' => '',
			'exec' => 'exiftool',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv4/class.tx_svmetaextract_sv4.php',
			'className' => 'tx_svmetaextract_sv4',
		)
	);

t3lib_extMgm::addService($_EXTKEY,  'metaExtract',  'tx_svmetaextract_sv5',
		array(

			'title' => 'PDF meta extraction',
			'description' => 'Extract meta data from PDF files using external program "pdfinfo".',

			'subtype' => 'pdf',

			'available' => TRUE,
			'priority' => 50,
			'quality' => 50,

			'os' => '',
			'exec' => 'pdfinfo',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv5/class.tx_svmetaextract_sv5.php',
			'className' => 'tx_svmetaextract_sv5',
		)
	);
	
t3lib_extMgm::addService($_EXTKEY,  'metaExtract',  'tx_svmetaextract_sv6',
		array(

			'title' => 'XMP meta extraction',
			'description' => 'Extract XMP meta data from jpg files using PHP.',

			'subtype' => 'jpeg, jpg',

			'available' => TRUE,
			'priority' => 60,
			'quality' => 50,

			'os' => '',
			'exec' => '',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv6/class.tx_svmetaextract_sv6.php',
			'className' => 'tx_svmetaextract_sv6',
		)
	);
?>