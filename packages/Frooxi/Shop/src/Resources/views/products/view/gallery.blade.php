<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery />
</v-product-gallery>

@pushOnce('styles')
    <style>
        @media (max-width: 1179px) {
            #desktop-gallery { display: none !important; }
        }
        @media (min-width: 1180px) {
            #mobile-gallery { display: none !important; }
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-gallery-template"
    >
        <div>
            <!-- Desktop Gallery -->
            @include('shop::products.view.gallery.desktop')

            <!-- Mobile Gallery -->
            @include('shop::products.view.gallery.mobile')
            
            <!-- Gallery Images Zoomer -->
            <x-shop::image-zoomer
                ::attachments="attachments"
                ::is-image-zooming="isImageZooming"
                ::initial-index="`media_${activeIndex}`"
            />
        </div>
    </script>

    <script type="module">
        app.component('v-product-gallery', {
            template: '#v-product-gallery-template',

            data() {
                return {
                    isImageZooming: false,

                    isMediaLoading: true,

                    media: {
                        images: @json(product_image()->getGalleryImages($product)),

                        videos: @json(product_video()->getVideos($product)),
                    },

                    baseFile: {
                        type: '',

                        path: ''
                    },

                    activeIndex: 0,

                    containerOffset: 110,

                    mobileTranslateX: 0,

                    mobileIsDragging: false,

                    mobileStartX: 0,

                    mobileCurrentX: 0,
                };
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        let selectedImage = newImages?.[this.activeIndex];

                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages) && selectedImage?.large_image_url) {
                            this.baseFile.path = selectedImage.large_image_url;
                        }
                    },
                },
            },
        
            mounted() {
                if (this.media.images.length) {

                    this.baseFile.type = 'image';

                    this.baseFile.path = this.media.images[0].large_image_url;
                } else if (this.media.videos.length) {

                    this.baseFile.type = 'video';

                    this.baseFile.path = this.media.videos[0].video_url;

                    // No image @load event will fire, so stop shimmer immediately
                    this.isMediaLoading = false;
                }

                window.addEventListener('resize', () => {
                    this.snapMobileSlide();
                });
            },

            computed: {
                lengthOfMedia() {
                    if (this.media.images.length) {
                        return [...this.media.images, ...this.media.videos].length > 5;
                    }
                },

                attachments() {
                    return [...this.media.images, ...this.media.videos].map(media => ({
                        url: media.type === 'videos' ? media.video_url : media.original_image_url,
                        
                        type: media.type === 'videos' ? 'video' : 'image',
                    }));
                },

                allMobileMedia() {
                    return [...this.media.images, ...this.media.videos];
                },
            },

            methods: {
                isActiveMedia(index) {
                    return index === this.activeIndex;
                },
                
                onMediaLoad() {
                    this.isMediaLoading = false;
                },

                mobileSwipeStart(e) {
                    this.mobileIsDragging = true;
                    this.mobileStartX = e.touches[0].clientX;
                    this.mobileCurrentX = e.touches[0].clientX;
                },

                mobileSwipeMove(e) {
                    if (!this.mobileIsDragging) return;
                    this.mobileCurrentX = e.touches[0].clientX;
                    const diff = this.mobileCurrentX - this.mobileStartX;
                    const container = this.$refs.mobileCarouselContainer;
                    const slideWidth = container ? container.offsetWidth : window.innerWidth;
                    this.mobileTranslateX = -this.activeIndex * slideWidth + diff;
                },

                mobileSwipeEnd(e) {
                    if (!this.mobileIsDragging) return;
                    this.mobileIsDragging = false;
                    const diff = this.mobileCurrentX - this.mobileStartX;
                    const allMedia = this.allMobileMedia;
                    if (diff < -50 && this.activeIndex < allMedia.length - 1) {
                        this.activeIndex++;
                    } else if (diff > 50 && this.activeIndex > 0) {
                        this.activeIndex--;
                    }
                    this.snapMobileSlide();
                    const media = allMedia[this.activeIndex];
                    if (media) {
                        if (media.type === 'videos') {
                            this.baseFile.type = 'video';
                            this.baseFile.path = media.video_url;
                        } else {
                            this.baseFile.type = 'image';
                            this.baseFile.path = media.large_image_url;
                        }
                    }
                },

                goToMobileSlide(index) {
                    this.activeIndex = index;
                    this.snapMobileSlide();
                    const allMedia = this.allMobileMedia;
                    const media = allMedia[index];
                    if (media) {
                        if (media.type === 'videos') {
                            this.baseFile.type = 'video';
                            this.baseFile.path = media.video_url;
                        } else {
                            this.baseFile.type = 'image';
                            this.baseFile.path = media.large_image_url;
                        }
                    }
                },

                snapMobileSlide() {
                    const container = this.$refs.mobileCarouselContainer;
                    const slideWidth = container ? container.offsetWidth : window.innerWidth;
                    this.mobileTranslateX = -this.activeIndex * slideWidth;
                },

                change(media, index) {
                    if (media.type == 'videos') {
                        this.baseFile.type = 'video';

                        this.baseFile.path = media.video_url;

                        this.onMediaLoad();
                    } else {
                        this.baseFile.type = 'image';

                        this.baseFile.path = media.large_image_url;
                    }

                    if (index > this.activeIndex) {
                        this.swipeDown();
                    } else if (index < this.activeIndex) {
                        this.swipeTop();
                    }

                    this.activeIndex = index;
                    this.snapMobileSlide();
                },

                swipeTop() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop -= this.containerOffset;
                },

                swipeDown() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop += this.containerOffset;
                },
            },
        });
    </script>
@endpushOnce
