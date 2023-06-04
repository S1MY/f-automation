$(function () {
    $('.ajaxFormController').submit(function (e) {
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
            success: function (result) {

                console.log(result);

                let from = result.from;

                if (from == 'register') {
                    window.location.href = '/confirm';
                } else if (from == 'confirm') {
                    let success = $('.popupWrapper .popupItem[data-popup="success"]');

                    $('.popupWrapper').show();
                    success.show()

                    success.children('.popupTitle').html('');
                    success.children('.popupText').html('');

                    success.children('.popupTitle').append(result.title);
                    success.children('.popupText').append(result.message);

                    window.location.href = '/cabinet';
                } else if (from == 'login') {
                    window.location.href = '/cabinet';
                } else if (from == 'offers') {
                    let success = $('.popupWrapper .popupItem[data-popup="success"]');

                    $('.popupWrapper').show();
                    success.show()

                    success.children('.popupTitle').html('');
                    success.children('.popupText').html('');

                    success.children('.popupTitle').append(result.title);
                    success.children('.popupText').append(result.message);

                    setTimeout(() => {
                        $('.popupWrapper').hide();
                        location.reload();
                    }, 3500);

                }

            },
            error: function (result) {
                console.log(result);

                $.each(result.responseJSON.errors, function (key, value) {
                    console.log(key);

                    if (key == 'not found') {
                        let error = $('.popupWrapper .popupItem[data-popup="error"]');

                        error.children('.popupTitle').html('');
                        error.children('.popupText').html('');

                        $('.popupWrapper').show();
                        error.show();

                        error.children('.popupTitle').append('Возникла ошибка.');
                        error.children('.popupText').append(value);
                    }

                    $(`.tabPageInput[data-name=${key}]`).addClass('_error');
                    $(`.tabPageInput[data-name=${key}]`).append('<span class="errorInput">' + value + '</span>');
                });

                $('.errorInput').fadeOut(3000);
                setTimeout(() => {
                    $('.errorInput').remove();
                }, 3500);
            }
        });
    });

    // Проголосовать
    $(document).on('click', '.offerBtn', function (e) {
        e.preventDefault();
        if (!$(this).hasClass('offerYes')) {
            let ajaxUrl = $(this).attr('data-action');
            let offerId = $(this).attr('data-offer');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: ajaxUrl,
                method: 'post',
                data: { offerId: offerId },
                success: function (result) {

                    console.log(result);
                    $('.offerBtn[data-offer=' + result.offer_id + ']').prev().children().children().html(result.now);
                    $('.offerBtn[data-offer=' + result.offer_id + ']').addClass('offerYes');
                    $('.offerBtn[data-offer=' + result.offer_id + ']').html('Вы уже голосовали!');

                },
                error: function (result) {
                    console.log(result);

                    $.each(result.responseJSON.errors, function (key, value) {
                        console.log(key);

                        if (key == 'not found') {
                            let error = $('.popupWrapper .popupItem[data-popup="error"]');

                            error.children('.popupTitle').html('');
                            error.children('.popupText').html('');

                            $('.popupWrapper').show();
                            error.show();

                            error.children('.popupTitle').append('Возникла ошибка.');
                            error.children('.popupText').append(value);
                        }

                        $(`.tabPageInput[data-name=${key}]`).addClass('_error');
                        $(`.tabPageInput[data-name=${key}]`).append('<span class="errorInput">' + value + '</span>');
                    });

                    $('.errorInput').fadeOut(3000);
                    setTimeout(() => {
                        $('.errorInput').remove();
                    }, 3500);
                }
            });
        }


    });

    // Пагинация
    $(document).on('click', '.paginationItem', function (e) {
        e.preventDefault();
        if (!$(this).hasClass('_disabled')) {
            let action = $(this).attr('data-action');
            let page = $(this).attr('href');
            const parentType = ($(this).parents('.tabItem').attr('data-type')) ? $(this).parents('.tabItem').attr('data-type') : null
            let accountId = 0;
            let groupId = 0;
            let formData = new FormData();
            if(parentType){
                if(parentType === 'account'){
                    accountId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('accountId', accountId);
                }else if(parentType === 'group'){
                    groupId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('groupId', groupId);
                }
            }
            formData.append('page', page);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            console.log(accountId)
            $.ajax({
                type: "POST",
                url: action,
                contentType: false,
                processData: false,
                cache: false,
                data: formData,
                dataType: "json",
                complete: function (response) {
                    console.log(response)
                    if (window.location.pathname == '/cabinet/offers' && $('.pageTab[data-tab=all]').hasClass('_active')) {
                        $('.offerLoadWrapper').html(response.responseText);
                    } else if (window.location.pathname == '/cabinet/vk/stories' && $('.pageTab[data-tab=all]').hasClass('_active')) {
                        $('.storiesAllWrapper').html(response.responseText);
                    } else {
                        if(accountId > 0){
                            $(`.tabItem[data-tab=${accountId}]`).children('.rowFlex').html(response.responseText);
                        }else if(groupId > 0){
                            $(`.tabItem[data-tab=${groupId}]`).children('.rowFlex').html(response.responseText);
                        }else{
                            $('.tableWrapper').html(response.responseText);
                        }
                    }

                }
            });
        }

    })
});
