$(document).ready(function () {
    $(".tabPageForm").submit(function (e) {
        e.preventDefault();
        let action = $(this).attr('data-action');
        let formData = new FormData();
        let serialize = $(this).serializeArray();
        $.each(serialize, function (i, val) {
          formData.append(val.name, val.value);
        });
        $.ajax({
          type: "POST",
          url: action,
          contentType: false,
          processData: false,
          cache: false,
          data: formData,
          dataType: "json",
          success: function (response) {
            $('.popupWrapper').removeClass('_notClose');
            if(response.result == "success"){
              // Успешный
              let message = "<p class='fixedMessage'>Настройка изменена!</p>";
              createMessage(message);
                $(this).get(0).reset();
            }else if(response.result == "inputs"){
              // Клиентские ошибки
              $.each(response.errors, function (key, value) {
                $(`.tabPageInput[data-name=${key}]`).addClass('_error');
                $(`.tabPageInput[data-name=${key}]`).append('<span class="errorInput">Название ошибки</span>');
              });
              $('.errorInput').fadeOut(3000);
              setTimeout(() => {
                $('.errorInput').remove();
              }, 3500);
              $(".popupWrapper").fadeOut();
              $('.popupItem').fadeOut();
            }else{
              // Серверные ошибки
              let message = "<p class='fixedMessage _error'>Андрюха по коням. Походу криминал</p>";
              createMessage(message);
            }
          }
        });
    });
    function createMessage(message) {
      $("body").append(message);
      $(".fixedMessage").fadeIn();
      setTimeout(function () {
        $(".fixedMessage").fadeOut();
      }, 3000);
      setTimeout(function () {
        $(".fixedMessage").remove();
      }, 4000);
    }
});