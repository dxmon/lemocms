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

use app\common\controller\Backend;
use think\facade\Request;
use think\facade\View;
use app\common\model\Link as LinkModel;
use think\Validate;

class Link extends Backend
{


    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function index()
    {
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = LinkModel::where('name', 'like', '%' . $keys . '%')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];
        }

        return View::fetch();

    }

    public function add()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try{
                $this->validate($data, 'Link');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }


            $res = LinkModel::create($data);
            if ($res) {
                $this->success(lang('add success'),url('index'));
            } else {
                $this->error(lang('add fail'));
            }
        }
        $view = [
            'info' => '',
            'title' => lang('add'),
        ];
        View::assign($view);
        return View::fetch();
    }

    public function edit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'Link');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $res = LinkModel::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('index'));
            } else {
                $this->error(lang('edit fail'));
            }
        }
        $info = LinkModel::find(Request::get('id'));
        $view = [
            'info' => $info,
            'title' => lang('edit'),
        ];
        View::assign($view);
        return View::fetch('add');

    }
    public function delete()
    {
        $id = Request::post('id');
        if ($id) {

            LinkModel::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));

        }
    }

    public function state()
    {
        $id = Request::post('id');
        if ($id) {
            $where['id'] = $id;

            $link = LinkModel::find($id);
            $where['status'] = $link['status'] ? 0 : 1;
            LinkModel::update($where);

            $this->success(lang('edit success'));

        } else {
            $this->error(lang('edit fail'));

        }


    }
}