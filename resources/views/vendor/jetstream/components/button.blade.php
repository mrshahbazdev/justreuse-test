<?php
	$slug = request()->segment(1);
	$slug1 = request()->segment(2);
?>

<?php if($slug=='reset-password') { ?>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-2 py-2 pb-3 sm:py-3 sm:pb-4 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-sm sm:text-base md:text-lg font-bold rounded active:bg-green-500 focus:outline-none disabled:opacity-25 transition-all ease-linear duration-500 w-full']) }}>
    {{ $slot }}
</button>

<?php } elseif($slug1=='verify') { ?>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-2 py-2 pb-3 sm:py-3 sm:pb-4 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-sm sm:text-base md:text-lg font-bold rounded sm:rounded-xl active:bg-green-500 focus:outline-none disabled:opacity-25 transition-all ease-linear duration-500 w-full cursor-pointer']) }}>
    {{ $slot }}
</button>

<?php } else { ?>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-6 py-2 pb-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-sm font-bold uppercase rounded tracking-widest active:bg-green-500 focus:outline-none disabled:opacity-25 transition ease-in-out duration-700 sm:w-auto w-full']) }}>
    {{ $slot }}
</button>

<?php } ?>