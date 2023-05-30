$(document).ready(function () {
  // Добавление аккаунта
  $('.addAccount').click(function (e) {
    $('.popupItem').fadeOut();
    $(".popupWrapper").addClass('_notClose');
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addAccount"]').fadeIn();
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
        const success = $('.popupWrapper .popupItem[data-popup="success"]');
        success.show()
        success.children('.popupTitle').html(response.title);
        success.children('.popupText').html(response.message);
        success.fadeIn();
        setTimeout(() => {
          location.reload();
        }, 5000);
      },
      error: function (response) {
        console.log(response)
        const error = $('.popupWrapper .popupItem[data-popup="error"]');
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
    $('.popupItem').fadeOut();
    $('.popupCenter').show();
    $('.popupWrapper').removeClass('_notClose');
    if (popupName == 'addAccount') {
      $('.popupItem').hide();
      $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: formData,
        success: function (response) {
          const success = $('.popupWrapper .popupItem[data-popup="success"]');
          success.show()
          success.children('.popupTitle').html(response.title);
          success.children('.popupText').html(response.message);
          success.fadeIn();
          setTimeout(() => {
            location.reload();
          }, 5000);
        },
        error: function (response) {
          const error = $('.popupWrapper .popupItem[data-popup="error"]');
          error.show()
          error.children('.popupTitle').html(response.title);
          error.children('.popupText').html(response.message);
          error.fadeIn();
        }
      });
    }
  });
});