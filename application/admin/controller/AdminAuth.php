<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use app\admin\model\System as SystemModel;
use think\Model;
class AdminAuth extends Controller{
	//1.登录之后添加session,来证明此人是登陆过的
	//2.其他页面需要验证是否有session来判断是否登录如果没有则提示他进入登录页面
	//3.登录界面不需要验证session
	//4.登录时长限制,判断数据库最后登录时间和自己限制时间对比如果超出则删除session
	//初始化判断是否存在session
	protected function _initialize(){
		$request=request();
		//不需要检测session的功能,现在只有一个登录
		$not_check= array('admin/Login/index','admin/Index/logout');
		if(in_array($request->module().'/'.$request->controller().'/'.$request->action(),$not_check)){
			return true;
		}
		if(!session('username')){
			echo "<script>alert('请先登录');location.href='".url('/admin/login')."'</script>";
		}
		$this ->auth_expired_check();
	}

	protected function auth_expired_check(){
		$admin =new AdminModel;
		if($user = $admin->where('username',session('username'))->find()){
			if(time() > $user->last_login_time +21600 ){
				session(null);
				echo "<script>alert('账号已过期，请重新登录');location.href='".url('/admin/login')."'</script>";
			}
		}
	}
	//判断账号是否有权利进行操作
	protected function adminAuth(){
		$user =AdminModel::get(['username'=>session('username')]);
		if(isset($user)){
			return $user->level;
		}
	}
	//给每个模块加上简历的值方便他们进行判断
	protected function jianli_value(){
		$System=SystemModel::get(1);
		$this->assign('system',$System);
	}
	//随机值，防止浏览器缓存无法更新
	protected function AutoVersion($file){
		if( file_exists($_SERVER['DOCUMENT_ROOT'].$file) ) {
            $ver = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
        } else {
            $ver = 1;
        }
		return $file .'?v=' .$ver;
	}
}

?>