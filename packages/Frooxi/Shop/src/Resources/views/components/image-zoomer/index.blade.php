<v-gallery-zoomer {{ $attributes }}></v-gallery-zoomer>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-gallery-zoomer-template"
    >
        <Teleport to="body">
        <div v-if="isOpen">
            <div
                ref="parentContainer"
                @click="handleOuterClick"
                style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999; background-color: #ffffff; display: flex; flex-direction: column; overflow: hidden; width: 100vw; height: 100vh;"
            >
                <div
                    style="position: absolute; top: 0; left: 0; right: 0; display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; z-index: 10;"
                >
                    <div style="font-size: 14px; color: #333; line-height: 1;">
                        @{{ currentIndex }} / @{{ attachments.length }}
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <span
                            class="icon-zoom-in"
                            @click.stop="toggleZoom"
                            style="font-size: 28px; color: #333; cursor: pointer; line-height: 1; opacity: 1;"
                        ></span>

                        <span
                            class="icon-cancel"
                            @click.stop="toggle"
                            style="font-size: 28px; color: #333; cursor: pointer; line-height: 1;"
                        ></span>
                    </div>
                </div>

                <button
                    v-if="attachments.length >= 2"
                    type="button"
                    class="icon-arrow-left"
                    @click.stop="navigate(currentIndex -= 1)"
                    style="position: fixed; left: 24px; top: 50%; transform: translateY(-50%); z-index: 10; font-size: 32px; color: #333; cursor: pointer; background: none; border: none; padding: 8px; opacity: 0.5; transition: opacity 0.2s; line-height: 1;"
                    onmouseover="this.style.opacity='1'"
                    onmouseout="this.style.opacity='0.5'"
                ></button>

                <button
                    v-if="attachments.length >= 2"
                    type="button"
                    class="icon-arrow-right"
                    @click.stop="navigate(currentIndex += 1)"
                    style="position: fixed; right: 24px; top: 50%; transform: translateY(-50%); z-index: 10; font-size: 32px; color: #333; cursor: pointer; background: none; border: none; padding: 8px; opacity: 0.5; transition: opacity 0.2s; line-height: 1;"
                    onmouseover="this.style.opacity='1'"
                    onmouseout="this.style.opacity='0.5'"
                ></button>

                <div
                    ref="mediaContainer"
                    data-lightbox-controls
                    style="flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; padding: 72px 96px 136px; box-sizing: border-box;"
                >
                    <div
                        style="position: relative; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;"
                        :style="{
                            cursor: isZooming ? (isDragging ? 'grabbing' : 'grab') : 'default'
                        }"
                    >
                        <div
                            v-for="(attachment, index) in attachments"
                            :key="index"
                            ref="slides"
                            style="display: none; align-items: center; justify-content: center; width: 100%; height: 100%;"
                            @click.stop
                        >
                            <video
                                v-if="attachment.type == 'video'"
                                controls
                                style="max-height: calc(100vh - 160px); max-width: 90vw; object-fit: contain;"
                            >
                                <source :src="attachment.url" type="video/mp4">
                                <source :src="attachment.url" type="video/ogg">
                                Your browser does not support HTML video.
                            </video>

                            <template v-if="attachment.type === 'image'">
                                <img
                                    :src="attachment.url"
                                    class="max-md:hidden"
                                    :style="{
                                        maxHeight: 'calc(100vh - 160px)',
                                        maxWidth: '90vw',
                                        objectFit: 'contain',
                                        transition: 'transform 0.3s ease-out',
                                        transform: `translate(${translateX}px, ${translateY}px)`,
                                        cursor: ! isZooming ? 'zoom-in' : (isDragging ? 'grabbing' : 'grab')
                                    }"
                                    @click.stop="handleClick"
                                    @mousedown.prevent="handleMouseDown"
                                    @mousemove.prevent="handleMouseMove"
                                    @mouseleave.prevent="resetImagePosition"
                                    @mouseup.prevent="resetImagePosition"
                                    @mousewheel="handleMouseWheel"
                                />

                                <img
                                    :src="attachment.url"
                                    class="md:hidden"
                                    :style="{
                                        maxHeight: 'calc(100vh - 160px)',
                                        maxWidth: '90vw',
                                        objectFit: 'contain',
                                        transition: 'transform 0.3s ease-out',
                                        transform: `translate(${translateX}px, ${translateY}px)`,
                                        cursor: ! isZooming ? 'zoom-in' : (isDragging ? 'grabbing' : 'grab')
                                    }"
                                />
                            </template>
                        </div>
                    </div>
                </div>

                <div
                    v-if="attachments.length"
                    style="position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10; max-width: calc(100vw - 32px); overflow-x: auto; padding-bottom: 4px;"
                >
                    <template v-for="(attachment, index) in attachments" :key="`thumb_${index}`">
                        <img
                            v-if="attachment.type === 'image'"
                            :src="attachment.url"
                            @click.stop="navigate(currentIndex = index + 1)"
                            :style="{
                                width: '72px',
                                height: '96px',
                                objectFit: 'cover',
                                cursor: 'pointer',
                                border: 'none',
                                borderRadius: '0',
                                opacity: currentIndex === index + 1 ? '1' : '0.5',
                                transition: 'opacity 0.2s',
                                flexShrink: '0'
                            }"
                        />

                        <video
                            v-if="attachment.type === 'video'"
                            :src="attachment.url"
                            muted
                            playsinline
                            @click.stop="navigate(currentIndex = index + 1)"
                            :style="{
                                width: '72px',
                                height: '96px',
                                objectFit: 'cover',
                                cursor: 'pointer',
                                border: 'none',
                                borderRadius: '0',
                                opacity: currentIndex === index + 1 ? '1' : '0.5',
                                transition: 'opacity 0.2s',
                                flexShrink: '0'
                            }"
                        ></video>
                    </template>
                </div>
            </div>
        </div>
        </Teleport>
    </script>

    <script type="module">
        app.component('v-gallery-zoomer', {
            template: '#v-gallery-zoomer-template',

            props: {
                attachments: {
                    type: Object,

                    required: true,

                    default: () => [],
                },

                isImageZooming: {
                    type: Boolean,

                    default: false,
                },

                initialIndex: {
                    type: String,
                    
                    default: 0,
                },
            },

            watch: {
                isImageZooming(newVal, oldVal) {  
                    this.currentIndex = parseInt(this.initialIndex.split('_').pop()) + 1;

                    this.toggle();

                    this.$nextTick(() => {
                        this.navigate(this.currentIndex);
                    });
                },
            },

            mounted() {
                this._keyHandler = (e) => {
                    if (!this.isOpen) return;
                    if (e.key === 'ArrowLeft') this.navigate(--this.currentIndex);
                    if (e.key === 'ArrowRight') this.navigate(++this.currentIndex);
                    if (e.key === 'Escape') this.toggle();
                };

                document.addEventListener('keydown', this._keyHandler);
            },

            beforeUnmount() {
                document.removeEventListener('keydown', this._keyHandler);
            },
        
            data() {
                return {
                    isOpen: this.isImageZooming,

                    isDragging: false,

                    isZooming: false,

                    currentIndex: 1,

                    startDragX: 0,

                    startDragY: 0,

                    translateX: 0,

                    translateY: 0,

                    isMouseMoveTriggered: false,

                    isMouseDownTriggered: false,
                };
            },

            methods: {
                toggleZoom() {
                    this.resetDrag();

                    this.isZooming = ! this.isZooming;
                },

                toggle() {
                    this.isOpen = ! this.isOpen;

                    document.body.style.overflow = this.isOpen ? 'hidden' : '';
                },

                open() {
                    this.isOpen = true;

                    document.body.style.overflow = 'hidden';
                },

                navigate(index) {
                    if (index > this.attachments.length) {
                        this.currentIndex = 1;
                    }

                    if (index < 1) {
                        this.currentIndex = this.attachments.length;
                    }

                    let slides = this.$refs.slides;

                    for (let i = 0; i < slides.length; i++) {
                        if (i == this.currentIndex - 1) {
                            continue;
                        }

                        slides[i].style.display = 'none';
                    }
                    
                    slides[this.currentIndex - 1].style.display = 'flex';

                    this.isZooming = false;

                    this.resetDrag();
                },

                handleClick(event) {
                    if (
                        this.isMouseMoveTriggered
                        && ! this.isMouseDownTriggered
                    ) {
                        return;
                    }

                    this.resetDrag();

                    this.isZooming = ! this.isZooming;
                },

                handleOuterClick(event) {
                    if (event.target !== this.$refs.parentContainer && event.target !== this.$refs.mediaContainer && !event.target.closest('[data-lightbox-controls]')) {
                        return;
                    }

                    if (this.isZooming) {
                        this.isZooming = false;
                        this.resetDrag();
                        return;
                    }

                    this.toggle();
                },

                handleMouseDown(event) {
                    this.isMouseDownTriggered = true;

                    this.isDragging = true;

                    this.startDragX = event.clientX;

                    this.startDragY = event.clientY;
                },

                handleMouseMove(event) {
                    this.isMouseMoveTriggered = true;
                    
                    this.isMouseDownTriggered = false;

                    if (! this.isDragging) {
                        return;
                    }

                    const deltaX = event.clientX - this.startDragX;
                    
                    const deltaY = event.clientY - this.startDragY;
                    
                    const newTranslateY = this.translateY + deltaY;

                    const remainingHeight = this.$refs.parentContainer.clientHeight - this.$refs.mediaContainer.clientHeight;

                    const maxTranslateY = Math.min(0, window.innerHeight - (event.srcElement.height + remainingHeight));

                    const clampedTranslateY = Math.max(maxTranslateY, Math.min(newTranslateY, 0));

                    this.translateY = clampedTranslateY;
                    
                    this.startDragY = event.clientY;
                    
                    this.startDragX = event.clientX;

                    this.translateX += deltaX;
                },

                handleMouseWheel(event) {
                    const deltaY = event.clientY - this.startDragY;

                    let newTranslateY = this.translateY - event.deltaY / Math.abs(event.deltaY) * 100;
                    
                    const remainingHeight = this.$refs.parentContainer.clientHeight - this.$refs.mediaContainer.clientHeight;

                    const maxTranslateY = Math.min(0, window.innerHeight - (event.srcElement.height + remainingHeight));

                    this.translateY = Math.max(maxTranslateY, Math.min(newTranslateY, 0));
                },

                resetImagePosition() {
                    this.isDragging = false;

                    this.translateX  = 0;

                    this.startDragX = 0;
                },

                resetDrag() {
                    this.isDragging = false;

                    this.startDragX = 0;

                    this.startDragY = 0;

                    this.translateX = 0;

                    this.translateY = 0;
                },
            },
        });
    </script>
@endPushOnce
