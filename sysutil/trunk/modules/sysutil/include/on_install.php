<?php
function xoops_module_install_sysutil(&$module) {
    $gperm_handler =& xoops_gethandler('groupperm');
    $mperm =& $gperm_handler->create();
    $mperm->setVar('gperm_groupid', XOOPS_GROUP_ANONYMOUS);
    $mperm->setVar('gperm_itemid', $module->getVar('mid'));
    $mperm->setVar('gperm_name', 'module_read');
    $mperm->setVar('gperm_modid', 1);
    $gperm_handler->insert($mperm);
    unset($mperm);

    $blocks =& XoopsBlock::getByModule($module->getVar('mid'), false);
    foreach ($blocks as $blc) {
        $bperm =& $gperm_handler->create();
        $bperm->setVar('gperm_groupid', XOOPS_GROUP_ANONYMOUS);
        $bperm->setVar('gperm_itemid', $blc);
        $bperm->setVar('gperm_name', 'block_read');
        $bperm->setVar('gperm_modid', 1);
        $gperm_handler->insert($bperm);
        unset($bperm);
    }
	unset($blocks);
	return true;
}
?>
