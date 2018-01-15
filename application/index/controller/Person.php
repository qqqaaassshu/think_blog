<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Person as PersonModel;
use app\index\model\Work as WorkModel;
use app\index\model\Intro as IntroModel;
use app\index\model\System as SystemModel;
use app\index\controller\Common;
class Person extends Common{
	public function index(){
		$Intro =IntroModel::get(1);
		$Person =new PersonModel;
		$System =SystemModel::get(1);
		$personlist =$Person->limit(5)->order('id','asc')->find();
		$Work =WorkModel::all();
		$this->assign('work',$Work);
		$this->assign('person',$personlist);
		$this->assign('intro',$Intro);
		$this->assign('system',$System);
		$js=$this->AutoVersion('/static/index/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/index/css/person.css');
		$this->assign('css',$css);
		$Pcss=$this->AutoVersion('/static/index/css/person-phone.css');
		$this->assign('Pcss',$Pcss);
		return $this->fetch();
	}
}
?>
