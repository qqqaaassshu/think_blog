<?php
namespace app\admin\controller;
use think\Controller;
use think\Image;
use app\admin\controller\AdminAuth;
use app\admin\model\Article as ArticleModel;
use app\admin\model\System as SystemModel;
class Article extends AdminAuth{
	function index($type=""){
		$data =array('title'=>'','type'=>'0','start'=>'','end'=>'');
		$this->jianli_value();
		$this->assign('data',$data);
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		if($type){
			$list =ArticleModel::where('type',$type)->order('create_time','desc')->paginate(10);
			$this->assign('list',$list);
			return $this->fetch();
		}else{
			$list =ArticleModel::order('create_time','desc')->paginate(10);
			$this->assign('list',$list);
			return $this->fetch();
		}
	}

	function create(){
		$this->jianli_value();
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}

	function add_article(){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$request = Request();
		$data =$request->param();
		$system =SystemModel::get(1);
		$validate =validate('Article');
		if (!$validate->check($data)) {
	        return 'validate';
	    }
		//ueditor没有内容则不发送，因此给他设默认值
		$data['editorValue'] = $request->param('editorValue') ?$request->param('editorValue'):"";
		if(!empty($data['editorValue'])){
			//进行保存图片的正则替换，将临时图片保存至正式文件夹
			$pattern='/<[img|IMG].*?src=[\'|\"](\/static\/common.*?(?:[\.gif|\.jpg|\.jpeg|\.png]))[\'|\"].*?[\/]?>/i';
			preg_match_all($pattern,$data['editorValue'],$res);
			$num=count($res[1]);
			for($i=0;$i<$num;$i++){
				$ueditor_img=$res[1][$i];
				//新建日期文件夹
				$tmp_arr=explode('/',$ueditor_img);
				$datafloder=ROOT_PATH.'public/static/common/images/upload/ueditor/'.$tmp_arr[6];
				if(!is_dir($datafloder)){
					mkdir($datafloder,0777);
				}
				$tmpimg='./'.$ueditor_img;
				$newimg=str_replace('/ueditor_temp/','/ueditor/',$tmpimg);
				//转移图片
				if(rename($tmpimg, $newimg)){
					$data['editorValue']=str_replace('/ueditor_temp/','/ueditor/',$data['editorValue']);
				}
			}
		}
		$article = new ArticleModel;
		$article->title = $data['title'];
		$article->type = $data['type'];
		$article->intro = $data['intro'];
		$article->content = $data['editorValue'];
		$article->create_time = time();
		$article->update_time = time();
		if(date('Ymd',$system->date) !=date('Ymd')){
			$system->date =time();
			$system->today_article =1;
		}else{
			$system->today_article = $system->today_article+1;
		}
		$system->article_count =count(ArticleModel::all());
		if(ArticleModel::get(['title'=>$data['title'],'type'=>$data['type']])){
			$response = 'error';
		}else{
			if($article->save()){
				$system->save();
				$response = 'success';
			}
		}
		return $response;
	}

	function modify($id=''){
		$article = ArticleModel::get($id);
		$this->jianli_value();
		$this->assign('article',$article);
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		return $this->fetch();
	}

	function modify_article($id=''){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$request = Request();
		$data =$request->param();
		$validate =validate('Article');
		if (!$validate->check($data)) {
	        return 'validate';
	    }
		$data['editorValue'] = $request->param('editorValue');
		$article = ArticleModel::get($id);
		$a= new ArticleModel;
		$result = $a->where('title',$data['title'])->where('type',$data['type'])->where('id','<>',$id)->find();
		if($result){
			$response = 'error';
		} elseif ( $data['title']==$article->title  && $article->type == $data['type']&& $article->intro == $data['intro']  && $article->content == $data['editorValue']){
			$response = 'warn';
		}else{
			//转移ueditor文件
			$oldcontent = $article->content;
			if(!empty($data['editorValue'])){
				//正则表达式匹配查找图片路径
				$pattern='/<[img|IMG].*?src=[\'|\"](\/static\/common.*?(?:[\.gif|\.jpg|\.jpeg|\.png]))[\'|\"].*?[\/]?>/i';
				preg_match_all($pattern,$data['editorValue'],$res);
				$num=count($res[1]);
				$j=0;
				if($num > 0){
					for($i=0;$i<$num;$i++){
						$ueditor_img=$res[1][$i];
						//判断是否是新上传的图片
						$pos=stripos($ueditor_img,"/ueditor_temp/");
						if($pos>0){
							//新建日期文件夹
							$tmp_arr=explode('/',$ueditor_img);
							$datafloder=ROOT_PATH.'public/static/common/images/upload/ueditor/'.$tmp_arr[6];
							if(!is_dir($datafloder)){
								mkdir($datafloder,0777);
							}
							$tmpimg='./'.$ueditor_img;
							$newimg=str_replace('/ueditor_temp/','/ueditor/',$tmpimg);
							//转移图片
							if(rename($tmpimg, $newimg)){
								$data['editorValue']=str_replace('/ueditor_temp/','/ueditor/',$data['editorValue']);
							}
							$j++;
						}else {
							$imgarr[]=$ueditor_img;
						}
					}
				}else{
					$imgarr[]=[];
				}
				if($j==$num){
					$imgarr[]=[];
				}
			//删除在编辑时被删除的原有图片
			if(!empty($oldcontent)){
				//正则表达式匹配查找图片路径
				$pattern='/<[img|IMG].*?src=[\'|\"](\/static\/common\/images.*?(?:[\.gif|\.jpg|\.jpeg|\.png]))[\'|\"].*?[\/]?>/i';
				preg_match_all($pattern,$oldcontent,$oldres);
				$num=count($oldres[1]);
				if($num>0){
					for($i=0;$i<$num;$i++){
						$delimg=$oldres[1][$i];
						$tmp_arr=explode('/',$delimg);
						if(!in_array($delimg, $imgarr)){
							$delimg='.'.$delimg;
							if(file_exists($delimg)){
								unlink($delimg);
							}
						}
					}
					$dir = ROOT_PATH.'public/static/common/images/upload/ueditor/'.$tmp_arr[6];
					if(is_dir($dir)){
						array_diff(scandir($dir),array('..','.')) ?"":rmdir($dir);
					}
				}
			}
			}
			//将表单进行提交数据库
			$article->title = $data['title'];
			$article->type = $data['type'];
			$article->intro = $data['intro'];
			$article->content = $data['editorValue'];
			$article->update_time = time();
			if($article->save()){
				$response = 'success';
			}
		}
		return $response;
	}

	public function del($id =''){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$article = ArticleModel::get($id);
		$system =SystemModel::get(1);
		$oldcontent= $article->content;
		if(!empty($oldcontent)){
				//正则表达式匹配查找图片路径
				$pattern='/<[img|IMG].*?src=[\'|\"](\/static\/common.*?(?:[\.gif|\.jpg|\.jpeg|\.png]))[\'|\"].*?[\/]?>/i';
				preg_match_all($pattern,$oldcontent,$oldres);
				$num=count($oldres[1]);
				if($num>0){
					for($i=0;$i<$num;$i++){
						$delimg=$oldres[1][$i];
						$tmp_arr=explode('/',$delimg);
						$delimg='.'.$delimg;
						if(file_exists($delimg)){
							unlink($delimg);
						}
					}
					$dir = ROOT_PATH.'public/static/common/images/upload/ueditor/'.$tmp_arr[6];
					if(is_dir($dir)){
						array_diff(scandir($dir),array('..','.')) ?"":rmdir($dir);
					}
				}
			}
		if ($article) {
			if(date('Ymd',time($article->create_time))==date('Ymd')){
				$system->today_article = $system->today_article-1;
				$system->save();
			}
	        $article->delete();
	        $response ='success';
	    } else {
	    	$response ='error';
		}
		return $response;
	}
	public function status($id =""){
		if(!$this->adminAuth()){
			$response = 'level';
			return $response;
		}
		$article = ArticleModel::get($id);
		if($article->status ==1){
			$article->status = 0;
			$response ='0';
		}else{
			$article->status =1;
			$response ='1';
		}
		if(!$article->save()){
			$response ='error';
		}
		return $response;
	}
	public function search(){
		$request =Request();
		$article =new ArticleModel;
		$data = $request->param();
		$start = $data['start'] ? strtotime($data['start']):"";
		$end = $data['end'] ? strtotime($data['end']):"";
		$js=$this->AutoVersion('/static/admin/js/index.js');
		$this->assign('js',$js);
		$css=$this->AutoVersion('/static/admin/css/style.css');
		$this->assign('css',$css);
		$this->jianli_value();
		if(!empty($data['type'])){
			if(empty($data['title']) && empty($start) && empty($end)){
				if($list =$article->where('type',$data['type'])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif($data['title'] && empty($start) && empty($end)){
				if($list =$article->where([
					'title'=>['like','%'.$data['title'].'%'],
					'type'=>$data['type']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif($data['title'] && $start && empty($end)){
				if($list =$article->where([
					'title'=>['like','%'.$data['title'].'%'],
					'create_time'=>['>',$start],
					'type'=>$data['type']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif($data['title'] && empty($start) && $end){
				if($list =$article->where([
					'title'=>['like','%'.$data['title'].'%'],
					'create_time'=>['<',$end],
					'type'=>$data['type']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif(empty($data['title']) && $start && empty($end)){
				if($list =$article->where([
					'create_time'=>['>',$start],
					'type'=>$data['type']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif(empty($data['title']) && empty($start) && $end){
				if($list =$article->where([
					'create_time'=>['<',$end],
					'type'=>$data['type']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif(empty($data['title']) && $start && $end){
				if($list =$article->where([
					'create_time'=>['>',$start],
					'create_time'=>['<',$end],
					'type'=>$data['type']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}
		}else{
			if(empty($data['title']) && empty($start) && empty($end)){
				if($list =$article->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif($data['title'] && empty($start) && empty($end)){
				if($list =$article->where([
					'title'=>['like','%'.$data['title'].'%']
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif($data['title'] && $start && empty($end)){
				if($list =$article->where([
					'title'=>['like','%'.$data['title'].'%'],
					'create_time'=>['>',$start]
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif($data['title'] && empty($start) && $end){
				if($list =$article->where([
					'title'=>['like','%'.$data['title'].'%'],
					'create_time'=>['<',$end]
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif(empty($data['title']) && $start && empty($end)){
				if($list =$article->where([
					'create_time'=>['>',$start]
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif(empty($data['title']) && empty($start) && $end){
				if($list =$article->where([
					'create_time'=>['<',$end]
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}elseif(empty($data['title']) && $start && $end){
				if($list =$article->where([
					'create_time'=>['>',$start],
					'create_time'=>['<',$end]
					])->order('create_time','desc')->paginate(10,false,['query' => Request()->param()])){
					$this->assign('data',$data);
					$this->assign('list',$list);
					return $this->fetch('index');
				}
			}
		}
	}
}
?>