<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    // 后台root
   	'admin/login' =>'admin/login/index',
   	'admin/login_action' =>'admin/login/login_action',
   	'admin/logout' =>'admin/index/logout',

    'admin/modify_password' =>'admin/index/modify_password',

    'admin/modify_person' =>'admin/person/modify_person',
   	'admin/person_logo' =>'admin/person/person_logo',

    'admin/article/[:type]' =>'admin/article/index',
    'admin/create' =>'admin/article/create',
    'admin/add_article' =>'admin/article/add_article',
    'admin/modify/:id' =>'admin/article/modify',
    'admin/modify_article/:id' =>'admin/article/modify_article',
    'admin/upload_img' =>'admin/article/upload_img',
    'admin/del/[:id]' =>'admin/article/del',
    'admin/status/:id' =>'admin/article/status',
    'admin/search' =>'admin/article/search',

    //前台root
    'index/article/[:id]'=>'index/article/index',
    'index/person'=>'index/person/index',
    'index/test'=>'index/test/PseudoClasses',
    'index/vuestudy/index'=>'index/vuestudy/index',
    'index/[:type]'=>'index/index/index'
];
