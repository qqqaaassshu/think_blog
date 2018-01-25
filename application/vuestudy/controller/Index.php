<?php
namespace app\vuestudy\controller;
use think\Controller;
class Index extends Controller{
	public function index(){
		return $this->fetch();
	}
}
?>