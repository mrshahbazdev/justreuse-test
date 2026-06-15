<?php
if(!isset($_GET['token']) || $_GET['token']=="")
{
    $arr = array("success"=>false,"userdata"=>"");
}
else{
    $token = $_GET['token'];
    if(isset(explode('.', $token)[1])){
        $result =  json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
        $decoded = is_null($result)?"":json_encode($result);
        $arr = array("success"=>true,"userdata"=>$decoded);
    }
    else
    {
        $arr = array("success"=>true,"userdata"=>"");
    }
}
echo json_encode($arr);
?>