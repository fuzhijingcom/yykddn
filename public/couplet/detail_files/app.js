(function($) {
  'use strict';
  $(function() {
	  $("#imgverifycode").click(function(){
		  var src = "/login/verify" +  '?' + Math.random();
	      $(this).attr('src',src);
	  });
	  $(".am-btn-danger").on("touchstart",function(){
	  		$(this).addClass("focus");
	  });
	  $(".am-btn-danger").on("touchend",function(){
	  		$(this).removeClass("focus");
	  });
	  $(".am-btn-red").on("touchstart",function(){
	  		$(this).addClass("am-btn-focus");
	  });
	  $(".am-btn-red").on("touchend",function(){
	  		$(this).removeClass("am-btn-focus");
	  });
	  $(".index-ico").on("touchstart",function(){
	  	$(this).css("color",'#fff');
	  });
	  $(".index-ico").on("touchend",function(){
	  	$(this).css("color",'#fff');
	  });
  });
})(jQuery);
