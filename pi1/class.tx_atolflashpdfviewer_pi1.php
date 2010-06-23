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

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'PDF viewer' for the 'atol_flashpdfviewer' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_atolflashpdfviewer
 */
class tx_atolflashpdfviewer_pi1 extends tslib_pibase {

	var $prefixId      = 'tx_atolflashpdfviewer_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_atolflashpdfviewer_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'atol_flashpdfviewer';	// The extension key.
	var $pi_checkCHash = true;

	var $swfPath = "uploads/tx_atolflashpdfviewer/swf/";

	/**
	 * Fonction d'initialisation
	 *
	 * @param 	array 		$conf: The PlugIn configuration
	 * @return	void
	 */
	function init($conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		

		// On récupère les valeurs du flexform
		$this->pi_initPIflexForm();
		$piFlexForm = $this->cObj->data['pi_flexform'];
		$this->conf['displayMode'] = $this->pi_getFFvalue($piFlexForm, 'displayMode', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'displayMode', 'sDEF') : $this->conf['displayMode'];
		$this->conf['templateFile'] = $this->pi_getFFvalue($piFlexForm, 'templateFile', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'templateFile', 'sDEF') : $this->conf['templateFile'];

		if ($this->conf['displayMode'] == 'LIST') {
			$this->conf['recordsSelectionMode'] = $this->pi_getFFvalue($piFlexForm, 'recordsSelectionMode', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'recordsSelectionMode', 'sDEF') : $this->conf['recordsSelectionMode'];
			$this->conf['startingpointDirectory'] = $this->pi_getFFvalue($piFlexForm, 'startingpointDirectory', 'sDEF') ? $this->formatPath($this->pi_getFFvalue($piFlexForm, 'startingpointDirectory', 'sDEF')) : $this->formatPath($this->conf['startingpointDirectory']);
			$this->conf['startingpointRecords'] = $this->pi_getFFvalue($piFlexForm, 'startingpointRecords', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'startingpointRecords', 'sDEF') : $this->conf['startingpointRecords'];
			$this->conf['startingpointDam'] = $this->pi_getFFvalue($piFlexForm, 'startingpointDam', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'startingpointDam', 'sDEF') : $this->conf['startingpointDam'];
			$this->conf['startingpointDamcat'] = $this->pi_getFFvalue($piFlexForm, 'startingpointDamcat', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'startingpointDamcat', 'sDEF') : $this->conf['startingpointDamcat'];
			$this->conf['recursive'] = $this->pi_getFFvalue($piFlexForm, 'recursive', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'recursive', 'sDEF') : $this->conf['recursive'];
			$this->conf['singlePid'] = $this->pi_getFFvalue($piFlexForm, 'singlePid', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'singlePid', 'sDEF') : $this->conf['singlePid'];

		} elseif ($this->conf['displayMode'] == 'SINGLE') {
			$this->conf['viewer.']['width'] = $this->pi_getFFvalue($piFlexForm, 'width', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'width', 'sDEF') : $this->conf['viewer.']['width'];
			$this->conf['viewer.']['height'] = $this->pi_getFFvalue($piFlexForm, 'height', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'height', 'sDEF') : $this->conf['viewer.']['height'];
			$this->conf['viewer.']['allowSearch'] = $this->pi_getFFvalue($piFlexForm, 'allowSearch', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'allowSearch', 'sDEF') : $this->conf['viewer.']['allowSearch'];
			$this->conf['viewer.']['allowFullscreen'] = $this->pi_getFFvalue($piFlexForm, 'allowFullscreen', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'allowFullscreen', 'sDEF') : $this->conf['viewer.']['allowFullscreen'];
			$this->conf['viewer.']['allowPrint'] = $this->pi_getFFvalue($piFlexForm, 'allowPrint', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'allowPrint', 'sDEF') : $this->conf['viewer.']['allowPrint'];
			$this->conf['viewer.']['allowClipboard'] = $this->pi_getFFvalue($piFlexForm, 'allowClipboard', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'allowClipboard', 'sDEF') : $this->conf['viewer.']['allowClipboard'];
			$this->conf['viewer.']['allowPager'] = $this->pi_getFFvalue($piFlexForm, 'allowPager', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'allowPager', 'sDEF') : $this->conf['viewer.']['allowPager'];
			$this->conf['viewer.']['zoomType'] = $this->pi_getFFvalue($piFlexForm, 'zoomType', 'sDEF') ? $this->pi_getFFvalue($piFlexForm, 'zoomType', 'sDEF') : $this->conf['viewer.']['zoomType'];
		}

		// Récursivité à zéro par défaut
		if(!isset($this->conf['recursive'])) {
			$this->conf['recursive'] = 0;
		}
		
		$this->conf['viewer.']['zoomType'] = 'best_fit' ? 1 : 2;

		// On récupère le code du template
		$this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);

		// Css à inclure dans la page
		$GLOBALS['TSFE']->additionalHeaderData['tx_atolflashpdfviewer_100'] = '
			<link rel="stylesheet" type="text/css" href="typo3conf/ext/' . $this->extKey . '/res/css/style.css"/>';
	}

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->init($conf);

		switch ($this->conf['displayMode']) {
			case 'LIST' : // mode LIST (espace documentaire)
				$content .= $this->displayList();
				break;
			case 'SINGLE' : // mode SINGLE (visionneuse)
				$content .= $this->displaySingle();
				break;
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Fonction d'affichage du mode LIST
	 *
	 * @return	void
	 */
	function displayList() {

		switch($this->conf['recordsSelectionMode']) {
			case 'DIRECTORY' : // Liste de fichiers PDF à partir d'un répertoire
				$content .= $this->getPdfDirectory();
				break;
			case 'RECORDS' : // Liste de fichiers PDF à partir des points d'entrées
				$content .= $this->getPdfRecords();
				break;
			case 'DAM' : // Liste de fichiers PDF à partir du DAM
				$content .= $this->getPdfDam();
				break;
			case 'DAMCAT' : // Lisre de fichiers PDF à partir de catégories du DAM
				$content .= $this->getPdfDamCat();
				break;
		}

		return $content;
	}

	/**
	 * Fonction d'affichage du mode SINGLE
	 *
	 * @return	void
	 */
	function displaySingle() {

		// On récupère le template SINGLE
		$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SINGLE###');

		switch ($this->piVars['table']) {
			case 'tx_atolflashpdfviewer_pdffiles' : // On recherche l'enregistrement dans la table 'tx_flaspdfviewer_pdffiles'				
				$pdf = $this->pdfInfo($this->piVars['uid']);
				break;
			case 'tx_dam' : // On recherche l'enregistrement dans la table 'tx_dam'
				$pdf = $this->pdfInfoDam($this->piVars['uid']);
				break;
		}

		if (isset($pdf)) {
			$file = $pdf['pdf_path'] . $pdf['pdf_name']; // Chemin complet du fichier PDF
			$swfFile = $this->swfPath . $pdf['pdf_hash'] . '.swf'; // Chemin complet du fichier swf

			// Si le fichier swf n'existe pas on le génère
			if (!file_exists($swfFile)) {
				$this->genSWF($file, $swfFile);
			}
		}
		
		$content = $this->cObj->substituteMarkerArrayCached($template['total'] , $markerArray, array());

		// Javascript à inclure dans la page
		$GLOBALS['TSFE']->additionalHeaderData['tx_atolflashpdfviewer_200'] = '
			<script type="text/javascript">
				var flashvars = {
					doc_url: "' . $swfFile . '",
					viewerWidth: "' . $this->conf['viewer.']['width'] . '",
					viewerHeight: "' . $this->conf['viewer.']['height'] . '",
					allow_search: "' . $this->conf['viewer.']['allowSearch'] . '",
					allow_fullscreen: "' . $this->conf['viewer.']['allowFullscreen'] . '",
					allow_print: "' . $this->conf['viewer.']['allowPrint'] . '",
					allow_clipboard: "' . $this->conf['viewer.']['allowClipboard'] . '",
					allow_pager: "' . $this->conf['viewer.']['allowPager'] . '",
					zoomtype: "' . $this->conf['viewer.']['zoomType'] . '",
					l10n_copyToClipboard: "' . $this->pi_getLL('zviewer.copy_to_clipboard') . '",
					l10n_clearSearch: "' . $this->pi_getLL('zviewer.clearSearch') . '",
					l10n_previousPage: "' . $this->pi_getLL('zviewer.previousPage') . '",
					l10n_nextpPage: "' . $this->pi_getLL('zviewer.nextpPage') . '",
					l10n_zoomIn: "' . $this->pi_getLL('zviewer.zoomIn') . '",
					l10n_zoomOut: "' . $this->pi_getLL('zviewer.zoomOut') . '",
					l10n_zoomBestFit: "' . $this->pi_getLL('zviewer.zoomBestFit') . '",
					l10n_zoomShowAll: "' . $this->pi_getLL('zviewer.zoomShowAll') . '",
					l10n_search: "' . $this->pi_getLL('zviewer.search') . '",
					l10n_fullScreen: "' . $this->pi_getLL('zviewer.fullScreen') . '",
					l10n_print: "' . $this->pi_getLL('zviewer.print') . '"
				}
			</script>
			<script type="text/javascript" src="typo3conf/ext/' . $this->extKey . '/res/js/jquery-1.4.2.min.js"></script>
			<script type="text/javascript" src="typo3conf/ext/' . $this->extKey . '/res/js/swfobject.js"></script>
			<script type="text/javascript" src="typo3conf/ext/' . $this->extKey . '/res/js/viewer.js"></script>';

		return $content;
	}

	/**
	 * Création de la liste à partir des fichiers PDF d'un répertoire
	 *
	 * @return	Contenu avec la liste des fichiers
	 */
	function getPdfDirectory() {

		// On vérifie que le chemin donné est bien un répertoire
		if (is_dir( $this->conf['startingpointDirectory'] )) {
			// On récupère les templates
			$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_LIST###');
			$template['file'] = $this->cObj->getSubpart($this->templateCode, '###FILE_SINGLE###');

			// On récupère la liste des fichiers PDF selon le niveau de récursion choisi
			$files = t3lib_div::getAllFilesAndFoldersInPath(array(), $this->conf['startingpointDirectory'], 'pdf', 0, $this->conf['recursive']);

			$markerArray = array();
			$subpartArray = array();
			$subpartArray['###FILES_LIST###'] = '';

			// Construction du contenu
			foreach ($files as $file) {
				$pdfHash = md5_file($file);
				$fileInfo = pathinfo($file);

				// Insertion d'un enregistrement à partir des info du fichier
				$uid = $this->insertRecord($fileInfo['basename'], $fileInfo['dirname'], $pdfHash);

				$markerArray['###FILE_NAME###'] = $this->getSingleLink($fileInfo['basename'], $this->conf['singlePid'], 'tx_atolflashpdfviewer_pdffiles', $uid);

				$subpartArray['###FILES_LIST###'] .= $this->cObj->substituteMarkerArrayCached($template['file'],$markerArray,array());
			}

			$content = $this->cObj->substituteMarkerArrayCached($template['total'] , array(), $subpartArray);
		}

		return $content;
	}

	/**
	 * Création de la liste à partir des points d'entrées
	 *
	 * @return	Contenu avec la liste des fichiers
	 */
	function getPdfRecords() {
		// On récupère les templates
		$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_LIST###');
		$template['file'] = $this->cObj->getSubpart($this->templateCode, '###FILE_SINGLE###');

		// Requête de récupération des enregistrements à partir du point d'entrée
		$select = 'uid';
		$from = 'tx_atolflashpdfviewer_pdffiles';
		$where = 'pid IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($this->conf['startingpointRecords']) . ')' . $this->cObj->enableFields('tx_atolflashpdfviewer_pdffiles');
		$orderBy = 'pdf_name';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $select, $from, $where, '', $orderBy);

		$markerArray = array();
		$subpartArray = array();
		$subpartArray['###FILES_LIST###'] = '';
			
		// On construit le contenu
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$markerArray['###FILE_NAME###'] = $this->getSingleLink($row['pdf_name'], $this->conf['singlePid'], 'tx_atolflashpdfviewer_pdffiles', $row['uid']);

			$subpartArray['###FILES_LIST###'] .= $this->cObj->substituteMarkerArrayCached($template['file'],$markerArray,array());
		}

		$content = $this->cObj->substituteMarkerArrayCached($template['total'] , array(), $subpartArray);

		return $content;
	}

	/**
	 * Création de la liste à partir d'enregistrements du DAM
	 *
	 * @return	Contenu avec la liste des fichiers
	 */
	function getPdfDam() {
		// On récupère les templates
		$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_LIST###');
		$template['file'] = $this->cObj->getSubpart($this->templateCode, '###FILE_SINGLE###');

		// On récupère les enregistrements du DAM
		$files = tx_dam_db::getReferencedFiles( 'tt_content', $uid, 'atol_flashpdfviewer', 'tx_dam_mm_ref' );

		$markerArray = array();
		$subpartArray = array();
		$subpartArray['###FILES_LIST###'] = '';
		
		// On construit le contenu
		foreach ( $files['rows'] as $key => $row ) {
			// On vérifie que c'est bien un fichier PDF
			if ($row['file_type'] == 'pdf') {
				$markerArray['###FILE_NAME###'] = $this->getSingleLink($row['file_name'], $this->conf['singlePid'], 'tx_dam', $row['uid']);

				$subpartArray['###FILES_LIST###'] .= $this->cObj->substituteMarkerArrayCached($template['file'],$markerArray,array());
			}
		}

		$content = $this->cObj->substituteMarkerArrayCached($template['total'] , array(), $subpartArray);

		return $content;
	}

	/**
	 * Création de la liste à partir de catégories du DAM
	 *
	 * @return	Contenu avec la liste des fichiers
	 */
	function getPdfDamCat() {
		// On récupère les templates
		$template['total'] = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_LIST###');
		$template['file'] = $this->cObj->getSubpart($this->templateCode, '###FILE_SINGLE###');

		// On récupère la liste des catégories et sous-catégories selon le niveau de récursion choisi
		$listCat = str_replace( 'tx_dam_cat_', '', $this->conf['startingpointDamcat']);
		$listCat = $this->getRecursiveDamCat($listCat, $this->conf['recursive']);

		// On récupère la liste des enregistrements correspondant à des fichiers PDF
		$select = 'tx_dam.uid, tx_dam.file_name, tx_dam.file_hash';
		$from = 'tx_dam INNER JOIN tx_dam_mm_cat ON tx_dam_mm_cat.uid_local=tx_dam.uid';
		$where = 'tx_dam.file_type LIKE \'pdf\' AND tx_dam_mm_cat.uid_foreign IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($listCat) . ')' . $this->cObj->enableFields('tx_dam');
		$orderBy = 'tx_dam.file_name';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $select, $from, $where, '', $orderBy);

		$markerArray = array();
		$subpartArray = array();
		$subpartArray['###FILES_LIST###'] = '';
		
		// On construit le contenu
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$markerArray['###FILE_NAME###'] = $this->getSingleLink($row['file_name'], $this->conf['singlePid'], 'tx_dam', $row['uid']);

			$subpartArray['###FILES_LIST###'] .= $this->cObj->substituteMarkerArrayCached($template['file'],$markerArray,array());
		}

		$content = $this->cObj->substituteMarkerArrayCached($template['total'] , array(), $subpartArray);

		return $content;
	}

	/**
	 * Liste des catégories avec leurs sous-catégories
	 *
	 * @param	string		$listCat: Liste des catégories de départ (ex : "1,3,17...")
	 * @param 	int			$level: Niveau de récursivité
	 * @return	Liste des catégories
	 */
	function getRecursiveDamCat($listCat, $level) {
		// On ajoute le caractère ',' à la fin de la liste si il n'est pas présent
		if (substr($result, -1) != ',') {
			$result = $listCat . ',';
		}

		// On construit un tableau à partir de la liste des catégories
		$list = explode(',', $listCat);

		if ($level > 0) {
			$level--;

			// On parcourt la liste des catégories
			foreach($list as $cat) {
				// On récupère la liste des sous-catégories
				$select = 'uid';
				$from = 'tx_dam_cat';
				$where = 'parent_id=' . intval($cat) . $this->cObj->enableFields('tx_dam_cat');

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);

				// On parcourt la liste des sous-catégories
				while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {

					$rec = $this->getRecursiveDamCat( $row['uid'], $level );

					// On ajoute les ids des catégories trouvées au résultat
					if ($rec != '') {
						$result .= $rec . ',';
					}
				}
			}
		}

		// On enlève le caractère ',' à la fin
		$result = substr( $result, 0, - 1 );
		return $result;
	}

	/**
	 * Insertion d'un enregistrement
	 *
	 * @param	string		$pdfName: Nom du fichier PDF
	 * @param 	string		$pdfPath: Répertoire du fichier PDF
	 * @param 	string		$pdfHash: MD5 du fichier PDF
	 * @return	L'id de l'enregistrement inséré (-1 si erreur)
	 */
	function insertRecord($pdfName, $pdfPath, $pdfHash) {
		// On test si l'enregistrement existe déjà
		$retVal = $this->recordExists($pdfName, $pdfPath, $pdfHash);

		$pdfPath = $this->formatPath($pdfPath);

		// Si il n'existe pas on l'insère
		if ($retVal == -1) {
			$insertValues = array(
					'pdf_name' => $pdfName,
					'pdf_path' => $pdfPath,
					'pdf_hash' => $pdfHash
			);

			$res = tslib_cObj::DBgetInsert('tx_atolflashpdfviewer_pdffiles',0,$insertValues,'pdf_name,pdf_path,pdf_hash',true);
			$retVal = $GLOBALS['TYPO3_DB']->sql_insert_id();
		}

		return $retVal;
	}

	/**
	 * Test si un enregistrement existe déjà
	 *
	 * @param	string		$pdfName: Nom du fichier PDF
	 * @param 	string		$pdfPath: Répertoire du fichier PDF
	 * @param 	string		$pdfHash: MD5 du fichier PDF
	 * @return	L'id de l'enregistrement, -1 si il n'existe pas
	 */
	function recordExists($pdfName, $pdfPath, $pdfHash) {
		$pdfPath = $this->formatPath($pdfPath);

		$select = 'uid';
		$from = 'tx_atolflashpdfviewer_pdffiles';
		$where =  'pdf_name LIKE \'' . $GLOBALS['TYPO3_DB']->quoteStr($pdfName) . '\' AND pdf_path LIKE \'' . $GLOBALS['TYPO3_DB']->quoteStr($pdfPath) . '\' AND pdf_hash LIKE \'' . $GLOBALS['TYPO3_DB']->quoteStr($pdfHash) . '\'' . $this->cObj->enableFields('tx_atolflashpdfviewer_pdffiles');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		if (isset($row['uid'])) {
			$retVal = $row['uid'];
		} else {
			$retVal = -1;
		}

		return $retVal;
	}

	/**
	 * Génère un fichier swf
	 *
	 * @param 	string		$file: Chemin complet du fichier PDF
	 * @param 	string		$pdfHash: MD5 du fichier PDF
	 * @return	Vrai si le fichier a bien été généré
	 */
	function genSWF($file, $swfFile) {
		$retVal = false;
		$isExt = (TYPO3_OS=='WIN' ? '.exe' : '');
		$conf = $confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['atol_flashpdfviewer']);
		$path = $conf['pdf2swfPath'];
		
		$path = $this->formatPath($path);

		$command = $path . 'pdf2swf' . $isExt;
		$options = '-T 9 -t -o';
		
		// On lance la commande pdf2swf
		exec(escapeshellarg($command) . ' ' . $options . ' ' . escapeshellarg($swfFile) . ' ' . escapeshellarg($file));

		if (file_exists($swfFile)) {
			$retVal = true;
		}

		return $retVal;
	}

	/**
	 * Récupère les les informations sur un enregistrement de la table tx_atolflashpdfviewer_pdffiles
	 *
	 * @param 	int		$uid: Identifiant de l'enregistrement
	 * @return	array	Champs de l'enregistrement
	 */
	function pdfInfo($uid) {
		$select = 'pdf_name, pdf_path, pdf_hash';
		$from = 'tx_atolflashpdfviewer_pdffiles';
		$where = 'uid='. intval($uid) . $this->cObj->enableFields('tx_atolflashpdfviewer_pdffiles');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		return $row;
	}

	/**
	 * Récupère les les informations sur un enregistrement de la table tx_dam
	 *
	 * @param 	int		$uid: Identifiant de l'enregistrement
	 * @return	array	Champs de l'enregistrement
	 */
	function pdfInfoDam($uid) {
		$select = 'file_name, file_path, file_hash';
		$from = 'tx_dam';
		$where = 'uid='. intval($uid) . $this->cObj->enableFields('tx_dam');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Pour que le résultat corresponde à celui de la fonction pdfInfo
		$pdf = array();
		$pdf['pdf_name'] = $row['file_name'];
		$pdf['pdf_path'] = $row['file_path'];
		$pdf['pdf_hash'] = $row['file_hash'];

		return $pdf;
	}

	/**
	 * Génère un lien pour visionner le fichier swf
	 *
	 * @param 	string		$text: Texte du lien
	 * @param 	int			$singlePid: Id de la page contenant la visionneuse
	 * @param 	string		$table: Table contenant l'enregistrement correspondant au fichier PDF
	 * @param 	int		$uid: Id de l'enregistrement
	 * @return	Lien
	 */
	function getSingleLink($text, $singlePid, $table, $uid) {
		$conf = array(
				'parameter' => $singlePid,
				'additionalParams' => '&tx_atolflashpdfviewer_pi1[table]=' . $table . '&tx_atolflashpdfviewer_pi1[uid]=' . $uid,
				'useCacheHash' => true
		);

		return $this->cObj->typoLink($text, $conf);
	}

	/**
	 * Rajoute le caractère '/' à la fin du chemin s'il n'est pas présent
	 *
	 * @param 	string		$path: Chemin
	 * @return	Chemin formaté
	 */
	function formatPath($path) {
		// On rajoute le caractère '/' à la fin du chemin s'il n'est pas présent
		if ($path != '' && (substr($path, -1) != '/' || substr($path, -1) != '/')) {
			$path .= '/';
		}

		return $path;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/pi1/class.tx_atolflashpdfviewer_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/pi1/class.tx_atolflashpdfviewer_pi1.php']);
}

?>