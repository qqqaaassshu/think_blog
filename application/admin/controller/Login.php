<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\controller\AdminAuth;
use app\admin\model\System as SystemModel;
use app\admin\model\Admin as AdminModel;
class Login extends AdminAuth{
	public function index(){
		$this->view->engine->layout(false);
		$request=request()->param();
		$admin =new AdminModel;
		$system =SystemModel::get(1);
		if(!empty(session('username'))){
			$this->redirect('/admin/index');
		}
		if(count($request)<2){
			return $this->fetch();
		}else if(count($request)==2){
			$user =array('username' => $request['username'],'password'=>md5($request['password']) );
			if($date = $admin->where($user)->find()){
				session('username',$date->username);
				session('level',$date->level);
				session('jianli',$system->work_status);
				cookie('expired', null,0);
				$admin->where($user)->update(['last_login_time'=>time()]);
				$this->redirect('/admin/index');
			}else{
				echo "<script>alert('账号或密码错误')</script>";
				return $this->fetch();
			}
		}
	}
}
?>