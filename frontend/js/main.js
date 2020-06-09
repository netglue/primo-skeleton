(function(window, document) {
    "use strict";
    /**
     * @type {Element}
     */
    var html;

    /**
     * @type {Element}
     */
    var body;

    /**
     * @type {Object}
     */
    var settings = {
        externalLinksInBlank: false,
        externalLinksNoOpener: true
    };


    document.addEventListener('DOMContentLoaded', function () {
        body = document.body;
        html = document.documentElement;
        html.classList.add('js-ready');
        html.classList.remove('no-js');
        initExternalLinks();
    });

    function initExternalLinks() {
        var nodeList = document.querySelectorAll('a[href^="http"]');
        for (var i = 0; i < nodeList.length; i++) {
            var element = nodeList[i];
            if (element.hostname && element.hostname !== window.location.hostname) {
                if (settings.externalLinksInBlank) {
                    element.setAttribute('target', '_blank');
                }
                if (settings.externalLinksNoOpener && element.hasAttribute('target')) {
                    element.setAttribute('rel', 'noopener');
                }
            }
        }
    }

})(window, document);

