<?php
namespace app\send\controller;
use think\Db;
use think\Controller;

class Api extends Controller {
    
    public function save()
    {
        $user_id = I('user_id');
        $number = I('number');
        $type = I('type');
        $time = I('time');

        $id = M('send')->add(array('number'=>$number,'user_id'=>$user_id,'type'=>$type,'time'=>$time));

        return $id;

    }
    
    public function send(){

        $s0 = M('send')->where(array('status'=>0))->field('id,number')->select();
        $c = count($s0);
        // dump($s0);
        for($i=0;$i<$c;$i++){
            // dump($s0[$i]['id']);
            // dump($s0[$i]['number']);
            $num = $s0[$i]['number'];
            $openid = M('users_mobile')->where(array('number'=>$num))->getField('openid');
            if($openid){
                $id = $s0[$i]['id'];
                $res = $this->send_gzh($id,$openid);
                $err = json_decode($res,true);
                $errcode = $err['errcode'];
                if((int)$errcode == 0){
                    M('send')->where(array('id'=>$id))->save(array('status'=>1));
                    //发送成功
                }
                
            }
        }


        //查询发送数据
        $user_id = I('user_id');
        if((int)$user_id > 0 ){
            $con['user_id'] = $user_id;
            $con['status'] = 1;
            $con['add_time'] = array('like','%'.date('Y-m-d').'%');
            $cc = M('send')->where($con)->field('id')->select();
            $cc = count($cc);
            echo $cc;
        }
    }

    private function send_gzh($id,$openid){
        $content = M('send')->where(array('id'=>$id))->find();

            
        $type = $content['type'];
        $kuaidi_name = M('kd')->where(array('type'=>$type))->getField('kuaidi_name');
        $address = $content['address'];
        $time = $content['time'];
        
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"zEeOZxDW1D0wcB4WuV7ZqtHpEDb8iyioQGeoZhtNugk",
            'url'=>"https://item.jd.com/4804713.html",
            'data'=>array(
                'first'=>array(
                    'value'=>"你的快件已达校园快递点。
如需代拿：点击下方 菜单栏 -> 点此下单
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $kuaidi_name,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>'广轻南海校区',
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> '点击菜单栏联系客服',
                    'color'=>"#000000"
                ),
                'keyword4'=>array(
                    'value'=>$address."
取件时间：".$time,
                    'color'=>"#000000"
                ),
               
                'remark'=>array(
                    'value'=>"
让运动富有激情，运动不惧雨汗
dacom运动蓝牙耳机跑步无线耳机
适用于苹果安卓，会员秒杀价：￥128元
点击“详情”，马上拥有，送货到宿舍
一年内质量问题免费换新",
                    'color'=>"#FF0000"
                )
            )
        );
    
        $json = json_encode($json);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
        
        return $out;
    

    }
       
}