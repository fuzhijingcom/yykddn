function goTopEx() { 
    var $obj = $("#goTopBtn");
    var win = $(window).scrollTop();
    var top = $("#container").offset().top;
    if(win>top){
        $obj.show(function(){
            $(this).css({
                "transition":'all 500ms linear',
                "transform":'translate(0px,-80px)'
            });
        });
    }else{
        $obj.hide(function(){
            $(this).css({
                "transition":'all 100ms linear',
                "transform":'translate(0px,80px)'
            });
        });
    }
    $(window).scroll(function(){
        var win = $(window).scrollTop();
        var top = $("#container").offset().top;
        if(win>top){
            $obj.show(function(){
                $(this).css({
                    "transition":'all 500ms linear',
                    "transform":'translate(0px,-80px)'
                });
            });
        }else{
            $obj.hide(function(){
                $(this).css({
                    "transition":'all 100ms linear',
                    "transform":'translate(0px,80px)'
                });
            });
        }
    });
    $obj.click(function(){
        $("html,body").animate({scrollTop:0},300);
    });
} 

//cookie 操作方法
function Cookie(){

}
Cookie.prototype.set = function(cname, cvalue, exdays){
    var d = new Date();
    exdays = exdays || 1000;
    d.setTime(d.getTime() + (exdays*24*60*60));
    var expires = "expires="+d.toUTCString();
    cvalue = escape(cvalue);
    document.cookie = cname + "=" + cvalue + ";" + expires+';path=/;'
}
Cookie.prototype.get = function(cname){
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return unescape(c.substring(name.length, c.length));
    }
}

Cookie.prototype.clear = function(name){
    this.set(name, "", -1);  
}

//搜索历史操作方法

function searchHistory(){
    this.cookie_name = 'search_history';
    this.max = 10;
    this.item = [];
    this.cookie = new Cookie(); 
}

searchHistory.prototype.add = function(c){
    var cookie  = this.cookie;
    var history_str = cookie.get(this.cookie_name);
    var history_split = [];
    var history_join = '';
    var delete_num = 0;

    if(typeof(history_str) != 'undefined'){
        history_split = history_str.split(',');
    }
    if($.inArray(c,history_split)==-1) {
        history_split.push(c);
        delete_num = history_split.length-this.max;
        history_split.splice(0,delete_num);
    }
    history_join = history_split.join(',');
    cookie.set(this.cookie_name,history_join);
}

searchHistory.prototype.get = function(){
    var cookie  = this.cookie;
    var history_str = cookie.get(this.cookie_name);
    var history_split = [];
    if(typeof(history_str) != 'undefined' && $.trim(history_str) !=''){
        
        history_split = history_str.split(','); 
    }
    return history_split;
}

searchHistory.prototype.clear = function(){
    var cookie  = this.cookie;
    cookie.clear(this.cookie_name);
}







