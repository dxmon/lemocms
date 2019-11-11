<?php
/**
 * lemocms
 * ============================================================================
 * 版权所有 2018-2027 lemocms，并保留所有权利。
 * 网站地址: https://www.lemocms.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/8/26
 */
namespace app\admin\controller;
use app\common\controller\Backend;
use app\common\model\ArticleCate;
use think\facade\Db;
use think\facade\Lang;
use think\facade\Request;
use think\facade\View;
use lemo\helper\TreeHelper;
class Article extends  Backend {

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function index(){

        if(Request::isPost()){
            $keys = Request::post('keys','','trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('article')->alias('a')
                ->join('article_cate ac','a.pid = ac.id','left')
                ->field('a.*,ac.title as cate_name')
                ->where('a.title|a.content','like','%'.$keys.'%')
                ->order('a.sort desc,a.id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data'],'count'=>$list['total']];

        }
        return View::fetch();

    }

    public function add(){
        if(Request::isPost()) {
            $data = Request::post();
            $res = \app\common\model\Article::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }
        }else{

            $ArticleCate = ArticleCate::where('status',1)->select()->toArray();
            $ArticleCate= TreeHelper::cateTree($ArticleCate);
            $params['name'] = 'container';
            $params['content'] = '';
            $view = [
                'info' => '',
                'ArticleCate' => $ArticleCate,
                'title' => lang('add'),
                'ueditor'=>build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }
    }

    public function edit()
    {
        if(Request::isPost()){
            $data = Request::post();
            if(!$data['id']){
                $this->error(lang('invalid data'));
            }

            $res = \app\common\model\Article::update($data);
            if($res){
                $this->success(lang('edit success'));
            }else{
                $this->error(lang('edit fail'));

            }
        }else{
            $id =  Request::get('id');
            $ArticleCate = ArticleCate::where('status',1)->select()->toArray();
            $ArticleCate= TreeHelper::cateTree($ArticleCate);

            $info = \app\common\model\Article::find($id);
            $params['name'] = 'container';
            $params['content'] = $info['content'];
            $view = [
                'info' => $info,
                'ArticleCate' => $ArticleCate,
                'title' => lang('edit'),
                'ueditor'=>build_ueditor($params),
            ];
            View::assign($view);
            return View::fetch('add');
        }


    }

    public function state()
    {
        $id = Request::post('id');
        if (empty($id)) {
            $this->error('data not exist');
        }
        $info = \app\common\model\Article::find($id);
        $status = $info['status'] == 1 ? 0 : 1;
        $info->status = $status;
        $info->save();
        $this->success(lang('edit success'));

    }
    public function delete(){

        if(Request::isPost()){

            $id = Request::post('id');
            \app\common\model\Article::destroy($id);
            $this->success('delete success');
        }

    }

    public function articleCate(){
        if(Request::isPost()){
            $keys = Request::post('keys','','trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list= cache('articleCate');
            if(!$list) {
                $list = Db::name('article_cate')
                    ->where('title','like','%'.$keys.'%')
                    ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                    ->toArray();
                foreach($list['data'] as $k=>$v){
                    $list['data'][$k]['lay_is_open']=false;
                }
                cache('articleCate', $list, 3600);
            }


            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data'],'count'=>$list['total']];

        }
        return View::fetch();
    }

    public function cateAdd(){
        if(Request::isPost()) {
            $data = Request::post();
            $res = \app\common\model\ArticleCate::create($data);
            if ($res) {
                $this->success(lang('add success'));
            } else {
                $this->error(lang('add fail'));

            }
        }else{

            $ArticleCate = ArticleCate::where('status',1)->select()->toArray();
            $ArticleCate= TreeHelper::cateTree($ArticleCate);

            $view = [
                'info' => '',
                'ArticleCate' => $ArticleCate,
                'title' =>lang('add'),
            ];
            View::assign($view);
            return View::fetch('cate_add');
        }

    }
    public function cateEdit(){
        if(Request::isPost()){
            $data = Request::post();
            if(!$data['id']){
                $this->error(lang('invalid data'));
            }

            $res = \app\common\model\ArticleCate::update($data);
            if($res){
                $this->success(lang('edit success'));
            }else{
                $this->error(lang('edit fail'));

            }
        }else{
            $id =  Request::get('id');
            $ArticleCate = ArticleCate::where('status',1)->select()->toArray();
            $ArticleCate= TreeHelper::cateTree($ArticleCate);

            $info = \app\common\model\ArticleCate::find($id);

            $view = [
                'info' => $info,
                'ArticleCate' => $ArticleCate,
                'title' => lang('edit'),
            ];
            View::assign($view);
            return View::fetch('cate_add');
        }


    }

    public function cateState(){
        $data =  Request::post();
        $id = Request::post('id');
        if (empty($id)) {
            $this->error(lang('data not exist'));
        }
        $info  = \app\common\model\ArticleCate::find($id);
        $status = $info['status'] == 1 ? 0 : 1;
        $info->status = $status;
        $info->save();
        $this->success(lang('edit success'));

    }
    public function cateDel(){

        if(Request::isPost()){

            $id = Request::post('id');
            $child = \app\common\model\ArticleCate::where('pid',$id)->find();
            if($child){
                $this->error(lang('delete child first'));
            }
            if(\app\common\model\ArticleCate::destroy($id)){
                $this->success(lang('delete success'));
            }else{
                $this->error(lang('delete fail'));
            }
        }

    }


}