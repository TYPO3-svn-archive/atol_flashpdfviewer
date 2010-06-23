<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	t3lib_div::loadTCA('tt_content');
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	$TCA["tt_content"]["types"]["list"]["subtypes_addlist"][$_EXTKEY."_pi1"]="pi_flexform";

	t3lib_extMgm::addPlugin(
		array('LLL:EXT:atol_flashpdfviewer/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY . '_pi1', t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'),
		'list_type'
	);

	t3lib_extMgm::addStaticFile($_EXTKEY,'static/default/', 'Default');
	
	if (t3lib_extMgm::isLoaded('dam')) {
		t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexformDAM_ds.xml');
	} else {
		t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');
	}
	
	$TCA['tx_atolflashpdfviewer_pdffiles'] = array (
		'ctrl' => array (
			'title' 	=> 'LLL:EXT:atol_flashpdfviewer/locallang_db.xml:tx_atolflashpdfviewer_pdffiles',		
			'label'     => 'uid',	
			'tstamp'    => 'tstamp',
			'crdate'    => 'crdate',
			'cruser_id' => 'cruser_id',
			'versioningWS' => TRUE, 
			'origUid' => 't3_origuid',
			'languageField'            => 'sys_language_uid',	
			'transOrigPointerField'    => 'l10n_parent',	
			'transOrigDiffSourceField' => 'l10n_diffsource',	
			'sortby' => 'sorting',	
			'delete' => 'deleted',	
			'enablecolumns' => array (		
				'disabled' => 'hidden',	
				'starttime' => 'starttime',	
				'endtime' => 'endtime',	
				'fe_group' => 'fe_group',
			),
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_atolflashpdfviewer_pdffiles.gif',
		),
	);
?>