<?php
require_once('admin.php');

$title = 'Options';
$this_file = 'options.php';
$parent_file = 'options.php';

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone', 'option_group_id');
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

require_once("optionhandler.php");
$non_was_selected = 0;
if ($option_group_id == '') {
    $option_group_id = 1;
    $non_was_selected = 1;
} else {
    $option_group_id = intval($option_group_id);
}
$message = "";

switch($action) {

case "update":
	$standalone = 0;
	wp_refcheck("/wp-admin");
    $any_changed = 0;
    
    // iterate through the list of options in this group
    // pull the vars from the post
    // validate ranges etc.
    // update the values
    $options = $wpdb->get_results("SELECT {$wpdb->options[$wp_id]}.option_id, option_name, option_type, option_value, option_admin_level "
                                  . "FROM {$wpdb->options[$wp_id]} "
                                  . "LEFT JOIN {$wpdb->optiongroup_options[$wp_id]} ON {$wpdb->options[$wp_id]}.option_id = {$wpdb->optiongroup_options[$wp_id]}.option_id "
                                  . "WHERE group_id = $option_group_id "
                                  . "ORDER BY seq");
    if ($options) {
        foreach ($options as $option) {
            // should we even bother checking?
            if ($user_level >= $option->option_admin_level) {
                $this_name = $option->option_name;
                $old_val = stripslashes($option->option_value);
                $new_val = $_POST[$this_name];

                if ($new_val != $old_val) {
                    // get type and validate
                    $msg = validate_option($option, $this_name, $new_val);
                    if ($msg == '') {
                        //no error message
                        $result = $wpdb->query("UPDATE {$wpdb->options[$wp_id]} SET option_value = '$new_val' WHERE option_id = $option->option_id");
                        if (!$result) {
                            $db_errors .= " SQL error while saving $this_name. ";
                        } else {
                            ++$any_changed;
                        }
                    } else {
                        $validation_message .= $msg;
                    }
                }
            }
        } // end foreach
        unset($cache_settings[$wp_id]); // so they will be re-read
//      get_settings('siteurl'); // make it happen now
    } // end if options
    if ($any_changed) {
        $message = $any_changed ._LANG_WOP_SETTING_SAVED;
    }
    
    if (($dB_errors != '') || ($validation_message != '')) {
        if ($message != '') {
            $message .= '<br />and ';
        }
        $message .= $dB_errors . '<br />' . $validation_message;
    }
        
    //break; //fall through

default:
	$standalone = 0;
	include_once("./admin-header.php");
	if ($user_level <= 3) {
		die("You have no right to edit the options for this blog.<br />Ask for a promotion from your <a href=\"mailto:".get_settings('admin_email')."\">blog admin</a> :)");
	}
?>

<?php
if ($non_was_selected) { // no group pre-selected, display opening page
?>
<div class="wrap">
<dl>
<?php
    //iterate through the available option groups. output them as a definition list.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM {$wpdb->optiongroups[$wp_id]} ORDER BY group_id");
    foreach ($option_groups as $option_group) {
        echo("  <dt><a href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"".replace_constant($option_group->group_desc)."\">{$option_group->group_name}</a></dt>\n");
        $current_long_desc = $option_group->group_longdesc;
        if ($current_long_desc == '') {
            $current_long_desc = '<br />'._LANG_WOP_NO_HELPS;
        }
        echo("  <dd>".replace_constant($option_group->group_desc).": $current_long_desc</dd>\n");
    } // end for each group
?>
  <dt><a href="options-permalink.php"><?php echo _LANG_WOP_PERM_LINKS; ?></a></dt>
  <dd><?php echo _LANG_WOP_PERM_CONFIG; ?></dd>
</dl>
</div>
<?php    

} else { //there was a group selected.

?>
<br clear="all" />
<?php if($messase) { ?>
<div class="wrap"><?php echo $message; ?></div>
<?php } ?>
<?php
	include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	$form = new XoopsThemeForm($current_desc, "form", $this_file);
    //Now display all the options for the selected group.
    $options = $wpdb->get_results("SELECT {$wpdb->options[$wp_id]}.option_id, option_name, option_type, option_value, option_width, option_height, option_description, option_admin_level "
                                  . "FROM {$wpdb->options[$wp_id]} "
                                  . "LEFT JOIN {$wpdb->optiongroup_options[$wp_id]} ON {$wpdb->options[$wp_id]}.option_id = {$wpdb->optiongroup_options[$wp_id]}.option_id "
                                  . "WHERE group_id = $option_group_id "
                                  . "ORDER BY seq");
    if ($options) {
        foreach ($options as $option) {
        	$form->addElement(get_option_formElement($option, ($user_level >= $option->option_admin_level)));
        }
    }
	$form->addElement(new XoopsFormButton("", "Update", _LANG_WOP_SUBMIT_TEXT, "submit"));
	$form->addElement(new XoopsFormHidden("option_group_id", $option_group_id));
	$form->addElement(new XoopsFormHidden("action", "update"));
	$form->display();

?>
<div class="wrap">
<?php
    if ($current_long_desc != '') {
        echo($current_long_desc);
    } else {
?>
  <p><?php echo _LANG_WOP_NO_HELPS; ?></p>
<?php
    }
?>
</div>
<?php
} // end else a group was selected
break;
} // end switch

include("admin-footer.php") ?>