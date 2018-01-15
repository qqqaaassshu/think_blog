<?php
namespace app\index\model;
use think\Model;

class Article extends Model{
	protected $table = 'blog_article';
	protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }
}
?>