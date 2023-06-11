$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.ajaxFormController').submit(function (e) {
        e.preventDefault();
        let ajaxUrl = $(this).attr('action');
        let formData = $(this).serialize();
        $.ajax({
            url: ajaxUrl,
            method: 'post',
            data: formData,
            success: function (result) {
                let from = result.from;
                if (from == 'register') {
                    window.location.href = '/confirm';
                } else if (from == 'confirm') {
                    successHandler(response.title, response.message, '/cabinet')
                } else if (from == 'login') {
                    window.location.href = '/cabinet';
                } else if (from == 'offers') {
                    successHandler(response.title, response.message)
                }

            },
            error: function (result) {

                $.each(result.responseJSON.errors, function (key, value) {

                    if (key == 'not found') {
                        errorHandler('Возникла ошибка.', value)
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
            $.ajax({
                url: ajaxUrl,
                method: 'post',
                data: { offerId: offerId },
                success: function (result) {
                    $('.offerBtn[data-offer=' + result.offer_id + ']').prev().children().children().html(result.now);
                    $('.offerBtn[data-offer=' + result.offer_id + ']').addClass('offerYes');
                    $('.offerBtn[data-offer=' + result.offer_id + ']').html('Вы уже голосовали!');

                },
                error: function (result) {
                    $.each(result.responseJSON.errors, function (key, value) {
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
            let myPageId = 0;
            let formData = new FormData();
            if(parentType){
                if(parentType === 'account'){
                    accountId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('account_id', accountId);
                }else if(parentType === 'group'){
                    groupId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('group_id', groupId);
                }
            }
            if(page.includes('?myPageId=')){
                myPageId = page.split('?')[1].split('&').find(param => param.startsWith('myPageId=')).split('=')[1];
                page = page.replace('?myPageId=' + myPageId, '/' + myPageId);
                formData.append('myPageId', myPageId);
            }
            formData.append('page', page);
            $.ajax({
                type: "POST",
                url: action,
                contentType: false,
                processData: false,
                cache: false,
                data: formData,
                dataType: "json",
                complete: function (response) {
                    const cuurentPath = window.location.pathname;
                    if (cuurentPath == '/cabinet/offers' && $('.pageTab[data-tab=all]').hasClass('_active')) {
                        $('.offerLoadWrapper').html(response.responseText);
                    } else if (cuurentPath == '/cabinet/vk/stories' && $('.pageTab[data-tab=all]').hasClass('_active')) {
                        $('.storiesAllWrapper').html(response.responseText);
                    }else if (cuurentPath == '/cabinet/vk/posts' && $('.pageTab[data-tab=all]').hasClass('_active')) {
                        $('.postsAllWrapper').html(response.responseText);
                    }else {
                        if(accountId > 0){
                            $(`.tabItem[data-tab=${accountId}]`).children('.rowFlex').html(response.responseText);
                        }else if(groupId > 0){
                            $(`.tabItem[data-tab=${groupId}]`).children('.rowFlex').html(response.responseText);
                        }else if(myPageId > 0){
                            $('.streamStory').html(response.responseText);
                        }else{
                            $('.tableWrapper').html(response.responseText);
                        }
                    }
                    if($('.videoGuid').length > 0){
                      const player = new Plyr('.videoGuid');
                    }
                }
            });
        }

    })
});