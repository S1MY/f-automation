$(document).ready(function () {
    $('.promoForm ').submit(function (e) {
        e.preventDefault();
        let action = $(this).attr('data-action');
        let formData = new FormData();
        let serialize = $(this).serializeArray();
        $.each(serialize, function (i, val) {
          formData.append(val.name, val.value);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
          type: "POST",
          url: action,
          contentType: false,
          processData: false,
          cache: false,
          data: formData,
          dataType: "json",
          success: function (result) {

            let success = $('.popupWrapper .popupItem[data-popup="success"]');

            $('.popupWrapper').show();
            success.show()

            let title = result.title;
            let message = result.message;

            $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="success"]').children(".popupText").html(message);

            $('.popupItem[data-popup="success"]').fadeIn();
            setTimeout(() => {
            location.reload();
            }, 5000);

          },
          error: function (result) {
              console.log('Ошибка');

              $.each(result.responseJSON.errors, function (key, value) {
                $(`.tabPageInput[data-name=${key}]`).addClass('_error');
                $(`.tabPageInput[data-name=${key}]`).append('<span class="errorInput">'+value+'</span>');
              });

              $('.errorInput').fadeOut(3000);
              setTimeout(() => {
                $('.errorInput').remove();
              }, 3500);

              $(".popupWrapper").fadeOut();
              $('.popupItem').fadeOut();
          }
        });
    });


    // Админка смена статуса офера
    $(document).on('click', '.offerBtn' , function(e) {
        $(this).prev().attr('checked', 1);
        $(this).parent().parent().parent().submit();
    });

    $(document).on('submit', '.offerItem' , function(e) {
        e.preventDefault();

        let ajaxUrl = $(this).attr('action');
        let formData = $(this).serialize();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: ajaxUrl,
            method: 'post',
            data: formData,
            success: function(result){

                var ofClass = 'offerBtn _yes';

                if( result.status != 1 ){
                    var ofClass = 'offerBtn _no';
                }

                $('#'+result.offer_id).html('<p class="offerResult '+ofClass+'">' + result.message + '</p>')
            },
            error: function (result) {
                console.log(result);

                $.each(result.responseJSON.errors, function(key,value) {
                    let error = $('.popupWrapper .popupItem[data-popup="error"]');

                    error.children('.popupTitle').html('');
                    error.children('.popupText').html('');

                    $('.popupWrapper').show();
                    error.show();

                    error.children('.popupTitle').append('Возникла ошибка.');
                    error.children('.popupText').append(value);
                });

            }
        });
    });

});
