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
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  sliderSearch()
  playerSearch()
  // Бургер
  $('.burgerButton').click(function (e) {
    e.preventDefault();
    $(this).toggleClass('_active');
    $('.sidebar').toggleClass('_active');
  });
  $(document).mouseup(function (e) {
    let select = $(".sidebar, .header");
    if (!select.is(e.target) &&
      select.has(e.target).length === 0) {
      $('.burgerButton').removeClass('_active');
      $('.sidebar').removeClass('_active');
    }
  });
  $(document).mouseup(function (e) {
    let select = $(".userHeader");
    if (!select.is(e.target) &&
      select.has(e.target).length === 0) {
      $('.userMenu').removeClass('_active');
    }
  });
  // Табы блоки
  $('.titleIcon').click(function (e) {
    e.preventDefault();
    if ($(this).parent().parent().hasClass('_tabOpen')) {
      localStorage.setItem('tab', '1');
    } else {
      localStorage.removeItem('tab');
    }
    $(this).parent().parent().toggleClass('_tabOpen');
    $(this).parent().parent().children('.tabPageItem').slideToggle();
  });
  // Меню юзера в шапке
  $(".userName, .userHeaderIcon").click(function (e) {
    e.preventDefault();
    $(".userMenu").toggleClass("_active");
  });
  // Открытие сабменю сайдбара
  $(".sidebarOpener").click(function (e) {
    e.preventDefault();
    $(this).parent().toggleClass("_active");
    $(this).next(".sidebarSubWrapper").slideToggle();
  });
  // Кабинет табы
  $(document).on('click', '.pageTab', function (e) {
    e.preventDefault();
    let tab = $(this).attr("data-tab");
    const accountId = ($(this).attr("data-accountId")) ? $(this).attr("data-accountId") : null
    const groupId = ($(this).attr("data-groupId")) ? $(this).attr("data-groupId") : null
    if (accountId || groupId) {
      const action = $(this).attr('data-action');
      const type = $(this).attr('data-type');
      let formData = new FormData();
      formData.append('type', type);
      const itemId = (accountId) ? accountId : groupId;
      if (accountId) {
        formData.append('account_id', accountId);
      } else if (groupId) {
        formData.append('group_id', groupId);
      }
      $.ajax({
        type: "POST",
        url: action,
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        dataType: "json",
        complete: function (response) {
          if (window.location.pathname == '/cabinet/vk/stories' && $(`.pageTab[data-accountId=${itemId}]`)) {
            $(`.tabItem[data-tab=${itemId}]`).children('.rowFlex').html(response.responseText);
          }
          if (window.location.pathname == '/cabinet/vk/posts' && $(`.pageTab[data-accountId=${itemId}]`)) {
            $(`.tabItem[data-tab=${itemId}]`).children('.rowFlex').html(response.responseText);
          }
          sliderSearch()
          playerSearch()
        }
      });
    }
    $(".pageTab").removeClass("_active");
    $(this).addClass("_active");
    $(".tabItem").removeClass("_active");
    $(`.tabItem[data-tab=${tab}]`).addClass("_active");
  });
  // Попап инструкция
  $('.instructorLink').click(function (e) {
    e.preventDefault();
    let name = $(this).attr("data-name");
    let popup = $(this).attr("data-popup");
    $(".popupWrapper").fadeIn();
    $(`.popupItem[data-popup=${popup}]`).children(".popupTitle").html(name);
    $(`.popupItem[data-popup=${popup}]`).fadeIn();
    playerSearch()
  });
  // Закрытие попапов
  $(".popupBg, .popupClose").click(function (e) {
    e.preventDefault();
    playerPause()
    $(".popupWrapper").fadeOut();
    $(".popupItem").fadeOut();
  });
  // Открытие гайдов в попап
  $(".guidLink").click(function (e) {
    e.preventDefault();
    let name = $(this).attr("data-name");
    let popup = $(this).attr("data-popup");
    $(".popupWrapper").fadeIn();
    $(`.popupItem[data-popup=${popup}]`).children(".popupTitle").html(name);
    $(`.popupItem[data-popup=${popup}]`).fadeIn();
  });
  // Копирование ссылки
  $("a.partnerItemBtn").click(function (e) {
    e.preventDefault();
    let btn = $(this);
    btn.addClass("_copied");
    let text = $(this).prev().text();
    let input = $("<input>");
    $("body").append(input);
    input.val(text).select();
    document.execCommand("copy");
    input.remove();
    let message;
    if ($(this).parent().hasClass('_promocode')) {
      createMessage("<p class='fixedMessage'>Промокод скопирован!</p>")
    } else {
      createMessage("<p class='fixedMessage'>Ссылка успешно скопирована!</p>")
    }
  });
  // Кастомный селект
  $(document).on('click', '.selectHeader', function (e) {
    e.preventDefault();
    $(this).children('.selectArrow').toggleClass('_active');
    $(this).next().slideToggle();
  });
  $(document).on('click', '.selectItem', function (e) {
    e.preventDefault();
    $('.selectItem').removeClass('_active');
    $(this).addClass('_active');
    let name = $(this).children('._name').text();
    let id = $(this).attr('data-id');
    $(this).parent().prev().children('p').html(name);
    $(this).parent().prev().prev().val(id);
    $(this).parent().prev().children('.selectArrow').removeClass('_active');
    if ($(this).attr('data-type') !== 'undefined') {
      if ($(this).attr('data-type') == 'group') {
        if ($('.tabPageGroup').length > 0) {
          $('.tabPageGroup').css({ "display": "flex" });
        }
      } else if ($(this).attr('data-type') == 'addVkGroup') {
        const accountId = $('.addGroupSelect').children('input[name="accountId"]').val()
        const ajaxUrl = $('.addGroupSelect').attr('data-action')
        let formData = {};
        formData.accountId = accountId;
        $.ajax({
          url: ajaxUrl,
          method: 'POST',
          data: formData,
          success: function (response) {
            let groupsItems = ``;
            if (response.status == "success") {
              if (Object.keys(response.message).length > 0) {
                for (const item of Object.values(response.message)) {
                  const name = item.name
                  groupsItems += `<div class="groupSelect">
                  <input type="radio" name="groupId" value=${item.id} class="groupSelectRadio">
                  <div class="groupSelectInfo">
                      <div class="accountAvatar" style="background: url(${item.avatar}), #FFFFFF;"></div>
                      <p class="accountName">${(name.length > 12) ? name.substring(0, 9) + '...' : name}</p>
                  </div>
              </div>`
                }
                $('.popupItem[data-popup="addGroup"]').children('.formBtn').css({ "display": "flex" });
              } else {
                groupsItems = `<p class="groupEmpty">Для данного аккаунта не найдены группы!</p>`
                $('.popupItem[data-popup="addGroup"]').children('.formBtn').css({ "display": "none" });
              }
              $('.groupSelectItems').html(groupsItems)
            } else {
              errorHandler(response.title, response.message)
            }
          },
          error: function (response) {
            errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
          }
        });
      } else {
        if ($('.tabPageGroup').length > 0) {
          $('.tabPageGroup').css({ "display": "none" });
        }
      }
      $('input[name="accType"]').val($(this).attr('data-type'));
    }
    $(this).parent().slideUp();
  });
  $(document).on('click', '.cardButton', function (e) {
    if(!$(this).hasClass('_stats')){
      e.preventDefault();
      const ajaxUrl = $(this).attr('data-action');
      let formData = new FormData();
      if ($(this).hasClass('_remove')) {
        formData.append('removeId', $(this).attr('data-removeId'));
      }else if($(this).hasClass('_autostart')){
        formData.append('autoStart', $(this).attr('data-autoStart'));
      }
      $.ajax({
        type: "POST",
        url: ajaxUrl,
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        dataType: "json",
        success: function (response) {
          $('.popupWrapper').fadeIn();
          $('.popupItem').fadeOut();
          if (response.status == "success") {
            successHandler(response.title, response.message)
          } else {
            errorHandler(response.title, response.message)
          }
        },
        error: function (response) {
          console.log(response)
          errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
        }
      });
    }
  });
  $(document).mouseup(function (e) {
    let select = $(".formSelect");
    if (!select.is(e.target) &&
      select.has(e.target).length === 0) {
      $('.selectFlow').slideUp();
      $('.selectArrow').removeClass('_active');
    }
  });
});
