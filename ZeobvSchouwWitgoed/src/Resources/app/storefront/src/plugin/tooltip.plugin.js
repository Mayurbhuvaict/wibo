import Plugin from "src/plugin-system/plugin.class";

export default class Tooltip extends Plugin {
    init() {
        this.PluginManager = window.PluginManager;
        this.initializePlugin();
    }

    initializePlugin() {
        this.addClickEvents()
        this.initialized = true
    }

    destroy() {
        this.initialized = false
    }

    addClickEvents() {
        const element = this.el
        element.addEventListener('mouseenter', this.insertTooltipElement)
        element.addEventListener('mouseleave', this.removeTooltipElement)
    }

    insertTooltipElement(event) {
        var scrollY = window.scrollY || window.pageYOffset;
        var scrollX = window.scrollX || window.pageXOffset;
        var tooltipTop = event.pageY;
        var tooltipLeft = event.pageX + 10;

        tooltipTop = (tooltipTop - scrollY + event.target.offsetHeight + 20 >= window.innerHeight ? (tooltipTop - event.target.offsetHeight - 20) : tooltipTop);
        tooltipLeft = (tooltipLeft - scrollX + event.target.offsetWidth + 20 >= window.innerWidth ? (tooltipLeft - event.target.offsetWidth - 20) : tooltipLeft);

        document.body.insertAdjacentHTML(
            "afterend",
            "<div class='schouw-tooltip' style='position: absolute; top: " + tooltipTop + "px; left: " + tooltipLeft + "px;'><div class='tooltip-arrow'></div><div class='tooltip-content'>" + event.target.firstElementChild.innerHTML + "</div></div>"
        )
    }

    removeTooltipElement() {
        const elements = document.getElementsByClassName('schouw-tooltip')
        while(elements.length > 0) {
            elements[0].parentNode.removeChild(elements[0])
        }
    }
}
