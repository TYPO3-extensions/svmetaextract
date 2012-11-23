<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Rene Fritz <r.fritz@colorcube.de>
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
 * Service 'XMP meta extraction' for the 'svmetaextract' extension
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */

require_once ('XMP.php');
require_once ('JPEG.php');

class tx_svmetaextract_sv6 extends t3lib_svbase {
	var $prefixId = 'tx_svmetaextract_sv6';		// Same as class name
	var $scriptRelPath = 'sv6/class.tx_svmetaextract_sv6.php';	// Path to this script relative to the extension dir.
	var $extKey = 'svmetaextract';	// The extension key.

	/**
	 * performs the service processing
	 *
	 * @param	string		Content which should be processed.
	 * @param	string		Content type
	 * @param	array		Configuration array
	 * @return	boolean
	 */
	function process($content = '', $type = '', $conf=array())	{

		$this->conf = $conf;

		$this->out = array();

		if ($content) {
			$this->setInput($content, $type);
		}

		if ($inputFile = $this->getInputFile() AND ($jpeg_header_data = get_jpeg_header_data($inputFile))) {
			$this->xmpRaw = get_XMP_text($jpeg_header_data);

			preg_match('#<x:xmpmeta[^>]*>.*</x:xmpmeta>#is', $this->xmpRaw, $match);
			$this->xmpRaw = $match[0];

			$this->xmp = parseXMP2simpleArray(read_XMP_array_from_text($this->xmpRaw));

			foreach ($this->xmp as $key => $value) {
					// ignore empty lines headers and emtpy entries
				if ($value) {
					switch (strtolower($key)) {
						case 'dc:creator':
							$this->out['fields']['creator'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'dc:description':
							$this->out['fields']['description'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'dc:rights':
							$this->out['fields']['copyright'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'dc:subject':
							$this->out['fields']['keywords'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'dc:title':
							$this->out['fields']['title'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'iptc4xmpcore:countrycode':
							if (strlen($value) == 2) {
								$select = 'cn_iso_3';
								$table = 'static_countries';
								$where = tx_staticinfotables_div::getIsoCodeField($table, $value) . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($value), 'static_countries');
								$where  .= t3lib_BEfunc::deleteClause($table);
								$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table, $where);
								if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
									$this->out['fields']['loc_country'] = $row['cn_iso_3'];
								}
							}
						break;
						case 'iptc4xmpcore:location':
							$value = trim($value);
							if ($value{0} == '/' OR $value{0} == '\\') {
								$this->out['fields']['file_orig_location'] = tx_svmetaextract_lib::forceUtf8($value);
							} else {
								$this->out['fields']['loc_desc'] = tx_svmetaextract_lib::forceUtf8($value);
							}
						break;
						case 'xmp:createdate':
							$this->out['fields']['date_cr'] = tx_svmetaextract_lib::parseDate($value);
						break;
						case 'xmp:creatortool':
							$this->out['fields']['file_creator'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'xmp:modifydate':
							$this->out['fields']['date_mod'] = tx_svmetaextract_lib::parseDate($value);
						break;
						case 'xap:createdate':
							$this->out['fields']['date_cr'] = tx_svmetaextract_lib::parseDate($value);
						break;
						case 'xap:creatortool':
							$this->out['fields']['file_creator'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'xap:modifydate':
							$this->out['fields']['date_mod'] = tx_svmetaextract_lib::parseDate($value);
						break;
						case 'xaprights:copyright':
							$this->out['fields']['copyright'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'xaprights:usageterms':
							$this->out['fields']['instructions'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'xmptpg:npages':
							$this->out['fields']['pages'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'pdf:keywords':
							$this->out['fields']['keywords'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'pdf:producer':
							$this->out['fields']['file_creator'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'photoshop:captionwriter':
							$this->out['fields']['photoshop:captionwriter'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'photoshop:city':
							$this->out['fields']['loc_city'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'photoshop:credit':
							$this->out['fields']['copyright'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'photoshop:headline':
							$this->out['fields']['title'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
						case 'photoshop:instructions':
							$this->out['fields']['instructions'] = tx_svmetaextract_lib::forceUtf8($value);
						break;
					}
				}
			}

			$this->out['fields']['meta']['xmp'] = $this->xmp;
			$this->out['fields']['meta']['xmp_xml'] = $this->xmpRaw;
		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/sv6/class.tx_svmetaextract_sv6.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/sv6/class.tx_svmetaextract_sv6.php']);
}

?>