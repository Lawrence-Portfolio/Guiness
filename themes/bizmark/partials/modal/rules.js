export default new class Chat {
  constructor() {
    this.closeRules = '#close-rules'
    
    this.init()
  }
  init() {
    $(this.closeRules).on('click', async function() {
      await $('#rules').modal('hide')
      $('html, body').animate({
        scrollTop: $("#live-stream").offset().top  // класс объекта к которому приезжаем
      }, 100); 
    })
  }
}