_Bbc(function ($) {
	$("#sidebar__toggle").click(function(e) {
	  var sidebarleft = $("#sidebar__slider").css('left');
	  var sidebarwidth = $("#sidebar__slider").css('width');
	  var width = $(window).width();
	  var height = $(window).height();

	  if ((width >= 720)) {
	    if (sidebarleft == "0px") {
	      $("#sidebar__slider").animate({
	        left: -1 * parseInt(sidebarwidth)
	      });
	      $(".main-content").animate({
	        marginLeft: 0
	      });
	    } else {
	      $("#sidebar__slider").animate({
	        left: 0
	      });
	      $(".main-content").animate({
	        marginLeft: 20 + '%'
	      })
	    }
	  } else {
	    if (sidebarleft == "0px") {
	      $("#sidebar__slider").animate({
	        left: -1 * parseInt(sidebarwidth)
	      });
	      $(".main-content").animate({
	        marginLeft: 0
	      });
	    } else {
	      $("#sidebar__slider").animate({
	        left: 0
	      });
	      $(".main-content").animate({
	        marginLeft: 0 + '%'
	      })
	    }
	  }
	});
});