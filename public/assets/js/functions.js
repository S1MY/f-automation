
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
let sliders = [];
const sliderSearch = () => {
  sliders.forEach(function (slider) {
    slider.slick('unslick');
  });
  sliders.splice(0, sliders.length);
  if ($('.itemsSlider').length > 0) {
    $(this).slick({
      infinite: false,
      prevArrow: '<p class="sliderArrow prev"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.8655 17.1671L10.0835 11.9995L14.8655 6.83187C14.9511 6.73954 14.999 6.6156 14.999 6.48653C14.999 6.35747 14.9511 6.23353 14.8655 6.1412C14.8239 6.0965 14.7743 6.06098 14.7195 6.03673C14.6647 6.01249 14.6059 6 14.5465 6C14.4871 6 14.4283 6.01249 14.3735 6.03673C14.3187 6.06098 14.2691 6.0965 14.2275 6.1412L9.13933 11.6386C9.05 11.7352 9 11.8647 9 11.9995C9 12.1343 9.05 12.2638 9.13933 12.3603L14.2265 17.8577C14.2681 17.9028 14.3179 17.9385 14.3728 17.963C14.4278 17.9874 14.4868 18 14.5465 18C14.6061 18 14.6652 17.9874 14.7201 17.963C14.7751 17.9385 14.8248 17.9028 14.8665 17.8577C14.9521 17.7654 15 17.6415 15 17.5124C15 17.3833 14.9521 17.2594 14.8665 17.1671L14.8655 17.1671Z" fill="#101010"/></svg></p>',
      nextArrow: '<p class="sliderArrow next"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.13452 6.83293L13.9165 12.0005L9.13452 17.1681C9.04891 17.2605 9.00098 17.3844 9.00098 17.5135C9.00098 17.6425 9.04891 17.7665 9.13452 17.8588C9.1761 17.9035 9.22573 17.939 9.28051 17.9633C9.33528 17.9875 9.3941 18 9.45351 18C9.51293 18 9.57174 17.9875 9.62652 17.9633C9.68129 17.939 9.73092 17.9035 9.7725 17.8588L14.8607 12.3614C14.95 12.2648 15 12.1353 15 12.0005C15 11.8657 14.95 11.7362 14.8607 11.6397L9.77349 6.14226C9.73187 6.09724 9.68212 6.06145 9.62716 6.03702C9.57221 6.01259 9.51316 6 9.45351 6C9.39386 6 9.33481 6.01259 9.27986 6.03702C9.2249 6.06145 9.17515 6.09724 9.13354 6.14226C9.04793 6.23459 9 6.35853 9 6.48759C9 6.61666 9.04793 6.7406 9.13354 6.83293L9.13452 6.83293Z" fill="#101010"/></svg></p>',
    });
    sliders.push($(this));
  }
}
const playerPause = () => {
  if ($('.videoGuid').length > 0) {
    players.forEach(function (player) {
      player.pause();
    });
  }
}
const progressOperation = (event) => {
  const percentLoaded = Math.round((event.loaded / event.total) * 100);
  $(".progressbar").css("width", percentLoaded + "%");
  $("._status").addClass("_loader");
  $("._status").html("Идёт загрузка...");
}
const errorOperation = () => {
  $("#file").val("");
  createMessage("<p class='fixedMessage _error'>При загрузке возникла ошибка!</p>");
}
const abortOperation = () => {
  $("#file").val("");
  createMessage("<p class='fixedMessage _error'>Не удалось загрузить файл!</p>");
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