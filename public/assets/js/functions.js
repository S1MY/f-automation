
const successHandler = (title, message, locate = null) => {
    $('.popupWrapper').fadeIn();
    $('.popupItem').fadeOut();
    const success = $('.popupItem[data-popup="success"]');
    success.children('.popupTitle').html(title);
    success.children('.popupText').html(message);
    success.fadeIn();
    setTimeout(() => {
      if(locate){
          window.location.href = locate;
      }else{
          location.reload();
      }
    }, 5000);
  }
const errorHandler = (title, message) => {
    $('.popupWrapper').fadeIn();
    $('.popupItem').fadeOut();
    const error = $('.popupItem[data-popup="error"]');
    error.children('.popupTitle').html(title);
    error.children('.popupText').html(message);
    error.fadeIn();
}
  