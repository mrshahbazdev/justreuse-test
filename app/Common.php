<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class Common extends Model
{

    public static function get_currency_code($curr_id) {
        if($curr_id=="")
        {
            $data = DB::table('settings')->where('key', 'app')->get()->first();
            $json_val = json_decode($data->value);
            $curr_id = $json_val->default_currency;
        }

        $r = DB::table('tbl_currencies')->where('id',$curr_id)->get()->first();
        $array_data = array("currency_id"=>$r->id,"currency_hex"=>$r->currency_hex,"currency_code"=>$r->short_code);

        return $array_data;
    }

    
  
}