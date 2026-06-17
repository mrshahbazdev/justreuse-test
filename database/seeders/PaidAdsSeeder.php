<?php

namespace Database\Seeders;

use App\Models\TblBannerAdvertisement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaidAdsSeeder extends Seeder
{
    /**
     * Seed paid banner advertisements for the home page.
     *
     * Run: php artisan db:seed --class=Database\\Seeders\\PaidAdsSeeder
     */
    public function run(): void
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subDays(5);
        $endDate = $now->copy()->addDays(30);

        // Use the first available user as the banner owner
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found in database. Please create a user first.');
            return;
        }
        $userId = $user->id;

        $banners = [
            [
                'web_banner' => 'banners/sample-banner-1.jpg',
                'app_banner' => 'banners/sample-banner-1.jpg',
                'web_link' => 'https://www.justreused.com',
                'app_link' => 'https://www.justreused.com',
                'page' => 'home',
            ],
            [
                'web_banner' => 'banners/sample-banner-2.jpg',
                'app_banner' => 'banners/sample-banner-2.jpg',
                'web_link' => 'https://www.justreused.com',
                'app_link' => 'https://www.justreused.com',
                'page' => 'home',
            ],
            [
                'web_banner' => 'banners/sample-banner-3.jpg',
                'app_banner' => 'banners/sample-banner-3.jpg',
                'web_link' => 'https://www.justreused.com',
                'app_link' => 'https://www.justreused.com',
                'page' => 'home',
            ],
        ];

        foreach ($banners as $banner) {
            TblBannerAdvertisement::create([
                'user_id' => $userId,
                'category_id' => null,
                'page' => $banner['page'],
                'web_banner' => $banner['web_banner'],
                'app_banner' => $banner['app_banner'],
                'web_link' => $banner['web_link'],
                'app_link' => $banner['app_link'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'live_days' => 35,
                'total_amount' => 0,
                'status' => 'approved',
                'active' => 1,
                'payment_status' => 'paid',
            ]);
        }

        $this->command->info('Paid banner advertisements seeded successfully!');
    }
}
