<div class="fixed top-14 z-[1000] h-full w-[270px] bg-white pt-4 border-r border-gray-100 transition-all duration-300 group-[.sidebar-collapsed]/container:w-[70px] max-lg:hidden" style="font-family:'Montserrat',sans-serif;">
    <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto group-[.sidebar-collapsed]/container:overflow-visible">
        <nav class="grid w-full gap-0.5">
            <!-- Navigation Menu -->
            @foreach (menu()->getItems('admin') as $menuItem)
                <div
                    class="px-3 group/item {{ $menuItem->isActive() ? 'active' : 'inactive' }}"
                    onmouseenter="adjustSubMenuPosition(event)"
                >
                    <a
                        href="{{ $menuItem->getUrl() }}"
                        class="flex gap-3 py-2.5 px-3 items-center cursor-pointer rounded-lg transition-all duration-150 {{ $menuItem->isActive() ? 'bg-[#D4A84B]/10 border-l-[3px] border-[#D4A84B]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    >
                        <span class="{{ $menuItem->getIcon() }} text-xl {{ $menuItem->isActive() ? 'text-[#D4A84B]' : 'text-gray-500'}}"></span>
                        
                        <p class="text-sm font-medium whitespace-nowrap group-[.sidebar-collapsed]/container:hidden {{ $menuItem->isActive() ? 'text-[#D4A84B] font-semibold' : 'text-gray-700'}}">
                            {{ $menuItem->getName() }}
                        </p>
                    </a>

                    @if ($menuItem->haveChildren())
                        <div class="{{ $menuItem->isActive() ? '!grid' : '' }} hidden min-w-[180px] ltr:pl-12 rtl:pr-12 pb-2 z-[100] overflow-hidden group-[.sidebar-collapsed]/container:!hidden group-[.sidebar-collapsed]/container:fixed group-[.sidebar-collapsed]/container:ltr:!left-[70px] group-[.sidebar-collapsed]/container:rtl:!right-[70px] group-[.sidebar-collapsed]/container:p-[0] group-[.sidebar-collapsed]/container:bg-white group-[.sidebar-collapsed]/container:border group-[.sidebar-collapsed]/container:ltr:rounded-r-lg group-[.sidebar-collapsed]/container:rtl:rounded-l-lg group-[.sidebar-collapsed]/container:border-gray-200 group-[.sidebar-collapsed]/container:shadow-lg group-[.sidebar-collapsed]/container:group-hover/item:!grid group-[.inactive]/item:hidden group-[.inactive]/item:fixed group-[.inactive]/item:ltr:left-[270px] group-[.inactive]/item:rtl:right-[270px] group-[.inactive]/item:p-[0] group-[.inactive]/item:bg-white group-[.inactive]/item:border group-[.inactive]/item:ltr:rounded-r-lg group-[.inactive]/item:rtl:rounded-l-lg group-[.inactive]/item:border-gray-200 group-[.inactive]/item:shadow-lg group-[.inactive]/item:group-hover/item:!grid">
                            @foreach ($menuItem->getChildren() as $subMenuItem)
                                <a
                                    href="{{ $subMenuItem->getUrl() }}"
                                    class="text-sm whitespace-nowrap py-1.5 px-3 transition-all {{ $subMenuItem->isActive() ? 'text-[#D4A84B] font-medium' : 'text-gray-500 hover:text-gray-900' }} group-[.sidebar-collapsed]/container:px-5 group-[.sidebar-collapsed]/container:py-2.5 group-[.inactive]/item:px-5 group-[.inactive]/item:py-2.5"
                                >
                                    {{ $subMenuItem->getName() }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </div>

    <!-- Collapse menu -->
    <v-sidebar-collapse></v-sidebar-collapse>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-sidebar-collapse-template"
    >
        <div
            class="fixed bottom-0 w-full max-w-[270px] cursor-pointer border-t border-gray-100 bg-white px-3 py-2 transition-all duration-300 hover:bg-gray-50"
            :class="{'max-w-[70px]': isCollapsed}"
            @click="toggle"
        >
            <div class="flex items-center gap-2.5 p-1">
                <span
                    class="icon-collapse text-lg transition-all text-gray-400 hover:text-gray-600"
                    :class="[isCollapsed ? 'ltr:rotate-[180deg] rtl:rotate-[0]' : 'ltr:rotate-[0] rtl:rotate-[180deg]']"
                ></span>
                <span v-if="!isCollapsed" class="text-xs text-gray-500">Collapse</span>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-sidebar-collapse', {
            template: '#v-sidebar-collapse-template',

            data() {
                return {
                    isCollapsed: {{ request()->cookie('sidebar_collapsed') ?? 0 }},
                }
            },

            methods: {
                toggle() {
                    this.isCollapsed = parseInt(this.isCollapsedCookie()) ? 0 : 1;

                    var expiryDate = new Date();

                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'sidebar_collapsed=' + this.isCollapsed + '; path=/; expires=' + expiryDate.toGMTString();

                    this.$root.$refs.appLayout.classList.toggle('sidebar-collapsed');
                },

                isCollapsedCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'sidebar_collapsed') {
                            return value;
                        }
                    }
                    
                    return 0;
                },
            },
        });
    </script>

    <script>
        const adjustSubMenuPosition = (event) => {
            let menuContainer = event.currentTarget;

            let subMenuContainer = menuContainer.lastElementChild;

            if (subMenuContainer) {
                const menuTopOffset = menuContainer.getBoundingClientRect().top;

                const subMenuHeight = subMenuContainer.offsetHeight;

                const availableHeight = window.innerHeight - menuTopOffset;

                let subMenuTopOffset = menuTopOffset;

                if (subMenuHeight > availableHeight) {
                    subMenuTopOffset = menuTopOffset - (subMenuHeight - availableHeight);
                }

                subMenuContainer.style.top = `${subMenuTopOffset}px`;
            }
        };
    </script>
@endpushOnce