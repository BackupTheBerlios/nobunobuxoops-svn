<?php 
include '../../../include/cp_header.php';
$wp_dir = basename( dirname( dirname( __FILE__ ) ) ) ;

xoops_cp_header();
if( file_exists( './mymenu.php' ) ) include( './mymenu.php' ) ;
echo "<h4>" . _AM_SYSUTIL_CONFIG . "</h4>";

echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
echo " - <b><a href='" . XOOPS_URL . "/modules/".$wp_dir."/admin/myblocksadmin.php'>" . _AM_SYSUTIL_BADMIN . "</a></b><br /><br />\n";
echo "</td><td class=\"odd\">";
echo " - <b><a href='" . XOOPS_URL . '/modules/sysutil/admin/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $xoopsModule -> getVar( 'mid' ) . "'>" . _AM_SYSUTIL_GENERALCONF . "</a></b><br /><br />\n";
echo "</td></tr></table>";
xoops_cp_footer();
?>
