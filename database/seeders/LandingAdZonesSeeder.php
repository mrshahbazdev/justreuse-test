<?php

namespace Database\Seeders;

use App\Models\AdZone;
use App\Models\AdTemplate;
use Illuminate\Database\Seeder;

class LandingAdZonesSeeder extends Seeder
{
    /**
     * Create ad zones and templates for the landing page.
     *
     * Run: php artisan db:seed --class=Database\\Seeders\\LandingAdZonesSeeder
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Landing Page – Top Banner',
                'page_location' => 'landing_top_banner',
                'price_per_day' => 5.00,
                'specifications' => ['width' => 1200, 'height' => 200, 'type' => 'image'],
            ],
            [
                'name' => 'Landing Page – Middle Banner',
                'page_location' => 'landing_mid_banner',
                'price_per_day' => 4.00,
                'specifications' => ['width' => 1200, 'height' => 200, 'type' => 'image'],
            ],
            [
                'name' => 'Landing Page – Sidebar / Inline',
                'page_location' => 'landing_sidebar',
                'price_per_day' => 3.00,
                'specifications' => ['width' => 400, 'height' => 300, 'type' => 'image'],
            ],
        ];

        $bannerTemplateHtml = <<<'HTML'
<a href="__LINK__" target="_blank" rel="noopener" style="display:block;border-radius:14px;overflow:hidden;text-decoration:none;max-width:__WIDTH__;margin:0 auto;">
    <div style="position:relative;width:100%;height:__HEIGHT__;background:#f8f9fa;border-radius:14px;overflow:hidden;">
        <img src="__IMAGE_URL__" alt="__HEADLINE__" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
        <div style="position:absolute;bottom:0;left:0;right:0;padding:16px 20px;background:linear-gradient(transparent,rgba(0,0,0,0.7));">
            <div style="display:flex;align-items:center;gap:10px;">
                <img src="__LOGO_URL__" alt="Logo" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:2px solid rgba(255,255,255,0.3);">
                <div>
                    <div style="color:#fff;font-size:16px;font-weight:700;">__HEADLINE__</div>
                    <div style="color:rgba(255,255,255,0.85);font-size:13px;">__SUBTITLE__</div>
                </div>
            </div>
            <span style="display:inline-block;margin-top:10px;padding:6px 16px;background:#F97316;color:#fff;border-radius:20px;font-size:13px;font-weight:600;">__CTA_TEXT__</span>
        </div>
    </div>
</a>
HTML;

        $sidebarTemplateHtml = <<<'HTML'
<a href="__LINK__" target="_blank" rel="noopener" style="display:block;border-radius:14px;overflow:hidden;text-decoration:none;max-width:__WIDTH__;margin:0 auto;border:1px solid #e5e7eb;box-shadow:0 4px 16px rgba(0,0,0,0.06);">
    <div style="position:relative;width:100%;overflow:hidden;border-radius:14px;">
        <img src="__IMAGE_URL__" alt="__HEADLINE__" style="width:100%;height:__HEIGHT__;object-fit:cover;" loading="lazy">
        <div style="padding:14px 16px;background:#fff;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                <img src="__LOGO_URL__" alt="Logo" style="width:28px;height:28px;border-radius:6px;object-fit:cover;">
                <span style="font-size:14px;font-weight:700;color:#111827;">__HEADLINE__</span>
            </div>
            <p style="font-size:13px;color:#6b7280;margin:0 0 10px;">__SUBTITLE__</p>
            <span style="display:inline-block;padding:6px 16px;background:#F97316;color:#fff;border-radius:20px;font-size:12px;font-weight:600;">__CTA_TEXT__</span>
        </div>
    </div>
</a>
HTML;

        foreach ($zones as $zoneData) {
            // Skip if zone already exists
            $existing = AdZone::where('page_location', $zoneData['page_location'])->first();
            if ($existing) {
                $this->command->info("Zone '{$zoneData['name']}' already exists, skipping.");
                continue;
            }

            $zone = AdZone::create([
                'name' => $zoneData['name'],
                'page_location' => $zoneData['page_location'],
                'price_per_day' => $zoneData['price_per_day'],
                'specifications' => $zoneData['specifications'],
                'auto_approve' => true,
                'is_active' => true,
            ]);

            $isSmall = str_contains($zoneData['page_location'], 'sidebar');

            AdTemplate::create([
                'ad_zone_id' => $zone->id,
                'name' => $zoneData['name'] . ' Template',
                'html_content' => $isSmall ? $sidebarTemplateHtml : $bannerTemplateHtml,
                'is_active' => true,
            ]);

            $this->command->info("Created zone + template: {$zoneData['name']}");
        }

        $this->command->info('Landing page ad zones and templates seeded successfully!');
    }
}
