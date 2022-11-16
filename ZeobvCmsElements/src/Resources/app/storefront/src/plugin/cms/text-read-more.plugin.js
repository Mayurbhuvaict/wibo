import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';

export default class TextReadMorePlugin extends Plugin {
    init() {
        this.expandButton = this.el.querySelector('.cms-element-text-read-more__expand-button');
        this.collapseButton = this.el.querySelector('.cms-element-text-read-more__collapse-button');

        this._registerEvents();
    }

    _registerEvents() {
        const that = this;
        this.expandButton.addEventListener('click', function (e) {
            that.el.classList.add('expanded');

        });

        this.collapseButton.addEventListener('click', function (e) {
            that.el.classList.remove('expanded');
        });
    }
}
