<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}

	t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_atolflashpdfviewer_pi1.php', '_pi1', 'list_type', 1);
	
	$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['atol_flashpdfviewer']);
	
	if (t3lib_extMgm::isLoaded('dam') && $confArr['useDamLinks']) {
			// xclass
		$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_media.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'xclass/class.ux_tx_dam_browse_media.php';
		
			// hooks
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] = t3lib_extMgm::extPath($_EXTKEY) . 'hooks/class.tx_atolflashpdfviewer_browselinkshooks_dam.php:tx_atolflashpdfviewer_browselinkshooks_dam';
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.browse_links.php']['browseLinksHook'][] = t3lib_extMgm::extPath($_EXTKEY) . 'hooks/class.tx_atolflashpdfviewer_browselinkshooks_dam.php:tx_atolflashpdfviewer_browselinkshooks_dam';
	} else {
			//xclass
		$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'xclass/class.ux_tx_rtehtmlarea_browse_links.php';
		
			// hooks
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] = t3lib_extMgm::extPath($_EXTKEY) . 'hooks/class.tx_atolflashpdfviewer_browselinkshooks.php:tx_atolflashpdfviewer_browselinkshooks';
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.browse_links.php']['browseLinksHook'][] = t3lib_extMgm::extPath($_EXTKEY) . 'hooks/class.tx_atolflashpdfviewer_browselinkshooks.php:tx_atolflashpdfviewer_browselinkshooks';
	}
	
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typolinkLinkHandler']['pdf'] = t3lib_extMgm::extPath($_EXTKEY) . 'hooks/class.tx_atolflashpdfviewer_handler.php:&tx_atolflashpdfviewer_handler';
?>