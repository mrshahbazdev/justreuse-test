<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      
      	if (request()->is('/')) {
            Cache::remember('home_page_ads', 3600, function () {
                // Pre-load home page ads
            });
        }
        date_default_timezone_set('Asia/Kolkata');

        //page apm's
        add_action('apm_before_main_content', function(){ echo ""; },20,1);

        add_action('apm_header_css', function($str){ 
            if($str=="other"){ $this->head_other_css(); }
            else{ $this->head_def_css(); }
        },20,1);
        add_action('apm_header_enqueue', function($str){ 
            if($str=="other"){ $this->head_other_js(); }
            else{ $this->head_def_js(); } 
        },20,1);


        add_action('apm_header', function(){ $this->head_def(); },20,1);
        add_action('apm_top_header', function(){ echo ""; },20,1);

        add_action('apm_top_nav', function($str){
            if($str=="other"){ $this->head_nav_category(); }
            else{ echo ""; }
         },20,1);

        add_action('apm_banner', function(){ echo ""; },20,1);
        add_action('apm_before_main', function(){ echo ""; },20,1);
        add_action('apm_main', function(){ echo ""; },20,1);
        add_action('apm_sidebar', function(){ echo ""; },20,1);
        add_action('apm_after_main', function(){ echo ""; },20,1);
        add_action('apm_top_footer', function(){ echo ""; },20,1);
        add_action('apm_footer', function(){ $this->footer_def(); },20,1);
        add_action('apm_footer_bottom', function(){ echo ""; },20,1);
    }


    public function head_def(){ echo view('layouts.apm.frontend_search_language_bar')->render(); }
    public function footer_def(){ echo view('layouts.apm.frontend_footer')->render(); }
    public function head_def_js() { echo view('layouts.apm.frontend_js')->render(); }
    public function head_other_js(){ echo view('layouts.apm.frontend_other_js')->render(); }
    public function head_def_css(){ echo view('layouts.apm.frontend_css')->render(); }
    public function head_other_css(){ echo view('layouts.apm.frontend_other_css')->render(); }
    public function head_nav_category() { echo view('layouts.apm.frontend_other_nav_category')->render(); }

}