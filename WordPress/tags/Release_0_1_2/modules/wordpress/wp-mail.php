<?php
require(dirname(__FILE__) . '/wp-config.php');

require_once(ABSPATH.WPINC.'/class-pop3.php');


timer_start();

$use_cache = 1;
$output_debugging_info = 0;	# =1 if you want to output debugging info

$time_difference = get_settings('time_difference');

//echo "Server TimeZone is ".date('O')."<br />";
//Get Server Time Zone
// If Server Time Zone is not collect, Please comment out following line;
$server_timezone = date("O");
// If Server Time Zone is not collect, Please uncomment following line and set collect timezone value;
// $server_timezone = "+0900"; //This is a sample value for JST+0900

$server_timezone = $server_timezone/100;
$weblog_timezone = $server_timezone + $time_difference;

error_reporting(2037);

$pop3 = new POP3();

if(!$pop3->connect($mailserver_url, $mailserver_port)) {
	echo "Ooops $pop3->ERROR <br />\n";
	exit;
}

$Count = $pop3->login($mailserver_login, $mailserver_pass);
if((!$Count) || ($Count == -1)) {
	echo "<h1>Login Failed: $pop3->ERROR</h1>\n";
	$pop3->quit();
	exit;
}


// ONLY USE THIS IF YOUR PHP VERSION SUPPORTS IT!
//register_shutdown_function($pop3->quit());

for ($iCount=1; $iCount<=$Count; $iCount++) {

	$MsgOne = $pop3->get($iCount);
	if((!$MsgOne) || (gettype($MsgOne) != 'array')) {
		echo "oops, $pop3->ERROR<br />\n";
		$pop3->quit();
		exit;
	}

	$content = '';
	$content_type = '';
	$boundary = '';
	$bodysignal = 0;
	$dmonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	while ( list ( $lineNum,$line ) = each ($MsgOne) ) {
		if (strlen($line) < 3) {
			$bodysignal = 1;
		}
		if ($bodysignal) {
			$content .= $line;
		} else {
			if (preg_match('/Content-Type: /', $line)) {
				$content_type = trim($line);
				$content_type = substr($content_type, 14, strlen($content_type)-14);
				$content_type = explode(';', $content_type);
				$content_type = $content_type[0];
			}
			if (($content_type == 'multipart/alternative') && (preg_match('/boundary="/', $line)) && ($boundary == '')) {
				$boundary = trim($line);
				$boundary = explode('"', $boundary);
				$boundary = $boundary[1];
			}
			if (preg_match('/Subject: /', $line)) {
				$subject = trim($line);
				$subject = substr($subject, 9, strlen($subject)-9);
				if (function_exists('mb_decode_mimeheader')) {
					$subject = mb_decode_mimeheader($subject);
				}
				if ($use_phoneemail) {
					$subject = explode($phoneemail_separator, $subject);
					$subject = trim($subject[0]);
					echo $subject."<br/>";
				}
				if (!ereg($subjectprefix, $subject)) {
					continue;
				}
			}
			if (preg_match('/Date: /', $line)) { // of the form '20 Mar 2002 20:32:37'
				$ddate = trim($line);
				$ddate = str_replace('Date: ', '', $ddate);
				if (strpos($ddate, ',')) {
					$ddate = trim(substr($ddate, strpos($ddate, ',')+1, strlen($ddate)));
				}
				$date_arr = explode(' ', $ddate);
				$date_time = explode(':', $date_arr[3]);
				
				$ddate_H = $date_time[0];
				$ddate_i = $date_time[1];
				$ddate_s = $date_time[2];
				
				$ddate_m = $date_arr[1];
				$ddate_d = $date_arr[0];
				$ddate_Y = $date_arr[2];
				
				$mail_timezone = trim(ereg_replace("\([^)]*\)","",$date_arr[4]))/100;
//				echo "Email TimeZone is {$date_arr[4]}<br />";
				$mail_time_difference = $weblog_timezone - $mail_timezone;
				for ($i=0; $i<12; $i++) {
					if ($ddate_m == $dmonths[$i]) {
						$ddate_m = $i+1;
					}
				}
				$ddate_U = mktime($ddate_H, $ddate_i, $ddate_s, $ddate_m, $ddate_d, $ddate_Y);
				$ddate_U = $ddate_U + ($mai_time_difference * 3600);
				$post_date = date('Y-m-d H:i:s', $ddate_U);
			}
		}
	}

	$ddate_today = time() + ($time_difference * 3600);
	$ddate_difference_days = ($ddate_today - $ddate_U) / 86400;


	# starts buffering the output
	ob_start();
	if ($ddate_difference_days > 14) {
		echo 'Too old<br />';
		continue;
	}
	if (preg_match('/'.$subjectprefix.'/', $subject)) {

		$userpassstring = '';

		echo '<div style="border: 1px dashed #999; padding: 10px; margin: 10px;">';
		echo "<p><b>$iCount</b></p><p><b>Subject: </b>$subject</p>\n";

		$subject = trim(str_replace($subjectprefix, '', $subject));

		if ($content_type == 'multipart/alternative') {
			$content = explode('--'.$boundary, $content);
			$content = $content[2];
			$charset = preg_match("/charset=\"([^\"]*)\"/",$content,$matches);
			if ($charset) $charset = $matches[1];
			$content = explode('Content-Transfer-Encoding: quoted-printable', $content);
			$content = strip_tags($content[1], '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
		}
		$content = trim($content);

		echo "<p><b>Content-type:</b> $content_type, <b>boundary:</b> $boundary</p>\n";
//		echo "<p><b>Raw content:</b><br /><pre>".$content.'</pre></p>';
		
		$btpos = strpos($content, $bodyterminator);
		if ($btpos) {
			$content = substr($content, 0, $btpos);
		}
		$content = trim($content);

		$blah = explode("\n", $content);
		$firstline = $blah[0];
		$secondline = $blah[1];

		if ($use_phoneemail) {
			$btpos = strpos($firstline, $phoneemail_separator);
			if ($btpos) {
				$userpassstring = trim(substr($firstline, 0, $btpos));
				$content = trim(substr($content, $btpos+strlen($phoneemail_separator), strlen($content)));
				$btpos = strpos($content, $phoneemail_separator);
				if ($btpos) {
					$userpassstring = trim(substr($content, 0, $btpos));
					$content = trim(substr($content, $btpos+strlen($phoneemail_separator), strlen($content)));
				}
			}
			$contentfirstline = $blah[1];
		} else {
			$userpassstring = strip_tags($firstline);
			$contentfirstline = '';
		}

        $flat = 999.0;
        $flon = 999.0;
        $secondlineParts = explode(':',strip_tags($secondline));
        if(strncmp($secondlineParts[0],"POS",3)==0) {
            echo "Found POS:<br>\n";
            //echo "Second parts is:".$secondlineParts[1];
            // the second line is the postion listing line
            $secLineParts = explode(',',$secondlineParts[1]);
            $flatStr = $secLineParts[0];
            $flonStr = $secLineParts[1];
            //echo "String are ".$flatStr.$flonStr; 
            $flat = floatval($secLineParts[0]);
            $flon = floatval($secLineParts[1]);
            //echo "values are ".$flat." and ".$flon;
            // ok remove that position... we should not have it in the final output
            $content = str_replace($secondline,'',$content);
        }

		$blah = explode(':', $userpassstring);
		$user_login = $blah[0];
		$user_pass = $blah[1];

		$content = $contentfirstline.str_replace($firstline, '', $content);
		$content = trim($content);
//Please uncomment following line, only if you want to check user and password.
//		echo "<p><b>Login:</b> $user_login, <b>Pass:</b> $user_pass</p>";
		
		if ($xoopsDB) {
			$sql = "SELECT ID, user_level FROM $tableusers WHERE user_login='".trim($user_login)."' ORDER BY ID DESC LIMIT 1";
			$result = $wpdb->get_row($sql);
			if (!$result) {
				echo '<p><b>Wrong login</b></p></div>';
				continue;
			} else {
				$sql = "SELECT * FROM ".$xoopsDB->prefix('users')." WHERE uname='".trim($user_login)."' AND pass='".md5(trim($user_pass))."' ORDER BY uid DESC LIMIT 1";
				$result1 = $wpdb->get_row($sql);
		
				if (!$result1) {
					echo '<p><b>Wrong password.</b></p></div>';
					continue;
				}
			}
		} else {
			$sql = "SELECT ID, user_level FROM $tableusers WHERE user_login='$user_login' AND user_pass='$user_pass' ORDER BY ID DESC LIMIT 1";
			$result = $wpdb->get_row($sql);

			if (!$result) {
				echo '<p><b>Wrong login or password.</b></p></div>';
				continue;
			}
		}

		$user_level = $result->user_level;
		$post_author = $result->ID;

		if ($user_level > 0) {
			$post_title = xmlrpc_getposttitle($content);
			if ($post_title == '') {
				$post_title = $subject;
			}

			$post_category = get_settings('default_category');
			if (preg_match('/<category>(.+?)<\/category>/is', $content, $matchcat)) {
				$post_category = xmlrpc_getpostcategory($content);
			} 
			if ($post_category == '') {
				$post_category = get_settings('default_post_category');
			}
			if (function_exists('mb_convert_encoding')) {
				echo "Subject : ".mb_convert_encoding($subject,$blog_charset,"auto")." <br />";
			} else {
				echo "Subject : ".$subject." <br />";
			}
			echo "Category : $post_category <br />";
			if (!$emailtestonly) {
				$content = preg_replace("|\n([^\n])|", " $1", $content);
				if ($charset == "") $charset = "JIS";
				if (trim($charset) == "Shift_JIS") $charset = "SJIS";
				$content =  preg_replace("/\=([0-9a-fA-F]{2,2})/e",  "pack('c',base_convert('\\1',16,10))", $content);
				if (function_exists('mb_convert_encoding')) {
					$content = addslashes(mb_convert_encoding(trim($content), $blog_charset, $charset));
					$post_title = addslashes(trim(mb_convert_encoding($post_title,$blog_charset,"auto")));
				} else {
					$content = addslashes(trim($content));
					$post_title = addslashes(trim($post_title));
				}
                if($flat > 500) {
                    $sql = "INSERT INTO $tableposts (post_author, post_date, post_content, post_title, post_category) VALUES ($post_author, '$post_date', '$content', '$post_title', $post_category)";
                } else {
                    $sql = "INSERT INTO $tableposts (post_author, post_date, post_content, post_title, post_category, post_lat, post_lon) VALUES ($post_author, '$post_date', '$content', '$post_title', $post_category, $flat, $flon)";
                }
				$result = $wpdb->query($sql);
				$post_ID = $wpdb->insert_id;
				echo "Post ID = $post_ID<br />";
				if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
					sleep($sleep_after_edit);
				}

				$blog_ID = 1;
				if($flat < 500) {
					pingGeoUrl($post_ID);	
				}

				pingWeblogs($blog_ID);
				pingBlogs($blog_ID);
				pingback($content, $post_ID);
			}
			echo "\n<p><b>Posted title:</b> $post_title<br />";
			echo "\n<b>Posted content:</b><br /><pre>".$content.'</pre></p>';

			// Double check it's not there already
			$exists = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post_ID AND category_id = $post_category");
			 if (!$exists && $result) { 
			 	$wpdb->query("
				INSERT INTO $tablepost2cat
				(post_id, category_id)
				VALUES
				($post_ID, $post_category)
				");
			}

			if(!$pop3->delete($iCount)) {
				echo '<p>Oops '.$pop3->ERROR.'</p></div>';
				$pop3->reset();
				exit;
			} else {
				echo "<p>Mission complete, message <strong>$iCount</strong> deleted.</p>";
			}

		} else {
			echo '<p><strong>Level 0 users can\'t post.</strong></p>';
		}
		echo '</div>';
		if ($output_debugging_info) {
			ob_end_flush();
		} else {
			ob_end_clean();
		}
	}
}

$pop3->quit();

timer_stop($output_debugging_info);
exit;

?>
