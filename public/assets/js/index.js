console.warn = () => { };
$(window).on("load", function () {
  $(".preloader").fadeOut();
  if (localStorage.getItem('action') == null) {
    localStorage.setItem('action', 'noname');
    $(".popupWrapper").fadeIn();
    $(`.popupItem[data-class="action"]`).fadeIn();
  }
  if ($('.pageItem[data-storage="tab"]').length > 0) {
    if (localStorage.getItem('tab') !== null) {
      $('.pageItem[data-storage="tab"]').removeClass('_tabOpen');
      $('.pageItem[data-storage="tab"]').children('.tabPageItem').addClass('_hidden');
    }
  }
});
$(document).ready(function () {
  $(document).mouseup(function (e) {
    let select = $(".userHeader");
    if (!select.is(e.target) &&
      select.has(e.target).length === 0) {
      $('.userMenu').removeClass('_active');
    }
  });
  // Меню юзера в шапке
  $(".userName, .userHeaderIcon").click(function (e) {
    e.preventDefault();
    $(".userMenu").toggleClass("_active");
  });
  $('input[name="privacy"]').change(function (e) {
    e.preventDefault();
    if ($(this).prop('checked')) {
      $(this).parent().parent().next().removeClass('_disabled');
    } else {
      $(this).parent().parent().next().addClass('_disabled');
    }
  });
  // факи
  $('.faqNav').click(function (e) {
    e.preventDefault();
    $('.faqNav').removeClass('_active');
    $(this).addClass('_active');
    let id = $(this).attr('data-id');
    $('.faqItem').slideUp();
    $(`.faqItem[data-id=${id}]`).slideDown();
  });
  let link = document.location.href;
  link = link.split('/');
  // Рега или вход
  $(".formWrapper").submit(function (e) {
    e.preventDefault();
    let action = $(this).attr('data-action');
    let formData = new FormData();
    let serialize = $(this).serializeArray();
    $.each(serialize, function (i, val) {
      formData.append(val.name, val.value);
    });
    if (action == 'register') {
      $.ajax({
        type: "POST",
        url: action,
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        dataType: "json",
        success: function (response) {
          if (response.result == "success") {
            // При успехе переадресация
            link[link.length - 1] = '/confirm';
            link = link.join('/');
            window.location.replace(link);
          } else if (response.result == "inputs") {
            // Клиентские ошибки
            $.each(response.errors, function (key, value) {
              $(`.tabPageInput[data-name=${key}]`).addClass('_error');
              $(`.tabPageInput[data-name=${key}]`).append('<span class="errorInput">Название ошибки</span>');
            });
            $('.errorInput').fadeOut(3000);
            setTimeout(() => {
              $('.errorInput').remove();
            }, 3500);
          } else {
            // Серверные ошибки
            let title = "ОЙ ОЙ ОЙ";
            let message = "Андрюха по коням. Походу криминал.";
            $('.popupItem[data-popup="error"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="error"]').children(".popupText").html(message);
            $('.popupItem[data-popup="error"]').fadeIn();
          }
        }
      });
    }
  });
  // Закрытие попапов
  $(".popupBg, .popupClose").click(function (e) {
    e.preventDefault();
    $(".popupWrapper").fadeOut();
    $(".popupItem").fadeOut();
  });
  // код

  $('.confirmInput').on('input', function(e) {
    e.preventDefault();
    let confirmCount = 0;
    let code = '';
    const currLength = $(this).val().length;
    const currIndex = parseInt($(this).attr('tabindex'));
    const nextIndex = currIndex + 1;
    const prevIndex = currIndex - 1;
    if (currIndex === 1 && currLength === 0 && e.keyCode === 8) {
      $(`.confirmInput[tabindex=${prevIndex}]`).focus();
      return false;
    }
    if (currLength === 1) {
      $(`.confirmInput[tabindex=${nextIndex}]`).focus();
    } else if (currLength === 0 && currIndex > 1) {
      $(`.confirmInput[tabindex=${prevIndex}]`).focus();
    } else if (currLength > 1) {
      let tempValue = $(this).val();
      for (let i = currIndex; i <= currIndex + currLength - 1; i++) {
        const symbol = tempValue.substr(0, 1);
        tempValue = tempValue.slice(1);
        const next = i + 1;
        $(`.confirmInput[tabindex=${i}]`).val(symbol);
        $(`.confirmInput[tabindex=${next}]`).focus();
      }
    }
    
    $('.confirmInput').each(function() {
      const value = $(this).val();
      if (value !== '') {
        confirmCount++;
        code += $(this).val();
      }
    });
  
    if (code.length > $('.confirmInput').length) {
      $('.confirmInput').val('');
      $(`.confirmInput[tabindex=1]`).focus();
      return false;
    }
  
    if (confirmCount === $('.confirmInput').length) {
      // Ваш код для отправки AJAX-запроса
    }
  });
  // Отправка кода ещё раз
  $('.codeAgain').click(function (e) {
    e.preventDefault();
  });
});
