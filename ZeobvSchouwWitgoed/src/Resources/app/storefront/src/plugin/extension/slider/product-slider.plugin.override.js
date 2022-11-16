import ProductSliderPlugin from  'src/plugin/slider/product-slider.plugin';

export default class ProductSliderPluginOverride extends ProductSliderPlugin {

    /**
     * default slider options
     *
     * @type {*}
     */
    static options = ProductSliderPlugin.options;

    /**
     * returns the slider settings for the current viewport
     *
     * @param viewport
     * @private
     */
    _getSettings(viewport) {
        super._getSettings(viewport);

        this._addItemLimit();
    }

    /**
     * extends the slider settings with the slider item limit depending on the product-box and the container width
     *
     * @private
     */
    _addItemLimit() {
        const containerWidth = this._getInnerWidth();
        const gutter = this._sliderSettings.gutter;
        const itemWidth = parseInt(this.options.productboxMinWidth.replace('px', ''), 0);

        const itemLimit = Math.floor(containerWidth / (itemWidth + gutter));

        const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)

        if (vw >= 768) {
            this._sliderSettings.items = Math.max(1, itemLimit);
        } else {
            this._sliderSettings.items = 2;
            this._sliderSettings.itemWidth = Math.floor((vw - gutter) / 2);
        }
    }
}
