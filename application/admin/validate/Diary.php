<?php
namespace app\admin\validate;
use think\Validate;
class Diary extends Validate{
	protected $rule=[
		'title'=>'length:2,21|require',
		'content' =>'require',
	];
}
?>