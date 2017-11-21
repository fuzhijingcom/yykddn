<?php
namespace app\anyile\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Xiadan extends MobileBase {
    
    public function _initialize(){
        parent ::_initialize();
        $sitting = M('kd_sitting')->where('name','kuaidi')->find();
        $value = $sitting['value'];
        if($sitting['status'] == 0 ){
            $this->redirect('kuaidi/index/errortime', array('errmsg' => $value), 2 , '页面跳转中...');
        }
    }
    
    
    public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        $type = I('type');
        if(!$type){
            $this->error('快递种类错误');
        }
 
    
        if($user_id == 0){
            $this->error('请先登陆');
        }
        $address_id = I('address_id/d');
        if($address_id)
            $address = M('user_address')->where("address_id", $address_id)->find();
        else
            $address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
    
        if(empty($address)){
            header("Location: ".U('anyile/User/add_address',array('source'=>'an','type'=>$type)));
        }else{
            $this->assign('address',$address);
        }
    
        $kd_model = M('anyile_kd');
        $kuaidi = $kd_model->where("type", $type)->find();
         
      
        $this->assign('kuaidi', $kuaidi );
        $this->assign('type',  $type);
     
    
        return $this->fetch();
    }
    
    public function xiadan_add(){
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
    
        $address_id = I("address_id/d"); //  收货地址id
        $user_note = trim(I('user_note'));   //买家留言

        $kuaidi_name = I("kuaidi_name");
        $order_amount = I("order_amount");
        $type  = I("type");

        $address = M('UserAddress')->where("address_id", $address_id)->find();
    
        if($address['sushe']==null){
            $this->error('宿舍不能为空，请重新编辑地址');
        }
    
        $data = array(
            'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$user_id, // 用户id
            'user_name'          =>$user_name, // 用户名
            'consignee'        =>$address['consignee'], // 收货人
            'province'         =>$address['province'],//'省份id',
            'city'             =>$address['city'],//'城市id',
            'district'         =>$address['district'],//'县',
            'twon'             =>$address['twon'],// '街道',
            'address'          =>$address['address'],//'详细地址',
            'mobile'           =>$address['mobile'],//'手机',
            'zipcode'          =>$address['zipcode'],//'邮编',
            'sushe'            =>$address['sushe'],//'宿舍',
            'duanhao'            =>$address['duanhao'],//'短号',
            'type'    =>           $type, //'物流编号',
            'kuaidi_name'    =>    $kuaidi_name, //'快递名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            'order_amount'     => $order_amount,
            'add_time'         =>date('Y-m-d H:i:s'), // 下单时间
            'user_note'        =>$user_note, // 用户下单备注
           
        );
    
        $order_id = M("anyile_kd_order")->insertGetId($data);
    
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '您提交了订单，未拿',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        M('anyile_kd_order_action')->insertGetId($action_info);
    
        header("Location: ".U('anyile/send/chenggong',array('order_id'=>$order_id,'source'=>'kuaidi' )));
       
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