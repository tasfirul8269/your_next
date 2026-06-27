@props(['options'])

<v-carousel :images="{{ json_encode($options['images'] ?? []) }}">
    <div class="overflow-hidden">
        <div class="shimmer h-screen w-screen"></div>
    </div>
</v-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-carousel-template"
    >
        <div class="hero-carousel relative m-auto flex w-full overflow-hidden"
             @touchstart="onTouchStart"
             @touchend="onTouchEnd"
             ref="carouselContainer"
        >
            <!-- Slider -->
            <div
                class="inline-flex translate-x-0 cursor-pointer will-change-transform"
                :class="{ 'transition-transform duration-700 ease-out': ! isTransitionDisabled }"
                ref="sliderContainer"
                @transitionend="handleTransitionEnd"
            >
                <div
                    class="relative w-screen flex-shrink-0 hero-slide-height"
                    v-for="(media, index) in displayImages"
                    :key="index"
                    @click="visitLink(media)"
                    ref="slide"
                >
                    <!-- Video Slide -->
                    <template v-if="media.type === 'video'">
                        <video
                            class="h-full w-full object-cover"
                            :ref="'video_' + index"
                            :src="media.media_url"
                            autoplay
                            muted
                            playsinline
                            :loop="false"
                            @ended="onVideoEnded(index)"
                            @loadeddata="onVideoLoaded(index)"
                        ></video>

                        <!-- Mute/Unmute Button -->
                        <button
                            class="hero-carousel__mute-btn"
                            @click.stop="toggleMute(index)"
                            :aria-label="isMuted ? '@lang('shop::components.carousel.unmute')' : '@lang('shop::components.carousel.mute')'"
                        >
                            <!-- Muted Icon -->
                            <svg v-if="isMuted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <line x1="23" y1="9" x2="17" y2="15"></line>
                                <line x1="17" y1="9" x2="23" y2="15"></line>
                            </svg>

                            <!-- Unmuted Icon -->
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            </svg>
                        </button>
                    </template>

                    <!-- Image Slide -->
                    <template v-else>
                        <x-shop::media.images.lazy
                            class="h-full w-full select-none object-cover"
                            ::lazy="index === 0 ? false : true"
                            ::src="media.media_url"
                            ::srcset="media.media_url"
                            ::sizes="
                                '(max-width: 525px) 525px, ' +
                                '(max-width: 1024px) 1024px, ' +
                                '(max-width: 1600px) 1280px, ' +
                                '1920px'
                            "
                            ::alt="media?.title || 'Carousel Image ' + (index + 1)"
                            tabindex="0"
                            ::fetchpriority="index === 0 ? 'high' : 'low'"
                            ::decoding="index === 0 ? 'sync' : 'async'"
                        />
                    </template>
                </div>
            </div>

            <!-- Pagination Dots -->
            <div
                class="hero-carousel__dots"
                v-if="images?.length >= 2"
            >
                <button
                    v-for="(media, index) in images"
                    :key="'dot-' + index"
                    class="hero-carousel__dot"
                    :class="{ 'hero-carousel__dot--active': index === activeIndex }"
                    :aria-label="'Go to slide ' + (index + 1)"
                    @click="navigateByPagination(index)"
                    @keydown.enter="navigateByPagination(index)"
                    @keydown.space.prevent="navigateByPagination(index)"
                ></button>
            </div>
        </div>
    </script>

    <script type="module">
        app.component("v-carousel", {
            template: '#v-carousel-template',

            props: ['images'],

            data() {
                return {
                    touchStartX: 0,
                    touchStartY: 0,
                    touchEndX: 0,
                    isSwiping: false,
                    currentIndex: this.images.length > 1 ? 1 : 0,
                    isTransitionDisabled: true,
                    slider: '',
                    slides: [],
                    autoPlayInterval: null,
                    direction: 'ltr',
                    startFrom: 1,
                    isMuted: true,
                    hasOnlyOneSlide: this.images.length <= 1,
                };
            },

            computed: {
                displayImages() {
                    if (this.images.length < 2) return this.images;

                    return [
                        this.images[this.images.length - 1],
                        ...this.images,
                        this.images[0]
                    ];
                },

                activeIndex() {
                    if (this.images.length < 2) return this.currentIndex;

                    if (this.currentIndex === 0) return this.images.length - 1;
                    if (this.currentIndex > this.images.length) return 0;

                    return this.currentIndex - 1;
                }
            },

            mounted() {
                this.slider = this.$refs.sliderContainer;

                if (
                    this.$refs.slide
                    && typeof this.$refs.slide[Symbol.iterator] === 'function'
                ) {
                    this.slides = Array.from(this.$refs.slide);
                }

                // Add touchmove with passive:false so we can preventDefault()
                if (this.$refs.carouselContainer) {
                    this.$refs.carouselContainer.addEventListener('touchmove', this.onTouchMove, { passive: false });
                }

                // Use requestIdleCallback for non-critical initialization
                if ('requestIdleCallback' in window) {
                    requestIdleCallback(() => {
                        this.init();
                        
                        this.$nextTick(() => {
                            this.isTransitionDisabled = false;
                        });

                        // Only start autoplay if there are multiple slides
                        if (this.images.length > 1) {
                            setTimeout(() => {
                                this.startAutoPlay();
                            }, 4000);
                        }
                    });
                } else {
                    setTimeout(() => {
                        this.init();

                        this.$nextTick(() => {
                            this.isTransitionDisabled = false;
                        });

                        // Only start autoplay if there are multiple slides
                        if (this.images.length > 1) {
                            setTimeout(() => {
                                this.startAutoPlay();
                            }, 4000);
                        }
                    });
                }
            },

            beforeUnmount() {
                this.cleanup();
            },

            methods: {
                init() {
                    this.direction = 'ltr';
                    this.startFrom = 1;

                    this.setPositionByIndex();

                    window.addEventListener('resize', this.setPositionByIndex);
                },

                // REMOVED: All drag-related methods disabled
                // handleDragStart, handleDrag, handleDragEnd, animation methods removed

                setPositionByIndex() {
                    this.currentTranslate = this.currentIndex * -window.innerWidth;

                    this.prevTranslate = this.currentTranslate;

                    this.setSliderPosition();

                    this.handleSlideChange();
                },

                setSliderPosition() {
                    if (this.slider) {
                        this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                    }
                },

                onTouchStart(e) {
                    this.touchStartX = e.touches[0].clientX;
                    this.touchStartY = e.touches[0].clientY;
                    this.isSwiping = false;
                },

                onTouchMove(e) {
                    if (!this.touchStartX) return;

                    const diffX = Math.abs(e.touches[0].clientX - this.touchStartX);
                    const diffY = Math.abs(e.touches[0].clientY - this.touchStartY);

                    // If horizontal movement is dominant, prevent vertical scroll
                    if (diffX > diffY && diffX > 10) {
                        this.isSwiping = true;
                        e.preventDefault();
                    }
                },

                onTouchEnd(e) {
                    if (!this.isSwiping) return;

                    this.touchEndX = e.changedTouches[0].clientX;
                    const diff = this.touchStartX - this.touchEndX;

                    if (Math.abs(diff) > 50) {
                        clearInterval(this.autoPlayInterval);

                        if (diff > 0) {
                            this.next();
                        } else {
                            this.prev();
                        }
                    }

                    this.touchStartX = 0;
                    this.touchStartY = 0;
                    this.isSwiping = false;
                },

                visitLink(media) {
                    if (media.link && !this.isSwiping) {
                        window.location.href = media.link;
                    }
                },

                navigateByPagination(index) {
                    clearInterval(this.autoPlayInterval);

                    this.currentIndex = index + 1;

                    this.setPositionByIndex();

                    // Don't restart autoplay here - handleSlideChange will manage it
                },

                next() {
                    // Don't advance if there's only one slide
                    if (this.images.length <= 1) return;
                    
                    this.currentIndex++;

                    this.setPositionByIndex();
                },

                prev() {
                    // Don't go back if there's only one slide
                    if (this.images.length <= 1) return;
                    
                    this.currentIndex--;

                    this.setPositionByIndex();
                },

                startAutoPlay() {
                    // Don't start autoplay if there's only one slide
                    if (this.images.length <= 1) {
                        clearInterval(this.autoPlayInterval);
                        return;
                    }
                    
                    clearInterval(this.autoPlayInterval);

                    this.autoPlayInterval = setInterval(() => {
                        const currentMedia = this.displayImages[this.currentIndex];
                        
                        // Only advance if current slide is NOT a video
                        if (!currentMedia || currentMedia.type !== 'video') {
                            this.next();
                        }
                    }, 4000);
                },

                handleTransitionEnd() {
                    if (this.images.length < 2) return;

                    if (this.currentIndex >= this.images.length + 1) {
                        this.isTransitionDisabled = true;
                        this.currentIndex = 1;
                        this.setPositionByIndex();
                        
                        setTimeout(() => {
                            this.isTransitionDisabled = false;
                        }, 50);
                    } else if (this.currentIndex <= 0) {
                        this.isTransitionDisabled = true;
                        this.currentIndex = this.images.length;
                        this.setPositionByIndex();

                        setTimeout(() => {
                            this.isTransitionDisabled = false;
                        }, 50);
                    }
                },

                handleSlideChange() {
                    const idx = this.currentIndex;

                    this.displayImages.forEach((media, i) => {
                        if (media.type === 'video') {
                            const videoEl = this.$refs['video_' + i];
                            const video = Array.isArray(videoEl) ? videoEl[0] : videoEl;

                            if (video) {
                                video.pause();
                                video.currentTime = 0;
                            }
                        }
                    });

                    const currentMedia = this.displayImages[idx];

                    if (currentMedia && currentMedia.type === 'video') {
                        this.$nextTick(() => {
                            const videoEl = this.$refs['video_' + idx];
                            const video = Array.isArray(videoEl) ? videoEl[0] : videoEl;

                            if (video) {
                                video.muted = this.isMuted;
                                video.play().catch(() => {});
                            }
                        });
                    } else {
                        // If it's an image, restart autoplay
                        this.startAutoPlay();
                    }
                },

                onVideoEnded(index) {
                    if (index === this.currentIndex && this.images.length > 1) {
                        clearInterval(this.autoPlayInterval);
                        this.next();
                    }
                },

                onVideoLoaded(index) {
                    // Ensure first video autoplays when it's the current slide
                    const currentMedia = this.displayImages[this.currentIndex];
                    if (currentMedia && currentMedia.type === 'video') {
                        const videoEl = this.$refs['video_' + index];
                        const video = Array.isArray(videoEl) ? videoEl[0] : videoEl;

                        if (video) {
                            video.muted = this.isMuted;
                            video.play().catch(() => {});
                        }
                    }
                },

                toggleMute(index) {
                    this.isMuted = !this.isMuted;

                    const videoEl = this.$refs['video_' + index];
                    const video = Array.isArray(videoEl) ? videoEl[0] : videoEl;

                    if (video) {
                        video.muted = this.isMuted;
                    }
                },

                cleanup() {
                    clearInterval(this.autoPlayInterval);

                    if (this.$refs.carouselContainer) {
                        this.$refs.carouselContainer.removeEventListener('touchmove', this.onTouchMove);
                    }

                    window.removeEventListener('resize', this.setPositionByIndex);
                },
            },
        });
    </script>

    <style>
        .hero-carousel {
            height: 100vh;
        }

        /* Mobile hero - 75vh */
        @media (max-width: 768px) {
            .hero-carousel {
                height: 75vh !important;
            }
        }

        /* Pagination Dots */
        .hero-carousel__dots {
            position: absolute;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 5;
        }

        .hero-carousel__dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            background: transparent;
            cursor: pointer;
            padding: 0;
            transition: all 0.3s ease;
        }

        .hero-carousel__dot--active {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(255, 255, 255, 0.95);
            transform: scale(1.15);
        }

        .hero-carousel__dot:hover:not(.hero-carousel__dot--active) {
            background: rgba(255, 255, 255, 0.4);
        }

        /* Mute/Unmute Button */
        .hero-carousel__mute-btn {
            position: absolute;
            bottom: 24px;
            right: 24px;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.5);
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            color: #fff;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .hero-carousel__mute-btn:hover {
            background: rgba(0, 0, 0, 0.55);
            border-color: rgba(255, 255, 255, 0.8);
        }

        /* Hero slide height - responsive */
        .hero-slide-height {
            height: 100vh;
        }

        @media (max-width: 768px) {
            .hero-slide-height {
                height: 75vh !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-carousel__dots {
                bottom: 20px;
                gap: 8px;
            }

            .hero-carousel__dot {
                width: 8px;
                height: 8px;
            }

            .hero-carousel__mute-btn {
                width: 36px;
                height: 36px;
                bottom: 16px;
                right: 16px;
            }
        }
    </style>
@endpushOnce
