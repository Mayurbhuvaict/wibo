import Plugin from 'src/plugin-system/plugin.class';
import ViewportDetection from 'src/helper/viewport-detection.helper';

export default class StickyFooterBuyButtonPlugin extends Plugin {

    static options = {
        "showOnScrollPosition": 800,
        "stickyBuyButtonClass": "nav-tabs-wrapper",
        "stickyClass": "fixed-footer",
        "target": "buy-widget-container",
        "elementOriginal": "product-detail-tab-navigation",
        "offsetClass": "offset",
        "productDetailMainClass": "product-detail-media",
        "addToCartModalId": "addToCartModal",
        "headerWrapperClass": "header-wrapper"
    }

    init() {
        this.PluginManager = window.PluginManager

        this.subscribeViewportEvents()

        if (this.pluginShouldBeActive()) this.initializePlugin();

        this.target = document.getElementsByClassName(this.options.target)[0];
        this.stickyBuyButtonEl = document.getElementsByClassName(this.options.stickyBuyButtonClass)[0];
        this.elementNeedsOffsetEl = document.getElementsByClassName(this.options.elementNeedsOffsetClass)[0];
        this.productDetailMainEl = document.getElementsByClassName(this.options.productDetailMainClass)[0];
        this.addToCartModalEl = document.getElementById(this.options.addToCartModalId);
        this.headerWrapperEl = document.getElementsByClassName(this.options.headerWrapperClass)[0];
        this.elementOriginal = document.getElementsByClassName(this.options.elementOriginal)[0];
        }

    subscribeViewportEvents() {
        // scope:test / scope: this gives a this reference to what we expected.
        document.$emitter.subscribe('Viewport/hasChanged', this.update, {scope: this})
    }

    update() {
        if (this.pluginShouldBeActive()) {
            if (this.initialized) return;

            this.initializePlugin()
        } else {
            if (!this.initialized) return;

            this.destroy();
        }
    }

    initializePlugin() {
        this.addEventListeners()
        this.initialized = true
    }

    destroy() {
        this.removeEventListeners()
        this.initialized = false
    }

    pluginShouldBeActive() {
        if (['XS', 'SM'].includes(ViewportDetection.getCurrentViewport())) {
            return false
        }
        return true
    }

    addEventListeners() {
        document.addEventListener('scroll', this.onScroll.bind(this));
    }

    removeEventListeners() {
        document.removeEventListener('scroll', this.onScroll.bind(this));
    }

    onScroll() {
      const scrollPosition = document.documentElement.scrollTop;
      let distanceTop = window.pageYOffset + this.productDetailMainEl.getBoundingClientRect().bottom - this.headerWrapperEl.offsetHeight;
      setTimeout(() => {
            if (scrollPosition > distanceTop) {
// by class, doesnt account for height diff in header
                // if (!this.$emitter._el.classList.contains(this.options.stickyClass)) {
                //     this.$emitter._el.classList.add(this.options.stickyClass)
                //     this.elementNeedsOffsetEl.classList.add(this.options.offsetClass)
                // }
                // now append to main navi
                this.headerWrapperEl.appendChild(this.$emitter._el)
            } else {
                this.elementOriginal.appendChild(this.$emitter._el)
                // if (this.$emitter._el.classList.contains(this.options.stickyClass)) {
                //     this.$emitter._el.classList.remove(this.options.stickyClass)
                //     this.elementNeedsOffsetEl.classList.remove(this.options.offsetClass)
                // }
            }
        }, 200)

    }

}
