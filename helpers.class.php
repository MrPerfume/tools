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
/**
* 判断 文件/目录 是否可写（取代系统自带的 is_writeable 函数）
* @param string $file 文件/目录
* @return boolean
*/
public static function is_writeable($file) {
if (is_dir($file)){
$dir = $file;
if ($fp = @fopen("$dir/test.txt", 'w')) {
@fclose($fp);
@unlink("$dir/test.txt");
$writeable = 1;
} else {
$writeable = 0;
}
} else {
if ($fp = @fopen($file, 'a+')) {
@fclose($fp);
$writeable = 1;
} else {
$writeable = 0;
}
}

return $writeable;
}
/**
* 格式化单位
*/
static public function byteFormat( $size, $dec = 2 ) {
$a = array ( "B" , "KB" , "MB" , "GB" , "TB" , "PB" );
$pos = 0;
while ( $size >= 1024 ) {
$size /= 1024;
$pos ++;
}
return round( $size, $dec ) . " " . $a[$pos];
}

/**
* 下拉框，单选按钮 自动选择
*
* @param $string 输入字符
* @param $param  条件
* @param $type   类型
* selected checked
* @return string
*/
static public function selected( $string, $param = 1, $type = 'select' ) {

$true = false;
if ( is_array( $param ) ) {
$true = in_array( $string, $param );
}elseif ( $string == $param ) {
$true = true;
}
$return='';
if ( $true )
$return = $type == 'select' ? 'selected="selected"' : 'checked="checked"';

echo $return;
}

/**
* 下载远程图片
* @param string $url 图片的绝对url
* @param string $filepath 文件的完整路径（例如/www/images/test） ，此函数会自动根据图片url和http头信息确定图片的后缀名
* @param string $filename 要保存的文件名(不含扩展名)
* @return mixed 下载成功返回一个描述图片信息的数组，下载失败则返回false
*/
static public function downloadImage($url, $filepath, $filename) {
//服务器返回的头信息
$responseHeaders = array();
//原始图片名
$originalfilename = '';
//图片的后缀名
$ext = '';
$ch = curl_init($url);
//设置curl_exec返回的值包含Http头
curl_setopt($ch, CURLOPT_HEADER, 1);
//设置curl_exec返回的值包含Http内容
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//设置抓取跳转（http 301，302）后的页面
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//设置最多的HTTP重定向的数量
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

//服务器返回的数据（包括http头信息和内容）
$html = curl_exec($ch);
//获取此次抓取的相关信息
$httpinfo = curl_getinfo($ch);
curl_close($ch);
if ($html !== false) {
//分离response的header和body，由于服务器可能使用了302跳转，所以此处需要将字符串分离为 2+跳转次数 个子串
$httpArr = explode("\r\n\r\n", $html, 2 + $httpinfo['redirect_count']);
//倒数第二段是服务器最后一次response的http头
$header = $httpArr[count($httpArr) - 2];
//倒数第一段是服务器最后一次response的内容
$body = $httpArr[count($httpArr) - 1];
$header.="\r\n";

//获取最后一次response的header信息
preg_match_all('/([a-z0-9-_]+):\s*([^\r\n]+)\r\n/i', $header, $matches);
if (!empty($matches) && count($matches) == 3 && !empty($matches[1]) && !empty($matches[1])) {
for ($i = 0; $i < count($matches[1]); $i++) {
if (array_key_exists($i, $matches[2])) {
$responseHeaders[$matches[1][$i]] = $matches[2][$i];
}
}
}
//获取图片后缀名
if (0 < preg_match('{(?:[^\/\\\\]+)\.(jpg|jpeg|gif|png|bmp)$}i', $url, $matches)) {
$originalfilename = $matches[0];
$ext = $matches[1];
} else {
if (array_key_exists('Content-Type', $responseHeaders)) {
if (0 < preg_match('{image/(\w+)}i', $responseHeaders['Content-Type'], $extmatches)) {
$ext = $extmatches[1];
}
}
}
//保存文件
if (!empty($ext)) {
//如果目录不存在，则先要创建目录
if(!is_dir($filepath)){
mkdir($filepath, 0777, true);
}

$filepath .= '/'.$filename.".$ext";
$local_file = fopen($filepath, 'w');
if (false !== $local_file) {
if (false !== fwrite($local_file, $body)) {
fclose($local_file);
$sizeinfo = getimagesize($filepath);
return array('filepath' => realpath($filepath), 'width' => $sizeinfo[0], 'height' => $sizeinfo[1], 'orginalfilename' => $originalfilename, 'filename' => pathinfo($filepath, PATHINFO_BASENAME));
}
}
}
}
return false;
}
/**
* 查找ip是否在某个段位里面
* @param string $ip 要查询的ip
* @param $arrIP     禁止的ip
* @return boolean
*/
public static function ipAccess($ip='0.0.0.0', $arrIP = array()){
$access = true;
$ip && $arr_cur_ip = explode('.', $ip);
foreach((array)$arrIP as $key=> $value){
if($value == '*.*.*.*'){
$access = false; //禁止所有
break;
}
$tmp_arr = explode('.', $value);
if(($arr_cur_ip[0] == $tmp_arr[0]) && ($arr_cur_ip[1] == $tmp_arr[1])) {
//前两段相同
if(($arr_cur_ip[2] == $tmp_arr[2]) || ($tmp_arr[2] == '*')){
//第三段为* 或者相同
if(($arr_cur_ip[3] == $tmp_arr[3]) || ($tmp_arr[3] == '*')){
//第四段为* 或者相同
$access = false; //在禁止ip列，则禁止访问
break;
}
}
}
}
return $access;
}
/**
* 取得输入目录所包含的所有目录和文件
* 以关联数组形式返回
* author: flynetcn
*/
static public function deepScanDir($dir)
{
$fileArr = array();
$dirArr = array();
$dir = rtrim($dir, '//');
if(is_dir($dir)){
$dirHandle = opendir($dir);
while(false !== ($fileName = readdir($dirHandle))){
$subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
if(is_file($subFile)){
$fileArr[] = $subFile;
} elseif (is_dir($subFile) && str_replace('.', '', $fileName)!=''){
$dirArr[] = $subFile;
$arr = self::deepScanDir($subFile);
$dirArr = array_merge($dirArr, $arr['dir']);
$fileArr = array_merge($fileArr, $arr['file']);
}
}
closedir($dirHandle);
}
return array('dir'=>$dirArr, 'file'=>$fileArr);
}
/**
* 取得输入目录所包含的所有文件
* 以数组形式返回
* author: flynetcn
*/
static public function get_dir_files($dir)
{
if (is_file($dir)) {
return array($dir);
}
$files = array();
if (is_dir($dir) && ($dir_p = opendir($dir))) {
$ds = DIRECTORY_SEPARATOR;
while (($filename = readdir($dir_p)) !== false) {
if ($filename=='.' || $filename=='..') { continue; }
$filetype = filetype($dir.$ds.$filename);
if ($filetype == 'dir') {
$files = array_merge($files, self::get_dir_files($dir.$ds.$filename));
} elseif ($filetype == 'file') {
$files[] = $dir.$ds.$filename;
}
}
closedir($dir_p);
}
return $files;
}
/**
* 删除文件夹及其文件夹下所有文件
*/
public static function deldir($dir) {
//先删除目录下的文件：
$dh=opendir($dir);
while ($file=readdir($dh)) {
if($file!="." && $file!="..") {
$fullpath=$dir."/".$file;
if(!is_dir($fullpath)) {
unlink($fullpath);
} else {
self::deldir($fullpath);
}
}
}

closedir($dh);
//删除当前文件夹：
if(rmdir($dir)) {
return true;
} else {
return false;
}
}
/**
* js 弹窗并且跳转
* @param string $_info
* @param string $_url
* @return js
*/
static public function alertLocation($_info, $_url) {
echo "<script type='text/javascript'>alert('$_info');location.href='$_url';</script>";
exit();
}
/**
* js 弹窗返回
* @param string $_info
* @return js
*/
static public function alertBack($_info) {
echo "<script type='text/javascript'>alert('$_info');history.back();</script>";
exit();
}
/**
* 页面跳转
* @param string $url
* @return js
*/
static public function headerUrl($url) {
echo "<script type='text/javascript'>location.href='{$url}';</script>";
exit();
}
/**
* 弹窗关闭
* @param string $_info
* @return js
*/
static public function alertClose($_info) {
echo "<script type='text/javascript'>alert('$_info');close();</script>";
exit();
}













 ?>
