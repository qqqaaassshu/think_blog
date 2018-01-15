<?php
namespace app\admin\model;
use think\Model;

class Article extends Model{
	protected $table = 'blog_article';
	protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }
    protected function getUpdateTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }
}
?>