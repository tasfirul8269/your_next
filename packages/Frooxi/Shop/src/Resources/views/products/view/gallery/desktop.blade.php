<!-- For large screens greater than 1180px. -->
<div id="desktop-gallery" style="display: flex; align-items: stretch; gap: 12px;">
    <!-- Product Thumbnail Strip -->
    <div style="width: 100px; min-width: 100px; max-width: 100px; display: flex; flex-direction: column; gap: 0; overflow: hidden;">
        <!-- Arrow Up -->
        <span
            class="icon-arrow-up cursor-pointer text-2xl"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            tabindex="0"
            @click="swipeDown"
            v-if="lengthOfMedia"
            style="text-align: center; flex-shrink: 0; padding: 4px 0;"
        >
        </span>

        <!-- Swiper Container -->
        <div
            ref="swiperContainer"
            style="flex: 1; overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column; gap: 6px; scroll-behavior: smooth;"
            class="scrollbar-hide"
        >
            <template v-for="(media, index) in [...media.images, ...media.videos]">
                <div
                    v-if="media.type == 'videos'"
                    class="cursor-pointer"
                    :style="`position: relative; width: 100px; aspect-ratio: 0.68965517241 / 1; border-radius: 0; opacity: ${isActiveMedia(index) ? '1' : '0.5'}; transition: opacity 0.2s ease; flex-shrink: 0; overflow: hidden;`"
                    @click="change(media, index)"
                    tabindex="0"
                >
                    <video
                        style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;"
                        alt="{{ $product->name }}"
                    >
                        <source
                            :src="media.video_url"
                            type="video/mp4"
                        />
                    </video>

                    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                        <div style="width: 36px; height: 36px; border-radius: 9999px; background: rgba(17, 17, 17, 0.72); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);">
                            <svg viewBox="0 0 24 24" aria-hidden="true" style="width: 14px; height: 14px; margin-left: 2px; fill: #ffffff; display: block;">
                                <path d="M8 6.5v11l9-5.5-9-5.5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <img
                    v-else
                    class="cursor-pointer"
                    :style="`width: 100px; aspect-ratio: 0.68965517241 / 1; object-fit: cover; border-radius: 0; opacity: ${isActiveMedia(index) ? '1' : '0.5'}; transition: opacity 0.2s ease; flex-shrink: 0;`"
                    :src="media.small_image_url"
                    alt="{{ $product->name }}"
                    width="100"
                    height="145"
                    tabindex="0"
                    @click="change(media, index)"
                />
            </template>
        </div>

        <!-- Arrow Down -->
        <span
            class="icon-arrow-down cursor-pointer text-2xl"
            v-if="lengthOfMedia"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.next')"
            tabindex="0"
            @click="swipeTop"
            style="text-align: center; flex-shrink: 0; padding: 4px 0;"
        >
        </span>
    </div>

    <!-- Product Base Image and Video with Shimmer -->
    <div
        style="flex: 1; min-width: 0;"
        v-show="isMediaLoading"
    >
        <div class="shimmer rounded-lg bg-zinc-100" style="width: 100%; aspect-ratio: 0.68965517241 / 1;"></div>
    </div>

    <div
        style="flex: 1; min-width: 0; position: relative;"
        v-show="! isMediaLoading"
    >
        @if (data_get($product, 'new'))
            <span style="position: absolute; left: 12px; top: 12px; z-index: 10; background: #1a1a1a; color: #fff; border-radius: 9999px; padding: 4px 14px; font-size: 12px; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase;">
                New
            </span>
        @endif
        <img
            class="cursor-pointer"
            style="width: 100%; aspect-ratio: 0.68965517241 / 1; object-fit: cover; border-radius: 0; display: block;"
            :src="baseFile.path"
            v-if="baseFile.type == 'image'"
            alt="{{ $product->name }}"
            tabindex="0"
            @click="isImageZooming = !isImageZooming"
            @load="onMediaLoad()"
            fetchpriority="high"
        />

        <div
            class="cursor-pointer"
            style="width: 100%; border-radius: 0; overflow: hidden;"
            tabindex="0"
            v-if="baseFile.type == 'video'"
        >
            <video
                controls
                style="width: 100%; aspect-ratio: 0.68965517241 / 1; object-fit: cover;"
                alt="{{ $product->name }}"
                @click="isImageZooming = !isImageZooming"
                @loadeddata="onMediaLoad()"
                @loadedmetadata="onMediaLoad()"
                :key="baseFile.path"
            >
                <source
                    :src="baseFile.path"
                    type="video/mp4"
                />
            </video>
        </div>
    </div>
</div>
