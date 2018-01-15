<?php
namespace app\index\model;
use think\Model;
class Work extends Model{
	protected $table = 'blog_work';
	protected function getStartAttr($value){
		return date('Y.m.d',strtotime($value));
	}
	protected function getEndAttr($value){
		return date('Y.m.d',strtotime($value));
	}
}
?>