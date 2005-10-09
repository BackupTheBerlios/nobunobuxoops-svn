<?php
ob_start('sysutil_autologin_rewrite');
if (strpos($_SERVER['REQUEST_URI'],'/user.php')===0) {
	if (empty($_GET['op'])) {
		header('Location: '.XOOPS_URL.'/modules/sysutil/');
		exit();
	} else if ($_GET['op'] == 'logout') {
		header('Location: '.XOOPS_URL.'/modules/sysutil/index.php?op=logout');
		exit();
	}
} else if (strpos($_SERVER['REQUEST_URI'],'/register.php')===0) {
    $config_handler =& xoops_gethandler('config');
    $xoopsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    if (!$xoopsConfigUser['allow_register']) {
	    redirect_header('index.php', 6, _US_NOREGISTER);
	    exit();
	}
}

define('SYSUTIL_AUTOLOGIN_LIFETIME', sysutil_get_xoops_option('sysutil', 'sysutil_login_lifetime')*3600);

if (empty($_SESSION['xoopsUserId'])) {
	if(isset($_COOKIE['autologin_uname']) && isset($_COOKIE['autologin_pass'])) {
		$confirm_url = '/modules/sysutil/index.php';
		if( ! empty( $_POST ) ) {
			$_SESSION['AUTOLOGIN_POST'] = $_POST ;
			$_SESSION['AUTOLOGIN_REQUEST_URI'] = $_SERVER['REQUEST_URI'] ;
			redirect_header( XOOPS_URL . $confirm_url. '?op=confirm' , 0 , '&nbsp;' ) ;
		} else if( ! empty( $_SERVER['QUERY_STRING'] ) && substr( $_SERVER['SCRIPT_NAME'] , -strlen($confirm_url) ) != $confirm_url) {
			$_SESSION['AUTOLOGIN_REQUEST_URI'] = $_SERVER['REQUEST_URI'] ;
			redirect_header( XOOPS_URL .  $confirm_url. '?op=confirm' , 0 , '&nbsp;' ) ;
		}
		$member_handler =& xoops_gethandler('member');
		$myts =& MyTextSanitizer::getInstance();
		$uname = $myts->stripSlashesGPC($_COOKIE['autologin_uname']);
		$pass = $myts->stripSlashesGPC($_COOKIE['autologin_pass']);
		$uname4sql = addslashes( $uname ) ;
		$criteria = new CriteriaCompo(new Criteria('uname', $uname4sql ));
		$user_handler =& xoops_gethandler('user');
		$users =& $user_handler->getObjects($criteria, false);
		if( empty( $users ) || count( $users ) != 1 ) {
			$user = false ;
		} else {
			$user = $users[0] ;
			$old_limit = time() - SYSUTIL_AUTOLOGIN_LIFETIME ; // 1 week default
			list( $old_Ynj , $old_encpass ) = explode( ':' , $pass ) ;
			if( strtotime( $old_Ynj ) < $old_limit || md5( $user->getVar('pass') . $old_Ynj ) != $old_encpass ) {
				$user = false ;
			}
		}
		unset( $users ) ;
		if (false != $user && $user->getVar('level') > 0) {
			// update time of last login
			$user->setVar('last_login', time());
			if (!$member_handler->insertUser($user, true)) {
			}
			$_SESSION['xoopsUserId'] = $user->getVar('uid');
			$_SESSION['xoopsUserGroups'] = $user->getGroups();
			$user_theme = $user->getVar('theme');
			if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
				$_SESSION['xoopsUserTheme'] = $user_theme;
			}
			$expire = time() + SYSUTIL_AUTOLOGIN_LIFETIME ; // 1 week default
			setcookie('autologin_uname', $user->getVar('uname'), $expire, $xoops_cookie_path, '', 0);
			$Ynj = date( 'Y-n-j' ) ;
			setcookie('autologin_pass', $Ynj . ':' . md5( $user->getVar('pass') . $Ynj ) , $expire, $xoops_cookie_path, '', 0);
		} else {
			setcookie('autologin_uname', '', time() - 3600, $xoops_cookie_path, '', 0);
			setcookie('autologin_pass', '', time() - 3600, $xoops_cookie_path, '', 0);
			header('Location: '.XOOPS_URL.'/modules/sysutil/');
			exit();
		}
	}
} else {
	if (isset($_COOKIE['autologin_rememberme'])&&intval($_COOKIE['autologin_rememberme'])==1){
		$member_handler =& xoops_gethandler('member');
        $xoopsUser =& $member_handler->getUser($_SESSION['xoopsUserId']);
        if (is_object($xoopsUser)) {
			$expire = time() +SYSUTIL_AUTOLOGIN_LIFETIME ; // 1 week default
			setcookie('autologin_uname', $xoopsUser->getVar('uname'), $expire, $xoops_cookie_path, '', 0);
			$Ynj = date( 'Y-n-j' ) ;
			setcookie('autologin_pass', $Ynj . ':' . md5( $xoopsUser->getVar('pass') . $Ynj ) , $expire, $xoops_cookie_path,'', 0);
			setcookie('autologin_rememberme', '',  time() - 3600, $xoops_cookie_path, '', 0);
 		}
	}
}

function sysutil_autologin_rewrite($s)
{
	$s = str_replace(XOOPS_URL.'/user.php?op=logout', XOOPS_URL.'/modules/sysutil/index.php?op=logout',$s);
	return $s;
}
?>
