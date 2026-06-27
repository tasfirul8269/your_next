<x-shop::layouts
    :has-header="true"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        @lang('shop::app.categories.index.title', ['default' => 'All Categories'])
    </x-slot>

    <div style="padding: 28px 16px 100px; max-width: 1400px; margin: 0 auto; background: #fff; min-height: 100vh;">

        <!-- Page Heading -->
        <h1 style="font-size: 28px; font-weight: 400; letter-spacing: 0.06em; text-transform: uppercase; color: #14284a; margin: 0 0 24px 0; font-family: 'DM Serif Display', Georgia, serif; line-height: 1.2;">
            Explore Categories
        </h1>

        <!-- Responsive category grid — inline grid, JS swaps column count on resize -->
        <div
            id="cat-grid"
            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;"
        >
            @foreach ($categories as $category)
                <a
                    href="{{ $category->url }}"
                    style="display: block; text-decoration: none; border-radius: 14px; overflow: hidden; position: relative; aspect-ratio: 3/4; background: #e5e7eb;"
                    aria-label="{{ $category->name }}"
                >
                    {{-- Background image --}}
                    @if ($category->logo_url)
                        <img
                            src="{{ $category->logo_url }}"
                            alt="{{ $category->name }}"
                            style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 0;"
                            loading="lazy"
                        >
                    @elseif ($category->banner_url)
                        <img
                            src="{{ $category->banner_url }}"
                            alt="{{ $category->name }}"
                            style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 0;"
                            loading="lazy"
                        >
                    @else
                        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, #1a2a4a 0%, #2d4270 100%);"></div>
                    @endif

                    {{-- Dark gradient overlay at bottom --}}
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.18) 50%, rgba(0,0,0,0) 100%);"></div>

                    {{-- Category label --}}
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 16px 14px 18px;">
                        <p style="margin: 0; font-size: 14px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #fff; font-family: Montserrat, sans-serif; line-height: 1.2;">
                            {{ $category->name }}
                        </p>
                        <p style="margin: 4px 0 0; font-size: 12px; font-weight: 400; letter-spacing: 0.06em; text-transform: uppercase; color: rgba(255,255,255,0.82); font-family: Montserrat, sans-serif;">
                            {{ $category->products_count }} Items
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($categories->isEmpty())
            <div style="text-align: center; padding: 64px 16px; color: #9ca3af; font-family: Montserrat, sans-serif; font-size: 15px;">
                No categories found.
            </div>
        @endif

    </div>

    <script>
        (function () {
            var grid = document.getElementById('cat-grid');
            if (!grid) return;

            function updateCols() {
                var w = window.innerWidth;
                if (w >= 1280) {
                    grid.style.gridTemplateColumns = 'repeat(5, 1fr)';
                    grid.style.gap = '20px';
                } else if (w >= 768) {
                    grid.style.gridTemplateColumns = 'repeat(4, 1fr)';
                    grid.style.gap = '16px';
                } else {
                    grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
                    grid.style.gap = '12px';
                }
            }

            updateCols();
            window.addEventListener('resize', updateCols);
        })();
    </script>

</x-shop::layouts>

