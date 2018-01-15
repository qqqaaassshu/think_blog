<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\controller\AdminAuth;
use app\admin\model\System as SystemModel;
use app\admin\model\Admin as AdminModel;
class Index extends AdminAuth{
	public function index(){
		$system =SystemModel::get(1);
		if(date('Ymd',$system->date) !=date('Ymd',time())){
			$system->date=time();
			$system->today_article=0;
			$system->save();
		}
		$this->assign('system',$system);
		$this->jianli_value();
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}
	//修改此账号的登录密码
	public function modify_password(){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$request=Request()->param();
		$admin =new AdminModel;
		$date=array('password' => md5($request['pw']));
		$admin_PW =AdminModel::get(['username'=>session('username')]);
		if($admin_PW->password !=$date['password']){
			echo "<script>alert('原密码错误');location.href='".url('/admin/index')."'</script>";
		}if($admin_PW->password==md5($request['newpw'])){
			echo "<script>alert('新密码和原来密码相同不得修改');location.href='".url('/admin/index')."'</script>";
		}else{
			if($admin->where($date)->find()){
				$admin->where($date)->update(['password'=>md5($request['newpw'])]);
				echo "<script>alert('密码修改成功');location.href='".url('/admin/login')."'</script>";
			}
		}
	}

	//修改此账号的简历是否显示
	public function jianli_switch(){
		$system =SystemModel::get(1);
		//如果简历关闭
		if($system->work_status == 0){
			$system->work_status =1;
		}else{
			$system->work_status =0;
		}
		if($system->save()){
			return $system->work_status;
		}else{
			return '<script>alert("简历开关修改失败")</script>';
		}
	}

	public function logout(){
		session(null);
		echo "<script>location.href='".url('/admin/login')."'</script>";
	}

	public function lastlogin(){
		$admin = AdminModel::get(['username'=>session('username')]);
		return $admin->last_login_time;
	}

	public function emptySession(){
		session(null);
	}
}
?>