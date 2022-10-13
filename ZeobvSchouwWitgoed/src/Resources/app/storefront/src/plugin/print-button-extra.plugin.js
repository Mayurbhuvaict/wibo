import Plugin from 'src/plugin-system/plugin.class'

export default class PrintButtonExtraPlugin extends Plugin {

    static options = {
    }

    init() {
        this.PluginManager = window.PluginManager
        this.initializePlugin()
    }

    initializePlugin() {
        this.addEventListeners()
        this.initialized = true
    }

    destroy() {
        this.removeEventListeners()
        this.initialized = false
    }

    addEventListeners() {
        this.el.addEventListener('click', this.onClick.bind(this))
    }

    removeEventListeners() {
        this.el.removeEventListener('click', this.onClick.bind(this))
    }

    onClick() {
        window.print()
    }

}
