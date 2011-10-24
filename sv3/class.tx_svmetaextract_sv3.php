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
 * Service 'EXIF extraction (exiftags)' for the 'cc_metaexif' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */


require_once(PATH_t3lib.'class.t3lib_svbase.php');
require_once(t3lib_extMgm::extPath('sv_metaextract').'lib/class.tx_svmetaextract_lib.php');

class tx_svmetaextract_sv3 extends t3lib_svbase {
	var $prefixId = 'tx_svmetaextract_sv3';		// Same as class name
	var $extKey = 'sv_metaextract';	// The extension key.


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

			$cmd = t3lib_exec::getCommand($this->info['exec']).'  '.escapeshellarg($inputFile);
			$exif = $ret = NULL;
			exec($cmd, $exif, $ret);

			if (!$ret AND is_array($exif)) {
				foreach ($exif as $line) {

					list($name,$value) = t3lib_div::trimExplode(':',$line);
					if ($value) { // ignore empty lines headers and emtpy entries

						$name=str_replace('-','',$name);
						$name=str_replace(' ','',$name);

							// add to exif table
						$this->exif[$name] = $value;

							// add to DAM table
						switch ($name) {
							case 'CameraModel':
								$this->out['fields']['file_creator'] = $value;
							break;
							case 'ImageCreated':
								$this->out['fields']['date_cr'] = tx_svmetaextract_lib::parseDate($value);
							break;
							case 'HorizontalResolution':
								$this->out['fields']['hres'] = intval($value);
							break;
							case 'VerticalResolution':
								$this->out['fields']['vres'] = intval($value);
							break;
							case 'ColorSpaceInformation':
								$this->out['fields']['color_space'] = $value;
							break;
							case 'Title':
								$this->out['fields']['title'] = $value;
							break;
						}
					}
				}
			}

			$this->postProcess();

			$this->out['fields']['meta']['EXIF'] = $this->exif;

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}


	/**
	 * processing of values
	 */
	function postProcess () {
		global $TYPO3_CONF_VARS;
		
		$csConvObj = t3lib_div::makeInstance('t3lib_cs');
		$csConvObj->convArray($this->exif, 'iso-8859-1', 'utf-8');
		$csConvObj->convArray($this->out['fields'], 'iso-8859-1', $this->conf['wantedCharset']);
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sv_metaextract/sv3/class.tx_svmetaextract_sv3.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sv_metaextract/sv3/class.tx_svmetaextract_sv3.php']);
}

?>
