<?php
namespace app\admin\controller;
use app\admin\controller\AdminAuth;
class System extends AdminAuth{
	public function index(){
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		$this->jianli_value();
		return $this->fetch();
	}
}
?>