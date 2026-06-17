<?php

namespace Database\Seeders;

use App\Models\TblBannerAdvertisement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaidAdsSeeder extends Seeder
{
    /**
     * Seed paid banner advertisements for the home page with demo images.
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

        // Remove old sample banners if they exist
        TblBannerAdvertisement::whereIn('web_banner', [
            'banners/sample-banner-1.jpg',
            'banners/sample-banner-2.jpg',
            'banners/sample-banner-3.jpg',
            'banners/demo-banner-1.jpg',
            'banners/demo-banner-2.jpg',
            'banners/demo-banner-3.jpg',
        ])->forceDelete();

        // Create banners directory if it doesn't exist
        Storage::disk('public')->makeDirectory('banners');

        // Download demo banner images
        $bannerImages = [
            'demo-banner-1.jpg' => 'https://placehold.co/1200x300/F97316/ffffff?text=Shop+Now+%E2%80%93+Best+Deals+on+JustReused&font=roboto',
            'demo-banner-2.jpg' => 'https://placehold.co/1200x300/ea580c/ffffff?text=Sell+Your+Items+Fast+%E2%80%93+Post+Free+Ad&font=roboto',
            'demo-banner-3.jpg' => 'https://placehold.co/1200x300/39763a/ffffff?text=Download+JustReused+App+Today&font=roboto',
        ];

        foreach ($bannerImages as $filename => $url) {
            try {
                $response = Http::get($url);
                if ($response->successful()) {
                    Storage::disk('public')->put('banners/' . $filename, $response->body());
                    $this->command->info("Downloaded: {$filename}");
                } else {
                    // Fallback: create a simple colored placeholder
                    $this->createPlaceholderImage($filename);
                }
            } catch (\Exception $e) {
                $this->createPlaceholderImage($filename);
            }
        }

        $banners = [
            [
                'web_banner' => 'banners/demo-banner-1.jpg',
                'app_banner' => 'banners/demo-banner-1.jpg',
                'web_link' => 'https://www.justreused.com',
                'app_link' => 'https://www.justreused.com',
                'page' => 'home',
            ],
            [
                'web_banner' => 'banners/demo-banner-2.jpg',
                'app_banner' => 'banners/demo-banner-2.jpg',
                'web_link' => 'https://www.justreused.com/post/add',
                'app_link' => 'https://www.justreused.com/post/add',
                'page' => 'home',
            ],
            [
                'web_banner' => 'banners/demo-banner-3.jpg',
                'app_banner' => 'banners/demo-banner-3.jpg',
                'web_link' => 'https://apps.apple.com/pk/app/justreused/id6499257286',
                'app_link' => 'https://apps.apple.com/pk/app/justreused/id6499257286',
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

        $this->command->info('Demo banner advertisements seeded successfully!');
    }

    /**
     * Create a simple SVG placeholder if external download fails.
     */
    private function createPlaceholderImage(string $filename): void
    {
        $colors = [
            'demo-banner-1.jpg' => ['bg' => '#F97316', 'text' => 'Shop Now - Best Deals on JustReused'],
            'demo-banner-2.jpg' => ['bg' => '#ea580c', 'text' => 'Sell Your Items Fast - Post Free Ad'],
            'demo-banner-3.jpg' => ['bg' => '#39763a', 'text' => 'Download JustReused App Today'],
        ];

        $color = $colors[$filename] ?? ['bg' => '#F97316', 'text' => 'JustReused'];
        $bg = $color['bg'];
        $text = $color['text'];

        // Create a simple SVG and save as the banner
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="300">
  <rect width="1200" height="300" fill="{$bg}"/>
  <text x="600" y="160" font-family="Arial, sans-serif" font-size="36" font-weight="bold" fill="white" text-anchor="middle">{$text}</text>
</svg>
SVG;

        Storage::disk('public')->put('banners/' . $filename, $svg);
        $this->command->warn("Created placeholder for: {$filename}");
    }
}
