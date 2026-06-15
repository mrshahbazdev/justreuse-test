<x-admin-layout>
    <div class="relative md:pt-28 pb-32 pt-12">
        <div class="px-4 md:px-10 mx-auto w-full">
            <div class="bg-white p-5 rounded-md">
              
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-sm my-3"><a href="{{route('features-map-show')}}">Back</a></button>
           
                <!-- Form to add features mapping -->
                <form action="{{ route('features-map-update') }}" method="POST">
                    @csrf

                    <!-- Category Dropdown -->
                    <label class="inline-block w-2/12">Category</label>
                    <select id="selected_category" name="cat_id" class="selectpicker w-2/4 h-14 px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none placeholder-black mb-3">
                        <option hidden disabled selected value="">{{ __('p_myads.please select') }}</option>
                        <?php
                        $traverse = function ($categorylist, $prefix = '') use (&$traverse,$featuredata) {
                            foreach ($categorylist as $category) {
                                $isparent = (is_null($category->parent_id)) ? "disabled" : "";

                                $isselected = (($featuredata->cat_id == $category->id )) ? "selected" : "";
                                $catname = $prefix . '| ' . $category->title;
                                $catid = $category->id;
                                echo '<option value="' . $catid . '"  ' . $isparent . ' ' . $isselected . '>' . $catname . '</option>';
                                $traverse($category->children, $prefix . '--');
                            }
                        };
                        $traverse($categorylist);
                        ?>
                    </select>
                    <!-- Category validation error -->
                    @error('cat_id')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror

                    <!-- Feature Title and Items (Dynamic) -->
                    <div id="feature-rows">
                        <div class="feature-row">
                            <div class="mb-3">
                            <div class="w-2/12 inline-block">Feature Title</div>
                                <div class="inline-block w-2/4">
                                    <input type="text" name="feature_title" value="{{$featuredata->features_title}}" class="form-input rounded-md shadow-sm block mt-1 w-full">
                                </div>
                            </div>
                            <div class="mb-3">
                            <div class="inline-block w-2/12 align-top">Feature Items</div>
                                <div class="inline-block w-2/4">
                                    <textarea name="feature_items" value="{{$featuredata->features_items}}" class="form-input rounded-md shadow-sm block mt-1 w-full">{{$featuredata->features_items}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation errors for dynamic fields -->
                    @error('feature_title')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                    @error('feature_items')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror

                    <!-- Submit Button -->
                    <div class="text-center">
                        <input type="hidden" name="id" value="{{$featuredata->id}}">
                        <button type="submit" class="bg-green-500 mt-2 text-white p-2">Update Map</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

 
</x-admin-layout>
