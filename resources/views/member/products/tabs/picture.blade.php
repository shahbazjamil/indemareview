<?php $data = $product->pictureObj; ?>
<!--{!! Form::open(['id'=>'createProduct','class'=>'ajax-form','method'=>'POST']) !!}-->
<input type="hidden" name="tabId" value="picture">
<div class="form-body">
  <div class="row">
    <div class="col-md-4">
      <div class="image-editor">
        <div class="canvas hide">
          <canvas id="canvas"></canvas>
        </div>
        <label class="btn-overlay" for="input-file">
          <h1>+</h1>
          <span>@lang('app.product.picture.addPicture')</span>
          <p class="description">@lang('app.product.picture.addPictureDescription')</p>
        </label>
        <input type="file" id="input-file" class="hide">
      </div>
    </div>

    <div class="col-md-4">
      <div class="form-group">
        <label class="control-label">@lang('app.product.picture.brightness')</label>
        <div id="range_brightness"></div>
      </div>
      <div class="form-group">
        <label class="control-label">@lang('app.product.picture.contrast')</label>
        <div id="range_contrast"></div>
      </div>

        <div class="form-actions-new">
        <button class="btn btn-default" id="btn-upload">
          <i class="fa fa-upload"> </i> @lang('app.product.picture.upload')</button>
        <!-- <button class="btn default" id="btn-scan">
          <i class="fa fa-camera"> </i> @lang('app.product.picture.scan')</button> -->
        <!-- <button class="btn default" id="btn-scan">
          <i class="fa fa-open"> </i> @lang('app.product.picture.load')</button> -->
<!--        <a class="btn btn-default" id="btn-save">
          <i class="fa fa-save"> </i> @lang('app.product.picture.saveAs')</a>-->
      </div>
    </div>

    <div class="col-md-4 text-right">
      <button class="btn btn-icon default" id="btn-canvas-reset">
        <i class="fa fa-refresh"> </i>
      </button>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="image-list">
      </div>
    </div>
  </div>
</div>
<!--{!! Form::close() !!}-->