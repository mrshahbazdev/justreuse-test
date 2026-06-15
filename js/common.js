jQuery(document).ready(function () {
	// $(".map").hide(); 
	// $(".show_map_toggle").click(function() {
    //     $(".map").toggle();
	// 	});
	jQuery('.cate_slider').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.cate_slider_left_arrow'),
		nextArrow: $('.cate_slider_right_arrow'),
		autoplay: false,
		autoplaySpeed: 1000,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 8,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1280,
				settings: {
				slidesToShow: 6,
				slidesToScroll: 1,
				infinite: true,
				dots: false
			}
			},
			{
				breakpoint: 1024,
				settings: {
				slidesToShow: 5,
				slidesToScroll: 1,
				infinite: true,
				dots: false
			}
			},
			{
				breakpoint: 640,
				settings: {
				slidesToShow: 4,
				slidesToScroll: 1
			}
			},
			{
				breakpoint: 480,
				settings: {
				slidesToShow: 2,
				slidesToScroll: 1
			}
			}
		]
	});
jQuery('.cat_slider').slick({
		dots: false,
		infinite: true,
		speed: 1500,
		arrows: false,
		autoplay: true,
		autoplaySpeed: 1000,
		slidesToShow: 8,
		slidesToScroll: 1,
		responsive: [
			{
				breakpoint: 1280,
				settings: {
				slidesToShow: 6,
				slidesToScroll: 1,
				infinite: true,
				dots: false
			}
			},
			{
				breakpoint: 1024,
				settings: {
				slidesToShow: 5,
				slidesToScroll: 1,
				infinite: true,
				dots: false
			}
			},
			{
				breakpoint: 640,
				settings: {
				slidesToShow: 4,
				slidesToScroll: 1
			}
			},
			{
				breakpoint: 480,
				settings: {
				slidesToShow: 3,
				slidesToScroll: 1
			}
			}
		]
	});

	jQuery('.cate_menu_slider').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.cate_menu_left_arrow'),
		nextArrow: $('.cate_menu_right_arrow'),
		autoplay: false,
		autoplaySpeed: 1500,
		centerMode: false,
		slidesToScroll: 1,
		slidesToShow: 1,
		variableWidth: true,
		responsive: [
		// {
		// breakpoint: 768,
		// settings: {
		// slidesToShow: 2,
		// slidesToScroll: 1,
		// variableWidth: false,
		// },
		// },
		// {
		// breakpoint: 640,
		// settings: {
		// slidesToShow: 1,
		// slidesToScroll: 1,
		// },
		// },
		]
	});

	
	jQuery('.recent_ads_61').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.recent_left_arrow'),
		nextArrow: $('.recent_right_arrow'),
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 6,
		slidesToScroll: 1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 768,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1

			}
			}
		]
	});
	
	
	jQuery('.recent_ads_51').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.recent_left_arrow'),
		nextArrow: $('.recent_right_arrow'),
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 5,
		slidesToScroll: 1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 768,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
			}
		]
	});
	
	
	jQuery('.recent_ads_41').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.recent_left_arrow'),
		nextArrow: $('.recent_right_arrow'),
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 4,
		slidesToScroll: 1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1

			}
			}
		]
	});
	
	jQuery('.Top_ads_61').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.topad_left_arrow'),
		nextArrow: $('.topad_right_arrow'),
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 6,
		slidesToScroll: 1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 768,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1

			}
			}
		]
	});
	
	
	jQuery('.Top_ads_51').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.topad_left_arrow'),
		nextArrow: $('.topad_right_arrow'),
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 5,
		slidesToScroll: 1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 768,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
			}
		]
	});
	
	
	jQuery('.Top_ads_41').slick({
		dots: false,
		infinite: false,
		speed: 1500,
		arrows: true,
		prevArrow: $('.topad_left_arrow'),
		nextArrow: $('.topad_right_arrow'),
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 4,
		slidesToScroll: 1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1

			}
			}
		]
	});


	// jQuery('.banner_image').slick({
	// dots: false,
	// infinite: true,
	// speed: 5000,
	// arrows: false,
	// autoplay: true,
	// autoplaySpeed: 5000,
	// centerMode: false,
	// variableWidth: false,
	// slidesToShow: 1,
	// slidesToScroll: 1,
	// pauseOnHover:true
	// });

	$('.banner_image').each(function() {
		var slider = $(this);
		slider.slick({
			dots: false,
			infinite: true,
			speed: 2000,
			arrows: false,
			autoplay: true,
			autoplaySpeed: 2000,
			slidesToShow: 1,
			slidesToScroll: 1,
			
		});

		var location = false;
		$("#location").click(function() {
			slider.slick("slickSetOption", "accessibility", location);
			slider.slick("slickSetOption", "draggable", location);
			slider.slick("slickSetOption", "swipe", location);
			slider.slick("slickSetOption", "touchMove", location);
			slider.slick("slickSetOption", "autoplay", location);

			location = !location;

			$(this).toggleClass("disabled");
		});

		var term = false;
		$("#term").click(function() {
			slider.slick("slickSetOption", "accessibility", term);
			slider.slick("slickSetOption", "draggable", term);
			slider.slick("slickSetOption", "swipe", term);
			slider.slick("slickSetOption", "touchMove", term);
			slider.slick("slickSetOption", "autoplay", term);

			term = !term;

			$(this).toggleClass("disabled");
		});
	});


	jQuery('.search_page_category_banner').slick({
		dots: false,
		infinite: true,
		speed: 1500,
		arrows: false,
		autoplay: true,
		autoplaySpeed: 1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow: 1,
		slidesToScroll: 1,
	});

	$('.product_image').each(function() {
		var slider = $(this);
		slider.slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: false,
		draggable: false,
		asNavFor: '.thumbnail_slider',
		rtl:dir_rtl,
		});
		var sLightbox = $(this);
		sLightbox.slickLightbox({
		src: 'src',
		itemSelector: '.prod_img img'
		});

		$('.thumbnail_slider').slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		asNavFor: '.product_image',
		arrows: true,
		prevArrow: $('.product_left_arrow'),
		nextArrow: $('.product_right_arrow'),
		dots: false,
		centerMode: false,
		focusOnSelect: true,
		rtl:dir_rtl,
		});
	});

	// $('.product_image').slick({
	//   slidesToShow: 1,
	//   slidesToScroll: 1,
	//   arrows: false,
	//   fade: false,
	//   asNavFor: '.thumbnail_slider'
	// });
	// $('.thumbnail_slider').slick({
	//   slidesToShow: 3,
	//   slidesToScroll: 1,
	//   asNavFor: '.product_image',
	//   arrows: true,
	// 	prevArrow: $('.product_left_arrow'),
	// 	nextArrow: $('.product_right_arrow'),
	//   dots: false,
	//   centerMode: false,
	//   focusOnSelect: true

	// });




	jQuery('.related_products').slick({
		dots: false,
		infinite:true,
		speed: 1500,
		arrows: true,
		prevArrow: $('.related_left_arrow'),
		nextArrow: $('.related_right_arrow'),
		autoplay: true,
		autoplaySpeed:1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow:4,
		slidesToScroll:1,
		rtl:dir_rtl,
		responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 768,
			settings: {
			slidesToShow: 3,
			slidesToScroll: 1
			}
			},
			{
			breakpoint: 640,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
			}
		]

	});


	jQuery('.recently_viewed').slick({
		dots: false,
		infinite:true,
		speed: 1500,
		arrows: true,
		prevArrow: $('.recent_left_arrow'),
		nextArrow: $('.recent_right_arrow'),
		autoplay: true,
		autoplaySpeed:1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow:2,
		slidesToScroll:1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 600,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
			},
			{
			breakpoint: 480,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
			}
			]
	});

	jQuery('.recently_viewed_mob').slick({
		dots: false,
		infinite:true,
		speed: 1500,
		arrows: true,
		prevArrow: $('.recent_mob_left_arrow'),
		nextArrow: $('.recent_mob_right_arrow'),
		autoplay: true,
		autoplaySpeed:1500,
		centerMode: false,
		variableWidth: false,
		slidesToShow:2,
		slidesToScroll:1,
		rtl:dir_rtl,
			responsive: [
			{
			breakpoint: 1024,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1,
			infinite: true,
			dots: false
			}
			},
			{
			breakpoint: 600,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1

			}
			},
			{
			breakpoint: 480,
			settings: {
			slidesToShow: 2,
			slidesToScroll: 1
			}
			}
		]
	});

	$('.menu_slider_owl.owl-carousel').owlCarousel({
		loop:false,
		nav:false,
		dots:false,
		items:5,
		autoWidth:false,
		mouseDrag: false,
		rtl:dir_rtl,//refer in header
		responsive:{
			481:{
				items:2
			},
			768:{
				items:3
			},
			1201:{
				items:5
			},
			1920:{
				items:5
			}
		}
	});

	var owl = $('.menu_slider_owl.owl-carousel');

	$('.custom-owl-buttons .owl-next').click(function() {
    owl.trigger('next.owl.carousel');
})

$('.custom-owl-buttons .owl-prev').click(function() {
    owl.trigger('prev.owl.carousel', [300]);
})

});






jQuery(document).ready(function () {
	if ($(window).width() < 640) {
		$(".nav-toggle").click(function () {
			$(".nav-content").toggleClass("hidden");
		});

		$(".chats").click(function () {
			$(".user_chats").addClass("hidden");
		});
	
		$(".chats").click(function () {
			$(".chat_detail").removeClass("hidden");
		});
	
		$(".close_btn").click(function () {
			$(".chat_detail").addClass("hidden");
			$(".user_chats").removeClass("hidden");
		});
	}

//dropdown open - post detail
	$( ".prod_info_title" ).click(function() {
		$(this).next(".prod_info_content").toggleClass("hidden");
		$(this).toggleClass("active");
	});

	
	// Flickering
	
		// (function($){  

			// var slider_menu = $(".menu_slider_owl");  

			// slider_menu.find("li").each(function() {  
				// if ($(this).find(".sub_menu").length > 0) {  

				// show subnav on hover  
				// $(this).mouseover(function() {  
				// $(this).find(".sub_menu").stop(true).slideDown(500);  
				// });  
				// hide submenus on exit  
				// $(this).mouseleave(function() {  
				// $(this).removeClass('active').find(".sub_menu").stop(true).slideUp(500);  
				// });  
				// }  
			// });

		// })(jQuery);

	// Flickering



	// MegaMenu Hover
	
	// $(document).ready(function(){
		// $(".menu_slider_owl li").hoverIntent(makeTall,makeShort);
	// }); 

	// function makeTall()
		// {
			// $(this).children(".sub_menu").stop(true, true).slideDown("slow");
		// }
	// function makeShort()
		// {
			// $(this).children(".sub_menu").stop(true, true).slideUp("slow");
		// }
		
	// MegaMenu Hover
		
	
	
	
	$(document).ready(function(){
		$(".login-pages").click(function(){
			if(!$(".login-pages-open").hasClass('opened')) {
				$(".login-pages-open").slideDown('slow',function(){$(this).addClass('opened')});
			} else {
				$(".login-pages-open").slideUp('slow',function(){$(this).removeClass('opened')});
			}
		});
		
		$(".login-pages").blur(function(){
			$(".login-pages-open").slideUp('slow',function(){$(this).removeClass('opened')});
		});
	});
	

	$(document).ready(function(){
		$(".lang-dropdown").click(function(){
				if(!$(".lang-dropdown-open").hasClass('opened')) {
					$(".lang-dropdown-open").slideDown('slow',function(){$(this).addClass('opened')});
				} else {
					$(".lang-dropdown-open").slideUp('slow',function(){$(this).removeClass('opened')});
				}
			});
		
		$(".lang-dropdown").blur(function(){
			$(".lang-dropdown-open").slideUp('slow',function(){$(this).removeClass('opened')});
		});
		
	});
	
	
	$(document).ready(function(){
		$(".lang-dropdown-mob").click(function(){
			if(!$(".lang-dropdown-mob-open").hasClass('opened')) {
				$(".lang-dropdown-mob-open").slideDown('slow',function(){$(this).addClass('opened')});
			} else {
				$(".lang-dropdown-mob-open").slideUp('slow',function(){$(this).removeClass('opened')});
			}
		});
		
		$(".lang-dropdown-mob").blur(function(){
			$(".lang-dropdown-mob-open").slideUp('slow',function(){$(this).removeClass('opened')});
		});
	});
	
	
	$(document).ready(function(){
		$(".search-icon").click(function(){
			if(!$(".search-field-open").hasClass('open')) {
				$(".search-field-open").slideDown('slow',function(){$(this).addClass('open')});
			} else {
				$(".search-field-open").slideUp('slow',function(){$(this).removeClass('open')});
			}
		});
		
		$(".search-icon").blur(function(){
			$(".search-field-open").slideDown('slow',function(){$(this).removeClass('open')});
		});	
	});
	
	
	$(document).ready(function() {
		if ($(window).width() > 767) {
		if($('#insights-sticky').length >0){

      var $window = $(window);  
			var $sidebar = $("#insights-sticky"); 
			var $sidebarHeight = $sidebar.innerHeight();   
			var $footerOffsetTop = $("#footer").offset().top; 
			var $sidebarOffset = $sidebar.offset();

			$window.scroll(function() {
				if($window.scrollTop() > $sidebarOffset.top) {
				$sidebar.addClass("fixed");   
				} else {
				$sidebar.removeClass("fixed");   
				}    
			});

    }
      
		}
	});
	
	$(document).ready(function () {
		$('#views-btn').click(function() {
			$('html, body').animate({
				scrollTop: $("#views-block").offset().top - 180
			}, 1000)
		}), 
		$('#engagement-btn').click(function (){
			$('html, body').animate({
				scrollTop: $("#engagement-block").offset().top - 160
			}, 1000)
		}),
		$('#visited-btn').click(function (){
			$('html, body').animate({
				scrollTop: $("#visited-block").offset().top - 120
			}, 1000)
		})
	});
	
	
	
	
	$(document).ready(function(){
		$(".all-cate-btn").click(function(){
			$(".responsive_menu").addClass("active");
		});
		
		$(".res_menu_close").click(function(){
			$(".responsive_menu").removeClass("active");
		});
	
	
	
		$('.child_cat_menu').hide(); 
		$('.sub_cat_menu span').click(function(){
			if( $(this).next().is(':hidden') ) {
				$('.sub_cat_menu span').removeClass('active').next().slideUp();
				$(this).toggleClass('active').next().slideDown();
			}
			else{
				$('.sub_cat_menu span').removeClass('active').next().slideUp();
			}
			return false;
		});
		
		
		$(".user-icon-dropdown").click(function(){
			$(".user-icon-dropdown-open").addClass("active");
		});
		
		$(".user-icon-close").click(function(){
			$(".user-icon-dropdown-open").removeClass("active");
		});
		
	
		
		$(document).ready(function(){
			// if ($(window).width() > 767) {
				$(".chat-area-adjust").click(function(){
					tab_operation('main');
				});
				$(".chat-area-adjust-right").click(function(){
					tab_operation('left');
				});
				$(".chat-area-adjust-left").click(function(){
					tab_operation('right');
				});
				
				$("body").delegate(".edit_offer_btn","click",function(){
					tab_operation('editoffer');
				});
				
				$(".chat-box").click(function(){
					tab_operation('chatbox');
				});
				
				$("body").delegate("#send_make_offer","click",function(){
					tab_operation('chatbox');
				});
				
				$("body").delegate(".questions","click",function(){
					tab_operation('main');
				});
			// }
		});
			
			
			function tab_operation(mode)
			{
					
					
					if(mode=="main")
					{
						
						
						
						if ($(window).width() > 1023) {
							$('.chat_converstion').animate({height:'640px'}, 500);
							$("#tab-contents").slideUp("slow");
							if ($(".chat_converstion").css("height") == "640px") {
								$(".chat_converstion").animate({"height": "460px"}, 500);
								$("#tab-contents").slideDown("slow");
							}
						}
						
						else if ($(window).width() > 767) {
							$('.chat_converstion').animate({height:'550px'}, 500);
							$("#tab-contents").slideUp("slow");
							if ($(".chat_converstion").css("height") == "550px") {
								$(".chat_converstion").animate({"height": "460px"}, 500);
								$("#tab-contents").slideDown("slow");
							}
						}
						
						else if ($(window).width() < 768) {
						
							var toggle_flag = 0;


							$('#tab-contents').slideToggle();

							if(toggle_flag==0){
								//code for slide down
								toggle_flag=1;
							}else{
								//code for slide up
								toggle_flag=0;
							}

						}
						
					}
					else if(mode=="editoffer")
					{
						if ($(window).width() > 767) {
							$("#tab-contents").slideDown("slow");
							$('.chat_converstion').animate({height:'460px'}, 500);
						}

						else if ($(window).width() < 768) {
							$("#tab-contents").slideDown("slow");
						}
					}
					
					else if(mode=="chatbox")
					{
						
						
						if ($(window).width() > 1023) {
							$("#tab-contents").slideUp("slow");
							$('.chat_converstion').animate({height:'640px'}, 500);
							if ($(".chat_converstion").css("height") == "460px") {
								$(".chat_converstion").animate({"height": "640px"}, 500);
							}
						}
						
						else if ($(window).width() > 767) {
							$("#tab-contents").slideUp("slow");
							$('.chat_converstion').animate({height:'550px'}, 500);
							if ($(".chat_converstion").css("height") == "460px") {
								$(".chat_converstion").animate({"height": "550px"}, 500);
							}
						}
						
						else if ($(window).width() < 768) {
							$('#tab-contents').slideUp();
						}
					}
					 
					else
					{
						if ($(window).width() > 767) {
							$('.chat_converstion').animate({height:'460px'}, 500);
						}
					}
				
			}
			if($('.show_bottom').length > 0 ){

        $('.show_bottom').scrollTop($('.show_bottom')[0].scrollHeight);
      }
			
			
	 
	});

	function checkStrength(password){
		let meterBar = $("#meter").find("#meter-bar");
		let meterStatus = $("#meter-text").find("#meter-status");
		let strength = 0;
		if(password.length < 6){
			meterBar.css({
				"background":"#6B778D",
				"width":"10%"
			});
			meterStatus.css("color","#6B778D");
			meterStatus.text("too short") ;
		}
		if(password.length > 7) strength += 1;
		if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1
		if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
		if(strength < 2){
			meterBar.css({
				"background":"#ef4444",
				"width":"25%"
			});
			meterStatus.css("color","#ef4444");
			meterStatus.text("weak");
		}else if(strength == 2){
			meterBar.css({
				"background":"#f59e0b",
				"width":"75%"
			});
			meterStatus.css("color","#f59e0b");
			meterStatus.text("good");
		}else{
			meterBar.css({
			"background":"#10b981",
			"width":"100%"
			});
			meterStatus.css("color","#10b981");
			meterStatus.text("strong");
		}
	}

	$(".password-strength").on("keyup",function(){
		checkStrength($(this).val());
	});
	

});
if($('#selected_category').length > 0){

  document.addEventListener("DOMContentLoaded", function() {
    var demo1 = new BVSelect({
      selector: "#selected_category",
      searchbox: false,
      offset: false
    });
  
    var demo1 = new BVSelect({
      selector: "#sub_category",
      searchbox: false,
      offset: false
    });
  
    var demo1 = new BVSelect({
      selector: "#currency_id",
      searchbox: false,
      offset: false
    });
  
  });

}
