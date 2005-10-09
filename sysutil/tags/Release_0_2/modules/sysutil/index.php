<?php
$xoopsOption['pagetype'] = 'user';
require_once ('../../mainfile.php');
$op = '';
if ( isset($_POST['op']) ) {
    $op = trim($_POST['op']);
} elseif ( isset($_GET['op']) ) {
    $op = trim($_GET['op']);
}

switch($op) {
    case 'login':
        $xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace( '?http://[^/]+(/.*)$?' , "$1" , XOOPS_URL ) ;
        if( $xoops_cookie_path == XOOPS_URL ) $xoops_cookie_path = '/' ;
        if (!empty($_POST['rememberme'])) {
            setcookie('autologin_rememberme', 1, time() +180, $xoops_cookie_path, '', 0);
        } else {
            setcookie('autologin_rememberme', 0, time() +180, $xoops_cookie_path, '', 0);
        }
        include_once XOOPS_ROOT_PATH.'/include/checklogin.php';
        exit();
        break;
    case 'logout':
        $message = '';
        $_SESSION = array();
        session_destroy();
        if ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
            setcookie($xoopsConfig['session_name'], '', time()- 3600, '/',  '', 0);
        }
        $xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace( '?http://[^/]+(/.*)$?' , "$1" , XOOPS_URL ) ;
        if( $xoops_cookie_path == XOOPS_URL ) $xoops_cookie_path = '/' ;
        setcookie('autologin_uname', '', time() - 3600, $xoops_cookie_path, '', 0);
        setcookie('autologin_pass', '', time() - 3600, $xoops_cookie_path, '', 0);
        setcookie('autologin_uname', '', time() - 3600, '/', '', 0); //
        setcookie('autologin_pass', '', time() - 3600, '/', '', 0); // for older auto login hacks (should be removed)
        // clear entry from online users table
        if (is_object($xoopsUser)) {
            $online_handler =& xoops_gethandler('online');
            $online_handler->destroy($xoopsUser->getVar('uid'));
        }
        $message = _US_LOGGEDOUT.'<br />'._US_THANKYOUFORVISIT;
        redirect_header(XOOPS_URL.'/', 1, $message);
        exit();
        break;
    case 'confirm':
    	// security check
		if(!isset( $_SESSION['AUTOLOGIN_REQUEST_URI'])) exit ;
		// get URI
		$url = $_SESSION['AUTOLOGIN_REQUEST_URI'] ;
		unset($_SESSION['AUTOLOGIN_REQUEST_URI']) ;
		if( preg_match('/javascript:/si', $url) ) exit ; // black list of url
		$url4disp = preg_replace("/&amp;/i", '&', htmlspecialchars($url, ENT_QUOTES));

		if( isset( $_SESSION['AUTOLOGIN_POST'] ) ) {
			// posting confirmation
			$old_post = $_SESSION['AUTOLOGIN_POST'] ;
			unset( $_SESSION['AUTOLOGIN_POST'] ) ;

			$hidden_str = '' ;
			foreach( $old_post as $k => $v ) {
				$hidden_str .= "\t".'      <input type="hidden" name="'.htmlspecialchars($k,ENT_QUOTES).'" value="'.htmlspecialchars($v,ENT_QUOTES).'" />'."\n" ;
			}
			echo '
			<html><head><meta http-equiv="Content-Type" content="text/html; charset='._CHARSET.'" />
			<title>'.$xoopsConfig['sitename'].'</title>
			</head>
			<body>
			<div style="text-align:center; background-color: #EBEBEB; border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-right: 1px solid #AAAAAA; border-bottom: 1px solid #AAAAAA; font-weight : bold;">
			  <h4>'._RETRYPOST.'</h4>
			  <form action="'.$url4disp.'" method="POST">
			  '.$hidden_str.'
			    <input type="submit" name="timeout_repost" value="'._SUBMIT.'" />
			  </form>
			</div>
			</body>
			</html>
			' ;
			exit ;
		} else {
			// just redirecting
			$time = 1 ;
			// $message = empty( $message ) ? _TAKINGBACK : $message ;
			$message = _TAKINGBACK ;
			echo '
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset='._CHARSET.'" />
			<meta http-equiv="Refresh" content="'.$time.'; url='.$url4disp.'" />
			<title>'.$xoopsConfig['sitename'].'</title>
			</head>
			<body>
			<div style="text-align:center; background-color: #EBEBEB; border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-right: 1px solid #AAAAAA; border-bottom: 1px solid #AAAAAA; font-weight : bold;">
			  <h4>'.$message.'</h4>
			  <p>'.sprintf(_IFNOTRELOAD, $url4disp).'</p>
			</div>
			</body>
			</html>
			' ;
			exit ;
		}
    	break;
    default:
        if ( !$xoopsUser ) {
            $config_handler =& xoops_gethandler('config');
            $xoopsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
            $xoopsOption['template_main'] = 'sysutil_userform.html';
            include XOOPS_ROOT_PATH.'/header.php';
            $xoopsTpl->assign('lang_login', _LOGIN);
            $xoopsTpl->assign('lang_username', _USERNAME);
            if (isset($_COOKIE[$xoopsConfig['usercookie']])) {
                $xoopsTpl->assign('usercookie', $_COOKIE[$xoopsConfig['usercookie']]);
            }
            if (isset($_GET['xoops_redirect'])) {
                $xoopsTpl->assign('redirect_page', htmlspecialchars(trim($_GET['xoops_redirect']), ENT_QUOTES));
            }
            $xoopsTpl->assign('allow_register', $xoopsConfigUser['allow_register']);
            $xoopsTpl->assign('lang_password', _PASSWORD);
            $xoopsTpl->assign('lang_rememberme', _SYSUTIL_REMEMBERME);
            $xoopsTpl->assign('lang_notregister', _US_NOTREGISTERED);
            $xoopsTpl->assign('lang_lostpassword', _US_LOSTPASSWORD);
            $xoopsTpl->assign('lang_noproblem', _US_NOPROBLEM);
            $xoopsTpl->assign('lang_youremail', _US_YOUREMAIL);
            $xoopsTpl->assign('lang_sendpassword', _US_SENDPASSWORD);
            include XOOPS_ROOT_PATH.'/footer.php';
        } elseif ( $xoopsUser ) {
            header('Location: '.XOOPS_URL.'/userinfo.php?uid='.$xoopsUser->getVar('uid'));
        }
}
?>
