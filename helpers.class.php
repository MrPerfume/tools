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

// 添加邮件日志
$modelMail = new MailLog ();
$modelMail->accept = $toemail;
$modelMail->subject = $subject;
$modelMail->message = $message;
$modelMail->send_status = 'waiting';
$modelMail->save ();
// 发送邮件
$mailer->AddAddress ( $toemail );
$mailer->Subject = $subject;
$mailer->Body = $message;

if ($mailer->Send () === true) {
$modelMail->times = $modelMail->times + 1;
$modelMail->send_status = 'success';
$modelMail->save ();
return true;
} else {
$error = $mailer->ErrorInfo;
$modelMail->times = $modelMail->times + 1;
$modelMail->send_status = 'failed';
$modelMail->error = $error;
$modelMail->save ();
return false;
}
}
/**
* 判断字符串是utf-8 还是gb2312
* @param unknown $str
* @param string $default
* @return string
*/
public static function utf8_gb2312($str, $default = 'gb2312')
{
   $str = preg_replace("/[\x01-\x7F]+/", "", $str);
   if (empty($str)) return $default;

   $preg =  array(
       "gb2312" => "/^([\xA1-\xF7][\xA0-\xFE])+$/", //正则判断是否是gb2312
       "utf-8" => "/^[\x{4E00}-\x{9FA5}]+$/u",      //正则判断是否是汉字(utf8编码的条件了)，这个范围实际上已经包含了繁体中文字了
   );

   if ($default == 'gb2312') {
       $option = 'utf-8';
   } else {
       $option = 'gb2312';
   }

   if (!preg_match($preg[$default], $str)) {
       return $option;
   }
   $str = @iconv($default, $option, $str);

   //不能转成 $option, 说明原来的不是 $default
   if (empty($str)) {
       return $option;
   }
   return $default;
}
/**
* utf-8和gb2312自动转化
* @param unknown $string
* @param string $outEncoding
* @return unknown|string
*/
public static function safeEncoding($string,$outEncoding = 'UTF-8')
{
$encoding = "UTF-8";
for($i = 0; $i < strlen ( $string ); $i ++) {
if (ord ( $string {$i} ) < 128)
continue;

if ((ord ( $string {$i} ) & 224) == 224) {
// 第一个字节判断通过
$char = $string {++ $i};
if ((ord ( $char ) & 128) == 128) {
// 第二个字节判断通过
$char = $string {++ $i};
if ((ord ( $char ) & 128) == 128) {
$encoding = "UTF-8";
break;
}
}
}
if ((ord ( $string {$i} ) & 192) == 192) {
// 第一个字节判断通过
$char = $string {++ $i};
if ((ord ( $char ) & 128) == 128) {
// 第二个字节判断通过
$encoding = "GB2312";
break;
}
}
}

if (strtoupper ( $encoding ) == strtoupper ( $outEncoding ))
return $string;
else
return @iconv ( $encoding, $outEncoding, $string );
}
/**
* 返回二维数组中某个键名的所有值
* @param input $array
* @param string $key
* @return array
*/
public static function array_key_values($array =array(), $key='')
{
$ret = array();
foreach((array)$array as $k=>$v){
$ret[$k] = $v[$key];
}
return $ret;
}














 ?>
