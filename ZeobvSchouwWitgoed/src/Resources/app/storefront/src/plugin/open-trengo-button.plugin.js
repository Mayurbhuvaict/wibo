import Plugin from 'src/plugin-system/plugin.class'

export default class OpenTrengoButtonPlugin extends Plugin {
    static options = {
        trengoLauncherClassName: '.trengo_launcher'
    }

    init() {
        this.PluginManager = window.PluginManager,
            self = this

        if (typeof window.Trengo === 'undefined') {
            return
        }

        window.Trengo.on_ready = function() {
            self.initializePlugin()
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

    addEventListeners() {
        this.el.addEventListener('click', this.onClick.bind(this))
    }

    removeEventListeners() {
        this.el.removeEventListener('click', this.onClick.bind(this))
    }

    onClick() {
        window.Trengo.Api.Widget.open('chat');
    }
}
