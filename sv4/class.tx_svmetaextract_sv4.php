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
 * Service 'EXIF extraction (exiftool)' for the 'cc_metaexif' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */


require_once(PATH_t3lib.'class.t3lib_svbase.php');
require_once(t3lib_extMgm::extPath('svmetaextract') . 'lib/class.tx_svmetaextract_lib.php');

class tx_svmetaextract_sv4 extends t3lib_svbase {
	var $prefixId = 'tx_svmetaextract_sv4';		// Same as class name
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

			$cmd = t3lib_exec::getCommand($this->info['exec']).' -q -m -g -S '.escapeshellarg($inputFile);
			$exif = $ret = NULL;
			exec($cmd, $exif, $ret);

			if (!$ret AND is_array($exif)) {
		
				$section = 'EXIF';
				array_shift($exif); // ---- ExifTool ----
				array_shift($exif); // ExifToolVersion                 : 6.57
				foreach ($exif as $line) {

					if (preg_match('#---- ([^ ]*) ----#', $line, $matches)) {
						$section = $matches[1];
					}

						// Only request 2 elements because exiftool output contains colons in $value
					list($name, $value) = explode(':', $line, 2);
					$name = trim($name);
					$value = trim($value);

					if ($value) { // ignore empty lines headers and empty entries



						$name=str_replace('-','',$name);
						$name=str_replace(' ','',$name);

							// add to exif table
						$this->exif[$section][$name] = $value;

							// add to DAM table
						switch ($name) {
							case 'CameraModel':
								$this->out['fields']['file_creator'] = $value;
							break;
							case 'XResolution':
							case 'HorizontalResolution':
								$this->out['fields']['hres'] = intval($value);
							break;
							case 'YResolution':
							case 'VerticalResolution':
								$this->out['fields']['vres'] = intval($value);
							break;

							case 'Title':
							case 'Headline':
							case 'XPTitle':
								$this->out['fields']['title'] = $value;
							break;
							
							case 'Keywords':
							case 'XPKeywords':
								$this->out['fields']['keywords'] = $value;
							break;

							case 'Subject':
							case 'ImageDescription':
							case 'Description':
							        $this->out['fields']['description'] = $value;
							break;
							case 'CaptionAbstract':
							        $this->out['fields']['caption'] = $value;
							break;
	
							case 'ModifyDate':
							        $this->out['fields']['date_mod'] = tx_svmetaextract_lib::parseDate($value);
							        $this->out['fields']['file_mtime'] = tx_svmetaextract_lib::parseDate($value);
							break;
							case 'ImageCreated':
							case 'CreateDate':
							case 'DateTimeOriginal':
							        $this->out['fields']['date_cr'] = tx_svmetaextract_lib::parseDate($value);
							        $this->out['fields']['file_ctime'] = tx_svmetaextract_lib::parseDate($value);
							break;
							case 'CreatorTool':
							case 'Software':
							        $this->out['fields']['file_creator'] = $value;
							break;
							case 'City':
							        $this->out['fields']['loc_city'] = $value;
							break;
							case 'Country':
					// TODO format?
							        # $this->out['fields']['loc_country'] = $value;
							break;
							
							
							case 'CountryCode':
								if (strlen($value)==2) {
									$isoCodeField = tx_staticinfotables_div::getIsoCodeField('static_countries', $value);
							
									$enableFields = t3lib_BEfunc::deleteClause('static_countries');
							
									$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('cn_iso_3', 'static_countries', $isoCodeField.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($value),'static_countries').$enableFields);
									if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
										$this->out['fields']['loc_country'] = $row['cn_iso_3'];
									}
								} 
							break;
							
							case 'Artist':
							case 'Creator':
							        $this->out['fields']['creator'] = $value;
							break;
							case 'Copyright':
							case 'CopyrightNotice':
							        $this->out['fields']['copyright'] = $value;
							break;
							case 'Rights':
							        $this->out['fields']['copyright'] = $value;
							break;
							case 'RightsUsageTerms':
							case 'UsageTerms':
							        $this->out['fields']['instructions'] = $value;
							break;
							case 'Credit':
							         $this->out['fields']['copyright'] = $value;
							break;
							case 'Instructions':
							        $this->out['fields']['instructions'] = $value;
							break;							
							
						}
					}
				}
			}
			
			// TODO read XMP XML
			// $this->out['fields']['meta']['XMP_XML'] = $this->xmpRaw;
			
			
			unset ($this->exif['File']);
			unset ($this->exif['Composite']);

			$this->postProcess();

			$this->out['fields']['meta'] = $this->exif;
			$this->out['exif_done'] = true;
			$this->out['iptc_done'] = true;
			$this->out['xmp_done'] = true;

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}


	/**
	 * processing of values
	 */
	function postProcess() {
		global $TYPO3_CONF_VARS;
		
		$csConvObj = t3lib_div::makeInstance('t3lib_cs');
		$csConvObj->convArray($this->out['fields'], 'utf-8', $this->conf['wantedCharset']);

	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/sv4/class.tx_svmetaextract_sv4.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/sv4/class.tx_svmetaextract_sv4.php']);
}

?>
