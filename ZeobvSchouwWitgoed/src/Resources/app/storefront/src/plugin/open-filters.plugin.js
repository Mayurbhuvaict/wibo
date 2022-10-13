import Plugin from "src/plugin-system/plugin.class";
import DomAccess from "src/helper/dom-access.helper";
import ViewportDetection from "src/helper/viewport-detection.helper";

export default class OpenFiltersPlugin extends Plugin {
    static options = {};

    init() {
        this.PluginManager = window.PluginManager;

        this.subscribeViewportEvents();

        if (this.pluginShouldBeActive()) this.initializePlugin();
    }

    subscribeViewportEvents() {
        // scope:test / scope: this gives a this reference to what we expected.
        document.$emitter.subscribe("Viewport/hasChanged", this.update, {
            scope: this,
        });
    }

    update() {
        // this now shows current plugin, if scope above is test will show test
        // console.warn(this)
        if (this.pluginShouldBeActive()) {
            if (this.initialized) return;

            this.initializePlugin();
        } else {
            if (!this.initialized) return;

            this.destroy();
        }
    }

    initializePlugin() {
        this.openFilters();
        this.initialized = true;
    }

    destroy() {
        this.initialized = false;
    }

    pluginShouldBeActive() {
        if (["XS", "SM"].includes(ViewportDetection.getCurrentViewport())) {
            return false;
        }
        return true;
    }

    openFilters() {
        let filters = this.el.getElementsByClassName("filter-multi-select");

        for (var i = 0; i < filters.length; i++) {
            if (i < 5) {
                // console.log(filters[i]);
                // console.log(filters[i].querySelector(".collapse"));
                filters[i].querySelector(".collapse").classList.add("show");
                filters[i]
                    .querySelector(".filter-panel-item-toggle")
                    .setAttribute("aria-expanded", true);
            }
        }
    }
}
