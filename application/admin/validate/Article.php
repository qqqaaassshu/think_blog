<?php
namespace app\admin\validate;
use think\Validate;
class Article extends Validate{
	protected $rule=[
		// 'title'=>'min:2|max:45|require',
		// 'intro'=>'min:10|max:300|require',
		// 'editorValue' =>'require',
	];
}
?>