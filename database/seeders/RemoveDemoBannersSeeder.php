<?php

namespace Database\Seeders;

use App\Models\TblBannerAdvertisement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RemoveDemoBannersSeeder extends Seeder
{
    /**
     * Remove the demo banner advertisements created by PaidAdsSeeder.
     *
     * Run: php artisan db:seed --class=Database\\Seeders\\RemoveDemoBannersSeeder
     */
    public function run(): void
    {
        $demoBanners = [
            'banners/demo-banner-1.jpg',
            'banners/demo-banner-2.jpg',
            'banners/demo-banner-3.jpg',
            'banners/sample-banner-1.jpg',
            'banners/sample-banner-2.jpg',
            'banners/sample-banner-3.jpg',
        ];

        $deleted = TblBannerAdvertisement::whereIn('web_banner', $demoBanners)->delete();

        // Also remove the files from storage
        foreach ($demoBanners as $banner) {
            if (Storage::disk('public')->exists($banner)) {
                Storage::disk('public')->delete($banner);
            }
        }

        $this->command->info("Removed {$deleted} demo banner advertisement(s) from database.");
    }
}
