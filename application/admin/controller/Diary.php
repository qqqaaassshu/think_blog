<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\controller\AdminAuth;
use app\admin\model\System as SystemModel;
use app\admin\model\Diary as DiaryModel;
class Diary extends AdminAuth{
	function index(){
		if(!$this->adminAuth()){
			echo "<script>alert('私人页面不做开放');history.back()</script>";
		}else{
			$data =array('title'=>'','start'=>'','end'=>'');
			$list = DiaryModel::order('create_time','desc')->paginate(10);
			$this->jianli_value();
			$js=$this->AutoVersion('/static/admin/js/index.js');
			$this->assign('js',$js);
			$css=$this->AutoVersion('/static/admin/css/style.css');
			$this->assign('css',$css);
			$this->assign('data',$data);
			$this->assign('list',$list);
			return $this->fetch();
		}
	}
	public function del($id =''){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$diary = DiaryModel::get($id);
		$system =SystemModel::get(1);
		if ($diary) {
	        $diary->delete();
	        $system->diary_count =$system->diary_count-1;
	        $system->save();
	        $response ='success';
	    } else {
	    	$response ='error';
		}
		return $response;
	}

	public function create(){
		$this->jianli_value();
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}

	public function add_diary(){
		if(!$this->adminAuth()){
			$response;
			return $response;
		}
		$request = Request();
		$data =$request->param();
		$validate = validate('Diary');
		if (!$validate->check($data)) {
	        return 'validate';
	    }
		$diary = new DiaryModel;
		$system =SystemModel::get(1);
		$diary->title = $data['title'];
		$diary->content = $data['content'];
		$diary->create_time = time();
		if(DiaryModel::get(['content'=>$data['content']])){
			return 'error';
		}
		if($diary->save()){
			$system->diary_count =count(DiaryModel::all());
			$system->save();
			$response = 'success';
		}
		return $response;
	}
	public function modify($id=''){
		$diary = DiaryModel::get($id);
		$this->jianli_value();
		$this->assign('diary',$diary);
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}
	public function modify_diary($id=''){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$request = Request();
		$data =$request->param();
		$validate = validate('Diary');
		if (!$validate->check($data)) {
	        return 'validate';
	    }
		$diary = DiaryModel::get($id);
		$a= new DiaryModel;
		if ( $data['title']==$diary->title && $data['content'] == $diary->content ){
			$response = 'warn';
		}else{
			//将表单进行提交数据库
			$diary->title = $data['title'];
			$diary->content = $data['content'];
			if($diary->save()){
				$response = 'success';
			}
		}
		return $response;
	}
	public function search(){
		$request =Request();
		$diary =new DiaryModel;
		$data = $request->param();
		$start = $data['start'] ? strtotime($data['start']):"";
		$end = $data['end'] ? strtotime($data['end']):"";
		$pageParam['query']['title']=$data['title'];
		$pageParam['query']['start']=$data['start'];
		$pageParam['query']['end']=$data['end'];
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		$this->jianli_value();
		if(empty($data['title']) && empty($start) && empty($end)){
			if($list =$diary->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}elseif($data['title'] && empty($start) && empty($end)){
			if($list =$diary->where('title','like','%'.$data['title'].'%')->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}elseif(empty($data['title']) && $start && empty($end)){
			if($list =$diary->where('create_time','>',$start)->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}elseif(empty($data['title']) && empty($start) && $end){
			if($list =$diary->where('create_time','<',$end)->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}elseif($data['title'] && empty($start) && $end){
			if($list =$diary->where([
				'title'=>['like','%'.$data['title'].'%'],
				'create_time'=>['<',$end]
				])->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}elseif($data['title'] && $start && empty($end)){
			if($list =$diary->where([
				'title'=>['like','%'.$data['title'].'%'],
				'create_time'=>['>',$start]
				])->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}elseif(empty($data['title']) && $start && $end){
			if($list =$diary->where([
				'create_time'=>['>',$start],
				'create_time'=>['<',$end]
				])->paginate(10,false,$pageParam)){
				$this->assign('data',$data);
				$this->assign('list',$list);
				return $this->fetch('index');
			}
		}
	}
}
?>