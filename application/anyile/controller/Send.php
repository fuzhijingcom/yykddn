<?php
namespace app\anyile\controller;
use app\home\logic\UsersLogic;

use Think\Db;
class Send extends MobileBase {

    public function chenggong() {
       
        $order_id = I('order_id/d');
       
        $type = I('type');
    
       
        $this->assign('type',$type);
        $this->assign('order_id',$order_id);
       
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