<?php /*a:3:{s:50:"E:\lemocms1.0\app\admin\view\ucenter\user\add.html";i:1583037531;s:47:"E:\lemocms1.0\app\admin\view\common\header.html";i:1583038741;s:47:"E:\lemocms1.0\app\admin\view\common\footer.html";i:1582960894;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('admin.sys_name'); ?>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/static/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="/static/admin/css/main.css?v=<?php echo time(); ?>" media="all">
    <link rel="stylesheet" href="/static/plugins/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style id="lemo-bg-color">
    </style>
</head>
<div class="lemo-container">
    <div class="lemo-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend><?php echo htmlentities($title); ?></legend>
        </fieldset>
        <form class="layui-form layui-form-pane" lay-filter="form">
            <div class="layui-form-item">
                <label class="layui-form-label">所属等级</label>
                <div class="layui-input-inline">
                    <select name="level_id" lay-verify="required">
                        <option value="">请选择等级</option>
                        <?php if(is_array($userLevel) || $userLevel instanceof \think\Collection || $userLevel instanceof \think\Paginator): $i = 0; $__LIST__ = $userLevel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities($vo['id']); ?>" <?php if(!empty($info) && $info['level_id']==$vo['id']): ?> selected <?php endif; ?> ><?php echo htmlentities($vo['level_name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="username" lay-verify="required" placeholder="<?php echo lang('pleaseEnter'); ?>昵称" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    在4到25个字符之间。
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">手机</label>
                <div class="layui-input-inline">
                    <input type="text" name="mobile" placeholder="<?php echo lang('pleaseEnter'); ?>手机" lay-verify="phone" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    必须是正确的手机号
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">头像</label>
                <input type="hidden" name="avatar" id="avatar" class="attach">
                <div class="layui-input-inline">
                    <div class="layui-upload">
                        <button type="button" class="layui-btn layui-btn-primary" data-path='user' id="uploads"><i class="icon icon-upload3"></i>点击上传</button>
                        <button type="button" class="layui-btn layui-btn-primary" id="selectAttach" data-href="<?php echo url('sys.attach/select'); ?>?mime=image"><i class="icon icon-upload3"></i>选择</button>
                        <div class="layui-upload-list">
                            <img class="layui-upload-img" id="addPic">
                            <p id="notice"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><?php echo lang('email'); ?></label>
                <div class="layui-input-inline">
                    <input type="text" name="email" lay-verify="email" placeholder="<?php echo lang('pleaseEnter'); ?>邮箱" class="layui-input">
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><?php echo lang('sex'); ?></label>
                <div class="layui-input-block">
                        <input type="radio" name="sex" lay-filter="sex" checked value="1" title="男">
                        <input type="radio" name="sex" lay-filter="sex" value="2" title="女">
                        <input type="radio" name="sex" lay-filter="sex" value="0" title="保密">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input type="hidden" name="id"  >
                    <button type="button" class="layui-btn" lay-submit="" lay-filter="submit"><?php echo lang('submit'); ?></button>
                    <a data-href="<?php echo url('index'); ?>" class="layui-btn layui-btn-primary back"><?php echo lang('back'); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="/static/plugins/layui/layui.js" charset="utf-8"></script>
<!--<script>-->
<!--    layui.config({-->
<!--        base: "/static/admin/js/",-->
<!--        version: true-->
<!--    }).extend({-->
<!--        Admin: 'Admin'-->
<!--    }).use(['Admin'], function () {-->
<!--        Admin = layui.Admin;-->
<!--    });-->
<!--</script>-->

<script>
    layui.config({
        base: "/static/admin/js/",
        version: true
    }).extend({
        Admin:'Admin',
    }).use(['Admin','form','laydate'], function () {
        var form = layui.form, laydate=layui.laydate ,$=layui.$;
        var info = '';
        //日期范围
        laydate.render({
            elem: '#date'
            ,range: true
        });

        info = <?php echo json_encode($info); ?>;
        form.val("form", info);
        if(info){
            $('#addPic').attr('src',info.avatar);
        }
        form.render();

    });
</script>