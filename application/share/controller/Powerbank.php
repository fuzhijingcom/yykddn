<?php
namespace app\share\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Powerbank extends MobileBase {
    
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
        	$this->assign('user',$user); //存储用户信息
        	
        }    
    }
    
    
    public function index()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        
        if($user_id == 0){
            $this->error('请先登陆');
        }
        
        $code = I('id');
        if(!$code){
        	$this->redirect('scan');
        }
        
        $power = M('powerbank')->where('code',$code)->find();
        if(!$power){
        	$this->error('该充电宝不存在','share/powerbank/index');
        }
        
        if($power['status'] == 0 && $power['user_id'] == $user_id){
        	$this->redirect(U('my',array('id'=>$code)));
        	exit;
        }
        if($power['status'] == 0 ){
        	$this->error('该充电宝正在被别人使用中','share/powerbank/index');
        }
        
        $address_id = I('address_id/d');
        if($address_id)
        	$address = M('user_address')->where("address_id", $address_id)->find();
        	else
        		$address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
        		
        		
        if(!$address){
        	$this->error('你不是老客户，暂时无法使用该服务','share/powerbank/index');
        	exit;
        }
        
        $this->assign('code',$code);
        $this->assign('address',$address);
        //dump($power);
    
        return $this->fetch();
    }
    public function my(){
    	$code = I('id');
    	
    	$order_id = I('order_id');
    	
    	if($code){
    		$order = M('kd_order')->where('user_note',$code)->order('order_id desc')->find();
    		if($order['user_id'] !== $this->user_id){
    			$this->error('该充电宝正在被别人使用中');
    		}
    	}
    	
    	if($order_id){
    		$order = M('kd_order')->where('order_id',$order_id)->find();
    	}
    	
    	
    	if(!$order){
    		$this->error('订单不存在');
    		exit;
    	}
    	$this->assign('order',$order);
    	
    	$code = $order['user_note'];
    	if(IS_POST){
    		
    		
    		M('powerbank')->where(array('code'=>$code))->save(array('user_id'=>0,'status'=>1));
    		$this->success('归还成功，欢迎 再次使用','share/powerbank/index');
    	}
    	
    	return $this->fetch();
    	
    }
    public function scan(){
    	
    	
    	return $this->fetch();
    }
    
    public function detail(){
    	$order_id = I('order_id');
    	$order = M('kd_order')->where('order_id',$order_id)->find();
    	if(!$order){
    		$this->error('订单不存在');
    		exit;
    	}
    	
    	
    	//处理充电宝信息
    	
    	$code = $order['user_note'];
    	M('powerbank')->where(array('code'=>$code))->save(array('user_id'=>$this->user_id,'status'=>0));
    	
    	
    	
    	$this->assign('order',$order);
    	return $this->fetch();
    }
    
    public function xiadan_add(){
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
    
        $address_id = I("address_id/d"); //  收货地址id
        $user_note = trim(I('user_note'));   //买家留言

        $kuaidi_name = "充电宝";
        $order_amount = 1;
        //默认一块钱
        $type  = "cdb";

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
           'qiang'=>0,
        );
    
        $order_id = M("kd_order")->insertGetId($data);
    
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '您提交了订单，未拿',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        M('kd_order_action')->insertGetId($action_info);
    
        //header("Location: ".U('kuaidi/send/chenggong',array('order_id'=>$order_id,'source'=>'kuaidi' )));
        header("Location: ".U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>'kuaidi' )));
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