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
 * Service 'PDF meta extraction' for the 'cc_metaexec' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */


require_once(PATH_t3lib . 'class.t3lib_svbase.php');

class tx_svmetaextract_sv5 extends t3lib_svbase {

	var $prefixId = 'tx_svmetaextract_sv5';		// Same as class name
	var $scriptRelPath = 'sv5/class.tx_svmetaextract_sv5.php';	// Path to this script relative to the extension dir.
	var $extKey = 'sv_metaextract';	// The extension key.


	/**
	 * Extracts PDF metadata using 'pdfinfo'
	 * performs the service processing
	 *
	 * @param	string 	Content which should be processed.
	 * @param	string 	Content type
	 * @param	array 	Configuration array
	 * @return	boolean
	 */
	function process($content='', $type='', $conf=array())	{

		$this->out=array();

		if ($content) {
			$this->setInput ($content, $type);
		}

		if($inputFile = $this->getInputFile()) {

			$cmd = t3lib_exec::getCommand($this->info['exec']).'  "'.$inputFile.'"';
			$pdfmeta='';
			$ret='';
			exec($cmd, $pdfmeta, $ret);

			if (!$ret AND is_array($pdfmeta)) {
				foreach ($pdfmeta as $line) {

						// Only request 2 elements because pdfinfo output might contain colons in $value
					list($name, $value) = explode(':', $line, 2);
					$name = trim($name);
					$value = trim($value);

					if ($value) { // ignore empty lines headers and empty entries

						switch ($name) {
							case 'Producer':
								$this->out['fields']['file_creator'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'Title':
								$this->out['fields']['title'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'Subject':
								$this->out['fields']['description'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'Keywords':
								$this->out['fields']['keywords'] = tx_svmetaextract_lib::forceUtf8($value);
							break;
							case 'CreationDate':
								$this->out['fields']['date_cr'] = tx_svmetaextract_lib::parseDate($value);
								$this->out['fields']['file_ctime'] = tx_svmetaextract_lib::parseDate($value);
							break;
							case 'ModDate':
								$this->out['fields']['date_mod'] = tx_svmetaextract_lib::parseDate($value);
								$this->out['fields']['file_mtime'] = tx_svmetaextract_lib::parseDate($value);
							break;
							case 'Pages':
							case 'PageCount':
								$this->out['fields']['pages'] = intval($value);
							break;
							case 'Page Size':
							case 'Page size':
								// 595 x 842 pts (A4)

								$v = explode(' ',$value);
								$unitFrom = $v[3];
								// TODO: create TCA to let user choose imperial/metric unit
								$unitTo = 'cm';

								$this->out['fields']['width'] = (float)tx_svmetaextract_lib::convertUnit($v[0], $unitFrom, $unitTo);
								$this->out['fields']['height'] = (float)tx_svmetaextract_lib::convertUnit($v[2], $unitFrom, $unitTo);
								$this->out['fields']['width_unit'] = $unitTo;
								$this->out['fields']['height_unit'] = $unitTo;
							break;
							case 'PDF version':
								$this->out['fields']['file_type_version'] = $value;
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



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sv_metaextract/sv5/class.tx_svmetaextract_sv5.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sv_metaextract/sv5/class.tx_svmetaextract_sv5.php']);
}

?>
