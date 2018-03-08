<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\work\logic\WorkLogic;

use think\Page;
use think\Db;
class Send extends MobileBase {

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
                  
        if(!$grade || $grade < 1  ){
              $this->redirect(U("work/error/noauth"));
            exit;
        }
        
    }
    


    public function index(){

        //$con['user_id'] = array('elt',100);
        $con['is_validated'] = 1;
        $con['qun'] = 1;
        $con['tuisong'] = 1;
        $con['subscribe'] = 1;
        $con['country'] !== 1;
        $list = M('users_qiang')->where($con)->field('user_id,openid')->select();

        $c = count($list);

        $jssdk = new \app\mobile\logic\Jssdk("wx9a1f0bef4dbb9da0","9879b778276fe4349cbc53d835d70286");
        $wx_content = "女王节福利：

取消平台服务费
抢单收益更大！
赚得比平时更多！
赶紧开始抢单吧！

（订单有限，先抢先得）";

        for($i=0;$i<$c;$i++){

            $out = $jssdk->push_msg($list[$i]['openid'],$wx_content);
            dump($out);
            if($out['msgcode'] == 0){
                M('users_qiang')->where(array('user_id'=>$list[$i]['user_id']))->save(array('country'=>1,'province'=>$out['errmsg']));
            }else{
                M('users_qiang')->where(array('user_id'=>$list[$i]['user_id']))->save(array('country'=>0,'subscribe'=>0,'province'=>$out['errmsg']));
            }

        }

        echo $c;
        //return $this->fetch();
    }
    
    
   
 
}