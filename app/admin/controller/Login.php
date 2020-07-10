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
 * Date: 2019/8/2
 */
namespace app\admin\controller;
use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\common\controller\Base;
use lemo\helper\SignHelper;
use think\facade\Session;
use think\facade\Request;
use think\captcha\facade\Captcha;

class Login extends Base {
    /*
     * 登录
     */
    public function initialize()
    {

        parent::initialize(); // TODO: Change the autogenerated stub

    }
    public function index(){
        if (!Request::isPost()) {
            $admin= Session::get('admin');
            $admin_sign= Session::get('admin_sign') == SignHelper::authSign($admin) ? $admin['id'] : 0;
            // 签名验证
            if ($admin && $admin_sign) {
                 redirect('index/index');
            }
            $view = ['loginbg'=>"/static/admin/images/bg.jpg"];
            return view('',$view);

        } else {
            $username = $this->request->post('username', '', 'lemo\helper\StringHelper::filterWords');
            $password = $this->request->post('password', '', 'lemo\helper\StringHelper::filterWords');
            $captcha = $this->request->post('captcha', '', 'lemo\helper\StringHelper::filterWords');
            $rememberMe = $this->request->post('rememberMe');
            // 用户信息验证
            try {
                if(!captcha_check($captcha)){
                    throw new \Exception(lang('captcha error'));
                }
                $res = self::checkLogin($username, $password,$rememberMe);
            } catch (\Exception $e) {

                 $this->error(lang('login fail')."：{$e->getMessage()}");
            }

            $this->success(lang('login success').'...',url('index/index'));
        }
    }

    /*
     * 验证码
     *
     */
    public function verify()
    {
        return Captcha::create();
    }

    /**
     * 根据用户名密码，验证用户是否能成功登陆
     * @param string $user
     * @param string $pwd
     * @throws \Exception
     * @return mixed
     */
    public static function checkLogin($user, $password,$rememberMe) {

        try {
            $where['username'] = strip_tags(trim($user));
            $password = strip_tags(trim($password));
            $admin = \app\admin\model\Admin::where($where)->find();
            if (!$admin) {
                throw new \Exception(lang('Please check username or password'));
            }
            if ($admin['status'] == 0) {
                throw new \Exception(lang('Account is disabled'));
            }
            if (!password_verify($password, $admin['password'])) {
                throw new \Exception(lang('Please check username or password'));
            }
            if (!$admin['group_id']) {
                $admin['group_id'] = 1;
            }
            $admin = $admin->toArray();
            $rules = AuthGroup::where('id', $admin['group_id'])
                ->value('rules');
            $admin['rules'] = $rules;
            if ($rememberMe) {
                $admin['expiretime'] = 30*24*3600 +time();
            }else{
                $admin['expiretime'] = 7*24*3600 +time();
            }
            unset($admin['password']);
            Session::set('admin', $admin);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }
}