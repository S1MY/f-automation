$(document).ready(function () {
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
        let message =
          "<p class='fixedMessage _error'>Максимальный размер видео-файла 200МБ</p>";
        createMessage(message);
      } else {
        if (file.type == 'video/mp4') {
            let url = URL.createObjectURL(file);
            video.src = url;
            video.ondurationchange = function () {
              $('input[name="duration"]').val(this.duration);
            };
        }else{
            $('input[name="duration"]').val(14.99);
        }
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
      let message = "<p class='fixedMessage'>Файл успешно загружен!</p>";
      createMessage(message);
      detector = true;
    }
    function errorOperation(event) {
      $("#file").val("");
      let message =
        "<p class='fixedMessage _error'>При загрузке возникла ошибка!</p>";
      createMessage(message);
    }
    function abortOperation(event) {
      $("#file").val("");
      let message =
        "<p class='fixedMessage _error'>Не удалось загрузить файл!</p>";
      createMessage(message);
    }
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
    $(".tabPageForm").submit(function (e) {
      e.preventDefault();
      const ajaxUrl = $(this).attr('action');
      if (detector == false) {
        let message = "<p class='fixedMessage _error'>Дождитесь загрузки файла!</p>";
        createMessage(message);
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
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          type: "POST",
          url: ajaxUrl,
          contentType: false,
          processData: false,
          cache: false,
          data: formData,
          dataType: "json",
          success: function (response) {
            console.log(response.status)
            $('.popupWrapper').removeClass('_notClose');
            $('.popupItem').fadeOut();
            if(response.status == "success"){
              const success = $('.popupItem[data-popup="success"]');
              success.children('.popupTitle').html(response.title);
              success.children('.popupText').html(response.message);
              success.fadeIn();
              setTimeout(() => {
                location.reload();
              }, 5000);
            }else if(response.status == "input"){
              $('.popupWrapper').fadeOut();
              let inputName = response.title;
              inputName = (inputName === 'accType') ? 'account' : 'accType'
              inputName = (inputName === 'accountId') ? 'account' : 'accountId'
              inputName = (inputName === 'accountId') ? 'account' : 'accountId'
              inputName = (inputName === 'duration') ? 'file' : 'duration'
              $(`.tabPageInput[data-name=${inputName}]`).addClass('_error');
              $(`.tabPageInput[data-name=${inputName}]`).append(`<span class="errorInput">${response.message}</span>`);
              $('.errorInput').fadeOut(3000);
              setTimeout(() => {
                $('.errorInput').remove();
              }, 3500);
              $(".popupWrapper").fadeOut();
              $('.popupItem').fadeOut();
            }
          },
          error: function (response) {
            $('.popupWrapper').removeClass('_notClose');
            $('.popupItem').fadeOut();
            const error = $('.popupItem[data-popup="error"]');
            error.children('.popupTitle').html(response.title);
            error.children('.popupText').html(response.message);
            error.fadeIn();
          }
        });
      }
    });
});