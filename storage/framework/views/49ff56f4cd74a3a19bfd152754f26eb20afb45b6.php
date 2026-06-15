<div>
    
    <div class="w-full float-left  border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">Ad Insights</h1>
            <p class="text-gray-600 -mt-6 pb-8">Viewing performance for: <strong class="text-gray-900"><?php echo e($post->title); ?></strong></p>
        </div>
    </div>

    
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">
            
            
            <div class=" rounded-lg shadow-sm p-4 border border-gray-200 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm font-medium text-gray-700">
                    <?php if($is_promoted): ?>
                        Your ad is currently promoted. Great job!
                    <?php else: ?>
                        Boost your ad's visibility by promoting it.
                    <?php endif; ?>
                </p>
                <?php if(!$is_promoted): ?>
                    <a href="<?php echo e(url('/selectPackage?post=' . $postId)); ?>" target="_blank" class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all">
                        <i class="fa fa-rocket"></i> Promote Ad
                    </a>
                <?php endif; ?>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class=" rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa fa-eye text-gray-400"></i> Views</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-baseline">
                            <span class="text-gray-600">Total Views</span>
                            <span class="text-3xl font-bold text-green-600"><?php echo e($total_user_views); ?></span>
                        </div>
                        <div class="flex justify-between items-baseline">
                            <span class="text-gray-600">Unique Viewers</span>
                            <span class="text-3xl font-bold text-green-600"><?php echo e($unique_user_views); ?></span>
                        </div>
                    </div>
                </div>

                
                <div class=" rounded-lg shadow-sm p-6 border border-gray-200">
                     <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa fa-users text-gray-400"></i> Engagement</h3>
                     <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        <div class="flex justify-between items-baseline border-b pb-2">
                            <span class="text-sm text-gray-600">Likes</span>
                            <span class="text-xl font-bold text-gray-800"><?php echo e($total_likes); ?></span>
                        </div>
                         <div class="flex justify-between items-baseline border-b pb-2">
                            <span class="text-sm text-gray-600">Comments</span>
                            <span class="text-xl font-bold text-gray-800"><?php echo e($total_comments); ?></span>
                        </div>
                         <div class="flex justify-between items-baseline border-b pb-2">
                            <span class="text-sm text-gray-600">Offers</span>
                            <span class="text-xl font-bold text-gray-800"><?php echo e($total_offer_request); ?></span>
                        </div>
                         <div class="flex justify-between items-baseline border-b pb-2">
                            <span class="text-sm text-gray-600">Exchanges</span>
                            <span class="text-xl font-bold text-gray-800"><?php echo e($total_exchange_request); ?></span>
                        </div>
                     </div>
                </div>

                
                <div class="rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa fa-map-marker-alt text-gray-400"></i> Top Cities</h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                        <?php $__empty_1 = true; $__currentLoopData = $total_city; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700"><?php echo e($city->city); ?></span>
                                    <span class="font-bold text-gray-800"><?php echo e($city->user_count); ?></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <?php
                                        $percentage = ($total_user_views > 0) ? ($city->user_count / $total_user_views) * 100 : 0;
                                    ?>
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo e($percentage); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-sm text-gray-500 text-center pt-8">No location data available yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class=" rounded-lg shadow-sm p-6 border border-gray-200 mt-6" wire:ignore>
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Performance Overview</h3>
                    <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg mt-3 sm:mt-0">
                        <button @click="$wire.updateChart('weekly')" :class="{ 'bg-white shadow-sm': $wire.chartPeriod === 'weekly' }" class="px-3 py-1 text-sm font-semibold rounded-md">Weekly</button>
                        <button @click="$wire.updateChart('monthly')" :class="{ 'bg-white shadow-sm': $wire.chartPeriod === 'monthly' }" class="px-3 py-1 text-sm font-semibold rounded-md">Monthly</button>
                        <button @click="$wire.updateChart('yearly')" :class="{ 'bg-white shadow-sm': $wire.chartPeriod === 'yearly' }" class="px-3 py-1 text-sm font-semibold rounded-md">Yearly</button>
                    </div>
                </div>
                <div>
                    <canvas id="insightsChart"></canvas>
                </div>
            </div>
            
        </div>
    </div>
    
    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            const ctx = document.getElementById('insightsChart').getContext('2d');
            const insightsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
                    datasets: [{
                        label: 'Total Views',
                        data: <?php echo json_encode($chartData, 15, 512) ?>,
                        backgroundColor: 'rgba(52, 211, 153, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: { legend: { display: false } }
                }
            });

            window.addEventListener('chart-updated', event => {
                insightsChart.data.labels = event.detail.labels;
                insightsChart.data.datasets[0].data = event.detail.data;
                insightsChart.update();
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
  <style>
  	
        
        
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            background-color: #fffbfa;
            color: #0f172a;
            min-height: 100vh;
        }
        
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
        }

        /* Footer */
        footer {
            border-top: 1px solid #e2e8f0;
            
            margin-top: auto;
            width: 100%;
        }
        
  </style>
</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/insights.blade.php ENDPATH**/ ?>