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

// L'interface à implémenter
require_once (PATH_t3lib.'interfaces/interface.t3lib_browselinkshook.php');
require_once (t3lib_extMgm::extPath('atol_flashpdfviewer').'class/class.ux_rtehtmlarea_pdfTree.php');
require_once(t3lib_extMgm::extPath('dam').'class.tx_dam_browse_media.php');


class tx_atolflashpdfviewer_browselinkshooks_dam implements t3lib_browseLinksHook {
  
	protected $invokingObject;
	protected $mode;
	protected $act;
	protected $bparams;
	protected $isHtmlAreaRTE;
	protected $isEnabled;
	protected $browserRenderObj; // DAM browser object
	private $key = 'tx_atolflashpdfviewer';
    
    // Initialisation (additionalParameters est un tableau vide)
    function init ($parentObject, $additionalParameters) {
        $this->invokingObject =& $parentObject;
		$this->mode =& $this->invokingObject->mode;
		$this->act =& $this->invokingObject->act;
		$this->bparams =& $this->invokingObject->bparams;
		$invokingObjectClass = get_class($this->invokingObject);
		$this->isHtmlAreaRTE = ($invokingObjectClass == 'tx_rtehtmlarea_browse_links' || $invokingObjectClass == 'ux_tx_rtehtmlarea_browse_links');
		$this->isEnabled = ((string)$this->mode == 'rte') && $this->isHtmlAreaRTE;
		
		if ($this->isEnabled) {
			$this->invokingObject->anchorTypes[] = 'tx_atolflashpdfviewer';
		}
    }
    
    // Onglets autorisés:
    // Pour être affiché, l'onglet doit se trouver dans ce tableau. C'est le moment d'ajouter l'id du nôtre (ou d'en enlever...) !
    function addAllowedItems ($currentlyAllowedItems) {
        $allowedItems =& $currentlyAllowedItems;
		if ($this->isEnabled) {
			
			$this->initMediaBrowser();
			$path = tx_dam::path_makeAbsolute($this->browserRenderObj->damSC->path);
			if (!$this->browserRenderObj->isReadOnlyFolder($path)) {
				$allowedItems[] = $this->key;
			}
		}
		return $allowedItems;
    }
    
    // Propriétés des onglets:
    // Pour être affiché, un onglet doit être configuré. 
    function modifyMenuDefinition ($menuDefinition) {
        $menuDef =& $menuDefinition;
		
		if ($this->isEnabled && in_array($this->key, $this->invokingObject->allowedItems)) {
			$menuDef[$this->key]['isActive'] = $this->invokingObject->act == $this->key;
			$menuDef[$this->key]['label'] =  'PDF';
			$menuDef[$this->key]['url'] = '#';
			$menuDef[$this->key]['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars('?act=' . $this->key . '&editorNo='.$this->invokingObject->editorNo.'&contentTypo3Language='.$this->invokingObject->contentTypo3Language.'&contentTypo3Charset='.$this->invokingObject->contentTypo3Charset).'\');return false;"';
		}
		
		return $menuDef;
    }
    
    // Contenu du nouvel onglet
    function getTab($act) {	
		$content = '';
		
		if ($this->isEnabled && $this->act == $this->key) {
			$content .= $this->invokingObject->addAttributesForm();
			$this->initMediaBrowser();
			$content .= $this->browserRenderObj->part_rte_linkfile();
			$this->addDAMStylesAndJSArrays();
		}
		
		return $content;
    }
    
    // Permet de récupérer d'éventuels paramètres
    function parseCurrentUrl ($href, $siteUrl, $info) {
		$linkParams = explode(':',$info['value']);
		
		if ($this->isEnabled && $linkParams[1] == 'tx_dam') {
			$info['act'] = $this->key;
			$href = preg_replace('/;/','/',$href) . '.pdf';
			$href = rawurldecode($href);
			$info['value'] = $href;
			$info['info'] = $href;
		}

		return $info;
    }
	
	protected function initMediaBrowser() {
		$this->browserRenderObj = t3lib_div::makeInstance('tx_dam_browse_media');
		$this->browserRenderObj->pObj =& $this->invokingObject;
		$this->invokingObject->browser =& $this->browserRenderObj;
			// init class browse_links
		$this->browserRenderObj->init();
		$this->browserRenderObj->mode =& $this->mode;
		$this->browserRenderObj->act =& $this->act;
		$this->browserRenderObj->bparams =& $this->bparams;
			// init the DAM object
		$this->browserRenderObj->initDAM();
			// processes MOD_SETTINGS
		$this->browserRenderObj->getModSettings();
			// Processes bparams parameter
		$this->browserRenderObj->processParams();
			// init the DAM selection after we've got the params
		$this->browserRenderObj->initDAMSelection();
		$this->browserRenderObj->allowedFileTypes = 'pdf';
	}

	protected function addDAMStylesAndJSArrays() {
		$this->invokingObject->doc->inDocStylesArray = array_merge($this->invokingObject->doc->inDocStylesArray, $this->browserRenderObj->doc->inDocStylesArray);
		$this->invokingObject->doc->JScodeArray = array_merge($this->invokingObject->doc->JScodeArray, $this->browserRenderObj->doc->JScodeArray);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/hooks/class.tx_atolflashpdfviewer_browselinkshooks_dam.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/hooks/class.tx_atolflashpdfviewer_browselinkshooks_dam.php']);
}

?>