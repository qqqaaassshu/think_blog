<?php
namespace app\index\controller;
use think\Controller;
use think\Model;
class Common extends Controller{
	//随机值，防止浏览器缓存无法更新
	protected function AutoVersion($file){
		if( file_exists($_SERVER['DOCUMENT_ROOT'].$file) ) {
            $ver = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
        } else {
            $ver = 1;
        }
		return $file .'?v=' .$ver;
	}
}

?>