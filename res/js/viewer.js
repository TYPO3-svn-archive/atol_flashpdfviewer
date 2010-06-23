var params = {
	menu: "false",
	bgcolor: '#efefef',
	allowFullScreen: 'true'
};

var attributes = {
	id: 'tx_atolflashpdfviewer_viewer'
};

swfobject.embedSWF('typo3conf/ext/atol_flashpdfviewer/res/swf/zviewer.swf',
	'tx_atolflashpdfviewer_viewer', flashvars.viewerWidth, flashvars.viewerHeight, '9.0.45',
	'typo3conf/ext/atol_flashpdfviewer/res/swf/expressInstall.swf',
	flashvars,
	params,
	attributes
);