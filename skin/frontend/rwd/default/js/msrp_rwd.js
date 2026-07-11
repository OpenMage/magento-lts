/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

Catalog.Map.showHelp = Catalog.Map.showHelp.wrap(function (parent, event) {
    var helpBox = document.getElementById('map-popup');
    var bodyNode = document.getElementsByTagName('body')[0];
    parent(event);
    
    if (helpBox && this != Catalog.Map && Catalog.Map.active != this.link) {
        helpBox.classList.remove('map-popup-right');
        helpBox.classList.remove('map-popup-left');
        if (Element.getWidth(bodyNode) < event.pageX + (Element.getWidth(helpBox) / 2)) {
            helpBox.classList.add('map-popup-left');
        } else if (event.pageX - (Element.getWidth(helpBox) / 2) < 0) {
            helpBox.classList.add('map-popup-right');
        }
    }
});
