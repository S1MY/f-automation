$(document).ready(function () {
  $('.passForm').submit(function (e) {
    e.preventDefault();
    const ajaxUrl = $(this).attr('action');
    const formData = $(this).serialize();
    $.ajax({
      url: ajaxUrl,
      method: 'POST',
      data: formData,
      success: function (response) {
        if (response.status == "success") {
          successHandler(response.title, response.message)
        }else if (response.status == "input") {
          let inputName = response.title;
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
  });
});