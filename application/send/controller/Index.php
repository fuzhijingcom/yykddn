<?php
namespace app\send\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Index extends MobileBase {
    
    public function _initialize(){
        parent ::_initialize();
       
        // $this->cartLogic = new \app\home\logic\CartLogic();
        if(session('?user'))
        {
        	$user = session('user');
        	$user = M('users')->where("user_id", $user['user_id'])->find();
        	session('user',$user);  //覆盖session 中的 user
        	$this->user = $user;
            $this->user_id = $user['user_id'];
            $this->nickname = $user['nickname'];
        	$this->assign('user',$user); //存储用户信息
        	
        }    
    }
    
    
    public function index()
    {
        $user_id = session('user.user_id');
        
        if($user_id == 0){
            $this->error('请先登陆');
        }
        
        $this->assign("user_id",$this->user_id);
        $this->assign("nickname",$this->nickname);
        $kd  = M('kd')->where(array('send'=>1))->order('shunxu')->select();
        $this->assign("kd",$kd);
        return $this->fetch();
    }


    //微信Jssdk 操作类 用分享朋友圈 JS
    public function ajaxGetWxConfig(){
    	$askUrl = I('askUrl');//分享URL
    	$weixin_config = M('wx_user')->find(); //获取微信配置
    	$jssdk = new \app\mobile\logic\Jssdk($weixin_config['appid'], $weixin_config['appsecret']);
    	$signPackage = $jssdk->GetSignPackage(urldecode($askUrl));
    	if($signPackage){
    		$this->ajaxReturn($signPackage,'JSON');
    	}else{
    		return false;
    	}
    }
       
}