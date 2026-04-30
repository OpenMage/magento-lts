---
name: mage-module-adminhtml
description: OpenMage admin UI — Container/Grid/Form/Tabs block hierarchy, mass-actions, AdminNotification inbox, form key, widget grid serializers, dependent fieldsets. Use when editing under app/code/core/Mage/Adminhtml/Block/, building admin grids/forms/tabs, implementing mass-actions, or wiring admin notifications.
---

# mage-module-adminhtml

Admin UI primitives for OpenMage. Every admin "list + edit" screen is the same four-block sandwich: a `*_Container` wraps a `*_Grid`; a `*_Edit` (Form_Container) wraps a `*_Edit_Tabs` whose tabs each extend `Widget_Form`. Buttons, ACL guards, layout wiring, mass-actions, and form-key validation all live in fixed slots — extend the right slot and don't reinvent.

## Block hierarchy

```
Widget_Container               (header + buttons)
 └── Widget_Grid_Container     (adds Add-New button, instantiates the grid child)
      └── Widget_Grid          (columns, filters, mass-action, pager)

Widget_Container
 └── Widget_Form_Container     (Save/Delete/Back buttons, header text)
      └── Widget_Form          (one Varien_Data_Form, fieldsets, fields)
      └── Widget_Tabs          (tab strip; each tab is its own Widget_Form impl)
```

Slot conventions (override these, not the parents):

- `_prepareLayout()` — instantiate children; ALWAYS `return parent::_prepareLayout();`.
- `_prepareCollection()` — grid only; build the data collection, call `parent` last.
- `_prepareColumns()` — grid only; `addColumn(...)` calls; `return parent::_prepareColumns();`.
- `_prepareMassaction()` — grid only; `getMassactionBlock()->addItem(...)`.
- `_prepareForm()` — form only; build `Varien_Data_Form`, set on `$this->setForm($form)`, return parent.
- `_beforeToHtml()` — tabs container only; lazy `addTab(...)` calls (children loaded then).

## Grid container + grid

The container is trivial — set `_controller`, `_blockGroup` (defaults `adminhtml`), `_headerText`, `_addButtonLabel`, then guard the Add button on ACL. `Widget_Grid_Container::_prepareLayout()` auto-creates the child grid as `{_blockGroup}/{_controller}_grid`.

```php
class Mage_Adminhtml_Block_Foo_Bar extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller     = 'foo_bar';      // → adminhtml/foo_bar_grid
        $this->_headerText     = Mage::helper('foo')->__('Manage Bars');
        $this->_addButtonLabel = Mage::helper('foo')->__('Add Bar');
        parent::__construct();
        if (!Mage::getSingleton('admin/session')->isAllowed('foo/bar/save')) {
            $this->_removeButton(self::BUTTON_TYPE_ADD);
        }
    }
}
```

### Grid `_prepareColumns()` skeleton

Column `type` values: `text` (default), `number`, `price`, `currency`, `date`, `datetime`, `options` (with `options` map), `checkbox`, `massaction`, `action`, `country`, `store`, `wrapline`, `concat`, `radio`. Custom rendering via `'renderer' => 'group/class'`.

```php
protected function _prepareColumns()
{
    $this->addColumn('entity_id', [
        'header' => Mage::helper('foo')->__('ID'),
        'width'  => '50px',
        'index'  => 'entity_id',
        'type'   => 'number',
    ]);
    $this->addColumn('name', [
        'header' => Mage::helper('foo')->__('Name'),
        'index'  => 'name',
    ]);
    $this->addColumn('status', [
        'header'  => Mage::helper('foo')->__('Status'),
        'index'   => 'status',
        'type'    => 'options',
        'options' => Mage::getSingleton('foo/bar')::getOptionArray(),
    ]);
    $this->addColumn('created_at', [
        'header' => Mage::helper('foo')->__('Created'),
        'index'  => 'created_at',
        'type'   => 'datetime',
    ]);
    $this->addColumn('action', [
        'type'    => 'action',
        'getter'  => 'getId',
        'actions' => [[
            'caption' => Mage::helper('foo')->__('Edit'),
            'url'     => ['base' => '*/*/edit'],
            'field'   => 'id',
        ]],
        'filter'   => false,
        'sortable' => false,
        'index'    => 'stores',
    ]);
    return parent::_prepareColumns();
}
```

`getRowUrl($row)` is what the row click navigates to (default returns `'#'`). `getGridUrl()` is the AJAX reload endpoint when `setUseAjax(true)`.

### Mass-action wiring (block + controller)

Block side — declared in `_prepareMassaction()`. Use the `MassAction::*` constants for the canonical actions (`DELETE`, `STATUS`, `ATTRIBUTES`).

```php
use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

protected function _prepareMassaction()
{
    $this->setMassactionIdField('entity_id');
    $this->getMassactionBlock()->setFormFieldName('bar');         // POST key

    $this->getMassactionBlock()->addItem(MassAction::DELETE, [
        'label' => Mage::helper('foo')->__('Delete'),
        'url'   => $this->getUrl('*/*/massDelete'),
        'confirm' => Mage::helper('foo')->__('Are you sure?'),
    ]);
    $this->getMassactionBlock()->addItem('change_status', [
        'label'      => Mage::helper('foo')->__('Change Status'),
        'url'        => $this->getUrl('*/*/massStatus'),
        'additional' => [
            'status' => [
                'name'   => 'status',
                'type'   => 'select',
                'class'  => 'required-entry',
                'label'  => Mage::helper('foo')->__('Status'),
                'values' => Mage::getSingleton('foo/bar')::getOptionArray(),
            ],
        ],
    ]);
    return parent::_prepareMassaction();
}
```

Controller side — the action receives the IDs under the `formFieldName` POST key. Force-protect destructive actions via `_setForcedFormKeyActions()` in `preDispatch`.

```php
public function massDeleteAction()
{
    $ids = $this->getRequest()->getParam('bar');
    if (!is_array($ids) || $ids === []) {
        $this->_getSession()->addError($this->__('Please select bar(s).'));
    } else {
        try {
            $coll = Mage::getResourceModel('foo/bar_collection')
                ->addFieldToFilter('entity_id', ['in' => $ids]);
            $coll->delete();
            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been deleted.', count($ids)),
            );
        } catch (Throwable $e) {
            $this->_getSession()->addError($e->getMessage());
        }
    }
    $this->_redirect('*/*/index');
}

public function preDispatch()
{
    $this->_setForcedFormKeyActions(['delete', 'massDelete']);
    return parent::preDispatch();
}
```

## Form container + form + tabs

`Widget_Form_Container` builds the Save/Save&Continue/Delete/Back buttons from `$_objectId` + `$_controller`. Override `getHeaderText()` to label the page based on `Mage::registry()`. The actual form lives in `*_Edit_Form` (single-form pages) OR in tab blocks under `*_Edit_Tab_*` (multi-section pages) collected by `*_Edit_Tabs`.

### Form `_prepareForm()` skeleton

Tabs implement `Mage_Adminhtml_Block_Widget_Tab_Interface` (`getTabLabel`, `getTabTitle`, `canShowTab`, `isHidden`).

```php
class Mage_Adminhtml_Block_Foo_Bar_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_bar');
        $form  = new Varien_Data_Form();
        $form->setHtmlIdPrefix('bar_');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => Mage::helper('foo')->__('Bar Information'),
        ]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
        $fieldset->addField('title', 'text', [
            'name'     => 'title',
            'label'    => Mage::helper('foo')->__('Title'),
            'required' => true,
        ]);
        $fieldset->addField('is_active', 'select', [
            'name'    => 'is_active',
            'label'   => Mage::helper('foo')->__('Status'),
            'options' => ['1' => $this->__('Enabled'), '0' => $this->__('Disabled')],
        ]);
        $fieldset->addField('store_id', 'multiselect', [
            'name'   => 'stores[]',
            'label'  => Mage::helper('foo')->__('Store View'),
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ]);

        Mage::dispatchEvent('adminhtml_foo_bar_edit_tab_main_prepare_form', ['form' => $form]);
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel(): string { return Mage::helper('foo')->__('General'); }
    public function getTabTitle(): string { return $this->getTabLabel(); }
    public function canShowTab(): bool    { return true; }
    public function isHidden(): bool      { return false; }
}
```

Field types (Varien_Data_Form_Element_*): `text`, `textarea`, `hidden`, `password`, `checkbox`, `checkboxes`, `select`, `multiselect`, `radio`, `radios`, `file`, `image`, `date`, `time`, `editor` (TinyMCE), `note`, `link`, `submit`, `button`, `gallery`, `obscure`. Custom renderers via `$field->setRenderer($block)`.

### Tabs (`Widget_Tabs`)

Lazy-add tabs in `_beforeToHtml()` so child layout is initialized. AJAX tabs use `'class' => 'ajax'` + `'url'`.

```php
protected function _beforeToHtml()
{
    $this->addTab('main', [
        'label'   => Mage::helper('foo')->__('General'),
        'content' => $this->getLayout()->createBlock('adminhtml/foo_bar_edit_tab_main')->toHtml(),
        'active'  => true,
    ]);
    $this->addTab('history', [
        'label' => Mage::helper('foo')->__('History'),
        'class' => 'ajax',
        'url'   => $this->getUrl('*/*/history', ['_current' => true]),
    ]);
    return parent::_beforeToHtml();
}
```

## Form key

All admin POSTs require a valid form key. `Mage_Adminhtml_Controller_Action::preDispatch()` validates automatically for POSTs; GET-style destructive actions must be force-protected:

```php
public function preDispatch()
{
    $this->_setForcedFormKeyActions(['delete', 'massDelete', 'massStatus']);
    return parent::preDispatch();
}
```

Manually: `if (!$this->_validateFormKey()) { $this->_redirect('*/*/'); return; }`. Templates emit the field with `<?= $this->getBlockHtml('formkey') ?>` (or `getFormKey()` for the bare value in JS posts).

## Widget grid serializer

When an edit form embeds another grid (e.g. "products in this rule"), the serializer block packs the selected rows into a hidden form field on submit. Pattern:

1. Layout adds `adminhtml/widget_grid_serializer` as a child of the form block, configured with `setGridBlock()`, `setInputElementName()`, and `addColumnInputName('your_column')`.
2. The grid's massaction column is named the same as the column input.
3. On submit, the form receives `your_column => [id1, id2, ...]`.

See `Mage_Adminhtml_Block_Widget_Grid_Serializer` for the full surface.

## Dependent fieldsets in `system.xml`

Hide/show config fields based on another field's value with `<depends>`:

```xml
<show_swatch translate="label">
    <label>Show Swatches</label>
    <frontend_type>select</frontend_type>
    <source_model>adminhtml/system_config_source_yesno</source_model>
    <depends><enabled>1</enabled></depends>
</show_swatch>
```

Cross-link `openmage-system-config` for the full backend/source/frontend model story.

## AdminNotification inbox

`Mage_AdminNotification_Model_Inbox` drives the bell-icon dropdown and System → Notifications grid. Severities: `SEVERITY_CRITICAL` / `MAJOR` / `MINOR` / `NOTICE`. Push messages from anywhere (observer, setup script, cron):

```php
Mage::getModel('adminnotification/inbox')->addCritical(
    'Backup failed',
    'Nightly DB backup did not complete; check logs.',
    Mage::helper('adminhtml')->getUrl('*/system_backup'),
);
// or addMajor(...), addMinor(...), addNotice(...)
```

The "subscriber" pattern is `Mage_AdminNotification_Model_Observer::preDispatch` wired against `controller_action_predispatch` (admin scope) — it fetches the upstream feed (`adminnotification/feed` → `checkUpdate()`) and parses new entries into the inbox. To add your own feed source, mirror that observer and call `Mage::getModel('adminnotification/inbox')->parse($rows)`.

## Tests touching admin globals

PHPUnit tests that boot adminhtml blocks (anything reading `Mage::getSingleton('admin/session')`, the form-key cookie, design package, or admin URL builder) leak global state. Mark them so they run in their own process:

```php
/**
 * @group Block
 * @group runInSeparateProcess
 * @runInSeparateProcess
 */
public function testAdminUrl(): void { ... }
```

The `@group runInSeparateProcess` lets CI shard them; the `@runInSeparateProcess` annotation is what actually forks. Both are required by convention. See `tests/unit/Mage/Adminhtml/Block/CacheTest.php` for the canonical example. Cross-link `phpunit-openmage-tests` for the rest of the test setup (Subject pattern, data-provider traits, `OpenMageTest` base class).

## Cross-references

- `openmage-acl-adminhtml` — `_isAllowed()`, `adminhtml.xml` ACL tree, menu wiring.
- `openmage-system-config` — `system.xml`, backend/source/frontend models, scopes.
- `openmage-layout-blocks` — layout XML, block aliases, template fallback.
- `openmage-controllers-routing` — admin controller base, `loadLayout/renderLayout`, dispatch flow.
- `phpunit-openmage-tests` — `OpenMageTest` base, separate-process semantics.
