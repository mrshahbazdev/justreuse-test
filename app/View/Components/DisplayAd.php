<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\UserAdvertisement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DisplayAd extends Component
{
    public $finalHtml;

    public function __construct($pageLocation, $categoryId = null)
    {
        // Cache for 30 minutes (1800 seconds)
        $cacheKey = "ad_{$pageLocation}" . ($categoryId ? "_cat_{$categoryId}" : '') . '_' . now()->format('YmdH');
        
        $this->finalHtml = Cache::remember($cacheKey, 1800, function () use ($pageLocation, $categoryId) {
            return $this->getAdHtml($pageLocation, $categoryId);
        });
    }

    private function getAdHtml($pageLocation, $categoryId)
    {
        // Select only required columns
        $advertisement = UserAdvertisement::where('status', 'approved')
            ->where('payment_status', 'paid')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->whereHas('adZone', function ($q) use ($pageLocation) {
                $q->where('page_location', 'LIKE', "%{$pageLocation}%")
                  ->where('is_active', true);
            })
            ->with(['adTemplate:id,html_content', 'adZone:id,specifications'])
            ->inRandomOrder()
            ->first();

        if (!$advertisement) {
            return null;
        }

        return $this->renderAd($advertisement);
    }

    private function renderAd($advertisement)
    {
        $template = $advertisement->adTemplate->html_content;
        
        $specs = $advertisement->adZone->specifications;
        
        if (is_string($specs)) {
            $specs = json_decode($specs, true) ?? [];
        }
        
        $widthValue = $specs['width'] ?? 'auto';
        $heightValue = $specs['height'] ?? 'auto';
        
        $width = is_numeric($widthValue) ? $widthValue . 'px' : 'auto';
        $height = is_numeric($heightValue) ? $heightValue . 'px' : 'auto';

        $rawImage = $advertisement->content['image'] ?? '';
        $imageUrl = $rawImage ? (str_starts_with($rawImage, 'http') ? $rawImage : Storage::url($rawImage)) : '';
        $headline = e($advertisement->content['headline'] ?? '');
        $subtitle = e($advertisement->content['subtitle'] ?? '');
        $link = e($advertisement->content['link'] ?? '#');
        $cta_text = e($advertisement->content['cta_text'] ?? 'Learn More');
        $rawLogo = $advertisement->content['logo'] ?? '';
        $logoUrl = $rawLogo ? (str_starts_with($rawLogo, 'http') ? $rawLogo : Storage::url($rawLogo)) : 'https://via.placeholder.com/40x40.png?text=Ad';

        $processedHtml = str_replace(
            ['__IMAGE_URL__', '__HEADLINE__', '__SUBTITLE__', '__LINK__', '__CTA_TEXT__', '__LOGO_URL__', '__WIDTH__', '__HEIGHT__'],
            [$imageUrl, $headline, $subtitle, $link, $cta_text, $logoUrl, $width, $height],
            $template
        );

        return $processedHtml;
    }

    public function render()
    {
        return $this->finalHtml 
            ? view('components.display-ad', ['finalHtml' => $this->finalHtml])
            : '<div></div>'; // Empty div if no ad
    }
}