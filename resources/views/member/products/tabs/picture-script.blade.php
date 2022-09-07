<?php $data = $product->pictureObj; ?>
<link rel="stylesheet" href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.css') }}">
<link rel="stylesheet"
      href="{{ asset('plugins/bower_components/ion-rangeslider/css/ion.rangeSlider.skinModern.css') }}">
<script src="{{ asset('plugins/bower_components/ion-rangeslider/js/ion-rangeSlider/ion.rangeSlider.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.1.0/fabric.js"></script>
<script>
  var canvasElement = $(".canvas");
  var canvas;
  var imgObject;
  var isCanvasOn = false;

  const initPicture = () => {
    $("#range_brightness").ionRangeSlider({
        grid: false,
        min: 0,
        max: 100,
        from: 50,
        onChange: (e) => controlBrightness(e)
    });
    $("#range_contrast").ionRangeSlider({
        grid: false,
        min: 0,
        max: 100,
        from: 50,
        onChange: (e) => controlContrast(e)
    });
  }
  
  const resetCancas = () => {
    console.log("canvasElement.width()", canvasElement.width());
    canvasElement.html('<canvas id="canvas"></canvas>');
    btnOverlay.removeClass("hide");
    canvasElement.addClass("hide");

    canvas = new fabric.Canvas('canvas');
    canvas.setWidth(canvasElement.width());
    canvas.setHeight(450);
    isCanvasOn = false;
  }

  var btnOverlay = $(".btn-overlay");
  $("#input-file").change(function(e) {
    var reader = new FileReader();
    reader.onload = function(event) {
      canvas.setWidth(canvasElement.width());
      var imgObj = new Image();
      imgObj.src = event.target.result;
      imgObj.onload = function() {
        const image = new fabric.Image(imgObj);
        image.set({
          angle: 0,
          padding: 10,
          cornersize: 10
        });
        //image.scaleToWidth(538);
        image.scaleToHeight(450);
        
        canvas.centerObject(image);
        canvas.add(image);
        
        var filter = new fabric.Image.filters.Brightness({
          brightness: 0,
        });
        image.filters.push(filter);

        filter = new fabric.Image.filters.Contrast({
          contrast: 0,
        });
        image.filters.push(filter);

        image.applyFilters();
        canvas.renderAll();
        imgObject = image;
      }
    }
    reader.readAsDataURL(e.target.files[0]);
    btnOverlay.addClass("hide");
    canvasElement.removeClass("hide");
    isCanvasOn = true;
  });

  $("#btn-canvas-reset").click(function() {
    resetCancas();
  });

  const controlBrightness = ({ from: value }) => {
    if (!isCanvasOn) return;

    const obj = imgObject;
    obj.filters [0].brightness = (value - 50) * 0.02
    obj.applyFilters();
    canvas.renderAll();
  }

  const controlContrast = ({ from: value }) => {
    if (!isCanvasOn) return;

    const obj = imgObject;
    obj.filters [1].contrast = (value - 50) * 0.02
    obj.applyFilters();
    canvas.renderAll();
  }

  initPicture();
  resetCancas();

  $("#btn-save").click((e) => {
    if (!isCanvasOn) return;
    this.href = canvas.toDataURL({
      format: 'jpeg',
      quality: 0.8
    });
    this.download = 'image.jpg'
  });

  $("#btn-upload").click((e) => {
    var url = "url/action";
    if (!isCanvasOn) return;
    var base64ImageContent = canvas.toDataURL({
      format: 'jpeg',
      quality: 0.8
    });
    base64ImageContent = base64ImageContent.replace(/^data:image\/(png|jpg|jpeg);base64,/, "");
    var blob = base64ToBlob(base64ImageContent, 'image/jpg');
    var formData = new FormData();
    formData.append('image', blob);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{route('member.products.uploadImage', [$product->id])}}",
        type: "POST", 
        cache: false,
        contentType: false,
        processData: false,
        data: formData
      })
    .done(function(e){
      toastr.success("@lang('app.product.picture.imageUploaded')");
      setImageList(JSON.parse(e));
    });
  });

  function base64ToBlob(base64, mime) 
  {
    mime = mime || '';
    var sliceSize = 1024;
    var byteChars = window.atob(base64);
    var byteArrays = [];

    for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
      var slice = byteChars.slice(offset, offset + sliceSize);

      var byteNumbers = new Array(slice.length);
      for (var i = 0; i < slice.length; i++) {
        byteNumbers[i] = slice.charCodeAt(i);
      }

      var byteArray = new Uint8Array(byteNumbers);

      byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, {type: mime});
  }

  function onRemove({ target }, fileName) {
    var formData = new FormData();
    formData.append('fileName', fileName);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{route('member.products.removeImage', [$product->id])}}",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: formData
      })
    .done(function(e){
      setImageList(JSON.parse(e));
    });
  }

  function setImageList(images) {
    console.log("ImageList", images);
    $(".image-list").html('');
    for (var index in images) {
      const fileName = images [index];
      const html = `<div class="image-item">
            <img src="{{ asset('user-uploads/products/'.$product->id) }}/${fileName}">
            <button class="btn btn-icon" onclick="onRemove(event, '${fileName}')">
              <i class="fa fa-trash"> </i>
            </button>
          </div>`;
      $(".image-list").append(html);
    }
  }
  

  setImageList(<?=json_encode($data)?>);
</script>