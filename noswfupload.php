<?php
/** noswfupload PHP Manager for Safari 4 beta
 * @author      Andrea Giammarchi
 * @license     Mit Style
 * @description if $_FILES is empty, set php://input as $_FILES entry if file has been sent via JavaScript noswfupload lib.
 *              This function is completely unobtrusive and it should work with PHP 4.3 or greater with every webserver.
 */
if(empty($_FILES))call_user_func(create_function('', '
    $file_put_contents  = function_exists(\'file_put_contents\') ? \'file_put_contents\' : create_function(\'$name,$value\', \'$bytes=false;if($fp=fopen($name,\\\'wb\\\')){fwrite($fp,$value,$bytes=strlen($value));fclose($fp);}return $bytes;\');
    $getallheaders      = function_exists(\'getallheaders\') ? \'getallheaders\' : create_function(\'\', \'foreach($_SERVER as $key=>$value){if($key===\\\'CONTENT_TYPE\\\'||$key===\\\'CONTENT_LENGTH\\\')$key=\\\'HTTP_\\\'.$key;if(strpos($key,\\\'HTTP_\\\')===0)$headers[str_replace(\\\' \\\',\\\'-\\\',ucwords(strtolower(str_replace(\\\'_\\\',\\\' \\\',substr($key,5)))))]=$value;}return $headers;\');
    $headers            = $getallheaders();
    if(
        isset($headers[\'X-Name\'], $headers[\'X-Filename\'], $headers[\'Content-Length\'], $headers[\'Content-Type\']) &&
        $headers[\'Content-Type\'] === \'multipart/form-data\' &&
        0 < ($len = strlen($input = file_get_contents(\'php://input\'))) &&
        $len == $headers[\'Content-Length\']
    ){
        switch(!!$file_put_contents($tmp_name = tempnam(function_exists(\'sys_get_temp_dir\') ? sys_get_temp_dir() : \'/tmp\', \'php\'), $input)){
            case    false:
                return;
            case    function_exists(\'finfo_open\'):
                $finfo  = finfo_open(FILEINFO_MIME);
                $type   = finfo_file($finfo, $tmp_name);
                finfo_close($finfo);
                break;
            case    function_exists(\'mime_content_type\'):
                $type   = mime_content_type($tmp_name);
                break;
            default:
                $type   = \'application/octet-stream\';
                break;
        }
        $_FILES[$headers[\'X-Name\']] = array(
            \'name\'      => $headers[\'X-Filename\'],
            \'type\'      => $type,
            \'tmp_name\'  => $tmp_name,
            \'error\'     => 0,
            \'size\'      => $len
        );
    }
'));
?>