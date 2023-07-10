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
    $(document).on('click', '.offerBtn', function (e) {
        e.preventDefault();
        const ajaxUrl = $(this).attr('data-action');
        const offerId = $(this).attr('data-offer');
        $.ajax({
            url: ajaxUrl,
            method: 'POST',
            data: { offerId: offerId },
            success: function (response) {
              if (response.status == "success") {
                $(`.offerBtn[data-offer=${response.message}]`).prev().children().children().html(response.title);
                $(`.offerBtn[data-offer=${response.message}]`).addClass('offerYes');
                $(`.offerBtn[data-offer=${response.message}]`).html('Вы уже голосовали');
                createMessage("<p class='fixedMessage'>Вы успешно проголосовали</p>");
              } else {
                errorHandler(response.title, response.message)
              }
            },
            error: function (response) {
              errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
            }
        });
    });
});