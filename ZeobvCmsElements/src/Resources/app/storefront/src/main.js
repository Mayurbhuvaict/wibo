import TextReadMore from './plugin/cms/text-read-more.plugin.js';

// Register via the existing PluginManager
window.PluginManager.register('TextReadMore', TextReadMore, '[data-cms-text-read-more]');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}
