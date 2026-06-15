<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use App\Models\Languagecode;
class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $language = request()->header('Accept-Language');
        $country = substr($language, 2);
        $languages = explode(',', $language);
        foreach ($languages as $language) {
            if (strpos($language, '-') !== false) {
                $parts = explode('-', $language);
                $countrycode = $parts[1];
            }
        }
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            $countryCode = $parts[1] ?? '';
            $get_current_country = Languagecode::where('country_code',$countryCode)->first();
            $lang_code = !empty($get_current_country['language_code']) ? $get_current_country['language_code'] : 'en';
            // Set the locale based on the country code
            switch ($countryCode) {
                case $countryCode:
                    App::setLocale($lang_code);
                    break;
                default:
                    App::setLocale('en'); // Default to 'en'
            }
        }
        return $next($request);
    }
}
