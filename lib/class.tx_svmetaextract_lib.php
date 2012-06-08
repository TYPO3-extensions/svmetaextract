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
 * Misc functions for the 'sv_metaextract' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */



class tx_svmetaextract_lib {

    /** 
     * convert date string into tstamp
     * 
     * @param        string        $date        String date to be parsed 
     */ 
    public static function parseDate($date)  {

		// Try PHP strtotime first and make an early return if it returns something usable
		$parsedDate = strtotime($date);
			// PHP < 5.1 returns '-1' instead of FALSE
		if ($parsedDate != FALSE && $parsedDate != '-1') {
			return $parsedDate;
		}

        // A string first parameter. Use as date/time string

        $matches = array(); 
        // Test for YYYY-MM-DD[ hh[:mm[:ss[.uuuuuu]]]] or YYYY:MM:DD[ hh[:mm[:ss[.uuuuuu]]]] 
        if ( preg_match( '/(\d\d\d\d)[-:](\d\d)[-:](\d\d)(\s(\d\d)(:(\d\d)(:(\d\d)){0,1}){0,1}){0,1}/i', $date, $matches ) )    { 
            $year = ($matches[1] < -9999 || $matches[1] > 9999)?date('Y'):$matches[1]; 
            $month = ($matches[2] < 1 || $matches[2] > 12)?date('m'):$matches[2]; 
            $day = ($matches[3] < 1 || $matches[3] > 31)?date('d'):$matches[3]; 
            $hour = (isset($matches[5]) && $matches[5] >= 0 && $matches[5] < 24)?$matches[5]:date('H'); 
            $minute = (isset($matches[7]) && $matches[7] >= 0 && $matches[7] < 60)?$matches[7]:date('i'); 
            $second = (isset($matches[9]) && $matches[9] >= 0 && $matches[9] < 60)?$matches[9]:date('s'); 
        } 
        // Test for M[M]/D[D]/YY[YY] 
        elseif ( preg_match( '#((\d){1,2}){1}/((\d){1,2}){1}/((\d\d){1,2}){1}#i', $date, $matches ) )  { 
            $year = ($matches[5] < -9999 || $matches[5] > 9999)?date('Y'):$matches[5]; 
            if ( $year < 100 ) 
            { 
                $year += ($year<30)?2000:1900; 
            } 
            $month = ($matches[1] < 1 || $matches[1] > 12)?date('m'):$matches[1]; 
            $day = ($matches[3] < 1 || $matches[3] > 31)?date('d'):$matches[3]; 
            $hour = date('H'); 
            $minute = date('i'); 
            $second = isset($matches[6])?$matches[6]:date('s'); 
        } 
        // Test for D[D]-M[M]-YY[YY] or D[D].M[M].YY[YY] or D[D]:M[M]:YY[YY] or D[D];M[M];YY[YY] 
        elseif ( preg_match( '/((\d){1,2}){1}[-:;.]((\d){1,2}){1}[-:;.]((\d\d){1,2}){1}/i', $date, $matches ) )   { 
            $year = ($matches[5] < -9999 || $matches[5] > 9999)?date('Y'):$matches[5]; 
            if ( $year < 100 ) 
            { 
                $year += ($year<30)?2000:1900; 
            } 
            $month = ($matches[3] < 1 || $matches[3] > 12)?date('m'):$matches[3]; 
            $day = ($matches[1] < 1 || $matches[1] > 31)?date('d'):$matches[1]; 
            $hour = date('H'); 
            $minute = date('i'); 
            $second = date('s'); 
        // Test YYYY
        }  if ( preg_match( '/^(\d\d\d\d)$/i', $date, $matches ) )   { 
            $year = ($matches[1] < -9999 || $matches[1] > 9999)?date('Y'):$matches[1]; 
            if ( $year < 100 ) 
            { 
                $year += ($year<30)?2000:1900; 
            } 
            $month = date('m'); 
            $day = date('d'); 
            $hour = date('H'); 
            $minute = date('i'); 
            $second = date('s'); 
        }  
        else  { 
            // No pattern match. Set the default date/time to 0 
            return 0; 
        } 
        // Convert all elements to integer 
        $year = (int)$year; 
        $month = (int)$month; 
        $day = (int)$day; 
        $hour = (int)$hour; 
        $minute = (int)$minute; 
        $second = (int)$second; 
        return mktime( $hour, $minute, $second, $month, $day, $year );
    }

    /**
     * Check if string is utf-8 encoded, if not, encode it to utf-8
     *
     * @param        string        $date        String date to be parsed
     */
	public static function forceUtf8($string) {
		if (!(utf8_encode(utf8_decode($string)) == $string)) {
			$string = utf8_encode($string);
		}

		return $string;

	}

	/**
	 * convert float value from a unit to another one
	 */
	public static function convertUnit($value, $unitFrom, $unitTo) {
		// 72 pts per inch
		// 2.54 cm per inch

		if ($unitFrom == 'pts' && $unitTo == 'cm') {

			$rawResult = $value / 72 * 2.54;
			return round($rawResult, 4);

		} else if ($unitFrom == 'pts' && $unitTo == 'in') {

			$rawResult = $value / 72;
			return round($rawResult, 4);

		}

			// Do not know how to handle conversion
		return $value;

	}

	/**
	 * @author   "Sebastián Grignoli" <grignoli@framework2.com.ar>
	 * @package  forceUTF8
	 * @version  1.1
	 * @link     http://www.framework2.com.ar/dzone/forceUTF8-es/
	  */

	public static function forceUtf8Alternative($text) {
		/**
		* Function forceUTF8
		*
		* This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
		*
		* It may fail to convert characters to unicode if they fall into one of these scenarios:
		*
		* 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
		*    are followed by any of these:  ("group B")
		*                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
		* For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
		* The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
		* is also a valid unicode character, and will be left unchanged.
		*
		* 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
		* 3) when any of these: ðñòó  are followed by THREE chars from group B.
		*
		* @name forceUtf8
		* @param string $text  Any string.
		* @return string  The same string, utf8 encoded
		*
		*/

		// Not used ATM but left in case of it's necessary again
		$max = strlen($text);
		$buf = "";
		for($i = 0; $i < $max; $i++) {
			$c1 = $text{$i};
			if ($c1>="\xc0") { //Should be converted to UTF8, if it's not UTF8 already
				$c2 = $i+1 >= $max? "\x00" : $text{$i+1};
				$c3 = $i+2 >= $max? "\x00" : $text{$i+2};
				$c4 = $i+3 >= $max? "\x00" : $text{$i+3};
				if ($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
					if ($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2;
						$i++;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif ($c1 >= "\xe0" & $c1 <= "\xef") { //looks like 3 bytes UTF8
					if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif ($c1 >= "\xf0" & $c1 <= "\xf7") { //looks like 4 bytes UTF8
					if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} else { //doesn't look like UTF8, but should be converted
					$cc1 = (chr(ord($c1) / 64) | "\xc0");
					$cc2 = (($c1 & "\x3f") | "\x80");
					$buf .= $cc1 . $cc2;
				}
			} elseif (($c1 & "\xc0") == "\x80") { // needs conversion
				$cc1 = (chr(ord($c1) / 64) | "\xc0");
				$cc2 = (($c1 & "\x3f") | "\x80");
				$buf .= $cc1 . $cc2;
			} else { // it doesn't need conversion
				$buf .= $c1;
			}
		}
		return $buf;
	}

	/**
	 * Detect if a string contains multi-byte non-ascii chars that fall in the UTF-8 ranges
	 * @param string input
	 * @return bool
	 */
	public static function detectUTF8($string_in)	{

		// Not used ATM but left in case of it's necessary again

		return preg_match('%(?:
			[\xC2-\xDF][\x80-\xBF]				# non-overlong 2-byte
			|\xE0[\xA0-\xBF][\x80-\xBF]			# excluding overlongs
			|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}	# straight 3-byte
			|\xED[\x80-\x9F][\x80-\xBF]			# excluding surrogates
			|\xF0[\x90-\xBF][\x80-\xBF]{2}		# planes 1-3
			|[\xF1-\xF3][\x80-\xBF]{3}			# planes 4-15
			|\xF4[\x80-\x8F][\x80-\xBF]{2}		# plane 16
			)+%xs', $string_in);
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/lib/class.tx_svmetaextract_lib.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/svmetaextract/lib/class.tx_svmetaextract_lib.php']);
}

?>
