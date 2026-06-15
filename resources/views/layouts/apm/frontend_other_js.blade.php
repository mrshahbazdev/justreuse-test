<?php 
    $dir_rtl =  App\Models\Setting::is_dir_rtl();
    echo "<script>var dir_rtl = ".$dir_rtl."</script>";
?>
<script src="{{ URL::to('js/jquery.min.js') }}"></script>
<script src="{{ URL::to('js/jquery-ui.js') }}"></script>
<script src="{{ URL::to('js/tailwindcss-stimulus-components.umd.js') }}"></script>
<script src="{{ URL::to('js/slick.min.js') }}"></script>
<script src="{{ URL::to('js/slick-lightbox.min.js') }}"></script>
<script src="{{ URL::to('js/owl.carousel.js') }}"></script>
<script src="{{ URL::to('js/alpine.min.js') }}" defer></script>
<script src="{{ URL::to('js/toastr.min.js') }}"></script>
<script src="{{ URL::to('js/jquery.hoverIntent.js') }}"></script>
<!-- <script src="{{ URL::to('js/stimulus.umd.js') }}"></script> -->
 
<script src="{{ URL::to('js/tailwind_popper.js') }}" defer></script>

@include('livewire.common.seo')
@include('livewire.common.location_default')