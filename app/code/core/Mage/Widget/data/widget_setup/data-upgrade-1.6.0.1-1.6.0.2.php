<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Remove storefront layout updates of widget instances that the widget generator would not produce
 * (GHSA-v7cc-v57g-wx93): instances whose type no enabled module declares in a widget.xml, and
 * layout updates whose XML is not the exact shape generated for the instance's declared type.
 * Widget instances themselves are kept; saving one again regenerates its layout updates.
 *
 * Each removed layout update is written to var/log/widget_instance_cleanup.log before it is
 * deleted, with its XML, handle, sort order and store/theme links, so it can be reviewed or put
 * back. Linked layout updates that belong to no widget instance are logged there for review, but
 * kept.
 *
 * Widget types are read from the modules enabled while this upgrade runs. If a module that provides
 * widgets is disabled at that time, the layout updates of its widget instances are removed: enable
 * the module and save each of those widget instances again to restore them.
 */

use Monolog\Level;

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

// Read the widget types of the modules enabled now, not a list cached before this upgrade
Mage::app()->removeCache('widget_config');
Mage::getSingleton('widget/widget')->resetDeclaredTypes();

$log = static function (string $message): void {
    Mage::log($message, Level::Warning, 'widget_instance_cleanup.log', true);
};
// Encoded: stored types and XML may contain quotes, markup or line breaks
$encode = static fn(mixed $data): string => json_encode(
    $data,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR,
);

/** @var Mage_Widget_Model_Resource_Widget_Instance $resource */
$resource = Mage::getResourceSingleton('widget/widget_instance');

$resource->deleteUnexpectedLayoutUpdates(static function (array $removals) use ($log, $encode): void {
    foreach ($removals as $removal) {
        $advice = $removal['reason'] === Mage_Widget_Model_Widget_Instance::LAYOUT_UPDATE_UNDECLARED_TYPE
            ? 'If the module that provides this widget type was disabled during the upgrade, enable it and save the widget instance again to regenerate its layout updates. Otherwise delete the widget instance.'
            : 'Review the widget instance and save it again to regenerate its layout updates.';
        $log(sprintf(
            'Removing layout update #%d of widget instance #%d (type %s): %s. %s Removed layout update: %s',
            $removal['layout_update_id'],
            $removal['instance_id'],
            $encode($removal['type']),
            $removal['reason'],
            $advice,
            $encode($removal),
        ));
    }
});

$resource->reportUnownedLayoutUpdates(static function (array $layoutUpdates) use ($log, $encode): void {
    foreach ($layoutUpdates as $layoutUpdate) {
        $log(sprintf(
            'Kept layout update #%d: it is linked to the storefront but belongs to no widget instance, and its XML is not what the widget generator produces. Review it, and delete it if no installed module owns it. Layout update: %s',
            $layoutUpdate['layout_update_id'],
            $encode($layoutUpdate),
        ));
    }
});

// Clean (not just invalidate) the caches that hold widget layout, so removed updates stop rendering now.
// This runs every time: a rerun after a run that stopped part way removes nothing, but its deletions may still be cached.
$config = Mage::getConfig();
$cacheTypes = $config instanceof Mage_Core_Model_Config
    ? $config->getNode(Mage_Widget_Model_Widget_Instance::XML_NODE_RELATED_CACHE)
    : false;
if ($cacheTypes instanceof Mage_Core_Model_Config_Element) {
    $cacheTypes = $cacheTypes->asArray();
    foreach (is_array($cacheTypes) ? array_keys($cacheTypes) : [] as $cacheType) {
        Mage::app()->getCacheInstance()->cleanType($cacheType);
    }
}

$installer->endSetup();
