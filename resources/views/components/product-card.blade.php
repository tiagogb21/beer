<div
    class="box-border relative flex flex-col items-start p-5 overflow-hidden bg-white rounded-md cursor-pointer group ltr:pr-0 rtl:pl-0 md:pb-1"
    role="button"
    title="{{ $product['name'] }}"
>
<livewire:pages.favorite :$product />
    <div class="flex mb-3 md:mb-3.5 pb-0">
        <span
            style="box-sizing: border-box; display: inline-block; overflow: hidden; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px; position: relative; max-width: 100%;">
            <span>
                <img alt="" aria-hidden="true"
                    src={{ asset('assets/red-shoe.jpg') }}
                    style="display: block; max-width: 100%; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px;">
            </span>
            <img
                alt="Armani Wide-Leg Trousers"
                src={{ asset('assets/red-shoe.jpg') }} decoding="async"
                data-nimg="intrinsic"
                class="object-cover transition duration-150 ease-linear transform bg-gray-300 rounded-md rounded-s-md group-hover:scale-105"
                srcset={{ asset('assets/red-shoe.jpg') }}
                style="position: absolute; inset: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%;"></span>
        <div
            class="absolute top-3.5 md:top-5 3xl:top-7 ltr:left-3.5 rtl:right-3.5 ltr:md:left-5 rtl:md:right-5 ltr:3xl:left-7 rtl:3xl:right-7 flex flex-col gap-y-1 items-start">
        </div>
    </div>
    <div class="w-full p-2 overflow-hidden ltr:pl-0 rtl:pr-0">
        <h2
            class="truncate mb-1 font-semibold md:mb-1.5 text-sm sm:text-base md:text-sm lg:text-base xl:text-lg text-heading">
            {{ $product['name'] }}</h2>
        <p class="text-body text-xs lg:text-sm leading-normal xl:leading-relaxed max-w-[250px] truncate">
            {{ $product['description'] }}</p>
        <div
            class="font-semibold text-sm sm:text-base mt-1.5 flex flex-wrap gap-x-2 md:text-base lg:text-xl md:mt-2.5 2xl:mt-3
           text-heading">
            <span class="inline-block false">{{ $product['price'] }}</span>
            <span class="font-normal text-orange-800 line-through sm:text-base">$16.00</span>
        </div>
    </div>
</div>
