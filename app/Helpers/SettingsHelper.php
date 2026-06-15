<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

if (!function_exists('getWatermarkPath')) {
    function getWatermarkPath()
    {
        Log::info('📍 getWatermarkPath() called');
        
        try {
            $settings = DB::table('settings')->where('key', 'app')->first();
            
            if (!$settings) {
                Log::error('Settings table mein "app" key nahi mili');
                return null;
            }
            
            Log::info('Settings found');
            
            $settingsValue = json_decode($settings->value, true);
            Log::info('Settings decoded: ' . json_encode($settingsValue));
            
            if (empty($settingsValue['app_watermark'])) {
                Log::error('app_watermark field empty hai');
                return null;
            }
            
            $relativePath = $settingsValue['app_watermark'];
            Log::info('Watermark relative path: ' . $relativePath);
            
            // Path construct karein
            $watermarkPath = public_path('storage/' . $relativePath);
            Log::info('Full watermark path: ' . $watermarkPath);
            
            if (file_exists($watermarkPath)) {
                Log::info('✅ Watermark file EXISTS');
                Log::info('File size: ' . filesize($watermarkPath) . ' bytes');
                return $watermarkPath;
            } else {
                Log::error('❌ Watermark file DOES NOT EXIST at: ' . $watermarkPath);
                
                // Alternative path
                $altPath = public_path($relativePath);
                Log::info('Trying alternative path: ' . $altPath);
                if (file_exists($altPath)) {
                    Log::info('✅ Found at alternative path');
                    return $altPath;
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('getWatermarkPath() exception: ' . $e->getMessage());
            return null;
        }
    }
}