<div>
    <style>
        .step { padding: 10px; border-bottom: 3px solid #ccc; color: #ccc; font-weight: bold; }
        .step.active { border-bottom-color: #28a745; color: #28a745; }
        .seg.active { background-color: #28a745; color: white; border-color: #28a745; }
    </style>
    <div class="shell p-4 sm:p-6 md:p-8 bg-white rounded-lg shadow-md">
        <header class="flex flex-col justify-between mb-6">
            <div class="steps-wrap flex gap-4 border-b pb-4">
                <div class="step flex-1 text-center {{ $currentStep == 1 ? 'active' : '' }}">
                    <span class="block text-sm text-gray-500">Step 1</span>
                    <span class="font-semibold">Basic Info</span>
                </div>
                <div class="step flex-1 text-center {{ $currentStep == 2 ? 'active' : '' }}">
                    <span class="block text-sm text-gray-500">Step 2</span>
                    <span class="font-semibold">Price & Details</span>
                </div>
            </div>
        </header>

        <form wire:submit.prevent="updatePost">
            @csrf

            {{-- STEP 1 --}}
            <div style="{{ $currentStep == 1 ? 'display:block;' : 'display:none;' }}">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Main Category</label>
                        <select wire:model="selectedParentCategory" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select a Category</option>
                            @foreach($parentCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                         @error('selectedParentCategory') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if(!empty($childCategories))
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sub-Category in {{ $parentCategory->title ?? '' }}</label>
                        <div class="categories grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 mt-2">
                            @foreach($childCategories as $child)
                            <button class="seg p-3 text-sm border rounded-md transition {{ $selectedChildCategory == $child->id ? 'active' : 'bg-gray-50 hover:bg-gray-100' }}" type="button" wire:click="selectChildCategory({{ $child->id }})">
                                {{ $child->title }}
                            </button>
                            @endforeach
                        </div>
                        @error('selectedChildCategory') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    
                    @if($selectedChildCategory)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Listing Title</label>
                        <input type="text" wire:model.lazy="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    @if($customFields->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4 border-t">
                            @foreach($customFields as $field)
                                 <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">{{ $field->name }} @if($field->required != '0') <span class="text-red-800">*</span> @endif</label>
                                    @php
                                        $fieldKey = $field->id . '_' . $field->form_field_name;
                                    @endphp
                                    @if(in_array($field->type, ["text", "number", "date"]))
                                        <input type="{{$field->type}}" wire:model.lazy="customFieldsData.{{ $fieldKey }}" class="block w-full p-2 border border-gray-300 rounded-md">
                                    @elseif(in_array($field->type, ["select", "radio-group"]))
                                        <select wire:model.lazy="customFieldsData.{{ $fieldKey }}" class="block w-full p-2 border border-gray-300 rounded-md">
                                             <option value="">Select {{ $field->name }}</option>
                                             @foreach(\App\Models\TblFieldsOption::where('field_id', $field->id)->get() as $option)
                                                <option value="{{ $option->value }}">{{ $option->key }}</option>
                                             @endforeach
                                        </select>
                                    @endif
                                    @error('customFieldsData.'.$fieldKey) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                 </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="actions mt-6 text-right">
                        <button class="btn bg-green-600 text-white hover:bg-green-700 px-6 py-2 rounded-md font-semibold" type="button" wire:click="nextStep">Continue</button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- STEP 2 --}}
            <div style="{{ $currentStep == 2 ? 'display:block;' : 'display:none;' }}">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" wire:model.lazy="price" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" required>
                            @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Currency</label>
                            <select wire:model="currency_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->short_code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model.lazy="description" rows="5" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm"></textarea>
                         @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700">Location</label>
                         <input type="text" wire:model.lazy="location" placeholder="e.g., Lahore, Pakistan" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                         @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($existingImages)
                    <div class="my-6">
                        <h2 class="text-lg font-semibold">Existing Images</h2>
                        <div class="flex flex-wrap gap-4 mt-2">
                            @foreach($existingImages as $index => $imagePath)
                            <div class="relative">
                                <img src="{{ Storage::url($imagePath) }}" class="h-24 w-24 object-cover rounded-lg border">
                                <button type="button" wire:click="removeExistingImage({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs font-bold">&times;</button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="my-6">
                         <label class="block text-sm font-medium text-gray-700">Upload New Images (Optional)</label>
                         <input type="file" wire:model="images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                         @error('images.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($images)
                    <div class="my-6">
                        <h2 class="text-lg font-semibold">New Images Preview</h2>
                        <div class="flex flex-wrap gap-4 mt-2">
                            @foreach($images as $index => $image)
                            <div class="relative">
                                <img src="{{ $image->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-lg border">
                                <button type="button" wire:click="removeNewImage({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs font-bold">&times;</button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="actions mt-8 flex justify-between">
                    <button class="btn bg-gray-200 text-gray-700 hover:bg-gray-300 px-6 py-2 rounded-md font-semibold" type="button" wire:click="previousStep">Back</button>
                    <button class="btn bg-green-600 text-white hover:bg-green-700 px-6 py-2 rounded-md font-semibold" type="submit">Update Post</button>
                </div>
            </div>
        </form>
    </div>
  <style>
     :root{
            --brand: #f3981a; /* primary orange */
            --brand-700:#e88400;
            --ink:#0f172a;      /* dark text */
            --muted:#6b7280;    /* secondary text */
            --line:#e5e7eb;     /* borders */
            --bg:#ffffff;       /* card bg */
            --chip:#f3f4f6;     /* chip bg */
            --chip-ink:#111827; /* chip text */
            --focus: 0 0 0 4px rgba(243,152,26,.15);
            --radius: 14px;
            --radius-lg: 18px;
        }
        /* Radio button wale toggle ke liye Sahi CSS */
input[type="radio"].sr-only:checked + div.block {
    background-color: #f3981a; /* Aapka brand color */
}

input[type="radio"].sr-only:checked + div.block + div.dot {
    transform: translateX(32px);
    background-color: white;
}

/* Checkbox wale toggle ke liye Sahi CSS (Instant Buy, etc.) */
input[type="checkbox"].sr-only:checked + div.block {
    background-color: #f3981a; /* Aapka brand color */
}

input[type="checkbox"].sr-only:checked + div.block + div.dot {
    transform: translateX(32px);
    background-color: white;
}
  	input[type="checkbox"].sr-only:checked + div {
    background-color: #f3981a; /* Aapka brand color */
}

input[type="checkbox"].sr-only:checked + div + div.dot {
    transform: translateX(32px); /* Dot ko move karne ke liye */
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

    @keyframes spinner-border {
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

    /* Sortable drag styles */
    .sortable-ghost {
        opacity: 0.4;
        background: #c8ebfb;
    }

    .sortable-chosen {
        background: #f0f9ff;
        border: 2px dashed #3b82f6;
    }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{
            margin:0; font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, Noto Sans, "Helvetica Neue", sans-serif;
            color:var(--ink); background:#fafafa; line-height:1.35;
        }

        .shell{max-width:980px; margin:32px auto; padding:24px;}
        @media (max-width:768px){.shell{padding:16px;}}

        /* Header */
        .brand{display:flex; align-items:center; gap:10px; font-weight:800; letter-spacing:.2px;}
        .brand svg{width:32px; height:32px}
        .brand span{font-size:26px}
        @media (max-width:768px){
            .brand{justify-content:center;}
            .brand span{font-size:22px;}
        }

        /* Stepper */
        .steps-wrap{display:flex; align-items:center; justify-content:space-between; margin:14px 0 28px; border-bottom: 2px solid var(--line);}
        .step{position:relative; padding:10px 0; font-weight:600; color:#6b7280; flex:1; text-align: center;}
        .step.active{color:var(--ink); border-bottom: 2px solid var(--brand); margin-bottom: -2px; }
        @media (max-width:768px) {
            .steps-wrap{flex-wrap: wrap;}
            .step{font-size: 14px;}
        }

        /* Card */
        .card{background:var(--bg); border:1px solid var(--line); border-radius:var(--radius-lg); padding:24px; box-shadow:0 2px 10px rgba(0,0,0,.03)}
        @media (max-width:768px){.card{padding:16px;}}


        h1{margin:2px 0 18px; font-size:34px}
        @media (max-width:768px){h1{font-size:28px; text-align:center;}}

        /* Category buttons */
        .categories{display:flex; gap:12px; flex-wrap:wrap}
        .seg{display:flex; align-items:center; gap:8px; border:1px solid #e3e0e0; background:#fff; padding:5px 16px; border-radius:5px; cursor:pointer; font-weight:600}
        .seg svg{width:18px;height:18px}
        .seg.active{border-color:var(--brand); box-shadow:var(--focus); background: var(--brand);}
        @media (max-width:768px){.seg{padding:10px 14px; gap:6px; font-size:14px;}}


        /* Grid */
        .grid{display:grid; grid-template-columns:1fr 1fr; gap:16px}
        @media (max-width:720px){
            .grid{grid-template-columns:1fr}
            .categories{gap:10px}
        }

        /* Field */
        label{display:block; font-weight:700; margin:16px 0 8px}
        .field{position:relative}
        .input, select, textarea{width:100%; padding:14px 14px;  border:1px solid #e3e0e0; outline:none; background:#fff; }
        /*.input:focus, select:focus, textarea:focus{border-color:var(--brand); box-shadow:var(--focus)}*/
        .with-icon{padding-left:40px}
        .field .ico{position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#6b7280}
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


        /* Toggle + Radios */
        .deal{display:flex; align-items:center; gap:22px; flex-wrap:wrap}
        .toggle{position:relative; width:54px; height:30px; background:#e5e7eb; border-radius:999px; cursor:pointer; transition:.2s}
        .toggle:before{content:""; position:absolute; top:3px; left:3px; width:24px; height:24px; background:#fff; border-radius:50%; transition:.2s; box-shadow:0 1px 3px rgba(0,0,0,.15)}
        .toggle.on{background:var(--brand)}
        .toggle.on:before{left:27px}
        .deal label{font-weight:600}
        .radios{display:flex; align-items:center; gap:22px}
        .radio{display:flex; align-items:center; gap:8px; cursor:pointer}
        .radio input{appearance:none; width:18px; height:18px; border:2px solid #9ca3af; border-radius:50%; display:grid; place-items:center}
        .radio input:checked{border-color:var(--brand)}
        .radio input:checked::before{content:""; width:8px; height:8px; border-radius:50%; background:var(--brand)}

        /* Chips */
        .chips{display:flex; gap:10px; flex-wrap:wrap}
        .chip{padding:10px 14px; border-radius:10px; background:var(--chip); color:var(--chip-ink); border:1.5px solid var(--line); cursor:pointer; font-weight:600}
        .chip.active{background:#fff; border-color:var(--brand); box-shadow:var(--focus); background: var(--brand); }

        /* Helper row */
        .row{display:grid; grid-template-columns:1fr 1fr; gap:16px}
        @media (max-width:720px){.row{grid-template-columns:1fr}}

        /* Footer */
        .actions{margin-top:22px}
        .btn{width:25%; padding:16px 18px; border:none; border-radius:14px; background:var(--brand); color:#fff; font-weight:800; font-size:18px; cursor:pointer; transition:.2s;}
        .btn:hover{background:var(--brand-700)}
        .btns{width:100%; padding:16px 18px; border:none; border-radius:14px; background:var(--brand); color:#fff; font-weight:800; font-size:18px; cursor:pointer; transition:.2s;}
        .btns:hover{background:var(--brand-700)}
        .btn-secondary {
            background: #e5e7eb;
            color: var(--ink);
            width: auto;
            flex: 1;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }

        .muted{color:var(--muted)}

        /* Submitted page styles */
        .completed-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 32px;
        }
        .completed-page .icon {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #10b981; /* Tailwind green-500 */
            border-radius: 50%;
            margin-bottom: 24px;
        }
        .completed-page h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .completed-page p {
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 24px;
        }
        .completed-page .my-ads-btn {
            background-image: linear-gradient(to top, #f59e0b, #f97316);
            width: 100%;
            padding: 16px 18px;
            border: none;
            border-radius: 14px;
            color: #fff;
            font-weight: 800;
            font-size: 18px;
            cursor: pointer;
            transition:.2s;
        }
        @media (max-width:768px){.completed-page .my-ads-btn {width: auto;}}
        /* ===== Upload Photos Redesign like Screenshot ===== */

        .post_add_img_upload .input-images {
          position: relative;
          border: 2px dashed #9cc0e5;   /* light blue dashed border */
          background: #fff;
          border-radius: 6px;
          min-height: 220px; /* to look spacious */
          padding: 20px 30px;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          transition: border-color .3s ease;
        }

        .post_add_img_upload .input-images:hover {
          border-color: #4a90e2;
          background: #f9fcff;
        }

        /* Center content */
        .post_add_img_upload .input-images .absolute {
          position: static; /* override absolute */
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
        }

        /* Upload icon */
        .post_add_img_upload .input-images .fa-plus {
          display: none; /* hide old plus icon */
        }

        /* Upload button style */
        .post_add_img_upload .input-images p {
          background: #28a745;          /* green */
          color: #fff;
          font-size: 16px;
          font-weight: 600;
          padding: 10px 20px;
          border-radius: 4px;
          cursor: pointer;
          margin-bottom: 8px;
          transition: background .25s ease;
        }

        .post_add_img_upload .input-images p:hover {
          background: #218838; /* darker green */
        }

        /* Small note under button */
        .post_add_img_upload .input-images::after {
          content: "(Max limit 5 MB per image)";
          font-size: 13px;
          color: #777;
          margin-top: 5px;
        }

        /* Info bullets (custom styling) */
        .upload-guidelines {
          margin-top: 15px;
          width: 100%;
          display: grid;
          padding: 20px;
          grid-template-columns: 1fr 1fr; /* two columns like image */
          gap: 12px 25px;
        }

        .upload-guidelines li {
          list-style: none;
          font-size: 14px;
          color: #444;
          line-height: 1.4;
          position: relative;
          padding-left: 24px;
        }

        .upload-guidelines li::before {
          content: "✔";
          color: #28a745;
          font-weight: bold;
          position: absolute;
          left: 0;
          top: 0;
        }
    /* === Package Dropdown Redesign === */

/* Parent box styling */
.w-full.px-4 {
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #fff;
  padding: 12px 16px;
  position: relative;
  display: flex;
  gap: 5px;
}

/* Hide radio inputs */
.package_type {
  display: none;
}

/* Each label becomes dropdown option */
.w-full.px-4 label {
  display: block;
  padding: 12px 14px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  margin-bottom: 8px;
  cursor: pointer;
  position: relative;
  font-size: 16px;
  font-weight: 600;
  transition: all .2s ease;
}

/* Hover effect */
.w-full.px-4 label:hover {
  background: #f3f4f6;
  border-color: #cbd5e1;
}

/* Selected (checked) option */
.package_type:checked + .block + .dot,
.package_type:checked ~ span,
.package_type:checked ~ small {
  /* keep for compatibility */
}

.package_type:checked ~ span,
.package_type:checked ~ small,
.package_type:checked ~ div,
.package_type:checked ~ .dot {
  /* no direct siblings, so we need wrapper style */
}

.w-full.px-4 input.package_type:checked + div {
  background: #22c55e !important; /* green */
  color: #fff;
}

/* Green highlight for selected label */
.package_type:checked ~ span,
.package_type:checked ~ .tooltip {
  color: #22c55e !important;
}

.w-full.px-4 label:has(input.package_type:checked) {
  background: #e8fdf3;
  border-color: #22c55e;
  color: #14532d;
}

/* Small info badge */
.w-full.px-4 label small {
  font-weight: 500;
  margin-left: 6px;
}

/* Price text */
.w-full.px-4 label span {
  font-size: 14px;
  font-weight: 500;
  color: #444;
}
/* Container */
ul.sm\:inline-flex {
  display: flex;
  flex-wrap: wrap;
}

/* Card wrapper */
ul.sm\:inline-flex li {
  flex: 1 1 250px;
}

/* Label as card */
ul.sm\:inline-flex label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 8px 15px;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  background: #ffffff;
  cursor: pointer;
  transition: all .25s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  position: relative;
}

/* Hover + active */
ul.sm\:inline-flex label:hover {
  border-color: #cbd5e1;
  background: #f9fafb;
}

ul.sm\:inline-flex input:checked ~ label {
  border-color: #22c55e;
  box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
}

/* Icon Style */
.option-icon {
  font-size: 22px;
  color: #6b7280;
  flex-shrink: 0;
}

ul.sm\:inline-flex input:checked ~ label .option-icon {
  color: #22c55e;
}

/* Text */
.option-text {
  flex: 1;
}
.option-text h4 {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}
.option-text p {
  font-size: 13px;
  color: #6b7280;
  margin-top: 2px;
}

/* Toggle Switch */
.toggle-wrapper {
  position: relative;
}
.toggle-line {
  width: 48px;
  height: 26px;
  background: #d1d5db;
  border-radius: 9999px;
  transition: background .3s;
}
.toggle-dot {
  width: 22px;
  height: 22px;
  background: #9ca3af;
  border-radius: 50%;
  position: absolute;
  top: 2px;
  left: 2px;
  transition: all .3s ease;
}

/* Checked State */
ul.sm\:inline-flex input:checked ~ label .toggle-line {
  background: #22c55e;
}
ul.sm\:inline-flex input:checked ~ label .toggle-dot {
  transform: translateX(22px);
  background: #ffffff;
}
/* ---------- FORM CONTAINER ---------- */
form .form-section {
  margin-bottom: 2rem;
}

/* ---------- LABELS ---------- */
form label {
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 8px;
  display: block;
  letter-spacing: .2px;
}

/* ---------- INPUTS & TEXTAREA ---------- */
form input[type="text"],
form input[type="number"],
form select,
form textarea {
  width: 100%;
  padding: 14px 18px;
  font-size: 15px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: #ffffff;
  color: #111827;
  transition: all 0.25s ease;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);
}

/* Placeholder style */
form input::placeholder,
form textarea::placeholder {
  color: #9ca3af;
  font-size: 14px;
}

/* ---------- FOCUS STATES ---------- */
form input:focus,
form select:focus,
form textarea:focus {
  outline: none;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
}

/* ---------- SELECT ---------- */
form select {
  background: #f9fafb url("data:image/svg+xml,%3Csvg fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 12px center;
  background-size: 18px;
  padding-right: 40px;
  cursor: pointer;
}

/* ---------- TEXTAREA ---------- */
form textarea {
  resize: none;
  min-height: 140px;
  line-height: 1.5;
}

/* ---------- ERROR ---------- */
form .alert-danger {
  margin-top: 6px;
  font-size: 14px;
  font-weight: 500;
  padding: 6px 10px;
  background: #fef2f2;
  border: 1px solid #fca5a5;
  border-radius: 8px;
}

/* ---------- FLEX ROW (PRICE + CITY) ---------- */
.flex-row-premium {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.flex-row-premium > div {
  flex: 1 1 300px;
}
  
/*Upload Guidelines*/  
  .upload-guidelines {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
  padding-left: 1rem;         /* keeps bullets inside */
  font-size: 0.875rem;        /* 14px on mobile */
  line-height: 1.5rem;
  color: #6b7280;             /* gray-500 */
}

@media (min-width: 640px) {   /* sm breakpoint */
  .upload-guidelines {
    font-size: 1rem;          /* 16px */
    padding-left: 1.5rem;
  }
}

.upload-guidelines li {
  margin-bottom: 0.25rem;     /* spacing between items */
}

@media (max-width: 767px) {
    .upload-guidelines li{
        font-size: 13px;
        padding: 4px;
        line-height: 1;
        margin-top: -20px;
        margin-bottom: -20px;
        width: 130px;
    }
}
  /* ==========================================================================
   1. Design Tokens & Global Styles
   ========================================================================== */

:root {
    /* Colors */
    --clr-primary: #4361EE;
    --clr-primary-dark: #3A56D4;
    --clr-primary-light: #EFF3FF;
    --clr-secondary: #06D6A0;
    --clr-accent: #FF6B6B;
    --clr-warning: #FFD166;
    --clr-dark: #1E1E2C;
    --clr-text-body: #495057;
    --clr-text-muted: #6C757D;
    --clr-border: #DEE2E6;
    --clr-surface: #FFFFFF;
    --clr-background: #F8F9FA;

    /* Gradients */
    --grad-primary: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-primary-dark) 100%);
    --grad-secondary: linear-gradient(135deg, var(--clr-secondary) 0%, #05B384 100%);
    --grad-warning: linear-gradient(135deg, var(--clr-warning) 0%, #FFB800 100%);
    --grad-header: linear-gradient(90deg, var(--clr-primary) 0%, var(--clr-secondary) 100%);
    --grad-text: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-dark) 100%);

    /* Sizing & Radius */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --radius-full: 50px;

    /* Shadows */
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
    --shadow-xl: 0 12px 32px rgba(0, 0, 0, 0.15);
    --shadow-focus: 0 0 0 4px rgba(67, 97, 238, 0.15);

    /* Transitions */
    --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    background-color: var(--clr-background);
    color: var(--clr-text-body);
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}


/* ==========================================================================
   2. Layout & Typography
   ========================================================================== */

.shell {
    max-width: 1200px;
    margin-inline: auto;
    padding-inline: 24px;
}

.c-panel {
    background: var(--clr-surface);
    border-radius: var(--radius-xl);
    padding: 40px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--clr-border);
    margin-bottom: 32px;
    position: relative;
}

h1 {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 24px;
    background: var(--grad-text);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.2;
}

h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--clr-dark);
    position: relative;
    padding-left: 20px;
}

h2::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 80%;
    width: 6px;
    background: var(--clr-primary);
    border-radius: 3px;
}


/* ==========================================================================
   3. Component: Header
   ========================================================================== */

.c-header {
    background: var(--clr-surface);
    border-radius: var(--radius-xl);
    padding: 28px 32px;
    margin-bottom: 32px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--clr-border);
    position: relative;
    overflow: hidden;
}

.c-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--grad-header);
}

/* ==========================================================================
   4. Component: Stepper
   ========================================================================== */

.c-stepper {
    display: flex;
    justify-content: center;
    gap: 60px;
    margin-block: 32px;
    position: relative;
    padding-inline: 20px;
}

.c-stepper::before {
    content: '';
    position: absolute;
    top: 13px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    height: 3px;
    background: var(--clr-border);
    z-index: 1;
}

.c-stepper__step {
    position: relative;
    font-weight: 500;
    color: var(--clr-text-muted);
    text-align: center;
    flex: 1;
    max-width: 200px;
    z-index: 2;
    background: var(--clr-surface);
    padding-top: 24px;
}

.c-stepper__step::before {
    content: '';
    width: 16px;
    height: 16px;
    background: var(--clr-border);
    border-radius: 50%;
    position: absolute;
    top: 5px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
    transition: var(--transition-base);
    border: 3px solid var(--clr-surface);
    box-shadow: var(--shadow-sm);
}

.c-stepper__step.is-active {
    color: var(--clr-primary);
    font-weight: 700;
}

.c-stepper__step.is-active::before {
    background: var(--clr-primary);
    box-shadow: 0 0 0 4px var(--clr-primary-light);
    transform: translateX(-50%) scale(1.1);
}

/* ==========================================================================
   5. Component: Forms
   ========================================================================== */

.c-form-control {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid var(--clr-border);
    border-radius: var(--radius-md);
    font-size: 16px;
    transition: var(--transition-base);
    background: var(--clr-surface);
    font-family: inherit;
    color: var(--clr-dark);
}

.c-form-control:focus {
    outline: none;
    border-color: var(--clr-primary);
    box-shadow: var(--shadow-focus);
    transform: translateY(-1px);
}

.c-form-label {
    font-weight: 600;
    color: var(--clr-dark);
    margin-bottom: 8px;
    display: block;
    font-size: 15px;
}

.c-form-label__required {
    color: var(--clr-accent);
}

textarea.c-form-control {
    min-height: 140px;
    resize: vertical;
}

select.c-form-control {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 20px;
    padding-right: 52px;
    appearance: none;
}

/* Toggle Switch */
.c-toggle {
    position: relative;
    width: 64px;
    height: 34px;
}

.c-toggle__input {
    opacity: 0;
    width: 0;
    height: 0;
}

.c-toggle__slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: var(--clr-border);
    border-radius: 17px;
    transition: var(--transition-base);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.c-toggle__slider::before {
    content: '';
    position: absolute;
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    border-radius: 50%;
    transition: var(--transition-base);
    box-shadow: var(--shadow-sm);
}

.c-toggle__input:checked + .c-toggle__slider {
    background-color: var(--clr-primary);
}

.c-toggle__input:checked + .c-toggle__slider::before {
    transform: translateX(30px);
}

/* ==========================================================================
   6. Component: Buttons & Badges
   ========================================================================== */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 18px 32px;
    border: 2px solid transparent;
    border-radius: var(--radius-lg);
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition-base);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
}

.btn--primary {
    background: var(--grad-primary);
    color: var(--clr-surface);
    box-shadow: var(--shadow-md);
}

.btn--primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.btn--secondary {
    background: var(--clr-surface);
    color: var(--clr-text-muted);
    border-color: var(--clr-border);
}

.btn--secondary:hover {
    background: var(--clr-background);
    border-color: #CED4DA;
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

/* ==========================================================================
   7. Component: File Uploader
   ========================================================================== */

.c-file-uploader {
    border: 3px dashed var(--clr-border);
    border-radius: var(--radius-xl);
    background: var(--clr-background);
    transition: var(--transition-base);
    padding: 60px 40px;
    text-align: center;
    cursor: pointer;
}

.c-file-uploader:hover,
.c-file-uploader.is-dragging {
    border-color: var(--clr-primary);
    background: var(--clr-primary-light);
    transform: translateY(-2px);
}

/* ==========================================================================
   8. Component: Advanced Fields Section
   ========================================================================== */

.c-advanced-fields {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: var(--radius-xl);
    padding: 32px;
    margin-block: 32px;
    border: 1px solid var(--clr-border);
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}

.c-advanced-fields::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--grad-header);
}

.c-advanced-fields__title {
    font-size: 22px;
    font-weight: 700;
    color: var(--clr-dark);
    margin: 0 0 28px 0;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--clr-border);
}

.c-advanced-fields__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 28px;
}

.c-advanced-fields__group {
    background: var(--clr-surface);
    border-radius: var(--radius-lg);
    padding: 24px;
    border: 1px solid var(--clr-border);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
}

.c-advanced-fields__group:hover,
.c-advanced-fields__group:focus-within {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--clr-primary);
}

/* Custom Radio Buttons in this section */
.c-radio-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 8px;
}

.c-radio {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border: 2px solid var(--clr-border);
    border-radius: var(--radius-md);
    background: var(--clr-background);
    cursor: pointer;
    transition: var(--transition-base);
}

.c-radio:hover {
    border-color: var(--clr-primary);
    background: var(--clr-primary-light);
}

.c-radio input[type="radio"] {
    appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid var(--clr-text-muted);
    border-radius: 50%;
    margin-right: 12px;
    position: relative;
    transition: var(--transition-base);
    flex-shrink: 0;
}

.c-radio input[type="radio"]:checked {
    border-color: var(--clr-primary);
    background: var(--clr-primary);
}

.c-radio input[type="radio"]:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: var(--clr-surface);
    border-radius: 50%;
}

.c-radio__label {
    font-size: 14px;
    font-weight: 500;
    color: var(--clr-text-muted);
    transition: var(--transition-base);
}

.c-radio:hover .c-radio__label,
.c-radio input[type="radio"]:checked + .c-radio__label {
    color: var(--clr-primary);
    font-weight: 600;
}

/* ==========================================================================
   9. States (Loading, Error, Success)
   ========================================================================== */

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--clr-border);
    border-top-color: var(--clr-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.c-form-error-text {
    color: var(--clr-accent);
    font-size: 14px;
    font-weight: 500;
    margin-top: 8px;
    padding: 8px 12px;
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: var(--radius-sm);
}

.alert--success {
    background: #E8F8F3;
    color: #0A5C46;
    border: 1px solid #A7F3D0;
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 24px;
    font-weight: 500;
}

/* ==========================================================================
   10. Responsive Design
   ========================================================================== */

@media (max-width: 768px) {
    .shell {
        padding-inline: 16px;
    }

    .c-panel {
        padding: 24px;
    }

    /* Stack stepper on mobile */
    .c-stepper {
        flex-direction: column;
        gap: 16px;
        padding: 0;
    }

    .c-stepper::before {
        display: none;
    }

    .c-stepper__step {
        max-width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border-radius: var(--radius-md);
        background: var(--clr-background);
    }
    
    .c-stepper__step::before {
        position: static;
        transform: none;
        flex-shrink: 0;
    }

    .c-advanced-fields__grid {
        grid-template-columns: 1fr;
    }

    .c-advanced-fields {
        padding: 24px;
    }
}
:root {
            --primary-color: #f89c1b; /* Emerald 600 - Slightly brighter */
            --primary-light: #b58038; /* Emerald 400 (Used for active background/toggles) */
            --focus-ring-color: rgba(224,184,53,0.5); /* Semi-transparent Primary for focus glow */
            --border-color: #e5e7eb; /* Gray 200 - Lighter border for clean look */
            --bg-light: #f9fafb; /* Gray 50 */
        }

        

        /* General Input/Select Styling */
        .form-label {
            display: block;
            font-weight: 600; /* Semibold for prominence */
            color: #1f2937; /* Dark gray text */
            margin-bottom: 0.75rem; /* Better spacing */
            font-size: 1rem;
        }

        .input-field, .input-select-field, .input-field-main, .input-field-readonly, .textarea-field {
            height: 3.5rem; /* Consistent height for inputs */
            padding: 0.75rem 1.25rem; /* px-5 py-3 */
            font-size: 1rem;
            color: #1f2937;
            border: 1px solid var(--border-color); /* Lighter border */
            border-radius: 0.5rem; /* Rounded corners */
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03); /* Lighter initial shadow */
            width: 100%; /* Ensure full width when used standalone */
        }
        
        /* Specific height for textarea */
        .textarea-field {
            height: 9rem; /* h-36 equivalent */
            resize: vertical;
        }

        /* Specific Input Styling for Focus/Interaction */
        .input-field:focus, .textarea-field:focus {
            outline: none;
            border-color: var(--primary-color); /* Border turns primary color */
            box-shadow: 0 0 0 3px var(--focus-ring-color); /* Cleaner focus glow */
            background-color: white;
        }

        .input-field-readonly {
            background-color: var(--bg-light);
            cursor: not-allowed;
            border: 1px dashed #d1d5db; /* Gray 300 dashed border */
        }

        /* Combined Price/Currency Field Styling */
        .input-group-container {
            /* Ensures the flex layout works */
            border-radius: 0.5rem;
            overflow: hidden; /* Important for rounding the entire group */
            border: 1px solid var(--border-color); /* Apply border to container */
        }

        .currency-select-wrapper {
            /* 4/12 width is 33.33% */
            position: relative;
            background-color: #f3f4f6; /* Light gray background for the currency selector */
            border-right: 1px solid var(--border-color); /* Separator line */
            display: flex; /* Ensure select fills the span */
            align-items: center;
            justify-content: center;
        }

        .input-select-field {
            /* Override default select styling */
            appearance: none; /* Remove native dropdown arrow */
            -webkit-appearance: none;
            -moz-appearance: none;
            height: 100%;
            width: 100%;
            border: none;
            background-color: transparent;
            text-align: center;
            padding-right: 2rem; /* Space for custom arrow */
            font-weight: 500;
        }

        /* Custom Arrow for Select Dropdown (Advanced Look) */
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
            /* Price input part */
            width: 100%;
            flex-grow: 1;
            border: none; /* Remove individual border */
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            padding: 0.75rem 1.25rem;
        }

        /* Focus state for the combined group */
        .input-group-container:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--focus-ring-color);
        }

        /* Specific focus for the main input inside the group */
        .input-group-container:focus-within .input-field-main:focus {
             /* Remove inner focus outline */
            box-shadow: none;
        }

        /* Container Card Styling */
        #advanced-form-container {
            background: #fffbfa;
            width: 100%;
            border-radius: 1rem;
            /* Enhanced Shadow for premium look */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        /* Toggle Switch Styling (Use primary-color) */
        input[type="checkbox"].sr-only:checked ~ .block {
            background-color: var(--primary-color) !important; /* Base color when checked */
        }

        input[type="checkbox"].sr-only:checked ~ .dot {
            transform: translateX(1.75rem); /* Move dot for w-16 switch */
            background-color: white !important;
        }

        /* Specific styling for larger screens if using lg:w-20 */
        @media (min-width: 1024px) {
            input[type="checkbox"].sr-only:checked ~ .dot {
                transform: translateX(2.25rem); /* Adjusted move for lg:w-20 switch */
            }
        }

        .dot {
            transition: transform 0.3s ease, background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Added shadow to dot */
        }

        /* Custom Button Styling */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: #f3f4f6; /* Gray 100 */
            color: #4b5563; /* Gray 600 */
            border: 1px solid #e5e7eb;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #e5e7eb; /* Gray 200 */
            transform: translateY(-1px); /* Subtle lift */
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
        }

        .btn:hover:not(:disabled) {
            background-color: #047857; /* Slightly darker emerald on hover */
            transform: translateY(-1px); /* Subtle lift */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
  		/* 1. Body: Base Flex Container (Keep this as is - this is correct) */
body {
    min-height: 100vh !important;
    display: flex !important;
    flex-direction: column !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* 2. .page-wrapper: Stretches to fill ALL space between header and footer (Keep this as is - this is correct) */
.page-wrapper {
    flex-grow: 1 !important;
    flex-shrink: 0 !important;
    display: flex !important;
    flex-direction: column !important;
}

/* 3. Target the main Livewire Content Containers (Keep flex-grow here) */
/* Yeh containers hain jo asli mein stretch ho rahe hain. */
.page-wrapper > div:not(:first-child) { 
    flex-grow: 1 !important;
    flex-shrink: 0 !important;
    min-height: 0 !important;
}

/* 4. Livewire container ke andar ka .shell: (THE KEY CHANGE IS HERE) */
/* Yahan se over-stretching ho rahi hai. Hum yahan se flex-grow hatayenge. */
.page-wrapper .shell {
    /* ❌ REMOVE: flex-grow: 1 !important; -- Yeh line hatane se space kam hona chahiye. */
    flex-grow: 0 !important; /* Ya ise 0 set kardo */
    
    /* min-height: auto rakho, ya isse bhi hata do. */
    min-height: auto !important; 
    
    /* Display: flex; ki zaroorat sirf tab hai agar aapko shell ke andar ke elements ko bhi stretch karna ho. 
       Agar nahi, to isse bhi hata sakte hain. Abhi ke liye rakhte hain. */
    display: flex !important;
    flex-direction: column !important;
    
    /* ❗ Padding-bottom ko kam karke ya zero karke dekho */
    padding-bottom: 20px !important; 
}

/* 5. Footer: Fixed at the bottom (Keep this as is - this is correct) */
footer {
    flex-shrink: 0 !important;
    width: 100% !important;
}
</style>
</div>
