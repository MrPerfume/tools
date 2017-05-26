<?php
/**
 * 助手类
 * @author me
 *
 */
 /**
* 判断当前服务器系统
* @return string
*/
public static function getOS(){
if(PATH_SEPARATOR == ':'){
return 'Linux';
}else{
return 'Windows';
}
}
/**
* 当前微妙数
* @return number
*/
public static function microtime_float() {
list ( $usec, $sec ) = explode ( " ", microtime () );
return (( float ) $usec + ( float ) $sec);
}
/**
* 切割utf-8格式的字符串(一个汉字或者字符占一个字节)
*
* @author zhao jinhan
* @version v1.0.0
*
*/
public static function truncate_utf8_string($string, $length, $etc = '...') {
$result = '';
$string = html_entity_decode ( trim ( strip_tags ( $string ) ), ENT_QUOTES, 'UTF-8' );
$strlen = strlen ( $string );
for($i = 0; (($i < $strlen) && ($length > 0)); $i ++) {
if ($number = strpos ( str_pad ( decbin ( ord ( substr ( $string, $i, 1 ) ) ), 8, '0', STR_PAD_LEFT ), '0' )) {
if ($length < 1.0) {
break;
}
$result .= substr ( $string, $i, $number );
$length -= 1.0;
$i += $number - 1;
} else {
$result .= substr ( $string, $i, 1 );
$length -= 0.5;
}
}
$result = htmlspecialchars ( $result, ENT_QUOTES, 'UTF-8' );
if ($i < $strlen) {
$result .= $etc;
}
return $result;
}













 ?>
