$(document).ready(function () {

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
    let fullPrice = 0;
    let subNames = [];
    let promoValue = 0;
    construct();
  // Сабскрайб
  $(".subItemCheck").click(function (e) {
    e.preventDefault();
    $(this).toggleClass("_active");
    let eachElements = $(this).parent().next().children().children(".subCheckBox");
    if ($(this).hasClass('_active')) {
        $.each(eachElements, function (indexInArray, valueOfElement) {
            if (
              $(valueOfElement).children(".subCheckBtn").hasClass("_active")
            ) {
        }else{
            $(valueOfElement).children(".subCheckBtn").addClass("_active");
            $(valueOfElement)
              .children(".subCheckBtn")
              .next()
              .prop("checked", true);
            $(valueOfElement).children(".subCheckBtn").html("Убрать");
            let childPrice = $(valueOfElement).children(".subCheckBtn").attr("data-price");
          fullPrice = parseInt(fullPrice) + parseInt(childPrice);
        }
        });
    }else{
        $.each(eachElements, function (indexInArray, valueOfElement) {
            $(valueOfElement).children(".subCheckBtn").removeClass("_active");
            $(valueOfElement)
              .children(".subCheckBtn")
              .next()
              .prop("checked", false);
            $(valueOfElement).children(".subCheckBtn").html("Выбрать");
            let childPrice = $(valueOfElement).children(".subCheckBtn").attr("data-price");
          fullPrice = parseInt(fullPrice) - parseInt(childPrice);
        });
    }
    construct();
  });
  $(".subCheckBtn").click(function (e) {
    e.preventDefault();
    // Обозначаем элемент
    const element = $(this);
    // Получаем стоимость функции
    const price = parseInt($(this).attr("data-price"));
    // Получаем список требуемых функций
    const parent = $(this).attr("data-parent");
    const children = $(this).attr("data-children");
    // Создаём массив требуемых функций
    const parents = parent.split(" или ");
    const childrens = children.split(" и ");
    // Детектор активации
    let detector = 0;
    if (parent != 0) {
      const eachElements = element.parent().parent().children(".subCheckBox");
      $.each(eachElements, function (indexInArray, valueOfElement) {
        for (let i = 0; i < parents.length; i++) {
          const funcId = $(valueOfElement).children(".subCheckNumber").text();
          if (funcId == parents[i]) {
            if ($(valueOfElement).children(".subCheckBtn").hasClass("_active")) {
              detector = 1;
            } else {
              element.html(`Требуется функция с ID ${parent}`);
            }
          }
        }
      });
    } else {
      detector = 1;
    }
  
    if (detector == 1) {
      if ($(this).hasClass("_active")) {
        // Получаем id зависимой функции
        const eachElements = element.parent().parent().children(".subCheckBox");
        $.each(eachElements, function (indexInArray, valueOfElement) {
          const funcId = $(valueOfElement).children(".subCheckNumber").text();
          childrens.forEach(children => {
            if (funcId == children) {
              if ($(valueOfElement).children(".subCheckBtn").hasClass("_active")) {
                $(valueOfElement).children(".subCheckBtn").removeClass("_active");
                $(valueOfElement).children(".subCheckBtn").next().prop("checked", false);
                $(valueOfElement).children(".subCheckBtn").html("Выбрать");
  
                const childPrice = parseInt($(valueOfElement).children(".subCheckBtn").attr("data-price"));
                fullPrice -= childPrice;
              }
            }
          });
        });
  
        $(this).removeClass("_active");
        $(this).next().prop("checked", false);
        fullPrice -= price;
        $(this).html("Выбрать");
      } else {
        fullPrice += price;
        $(this).addClass("_active");
        $(this).next().prop("checked", true);
        $(this).html("Убрать");
      }
  
      construct();
    }
  });
  
  $(".subItemArrow").click(function (e) {
    e.preventDefault();
    if ($(this).parent().hasClass('_active')) {
        $(this).parent().removeClass('_active');
        $(this).parent().next().slideUp();
    }else{
        $(this).parent().addClass('_active');
        $(this).parent().next().slideDown();
    }
  });
  $(".freekassa").click(function (e) {
    e.preventDefault();
    let data = $('.pageItem[data-popup="deposit"]').serialize();
    // То как должен выглядеть ответ после ответ с сервера
    let title = "Что-то пошло не так...";
    let message =
      "При попытке приобретения подписку возникла непредвиденная ошибка.<br> Можете попробовать снова или написать нам в группу";
    $('.popupItem[data-popup="error"]').children(".popupTitle").html(title);
    $('.popupItem[data-popup="error"]').children(".popupText").html(message);
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="error"]').fadeIn();
  });
  $(".pageTarget").submit(function (e) {
    e.preventDefault();
    const popupName = $(this).attr("data-form");
    const ajaxUrl = $(this).attr("action");
    if(popupName === 'subscribe'){
      if (!$('input[name="promocode"]').val()) {
        $('input[name="promocode"]').val('empty');
      }
    }
    const formData = $(this).serialize();
    if(popupName === 'subscribe'){
      $('input[name="promocode"]').val();
    }
    $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: formData,
        success: function(response){
          $(".popupWrapper").fadeIn();
          $('.popupItem').fadeOut();
          if (response.status == "success") {
            if (popupName === 'deposit') {
              
            }else{
              successHandler(response.title, response.message)
            }
          } else {
            errorHandler(response.title, response.message)
          }
        },
        error: function (response) {
          console.log(response)
          $(".popupWrapper").fadeIn();
          $('.popupItem').fadeOut();
          errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
        }
    });
  });
  $(".promoBtn").click(function (e) {
    e.preventDefault();
    let promocode = $(this).prev().val();
    // То как должен выглядеть ответ после ответ с сервера
    if (promocode == "FIRST100") {
      $(".promoResponse").removeClass("_error");
      $(".promoResponse span").html("Промокод применён");
      $(".promoResponse").fadeIn();
      promoValue = 100;
      construct();
    } else {
      $(".promoResponse span").html("Промокод не существует");
      // $('.promoResponse span').html('Промокод недействителен');
      $(".promoResponse").addClass("_error");
      $(".promoResponse").fadeIn();
      setTimeout(() => {
        $(".promoResponse").fadeOut();
      }, 3000);
    }
  });
  function construct(){
    subNames = [];
    fullPrice = 0;
    $('input[name="promoChecker"]').val(0)
    $.each($('.subCheckBtn'), function (indexInArray, valueOfElement) {
        if ($(valueOfElement).hasClass('_active')) {
          subNames.push($(valueOfElement).attr('data-name'));
          fullPrice = parseInt(fullPrice) + parseInt($(valueOfElement).attr('data-price'))
        }
    });
    if (subNames.length < 1) {
        subNames = ['Никакие'];
        fullPrice = 0;
    }
    if (promoValue != 0) {
      $('input[name="promoChecker"]').val(1)
      fullPrice = parseInt(fullPrice) - parseInt(promoValue);
    }
    $('.subNames').html(subNames.join(', '));
    $('.fullPrice').html(fullPrice);
    $('#priceInput').val(fullPrice);
  }
});
