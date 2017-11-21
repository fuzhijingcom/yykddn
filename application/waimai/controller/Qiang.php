<?php
namespace app\waimai\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Qiang extends MobileBase {
    public $user_id = 0;
    public $user = array();
    /**
     * 析构流函数
    */
    public function  __construct() {
        parent::__construct();
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
        $grade =  M('yuangong') ->where("yid",$this->user_id)->getField('grade');
        $this->assign('grade',$grade);
        if($grade <= 5 ){
            $this->redirect(U("work/error/noauth"));
            exit;
        }
    
    }
    
    public function index(){
     
        
        
       return $this->fetch();
    }

 public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
       
        if($user_id == 0){
            $this->error('请先登陆');
        }
        
        $address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
        $this->assign('address',$address);
        return $this->fetch();
    }
    
    public function xiadan_add(){
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        
        $fen = trim(I('fen'));   //买家留言，几份
       
        
        $type = 'wm';

        $kuaidi = M('kd')->where("type", $type)->find();
       
       
       
        
        $kuaidi_name = $kuaidi["kuaidi_name"];
        
        $price = $kuaidi["price"];
        
        $qiang = $kuaidi["qiang"];
        
        $order_amount = (float)$price * (int)$fen; //计算总价格
       
        $data = array(
            'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$user_id, // 用户id
            'user_name'        =>$user_name, // 用户名
            'pay_status'        =>1, //支付状态
            'pay_time'        => time(), //支付时间
            'consignee'        =>I('name'), // 收货人
            'address'          =>I('sushe'),//'详细地址',
            'mobile'           =>I('mobile'),//'手机',
            'sushe'            =>I('sushe'),//'宿舍',
            'duanhao'        =>I('duanhao'),//'短号',
            'type'    =>           $type, //'物流编号',
            'kuaidi_name'    =>    $kuaidi_name, //'快递名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            'order_amount'     => $order_amount,
            'add_time'         =>date('Y-m-d H:i:s'), // 下单时间
            'user_note'        => $fen.'份。', // 用户下单备注，加上货号
            'qiang'        =>   $qiang, // 是否抢单
           'admin_note'   =>   $fen, // 几份
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
    
        
        if($qiang == 1){
            $qiangurl =  "http://www.yykddn.com/kuaidi/send/send_all?order_id=".$order_id;
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $qiangurl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $out=curl_exec($ch);
            curl_close($ch);
        }
        
       // header("Location: ".U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>'kuaidi' )));
       
        $this->success('下单成功','xiadan');
        
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