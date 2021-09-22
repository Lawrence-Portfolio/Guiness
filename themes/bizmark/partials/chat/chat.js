export default new class Chat {
  constructor() {
    this.uploadImgBtn = '#upload-image-btn'
    this.uploadImgInput = '#upload-image-input'
    this.uploadText = '#upload-text'
    this.previewImage = '#preview-image'
    this.uploadIcon = '.upload-icon'
    this.fileSizeInvalid =  '.file-size-invalid'
    this.init()
  }
  init() {
    let input = this.uploadImgInput
    $(this.uploadImgBtn).on('click', function() {
      $(input).click()
    })

    let uploadText = this.uploadText
    let uploadIcon = this.uploadIcon
    let fileSizeInvalid = this.fileSizeInvalid

    $(this.uploadImgInput).change(function(e) {
      const maxSizeFiles = 10485760
      const uploadedFiles = $(e.target.files);

      let uploadedSizeFiles = 0;
      let files = []
      for( let i = 0; i < uploadedFiles.length; i++ ){
        files.push( uploadedFiles[i] );
        uploadedSizeFiles = uploadedSizeFiles + uploadedFiles[i].size
      }
      if(maxSizeFiles > uploadedSizeFiles) {
        files.forEach(file => {
          if(/\.(jpg|jpeg|png)$/i.test(file.name)) {
            $(uploadText).html(`${files.length} image(s)`)
            
          } else {
            $(uploadText).html('Upload a photo')
            files = []
            errorMessage('Picture format: .jpg, .jpeg, .png')
          }
        })
      } else {
        $(uploadText).html('Upload a photo')
        files = []
        errorMessage('Files should not be larger than 10 MB!') 
      }
    })
    // function getImagePreviews(files) {
    //   files.forEach(file => {
    //     if(/\.(jpg|jpeg|png)$/i.test(file.name)) {
    //       console.log(file)
    //       let reader = new FileReader
    //       let previewImageUrls = []

    //       reader.addEventListener('load', function (e) {
    //         console.log(e)
    //       })
    //       reader.readAsDataURL(file)
    //     } else {
    //       errorMessage('Picture format: .jpg, .jpeg, .png') 
    //     }
    //   });
    // }
    function get_extension(filename) {
      return filename.slice((filename.lastIndexOf('.') - 1 >>> 0) + 2);
    }
    function errorMessage(message) {
      $(fileSizeInvalid).html(message).css({
        'display': 'block'
      })

      setTimeout(function () {  
        $(fileSizeInvalid).html('').css({
          'display': 'none'
        })
      }, 5000)
    }
  }
}