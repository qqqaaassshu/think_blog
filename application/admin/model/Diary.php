<?php
namespace app\admin\model;
use think\Model;

class Diary extends Model{
	protected $table = 'blog_diary';
	protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d',$value);
    }
}
?>