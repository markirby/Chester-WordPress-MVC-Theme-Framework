/*jshint bitwise:true, curly:true, eqeqeq:true, forin:true, immed:false, latedef:true, newcap:true, noarg:true, noempty:true, nonew:true, undef:true, strict:false, trailing:true, 
  browser:true, jquery:true */
/*global tb_remove, tb_show, console */
/*!
 * jQuery wordpress image upload jquery plugin
 * Original author: @markirby
 * Licensed under the MIT license
 */
 
;(function ($, window, document, undefined) {
  var wpImageUpload = 'wpImageUpload',
    defaults = {
      imageUploadButtonClass: '.image_upload_button',
      imageRemoveButtonClass: '.image_remove_button',
      imageInputElementClass: '.image_input_value',
      supportsMultipleFieldInstances: false,
      supportsVimeo: false
    };
    
  function WPImageUpload(element, options) {
    this.element = element;
    this.$element = $(element);
    this.options = $.extend({}, defaults, options);
    
    this._defaults = defaults;
    this._name = wpImageUpload;
    this.init();
  }
  
  WPImageUpload.prototype.init = function() {
    this.addEventListeners();
  };
  
  WPImageUpload.prototype.addEventListeners = function() {
    this.addEventListenerToUploadButtons();
    this.addEventListenerToRemoveButtons();
    this.addEventListenerToNewFieldCreated();
  };
  
  WPImageUpload.prototype.addEventListenerToUploadButtons = function() {
    var self = this;
    this.$element.find(this.options.imageUploadButtonClass).each(function() {
      $(this).unbind('click.wpImageUpload');
      $(this).bind('click.wpImageUpload', function(event) {
        event.stopPropagation();
        event.preventDefault();
        self.showImageUploadDialog($(this).parent());
      });
      
    });
  };
  
  WPImageUpload.prototype.addEventListenerToRemoveButtons = function() {
    var self = this;
    this.$element.find(this.options.imageRemoveButtonClass).each(function() {
      $(this).unbind('click.wpImageRemove');
      $(this).bind('click.wpImageRemove', function(event) {
          event.stopPropagation();
          event.preventDefault();
          self.removeImage($(this).parent());
      });
    });
  };
  
  WPImageUpload.prototype.addEventListenerToNewFieldCreated = function() {
    if (!this.options.supportsMultipleFieldInstances) {
      return;
    }
    var self = this;
    
    this.$element.parent().bind("DOMSubtreeModified", {self:this}, function(event) {
      self.addEventListenerToUploadButtons();
      self.addEventListenerToRemoveButtons();
      
    });
  };
  
  WPImageUpload.prototype.showImageUploadDialog = function($parentElement) {
    var self = this;
    var
      $uploadImageButton = $parentElement.find(this.options.imageUploadButtonClass),
      $removeImageButton = $parentElement.find(this.options.imageRemoveButtonClass),
      $inputElement = $parentElement.find(this.options.imageInputElementClass),
      $imgElement = $parentElement.find('img'),
      $videoElement = $parentElement.find('iframe');

    window.send_to_editor = function(html) {
      var imageURL = $('img', html).attr('src');
      
      var videoURL = "";
      if (self.options.supportsVimeo && !imageURL) {
        videoURL = $(html).attr('href');
      }
      
      if (imageURL) {
        $inputElement.val(imageURL);
        self.setImageURL($imgElement, imageURL);
        self.emptyVideo($videoElement);
      } else if (self.options.supportsVimeo && videoURL) {
        $inputElement.val(videoURL);
        self.setVideoURL($videoElement, videoURL);
        self.emptyImage($imgElement);
      }

      $uploadImageButton.val('Reupload');
      $removeImageButton.removeClass('hidden');
      
      tb_remove();
    };
    tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
  };
  
  WPImageUpload.prototype.removeImage = function($parentElement) {
    var
      $uploadImageButton = $parentElement.find(this.options.imageUploadButtonClass),
      $removeImageButton = $parentElement.find(this.options.imageRemoveButtonClass),
      $inputElement = $parentElement.find(this.options.imageInputElementClass),
      $imgElement = $parentElement.find('img'),
      $videoElement = $parentElement.find('iframe');
    
    $inputElement.val("");
    this.emptyImage($imgElement);
    this.emptyVideo($videoElement);
    $uploadImageButton.val('Upload');
    $removeImageButton.addClass('hidden');
  };
  
  WPImageUpload.prototype.emptyImage = function($imageElement) {
    $imageElement.addClass('hidden');
    $imageElement.attr("src", "data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==");
  };
  
  WPImageUpload.prototype.emptyVideo = function($videoElement, URL) {
    $videoElement.attr("src", "");
    $videoElement.addClass('hidden');
  };
  
  WPImageUpload.prototype.setImageURL = function($imageElement, URL) {
    $imageElement.attr('src', URL);
    $imageElement.removeClass('hidden');
  };
  
  WPImageUpload.prototype.setVideoURL = function($videoElement, URL) {
    $videoElement.attr('src', URL);
    $videoElement.removeClass('hidden');
  };
  
  $.fn[wpImageUpload] = function ( options ) {
    return this.each(function () {
      if (!$.data(this, 'plugin_' + wpImageUpload)) {
        $.data(this, 'plugin_' + wpImageUpload,
        new WPImageUpload( this, options ));
      }
    });
  };
  
})( jQuery, window, document );