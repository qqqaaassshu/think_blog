<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use app\admin\controller\AdminAuth;
use app\admin\model\Intro as IntroModel;
use app\admin\model\Work as WorkModel;
use app\admin\model\System as SystemModel;
class Jianli extends AdminAuth{
	function index(){
		$Intro=IntroModel::get(1);
		$this->assign('Intro',$Intro);
		$list =WorkModel::all();
		$this->assign('list',$list);
		$this->jianli_value();
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}
	function modify(){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$Intro=IntroModel::get(1);
		$request = Request();
		$data =$request->param();
		$i =0;
		$rule = [
			'name'     => 'require|min:1|max:12',
			'birth'         => 'require|date',
			'school'    => 'require|min:5|max:36',
			'major' => 'require|min:2|max:18',
			'address' => 'require|min:12|max:60',
			'tel' => 'require|max:11',
			'email' => 'require|email',
			'education' => 'require|min:2|max:12',
			'intro' => 'require|min:10|max:300',
		];
		// 数据验证
		$validate = new Validate($rule);
		if (!$validate->check($data)) {
			return 'validate';
		}
		foreach ($data as $key => $value) {
			if($Intro[$key]==$value){
				$i++;
			}
		}
		if($i==10){
			return 'warn';
		}else{
			$Intro->name =$data['name'];
			$Intro->birth =$data['birth'];
			$Intro->person_type =$data['person_type'];
			$Intro->school =$data['school'];
			$Intro->major =$data['major'];
			$Intro->address =$data['address'];
			$Intro->tel =$data['tel'];
			$Intro->email =$data['email'];
			$Intro->education =$data['education'];
			$Intro->intro =$data['intro'];
		}
		if($Intro->save()){
			$response ='success';
		}else{
			$response ='error';
		}
		return $response;
	}
	//履历的增加、删除和更新：第一种做法：判断履历数量是否改变如果没有改变则判断是否进行更新，如果没有更新则报错如果更新了则清空数据库重新添加，如果履历数量改变则直接清空数据库，然后存储新数据，数据量少可以这么做，如果数据量多的话则用下面一种方法：判断履历数量是否改变如果没有改变则判断是否进行更新，将数据库数据和表单提交数据遍历至新的数组然后将数组进行对比，对比一致则计数器加一，如果不一致则更新这一条数据，最后进行比对计数器和履历数量是否相等如果相等则报错，如果履历数量不一致则判断是增加还是删除，如果是增加需要先判断数据库中的数据是否需要更新(判断步骤与更新履历一致)，然后新增；如果是删除则将表单提交的id和数据库中的id进行比对，其中不一致的进行删除操作
	public function work_modify(){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$request = Request();
		$data = $request->param();
		$list =WorkModel::all();
		$work=new WorkModel;
		$count = count($data['id']);
		$new =array();
		$old =array();
		$new_add=array();
		$id=array();
		for($i=0;$i<$count;$i++){
			$intro_validate =mb_strlen($data['intro'][$i],'utf8')>150 || mb_strlen($data['intro'][$i],'utf8')<10;
			$company_validate =mb_strlen($data['company'][$i],'utf8')>12 || mb_strlen($data['company'][$i],'utf8')<5;
			$job_validate =mb_strlen($data['job'][$i],'utf8')>10 || mb_strlen($data['job'][$i],'utf8')<2;
			if($intro_validate || $company_validate || $job_validate){
				return 'validate';
			}
		}
		//计数器
		$j=0;
		//如果履历数量相等则判断数据是否有更新，否则进行下一步判断
		if($count ==count($list)){
			//循环履历次数根据每次相比进行判断如果与数据库数据相等则计数器加1否则更新此条数据
			for($i =0;$i<$count;$i++){
				foreach($data as $key=>$value){
					$new[$key] =$data[$key][$i];
					$old[$key] =$list[$i][$key];
				}
				if($new ==$old){
					$j++;
				}else{
					if($work::update($new)){
						$response ="success";
					}else{
						$response ="error";
						return $response;
					}
				}
			}
			//判断计数器与履历次数是否相等，如果相等则报错
			if($j==$count){
				$response ="warn";
			}
		}else{
			if($count>count($list)){
				//如果履历数量大于原先则新增记录并且观察原先记录是否更新
				for($i =0;$i<count($list);$i++){
					//将表单提交的id和数据库进行对比，如果存在则判断是否需要更新。否则进行添加操作
					foreach($data as $key=>$value){
						$new[$key] =$data[$key][$i];
						$old[$key] =$list[$i][$key];
					}
					if($new !=$old){
						if($work::update($new)){
							$response ="success";
						}else{
							$response ="error";
							return $response;
						}
					}
				}
				//将新履历添加到数据库
				for($i=count($list);$i<$count;$i++){
					foreach($data as $key=>$value){
						$new[$key] =$data[$key][$i];
					}
					if($work->isUpdate(false)->save($new)){
						$j++;
					}else{
						$response ="error";
						return $resopnse;
					}
					if($j == $count-count($list)){
						$response ="success";
					}
				}
			}else{
				//如果履历数量小于原先则删除多余记录并且观察原先记录是否更新
				//首先获取数据库的ID和其中最后一条的ID，然后取出表单上传的ID，让小于数据库最后一条的ID进行逐个对比，中间有不一致的就是需要删除的记录
				for($i=0;$i<count($list);$i++){
					$del[$i] =$list[$i]['id'];
				}
				$id =array_values(array_diff($del,$data['id']));
				if($work::destroy($id)){
					$response ="success";
					for($i=0;$i<count($data['id']);$i++){
						foreach($data as $key=>$value){
							$new[$key] =$data[$key][$i];
							$old[$key] =WorkModel::all()[$i][$key];
						}
						if($new !=$old){
							if($work::update($new)){
								$response ="success";
							}else{
								$response ="error";
								return $response;
							}
						}
					}
				}else{
					$response ="error";
					return $resopnse;
				}
			}
		}
		return $response;
	}
}
?>