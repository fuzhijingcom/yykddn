<?php
namespace app\api\controller;
use think\Controller;
use think\Session;

class Menu extends Controller {
	/* {
		"type":"view",
		"name":"香港代购化妆品",
		"url":"http://www.yykddn.com/index.php/mobile/Goods/goodsList/id/855.html"
	} */
    
    public function create(){
        $access_token = access_token();
         
         $json='{
	        "button":[
	         {
				
	           "name":"点此下单",
	           "sub_button":[
	           {
				"type":"view",
				"name":"快递代拿下单",
				"url":"http://www.yykddn.com/kuaidi"   
	           },
	           {
				"type":"view",
				"name":"帮买东西下单",
				"url":"http://www.yykddn.com/song"   
	           },
             
	           {
				"type":"view",
				"name":"在线寄件快递",
				"url":"http://www.yykddn.com/jijian"   
	           },
                {
				"type":"view",
				"name":"申请自由快递员",
				"url":"http://www.yykddn.com/qiangdan/index"   
	           },
                {
				"type":"view",
				"name":"申请校园合伙人",
				"url":"http://www.yykddn.com/jiameng"   
	           }
	           
	           ]
	        },
	         {
	           "name":"查询订单",
	           "sub_button":[
                
	           {
				"type":"view",
				"name":"所有订单查询",
				"url":"http://www.yykddn.com/kuaidi/order/order_list"   
	           },
             
             {
	             "type":"view",
				"name":"联系在线客服",
				"url":"http://www.yykddn.com/kefu" 
	           },
	           {
				"type":"view",
				"name":"账户余额查询",
				"url":"http://www.yykddn.com/my/user/account"   
	           },
	          
                {
				"type":"view",
				"name":"抢单推送设置",
				"url":"http://www.yykddn.com/qiangdan/index/tuisong"   
	           },
              {
				"type":"view",
				"name":"抢单员工作台",
				"url":"http://www.yykddn.com/qiangdan"   
	           }
             ]
	        
	        },
	        
	        
	        {
	           "name":"手机维修",
	           "sub_button":[
				{
				"type":"view",
				"name":"手机回收下单",
				"url":"http://www.yykddn.com/repair/mobile/huishou"   
	           },
	           {
				"type":"view",
				"name":"电脑维修下单",
				"url":"http://www.yykddn.com/repair/computer"   
	           },
				{
				"type":"view",
				"name":"手机维修下单",
				"url":"http://www.yykddn.com/repair"   
	           },
              
	           {
				"type":"view",
				"name":"驿源充电宝",
				"url":"http://www.yykddn.com/share/powerbank/scan"   
	           },
               
              
	           {
				"type":"view",
				"name":"驿源【工作台】",
				"url":"http://www.yykddn.com/work/"   
	           }
	        
             ]
	          }
	        ]
	    }';
        
        
       
    
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
    
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
        var_dump($out);
    
    }
}




/* 
 * {
				"type":"view",
				"name":"代拿订单查询（智能柜）",
				"url":"http://www.yykddn.com/box/order/order_list"   
	           },
	           {
				"type":"view",
				"name":"寄快递订单查询",
				"url":"http://www.yykddn.com/jijian/order/order_list"   
	           },
 * 
 * 
 * 
 * 
 * {
				"type":"view",
				"name":"快递代拿（智能柜）",
				"url":"http://www.yykddn.com/box"   
	           },
 * 
 *  {
				"type":"view",
				"name":"外卖点餐",
				"url":"http://www.yykddn.com/waimai"
	           },
	           {
				"type":"view",
				"name":"驿源商城",
				"url":"http://www.yykddn.com/mobile"
	           }
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * $json='{
	        "button":[
	         {
				"type":"view",
				"name":"免费电影观看",
				"url":"http://www.9413520.cn/vippojie/"
	        },
	        {
				"type":"view",
				"name":"电影讨论区",
				"url":"http://www.yudw.com/forum.php?mod=forumdisplay&fid=169"
	        }
    
	  
	        ]
	          }
	        ]
	    }';
	    
	    
	    */