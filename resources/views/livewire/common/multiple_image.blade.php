<link rel="stylesheet" href="{{ URL::to('css/image-uploader.min.css') }}">
<script src="{{ URL::to('js/image-uploader.min.js') }}"></script>

<!-- <div class="input-images"></div> -->
<input type="hidden" class="img_index" name="selected-img-index" />
<?php
$img_limit = App\Models\Setting::get_image_size_settings();
?>
<script>
  $('.input-images').imageUploader({
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxFiles: <?php echo !empty($img_limit['max_image_limit']) ? $img_limit['max_image_limit'] : 5 ?>,
  });
</script>
