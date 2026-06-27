<div
    id="mobile-gallery"
    style="width: 100%;"
>
    <!-- Shimmer loading state -->
    <div
        v-if="isMediaLoading && allMobileMedia.length === 0"
        style="width: 100%; overflow: hidden;"
    >
        <div
            class="shimmer"
            style="width: 100%; aspect-ratio: 0.68965517241 / 1; background: #e4e4e7;"
        ></div>
    </div>

    <div v-show="!isMediaLoading || allMobileMedia.length > 0" style="width: 100%;">
        <!-- Main Image Carousel -->
        <div
            ref="mobileCarouselContainer"
            style="position: relative; width: 100%; overflow: hidden;"
            @touchstart="mobileSwipeStart($event)"
            @touchmove="mobileSwipeMove($event)"
            @touchend="mobileSwipeEnd($event)"
        >
            <div
                ref="mobileCarouselTrack"
                :style="{
                    display: 'flex',
                    width: (allMobileMedia.length * 100) + '%',
                    transform: 'translateX(' + mobileTranslateX + 'px)',
                    transition: mobileIsDragging ? 'none' : 'transform 0.3s ease'
                }"
            >
                <div
                    v-for="(item, index) in allMobileMedia"
                    :key="'mobile_slide_' + index"
                    :style="{ width: (100 / allMobileMedia.length) + '%', flexShrink: 0 }"
                >
                    <video
                        v-if="item.type === 'videos'"
                        controls
                        playsinline
                        preload="metadata"
                        style="display: block; width: 100%; aspect-ratio: 0.68965517241 / 1; object-fit: cover; background: #f3f4f6;"
                    >
                        <source :src="item.video_url" type="video/mp4" />
                    </video>

                    <img
                        v-else
                        :src="item.large_image_url"
                        :alt="@js($product->name)"
                        style="display: block; width: 100%; aspect-ratio: 0.68965517241 / 1; object-fit: cover; background: #f3f4f6;"
                        @load="index === 0 ? onMediaLoad() : null"
                    />
                </div>
            </div>

            <!-- Zoom/Fullscreen button -->
            <button
                type="button"
                aria-label="Toggle image zoom"
                @click.stop="isImageZooming = !isImageZooming"
                style="position: absolute; right: 12px; bottom: 12px; width: 48px; height: 48px; border: none; border-radius: 9999px; background: #ffffff; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16); z-index: 2;"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true" style="display: block; width: 20px; height: 20px;">
                    <path d="M9 3H4v5" fill="none" stroke="#111111" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    <path d="M15 3h5v5" fill="none" stroke="#111111" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    <path d="M9 21H4v-5" fill="none" stroke="#111111" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                    <path d="M15 21h5v-5" fill="none" stroke="#111111" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                </svg>
            </button>
        </div>

        <!-- Thumbnail Strip -->
        <div
            v-if="allMobileMedia.length"
            style="display: flex; gap: 8px; overflow-x: auto; padding: 12px;"
            class="scrollbar-hide"
        >
            <div
                v-for="(item, index) in allMobileMedia"
                :key="'mobile_thumb_' + index"
                @click="goToMobileSlide(index)"
                :style="{
                    width: '22vw',
                    aspectRatio: '0.68965517241 / 1',
                    flexShrink: 0,
                    cursor: 'pointer',
                    opacity: activeIndex === index ? '1' : '0.5',
                    transition: 'opacity 0.2s',
                    position: 'relative',
                    overflow: 'hidden'
                }"
            >
                <video
                    v-if="item.type === 'videos'"
                    muted
                    playsinline
                    preload="metadata"
                    style="display: block; width: 100%; height: 100%; object-fit: cover; pointer-events: none;"
                >
                    <source :src="item.video_url" type="video/mp4" />
                </video>
                <img
                    v-else
                    :src="item.small_image_url"
                    :alt="@js($product->name)"
                    style="display: block; width: 100%; height: 100%; object-fit: cover;"
                />

                <div
                    v-if="item.type === 'videos'"
                    style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;"
                >
                    <div style="width: 34px; height: 34px; border-radius: 9999px; background: rgba(17, 17, 17, 0.72); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);">
                        <svg viewBox="0 0 24 24" aria-hidden="true" style="width: 14px; height: 14px; margin-left: 2px; fill: #ffffff; display: block;">
                            <path d="M8 6.5v11l9-5.5-9-5.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Product Video -->
        <div
            v-if="media.videos.length > 0"
            @click="goToMobileSlide(media.images.length)"
            style="text-align: center; padding: 12px 0; font-size: 14px; color: #333; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;">
                <rect x="2" y="5" width="14" height="14" rx="2" ry="2"></rect>
                <path d="M16 10l6-4v12l-6-4z"></path>
            </svg>
            View Product Video
        </div>
    </div>
</div>
