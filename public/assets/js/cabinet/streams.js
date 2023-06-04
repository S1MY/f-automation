$(document).ready(function () {
  let errorSvg = '<svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 64 64"><circle cx="32" cy="32" r="28" fill="none" stroke="#FF5437" stroke-miterlimit="10" stroke-width="4"/><line x1="32" x2="32" y1="18" y2="38" fill="none" stroke="#FF5437" stroke-miterlimit="10" stroke-width="4"/><line x1="32" x2="32" y1="42" y2="46" fill="none" stroke="#FF5437" stroke-miterlimit="10" stroke-width="4"/></svg>';
  // Типы стрима
  $(".streamType").click(function (e) {
    e.preventDefault();
    $(".streamType").removeClass("_active");
    $(this).addClass("_active");
    let type = $(this).attr("data-type");
    $('input[name="type"]').val(type);
    $("._type").removeClass("_active");
    $(`._type[data-type=${type}]`).addClass("_active");
  });
  // Загрузка и статус видоса
  let detector = false;
  let video = document.createElement("video");
  $("#file").change(function (e) {
    e.preventDefault();
    let file = this.files[0];
    let size = file.size;
    detector == false;
    $(".progressbar").css("width", "0%");
    if (
      $(".tabPageInput[data-name='file']")
        .parent()
        .children("button")
        .hasClass("_disabled")
    ) {
    } else {
      $(".tabPageInput[data-name='file']")
        .parent()
        .children("button")
        .addClass("_disabled");
    }
    if (size > 209715200) {
      $("#file").val("");
      createMessage("<p class='fixedMessage _error'>Максимальный размер видео-файла 200МБ</p>");
    } else {
      let url = URL.createObjectURL(file);
      video.src = url;
      video.ondurationchange = function () {
        $('input[name="duration"]').val(this.duration);
      };
      $('.fileName').html(file.name);
      let newSize = size / 1048576;
      newSize = newSize.toFixed(2).replace(".00", "");
      $("._size").html(`${newSize} mb`);
      const videoData = new FormData();
      videoData.append("file", file);
      let xhr = new XMLHttpRequest();
      xhr.upload.addEventListener("progress", progressOperation, false);
      xhr.addEventListener("load", loadOperation, false);
      xhr.addEventListener("error", errorOperation, false);
      xhr.addEventListener("abort", abortOperation, false);
      xhr.open("POST", "upload_processing.php");
      xhr.send(videoData);
    }
  });
  function progressOperation(event) {
    const percentLoaded = Math.round((event.loaded / event.total) * 100);
    $(".progressbar").css("width", percentLoaded + "%");
    $("._status").addClass("_loader");
    $("._status").html("Идёт загрузка...");
  }
  function loadOperation(event) {
    $(".tabPageInput[data-name='file']")
      .parent()
      .children("button")
      .removeClass("_disabled");
    $("._status").removeClass("_loader");
    $("._status").addClass("_complete");
    $("._status").html("Файл загружен");
    createMessage("<p class='fixedMessage'>Файл успешно загружен!</p>");
    detector = true;
  }
  function errorOperation(event) {
    $("#file").val("");
    createMessage("<p class='fixedMessage _error'>При загрузке возникла ошибка!</p>");
  }
  function abortOperation(event) {
    $("#file").val("");
    createMessage("<p class='fixedMessage _error'>Не удалось загрузить файл!</p>");
  }
  $('.cardButton').click(function (e) { 
    e.preventDefault();
    let action;
    let formData = new FormData();
    let streamId = $(this).attr('data-stream');
    formData.append("streamId", streamId);
    if ($(this).hasClass('_on')) {
      let action = '/stream/on';
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
            let title = "Вай вай вай";
            let message = "Трансляция 'Название' успешно активирована.";
            $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="success"]')
              .children(".popupText")
              .html(message);
            $('.popupItem[data-popup="success"]').fadeIn();
            setTimeout(() => {
              $('.popupItem[data-popup="success"]').fadeOut();
            }, 5000);
            // Обновление данных
            $(this).parent().parent().children('._nextStart').children('span').html('14.12.2023 в 5:20 (МСК)');
            $(this).removeClass('_on');
            $(this).addClass('_off');
            $(this).html('Отключить');
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
    }else if($(this).hasClass('_off')){
      let action = '/stream/off';
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
            let title = "Вай вай вай";
            let message = "Трансляция 'Название' успешно остановлена.";
            $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="success"]')
              .children(".popupText")
              .html(message);
            $('.popupItem[data-popup="success"]').fadeIn();
            setTimeout(() => {
              $('.popupItem[data-popup="success"]').fadeOut();
            }, 5000);
            // Обновление данных
            $(this).parent().parent().children('._nextStart').children('span').html('Не определён');
            $(this).removeClass('_off');
            $(this).addClass('_on');
            $(this).html('Включить');
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
    }else if($(this).hasClass('_edit')){
      $('.editText').slideDown();
      let action = '/stream/generateEdit';
      // Запрашиваем данные и на их основе генерируем форму
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
            $('.tabPageForm[data-action="/stream/create"]').hide();
            $('.tabPageItem').append(editForm);
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
    }else if($(this).hasClass('_remove')){
      let action = '/stream/remove';
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
            let title = "Вай вай вай";
            let message = "Трансляция успешно удалена.<br> Сейчас обновится страница для применения изменений.";
            $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="success"]')
              .children(".popupText")
              .html(message);
            $('.popupItem[data-popup="success"]').fadeIn();
            setTimeout(() => {
              location.reload();
            }, 5000);
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
    }else{
      let link = $(this).attr('href');
      window.location = link;
    }
  });
  $('.editStop').click(function (e) { 
    e.preventDefault();
    $('.tabPageForm[data-action="/stream/create"]').show();
    $('.tabPageForm[data-action="/stream/edit"]').remove();
  });
  $(".tabPageForm").submit(function (e) {
    e.preventDefault();
    let action = $(this).attr('data-action');
    if (detector == false) {
      createMessage("<p class='fixedMessage _error'>Дождитесь загрузки файла!</p>");
    }else{
      let formData = new FormData();
      let serialize = $(this).serializeArray();
      $.each(serialize, function (i, val) {
        formData.append(val.name, val.value);
      });
      let file = document.getElementById("file").files[0];
      formData.append("file", file);
      $(".popupWrapper").fadeIn();
      $('.popupItem').fadeOut();
      $('.popupItem[data-popup="loader"]').fadeIn();
      $(".popupWrapper").addClass('_notClose');
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
            let title = "Вай вай вай";
            let message = "Трансляция успешно создана.<br> Сейчас обновится страница для применения изменений.";
            $('.popupItem[data-popup="success"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="success"]')
              .children(".popupText")
              .html(message);
            $('.popupItem[data-popup="success"]').fadeIn();
            setTimeout(() => {
              location.reload();
            }, 5000);
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
            let title = "ОЙ ОЙ ОЙ";
            let message = "Андрюха по коням. Походу криминал.";
            $('.popupItem[data-popup="error"]').children(".popupTitle").html(title);
            $('.popupItem[data-popup="error"]').children(".popupText").html(message);
            $('.popupItem[data-popup="error"]').fadeIn();
          }
        }
      });
    }
  });
});
