$(() => {
    "use strict";
    require('../partials/chat/chat')
    require('../partials/chat/messaging')
    require('../partials/modal/rules')
})
$('.go-live-stream').on('click', function() {
    $('html, body').animate({
      scrollTop: $("#live-stream").offset().top  // класс объекта к которому приезжаем
    }, 100); 
})
