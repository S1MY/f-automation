$(document).ready(function () {
  // Добавление аккаунта
  $('.addAccount').click(function (e) {
    $('.popupItem').fadeOut();
    $(".popupWrapper").addClass('_notClose');
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addAccount"]').fadeIn();
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
      },
      error: function (response) {
        $('.popupItem').fadeOut();
        const error = $('.popupItem[data-popup="error"]');
        error.show()
        error.children('.popupTitle').html(response.title);
        error.children('.popupText').html(response.message);
        error.fadeIn();
      }
    });
  });
  $('.removeAccount').click(function (e) {
    e.preventDefault();
    const accountId = $(this).attr('data-accountId');
    const ajaxUrl = $(this).attr('data-action');
    let formData = {};
    formData.accountId = accountId;
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $(".popupWrapper").fadeIn();
    $.ajax({
      url: ajaxUrl,
      method: 'POST',
      data: formData,
      success: function (response) {
        const success = $('.popupItem[data-popup="success"]');
        success.show()
        success.children('.popupTitle').html(response.title);
        success.children('.popupText').html(response.message);
        success.fadeIn();
        setTimeout(() => {
          location.reload();
        }, 5000);
      },
      error: function (response) {
        const error = $('.popupItem[data-popup="error"]');
        error.show()
        error.children('.popupTitle').html(response.title);
        error.children('.popupText').html(response.message);
        error.fadeIn();
      }
    });
  });
  $('.removeGroup').click(function (e) {
    e.preventDefault();
    const groupId = $(this).attr('data-groupId');
    const ajaxUrl = $(this).attr('data-action');
    let formData = {};
    formData.groupId = groupId;
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $(".popupWrapper").fadeIn();
    $.ajax({
      url: ajaxUrl,
      method: 'POST',
      data: formData,
      success: function (response) {
        const success = $('.popupItem[data-popup="success"]');
        success.show()
        success.children('.popupTitle').html(response.title);
        success.children('.popupText').html(response.message);
        success.fadeIn();
        setTimeout(() => {
          location.reload();
        }, 5000);
      },
      error: function (response) {
        const error = $('.popupItem[data-popup="error"]');
        error.show()
        error.children('.popupTitle').html(response.title);
        error.children('.popupText').html(response.message);
        error.fadeIn();
      }
    });
  });
  $('.buyPlace').click(function (e) {
    e.preventDefault();
    // Нужно обязательно отправить type
    // vk - акк вк
    // vkg - группа
    // tg
    $('.popupItem').fadeOut();
    let type = $(this).parent().parent().parent().attr('data-tab');
    $(".popupWrapper").fadeIn();
    // Успешный
    let title = "Вай вай вай";
    let message = "Дополнительное место успешно куплено<br> Страница автоматически перегрузится, чтобы изменения применились.";
    $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
    $('.popupItem[data-popup="success"]')
      .children(".popupText")
      .html(message);
    $('.popupItem[data-popup="success"]').fadeIn();
    setTimeout(() => {
      location.reload();
    }, 5000);
    // Хуёвый
    // let title = "ОЙ ОЙ ОЙ";
    // let message = "Не удалось купить место.<br> Скорее всего вы ебаный бомж и у вас нет средств.";
    // $('.popupItem[data-popup="error"]').children(".popupTitle").html(title);
    // $('.popupItem[data-popup="error"]').children(".popupText").html(message);
    // $('.popupItem[data-popup="error"]').fadeIn();
  });
  $('.popupItem').submit(function (e) {
    e.preventDefault();
    const ajaxUrl = $(this).attr('action');
    let formData = $(this).serialize();
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
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
          const success = $('.popupItem[data-popup="success"]');
          success.show()
          success.children('.popupTitle').html(response.title);
          success.children('.popupText').html(response.message);
          success.fadeIn();
          setTimeout(() => {
            location.reload();
          }, 5000);
        },
        error: function (response) {
          const error = $('.popupItem[data-popup="error"]');
          error.show()
          error.children('.popupTitle').html(response.title);
          error.children('.popupText').html(response.message);
          error.fadeIn();
        }
      });
    } else if (popupName == 'addGroup') {
      if (formData.indexOf('groupId') >= 0) {
        $.ajax({
          url: ajaxUrl,
          method: 'POST',
          data: formData,
          success: function (response) {
            const success = $('.popupItem[data-popup="success"]');
            success.show()
            success.children('.popupTitle').html(response.title);
            success.children('.popupText').html(response.message);
            success.fadeIn();
            setTimeout(() => {
              location.reload();
            }, 5000);
          },
          error: function (response) {
            const error = $('.popupItem[data-popup="error"]');
            error.show()
            error.children('.popupTitle').html(response.title);
            error.children('.popupText').html(response.message);
            error.fadeIn();
          }
        });
      } else {
        const message = "<p class='fixedMessage _error'>Выберите группу!</p>";
        $("body").append(message);
        $(".fixedMessage").fadeIn();
        setTimeout(function () {
          $(".fixedMessage").remove();
        }, 3000);
      }
    }
  });
});