//  Theme Custom jquery file, 

    // Created on   : 20/10/2017. 
    // Theme Name   : Finpro.
    // Description  : Finpro - finance and business template.
    // Version      : 1.0.
    // Author       : @Unifytheme.
    // Developed by : @Unifytheme.
    
"use strict";


// Prealoder
 function prealoader () {
   if ($('#loader').length) {
     $('#loader').fadeOut(); // will first fade out the loading animation
     $('#loader-wrapper').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
     $('body').delay(350).css({'overflow':'visible'});
  };
 }



// WOW animation 
function wowAnimation () {
  if($('.wow').length) {
    var wow = new WOW(
    {
      boxClass:     'wow',      // animated element css class (default is wow)
      animateClass: 'animated', // animation css class (default is animated)
      offset:       80,          // distance to the element when triggering the animation (default is 0)
      mobile:       true,       // trigger animations on mobile devices (default is true)
      live:         true,       // act on asynchronously loaded content (default is true)
    }
  );
  wow.init();
  }
}



// placeholder remove
function removePlaceholder () {
  var inputField = $("input,textarea");
  if (inputField.length) {
    inputField.each(
        function(){
        $(this).data('holder',$(this).attr('placeholder'));
        $(this).on('focusin', function() {
            $(this).attr('placeholder','');
        });
        $(this).on('focusout', function() {
            $(this).attr('placeholder',$(this).data('holder'));
        });
            
    });
  }
}


// Banner slider 
function BannerSlider () {
  var banner = $("#finance-main-banner");
  if (banner.length) {
    banner.revolution({
      sliderType:"standard",
      sliderLayout:"auto",
      loops:true,
      delay:7000,
      navigation: {
         bullets: {
            enable: true,
            hide_onmobile: false,
            style: "uranus",
            hide_onleave: false,
            direction: "horizontal",
            h_align: "center",
            v_align: "bottom",
            h_offset: -15,
            v_offset: 30,
            space: 10,
            tmp: '<span class="tp-bullet-inner"></span>'
        }

      },
      responsiveLevels:[2220,1183,975,751],
          gridwidth:[1170,970,770,320],
          gridheight:[700,700,700,650],
          shadow:0,
          spinner:"off",
          autoHeight:"off",
          disableProgressBar:"on",
          hideThumbsOnMobile:"off",
          hideSliderAtLimit:0,
          hideCaptionAtLimit:0,
          hideAllCaptionAtLilmit:0,
          debugMode:false,
          fallbacks: {
            simplifyAll:"off",
            disableFocusListener:false,
          },
    });
  };
}


// Banner slider Home Two
function BannerSlidertwo () {
  var banner = $("#finance-main-banner-two");
  if (banner.length) {
    banner.revolution({
      sliderType:"standard",
      sliderLayout:"auto",
      loops:true,
      delay:7000,
      navigation: {
         bullets: {
            enable: true,
            hide_onmobile: false,
            style: "uranus",
            hide_onleave: false,
            direction: "horizontal",
            h_align: "center",
            v_align: "bottom",
            h_offset: -15,
            v_offset: 30,
            space: 10,
            tmp: '<span class="tp-bullet-inner"></span>'
        }

      },
      responsiveLevels:[2220,1183,975,751],
          gridwidth:[1170,970,770,320],
          gridheight:[940,940,800,700],
          shadow:0,
          spinner:"off",
          autoHeight:"off",
          disableProgressBar:"on",
          hideThumbsOnMobile:"off",
          hideSliderAtLimit:0,
          hideCaptionAtLimit:0,
          hideAllCaptionAtLilmit:0,
          debugMode:false,
          fallbacks: {
            simplifyAll:"off",
            disableFocusListener:false,
          },
    });
  };
}


// Main Menu Function 
function themeMenu () {
  var menu= $("#mega-menu-holder");
  if(menu.length) {
    menu.slimmenu({
      resizeWidth: '991',
      animSpeed:'medium',
      indentChildren: true,
    });
  }
}


// Fancybox 
function FancypopUp () {
  var popBox = $(".fancybox");
  if (popBox.length) {
      popBox.fancybox({
      openEffect  : 'elastic',
        closeEffect : 'elastic',
         helpers : {
            overlay : {
                css : {
                    'background' : 'rgba(0, 0, 0, 0.6)'
                }
            }
        }
    });
  };
}


// Counter function
function CounterNumberChanger () {
  var timer = $('.timer');
  if(timer.length) {
    timer.appear(function () {
      timer.countTo();
    })
  }
}



// Finance Client Slider
function financeClientSlider () {
  var cSlider = $ (".finance-client-slider");
  if(cSlider.length) {
     cSlider.owlCarousel({
        loop:true,
        nav:false,
        dots:false,
        autoplay:true,
        autoplayTimeout:4000,
        autoplaySpeed:1200,
        lazyLoad:true,
        dragEndSpeed:1000,
        responsive:{
            0:{
                items:1
            },
            992:{
                items:2
            }
        }
    })
  }
}



// Finance Blog Slider 
function financeBlogSlider () {
  var bSlider = $ (".blog-side-carousel");
  if(bSlider.length) {
    bSlider.owlCarousel({
        loop:true,
        nav:true,
        navText: ["",""],
        dots:false,
        autoplay:true,
        autoplayTimeout:7000,
        autoplaySpeed:900,
        lazyLoad:true,
        navSpeed:1000,
        dragEndSpeed:1000,
        items:1,
    })
  }
}


// Partner Logo Footer 
function partnersLogo () {
  var pSlider = $ ("#partner-logo");
  if(pSlider.length) {
     pSlider.owlCarousel({
        loop:true,
        nav:false,
        dots:false,
        autoplay:true,
        autoplayTimeout:4000,
        autoplaySpeed:900,
        lazyLoad:true,
        dragEndSpeed:1000,
        responsive:{
            0:{
                items:1
            },
            450:{
                items:2
            },
            700:{
                items:3
            },
            1200:{
                items:4
            }
        }
    })
  }
}


// Project Slider
function projectSlider () {
  var pSlider = $ (".project-slider");
  if(pSlider.length) {
     pSlider.owlCarousel({
        loop:true,
        nav:false,
        dots:false,
        autoplay:true,
        autoplayTimeout:4000,
        autoplaySpeed:1200,
        lazyLoad:true,
        dragEndSpeed:1000,
        responsive:{
            0:{
                items:1
            },
            551:{
                items:2
            },
            992:{
                items:3
            },
            1200:{
                items:4
            }
        }
    })
  }
}


// Scroll to top
function scrollToTop () {
  var scrollTop = $ (".scroll-top")
  if (scrollTop.length) {

    //Check to see if the window is top if not then display button
    $(window).on('scroll', function (){
      if ($(this).scrollTop() > 200) {
        scrollTop.fadeIn();
      } else {
        scrollTop.fadeOut();
      }
    });
    
    //Click event to scroll to top
      scrollTop.on('click', function() {
      $('html, body').animate({scrollTop : 0},1500);
      return false;
    });
  }
}



//Contact Form Validation
function contactFormValidation () {
  var activeform = $(".form-validation");
  if(activeform.length){
      activeform.validate({ // initialize the plugin
        rules: {
          name: {
            required: true
          },
          email: {
            required: true,
            email: true
          },
          message: {
            required: true
          },
          phone: {
            required: true
          }
        },
      submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function() {
                        $('.form-validation :input').attr('disabled', 'disabled');
                        activeform.fadeTo( "slow", 1, function() {
                            $(this).find(':input').attr('disabled', 'disabled');
                            $(this).find('label').css('cursor','default');
                            $('#alert-success').fadeIn();
                        });
                    },
                    error: function() {
                        activeform.fadeTo( "slow", 1, function() {
                            $('#alert-error').fadeIn();
                        });
                    }
                });
            }
        });
  }
}

// Close suddess Alret
function closeSuccessAlert () {
  var closeButton = $ (".closeAlert");
  if(closeButton.length) {
      closeButton.on('click', function(){
        $(".alert-wrapper").fadeOut();
      });
      closeButton.on('click', function(){
        $(".alert-wrapper").fadeOut();
      })
  }
}


// Sticky header
function stickyHeader () {
  var sticky = $('.theme-main-menu'),
      scroll = $(window).scrollTop();

  if (sticky.length) {
    if (scroll >= 190) sticky.addClass('fixed');
    else sticky.removeClass('fixed');
    
  };
}


// Accordion panel
function themeAccrodion () {
  if ($('.theme-accordion > .panel').length) {
    $('.theme-accordion > .panel').on('show.bs.collapse', function (e) {
          var heading = $(this).find('.panel-heading');
          heading.addClass("active-panel");
          
    });
    
    $('.theme-accordion > .panel').on('hidden.bs.collapse', function (e) {
        var heading = $(this).find('.panel-heading');
          heading.removeClass("active-panel");
          //setProgressBar(heading.get(0).id);
    });

    $('.panel-heading a').on('click',function(e){
        if($(this).parents('.panel').children('.panel-collapse').hasClass('in')){
            e.stopPropagation();
        }
    });

  };
}

// Mixitup gallery
function mixitupGallery () {
  if ($("#mixitUp-item").length) {
    $("#mixitUp-item").mixItUp()
  };
}


// Related Product Slider
function productSlider () {
  var pSlider = $ (".related-product-slider");
  if(pSlider.length) {
      pSlider.owlCarousel({
        loop:true,
        nav:false,
        dots:false,
        autoplay:true,
        autoplayTimeout:4000,
        autoplaySpeed:1000,
        lazyLoad:true,
        smartSpeed:1000,
        responsive:{
            0:{
                items:1
            },
            550:{
                items:2
            },
            1200:{
                items:3
            }
        }
    })
  }
}


// Product value
function productValue () {
  var inputVal = $("#product-value");
  if(inputVal.length) {
    $("#value-decrease").on('click', function() {
        var v= inputVal.val()-1;
        if(v>=inputVal.attr('min'))
        inputVal.val(v)
      });

    $("#value-increase").on('click', function() {
      var v= inputVal.val()*1+1;
      if(v<=inputVal.attr('max'))
      inputVal.val(v)
    });
  }
}


// DOM ready function
jQuery(document).on('ready', function() {
	(function ($) {
	   removePlaceholder ();
     BannerSlider ();
     BannerSlidertwo ();
     financeClientSlider ();
     financeBlogSlider ();
     projectSlider ();
     themeMenu ();
     wowAnimation ();
     FancypopUp ();
     CounterNumberChanger ();
     partnersLogo ();
     scrollToTop ();
     contactFormValidation ();
     closeSuccessAlert ();
     themeAccrodion ();
     productSlider ();
     productValue ();
     mixitupGallery ();
  })(jQuery);
});


// Window load function
jQuery(window).on('load', function () {
   (function ($) {
		  prealoader ();
  })(jQuery);
 });


// Window scroll function
jQuery(window).on('scroll', function () {
  (function ($) {
    stickyHeader ();
  })(jQuery);
});
