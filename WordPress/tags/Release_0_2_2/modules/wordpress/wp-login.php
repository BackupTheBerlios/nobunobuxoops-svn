<?php
	include_once '../../mainfile.php';
	if ($xoopsUser) {
		$loc = XOOPS_URL."/modules/". basename(dirname(__FILE__));
	} else {
		$loc = XOOPS_URL."/user.php";
	}
	redirect_header($loc, 1, "This function is not avaiable in XOOPS Environment.");
?>
