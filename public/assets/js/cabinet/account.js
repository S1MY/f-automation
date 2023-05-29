$(document).ready(function () {
  // Добавление аккаунта
  $('.addAccount').click(function (e) { 
    $('.popupItem').fadeOut();
    $(".popupWrapper").addClass('_notClose');
    let type;
    if ($(this).hasClass('firstAddAccount')) {
      type = $(this).parent().attr('data-tab');
    }else{
      type = $(this).parent().parent().parent().attr('data-tab');
    }
    $('.popupItem[data-popup="addAccount"]').children('input[name="type"]').val(type);
    $(".popupWrapper").fadeIn();
    $('.popupItem[data-popup="addAccount"]').fadeIn();
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
    let popupName = $(this).attr("data-popup");
    $('.popupItem').fadeOut();
    $('.popupCenter').show();
    $('.popupWrapper').removeClass('_notClose');
    if (popupName == 'addAccount') {
        // Типо здесь идёт отправка и ответ
        $('.popupItem').hide();
        // Успешный
        let title = "Всё прошло круто";
        let message =
          "Аккаунт - <span>Фамилия Имя</span> - успешно привязан.<br> Страница автоматически перегрузится, чтобы изменения применились.";
        $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
        $('.popupItem[data-popup="success"]')
          .children(".popupText")
          .html(message);
        $('.popupItem[data-popup="success"]').fadeIn();
        setTimeout(() => {
          location.reload();
        }, 5000);
        // Хуёвый
        // let title = "Пах-пах на губах";
        // let message = "Не удалось привязать аккаунт.<br> Скорее всего ссылка недействительна или неверно скопирована.";
        // $('.popupItem[data-popup="error"]').children(".popupTitle").html(title);
        // $('.popupItem[data-popup="error"]').children(".popupText").html(message);
        // $('.popupItem[data-popup="error"]').fadeIn();
    }
  });
});