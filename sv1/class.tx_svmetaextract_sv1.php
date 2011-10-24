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
 * Service 'IPTC extraction' for the 'cc_metaexif' extension.
 *
 * Note: Support for JPC, JP2, JPX, JB2, XBM, and WBMP became available in PHP 4.3.2. Support for SWC exists as of PHP 4.3.0 and TIFF support was added in PHP 4.2.0
 *
 * Note: JPEG 2000 support was added in PHP 4.3.2. Note that JPC and JP2 are capable of having components with different bit depths.
 * In this case, the value for "bits" is the highest bit depth encountered. Also, JP2 files may contain multiple JPEG 2000 codestreams.
 * In this case, getimagesize() returns the values for the first codestream it encounters in the root of the file.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see http://demo.imagefolio.com/demo/ImageFolio31_files/skins/cool_blue/images/iptc.html
 */


require_once(PATH_t3lib.'class.t3lib_svbase.php');
require_once(t3lib_extMgm::extPath('sv_metaextract').'lib/class.tx_svmetaextract_lib.php');

class tx_svmetaextract_sv1 extends t3lib_svbase {
	var $prefixId = 'tx_svmetaextract_sv1';		// Same as class name
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
		$this->iptc = array();

		if ($content) {
			$this->setInput ($content, $type);
		}

		if($inputFile = $this->getInputFile()) {

			$info = array();
			$size = GetImageSize ($inputFile, $info);
			if (isset($info['APP13'])) {
				$iptc = iptcparse($info['APP13']);
				if (is_array($iptc)) {
					foreach($iptc as $key => $val) {
						foreach($val as $k => $v) {
							if (!trim($v)) {
								continue;
							}
								// clean up
							$v = trim($v);
							$v = str_replace(chr(0x00), ' ', $v);
							$v = str_replace(chr(213), "'", $v);

							switch ($key) {


								case '2#105': // headline
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['title'] = $v;
									$this->iptc['headline'] = $v;
								break;

								case '2#005': // object name - Kurztitel, Objektname, Dokumententitel, Object Name, Objektname
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['ident'] = $v;
									$this->iptc['object_name'] = $v;
								break;

								case '2#120': // caption - Beschreibung des Objektinhaltes, Beschreibung, Objektbeschreibung, Beschreibung, Caption, Objektbeschreibung
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['title'] = $v;
									$this->iptc['caption'] = $v;
								break;

								case '2#025': // keyword - Liste von Schlüsselwörtern, Suchbegriffe, Stichwörter
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['keywords'][] = $v;
								break;

								// ---- Copyright

								case '2#040': // special instructions - Hinweise zur Benutzung, Hinweis, Besondere Hinweise, Anweisung
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['instructions'] = $v;
									$this->iptc['special_instructions'] = $v;
								break;

								case '2#116': // copyright notice - Angaben zum Copyright, Copyright-Info, Copyright-Vermerk,Copyright-Informationen
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['copyright'] = $v;
									$this->iptc['copyright_notice'] = $v;
								break;

								case '2#110': // credit
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['credit'] = $v;
								break;

								case '2#115': // Source - Inhaber der Rechte. Kann eine Agentur, ein Agenturmitglied oder ein Individuum sein, Quelle
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['publisher'] = $v;
									$this->iptc['source'] = $v;
								break;

								case '2#080': // byline - Liste von Namen der Autoren, Photografen oder Grafiker, Fotograf, Name des Autors
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['creator'] = $v;
									$this->iptc['byline'] = $v;
								break;

								case '2#085': // byline title
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['byline_title'] = $v;
								break;

								// ---- Location

								case '2#100': // country code
									$this->out['fields']['loc_country'] = strtoupper($v); // ISO 3 ?!
									$this->iptc['country_code'] = $v;
								break;

								case '2#101': // country
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['country'] = $v;
								break;

								case '2#090': // city
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['loc_city'] = $v;
									$this->iptc['city'] = $v;
								break;

								case '2#095': // state
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['state'] = $v;
								break;

								case '2#092': // Sublocation - Stadt/Ort an dem das Objekt entstanden ist, Stelle/Flecken
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->out['fields']['loc_desc'] = $v;
									$this->iptc['sublocation'] = $v;
								break;

								// ---- div

								case '2#055': // date created - Datum, Erstellt am, Erstellungsdatum
									$this->out['fields']['date_cr'] = tx_svmetaextract_lib::parseDate($v);
									$this->iptc['date_created'] = $v;
								break;

								// ----

								case '2#000': // rubbish ??
								break;

								case '2#022': // Fixture Identifier - ???
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['fixture'] = $v;
								break;

								case '2#122': // caption writer
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['caption_writer'] = $v;
								break;

								case '2#012': // Subject Reference  - Liste mit Definitionsstrukturen von Themen in der Form IPR:Referenznummer:Name:ThemenName:DetailName
									$v = tx_svmetaextract_lib::forceUtf8($v);
									$this->iptc['subject_reference'][]= $v;
								break;

								case '2#015': // category
									$this->iptc['category'][]= $v;
								break;
								case '2#020': // supplemental category
									$this->iptc['supplemental_category'][]= $v;
								break;
								default:
									//$v = tx_svmetaextract_lib::forceUtf8($v);
									//$this->iptc['iptc_'.$key] = $v;
								break;
							}
						}
					}
				}
			}

			$this->postProcess();

			$this->out['fields']['meta']['IPTC'] = $this->iptc;

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


		if (is_array($this->iptc['keywords'])) {
			$this->out['fields']['keywords'] = $this->iptc['keywords'] = implode(',', $this->iptc['keywords']);
		}
		if (is_array($this->iptc['category'])) {
			$this->iptc['category'] = implode("\n", $this->iptc['category']);
		}
		if (is_array($this->iptc['supplemental_category'])) {
			$this->iptc['supplemental_category'] = implode("\n", $this->iptc['supplemental_category']);
		}
		if (is_array($this->iptc['subject_reference'])) {
			$this->iptc['subject_reference'] = implode("\n", $this->iptc['subject_reference']);
		}

			// detect country code
		if ($this->out['fields']['loc_country']=='' AND $this->iptc['country']) {
			$country_en = $this->iptc['country'];
			if($country_en) {
				$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($country_en, 'static_countries');
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('cn_iso_3', 'static_countries', 'cn_short_en LIKE '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr, 'static_countries').t3lib_BEfunc::deleteClause('static_countries'));
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					$this->out['fields']['loc_country'] = $row['cn_iso_3'];
					$this->iptc['country_code'] = $row['cn_iso_3'];
				}
			}
		}

	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sv_metaextract/sv1/class.tx_svmetaextract_sv1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sv_metaextract/sv1/class.tx_svmetaextract_sv1.php']);
}

?>