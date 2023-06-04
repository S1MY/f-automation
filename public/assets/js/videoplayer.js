// $(document).ready(function () {
  // Константа дял проверки поддержки видеоформата
  const videoWorks = !!document.createElement("video").canPlayType;
  let counter = 0;
  if (videoWorks) {
    creator();
  }
  // Данная функция должна работать при пагинации с видеороликами
  function creator(){
    $.each($(".videoGuid"), function (indexInArray, valueOfElement) {
      valueOfElement.controls = false;
      let videoDuration;
      $(valueOfElement).attr('data-index', counter);
      counter++;
      valueOfElement.volume = 0.5;
      valueOfElement.onloadedmetadata = function () {
        videoDuration = Math.round(valueOfElement.duration);
        let time = formatTime(videoDuration);
        $(this)
          .next()
          .children(".videoTime")
          .children(".duration")
          .html(`${time.minutes}:${time.seconds}`);
        $(this).next().children('.videoProgress').children('.seek').prop('max', videoDuration);
        $('.volume').val(valueOfElement.volume);
        let curr = $('.volume').val();
        let min = $('.volume').attr('min');
        let max = $('.volume').attr('max');
        $('.volume').css('background-size', (curr * 100) / max + '% 100%');
      };
    });
  }
  $('.videoStarter').click(function (e) { 
    e.preventDefault();
    let parent = $(this).parent();
    let index = $(parent).children('.videoGuid').attr('data-index');
    allStop(index);
    let timer = null;
    let video = $(parent).children('.videoGuid').get(0);
    clearInterval(timer);
    if (video.paused || video.ended) {
      videoStart(parent, video, timer)
    } else {
      videoStop(parent, video);
    }
  });
  $(".togglePlay").click(function (e) {
    e.preventDefault();
    let parent = $(this).parent().parent();
    let index = $(parent).children('.videoGuid').attr('data-index');
    allStop(index);
    let timer = null;
    let video = $(parent).children('.videoGuid').get(0);
    clearInterval(timer);
    if (video.paused || video.ended) {
      videoStart(parent, video, timer)
    } else {
      videoStop(parent, video);
    }
  });
  function allStop(index){
    $.each($(".videoGuid"), function (indexInArray, valueOfElement) {
      if (indexInArray != index) {
        valueOfElement.pause();
        $(valueOfElement).next().removeClass('_hidden');
        if ($('.videoName').length > 0) {
          $('.videoName').removeClass('_hidden')
        }
          $('.stop').hide();
          $('.play').show();
      }
    });
  }
  let mouse = null;
  $('.videoStarter').mousemove(function (e) { 
    clearInterval(mouse);
    let video = $(this).parent().children('.videoGuid').get(0);
    let videoContoller = $(this).parent().children('.videoContoller');
    if (video.paused || video.ended) {
      $(videoContoller).removeClass('_hidden');
    }else{
      $(videoContoller).removeClass('_hidden');
    mouse = setInterval(function () {
        $(videoContoller).addClass('_hidden');
    }, 1500);
    }
  });
  $('.videoStarter').mouseover(function () { 
    let video = $(this).parent().children('.videoGuid').get(0);
    let videoContoller = $(this).parent().children('.videoContoller');
    if (video.paused || video.ended) {
    } else {
      if ($(videoContoller).hasClass('_hidden')) {
        $(videoContoller).addClass('_hidden');
      }
    }
  });
  function videoStart(parent, video, timer){
    let videoController =  $(parent).children('.videoContoller');
    let videoStarter =  $(parent).children('.videoStarter');
    let play = $(videoController).children('.togglePlay').children(".play");
    let stop = $(videoController).children('.togglePlay').children(".stop");
    $(videoStarter).children('.starterPlay').addClass('_animate');
    setTimeout(() => {
      $(videoStarter).children('.starterPlay').removeClass('_animate');
    }, 500);
    video.play();
    play.hide();
    stop.show();
    let elapsed = $(videoController).children('.videoTime').children(".elapsed");
    let seek = $(videoController).children('.videoProgress').children('.seek');
    timer = setInterval(function () {
      let time = formatTime(Math.round(video.currentTime));
      seek.val(Math.round(video.currentTime));
      let curr = seek.val();
      let min = seek.attr('min');
      let max = seek.attr('max');
      seek.css('background-size', (curr * 100) / max + '% 100%');
      elapsed.html(`${time.minutes}:${time.seconds}`);
      if (video.ended) {
          stop.hide();
          play.show();
          let timer = null;
          clearInterval(timer);
      }
    }, 1000);
    $(parent).children('.videoShadow').fadeOut();
    setTimeout(() => {
      $(videoController).addClass('_hidden');
    }, 1500);
    if ($(parent).children('.videoName').length > 0) {
      setTimeout(() => {
        $(parent).children('.videoName').addClass('_hidden');
      }, 1500);
    }
  }
  function videoStop(parent, video){
    let videoController =  $(parent).children('.videoContoller');
    let videoStarter =  $(parent).children('.videoStarter');
    let play = $(videoController).children('.togglePlay').children(".play");
    let stop = $(videoController).children('.togglePlay').children(".stop");
    $(videoStarter).children('.starterStop').addClass('_animate');
    setTimeout(() => {
      $(videoStarter).children('.starterStop').removeClass('_animate');
    }, 500);
    video.pause();
    stop.hide();
    play.show();
    $(parent).children('.videoShadow').fadeIn();
    $(videoController).removeClass('_hidden');
    setTimeout(() => {
      $(videoController).removeClass('_hidden');
    }, 1500);
    if ($(parent).children('.videoName').length > 0) {
      $(parent).children('.videoName').removeClass('_hidden');
      setTimeout(() => {
        $(parent).children('.videoName').removeClass('_hidden');
      }, 1500);
    }
  }
  $('.volume').click(function (e) { 
    e.preventDefault();
    let value = $(this).val();
    let min = $(this).attr('min');
    let max = $(this).attr('max');
    $(this).css('background-size', (value * 100) / max + '% 100%');
    let video = $(this).parent().parent().prev().get(0);
    video.volume = value;
    $(this).val(value);
  });
  $('.seek').click(function (e) { 
    e.preventDefault();
    let value = $(this).val();
    let min = $(this).attr('min');
    let max = $(this).attr('max');
    $(this).css('background-size', (value * 100) / max + '% 100%');
    let video = $(this).parent().parent().prev().get(0);
    video.currentTime = value;
    $(this).val(value);
  });
  $('.toggleScreen').click(function (e) { 
    e.preventDefault();
    let videoContainer = $(this).parent().parent().get(0);
    let open = $(this).children('.open');
    let close = $(this).children('.close');
    if (document.fullscreenElement) {
      document.exitFullscreen();
      open.show();
      close.hide();
    } else if (document.webkitFullscreenElement) {
      document.webkitExitFullscreen();
      open.show();
      close.hide();
    } else if (videoContainer.webkitRequestFullscreen) {
      videoContainer.webkitRequestFullscreen();
      open.hide();
      close.show();
    } else {
      videoContainer.requestFullscreen();
      open.hide();
      close.show();
    }
  });
  function formatTime(timeInSeconds) {
    let result = new Date(timeInSeconds * 1000).toISOString().substr(11, 8);
    return {
      minutes: result.substr(3, 2),
      seconds: result.substr(6, 2),
    };
  }
// });