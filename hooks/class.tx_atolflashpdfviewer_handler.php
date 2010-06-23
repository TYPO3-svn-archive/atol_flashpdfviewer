<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Cédric Girardot <cgi@atolcd.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

if (!defined ('TYPO3_MODE'))
	die ('Access denied.');

require_once (t3lib_extMgm::extPath('atol_flashpdfviewer').'pi1/class.tx_atolflashpdfviewer_pi1.php');
	

class tx_atolflashpdfviewer_handler {
	
	/**
	 * Process the link generation
	 *
	 * @param 	string 		$linktxt
	 * @param 	array 		$conf
	 * @param 	string 		$linkHandlerKeyword Define the identifier that an record is given
	 * @param 	string 		$linkHandlerValue Table and uid of the requested record like "tt_news:2"
	 * @param 	string 		$linkParams Full link params like "record:tt_news:2"
	 * @param 	tslib_cObj 	$pObj
	 * @return 	Link
	 */
	function main($linktxt, $conf, $linkHandlerKeyword, $linkHandlerValue, $linkParams, &$pObj) {
		$params = explode(':',$linkHandlerValue); // Paramètres du lien
		$fpv = t3lib_div::makeInstance('tx_atolflashpdfviewer_pi1');
		$fpv->cObj = $pObj;
		$pluginConf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_atolflashpdfviewer_pi1.']; // Configuration du plugin
		$furtherLinkParams = str_replace('pdf:' . $linkHandlerValue, '', $linkParams); // Autres paramètres du lien (ex : affichage dans une nouvelle fenêtre)
		$singleLink = '';
		
		if ($params[0] == 'tx_atolflashpdfviewer') {
			
			$file = $params[1];
			$file = preg_replace('/;/','/',$file) . '.pdf';
			$file = rawurldecode($file);
				
			$pdfHash = md5_file($file);
				
			// On crée un enregistrement dans la table tx_atolflashpdfviewer_pdffiles
			$uid = $fpv->insertRecord(basename($file), dirname($file), $pdfHash);
			
			$singlePid = $pluginConf['singlePid'] . ' ' . $furtherLinkParams;
				
			$singleLink = $fpv->getSingleLink($linktxt, $singlePid, 'tx_flaspdfviewer_pdffiles', $uid);
			
		} elseif ($params[0] == 'tx_dam') {
			$singlePid = $pluginConf['singlePid'] . ' ' . $furtherLinkParams;
				
			$singleLink =  $fpv->getSingleLink($linktxt, $singlePid, 'tx_dam', $params[1]);
		}
		
		return $singleLink;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/hooks/class.tx_atolflashpdfviewer_handler.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/hooks/class.tx_atolflashpdfviewer_handler.php']);
}

?>