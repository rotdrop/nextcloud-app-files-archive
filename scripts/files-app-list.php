<?php
$config = \OC::$server->getConfig();
$userSession = \OC::$server->getUserSession();

$showgridview = $config->getUserValue($userSession->getUser()->getUID(), 'files', 'show_grid', true);
$isIE = OC_Util::isIe();

$tmpl = new OCP\Template('files_archive', 'file-list', '');

// gridview not available for ie
$tmpl->assign('showgridview', $showgridview && !$isIE);

$tmpl->printPage();
