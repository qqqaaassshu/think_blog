<?php
namespace app\admin\controller;
use think\Controller;
use think\Image;
use think\Validate;
use app\admin\controller\AdminAuth;
use app\admin\model\Person as PersonModel;
use app\admin\model\System as SystemModel;
class Person extends AdminAuth{
	public function index(){
		$admin =PersonModel::get(1);
		$this->jianli_value();
		$this->assign('admin',$admin);
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}
	// ajax图片上传方法
	public function Person_logo(){
		$request =Request();
		$file =Request()->file('image');
		//验证图片类型，后缀和大小字节
		$info = $file->validate(['type'=>'image/jpeg,image/png,image/gif','ext'=>'jpg,png,gif','size'=>1048576])->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info){
			$info = $file->move(ROOT_PATH .'public/static/common/images','logo.jpg');
			$image = Image::open($file);
			$image_type = request()->param('type') ? request()->param('type') : 2;
            // 图片处理
            switch ($image_type) {
                case 1: // 图片裁剪
                    $image->crop(300, 300);
                    break;
                case 2: // 缩略图
                    $image->thumb(200, 200, Image::THUMB_SCALING);
                    break;
                case 3: // 垂直翻转
                    $image->flip();
                    break;
                case 4: // 水平翻转
                    $image->flip(Image::FLIP_Y);
                    break;
                case 5: // 图片旋转
                    $image->rotate();
                    break;
                case 6: // 图片水印
                    $image->water('./logo.png', Image::WATER_NORTHWEST, 50);
                    break;
                case 7: // 文字水印
                    $image->text('ThinkPHP', VENDOR_PATH . 'topthink/think-captcha/assets/ttfs/1.ttf', 20, '#ffffff');
                    break;
            }
            // 保存图片（以当前时间戳）
            $image->save(ROOT_PATH . 'public/static/common/images/logo.jpg');
            return 'success';
		}else{
			exit();
        }
	}
	//采用同步上传图片时，中间一段图片判断代码有用
	public function Modify_Person(){
		if($this->adminAuth() !=1){
			$response = 'level';
		}else{
			$data=Request()->param();
			$file =Request()->file('image');
			$admin =PersonModel::get(1);
			$rule = [
	        	'nickname' => 'require|min:2|max:10',
	            'intro' => 'max:30',
	        ];
	        $validate = new Validate($rule);
	        if(!$validate->check($data)){
	            return  'validate';
	        }
			if($file){
				$file->validate(['ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public/common/static/images','logo.jpg');
				$admin->nickname = $data['nickname'];
				$admin->intro = $data['intro'];
				if($admin->save()){
					$response ='success';
				}
			}else{
				if($admin['nickname'] == $data['nickname'] && $admin['intro'] == $data['intro']){
					$response ='error';
				}else{
					$admin->nickname = $data['nickname'];
					$admin->intro = $data['intro'];
					if($admin->save()){
						$response ='success';
					}
				}
			}
		}
		return $response;
	}
}
?>