$(document).ready(function () {
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
            if(response.result == "success"){
                $(this).after('<p class="offerYes">Вы уже голосовали!</p>');
                $(this).remove();
                let message = "<p class='fixedMessage'>Вы успешно проголосовали</p>";
                createMessage(message);
            }else{
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