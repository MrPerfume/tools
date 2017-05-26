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













 ?>
