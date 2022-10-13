import Plugin from "src/plugin-system/plugin.class";
import DomAccess from "src/helper/dom-access.helper";
import ViewportDetection from "src/helper/viewport-detection.helper";

export default class SlideUsps extends Plugin {
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
        this.showSlide();
        this.initialized = true;
    }

    destroy() {
        this.initialized = false;
    }

    pluginShouldBeActive() {
        if (["XS", "SM"].includes(ViewportDetection.getCurrentViewport())) {
            return true;
        }
        return false;
    }

    showSlide() {
        let currentSlideIndex = 0;
        const slideArray = [];
        let slides = this.el.getElementsByClassName("col-md-4");

        for (var i = 0; i < slides.length; i++) {
            // hides 1 2 and 3
            slides[i].classList.add("d-none");
            if (i < 1) {
                slides[i].classList.add("d-block");
            }
            slideArray.push(slides[i]);
        }

        function advanceSliderItem() {
            currentSlideIndex++;

            if (currentSlideIndex >= slideArray.length) {
                currentSlideIndex = 0;
            }
            // hide 1 2 3
            for (var i = 0; i < slides.length; i++) {
                slides[i].classList.add("d-none");
                slides[i].classList.remove("d-block");
            }
            //  show i
            slides[currentSlideIndex].classList.add("d-block");
        }

        let intervalID = setInterval(advanceSliderItem, 3000);
    }
}
