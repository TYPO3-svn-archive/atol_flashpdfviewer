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


class tx_atolflashpdfviewer_browselinkshooks implements t3lib_browseLinksHook {
    // Sauvegarde locale du cObj parent
    protected $pObj;
	private $key = 'tx_atolflashpdfviewer';
    
    // Initialisation (additionalParameters est un tableau vide)
    function init ($parentObject, $additionalParameters) {
        $this->pObj = $parentObject;
		$this->expandFolder = t3lib_div::_GP('expandFolder');
    }
    
    // Onglets autorisés:
    // Pour être affiché, l'onglet doit se trouver dans ce tableau. C'est le moment d'ajouter l'id du nôtre (ou d'en enlever...) !
    function addAllowedItems ($currentlyAllowedItems) {
        $currentlyAllowedItems[] = $this->key;
        
        return $currentlyAllowedItems;
    }
    
    // Propriétés des onglets:
    // Pour être affiché, un onglet doit être configuré. 
    function modifyMenuDefinition ($menuDefinition) {
        $key = $this->key;
        $menuDefinition[$key]['isActive'] = $this->pObj->act == $key;
        $menuDefinition[$key]['label'] = "PDF";
        $menuDefinition[$key]['url'] = '#';
        $menuDefinition[$key]['addParams'] = 'onclick="jumpToUrl(\'?act='.$key.'&editorNo='.$this->pObj->editorNo.'&contentTypo3Language='.$this->pObj->contentTypo3Language.'&contentTypo3Charset='.$this->pObj->contentTypo3Charset.'\');return false;"';                    
            
        
        return $menuDefinition;
    }
    
    // Contenu du nouvel onglet
    function getTab($act) {
		if ($act != $this->key) {
			return false;
		}
		
		if (isset($this->pObj->classesAnchorJSOptions)) {
			$this->pObj->classesAnchorJSOptions[$act]=@$this->pObj->classesAnchorJSOptions['page'];
		}
		
		$content = '';
		
		$content .= $this->pObj->addAttributesForm();

		$foldertree = t3lib_div::makeInstance('ux_rtehtmlarea_pdfTree');
		$tree=$foldertree->getBrowsableTree();
		
		if (!$this->pObj->curUrlInfo['value'] || $this->pObj->curUrlInfo['act']!='file')	{
			$cmpPath='';
		} elseif (substr(trim($this->pObj->curUrlInfo['info']),-1)!='/')	{
			$cmpPath=PATH_site.dirname($this->pObj->curUrlInfo['info']).'/';
			if (!isset($this->expandFolder)) $this->expandFolder = $cmpPath;
		} else {
			$cmpPath=PATH_site.$this->pObj->curUrlInfo['info'];
		}
	
		list(,,$specUid) = explode('_',$this->PM);
		$files = $this->pObj->expandPdf($foldertree->specUIDmap[$specUid],'pdf');

		$content.= '
			<!--
			Wrapper table for folder tree / file list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-linkFiles">
				<tr>
					<td class="c-wCell" valign="top">'.$this->pObj->barheader('Arborescence:').$tree.'</td>
					<td class="c-wCell" valign="top">'.$files.'</td>
				</tr>
			</table>
			';

		return $content;
    }
    
    // Permet de récupérer d'éventuels paramètres
    function parseCurrentUrl ($href, $siteUrl, $info) {
		$linkParams = explode(':',$info['value']);
		
		if ($linkParams[1] == $this->key) {
			$info['act'] = $this->key;
			$href = preg_replace('/;/','/',$href) . '.pdf';
			$href = rawurldecode($href);
			$info['value'] = $href;
			$info['info'] = $href;
		}
		
		return $info;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/hooks/class.tx_atolflashpdfviewer_browselinkshooks.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/atol_flashpdfviewer/hooks/class.tx_atolflashpdfviewer_browselinkshooks.php']);
}

?>