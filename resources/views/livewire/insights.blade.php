<div>
<style>
    /* ===== INSIGHTS PAGE SCOPED CSS (ins- prefix) ===== */
    :root {
        --ins-primary: #16a34a;
        --ins-primary-light: #22c55e;
        --ins-primary-bg: #f0fdf4;
        --ins-accent: #f97316;
        --ins-bg: #f8fafc;
        --ins-card: #ffffff;
        --ins-text: #1e293b;
        --ins-text-muted: #64748b;
        --ins-border: #e2e8f0;
        --ins-radius: 16px;
    }

    body { background: var(--ins-bg) !important; }
    footer { background: var(--ins-bg) !important; }

    .ins-shell {
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px 16px 60px;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* --- Header --- */
    .ins-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .ins-header-thumb {
        width: 64px; height: 64px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid var(--ins-border);
        flex-shrink: 0;
    }
    .ins-header-info { flex: 1; min-width: 200px; }
    .ins-header-title {
        font-size: 22px; font-weight: 700;
        color: var(--ins-text); margin: 0 0 2px;
        line-height: 1.3;
    }
    .ins-header-sub {
        font-size: 13px; color: var(--ins-text-muted); margin: 0;
    }
    .ins-back-link {
        font-size: 13px; color: var(--ins-primary);
        text-decoration: none; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .ins-back-link:hover { text-decoration: underline; }

    /* --- Promote Banner --- */
    .ins-promote-banner {
        background: linear-gradient(135deg, #065f46 0%, #059669 100%);
        color: #fff;
        padding: 16px 20px;
        border-radius: var(--ins-radius);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .ins-promote-banner.ins-active {
        background: linear-gradient(135deg, #14532d 0%, #166534 100%);
    }
    .ins-promote-text { font-size: 14px; font-weight: 500; }
    .ins-promote-text i { margin-right: 6px; }
    .ins-promote-btn {
        background: #fff;
        color: #065f46;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: none; cursor: pointer;
    }
    .ins-promote-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* --- Stat Cards Row --- */
    .ins-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .ins-stat-card {
        background: var(--ins-card);
        border: 1px solid var(--ins-border);
        border-radius: var(--ins-radius);
        padding: 18px 16px;
        text-align: center;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }
    .ins-stat-card:hover {
        border-color: var(--ins-primary);
        box-shadow: 0 4px 16px rgba(22, 163, 74, 0.08);
        transform: translateY(-2px);
    }
    .ins-stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-bottom: 10px;
    }
    .ins-stat-icon.views { background: #dbeafe; color: #2563eb; }
    .ins-stat-icon.unique { background: #ede9fe; color: #7c3aed; }
    .ins-stat-icon.likes { background: #fce7f3; color: #db2777; }
    .ins-stat-icon.comments { background: #fef3c7; color: #d97706; }
    .ins-stat-icon.offers { background: #d1fae5; color: #059669; }
    .ins-stat-icon.exchanges { background: #ffedd5; color: #ea580c; }

    .ins-stat-value {
        font-size: 28px; font-weight: 800;
        color: var(--ins-text);
        line-height: 1;
        margin-bottom: 4px;
    }
    .ins-stat-label {
        font-size: 12px; font-weight: 500;
        color: var(--ins-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- Section Card --- */
    .ins-section {
        background: var(--ins-card);
        border: 1px solid var(--ins-border);
        border-radius: var(--ins-radius);
        padding: 24px;
        margin-bottom: 20px;
    }
    .ins-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .ins-section-title {
        font-size: 16px; font-weight: 700;
        color: var(--ins-text);
        display: flex; align-items: center; gap: 8px;
        margin: 0;
    }
    .ins-section-title i {
        color: var(--ins-text-muted); font-size: 15px;
    }

    /* --- Chart Period Tabs --- */
    .ins-period-tabs {
        display: inline-flex;
        background: #f1f5f9;
        border-radius: 8px;
        padding: 3px;
        gap: 2px;
    }
    .ins-period-tab {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        background: transparent;
        color: var(--ins-text-muted);
        transition: all 0.2s;
    }
    .ins-period-tab:hover { color: var(--ins-text); }
    .ins-period-tab.active {
        background: #fff;
        color: var(--ins-primary);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* --- Cities Section --- */
    .ins-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .ins-city-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .ins-city-item:last-child { border-bottom: none; }
    .ins-city-rank {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: var(--ins-text-muted);
        flex-shrink: 0;
    }
    .ins-city-rank.top { background: var(--ins-primary-bg); color: var(--ins-primary); }
    .ins-city-info { flex: 1; }
    .ins-city-name {
        font-size: 13px; font-weight: 600;
        color: var(--ins-text); margin: 0 0 4px;
    }
    .ins-city-bar-bg {
        width: 100%; height: 6px;
        background: #f1f5f9;
        border-radius: 3px;
        overflow: hidden;
    }
    .ins-city-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--ins-primary), var(--ins-primary-light));
        border-radius: 3px;
        transition: width 0.5s ease;
    }
    .ins-city-count {
        font-size: 14px; font-weight: 700;
        color: var(--ins-text);
        min-width: 32px;
        text-align: right;
    }

    /* --- Reach More CTA --- */
    .ins-reach-cta {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fbbf24;
        border-radius: var(--ins-radius);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .ins-reach-text {
        font-size: 14px; font-weight: 600;
        color: #78350f;
    }
    .ins-reach-text i { font-size: 18px; margin-right: 6px; }
    .ins-reach-btn {
        background: #f59e0b;
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .ins-reach-btn:hover { background: #d97706; }

    /* --- Empty State --- */
    .ins-empty {
        text-align: center;
        padding: 32px 16px;
        color: var(--ins-text-muted);
    }
    .ins-empty i { font-size: 32px; margin-bottom: 8px; display: block; opacity: 0.4; }
    .ins-empty p { font-size: 13px; margin: 0; }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .ins-shell { padding: 16px 12px 40px; }
        .ins-header-title { font-size: 18px; }
        .ins-header-thumb { width: 48px; height: 48px; }
        .ins-stats-row { grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .ins-stat-value { font-size: 22px; }
        .ins-stat-icon { width: 32px; height: 32px; font-size: 14px; }
        .ins-grid-2 { grid-template-columns: 1fr; }
        .ins-section { padding: 16px; }
        .ins-promote-banner { flex-direction: column; text-align: center; }
        .ins-reach-cta { flex-direction: column; text-align: center; }
    }
    @media (max-width: 480px) {
        .ins-stats-row { grid-template-columns: repeat(2, 1fr); }
    }

    /* Footer fix */
    .page-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
    .ins-shell { flex-grow: 1; }
</style>

<div class="ins-shell">

    {{-- Back Link --}}
    <a href="{{ url('/ad/' . $post->slug) }}" class="ins-back-link" style="margin-bottom: 16px; display: inline-flex;">
        <i class="fa fa-arrow-left"></i> Back to Ad
    </a>

    {{-- Header --}}
    <div class="ins-header">
        @php
            $firstImage = '';
            if (!empty($post->images)) {
                $imgs = explode(',', $post->images);
                $firstImage = asset('storage/' . trim($imgs[0]));
            }
        @endphp
        @if($firstImage)
            <img src="{{ $firstImage }}" alt="{{ $post->title }}" class="ins-header-thumb">
        @else
            <div class="ins-header-thumb" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                <i class="fa fa-image" style="color:#cbd5e1; font-size:20px;"></i>
            </div>
        @endif
        <div class="ins-header-info">
            <h1 class="ins-header-title">{{ $post->title }}</h1>
            <p class="ins-header-sub">
                <i class="fa fa-calendar-alt"></i> Posted {{ $post->created_at->diffForHumans() }}
                &nbsp;·&nbsp;
                <i class="fa fa-eye"></i> {{ $total_user_views }} total views
            </p>
        </div>
    </div>

    {{-- Promote Banner --}}
    <div class="ins-promote-banner {{ $is_promoted ? 'ins-active' : '' }}">
        <span class="ins-promote-text">
            @if($is_promoted)
                <i class="fa fa-check-circle"></i> Your ad is currently promoted — getting extra visibility!
            @else
                <i class="fa fa-rocket"></i> Boost your ad's visibility and reach more buyers.
            @endif
        </span>
        @if(!$is_promoted)
            <a href="{{ url('/selectPackage?post=' . $postId) }}" target="_blank" class="ins-promote-btn">
                <i class="fa fa-bolt"></i> Promote Now
            </a>
        @endif
    </div>

    {{-- Stats Row --}}
    <div class="ins-stats-row">
        <div class="ins-stat-card">
            <div class="ins-stat-icon views"><i class="fa fa-eye"></i></div>
            <div class="ins-stat-value">{{ number_format($total_user_views) }}</div>
            <div class="ins-stat-label">Total Views</div>
        </div>
        <div class="ins-stat-card">
            <div class="ins-stat-icon unique"><i class="fa fa-user-friends"></i></div>
            <div class="ins-stat-value">{{ number_format($unique_user_views) }}</div>
            <div class="ins-stat-label">Unique Viewers</div>
        </div>
        <div class="ins-stat-card">
            <div class="ins-stat-icon likes"><i class="fa fa-heart"></i></div>
            <div class="ins-stat-value">{{ number_format($total_likes) }}</div>
            <div class="ins-stat-label">Likes</div>
        </div>
        <div class="ins-stat-card">
            <div class="ins-stat-icon comments"><i class="fa fa-comment-dots"></i></div>
            <div class="ins-stat-value">{{ number_format($total_comments) }}</div>
            <div class="ins-stat-label">Comments</div>
        </div>
        <div class="ins-stat-card">
            <div class="ins-stat-icon offers"><i class="fa fa-hand-holding-usd"></i></div>
            <div class="ins-stat-value">{{ number_format($total_offer_request) }}</div>
            <div class="ins-stat-label">Offers</div>
        </div>
        <div class="ins-stat-card">
            <div class="ins-stat-icon exchanges"><i class="fa fa-exchange-alt"></i></div>
            <div class="ins-stat-value">{{ number_format($total_exchange_request) }}</div>
            <div class="ins-stat-label">Exchanges</div>
        </div>
    </div>

    {{-- Performance Chart --}}
    <div class="ins-section" wire:ignore>
        <div class="ins-section-head">
            <h3 class="ins-section-title"><i class="fa fa-chart-line"></i> Performance Overview</h3>
            <div class="ins-period-tabs">
                <button onclick="switchPeriod(this, 'weekly')" class="ins-period-tab active">Weekly</button>
                <button onclick="switchPeriod(this, 'monthly')" class="ins-period-tab">Monthly</button>
                <button onclick="switchPeriod(this, 'yearly')" class="ins-period-tab">Yearly</button>
            </div>
        </div>
        <canvas id="insightsChart" height="100"></canvas>
    </div>

    {{-- Bottom Grid: Cities + Reach More --}}
    <div class="ins-grid-2">
        {{-- Top Cities --}}
        <div class="ins-section" style="margin-bottom: 0;">
            <div class="ins-section-head" style="margin-bottom: 12px;">
                <h3 class="ins-section-title"><i class="fa fa-map-marker-alt"></i> Top Cities</h3>
            </div>
            <div style="max-height: 280px; overflow-y: auto;">
                @forelse($total_city as $index => $city)
                    <div class="ins-city-item">
                        <div class="ins-city-rank {{ $index < 3 ? 'top' : '' }}">{{ $index + 1 }}</div>
                        <div class="ins-city-info">
                            <p class="ins-city-name">{{ $city->city }}</p>
                            <div class="ins-city-bar-bg">
                                @php
                                    $pct = ($total_user_views > 0) ? ($city->user_count / $total_user_views) * 100 : 0;
                                @endphp
                                <div class="ins-city-bar-fill" style="width: {{ max($pct, 3) }}%"></div>
                            </div>
                        </div>
                        <div class="ins-city-count">{{ $city->user_count }}</div>
                    </div>
                @empty
                    <div class="ins-empty">
                        <i class="fa fa-map"></i>
                        <p>No location data available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Reach More / Tips --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @if($reach_page)
            <div class="ins-reach-cta">
                <span class="ins-reach-text">
                    <i class="fa fa-lightbulb"></i> Learn how to increase your ad's reach and get more buyers.
                </span>
                <a href="{{ url('/page/' . $reach_page->slug) }}" class="ins-reach-btn" target="_blank">
                    <i class="fa fa-external-link-alt"></i> Read Tips
                </a>
            </div>
            @endif

            <div class="ins-section" style="margin-bottom: 0; flex: 1;">
                <div class="ins-section-head" style="margin-bottom: 12px;">
                    <h3 class="ins-section-title"><i class="fa fa-info-circle"></i> Quick Tips</h3>
                </div>
                <div style="font-size: 13px; color: var(--ins-text-muted); line-height: 1.8;">
                    <p style="margin: 0 0 8px;"><i class="fa fa-check" style="color: var(--ins-primary); margin-right: 6px;"></i> Add high-quality photos to get 3x more views</p>
                    <p style="margin: 0 0 8px;"><i class="fa fa-check" style="color: var(--ins-primary); margin-right: 6px;"></i> Update your price regularly to stay competitive</p>
                    <p style="margin: 0 0 8px;"><i class="fa fa-check" style="color: var(--ins-primary); margin-right: 6px;"></i> Reply quickly to messages for better engagement</p>
                    <p style="margin: 0;"><i class="fa fa-check" style="color: var(--ins-primary); margin-right: 6px;"></i> Promote your ad to reach 10x more buyers</p>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        var ctx = document.getElementById('insightsChart').getContext('2d');

        var gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(22, 163, 74, 0.15)');
        gradient.addColorStop(1, 'rgba(22, 163, 74, 0.01)');

        var insightsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Views',
                    data: @json($chartData),
                    backgroundColor: gradient,
                    borderColor: '#16a34a',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#16a34a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { font: { size: 11 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 12 },
                        bodyFont: { size: 13, weight: 'bold' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                }
            }
        });

        window.addEventListener('chart-updated', function(event) {
            insightsChart.data.labels = event.detail.labels;
            insightsChart.data.datasets[0].data = event.detail.data;
            insightsChart.update();
        });

        window.insightsChart = insightsChart;
    });

    function switchPeriod(btn, period) {
        document.querySelectorAll('.ins-period-tab').forEach(function(t) { t.classList.remove('active'); });
        btn.classList.add('active');
        @this.updateChart(period);
    }
</script>
@endpush

</div>
