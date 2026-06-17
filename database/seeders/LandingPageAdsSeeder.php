<?php

namespace Database\Seeders;

use App\Models\AdZone;
use App\Models\AdTemplate;
use App\Models\UserAdvertisement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LandingPageAdsSeeder extends Seeder
{
    /**
     * Seed landing page ad zones, templates, and demo advertisements.
     *
     * Run: php artisan db:seed --class=Database\\Seeders\\LandingPageAdsSeeder
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        $now = Carbon::now();
        $startDate = $now->copy()->subDays(5)->format('Y-m-d');
        $endDate = $now->copy()->addDays(60)->format('Y-m-d');

        // ─── AD ZONES FOR LANDING PAGE ───
        $topBannerZone = AdZone::updateOrCreate(
            ['page_location' => 'landing_top_banner'],
            [
                'name' => 'Landing Page – Top Banner',
                'price_per_day' => 5.00,
                'specifications' => json_encode(['width' => 1200, 'height' => 280, 'type' => 'image']),
                'auto_approve' => true,
                'is_active' => true,
            ]
        );

        $midBannerZone = AdZone::updateOrCreate(
            ['page_location' => 'landing_mid_banner'],
            [
                'name' => 'Landing Page – Middle Banner',
                'price_per_day' => 4.00,
                'specifications' => json_encode(['width' => 1200, 'height' => 250, 'type' => 'image']),
                'auto_approve' => true,
                'is_active' => true,
            ]
        );

        $sidebarZone = AdZone::updateOrCreate(
            ['page_location' => 'landing_sidebar'],
            [
                'name' => 'Landing Page – Sidebar / Inline',
                'price_per_day' => 3.00,
                'specifications' => json_encode(['width' => 400, 'height' => 300, 'type' => 'image']),
                'auto_approve' => true,
                'is_active' => true,
            ]
        );

        // ─── AD TEMPLATES ───
        $fullWidthTemplate = AdTemplate::updateOrCreate(
            ['ad_zone_id' => $topBannerZone->id, 'name' => 'Full-Width Hero Banner'],
            [
                'html_content' => '<a href="__LINK__" target="_blank" rel="noopener" style="display:block;width:100%;max-width:__WIDTH__;overflow:hidden;border-radius:16px;text-decoration:none;position:relative;">
    <img src="__IMAGE_URL__" alt="__HEADLINE__" style="width:100%;height:__HEIGHT__;object-fit:cover;border-radius:16px;">
    <div style="position:absolute;bottom:0;left:0;right:0;padding:24px 32px;background:linear-gradient(transparent,rgba(0,0,0,0.75));border-radius:0 0 16px 16px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <img src="__LOGO_URL__" alt="Logo" style="width:36px;height:36px;border-radius:50%;border:2px solid #fff;">
            <span style="color:#fff;font-size:12px;opacity:0.8;">Sponsored</span>
        </div>
        <h3 style="margin:0 0 4px;color:#fff;font-size:22px;font-weight:700;">__HEADLINE__</h3>
        <p style="margin:0 0 12px;color:#ddd;font-size:14px;">__SUBTITLE__</p>
        <span style="background:#F97316;color:#fff;padding:8px 20px;border-radius:8px;font-size:14px;font-weight:600;">__CTA_TEXT__</span>
    </div>
</a>',
                'is_active' => true,
            ]
        );

        $midBannerTemplate = AdTemplate::updateOrCreate(
            ['ad_zone_id' => $midBannerZone->id, 'name' => 'Gradient Card Banner'],
            [
                'html_content' => '<a href="__LINK__" target="_blank" rel="noopener" style="display:flex;align-items:center;width:100%;max-width:__WIDTH__;height:__HEIGHT__;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:16px;overflow:hidden;text-decoration:none;box-shadow:0 4px 20px rgba(102,126,234,0.3);">
    <div style="flex:1;padding:32px 40px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <img src="__LOGO_URL__" alt="Logo" style="width:32px;height:32px;border-radius:50%;">
            <span style="color:rgba(255,255,255,0.7);font-size:11px;text-transform:uppercase;letter-spacing:1px;">Sponsored</span>
        </div>
        <h3 style="margin:0 0 8px;color:#fff;font-size:24px;font-weight:700;">__HEADLINE__</h3>
        <p style="margin:0 0 16px;color:rgba(255,255,255,0.85);font-size:15px;">__SUBTITLE__</p>
        <span style="background:#fff;color:#764ba2;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;">__CTA_TEXT__</span>
    </div>
    <div style="flex:0 0 40%;height:100%;">
        <img src="__IMAGE_URL__" alt="__HEADLINE__" style="width:100%;height:100%;object-fit:cover;">
    </div>
</a>',
                'is_active' => true,
            ]
        );

        $compactTemplate = AdTemplate::updateOrCreate(
            ['ad_zone_id' => $sidebarZone->id, 'name' => 'Compact Card Ad'],
            [
                'html_content' => '<a href="__LINK__" target="_blank" rel="noopener" style="display:block;width:100%;max-width:__WIDTH__;border-radius:12px;overflow:hidden;text-decoration:none;box-shadow:0 2px 12px rgba(0,0,0,0.08);border:1px solid #eee;background:#fff;">
    <img src="__IMAGE_URL__" alt="__HEADLINE__" style="width:100%;height:180px;object-fit:cover;">
    <div style="padding:16px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <img src="__LOGO_URL__" alt="Logo" style="width:24px;height:24px;border-radius:50%;">
            <span style="color:#999;font-size:11px;">Ad</span>
        </div>
        <h4 style="margin:0 0 4px;color:#1a1a1a;font-size:16px;font-weight:600;">__HEADLINE__</h4>
        <p style="margin:0 0 12px;color:#666;font-size:13px;">__SUBTITLE__</p>
        <span style="background:#F97316;color:#fff;padding:6px 16px;border-radius:6px;font-size:13px;font-weight:500;">__CTA_TEXT__</span>
    </div>
</a>',
                'is_active' => true,
            ]
        );

        // ─── GENERATE LOCAL BANNER IMAGES ───
        Storage::disk('public')->makeDirectory('user_ads');
        Storage::disk('public')->makeDirectory('user_ad_logos');

        $bannerImages = [
            'ad-summer-sale.svg' => ['bg' => '#F97316', 'grad' => '#fb923c', 'text' => '☀ SUMMER SALE', 'sub' => 'Up to 70% Off — Electronics, Fashion & More', 'w' => 1200, 'h' => 400],
            'ad-sell-gadgets.svg' => ['bg' => '#10b981', 'grad' => '#34d399', 'text' => '📱 Sell Your Gadgets', 'sub' => 'Post your ad in 60 seconds — Totally Free!', 'w' => 1200, 'h' => 400],
            'ad-premium.svg' => ['bg' => '#764ba2', 'grad' => '#667eea', 'text' => '⭐ Premium Membership', 'sub' => 'Verified Badge • Priority Listing • Instant Chat', 'w' => 800, 'h' => 400],
            'ad-download-app.svg' => ['bg' => '#3b82f6', 'grad' => '#6366f1', 'text' => '📲 Get the App', 'sub' => 'Buy & Sell on the Go — iOS & Android', 'w' => 800, 'h' => 400],
            'ad-iphone-deal.svg' => ['bg' => '#ef4444', 'grad' => '#f97316', 'text' => '🔥 iPhone 15 Pro', 'sub' => 'Certified Refurbished — Save 40%!', 'w' => 600, 'h' => 400],
            'ad-furniture.svg' => ['bg' => '#059669', 'grad' => '#10b981', 'text' => '🛋 Furniture Sale', 'sub' => 'Quality Pre-Owned — Local Pickup Available', 'w' => 600, 'h' => 400],
        ];

        foreach ($bannerImages as $filename => $data) {
            $svg = $this->generateBannerSvg($data['bg'], $data['grad'], $data['text'], $data['sub'], $data['w'], $data['h']);
            Storage::disk('public')->put('user_ads/' . $filename, $svg);
        }

        $logoImages = [
            'logo-jr.svg' => ['bg' => '#F97316', 'text' => 'JR'],
            'logo-sell.svg' => ['bg' => '#10b981', 'text' => 'S'],
            'logo-pro.svg' => ['bg' => '#764ba2', 'text' => 'P'],
            'logo-app.svg' => ['bg' => '#3b82f6', 'text' => 'A'],
            'logo-deal.svg' => ['bg' => '#ef4444', 'text' => 'D'],
            'logo-home.svg' => ['bg' => '#059669', 'text' => 'H'],
        ];

        foreach ($logoImages as $filename => $data) {
            $svg = $this->generateLogoSvg($data['bg'], $data['text']);
            Storage::disk('public')->put('user_ad_logos/' . $filename, $svg);
        }

        // ─── DEMO ADS ───
        $demoAds = [
            [
                'ad_zone_id' => $topBannerZone->id,
                'ad_template_id' => $fullWidthTemplate->id,
                'content' => [
                    'headline' => 'Summer Sale – Up to 70% Off!',
                    'subtitle' => 'Grab the best deals on electronics, fashion & more. Limited time offer!',
                    'link' => 'https://www.justreused.com',
                    'cta_text' => 'Shop Now',
                    'image' => 'user_ads/ad-summer-sale.svg',
                    'logo' => 'user_ad_logos/logo-jr.svg',
                ],
            ],
            [
                'ad_zone_id' => $topBannerZone->id,
                'ad_template_id' => $fullWidthTemplate->id,
                'content' => [
                    'headline' => 'Sell Your Old Gadgets Today',
                    'subtitle' => 'Turn unused items into cash. Post your ad in 60 seconds — totally free!',
                    'link' => 'https://www.justreused.com/post-add',
                    'cta_text' => 'Post Free Ad',
                    'image' => 'user_ads/ad-sell-gadgets.svg',
                    'logo' => 'user_ad_logos/logo-sell.svg',
                ],
            ],
            [
                'ad_zone_id' => $midBannerZone->id,
                'ad_template_id' => $midBannerTemplate->id,
                'content' => [
                    'headline' => 'Premium Membership',
                    'subtitle' => 'Get verified badge, priority listing & instant chat with buyers.',
                    'link' => 'https://www.justreused.com/selectPackage',
                    'cta_text' => 'Upgrade Now',
                    'image' => 'user_ads/ad-premium.svg',
                    'logo' => 'user_ad_logos/logo-pro.svg',
                ],
            ],
            [
                'ad_zone_id' => $midBannerZone->id,
                'ad_template_id' => $midBannerTemplate->id,
                'content' => [
                    'headline' => 'Download the App',
                    'subtitle' => 'Chat, buy & sell on the go. Available on iOS and Android.',
                    'link' => 'https://apps.apple.com/pk/app/justreused/id6499257286',
                    'cta_text' => 'Get the App',
                    'image' => 'user_ads/ad-download-app.svg',
                    'logo' => 'user_ad_logos/logo-app.svg',
                ],
            ],
            [
                'ad_zone_id' => $sidebarZone->id,
                'ad_template_id' => $compactTemplate->id,
                'content' => [
                    'headline' => 'iPhone 15 Pro – Like New',
                    'subtitle' => 'Certified refurbished with warranty. Save 40% vs retail!',
                    'link' => 'https://www.justreused.com',
                    'cta_text' => 'View Deal',
                    'image' => 'user_ads/ad-iphone-deal.svg',
                    'logo' => 'user_ad_logos/logo-deal.svg',
                ],
            ],
            [
                'ad_zone_id' => $sidebarZone->id,
                'ad_template_id' => $compactTemplate->id,
                'content' => [
                    'headline' => 'Furniture Clearance',
                    'subtitle' => 'Quality pre-owned sofas, tables & more. Local pickup available.',
                    'link' => 'https://www.justreused.com',
                    'cta_text' => 'Browse Now',
                    'image' => 'user_ads/ad-furniture.svg',
                    'logo' => 'user_ad_logos/logo-home.svg',
                ],
            ],
        ];

        // Remove old demo ads for these zones
        UserAdvertisement::where('status', 'approved')
            ->whereIn('ad_zone_id', [$topBannerZone->id, $midBannerZone->id, $sidebarZone->id])
            ->where('total_amount', 0)
            ->delete();

        foreach ($demoAds as $ad) {
            UserAdvertisement::create([
                'user_id' => $user->id,
                'ad_zone_id' => $ad['ad_zone_id'],
                'ad_template_id' => $ad['ad_template_id'],
                'content' => $ad['content'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_amount' => 0,
                'payment_status' => 'paid',
                'status' => 'approved',
            ]);
        }

        $this->command->info('Landing page ad zones, templates, and demo ads seeded successfully!');
        $this->command->info('Images stored in: storage/app/public/user_ads/ and user_ad_logos/');
    }

    /**
     * Generate a professional-looking SVG banner image.
     */
    private function generateBannerSvg(string $bg, string $grad, string $text, string $sub, int $w, int $h): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bg}"/>
      <stop offset="100%" style="stop-color:{$grad}"/>
    </linearGradient>
  </defs>
  <rect width="{$w}" height="{$h}" fill="url(#bg)" rx="16"/>
  <circle cx="100" cy="80" r="120" fill="rgba(255,255,255,0.05)"/>
  <circle cx="{$w}" cy="{$h}" r="200" fill="rgba(255,255,255,0.04)"/>
  <text x="50%" y="42%" font-family="system-ui,-apple-system,sans-serif" font-size="42" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">{$text}</text>
  <text x="50%" y="62%" font-family="system-ui,-apple-system,sans-serif" font-size="20" fill="rgba(255,255,255,0.9)" text-anchor="middle" dominant-baseline="middle">{$sub}</text>
</svg>
SVG;
    }

    /**
     * Generate a simple circular logo SVG.
     */
    private function generateLogoSvg(string $bg, string $text): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80">
  <circle cx="40" cy="40" r="40" fill="{$bg}"/>
  <text x="50%" y="54%" font-family="system-ui,-apple-system,sans-serif" font-size="28" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">{$text}</text>
</svg>
SVG;
    }
}
