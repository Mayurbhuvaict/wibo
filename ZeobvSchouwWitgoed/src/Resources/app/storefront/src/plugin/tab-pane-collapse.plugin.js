import Plugin from "src/plugin-system/plugin.class";

export default class TabPaneCollapse extends Plugin {
    static options = {
        tabContainerClass: 'tab-pane-container',
        tabIsCollapsibleClass: 'collapsable',
        toggleButtonClass: 'toggle-collapsable-tab-btn',
        tabMaxHeight: 500
    };

    init() {
        this.PluginManager = window.PluginManager;
        this.initializePlugin();
    }

    initializePlugin() {
        this.prepareWrapperClasses();
        this.addClickEvents();
        this.initialized = true;
    }

    destroy() {
        this.initialized = false;
    }

    /**
     * Add class to tab if it's collapsible, showing the button. This is based on the tab height.
     */
    prepareWrapperClasses() {
        let elem = this.el.getElementsByClassName(this.options.tabContainerClass);

        // Set current tab as collapsible if height enough.
        if (elem.length > 0) {
            if (elem[0].offsetHeight >= this.options.tabMaxHeight) {
                this.el.classList.add(this.options.tabIsCollapsibleClass);
            }
        }
    }

    /**
     * Add the button click event, toggling the tab collapsed class removing or adding a max-height via scss.
     */
    addClickEvents() {
        let collapseBtn = this.el.getElementsByClassName(this.options.toggleButtonClass),
            self = this;

        if (collapseBtn.length > 0) {
            collapseBtn[0].addEventListener('click', function (event) {
                event.preventDefault();
                self.el.classList.toggle(self.options.tabIsCollapsibleClass);
            }, false);
        }
    }
}
