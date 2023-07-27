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
            success: function (response) {
                console.log(response)
                let from = response.from;
                if (from == 'register') {
                    window.location.href = '/confirm';
                } else if (from == 'confirm') {
                    successHandler(response.title, response.message, '/cabinet')
                } else if (from == 'login') {
                    window.location.href = '/cabinet';
                } else if (from == 'repair') {
                    successHandler(response.title, response.message, '/login')
                }
            },
            error: function (response) {
                console.log(response)
                $.each(response.responseJSON.errors, function (key, value) {

                    if (key == 'not found') {
                        errorHandler('Возникла ошибка.', value)
                    }else{
                        $(`.tabPageInput[data-name=${key}]`).addClass('_error');
                        $(`.tabPageInput[data-name=${key}]`).append('<span class="errorInput">' + value + '</span>');
                        $('.errorInput').fadeOut(3000);
                        setTimeout(() => {
                            $('.errorInput').remove();
                        }, 3500);
                    }
                });
            }
        });
    });

    // Пагинация
    $(document).on('click', '.paginationItem', function (e) {
        e.preventDefault();
        if (!$(this).hasClass('_disabled')) {
            const currentPath = window.location.pathname;
            let admin = null;
            if(currentPath == '/cabinet/admin'){
                admin = $(this).parent().parent().attr('data-name');
                console.log(admin)
            }
            let action = $(this).attr('data-action');
            let page = $(this).attr('href');
            const parentType = ($(this).parents('.tabItem').attr('data-type')) ? $(this).parents('.tabItem').attr('data-type') : null
            let accountId = 0;
            let groupId = 0;
            let channelId = 0;
            let myPageId = 0;
            let myCommentId = 0;
            let formData = new FormData();
            if (parentType) {
                if (parentType === 'account') {
                    accountId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('account_id', accountId);
                } else if (parentType === 'group') {
                    groupId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('group_id', groupId);
                }else if (parentType === 'channel') {
                    channelId = $(this).parents('.tabItem').attr('data-tab');
                    formData.append('channel_id', channelId);
                }
            }
            if (page.match(/item(\d+)/)) {
                const match = page.match(/item(\d+)/);
                myPageId = match ? match[1] : null;
                formData.append('myPageId', myPageId);
            }
            if (page.match(/comments(\d+)/)) {
                const match = page.match(/comments(\d+)/);
                myCommentId = match ? match[1] : null;
                formData.append('myCommentId', myCommentId);
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
                    if (currentPath == '/cabinet/offers') {
                        const offerParent = $('.pageTab._active').attr('data-tab');
                        $(`.tabItem[data-tab=${offerParent}] .offerFlex`).html(response.responseText);
                    } else if ((currentPath == '/cabinet/vk/stories' || currentPath == '/cabinet/vk/posts' || currentPath == '/cabinet/vk/streams' || currentPath == '/cabinet/tg/tg_streams') && $('.pageTab[data-tab=all]').hasClass('_active')) {
                        $('.storyWrapper').html(response.responseText);
                    }else if ((currentPath == '/cabinet/partners')) {
                        $('.pageTable').html(response.responseText);
                    }else if ((currentPath == '/cabinet/admin') && admin) {
                        if (admin === 'finance') {
                            $("#operationsLoad").html(response.responseText);
                        }else{
                            $("#promocodesLoad").html(response.responseText);
                        }
                    } else {
                        if (accountId > 0 || groupId > 0 || channelId > 0) {
                            let itemId;
                            if(accountId > 0){
                                itemId = accountId;
                            }else if(groupId > 0){
                                itemId = groupId;
                            }else{
                                itemId = channelId;
                            }
                            $(`.tabItem[data-tab=${itemId}]`).children('.rowFlex').html(response.responseText);
                        } else if (myPageId > 0) {
                            $('.storyWrapper').html(response.responseText);
                        } else {
                            $('.tableWrapper').html(response.responseText);
                        }
                    }
                    sliderSearch()
                    playerSearch()
                }
            });
        }

    })
});