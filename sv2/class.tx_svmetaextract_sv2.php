<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Service 'EXIF extraction' for the 'cc_metaexif' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */


require_once(PATH_t3lib.'class.t3lib_svbase.php');

class tx_svmetaextract_sv2 extends t3lib_svbase {
	var $prefixId = 'tx_svmetaextract_sv2';		// Same as class name
	var $extKey = 'svmetaextract';	// The extension key.


	/**
	 * performs the service processing
	 *
	 * @param	string 	Content which should be processed.
	 * @param	string 	Content type
	 * @param	array 	Configuration array
	 * @return	boolean
	 */
	function process($content='', $type='', $conf=array())	{

		$this->conf = $conf;

		$this->out = array();
		$this->out['fields'] = array();
		$this->exif = array();


		if ($content) {
			$this->setInput ($content, $type);
		}

		if($inputFile = $this->getInputFile()) {

			// Parameter: filename (string),sections(string),arrays(bool),thumbnail(bool)
			$exifdata = @exif_read_data($inputFile,'',true,false);
			$exif = $exifdata['EXIF'];
			$exif = is_array($exif) ? $exif : array();
			if(is_array($exifdata['COMMENT'])) {
				$exif['fields']['description'] = tx_svmetaextract_lib::forceUtf8(implode("\n",$exifdata['COMMENT']));
			}
			if(is_array($exifdata['IFD0'])) {
				$exif = array_merge($exifdata['IFD0'],$exif);
			}

			foreach ($exif as $name => $value) {

				if (!is_array($value)) {

					if (trim($value)) { // ignore empty lines headers and empty entries

						$name=str_replace('-','',$name);

							// add to exif table
						$this->exif[$name] = $value;

							// add to DAM table
						switch ($name) {
							case 'ImageCreated':
							case 'DateTimeOriginal':
								$parsedDate = tx_svmetaextract_lib::parseDate($value);
								$this->out['fields']['date_cr'] = $parsedDate;
								$this->out['fields']['date_mod'] = $parsedDate;
								$this->out['fields']['file_ctime'] = $parsedDate;
								$this->out['fields']['file_mtime'] = $parsedDate;
							break;
							case 'HorizontalResolution':
								$this->out['fields']['hres'] = intval($value);
							break;
							case 'VerticalResolution':
								$this->out['fields']['vres'] = intval($value);
							break;
	// I removed this because the built-in 'identify' will recognize 'RGB' and sRGB is a color profile
	//						case 'ColorSpace':
	//						case 'ColorSpaceInformation':
	//							if (!$this->out['fields']['color_space']) {
	//								$this->out['fields']['color_space'] = ($value==1)?'sRGB':$value;
	//							}
							break;
							case 'Copyright':
								$this->out['fields']['copyright'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'Artist':
								$this->out['fields']['creator'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'Software':
							case 'CameraModel':
							case 'Model':
								$this->out['fields']['file_creator'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'Title':
								$this->out['fields']['title'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'ImageDescription':
								$this->out['fields']['description'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
						}
					}
				}
			}

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/sv2/class.tx_svmetaextract_sv2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/sv2/class.tx_svmetaextract_sv2.php']);
}

?>