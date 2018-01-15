<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Flash as FlashModel;
use app\index\model\Article as ArticleModel;
use app\index\model\Person as PersonModel;
use app\index\model\System as SystemModel;
use app\index\controller\Common;
class Article extends Common{
	public function index($id){
		$flash =new FlashModel;
		$article =ArticleModel::get($id);
		$Person =PersonModel::get(1);
		$System =SystemModel::get(1);
		$Hflash =$flash->where('type',1)->order('order','asc')->select();
		$Vflash =$flash->where('type',0)->order('order','asc')->select();
		$this->assign('Vlist',$Vflash);
		$this->assign('Hlist',$Hflash);
		$this->assign('article',$article);
		$this->assign('person',$Person);
		$this->assign('system',$System);
		$js=$this->AutoVersion('/static/index/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/index/css/style.css');
		$this->assign('css',$css);
		$Pcss=$this->AutoVersion('/static/index/css/phone.css');
		$this->assign('Pcss',$Pcss);
		return $this->fetch();
	}
}
?>