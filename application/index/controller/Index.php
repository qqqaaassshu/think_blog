<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Flash as FlashModel;
use app\index\model\Article as ArticleModel;
use app\index\model\Diary as DiaryModel;
use app\index\model\Person as PersonModel;
use app\index\model\System as SystemModel;
use app\index\controller\Common;
class Index extends Common{
	public function index($type=''){
		$flash =new FlashModel;
		$Hflash =$flash->where('type',1)->order('order','asc')->select();
		$Diary =new DiaryModel;
		$Person =PersonModel::get(1);
		$System =SystemModel::get(1);
		if(DiaryModel::count() < 0){
			$this->assign('diary','');
		}else{
			$DiaryM =$Diary->limit(1)->order('id','desc')->find();
			$this->assign('diary',$DiaryM);
		}
		$this->assign('Hlist',$Hflash);
		if(empty($type)){
			$article =ArticleModel::where(['status'=>'1'])->order('create_time','desc')->paginate(5);
		}else{
			$article =ArticleModel::where(['status'=>'1','type'=>$type])->order('create_time','desc')->paginate(5);
		}
		$this->assign('system',$System);
		$this->assign('person',$Person);
		$this->assign('list',$article);
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