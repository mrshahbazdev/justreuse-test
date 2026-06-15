jQuery(document).ready(function () {
  jQuery('.cate_slider').slick({
    dots: false,
    infinite: true,
    speed: 500,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 2000,

    centerMode: false,
    variableWidth: false,
    slidesToShow: 6,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 4,
          infinite: true,
          dots: false
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 3,
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


  jQuery('.Top_ads').slick({
    dots: false,
    infinite: true,
    speed: 1000,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 500,
    centerMode: false,
    variableWidth: false,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 4,
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
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 310,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      }

    ]

  });
  
  jQuery('.recent_ads').slick({
    dots: false,
    infinite: true,
    speed: 300,
    arrows: true,
    prevArrow: $('.left_arrow'),
    nextArrow: $('.right_arrow'),
    autoplay: true,
    autoplaySpeed: 500,
    centerMode: false,
    variableWidth: false,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 4,
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
          slidesToScroll: 2
        }
      }

    ]

  });

  jQuery('.banner_image').slick({
    dots: false,
    infinite: true,
    speed: 1000,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 800,
    centerMode: false,
    variableWidth: false,
    slidesToShow: 1,
    slidesToScroll: 1,

  });

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
});
