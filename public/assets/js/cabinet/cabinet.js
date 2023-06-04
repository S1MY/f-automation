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
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let action, type, formData;
    if(accountId || groupId){
      action = $(this).attr('data-action');
      type = $(this).attr('data-type');
      formData = new FormData();
      formData.append('type', type);
    }
    if(accountId){
      formData.append('account_id', accountId);
      $.ajax({
          type: "POST",
          url: action,
          contentType: false,
          processData: false,
          cache: false,
          data: formData,
          dataType: "json",
          complete: function (response) {
              if (window.location.pathname == '/cabinet/vk/stories' && $(`.pageTab[data-accountId=${accountId}]`)) {
                $(`.tabItem[data-tab=${accountId}]`).children('.rowFlex').html(response.responseText);
              }
          }
      });
    }else if(groupId){
      formData.append('group_id', groupId);
      $.ajax({
          type: "POST",
          url: action,
          contentType: false,
          processData: false,
          cache: false,
          data: formData,
          dataType: "json",
          complete: function (response) {
              if (window.location.pathname == '/cabinet/vk/stories' && $(`.pageTab[data-accountId=${groupId}]`)) {
                $(`.tabItem[data-tab=${groupId}]`).children('.rowFlex').html(response.responseText);
              }
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
  });
  // Закрытие попапов
  $(".popupBg, .popupClose").click(function (e) {
    e.preventDefault();
    $(".popupWrapper").fadeOut();
    $(".popupItem").fadeOut();
    if ($('.popupItem[data-class="guid"]').length > 0) {
      $.each($(".videoGuid"), function (indexInArray, valueOfElement) {
        valueOfElement.pause();
        let play = $(valueOfElement)
          .next()
          .children(".togglePlay")
          .children(".play");
        let stop = $(valueOfElement)
          .next()
          .children(".togglePlay")
          .children(".stop");
        stop.hide();
        play.show();
      });
    }
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
      message = "<p class='fixedMessage'>Промокод скопирован!</p>";
    } else {
      message = "<p class='fixedMessage'>Ссылка успешно скопирована!</p>";
    }
    $("body").append(message);
    $(".fixedMessage").fadeIn();
    setTimeout(function () {
      $(".fixedMessage").fadeOut();
      btn.removeClass("_copied");
    }, 3000);
    setTimeout(function () {
      $(".fixedMessage").remove();
    }, 4000);
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
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: ajaxUrl,
          method: 'POST',
          data: formData,
          success: function (response) {
            let groupsItems = ``;
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
          },
          error: function (response) {
            console.log(response);
            $('.popupItem').fadeOut();
            const error = $('.popupItem[data-popup="error"]');
            error.show()
            error.children('.popupTitle').html(response.title);
            error.children('.popupText').html(response.message);
            error.fadeIn();
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
  $(document).mouseup(function (e) {
    let select = $(".formSelect");
    if (!select.is(e.target) &&
      select.has(e.target).length === 0) {
      $('.selectFlow').slideUp();
      $('.selectArrow').removeClass('_active');
    }
  });
});
