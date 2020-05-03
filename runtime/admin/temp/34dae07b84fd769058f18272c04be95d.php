<?php /*a:3:{s:52:"E:\lemocms1.0\app\admin\view\ucenter\user\index.html";i:1583037531;s:47:"E:\lemocms1.0\app\admin\view\common\header.html";i:1583038741;s:47:"E:\lemocms1.0\app\admin\view\common\footer.html";i:1582960894;}*/ ?>
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
                <legend><?php echo lang('user'); ?><?php echo lang('list'); ?></legend>
                <blockquote class="layui-elem-quote">
                    <div class="lemo-table">
                        <div class="layui-inline">
                            <input type="text" id='keys' name="keys" lay-verify="required"
                                   placeholder="<?php echo lang('pleaseEnter'); ?>" autocomplete="off" class="layui-input">
                        </div>
                        <a  href="javascript:;" class="layui-btn data-add-btn layui-btn-sm" lay-submit="" lay-filter="add" id="search">
                            <?php echo lang('search'); ?>
                        </a>
                        <a data-href="<?php echo url('add'); ?>" class="layui-btn layui-btn-sm layui-btn-warm add"><?php echo lang('add'); ?><?php echo lang('user'); ?></a>
                        <a href="javascript:;"  class="layui-btn layui-btn-sm layui-btn-danger" id="delAll"><?php echo lang('delete checked'); ?></a>

                    </div>

                </blockquote>

            </fieldset>

            <table class="layui-table" id="list" lay-filter="list"></table>
    </div>
</div>


<script type="text/html" id="action">
    <a data-href="<?php echo url('edit'); ?>?id={{d.id}}" class="layui-btn  layui-btn-xs" lay-event="edit"><?php echo lang('edit'); ?></a>
    <a  class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><?php echo lang('del'); ?></a>
</script>
<script type="text/html" id="status">
    <input type="checkbox" name="status" value="{{d.id}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="status" {{ d.status == 1 ? 'checked' : '' }}>
</script>
<script type="text/html" id="sex">
    {{# if(d.sex==1){ }}
    男
    {{# }else if( d.sex==2){ }}
    女
    {{# }else{ }}
        保密
    {{# } }}
</script>
<script type="text/html" id="avatar">
    {{d.avatar}}<img src="/static/admin/images/image.gif" onmouseover="layer.tips('<img src={{d.avatar}}>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();">
</script>

<script type="text/html" id="last_login">
    {{layui.util.toDateString(d.last_login*1000, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<script type="text/html" id="create_time">
    {{layui.util.toDateString(d.create_time*1000, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!--<script type="text/html" id="update_time">-->
<!--    {{layui.util.toDateString(d.update_time*1000, 'yyyy-MM-dd HH:mm:ss')}}-->
<!--</script>-->

<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
<!--        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>-->
<!--        <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>-->
<!--        <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>-->
    </div>
</script>
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
    var tableIn = null;
    layui.config({
        base: "/static/admin/js/",
        version: true
    }).extend({
        Admin:'Admin',
    }).use(['table','Admin'], function () {
        var $ = layui.jquery,
            table = layui.table;

        tableIn = table.render({
            elem: '#list',
            url: '<?php echo url("index"); ?>',
            method: 'post',
            title: '用户数据表',
            toolbar: '#toolbar', //开启头部工具栏，并为其绑定左侧模板

            defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                title: '提示'
                ,layEvent: 'LAYTABLE_TIPS'
                ,icon: 'layui-icon-tips' }],
            cols: [[
                {checkbox: true, fixed: true},
                {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
                {field: 'username', title: '名字', width: 120,},
                {field: 'email', title: '邮箱', width: 120, },
                {field: 'mobile', title: '手机', width: 120, },
                {field: 'sex', title: '性别', width: 120, templet:"#sex"},
                {field: 'level_name', title: '会员等级', width: 120, },
                {field: 'avatar', title: '头像', width: 120,templet:"#avatar" },
                // {field: 'store_id', title: '店铺id', width: 120,sort: true},
                {field: 'status', title: '状态', width: 180, templet:'#status'},
                {field: 'create_time', title: '注册时间', width: 180,templet:'#create_time'},
                // {field: 'update_time', title: '更新时间', width: 180,templet:'#update_time'},
                {field: 'last_login', title: '最后登录时间', width: 180,templet:'#last_login'},

                {title:'操作',width:150, toolbar: '#action',align:"center"},

            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 15,
            page: true
        });

    });


</script>