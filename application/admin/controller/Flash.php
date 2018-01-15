<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use app\admin\controller\AdminAuth;
use app\admin\model\Flash as FlashModel;
use app\admin\model\System as SystemModel;
class Flash extends AdminAuth{
	public function index(){
		$flash =new FlashModel;
		$Hflash =$flash->where('type',1)->order('order','asc')->select();
		$Vflash =$flash->where('type',0)->order('order','asc')->select();
		$this->assign('Hlist',$Hflash);
		$this->assign('Vlist',$Vflash);
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		$this->jianli_value();
		return $this->fetch();
	}
	public function addFlash($type){
		if($this->adminAuth() !=1){
			exit('此账户没有权限进行上传图片');
		}else{
			$request=Request();
			$flash =new FlashModel;
			$file =Request()->file('image');
			$count =count(FlashModel::all(['type'=>$type]));
			$info = $file->validate(['type'=>'image/jpeg,image/png,image/gif','ext'=>'jpg,png,gif','size'=>1048576])->move(ROOT_PATH .'public/static/common/images/upload/flash');
			$path = str_replace('\\','/',$info->getSaveName());

			if($info){
				if($count>=9){
					exit('幻灯片数量最多只能有9张');
				}
				$flash->path = $path;
				$flash->type = $type;
				$flash->order =$count+1;
				if($flash->save()){
					return 'success';
				}else{
					exit();
				}
			}else{
				exit();
			}
		}
	}

	public function modifyFlash(){
		$request=Request();
		$data =$request->param();
		$count= count($data['id']);
		$j=0;
		$sum=array_sum($data['order']);
        $order =$data['order'];
        sort($order);
        if((($order[0]+$order[$count-1])*$count/2) !== $sum || $order[0] !=1 || $order[$count-1]>9){
			return  'validate';
		}
		for($i=0;$i<$count;$i++){
			$flash =FlashModel::get($data['id'][$i]);
			if($flash->order ==$data['order'][$i]){
				$j++;
			}else{
				$flash->order =$data['order'][$i];
				if($flash->save()){
					$response ='success';
				}else{
					$response ='warn';
					return $response;
				}
			}
			if($j ==$count){
				$response ='error';
			}
		}
		return $response;
	}

	public function delFlash($id){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$flash = FlashModel::get($id);
		$type =$flash->type;
		$typeflash=new FlashModel;
		$result =$typeflash->where('type',$type)->where('order','>',$flash->order)->select();
		for($i=0;$i<count($result);$i++){
			$order = FlashModel::get($result[$i]['id']);
			$order->order =$order->order-1;
			$order->save();
		}
		$path =ROOT_PATH .'public/static/common/images/upload/flash/'.$flash->path;
		$tmp_arr=explode('\\',$flash->path);
		$dir =ROOT_PATH .'public/static/common/images/upload/flash/'.$tmp_arr[0];
		if(file_exists($path)){
			unlink($path);
		}
		if(is_dir($dir)){
			array_diff(scandir($dir),array('..','.')) ?"":rmdir($dir);
		}
		if ($flash) {
	        $flash->delete();
	        $response ='success';
	    } else {
	    	$response ='error';
		}
		return $response;
	}
}
?>
