$(document).ready(function () {
  if($('.itemsSlider').length > 0){
    $('.itemsSlider').slick({
      infinite: false,
      prevArrow: '<p class="sliderArrow prev"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.8655 17.1671L10.0835 11.9995L14.8655 6.83187C14.9511 6.73954 14.999 6.6156 14.999 6.48653C14.999 6.35747 14.9511 6.23353 14.8655 6.1412C14.8239 6.0965 14.7743 6.06098 14.7195 6.03673C14.6647 6.01249 14.6059 6 14.5465 6C14.4871 6 14.4283 6.01249 14.3735 6.03673C14.3187 6.06098 14.2691 6.0965 14.2275 6.1412L9.13933 11.6386C9.05 11.7352 9 11.8647 9 11.9995C9 12.1343 9.05 12.2638 9.13933 12.3603L14.2265 17.8577C14.2681 17.9028 14.3179 17.9385 14.3728 17.963C14.4278 17.9874 14.4868 18 14.5465 18C14.6061 18 14.6652 17.9874 14.7201 17.963C14.7751 17.9385 14.8248 17.9028 14.8665 17.8577C14.9521 17.7654 15 17.6415 15 17.5124C15 17.3833 14.9521 17.2594 14.8665 17.1671L14.8655 17.1671Z" fill="#101010"/></svg></p>',
      nextArrow: '<p class="sliderArrow next"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.13452 6.83293L13.9165 12.0005L9.13452 17.1681C9.04891 17.2605 9.00098 17.3844 9.00098 17.5135C9.00098 17.6425 9.04891 17.7665 9.13452 17.8588C9.1761 17.9035 9.22573 17.939 9.28051 17.9633C9.33528 17.9875 9.3941 18 9.45351 18C9.51293 18 9.57174 17.9875 9.62652 17.9633C9.68129 17.939 9.73092 17.9035 9.7725 17.8588L14.8607 12.3614C14.95 12.2648 15 12.1353 15 12.0005C15 11.8657 14.95 11.7362 14.8607 11.6397L9.77349 6.14226C9.73187 6.09724 9.68212 6.06145 9.62716 6.03702C9.57221 6.01259 9.51316 6 9.45351 6C9.39386 6 9.33481 6.01259 9.27986 6.03702C9.2249 6.06145 9.17515 6.09724 9.13354 6.14226C9.04793 6.23459 9 6.35853 9 6.48759C9 6.61666 9.04793 6.7406 9.13354 6.83293L9.13452 6.83293Z" fill="#101010"/></svg></p>',
    });
  }
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  // Загрузка и статус файла
  let detector = false;
  let video = document.createElement("video");
  let durations = []
  let filenames = []
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
    if (files.length > 5) {
      createMessage("<p class='fixedMessage _error'>Максимальное количество файлов равно 5</p>");
      return false
    }
    $('._filecounts').html(`${files.length} / 5`);
    for (let i = 0; i < files.length; i++) {
      let file = files[i];
      let size = file.size;
      totalSize += size;

      if (size > 419430400) {
        $("#file").val("");
        createMessage("<p class='fixedMessage _error'>Максимальный размер видео-файла 50МБ</p>");
        return false
      } else {
        if (file.type == 'video/mp4') {
          let url = URL.createObjectURL(file);
          video.src = url;
          video.ondurationchange = function () {
            durations.push(this.duration)
          };
        } else {
          durations.push(14.99)
        }
        $('input[name="duration"]').val(durations.join('_;_'));
        filenames.push(file.name)
        $('.fileName').html(filenames.join('_;_'));
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
        xhr.open("POST", "upload_processing.php");
        xhr.send(videoData);
      }
    }

    if (totalSize > 1258291200) {
      $("#file").val("");
      createMessage("<p class='fixedMessage _error'>Общий размер загружаемых файлов не должен превышать 150МБ</p>");
      $('.fileName').html('Файл не обнаружен');
      $("._status").html('0 mb');
      $("._status").removeClass('_complete');
      $("._size").html('0 mb');
      $('._filecounts').html('0 / 5')
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
      if (!$('input[name="closeComments"]').is(':checked')) {
        formData.append('closeComments', 0);
      }
      if (!$('input[name="onlyFriends"]').is(':checked')) {
        formData.append('onlyFriends', 0);
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
            } else if (response.title == 'duration') {
              inputName = 'file'
            }
            $(`.tabPageInput[data-name='${inputName}']`).addClass('_error');
            $(`.tabPageInput[data-name='${inputName}']`).append(`<span class="errorInput">${response.message}</span>`);
            $('.errorInput').fadeOut(3000);
            setTimeout(() => {
              $('.errorInput').remove();
            }, 3500);
            $(".popupWrapper").fadeOut();
            $('.popupItem').fadeOut();
          } else {
            errorHandler(response.title, response.message)
          }
        },
        error: function (response) {
          errorHandler('Возникла ошибка', 'Возможно требуется обратиться к администратору')
        }
      });
    }
  });
});