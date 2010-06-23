<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <>
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
 
class ux_tx_dam_browse_media extends tx_dam_browse_media {
	/**
	 * Render list of files.
	 *
	 * @param	array		List of files. See t3lib_div::getFilesInDir
	 * @param	string		$mode EB mode: "db", "file", ...
	 * @return	string		HTML output
	 */
	function renderFileList($files, $mode='file', $act='') {
		global $LANG, $BACK_PATH, $TCA, $TYPO3_CONF_VARS;

		$out = '';


		// sorting selector
		
		// TODO move to scbase (see tx_dam_list_thumbs too)
		
		$allFields = tx_dam_db::getFieldListForUser('tx_dam');
		if (is_array($allFields) && count($allFields)) {
			$fieldsSelItems=array();
			foreach ($allFields as $field => $title) {
				$fL = is_array($TCA['tx_dam']['columns'][$field]) ? preg_replace('#:$#', '', $GLOBALS['LANG']->sL($TCA['tx_dam']['columns'][$field]['label'])) : '['.$field.']';
				$fieldsSelItems[$field] = t3lib_div::fixed_lgd_cs($fL, 15);
			}
			$sortingSelector = $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:labelSorting',1).' ';
			$sortingSelector .= t3lib_befunc::getFuncMenu($this->addParams, 'SET[txdam_sortField]', $this->damSC->MOD_SETTINGS['txdam_sortField'], $fieldsSelItems);
			
			if($this->damSC->MOD_SETTINGS['txdam_sortRev'])	{
				$params = (array)$this->addParams + array('SET[txdam_sortRev]' => '0');
				$href = t3lib_div::linkThisScript($params);
				$sortingSelector .=  '<button name="SET[txdam_sortRev]" type="button" onclick="self.location.href=\''.htmlspecialchars($href).'\'">'.
						'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/pil2up.gif','width="12" height="7"').' alt="" />'.
						'</button>';
			} else {
				$params = (array)$this->addParams + array('SET[txdam_sortRev]' => '1');
				$href = t3lib_div::linkThisScript($params);
				$sortingSelector .=  '<button name="SET[txdam_sortRev]" type="button" onclick="self.location.href=\''.htmlspecialchars($href).'\'">'.
						'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/pil2down.gif','width="12" height="7"').' alt="" />'.
						'</button>';
			}
			$sortingSelector = $this->getFormTag().$sortingSelector.'</form>';
		}

		$out .= $sortingSelector;
		$out .= $this->doc->spacer(20);

			// Listing the files:
		if (is_array($files) AND count($files))	{

			$displayThumbs = $this->displayThumbs();
			$dragdropImage = ($mode == 'rte' && ($act == 'dragdrop' ||$act == 'media_dragdrop'));
			$addAllJS = '';

				// Traverse the file list:
			$lines=array();
			foreach($files as $fI)	{

				if (!$fI['__exists']) {
					tx_dam::meta_updateStatus ($fI);
					continue;
				}

					// Create file icon:
				$iconFile = tx_dam::icon_getFileType($fI);
				$iconTag = tx_dam_guiFunc::icon_getFileTypeImgTag($fI);
				$iconAndFilename = $iconTag.htmlspecialchars(t3lib_div::fixed_lgd_cs($fI['file_title'], max($GLOBALS['BE_USER']->uc['titleLen'], 120)));


					// Create links for adding the file:
				if (strstr($fI['file_name_absolute'], ',') || strstr($fI['file_name_absolute'], '|'))	{	// In case an invalid character is in the filepath, display error message:
					$eMsg = $LANG->JScharCode(sprintf($LANG->getLL('invalidChar'), ', |'));
					$ATag_insert = '<a href="#" onclick="alert('.$eMsg.');return false;">';

					// If filename is OK, just add it:
				} else {

						// JS: insertElement(table, uid, type, filename, fpath, filetype, imagefile ,action, close)
					$onClick_params = implode (', ', array(
						"'".$fI['_ref_table']."'",
						"'".$fI['_ref_id']."'",
						"'".$mode."'",
						t3lib_div::quoteJSvalue($fI['file_name']),
						t3lib_div::quoteJSvalue($fI['_ref_file_path']),
						"'".$fI['file_type']."'",
						"'".$iconFile."'")
						);
						
					$titleAttrib = tx_dam_guiFunc::icon_getTitleAttribute($fI);

					if ($mode === 'rte') {
						if ($act === 'media') {
							$onClick = 'return link_folder(\''.t3lib_div::rawUrlEncodeFP(tx_dam::file_relativeSitePath($fI['_ref_file_path'])).'\');';
							$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						} elseif ($act === 'tx_atolflashpdfviewer') {
							$onClick = 'return link_spec(\'pdf:tx_dam:' . $fI['uid'] . '\');';
							$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						}
					} elseif (!$dragdropImage) {
						$onClick = 'return insertElement('.$onClick_params.');';
						$ATag_add = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						$addIcon = $ATag_add.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/plusbullet2.gif', 'width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" /></a>';
						
						$onClick = 'return insertElement('.$onClick_params.', \'\', 1);';
						$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						
						$addAllJS .= ($mode === 'rte')?'':'insertElement('.$onClick_params.'); ';
					}
				}
				
					// Create link to showing details about the file in a window:
				if ($fI['__exists']) {
					$infoOnClick = 'launchView(\'' . t3lib_div::rawUrlEncodeFP($fI['file_name_absolute']) . '\', \'\'); return false;';
					$ATag_info = '<a href="#" onclick="' . htmlspecialchars($infoOnClick) . '">';
					$info = $ATag_info.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/zoom2.gif', 'width="12" height="12"').' title="'.$LANG->getLL('info',1).'" alt="" /> '.$LANG->getLL('info',1).'</a>';
					$info = '<span class="button">'.$info.'</span>';
				} else {
					$info = '&nbsp;';
				}


					// Thumbnail/size generation:
				$clickThumb = '';
				if ($displayThumbs AND is_file($fI['file_name_absolute']) AND tx_dam_image::isPreviewPossible($fI))	{
					$addAttrib = array();
					$addAttrib['title'] = tx_dam_guiFunc::meta_compileHoverText($fI);
					$clickThumb = tx_dam_image::previewImgTag($fI, '', $addAttrib);
					$clickThumb = '<div class="clickThumb">'.$ATag_insert.$clickThumb.'</a>'.'</div>';
				} elseif ($displayThumbs) {
					$clickThumb = '<div style="width:68px"></div>';
				}
					// Image for drag & drop replaces the thumbnail
				if ($dragdropImage AND t3lib_div::inList($TYPO3_CONF_VARS['GFX']['imagefile_ext'], $fI['file_type']) AND is_file($fI['file_name_absolute']))	{
					if (t3lib_div::_GP('noLimit'))	{
						$maxW=10000;
						$maxH=10000;
					} else {
						$maxW=380;
						$maxH=500;
					}
					$IW = $fI['hpixels'];
					$IH = $fI['vpixels'];
					if ($IW>$maxW)	{
						$IH=ceil($IH/$IW*$maxW);
						$IW=$maxW;
					}
					if ($IH>$maxH)	{
						$IW=ceil($IW/$IH*$maxH);
						$IH=$maxH;
					}
					$clickThumb = '<img src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').substr($fI['file_name_absolute'],strlen(PATH_site)).'" width="'.$IW.'" height="'.$IH.'"' . ($this->defaultClass?(' class="'.$this->defaultClass.'"'):''). ' alt="'.$fI['alt_text'].'" title="'.$fI[$this->imgTitleDAMColumn].'" txdam="'. $fI['uid'] .'" />';
					$clickThumb = '<div class="clickThumb2">'.$clickThumb.'</div>';
				}

					// Show element:
				$lines[] = '
					<tr>
						<td valign="middle" class="bgColor4" nowrap="nowrap" style="min-width:20em">'.($dragdropImage?'':$ATag_insert).$iconAndFilename.'</a>'.'&nbsp;</td>
						<td valign="middle" class="bgColor4" width="1%">'.($mode == 'rte'?'':$addIcon).'</td>
						<td valign="middle" nowrap="nowrap" width="1%">'.$info.'</td>
					</tr>';


				$infoText = '';
				if ($this->getModSettings('extendedInfo')) {
					$infoText = tx_dam_guiFunc::meta_compileInfoData ($fI, 'file_name, file_size:filesize, _dimensions, caption:truncate:50, instructions', 'table');
					$infoText = str_replace('<table>', '<table border="0" cellpadding="0" cellspacing="1">', $infoText);
					$infoText = str_replace('<strong>', '<strong style="font-weight:normal;">', $infoText);
					$infoText = str_replace('</td><td>', '</td><td class="bgColor-10">', $infoText);
				}


				if (($displayThumbs || $dragdropImage) AND $infoText) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="3">
							<table border="0" cellpadding="0" cellspacing="0"><tr>
								<td valign="top">'.$clickThumb.'</td>
								<td valign="top" style="padding-left:1em">'.$infoText.'</td></tr>
							</table>
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				} elseif ($clickThumb OR $infoText) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="3" style="padding-left:22px">
							'.$clickThumb.$infoText.'
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				}

				$lines[] = '
						<tr>
							<td colspan="3"><div style="height:0.5em;"></div></td>
						</tr>';
			}

			// Wrap all the rows in table tags:
		$out .= '



		<!--
			File listing
		-->
				<table border="0" cellpadding="1" cellspacing="0" id="typo3-fileList">
					'.implode('',$lines).'
				</table>';
		}
		
		
		if ($addAllJS) {
			$label = $LANG->getLL('eb_addAllToList', true);
			$titleAttrib = ' title="'.$label.'"';
			$onClick = $addAllJS.'return true;';
			$ATag_add = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
			$addIcon = $ATag_add.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/plusbullet2.gif', 'width="18" height="16"').' alt="" />';
	
			$addAllButton = '<div class="addAllButton"><span class="button"'.$titleAttrib.'>'.$ATag_add.$addIcon.$label.'</a></span></div>';
			$out = $out.$addAllButton;
		}

			// Return accumulated content for filelisting:
		return $out;
	}
}
?>