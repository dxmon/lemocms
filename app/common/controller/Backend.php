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
 * Date: 2019/9/21
 */

namespace app\common\controller;

use app\admin\model\Admin;
use app\admin\model\AuthRule;
use app\common\controller\base;
use lemo\helper\FileHelper;
use lemo\helper\SignHelper;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Lang;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
class Backend extends \app\common\controller\Base
{
    public $pageSize=15;
    public $menu = '';
    public $adminRules='';
    public $hrefId='';
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        //判断管理员是否登录
        if (!session('admin.id') && !session('admin')) {
            $this->redirect(url('/admin/login/index'));
        }
        $controller = $this->request->controller();
        if(strpos($controller,'.')!==false){
            $module = explode('.',$controller)[0];
        }else{
            $module = $controller;
        }
        $this->pageSize = $this->request->param('limit')?$this->request->param('limit'):15;
        $this->authCheck();
        //加载语言包
        $this->loadlang(strtolower($module));
    }
    //加载插件语言
    protected function loadlang($name)
    {
        Lang::load([
            $this->app->getAppPath() . 'lang'.DIRECTORY_SEPARATOR. Lang::getLangset().DIRECTORY_SEPARATOR. $name.'.php'
        ]);
    }
    /**
     * 验证权限
     */
    public function authCheck(){

        $allow = [
            'admin/index/index',
            'admin/index/main',
            'admin/index/clearData',
            'admin/index/logout',
            'admin/login/password',
        ];
        $route = app('http')->getName().'/'.strtolower(Request::controller()).'/'.(Request::action());
        if(session('admin.id')!==1){
            $this->hrefId = Db::name('auth_rule')->where('href',$route)->value('id');
            //当前管理员权限
            $map['a.id'] = Session::get('admin.id');
            $rules=Db::name('admin')->alias('a')
                ->join('auth_group ag','a.group_id = ag.id','left')
                ->where($map)
                ->value('ag.rules');
            //用户权限规则id
            $adminRules = explode(',',$rules);
            // 不需要权限的规则id;
            $noruls = AuthRule::where('auth_open',1)->column('id');
            $this->adminRules = array_merge($adminRules,$noruls);
            if($this->hrefId){
                // 不在权限里面，并且请求为post
                if(!in_array($this->hrefId,$this->adminRules)){
                    $this->error(lang('permission denied'));
                    exit();
                }
            }else{
                if(!in_array($route,$allow)) {
                    $this->error(lang('permission denied'));
                    exit();
                }

            }
        }
        return $this->adminRules;

    }
    /**
     * 退出登录
     */
    public function logout()
    {
        session('admin',null);
        Session::clear();
        $this->success(lang('logout success'), '@admin/login');
    }

    /*
     * 修改密码
     */
    public function password(){
        if (!Request::isPost()){

            return View::fetch('login/password');

        }else{
            if( Request::isPost() and Session::get('admin.id')===3){
                $this->error(lang('test data cannot edit'));
            }

            $data =  Request::post();
            $oldpassword = Request::post('oldpassword', '123456', 'lemo\helper\StringHelper::filterWords');
            $admin = Admin::find($data['id']);
            if(!password_verify($oldpassword, $admin['password'])){
                $this->error(lang('origin password error'));
            }
            $password = Request::post('password', '123456','lemo\helper\StringHelper::filterWords');
            try {
                $data['password'] = password_hash($password,PASSWORD_BCRYPT, SignHelper::passwordSalt());

                if(Session::get('admin.id')==1){
                    Admin::update($data);
                }elseif(Session::get('admin.id')==$data['id']){
                    Admin::update($data);
                }else{
                    $this->error(lang('permission denied'));
                }

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success(lang('edit success'));

        }
    }

    public function base(){
        if (!Request::isPost()){
            return View::fetch('admin/password');
        }else{
            $data =  Request::post();
            $admin = Admin::find($data['id']);
            $oldpassword = Request::post('oldpassword', '123456', 'lemo\helper\StringHelper::filterWords');
            if(!password_verify($oldpassword, $admin['password'])){
                $this->error(lang('origin password error'));
            }
            $password = Request::post('password', '123456','lemo\helper\StringHelper::filterWords');
            try {
                $data['password'] = password_hash($password,PASSWORD_BCRYPT, SignHelper::passwordSalt());

                if(Session::get('admin.id')==1){
                    Admin::update($data);
                }elseif(Session::get('admin.id')==$data['id']){
                    Admin::update($data);
                }else{
                    $this->error(lang('permission denied'));
                }

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success(lang('edit success'));

        }
    }

    /*
     * 清除缓存 出去session缓存
     */
    public function clearData(){
        $dir = config('admin.clear_cache_dir') ? app()->getRootPath().'runtime/admin' : app()->getRootPath().'runtime';
        Cache::clear();
        if(FileHelper::delDir($dir) ){
            $this->success('清除成功');
        }
    }




}