<?php
$title = "Template &amp; file editing";

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
} 

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone','redirect','profile','error','warning','a','file');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

switch($action) {

case 'update':

	$standalone = 1;
	require_once("admin-header.php");

	if ($user_level < 3) {
		die('<p>You have no right to edit the template for this blog.<br />Ask for a promotion to your <a href="mailto:'.get_settings('admin_email').'">blog admin</a>. :)</p>');
	}

	$newcontent = stripslashes($_POST['newcontent']);
	$file = $_POST['file'];
	$f = fopen($file, 'w+');
	fwrite($f, $newcontent);
	fclose($f);

	$file = str_replace('../', '', $file);
	header("Location: templates.php?file=$file&a=te");
	exit();

break;

default:
// Temporaly Disable
	include_once '../../../mainfile.php';
	$loc = XOOPS_URL."/modules/". basename(dirname(dirname(__FILE__)))."/wp-admin/post.php";
	redirect_header($loc, 1, "This functions is temporaly disabled.");
//
	require_once('admin-header.php');
	if ($user_level <= 3) {
		die('<p>You have no right to edit the template for this blog.<br>Ask for a promotion to your <a href="mailto:'.get_settings('admin_email').'">blog admin</a>. :)</p>');
	}

	if ('' == $file) {
		$file = 'index.php';
	}
	
	if ('..' == substr($file,0,2))
		die ('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.');
	
	if (':' == substr($file,1,1))
		die ('Sorry, can&#8217;t call files with their real path.');

	if ('/' == substr($file,0,1))
		$file = '.' . $file;
	
	$file = stripslashes($file);
	$file = '../' . $file;
	
	if (!is_file($file))
		$error = 1;

	if ((substr($file,0,2) == 'wp') and (substr($file,-4,4) == '.php') and ($file != 'wp.php'))
		$warning = ' &#8212; this is a WordPress file, be careful when editing it!';
	
	if (!$error) {
		$f = fopen($file, 'r');
		$content = fread($f, filesize($file));
		$content = htmlspecialchars($content);
//		$content = str_replace("</textarea","&lt;/textarea",$content);
	}

	?>
 <div class="wrap"> 
  <?php
	echo "Editing <strong>$file</strong> $warning";
	if ('te' == $a)
		echo _LANG_WAT_EDITED_SUCCESS;
	
	if (!$error) {
	?> 
  <form name="template" action="templates.php" method="post"> 
     <textarea cols="80" rows="20" style="width:100%; font-family: 'Courier New', Courier, monopace; font-size:small;" name="newcontent" tabindex="1"><?php echo $content ?></textarea> 
     <input type="hidden" name="action" value="update" /> 
     <input type="hidden" name="file" value="<?php echo $file ?>" /> 
     <br /> 
     <?php
		if (is_writeable($file)) {
			echo "<input type=\"submit\" name=\"submit\" value=\""._LANG_WAT_UPTEXT_TEMP."\" tabindex=\"2\" />";
		} else {
			echo "<input type=\"button\" name=\"oops\" value=\"("._LANG_WAT_FILE_CHMOD.")\" tabindex=\"2\" />";
		}
		?> 
   </form> 
  <?php
	} else {
		echo _LANG_WAT_OOPS_EXISTS;
	}
	?> 
</div> 
<div class="wrap">
<h2>Note : </h2>
  <p><?php echo _LANG_WAT_OTHER_FILE; ?></p> 
  <p><?php echo _LANG_WAT_TYPE_HERE; ?></p>
  <form name="file" action="templates.php" method="get"> 
    <input type="text" name="file" /> 
    <input type="submit" name="submit" value="go" /> 
  </form> 
  <p><?php echo _LANG_WAT_FTP_CLIENT; ?></p> 
</div> 
<?php

break;
}

include("admin-footer.php");
?> 