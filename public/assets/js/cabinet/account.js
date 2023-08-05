$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  // Добавление аккаунта
  $('.addAccount, .updateAccount').click(function (e) {
    const type = ($(this).hasClass('updateAccount')) ? 'update' : 'add';
    $('.popupItem[data-popup="addAccount"]').children('input[name="type"]').val(type);
    $('.popupItem').fadeOut();
    $(".popupWrapper").addClass('_notClose');
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addAccount"]').fadeIn();
  });
  $('.addTelegram').click(function (e) {
    e.preventDefault();
    $('.popupItem').fadeOut();
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addTelegram"]').fadeIn();
  });
  $('.addGroup').click(function (e) {
    e.preventDefault();
    $('.popupItem').fadeOut();
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addGroup"]').fadeIn();
    const accountId = $('.addGroupSelect').children('input[name="accountId"]').val()
    const ajaxUrl = $('.addGroupSelect').attr('data-action')
    let formData = {};
    formData.accountId = accountId;
    $.ajax({
      url: ajaxUrl,
      method: 'POST',
      data: formData,
      success: function (response) {
        if (response.status === 'success') {
          let groupsItems = ``;
          if (Object.keys(response.message).length > 0) {
            for (const item of Object.values(response.message)) {
              const name = item.name
              groupsItems += `<div class="groupSelect">
              <input type="radio" name="groupId" value=${item.id} class="groupSelectRadio">
              <div class="groupSelectInfo">
                  <div class="accountAvatar" style="background: url(${item.avatar}), #FFFFFF;"></div>
                  <p class="accountName">${(name.length > 14) ? name.substring(0, 11) + '...' : name}</p>
              </div>
          </div>`
            }
            $('.popupItem[data-popup="addGroup"]').children('.formBtn').css({ "display": "flex" });
          } else {
            groupsItems = `<p class="groupEmpty">Для данного аккаунта не найдены группы!</p>`
            $('.popupItem[data-popup="addGroup"]').children('.formBtn').css({ "display": "none" });
          }
          $('.groupSelectItems').html(groupsItems)
        }else{
          errorHandler(response.title, response.message)
        }
      },
      error: function (response) {
        errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
      }
    });
  });
  $('.addChannel').click(function (e) {
    e.preventDefault();
    $('.popupItem').fadeOut();
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addChannel"]').fadeIn();
    const accountId = $('.addChannelSelect').children('input[name="accountId"]').val()
    const ajaxUrl = $('.addChannelSelect').attr('data-action')
    let formData = {};
    formData.accountId = accountId;
    $.ajax({
      url: ajaxUrl,
      method: 'POST',
      data: formData,
      success: function (response) {
        if (response.status === 'success') {
          let groupsItems = ``;
          if (Object.keys(response.message).length > 0) {
            for (const item of Object.values(response.message)) {
              const title = item.title;
              const username = item.username;
              const avatar = '/files/avatar.jpg';
              groupsItems += `<div class="groupSelect">
              <input type="radio" name="username" value=${username} class="groupSelectRadio">
              <div class="groupSelectInfo">
                  <div class="accountAvatar" style="background: url(${avatar}), #FFFFFF;"></div>
                  <p class="accountName">${(title.length > 14) ? title.substring(0, 11) + '...' : title}</p>
              </div>
          </div>`
            }
            $('.popupItem[data-popup="addChannel"]').children('.formBtn').css({ "display": "flex" });
          } else {
            groupsItems = `<p class="groupEmpty">Для данного аккаунта не найдены каналы!</p>`
            $('.popupItem[data-popup="addChannel"]').children('.formBtn').css({ "display": "none" });
          }
          $('.channelSelectItems').html(groupsItems)
        }else{
          errorHandler(response.title, response.message)
        }
      },
      error: function (response) {
        errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
      }
    });
  });
  $('.removeAccount').click(function (e) {
    e.preventDefault();
    const accountId = $(this).attr('data-accountId');
    const ajaxUrl = $(this).attr('data-action');
    let formData = {};
    formData.accountId = accountId;
    ajaxEasyHandler(ajaxUrl, formData);
  });
  $('.removeGroup').click(function (e) {
    e.preventDefault();
    const groupId = $(this).attr('data-groupId');
    const ajaxUrl = $(this).attr('data-action');
    let formData = {};
    formData.groupId = groupId;
    ajaxEasyHandler(ajaxUrl, formData);
  });
  $('.removeChannel').click(function (e) {
    e.preventDefault();
    const channelId = $(this).attr('data-channelId');
    const ajaxUrl = $(this).attr('data-action');
    let formData = {};
    formData.channelId = channelId;
    ajaxEasyHandler(ajaxUrl, formData);
  });
  $('.phoneButton').click(function (e) { 
    e.preventDefault();
    $(this).parent().toggleClass('_viewed');
  });
  $('.buyPlace').click(function (e) {
    e.preventDefault();
    const ajaxUrl = $(this).attr('data-action');
    let formData = {};
    formData.id = 0;
    $(".popupWrapper").fadeIn();
    $.ajax({
      url: ajaxUrl,
      method: 'POST',
      data: formData,
      success: function (response) {
        if (response.status === 'success') {
          successHandler(response.title, response.message)
        }else if(response.status === 'places'){
          const places = $('.popupItem[data-popup="maximumPlaces"]');
          places.children('.popupTitle').html(response.title);
          places.children('.popupText').html(response.message);
          places.fadeIn();
        }else{
          errorHandler(response.title, response.message)
        }
      },
      error: function (response) {
        errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
      }
    });
  });
  $('.popupItem').submit(function (e) {
    e.preventDefault();
    const ajaxUrl = $(this).attr('action');
    let formData = $(this).serialize();
    const popupName = $(this).attr("data-popup");
    $('.popupCenter').show();
    $('.popupWrapper').removeClass('_notClose');
    if (popupName == 'addAccount') {
      $('.popupItem').fadeOut();
      $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: formData,
        success: function (response) {
          if (response.status === "success") {
            successHandler(response.title, response.message)
          } else {
            errorHandler(response.title, response.message)
          }
        },
        error: function (response) {
          errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
        }
      });
    } else if (popupName == 'addGroup') {
      if (formData.indexOf('groupId') >= 0) {
        $.ajax({
          url: ajaxUrl,
          method: 'POST',
          data: formData,
          success: function (response) {
            if (response.status === "success") {
              successHandler(response.title, response.message)
            } else {
              errorHandler(response.title, response.message)
            }
          },
          error: function (response) {
            errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
          }
        });
      } else {
        createMessage("<p class='fixedMessage _error'>Выберите группу!</p>")
      }
    } else if (popupName == 'addChannel') {
      if (formData.indexOf('username') >= 0) {
        $.ajax({
          url: ajaxUrl,
          method: 'POST',
          data: formData,
          success: function (response) {
            console.log(response)
            if (response.status === "success") {
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
      } else {
        createMessage("<p class='fixedMessage _error'>Выберите группу!</p>")
      }
    }else if (popupName == 'addTelegram') {
      let formData = new FormData();
      let serialize = $(this).serializeArray();
      $.each(serialize, function (i, val) {
        formData.append(val.name, val.value);
      });
      if ($('input[name="tg_phone"]').val() === '') {
        formData.append('tg_phone', 'empty');
      }
      if ($('input[name="tg_code"]').val() === '') {
        formData.append('tg_code', 'empty');
      }
      if ($('input[name="tg_first_name"]').val() === '') {
        formData.append('tg_first_name', 'empty');
      }
      if ($('input[name="tg_password"]').val() === '') {
        formData.append('tg_password', 'empty');
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
          console.log(response)
          if (response.status === "success") {
            successHandler(response.title, response.message)
          } else if(response.status === "next") {
            $('.popupComment').html(response.message)
            $('.telegramAuth').removeClass('_active')
            $(`.telegramAuth[data-name='${response.title}']`).addClass('_active');
            $('input[name="tg_phone"]').val('');
            $('input[name="tg_code"]').val('');
            $('input[name="tg_first_name"]').val('');
            $('input[name="tg_password"]').val('');
          }else if (response.status === "input") {
            $('.popupWrapper').fadeOut();
            $(`.tabPageInput[data-name='${response.title}']`).addClass('_error');
            $(`.tabPageInput[data-name='${response.title}']`).append(`<span class="errorInput">${response.message}</span>`);
            $('.errorInput').fadeOut(3000);
            setTimeout(() => {
              $('.errorInput').remove();
            }, 3500);
          }else {
            console.log(response)
            errorHandler(response.title, response.message)
          }
        },
        error: function (response) {
          errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
        }
      });
    }
  });
});