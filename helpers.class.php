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
/**
* 遍历文件夹
* @param string $dir
* @param boolean $all  true表示递归遍历
* @return array
*/
public static function scanfDir($dir='', $all = false, &$ret = array()){
if ( false !== ($handle = opendir ( $dir ))) {
while ( false !== ($file = readdir ( $handle )) ) {
if (!in_array($file, array('.', '..', '.git', '.gitignore', '.svn', '.htaccess', '.buildpath','.project'))) {
$cur_path = $dir . '/' . $file;
if (is_dir ( $cur_path )) {
$ret['dirs'][] =$cur_path;
$all && self::scanfDir( $cur_path, $all, $ret);
} else {
$ret ['files'] [] = $cur_path;
}
}
}
closedir ( $handle );
}
return $ret;
}
/**
* 邮件发送
* @param string $toemail
* @param string $subject
* @param string $message
* @return boolean
*/
public static function sendMail($toemail = '', $subject = '', $message = '') {
$mailer = Yii::createComponent ( 'application.extensions.mailer.EMailer' );

//邮件配置
$mailer->SetLanguage('zh_cn');
$mailer->Host = Yii::app()->params['emailHost']; //发送邮件服务器
$mailer->Port = Yii::app()->params['emailPort']; //邮件端口
$mailer->Timeout = Yii::app()->params['emailTimeout'];//邮件发送超时时间
$mailer->ContentType = 'text/html';//设置html格式
$mailer->SMTPAuth = true;
$mailer->Username = Yii::app()->params['emailUserName'];
$mailer->Password = Yii::app()->params['emailPassword'];
$mailer->IsSMTP ();
$mailer->From = $mailer->Username; // 发件人邮箱
$mailer->FromName = Yii::app()->params['emailFormName']; // 发件人姓名
$mailer->AddReplyTo ( $mailer->Username );
$mailer->CharSet = 'UTF-8';














 ?>
