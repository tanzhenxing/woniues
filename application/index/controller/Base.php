<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller
{
    protected $user_login; // 当前登录的用户信息
    protected $site_info; // 当前网站信息
    protected $site_code = 'woniu_es'; // 网站唯一标识码
    protected $cos_code = 'tencent'; // 腾讯云存储唯一标识码

    /**
     * 基类初始化
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        // 获取网站信息
        $get_site_info = \app\common\controller\Site::info('woniu_es');
        if ($get_site_info['code']) {
            echo json($get_site_info);
            exit;
        }
        $site_info = $get_site_info['data'];
        $this->assign('site',$site_info);
        $this->site_info = $site_info;
        // 用户登录检测
        $user_login_check = \app\common\controller\User::loginCheck();
        if ($user_login_check['code']) {
            echo json($user_login_check);
            $this->redirect('/index/login/index'); // 跳转到登录页
            exit;
        }
        $user_login = $user_login_check['data']['user_login'];
        $this->assign('user_login',$user_login_check['data']['user_login']);
        $this->user_login = $user_login;
    }

    /**
     * 空操作
     * @return array
     */
    public function _empty()
    {
        $request_url = $this->request->domain() . $this->request->url();
        $result = array('code'=>1, 'message'=>'访问的网址:[ ' . $request_url .' ] 不存在');
        return json($result);
    }

}
