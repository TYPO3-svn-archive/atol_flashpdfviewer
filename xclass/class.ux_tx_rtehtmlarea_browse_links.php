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
 
class ux_tx_rtehtmlarea_browse_links extends tx_rtehtmlarea_browse_links {
	/******************************************************************
	 *
	 * File listing
	 *
	 ******************************************************************/
	/**
	 * For RTE: This displays all files from folder. No thumbnails shown
	 *
	 * @param	string		The folder path to expand
	 * @param	string		List of fileextensions to show
	 * @return	string		HTML output
	 */
	function expandPdf($expandFolder=0,$extensionList='')	{
		global $BACK_PATH;

		$expandFolder = $expandFolder ? $expandFolder : $this->expandFolder;
		$out='';
		if ($expandFolder && $this->checkFolder($expandFolder))	{

				// Create header for filelisting:
			$out.=$this->barheader($GLOBALS['LANG']->getLL('files').':');

				// Prepare current path value for comparison (showing red arrow)
			if (!$this->curUrlInfo['value'])	{
				$cmpPath='';
			} else {
				$linkParams = explode(':',$this->curUrlInfo['info'],3);
				$cmpPath = PATH_site . $linkParams[2];
			}

				// Create header element; The folder from which files are listed.
			$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);
			$picon='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/i/_icon_webfolders.gif','width="18" height="16"').' alt="" />';
			$picon.=htmlspecialchars(t3lib_div::fixed_lgd_cs(basename($expandFolder),$titleLen));
			$picon='<a href="#" onclick="return link_spec(\'pdf:tx_atolflashpdfviewer:'.t3lib_div::rawUrlEncodeFP(substr($expandFolder,strlen(PATH_site))).'\');">'.$picon.'</a>';
			if ($this->curUrlInfo['act'] == $this->key && $cmpPath == $expandFolder)	{
				$out.= '<img'.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/blinkarrow_left.gif', 'width="5" height="9"') . ' class="c-blinkArrowL" alt="" />';
			}
			$out.=$picon.'<br />';

				// Get files from the folder:
			if ($this->mode == 'wizard' && $this->act == $this->key) {
				$files = t3lib_div::get_dirs($expandFolder);
			} else {
				$files = t3lib_div::getFilesInDir($expandFolder, $extensionList, 1, 1);	// $extensionList='', $prependPath=0, $order='')
			}

			$c=0;
			$cc=count($files);
			if (is_array($files))	{
				foreach($files as $filepath)	{
					$c++;
					$fI=pathinfo($filepath);

					if ($this->mode == 'wizard' && $this->act == $this->key) {
						$filepath = $expandFolder.$filepath.'/';
						$icon = '<img' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/i/_icon_webfolders.gif', 'width="18" height="16"') . ' alt="" />';
					} else {
							// File icon:
						$icon = t3lib_BEfunc::getFileIcon(strtolower($fI['extension']));

							// Get size and icon:
						$size = ' (' . t3lib_div::formatSize(filesize($filepath)) . 'bytes)';
						$icon = '<img' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/fileicons/' . $icon . '', 'width="18" height="16"') . ' title="' . htmlspecialchars($fI['basename'] . $size) . '" alt="" />';
					}

						// If the listed file turns out to be the CURRENT file, then show blinking arrow:
					if (($this->curUrlInfo['act'] == 'tx_atolflashpdfviewer') && $cmpPath == $filepath) {
						$arrCol='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/blinkarrow_left.gif','width="5" height="9"').' class="c-blinkArrowL" alt="" />';
					} else {
						$arrCol='';
					}

						// Put it all together for the file element:
					$out.='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/join'.($c==$cc?'bottom':'').'.gif','width="18" height="16"').' alt="" />'.
							$arrCol.
							'<a href="#" onclick="return link_spec(\'pdf:tx_atolflashpdfviewer:'.preg_replace('/\//',';',t3lib_div::rawUrlEncodeFP(substr($fI['dirname'] . '/' . $fI['filename'],strlen(PATH_site)))).'\');">'.
							$icon.
							htmlspecialchars(t3lib_div::fixed_lgd_cs(basename($filepath),$titleLen)).
							'</a><br />';
				}
			}
		}
		return $out;
	}
}
?>