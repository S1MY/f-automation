$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  // Типы поста
  $(".streamType").click(function (e) {
    e.preventDefault();
    $(".streamType").removeClass("_active");
    $(this).addClass("_active");
    let type = $(this).attr("data-type");
    $('input[name="type"]').val(type);
    $("._type").removeClass("_active");
    $(`._type[data-type=${type}]`).addClass("_active");
  });
  // Загрузка и статус файла
  let detector = false;
  let video = document.createElement("video");
  $("#file").change(function (e) {
    e.preventDefault();
    let files = this.files;
    let totalSize = 0;
    detector = false;
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
    if (files.length > 1) {
      createMessage("<p class='fixedMessage _error'>Максимальное количество файлов равно 1</p>");
      durations = []
      filenames = []
      $("#file").val("");
      $('._filecounts').html('0 / 1')
      $('.fileName').html('Файл не выбран');
      $("._status").html('Файл не обнаружен');
      $("._status").removeClass('_complete');
      $("._size").html('0 mb');
      return false
    }
    $('._filecounts').html(`${files.length} / 1`);
    for (let i = 0; i < files.length; i++) {
      let file = files[i];
      let size = file.size;
      totalSize += size;

      if (size > 419430400) {
        $("#file").val("");
        createMessage("<p class='fixedMessage _error'>Максимальный размер видео-файла 50МБ</p>");
        return false
      } else {
        if (file.type == 'video/mp4' || file.type == 'video/avi') {
          let url = URL.createObjectURL(file);
          video.src = url;
          video.ondurationchange = function () {
            $('input[name="duration"]').val(this.duration);
          };
        }
        $('.fileName').html(file.name);
        let newSize = totalSize / 1048576;
        newSize = newSize.toFixed(2).replace(".00", "");
        $("._size").html(`${newSize} mb`);
        const videoData = new FormData();
        videoData.append("file", file);
        let xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", progressOperation, false);
        xhr.addEventListener("load", loadOperation, false);
        xhr.addEventListener("error", errorOperation, false);
        xhr.addEventListener("abort", abortOperation, false);
        xhr.open("POST", location.origin + "//upload-process");
        xhr.send(videoData);
      }
    }

    if (totalSize > 419430400) {
      $("#file").val("");
      createMessage("<p class='fixedMessage _error'>Общий размер загружаемых файлов не должен превышать 50МБ</p>");
      $('.fileName').html('Файл не выбран');
      $("._status").html('Файл не обнаружен');
      $("._status").removeClass('_complete');
      $("._size").html('0 mb');
      $('._filecounts').html('0 / 1')
      $("#file").val();
      detector = false;
    }
  });
  const loadOperation = () => {
    $(".tabPageInput[data-name='file']")
      .parent()
      .children("button")
      .removeClass("_disabled");
    $("._status").removeClass("_loader");
    $("._status").addClass("_complete");
    $("._status").html("Файлы загружены");
    createMessage("<p class='fixedMessage'>Файлы успешно загружены!</p>");
    detector = true;
  }
  $(".tabPageForm").submit(function (e) {
    e.preventDefault();
    const ajaxUrl = $(this).attr('action');
    if (detector == false) {
      createMessage("<p class='fixedMessage _error'>Дождитесь загрузки файла!</p>");
    } else {
      let formData = new FormData();
      let serialize = $(this).serializeArray();
      $.each(serialize, function (i, val) {
        formData.append(val.name, val.value);
      });
      if (!$('input[name="public"]').is(':checked')) {
        formData.append('public', 0);
      }
      if ($('input[name="interval"]').val() === '') {
        formData.append('interval', 0);
      }
      if ($('input[name="daily"]').val() === '') {
        formData.append('daily', 0);
      }
      if ($('input[name="date"]').val() === '') {
        formData.append('date', 'Пустое');
      }
      for (let i = 0; i < document.getElementById("file").files.length; i++) {
        const file = document.getElementById("file").files[i]
        formData.append(`file[${i}]`, file);
      }
      $(".popupWrapper").fadeIn();
      $('.popupItem').fadeOut();
      $('.popupItem[data-popup="loader"]').fadeIn();
      $(".popupWrapper").addClass('_notClose');
      $.ajax({
        type: "POST",
        url: ajaxUrl,
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        dataType: "json",
        success: function (response) {
          console.log(response)
          $('.popupWrapper').removeClass('_notClose');
          $('.popupItem').fadeOut();
          if (response.status == "success") {
            successHandler(response.title, response.message)
          } else if (response.status == "input") {
            $('.popupWrapper').fadeOut();
            let inputName = response.title;
            if (response.title == 'accType') {
              inputName = 'account'
            } else if (response.title == 'accountId') {
              inputName = 'account'
            }else if (response.title == 'channelId') {
              inputName = 'channel'
            } else if (response.title == 'duration') {
              inputName = 'file'
            } else if (response.title == 'type') {
              inputName = 'tabs'
            }else if (response.title == 'interval') {
              inputName = 'tabs'
            }else if (response.title == 'daily') {
              inputName = 'tabs'
            }else if (response.title == 'date') {
              inputName = 'tabs'
            }
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
          console.log(response)
          errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
        }
      });
    }
  });
});
