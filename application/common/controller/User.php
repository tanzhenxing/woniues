<?php
namespace app\common\controller;

use app\common\validate\UserLogin;
use think\Controller;

class User extends Controller
{
    /**
     * 用户登录验证
     * @param $data
     * @return array
     */
    public static function loginValidate($data)
    {
        // 验证登录信息
        $validate = new UserLogin();
        if (!$validate) {
            $result = array('code'=>1,'message'=>$validate->getError());
            return $result;
        }
        // 检查用户名是否存在
        $get_user = \app\common\model\User::get(['username'=>$data['username']]);
        if(empty($get_user)) {
            $result = array('code'=>1,'message'=>'username not exist.');
            return $result;
        }
        // 检测密码是否正确
        // $password = password_hash($data['password'],PASSWORD_BCRYPT); // 密码加密方式
        if (!password_verify($data['password'],$get_user['password'])) {
            $result = array('code'=>1,'message'=>'password is error.');
            return $result;
        }
        // 保存用户登录session 记录
        session('username',$get_user['username']);
        // 返回结果
        $result = array('code'=>0,'message'=>'user login success','data'=>$get_user);
        return $result;
    }

    /**
     * 当前用户是否已登录检测
     * @return array
     */
    public static function loginCheck()
    {
        // 检查用户session是否存在
        $session_username = session('username');
        if(empty($session_username)) {
            $result = array('code'=>1,'message'=>'this user session is null,not login.');
            return $result;
        }
        // 检查用户是否登录
        $get_user = \app\common\model\User::get(['username'=>$session_username]);
        if(empty($get_user)) {
            $result = array('code'=>1,'message'=>'username is error.');
            return $result;
        }
        // 返回结果
        $result = array('code'=>0,'message'=>'user login check success','data'=>array('user_login'=>$get_user));
        return $result;
    }

}
