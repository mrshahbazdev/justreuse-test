function validateForm() {
  var chat_message = document.forms["chatform"]["chat_message"].value;
  var imgname = document.forms["chatform"]["filename"].value;
  if ((chat_message == "") && (imgname == "")) {
    toastr.warning("Please type any message!");
    return false;
  }
}

// search chat start
$(".search").click(function () {
  $(".search_div").css("display", "block");
  $(".heading_div").css("display", "none");
});
$(".close").click(function () {
  $(".search_div").css("display", "none");
  $(".heading_div").css("display", "block");
});
// search chat end

// share location start
$(window).on('load', function () {
  var elem = document.getElementById('messages_area');
  elem.scrollTop = elem.scrollHeight;
});

$('#share_loc_btn').on('click', function (e) {
  document.querySelector("#share_loc_popup").style.display = "block";
});

$('#share_loc_cancel').on('click', function (e) {
  document.querySelector("#share_loc_popup").style.display = "none";
});
// share location end

// make offer start

$("#make-offer-chat ul li").on('click', function (e) {
  e.preventDefault();
  var get_val = $(this).attr('data-defmsg');
  var get_currency = $(this).attr("data-defcurncy");
  $("#make-offer-chat ul li").removeClass("price-active");
  $(".make-offer-design p.offer-price").text(get_currency + get_val); // curency symbol need to change
  $(".make-offer-design input.offer-price").val(get_val);
  $(this).addClass("price-active");
  var inputVal = $(".offer_input").val(get_val);
  
  var rowVal = $(this).attr("row-val");
  
  // languages  - offer_title_1 variables declared in chat-list.blade.php file. 
		
	
	// rowVal == 5 means post price (1st price). rowVal == 1,2,3,4 means nxt four price.
	if(rowVal == 5)
	{
		$("#offer-title").text(offer_title_1);
		$("#offer-desc").text(offer_desc_1);
	}else if(rowVal == 1){
		$("#offer-title").text(offer_title_2);
		$("#offer-desc").text(offer_desc_2);
	}else if(rowVal == 2){
		$("#offer-title").text(offer_title_3);
		$("#offer-desc").text(offer_desc_3);
	}else if(rowVal == 3){
		$("#offer-title").text(offer_title_4);
		$("#offer-desc").text(offer_desc_4);
	}else if(rowVal == 4){
		$("#offer-title").text(offer_title_5);
		$("#offer-desc").text(offer_desc_5);
	}
  
  
});



//$("#tab-contents").hide();
// $(".chat-box").show();
$(document).ready(function () {
  $(".show-hide-redymade-chat").on('click', function () {
    //$(".chat-box").slideToggle("slow");
    //$("#tab-contents").slideToggle("slow");
  });
});


$(document).ready(function () {
	if ($(window).width() > 1023) {
		$(".defalut-chat li").on('click', function () {
			$("#tab-contents").slideUp("slow");
			$('.chat_converstion').animate({height:'640px'}, 500);
			if ($(".chat_converstion").css("height") == "300px") {
				$(".chat_converstion").animate({"height": "640px"}, 500);
			}
		});
	}

	else if ($(window).width() > 767) {
		$(".defalut-chat li").on('click', function () {
			$("#tab-contents").slideUp("slow");
			$('.chat_converstion').animate({height:'550px'}, 500);
			if ($(".chat_converstion").css("height") == "300px") {
				$(".chat_converstion").animate({"height": "550px"}, 500);
			}
		});
	}
	
	
	if ($(window).width() > 1023) {
		$("#send").on('click', function () {
			$("#tab-contents").slideUp("slow");
			$('.chat_converstion').animate({height:'640px'}, 500);
			if ($(".chat_converstion").css("height") == "300px") {
				$(".chat_converstion").animate({"height": "640px"}, 500);
			}
		});
	}

	else if ($(window).width() > 767) {
		$("#send").on('click', function () {
			$("#tab-contents").slideUp("slow");
			$('.chat_converstion').animate({height:'550px'}, 500);
			if ($(".chat_converstion").css("height") == "300px") {
				$(".chat_converstion").animate({"height": "550px"}, 500);
			}
		});
	}
});


$("body").delegate(".edit-offer", 'click', function (e) {
  e.preventDefault();
  var offered_price = $(this).attr('data-youroffer');
  var get_curency = $(this).attr("data-defcurency");
  //$(".chat-box").slideToggle("slow");
  //$("#tab-contents").slideToggle("slow");
  $("#tabs li").removeClass("tab-active");
  $("#tabs li:nth-child(2)").addClass("tab-active");
  $("#tab-contents #first").removeClass('hidden');
  $("#tab-contents #make-offer-chat").removeClass('hidden');
  $("#tab-contents #first").addClass('hidden');
  $("#make-offer-chat ul li").removeClass('price-active');
  $('#make-offer-chat ul li').each(function () {
    var check_price_val = $(this).attr('data-defmsg');
    if (check_price_val === offered_price) {
      $(this).addClass('price-active');
      $(".make-offer-design p.offer-price").text(get_curency + offered_price);
      $(".make-offer-design input.offer-price").val(offered_price);
      $(".offer_input").val(offered_price);
    }
  });
});

let tabsContainer = document.querySelector("#tabs");
if ((tabsContainer != null) && (tabsContainer.querySelectorAll("a") != null)) {
  let tabTogglers = tabsContainer.querySelectorAll("a");
  tabTogglers.forEach(function (toggler) {
    toggler.addEventListener("click", function (e) {
      e.preventDefault();
      let tabName = this.getAttribute("href");
      let tabContents = document.querySelector("#tab-contents");
      for (let i = 0; i < tabContents.children.length; i++) {
        tabTogglers[i].parentElement.classList.remove("tab-active");
        tabContents.children[i].classList.remove("hidden");
        if ("#" + tabContents.children[i].id === tabName) {
          continue;
        }
        tabContents.children[i].classList.add("hidden");
		$("#tab-contents").show();
      }
      e.target.parentElement.classList.add("tab-active");
    });
  });
  document.getElementById("default-tab").click();
}

// make offer end


// block user start
$(".block").click(function () {
  $(".block_div").toggle();
});
$(document).ready(function () {
  $(".block_user").click(function (event) {
    var ids = $(this).attr('data-id');
    var msg = ($(this).attr('data-block-status') == 0) ? " to Block the user?" : " to Unblock the user?";
    if (confirm('Are you sure you want' + msg)) {
      delete_chat(ids, 'block');
    } else {
      event.preventDefault();
    }
  });
});
// block user end

// delete chat start
$(document).ready(function () {
  $(".delete_all").click(function (event) {
    var ids = $(this).attr('data-id');
    if (confirm('Are you sure to delete?')) {
      delete_chat(ids, 'delete');
    } else {
      event.preventDefault();
    }
  });
});
// delete chat end