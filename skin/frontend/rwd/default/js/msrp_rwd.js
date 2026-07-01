/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 * Wraps Catalog.Map.showHelp to adjust popup positioning for RWD theme.
 */

(function () {
    var originalShowHelp = Catalog.Map.showHelp;
    Catalog.Map.showHelp = function (event) {
        var helpBox = document.getElementById('map-popup');
        var bodyNode = document.body;
        originalShowHelp.call(this, event);

        if (helpBox && this != Catalog.Map && Catalog.Map.active != this.link) {
            helpBox.classList.remove('map-popup-right');
            helpBox.classList.remove('map-popup-left');
            if (bodyNode.offsetWidth < event.pageX + (helpBox.offsetWidth / 2)) {
                helpBox.classList.add('map-popup-left');
            } else if (event.pageX - (helpBox.offsetWidth / 2) < 0) {
                helpBox.classList.add('map-popup-right');
            }
        }
    };
})();
