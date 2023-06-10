
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
const progressOperation = (event) => {
  const percentLoaded = Math.round((event.loaded / event.total) * 100);
  $(".progressbar").css("width", percentLoaded + "%");
  $("._status").addClass("_loader");
  $("._status").html("Идёт загрузка...");
}
const loadOperation = (detector) => {
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