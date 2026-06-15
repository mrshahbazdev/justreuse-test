<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Languages;

class TblNotifications extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['from_id','to_id','msg','read_status','deleted_at','post_id','notify_from','slug'];    
  
   public static function translate($locale, $originalText, $placeholders = [])
    {
        $translatedText = Languages::where('lang_code', $locale)
            ->where('lang_org_text', $originalText)
            ->value('lang_text');

        if (!$translatedText) {
            // If no translation is found, use the original text
            $translatedText = $originalText;
        }

        // Replace placeholders in the translated text
        foreach ($placeholders as $key => $value) {
            $translatedText = str_replace(":{$key}", $value, $translatedText);
        }

        return $translatedText;
    }
}
