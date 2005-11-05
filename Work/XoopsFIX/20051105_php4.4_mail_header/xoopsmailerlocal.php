<?php
// $Id: xoopsmailerlocal.php,v 1.2 2005/03/18 12:51:55 onokazu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: NobuNobu (Nobuki@Kowa.ORG)                                        //
// URL:  http://jp.xoops.org                                                 //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
class XoopsMailerLocal extends XoopsMailer {

    function XoopsMailerLocal(){
        $this->multimailer = new XoopsMultiMailerLocal();
        $this->reset();
        $this->charSet = 'iso-2022-jp';
        $this->encoding = '7bit';
    }

    function encodeFromName($text){
        return $this->STRtoJIS($text,_CHARSET);
    }

    function encodeSubject($text){
        if ($this->multimailer->needs_encode) {
            return $this->STRtoJIS($text,_CHARSET);
        } else {
            return $text;
        }
    }

    function encodeBody(&$text){
        if ($this->multimailer->needs_encode) {
            $text = $this->STRtoJIS($text,_CHARSET);
        }
    }

    /*-------------------------------------
     PHP FORM MAIL 1.01 by TOMO
     URL : http://www.spencernetwork.org/
     E-Mail : groove@spencernetwork.org
    --------------------------------------*/
    function STRtoJIS($str, $from_charset){
        if (function_exists('mb_convert_encoding')) { //Use mb_string extension if exists.
            $str_JIS  = mb_convert_encoding($str, "ISO-2022-JP", $from_charset);
        } else if ($from_charset=='EUC-JP') {
            $str_JIS = '';
            $mode = 0;
            $b = unpack("C*", $str);
            $n = count($b);
            for ($i = 1; $i <= $n; $i++) {
                if ($b[$i] == 0x8E) {
                    if ($mode != 2) {
                        $mode = 2;
                        $str_JIS .= pack("CCC", 0x1B, 0x28, 0x49);
                    }
                    $b[$i+1] -= 0x80;
                    $str_JIS .= pack("C", $b[$i+1]);
                    $i++;
                } elseif ($b[$i] > 0x8E) {
                    if ($mode != 1){
                        $mode = 1;
                        $str_JIS .= pack("CCC", 0x1B, 0x24, 0x42);
                    }
                    $b[$i] -= 0x80; $b[$i+1] -= 0x80;
                    $str_JIS .= pack("CC", $b[$i], $b[$i+1]);
                    $i++;
                } else {
                    if ($mode != 0) {
                        $mode = 0;
                        $str_JIS .= pack("CCC", 0x1B, 0x28, 0x42);
                    }
                    $str_JIS .= pack("C", $b[$i]);
                }
            }
            if ($mode != 0) $str_JIS .= pack("CCC", 0x1B, 0x28, 0x42);
        }
        return $str_JIS;
    }
}

class XoopsMultiMailerLocal extends XoopsMultiMailer {

    var $needs_encode;

    function XoopsMultiMailerLocal() {
        $this->XoopsMultiMailer();

        $this->needs_encode = true;
        if (function_exists('mb_convert_encoding')) {
            $mb_overload = ini_get('mbstring.func_overload');
            if (($this->Mailer == 'mail') && (intval($mb_overload) & 1)) { //check if mbstring extension overloads mail()
                $this->needs_encode = false;
            }
        }
    }

    function addr_format($addr) {
        if(empty($addr[1])) {
            $formatted = $addr[0];
        } else {
            $formatted = sprintf('%s <%s>', $this->encode_header($addr[1], 'text', true), $addr[0]);
        }
        return $formatted;
    }

    function encode_header ($str, $position = 'text', $force=false) {
        $encode_charset = 'ISO-2022-JP';
        if (function_exists('mb_convert_encoding')) { //Use mb_string extension if exists.
            if ($this->needs_encode || $force) {
                $str = mb_convert_encoding($str, $encode_charset, mb_detect_encoding($str));
                $i = 0;
                $encoded ='';
                $cut_length = floor((76-strlen('Subject: =?'.$encode_charset.'?B?'.'?='))/4)*3;
                while($i < strlen($str)) {
                	$partstr = mb_strcut ( $str, $i, $cut_length, $encode_charset);
                	if ($i) $encoded .= "\r\n ";
                	$encoded .= '=?' . $encode_charset . '?B?' . base64_encode($partstr) . '?=';
                	$i += strlen($partstr);
                }
            } else {
                $encoded = $str;
            }
        } else {
            $encoded = parent::encode_header($str, $position);
        }
        return $encoded;
    }
}
?>