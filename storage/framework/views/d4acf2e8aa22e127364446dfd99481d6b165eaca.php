<nav class="bg-white top-0 absolute1 z-50 w-full flex flex-wrap items-center justify-between px-2 py-3 navbar-expand-lg">
	<div class="container lg:px-4 mx-auto flex  items-center justify-between">
		<!-- <div class="w-full relative flex justify-between lg:w-auto lg:static lg:block lg:justify-start">

			<button class="cursor-pointer text-xl leading-none px-3 py-1 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none  focus:outline-none hidden" type="button" onclick="toggleNavbar('example-collapse-navbar')">
				<i class="fas fa-bars"></i>
			</button>
		</div> -->
		<!--search-->
		<div class="input-group flex w-full lg:w-4/12 lg:justify-between relative">
			<input type="search" class="w-full lg:w-full form-control px-2 py-2 bg-gray-100 rounded outline-none" placeholder="Search..." id="top-searchs">
			<div class="absolute top-full bg-white left-0 right-0 z-40">
				<ul class="z-20 top-full left-0 bg-white shadow-md" id="filtered_search"></ul>
			</div>
			<span class="mdi mdi-magnify search-icon"></span>
			<button class=" input-group-text btn btn-primary bg-green-500 text-white rounded-l-none rounded px-3 outline-none shadow" type="submit">Search</button>
		</div>
		<!--end-->
		<div class="lg:flex flex-grow fixed lg:relative w-full left-0 -top-22 lg:top-0 mt-44 lg:mt-0 right-0 items-center bg-white lg:bg-transparent shadow lg:shadow-none hidden z-10" id="example-collapse-navbar">



			<?php
			$userImg = auth()->user()->profile_photo_path;

			if (!empty($userImg)) {
				$userImgUrl = URL::asset('storage/' . $userImg);
			} else {
				$userImgUrl = URL::asset('storage/profile-avatar.jpg');
			}
			?>
			<ul class="flex flex-col lg:flex-row list-none lg:ml-auto">

				<li class="flex items-center">
					<form method="POST" action="<?php echo e(route('logout')); ?>">
						<?php echo csrf_field(); ?>
						<a href="<?php echo e(route('logout')); ?>" class="lg:text-gray-800 lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1" fa-lg=""></i> Logout</a>
					</form>
				</li>
				<li class="inline-block relative mr-10">
					<a class="lg:text-gray-800 lg:hover:text-yellow-300 text-gray-800 px-3 py-4 lg:py-2 flex items-center text-sm" href="#pablo" onclick="openDropdown(event,'demo-pages-dropdown')">
						<img src="<?php echo e($userImgUrl); ?>" class="bg-white rounded-full mr-1" width="30" height="30" alt="your profile" />
						<?php echo e(auth()->user()->name); ?> <i class="fa fa-angle-down ml-2" fa-lg=""></i></a>
					<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg hidden" id="demo-pages-dropdown" data-popper-placement="bottom-start" style="width:150px;position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 38px);">
						<a href="<?php echo e(URL::to('admin/my-profile')); ?>" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800">My Profile</a>
					</div>
				</li>
				<li class="hidden flex items-center">&nbsp;</li>
			</ul>
		</div>
	</div>
</nav>
<script>
	var getserach = document.getElementById('top-searchs');
	// console.log(getserach);

	jQuery(function($) {
		var searchText = '';
		$("#top-searchs").on("keyup", function(e) {
			searchText = this.value;
			if (searchText.length > 0) {
				$("#filtered_search").html('');
			}
		});

		$("#top-searchs").autocomplete({
			minLength: 2,
			source: function(request, response) {
				console.log(request);
				jQuery.ajax({
					url: "<?php echo e(URL::to('/dashboard-search')); ?>",
					dataType: "json",
					data: {
						search: request.term,
					},
					success: function(data) {

						// // Assuming you have fetched the data from the server using AJAX and stored it in the 'data' variable
						var outputAr = data;

						// Initialize an empty string to store the HTML
						var li = '';

						// Loop through the data array
						$.each(outputAr, function(currentIndex, currentElem) {
							// Access the 'name' and 'url' properties of each element
							var name = currentElem.name;
							var url = currentElem.url;

							// Create a list item with the name as the text and the url as the href attribute
							li += '<a href="' + url + '"><li class="border-b border-gray-200  block px-4 py-3 hover:text-orange-500 text-base  cursor-pointer ">' + name + '</li></a>';
						});

						// Append the generated HTML to the element with id 'filtered_search'
						$("#filtered_search").html('');
						$("#filtered_search").append(li);


					}
				});
			}
		})
	});
</script><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/dashboard/admin-dropdown.blade.php ENDPATH**/ ?>