<div>
    <div class="shell">
        <header class="flex flex-col justify-between mb-6">
            <span class="bg-orange text-white text-center poppins-600 rounded-full py-2 mt-1">Add Post</span> 
            <div class="steps-wrap" style="margin-top:14px;">
                <div class="step <?php echo e($currentStep == 1 ? 'active' : ''); ?>">Step 1 – Basic Info</div>
                <div class="step <?php echo e($currentStep == 2 ? 'active' : ''); ?>">Step 2 – Price/Details</div>
            </div>
        </header>

        <?php if(session()->has('message')): ?>
            <div class="alert alert-success mb-4">
                <?php echo e(session('message')); ?>

            </div>
        <?php endif; ?>

        
        <?php if($currentStep == 1): ?>
        <div id="step-1">
            <h2>Choose Main Category</h2>
            <br>
            <select wire:model="selectedParentCategory" class="form-select" required>
                <option value="">Select a Category</option>
                <?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->title); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <br><br>

            <?php if(!empty($childCategories) && $selectedParentCategory): ?>
                <h2>What are you selling in <b><?php echo e($selectedParentCategoryTitle); ?></b>?</h2>

                <div class="categories mt-4" data-group="vehicle-type">
                    <?php $__currentLoopData = $childCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button 
                            class="seg <?php echo e($selectedChildCategory == $child->id ? 'active' : ''); ?>" 
                            type="button" 
                            wire:click="selectChildCategory(<?php echo e($child->id); ?>)"
                            wire:key="category-<?php echo e($child->id); ?>">

                            <img src="<?php echo e(asset('storage/' . $child->image)); ?>" 
                                 alt="<?php echo e($child->title); ?>" 
                                 class="rounded-circle me-2" 
                                 style="width:26px; height:26px; border-radius:50%; object-fit:cover;">

                            <?php echo e($child->title); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <br>
                
                <?php if($selectedChildCategory): ?>
                    
                    <div wire:loading wire:target="selectChildCategory" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading custom fields...</p>
                    </div>

                    <div wire:loading.remove wire:target="selectChildCategory">
                        
                        <label>Product Title <span class="text-red-800">*</span></label>
                        <input class="input" type="text" placeholder="Enter Product Title *" wire:model.defer="title" required />
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        

                        
                        <?php if(!empty($customFields) && count($customFields) > 0): ?>
                            <div class="mt-6 mb-6" wire:key="custom-fields-wrapper-<?php echo e($selectedChildCategory); ?>">
                                <h3 class="text-lg font-bold mb-4 text-gray-800">Additional Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $fieldName = $field->id . '_' . $field->form_field_name;
                                            $fieldWireModel = 'customFieldsData.' . $fieldName;
                                            $isRequired = $field->required != '0';
                                        ?>

                                        
                                        <?php if(in_array($field->type, ['text', 'number', 'date'])): ?>
                                            <div class="mb-4" wire:key="field-<?php echo e($field->id); ?>">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <?php echo e($field->name); ?>

                                                    <?php if($isRequired): ?> <span class="text-red-800">*</span> <?php endif; ?>
                                                </label>
                                                <input 
                                                    type="<?php echo e($field->type); ?>" 
                                                    wire:model.defer="<?php echo e($fieldWireModel); ?>"
                                                    <?php echo e($isRequired ? 'required' : ''); ?>

                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                                />
                                                <?php $__errorArgs = [$fieldWireModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if($field->type == 'textarea'): ?>
                                            <div class="mb-4" wire:key="field-<?php echo e($field->id); ?>">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <?php echo e($field->name); ?>

                                                    <?php if($isRequired): ?> <span class="text-red-800">*</span> <?php endif; ?>
                                                </label>
                                                <textarea 
                                                    wire:model.defer="<?php echo e($fieldWireModel); ?>"
                                                    <?php echo e($isRequired ? 'required' : ''); ?>

                                                    rows="3"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                                ></textarea>
                                                <?php $__errorArgs = [$fieldWireModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if($field->form_field_name === 'brandwithmodel'): ?>
                                            <?php
                                                $brandOptions = \App\Models\TblFieldsOption::where('form_field_name', $field->form_field_name)
                                                    ->where('active', '1')
                                                    ->orderBy('key', 'asc')
                                                    ->get();
                                            ?>
                                            
                                            
                                            <div class="mb-4" wire:key="field-<?php echo e($field->id); ?>">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <?php echo e($field->name); ?>

                                                    <?php if($isRequired): ?> <span class="text-red-800">*</span> <?php endif; ?>
                                                </label>
                                                <select 
                                                    wire:model="<?php echo e($fieldWireModel); ?>"
                                                    <?php echo e($isRequired ? 'required' : ''); ?>

                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                                >
                                                    <option value="">Select Brand</option>
                                                    <?php $__currentLoopData = $brandOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->id); ?>"><?php echo e($option->key); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php $__errorArgs = [$fieldWireModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>

                                            
                                            <?php
                                                $modelFieldName = $field->id . '_brandswithmodels';
                                                $modelWireModel = 'customFieldsData.' . $modelFieldName;
                                            ?>
                                            <div class="mb-4" wire:key="field-models-<?php echo e($field->id); ?>">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Models
                                                    <?php if($isRequired): ?> <span class="text-red-800">*</span> <?php endif; ?>
                                                </label>
                                                <select 
                                                    wire:model.defer="<?php echo e($modelWireModel); ?>"
                                                    <?php echo e($isRequired ? 'required' : ''); ?>

                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                                >
                                                    <?php if(isset($dynamicModels[$field->id]) && !empty($dynamicModels[$field->id])): ?>
                                                        <option value="">Select Model</option>
                                                        <?php $__currentLoopData = $dynamicModels[$field->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e(strtolower(str_replace(' ', '-', $model))); ?>"><?php echo e($model); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        <option value="">Select brand first</option>
                                                    <?php endif; ?>
                                                </select>
                                                <?php $__errorArgs = [$modelWireModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if(in_array($field->type, ['select', 'autocomplete', 'radio-group']) && $field->form_field_name !== 'brandwithmodel'): ?>
                                            <?php
                                                $options = \App\Models\TblFieldsOption::where('form_field_name', $field->form_field_name)
                                                    ->where('active', '1')
                                                    ->get();
                                            ?>
                                            <div class="mb-4" wire:key="field-<?php echo e($field->id); ?>">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <?php echo e($field->name); ?>

                                                    <?php if($isRequired): ?> <span class="text-red-800">*</span> <?php endif; ?>
                                                </label>
                                                <select 
                                                    wire:model.defer="<?php echo e($fieldWireModel); ?>"
                                                    <?php echo e($isRequired ? 'required' : ''); ?>

                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                                >
                                                    <option value="">Select <?php echo e($field->name); ?></option>
                                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option->value); ?>"><?php echo e($option->key); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php $__errorArgs = [$fieldWireModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if($field->type == 'checkbox-group'): ?>
                                            <?php
                                                $options = \App\Models\TblFieldsOption::where('form_field_name', $field->form_field_name)
                                                    ->where('active', '1')
                                                    ->get();
                                            ?>
                                            <div class="mb-4" wire:key="field-<?php echo e($field->id); ?>">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <?php echo e($field->name); ?>

                                                    <?php if($isRequired): ?> <span class="text-red-800">*</span> <?php endif; ?>
                                                </label>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <button 
                                                            type="button"
                                                            wire:click="$set('<?php echo e($fieldWireModel); ?>', '<?php echo e($option->value); ?>')"
                                                            class="px-3 py-2 border border-gray-300 rounded-full text-sm hover:bg-gray-50 transition
                                                                   <?php echo e(isset($customFieldsData[$fieldName]) && $customFieldsData[$fieldName] == $option->value ? 'bg-green-500 text-white border-green-500' : ''); ?>"
                                                        >
                                                            <?php echo e($option->key); ?>

                                                        </button>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                                <?php $__errorArgs = [$fieldWireModel];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        


                        
                        <?php if(!empty($productConditionHtml)): ?>
                        <div class="mt-4" wire:key="product-condition-<?php echo e($selectedChildCategory); ?>">
                            <?php echo $productConditionHtml; ?>

                        </div>
                        <?php endif; ?>

                        <div class="actions mt-6">
                            <button class="btn" type="button" wire:click="nextStep" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="nextStep">Continue</span>
                                <span wire:loading wire:target="nextStep">Loading...</span>
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center text-gray-500 py-4">
                        Please select a sub-category to continue
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center text-gray-500 py-4">
                    Please select a main category first
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        
        <?php if($currentStep == 2): ?>
        <div id="advanced-form-container" class="p-6 md:p-10 bg-white rounded-xl shadow-2xl">
        <div id="step-2">
            
            <div class="sm:flex sm:space-x-4 lg:space-x-8 w-full mb-8">
                <div class="w-full sm:w-1/2 mb-6 sm:mb-0">
                    <div class="mb-6">
                        <label class="form-label">Price <span class="text-red-800">*</span></label>
                        <div class="flex input-group-container">
                            <span class="currency-select-wrapper w-4/6 xl:w-4/12">
                                <select wire:model="currency_id" class="input-select-field">
                                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($currency->id); ?>"><?php echo $currency->short_code . " (" . $currency->currency_hex . ")"; ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </span>
                            <input type="number" class="input-field-main"
                                   wire:model="price" min="0" required>
                        </div>
                        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-6">
                        <label class="form-label">City <span class="text-red-800">*</span></label>
                        <input type="text" class="input-field-readonly"
                               wire:model="text_city_sst" placeholder="City (will be auto-filled)" readonly>
                        <?php $__errorArgs = ['text_city_sst'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="w-full sm:w-1/2 mb-4 sm:mb-0">
                    <div class="mb-6">
                        <label class="form-label">Location <span class="text-red-800">*</span></label>
                        <input type="text" id="searchTextField" class="input-field"
                               wire:model="location" placeholder="Start typing your full address..." required>
                        <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-6">
                        <label class="form-label">Country <span class="text-red-800">*</span></label>
                        <input type="text" class="input-field-readonly"
                               wire:model="text_country_sst" placeholder="Country (will be auto-filled)" readonly>
                        <?php $__errorArgs = ['text_country_sst'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            
            <input type="hidden" wire:model="city_name">
            <input type="hidden" wire:model="main_city_name">
            <input type="hidden" wire:model="city_lat">
            <input type="hidden" wire:model="city_lag">
            <input type="hidden" wire:model="country_long">
            <input type="hidden" wire:model="country_short">
            <input type="hidden" wire:model="state_long">
            <input type="hidden" wire:model="state_short">

            
            <div class="w-full mb-8">
                <ul class="sm:inline-flex w-full space-y-4 sm:space-y-0 sm:space-x-8">
                    <li class="w-full sm:w-auto">
                        <label for="Products_exchangeToBuy" class="w-full sm:w-auto flex items-center cursor-pointer space-x-4">
                            <span class="block text-base text-black font-semibold">Exchange to Buy</span>
                            <div class="relative">
                                <input type="checkbox" id="Products_exchangeToBuy" class="sr-only" wire:model="exchangeToBuy" value="1">
                                <div class="block bg-gray-300 w-16 lg:w-20 h-9 lg:h-11 rounded-full transition-colors duration-300"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-7 lg:w-9 h-7 lg:h-9 rounded-full transition-transform duration-300 shadow"></div>
                            </div>
                        </label>
                    </li>

                    <li class="w-full sm:w-auto">
                        <label for="Products_InstantBuy" class="w-full sm:w-auto flex items-center cursor-pointer space-x-4">
                            <span class="block text-base text-black font-semibold">Instant Buy</span>
                            <div class="relative">
                                <input type="checkbox" id="Products_InstantBuy" class="sr-only" wire:model="InstantBuy" value="1">
                                <div class="block bg-gray-300 w-16 lg:w-20 h-9 lg:h-11 rounded-full transition-colors duration-300"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-7 lg:w-9 h-7 lg:h-9 rounded-full transition-transform duration-300 shadow"></div>
                            </div>
                        </label>
                    </li>

                    <li class="w-full sm:w-auto">
                        <label for="Products_FixedPrice" class="w-full sm:w-auto flex items-center cursor-pointer space-x-4">
                            <span class="block text-base text-black font-semibold">Fixed Price</span>
                            <div class="relative">
                                <input type="checkbox" id="Products_FixedPrice" class="sr-only" wire:model="FixedPrice" value="1">
                                <div class="block bg-gray-300 w-16 lg:w-20 h-9 lg:h-11 rounded-full transition-colors duration-300"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-7 lg:w-9 h-7 lg:h-9 rounded-full transition-transform duration-300 shadow"></div>
                            </div>
                        </label>
                    </li>
                </ul>
            </div>

            
            <?php if($showShippingFee): ?>
            <div class="w-full block mb-8 shipping_fee">
                <label class="form-label">Shipping Cost</label>
                <input type="number"
                        placeholder="Shipping Cost"
                        class="input-field"
                        wire:model="shipping_fee"
                        min="0"
                        step="0.01">
            </div>
            <?php endif; ?>

            
            <div class="w-full block mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-green-700">Product Video</h2>
            </div>

            <div class="w-full mb-8">
                <label class="form-label">YouTube Video Embed URL</label>
                <input type="text" placeholder="Example: https://www.youtube.com/embed/9xwazD5SyVg" class="input-field placeholder-gray-400"
                        wire:model="video_url">
            </div>

            
            <div class="w-full flex flex-wrap mb-8">
                <div class="w-full lg:flex lg:space-x-6">
                    <div class="w-full lg:w-1/2 mb-8 lg:mb-0">
                        <div class="relative w-full float-left mt-2 mb-4 sm:m-0">
                            <div class="w-full float-left">
                                <div class="w-full rounded-xl bg-gray-50 float-left p-6 border border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Product Images</h3>
                                    <div class="w-full float-left py-2">
                                        <div class="border-4 border-dashed border-gray-300 rounded-xl hover:border-primary-light cursor-pointer relative h-52 transition-colors duration-200">
                                            <input type="file" wire:model="images" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="image-upload">
                                            <label for="image-upload" class="absolute flex flex-col items-center top-0 bottom-0 left-0 right-0 justify-center text-center cursor-pointer">
                                                <div class="text-4xl font-normal text-primary-light mb-4">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                                </div>
                                                <p class="text-xl text-gray-700 font-semibold mb-2">Upload Photos</p>
                                                <p class="text-sm text-gray-500">Click to browse or drag and drop (Max 5MB)</p>
                                            </label>
                                        </div>

                                        <div class="mt-6">
                                            <?php if($uploadedImages && count($uploadedImages) > 0): ?>
                                                <h4 class="text-lg font-semibold mb-3 text-green-700 flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12m0 0h4m-4 0H8"></path>
                                                    </svg>
                                                    Uploaded Images (Drag to reorder)
                                                </h4>

                                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                                    <?php $__currentLoopData = $uploadedImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div data-index="<?php echo e($index); ?>"
                                                            class="relative group bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden aspect-square"
                                                            wire:key="uploaded-image-<?php echo e($index); ?>-<?php echo e($image->getFilename()); ?>">

                                                            <div class="drag-handle absolute top-2 left-2 cursor-move bg-black bg-opacity-60 text-white p-1 rounded transition opacity-0 group-hover:opacity-100 z-10 hover:bg-opacity-80">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                                                </svg>
                                                            </div>

                                                            <img src="<?php echo e(asset('storage/livewire-tmp/' . $image->getFilename())); ?>" class="w-full h-full object-cover">

                                                            <?php if($index === 0): ?>
                                                                <div class="absolute bottom-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full z-10">
                                                                    Cover Photo
                                                                </div>
                                                            <?php endif; ?>

                                                            <button
                                                                type="button"
                                                                wire:click.prevent="removeImage(<?php echo e($index); ?>)"
                                                                wire:confirm="Are you sure you want to remove this image?"
                                                                wire:loading.attr="disabled"
                                                                class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white w-8 h-8 flex items-center justify-center rounded-full text-lg leading-none opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10 shadow-md">
                                                                &times;
                                                            </button>

                                                            <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                                                #<?php echo e($index + 1); ?>

                                                            </div>

                                                            <div wire:loading wire:target="removeImage(<?php echo e($index); ?>)"
                                                                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                                                                <div class="text-white text-xl">Removing...</div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>

                                                <div wire:loading wire:target="images" class="mt-4">
                                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                        <p class="text-blue-700 font-semibold">Uploading images...</p>
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                                            <div class="bg-blue-600 h-2.5 rounded-full animate-pulse" style="width: 45%"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php else: ?>
                                                <div class="mt-4 text-center text-gray-500 py-8 border-2 border-dashed border-gray-300 rounded-lg bg-white">
                                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="mt-2 text-lg">No images uploaded yet</p>
                                                    <p class="text-sm">Upload images to see them here</p>
                                                </div>
                                            <?php endif; ?>

                                            <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-red-500 text-sm mt-2 block bg-red-50 p-2 rounded"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="w-full lg:w-1/2">
                        <label class="block text-xl font-semibold text-gray-800 mb-4">Package Selection</label>
                        <div class="mb-4 md:mb-8">
                            <p class="text-base font-medium leading-relaxed text-gray-600">Choose your advertisement package</p>
                        </div>

                        <?php if(!empty($packagesList) && !empty($payment_methods)): ?>
                        <div class="w-full p-4 bg-white rounded-lg border border-gray-200">
                            <?php $__currentLoopData = $packagesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk_index => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <label for="package_<?php echo e($pk_index); ?>" class="w-full cursor-pointer block p-4 rounded-lg transition-all duration-200 mb-4 last:mb-0 hover:bg-gray-50 border <?php echo e($package_type == $package->id ? 'border-primary-light ring-2 ring-primary-light bg-green-50' : 'border-gray-200'); ?>">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <input type="radio"
                                                    name="package_type"
                                                    id="package_<?php echo e($pk_index); ?>"
                                                    class="sr-only package_type"
                                                    wire:model="package_type"
                                                    value="<?php echo e($package->id); ?>"
                                                    wire:change="$set('showPaymentMethods', <?php echo e($package->short_name == 'free' ? 'false' : 'true'); ?>)">
                                                <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center transition-colors duration-200"
                                                    style="border-color: <?php echo e($package_type == $package->id ? 'var(--primary-color)' : 'var(--border-color)'); ?>;">
                                                    <div class="w-3 h-3 rounded-full transition-transform duration-200"
                                                        style="background-color: var(--primary-color); transform: scale(<?php echo e($package_type == $package->id ? '1' : '0'); ?>);"></div>
                                                </div>
                                            </div>

                                            <div>
                                                <span class="text-lg md:text-xl font-bold text-gray-800"><?php echo e($package->name); ?></span>
                                                <span class="block text-sm text-gray-600">
                                                    <?php $pack_currency = App\Models\Setting::get_admin_default_currency(); ?>
                                                    <?php echo $pack_currency['currency_hex']; ?> <?php echo e(number_format($package->price, 2)); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <?php if($package->short_name == 'free'): ?>
                                            <span class="text-xs font-bold px-2 py-1 rounded-full bg-yellow-500 text-white tooltip-container">
                                                Free Now
                                                <span class="tooltip-text">Duration : <?php echo e($package->duration); ?> days, Limit : <?php echo e($package->single_pack_limit); ?> Ad</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs font-bold px-2 py-1 rounded-full bg-primary-color text-white tooltip-container">
                                                Upgrade
                                                <span class="tooltip-text">Duration : <?php echo e($package->duration); ?> days, Limit : 1 Ad</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['package_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-2 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            
            <div class="w-full block mb-8">
                <label class="form-label">Description <span class="text-red-800">*</span></label>
                <textarea class="textarea-field"
                          wire:model="description" placeholder="Enter product description..." required></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <?php if($showPaymentMethods): ?>
              <div class="w-full mb-8 payment_methods" id="payment-section">
                  <label class="form-label">Choose Payment Method <span class="text-red-800">*</span></label>
                  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-60 overflow-y-auto">
                      <ul class="space-y-3">
                          <?php $__currentLoopData = $payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <li class="border-b border-gray-200 pb-3 last:border-b-0 last:pb-0">
                                  <label class="inline-flex items-center w-full cursor-pointer py-2">
                                      <input type="radio"
                                              class="sr-only payment_type"
                                              name="payment_type"
                                              wire:model="payment_type"
                                              value="<?php echo e($p['name']); ?>"
                                              required>
                                      <span class="w-5 h-5 border-2 rounded-full flex items-center justify-center mr-3 transition-colors duration-200"
                                          style="border-color: <?php echo e($payment_type == $p['name'] ? 'var(--primary-color)' : 'var(--border-color)'); ?>;">
                                          <div class="w-3 h-3 rounded-full transition-transform duration-200"
                                              style="background-color: var(--primary-color); transform: scale(<?php echo e($payment_type == $p['name'] ? '1' : '0'); ?>);"></div>
                                      </span>
                                      <span class="font-medium text-gray-700"><?php echo e($p['display_name']); ?></span>
                                  </label>
                              </li>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </ul>
                  </div>
                  <?php $__errorArgs = ['payment_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            <?php endif; ?>

            
            <div class="actions flex gap-4 pt-4 border-t border-gray-200">
                <button class="btn btn-secondary" type="button" wire:click="previousStep" wire:loading.attr="disabled">
                    Back
                </button>
                <button class="btn" type="button" wire:click="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Submit</span>
                    <span wire:loading wire:target="submit">Submitting...</span>
                </button>
            </div>
        </div>
    </div>
        <?php endif; ?>
    </div>

   
   <script>
        function initMap() {
            var input = document.getElementById('searchTextField');
            if (!input) {
                setTimeout(initMap, 500);
                return;
            }

            var autocomplete = new google.maps.places.Autocomplete(input);
            
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.address_components) {
                    return;
                }
                
                const components = place.address_components;
                const country = components.find(item => item.types.includes('country'));
                const state = components.find(item => item.types.includes('administrative_area_level_1'));
                const mainCity = components.find(item => item.types.includes('locality'));
                const localArea = components.find(item => item.types.includes('sublocality')) || 
                                  components.find(item => item.types.includes('administrative_area_level_2'));

                window.livewire.find('<?php echo e($_instance->id); ?>').set('city_name', localArea ? localArea.long_name : (mainCity ? mainCity.long_name : ''));
                window.livewire.find('<?php echo e($_instance->id); ?>').set('main_city_name', mainCity ? mainCity.long_name : (state ? state.long_name : ''));
                window.livewire.find('<?php echo e($_instance->id); ?>').set('text_city_sst', mainCity ? mainCity.long_name : (state ? state.long_name : ''));
                window.livewire.find('<?php echo e($_instance->id); ?>').set('text_country_sst', country ? country.long_name : '');
                
                if (place.geometry && place.geometry.location) {
                    window.livewire.find('<?php echo e($_instance->id); ?>').set('city_lat', place.geometry.location.lat());
                    window.livewire.find('<?php echo e($_instance->id); ?>').set('city_lag', place.geometry.location.lng());
                }
                
                if (country) {
                    window.livewire.find('<?php echo e($_instance->id); ?>').set('country_long', country.long_name);
                    window.livewire.find('<?php echo e($_instance->id); ?>').set('country_short', country.short_name);
                }
                
                if (state) {
                    window.livewire.find('<?php echo e($_instance->id); ?>').set('state_long', state.long_name);
                    window.livewire.find('<?php echo e($_instance->id); ?>').set('state_short', state.short_name);
                }
            });
        }

        document.addEventListener('livewire:load', function() {
            if (!document.querySelector('script[src*="maps.googleapis.com"]')) {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDH2sK6kRKk4kd4Z0wfA0rsY8RQwUPJYq8&libraries=places&callback=initMap';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            } else {
                initMap();
            }
        });

        document.addEventListener('livewire:update', function() {
            setTimeout(initMap, 100);
        });

        Livewire.on('categorySelected', (data) => {
            console.log('Category selected:', data.categoryId);
        });
    </script>

<style>
    :root{
        --brand: #f3981a;
        --brand-700:#e88400;
        --ink:#0f172a;
        --muted:#6b7280;
        --line:#e5e7eb;
        --bg:#ffffff;
        --chip:#f3f4f6;
        --chip-ink:#111827;
        --focus: 0 0 0 4px rgba(243,152,26,.15);
        --radius: 14px;
        --radius-lg: 18px;
        --primary-color: #f89c1b;
        --primary-light: #b58038;
        --focus-ring-color: rgba(224,184,53,0.5);
        --border-color: #e5e7eb;
        --bg-light: #f9fafb;
    }

    .payment_methods {
        max-height: 300px;
        overflow-y: auto;
    }

    .page-wrapper {
        min-height: auto !important;
    }

    .shell {
        min-height: auto !important;
        overflow: visible;
    }

    body {
        overflow-x: hidden;
    }

    .payment_methods ul {
        max-height: 200px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) #f1f1f1;
    }

    .payment_methods ul::-webkit-scrollbar {
        width: 6px;
    }

    .payment_methods ul::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .payment_methods ul::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    input[type="radio"].sr-only:checked + div.block {
        background-color: #f3981a;
    }

    input[type="radio"].sr-only:checked + div.block + div.dot {
        transform: translateX(32px);
        background-color: white;
    }

    input[type="checkbox"].sr-only:checked + div.block {
        background-color: #f3981a;
    }

    input[type="checkbox"].sr-only:checked + div.block + div.dot {
        transform: translateX(32px);
        background-color: white;
    }

    input[type="checkbox"].sr-only:checked + div {
        background-color: #f3981a;
    }

    input[type="checkbox"].sr-only:checked + div + div.dot {
        transform: translateX(32px);
        background-color: white;
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 0.2em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }

    @keyframes  spinner-border {
        to { transform: rotate(360deg); }
    }

    .visually-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0,0,0,0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    .sortable-ghost {
        opacity: 0.4;
        background: #c8ebfb;
    }

    .sortable-chosen {
        background: #f0f9ff;
        border: 2px dashed #3b82f6;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, Noto Sans, "Helvetica Neue", sans-serif;
        color: var(--ink);
        background: #fafafa;
        line-height: 1.35;
    }

    .shell {
        max-width: 980px;
        margin: 32px auto;
        padding: 24px;
    }

    @media (max-width:768px) {
        .shell {
            padding: 16px;
        }
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        letter-spacing: .2px;
    }

    .brand svg {
        width: 32px;
        height: 32px;
    }

    .brand span {
        font-size: 26px;
    }

    @media (max-width:768px) {
        .brand {
            justify-content: center;
        }
        .brand span {
            font-size: 22px;
        }
    }

    .steps-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 14px 0 28px;
        border-bottom: 2px solid var(--line);
    }

    .step {
        position: relative;
        padding: 10px 0;
        font-weight: 600;
        color: #6b7280;
        flex: 1;
        text-align: center;
    }

    .step.active {
        color: var(--ink);
        border-bottom: 2px solid var(--brand);
        margin-bottom: -2px;
    }

    @media (max-width:768px) {
        .steps-wrap {
            flex-wrap: wrap;
        }
        .step {
            font-size: 14px;
        }
    }

    .card {
        background: var(--bg);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,.03);
    }

    @media (max-width:768px) {
        .card {
            padding: 16px;
        }
    }

    h1 {
        margin: 2px 0 18px;
        font-size: 34px;
    }

    @media (max-width:768px) {
        h1 {
            font-size: 28px;
            text-align: center;
        }
    }

    .categories {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .seg {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #e3e0e0;
        background: #fff;
        padding: 5px 16px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
    }

    .seg svg {
        width: 18px;
        height: 18px;
    }

    .seg.active {
        border-color: var(--brand);
        box-shadow: var(--focus);
        background: var(--brand);
    }

    @media (max-width:768px) {
        .seg {
            padding: 10px 14px;
            gap: 6px;
            font-size: 14px;
        }
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width:720px) {
        .grid {
            grid-template-columns: 1fr;
        }
        .categories {
            gap: 10px;
        }
    }

    label {
        display: block;
        font-weight: 700;
        margin: 16px 0 8px;
    }

    .field {
        position: relative;
    }

    .input, select, textarea {
        width: 100%;
        padding: 14px 14px;
        border: 1px solid #e3e0e0;
        outline: none;
        background: #fff;
    }

    .with-icon {
        padding-left: 40px;
    }

    .field .ico {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group .input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .unit {
        padding: 14px;
        border: 1.5px solid var(--line);
        border-left: 0;
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
        background: #f3f4f6;
        color: var(--chip-ink);
        font-weight: 600;
    }

    .deal {
        display: flex;
        align-items: center;
        gap: 22px;
        flex-wrap: wrap;
    }

    .toggle {
        position: relative;
        width: 54px;
        height: 30px;
        background: #e5e7eb;
        border-radius: 999px;
        cursor: pointer;
        transition: .2s;
    }

    .toggle:before {
        content: "";
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background: #fff;
        border-radius: 50%;
        transition: .2s;
        box-shadow: 0 1px 3px rgba(0,0,0,.15);
    }

    .toggle.on {
        background: var(--brand);
    }

    .toggle.on:before {
        left: 27px;
    }

    .deal label {
        font-weight: 600;
    }

    .radios {
        display: flex;
        align-items: center;
        gap: 22px;
    }

    .radio {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .radio input {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #9ca3af;
        border-radius: 50%;
        display: grid;
        place-items: center;
    }

    .radio input:checked {
        border-color: var(--brand);
    }

    .radio input:checked::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand);
    }

    .chips {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .chip {
        padding: 10px 14px;
        border-radius: 10px;
        background: var(--chip);
        color: var(--chip-ink);
        border: 1.5px solid var(--line);
        cursor: pointer;
        font-weight: 600;
    }

    .chip.active {
        background: #fff;
        border-color: var(--brand);
        box-shadow: var(--focus);
        background: var(--brand);
    }

    .row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width:720px) {
        .row {
            grid-template-columns: 1fr;
        }
    }

    .actions {
        margin-top: 22px;
    }

    .btn {
        width: 25%;
        padding: 16px 18px;
        border: none;
        border-radius: 14px;
        background: var(--brand);
        color: #fff;
        font-weight: 800;
        font-size: 18px;
        cursor: pointer;
        transition: .2s;
    }

    .btn:hover {
        background: var(--brand-700);
    }

    .btns {
        width: 100%;
        padding: 16px 18px;
        border: none;
        border-radius: 14px;
        background: var(--brand);
        color: #fff;
        font-weight: 800;
        font-size: 18px;
        cursor: pointer;
        transition: .2s;
    }

    .btns:hover {
        background: var(--brand-700);
    }

    .btn-secondary {
        background: #e5e7eb;
        color: var(--ink);
        width: auto;
        flex: 1;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .muted {
        color: var(--muted);
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .input-field, .input-select-field, .input-field-main, .input-field-readonly, .textarea-field {
        height: 3.5rem;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        color: #1f2937;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        width: 100%;
    }

    .textarea-field {
        height: 9rem;
        resize: vertical;
    }

    .input-field:focus, .textarea-field:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--focus-ring-color);
        background-color: white;
    }

    .input-field-readonly {
        background-color: var(--bg-light);
        cursor: not-allowed;
        border: 1px dashed #d1d5db;
    }

    .input-group-container {
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .currency-select-wrapper {
        position: relative;
        background-color: #f3f4f6;
        border-right: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-select-field {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        height: 100%;
        width: 100%;
        border: none;
        background-color: transparent;
        text-align: center;
        padding-right: 2rem;
        font-weight: 500;
    }

    .currency-select-wrapper::after {
        content: '▼';
        font-size: 0.6rem;
        color: #6b7280;
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .input-field-main {
        width: 100%;
        flex-grow: 1;
        border: none;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        padding: 0.75rem 1.25rem;
    }

    .input-group-container:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--focus-ring-color);
    }

    .input-group-container:focus-within .input-field-main:focus {
        box-shadow: none;
    }

    #advanced-form-container {
        background: #fffbfa;
        width: 100%;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .dot {
        transition: transform 0.3s ease, background-color 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    body {
        min-height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .page-wrapper {
        flex-grow: 1 !important;
        flex-shrink: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .page-wrapper > div:not(:first-child) {
        flex-grow: 1 !important;
        flex-shrink: 0 !important;
        min-height: 0 !important;
    }

    .page-wrapper .shell {
        flex-grow: 0 !important;
        min-height: auto !important;
        display: flex !important;
        flex-direction: column !important;
        padding-bottom: 20px !important;
    }

    footer {
        flex-shrink: 0 !important;
        width: 100% !important;
    }

    .tooltip-container {
        position: relative;
    }

    .tooltip-text {
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 10px;
        position: absolute;
        z-index: 10;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        width: 200px;
        font-size: 12px;
        white-space: nowrap;
    }

    .tooltip-container:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/post/add.blade.php ENDPATH**/ ?>