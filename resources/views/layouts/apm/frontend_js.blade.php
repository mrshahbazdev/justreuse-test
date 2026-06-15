<?php 
    $dir_rtl =  App\Models\Setting::is_dir_rtl();
    echo "<script>var dir_rtl = ".$dir_rtl."</script>";
?>
<script src="{{ URL::to('js/jquery.min.js') }}"></script>
