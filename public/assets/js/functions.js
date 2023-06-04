
const successHandler = (title, message, locate = null) => {
    $('.popupWrapper').fadeIn();
    $('.popupItem').fadeOut();
    const success = $('.popupItem[data-popup="success"]');
    success.children('.popupTitle').html(title);
    success.children('.popupText').html(message);
    success.fadeIn();
    setTimeout(() => {
        if (locate) {
            window.location.href = locate;
        } else {
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
let players = []
const playerSearch = () => {
    players = []
    if ($('.videoGuid').length > 0) {
        $('.videoGuid').each(function () {
            const player = new Plyr(this);
            players.push(player);
        });
    }
}
const playerPause = () => {
    if ($('.videoGuid').length > 0) {
        players.forEach(function (player) {
          player.pause();
        });
      }
}

const createMessage = (message) => {
  $("body").append(message);
  $(".fixedMessage").fadeIn();
  setTimeout(function () {
    $(".fixedMessage").fadeOut();
  }, 3000);
  setTimeout(function () {
    $(".fixedMessage").remove();
  }, 4000);
}