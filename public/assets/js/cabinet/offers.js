$(document).ready(function () {
    $('.tabPageForm').submit(function (e) {
        e.preventDefault();
        const ajaxUrl = $(this).attr('action');
        const formData = new FormData();
        const serialize = $(this).serializeArray();
        $.each(serialize, function (i, val) {
          formData.append(val.name, val.value);
        });
        if (!$('input[name="developer"]').is(':checked')) {
          formData.append('developer', 0);
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
            $('.popupWrapper').removeClass('_notClose');
            $('.popupItem').fadeOut();
            if (response.status == "success") {
              successHandler(response.title, response.message)
            } else if (response.status == "input") {
              $('.popupWrapper').fadeOut();
              const inputName = response.title;
              $(`.tabPageInput[data-name='${inputName}']`).addClass('_error');
              $(`.tabPageInput[data-name='${inputName}']`).append(`<span class="errorInput">${response.message}</span>`);
              $('.errorInput').fadeOut(3000);
              setTimeout(() => {
                $('.errorInput').remove();
              }, 3500);
            } else {
              errorHandler(response.title, response.message)
            }
          },
          error: function (response) {
            errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
          }
        });
    });
    $('.offerBtn').click(function (e) {
        e.preventDefault();
        let formData = new FormData();
        let offerId = $(this).attr('data-offer');
        formData.append("offerId", offerId);
        let action = '/offers/vote';
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
                    $(this).after('<p class="offerYes">Вы уже голосовали!</p>');
                    $(this).remove();
                    createMessage("<p class='fixedMessage'>Вы успешно проголосовали</p>");
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
    });
});