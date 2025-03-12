---
title: Brief Guide on Grid Column
draft: false
date: 2025-03-11
authors:
  - kiatng
categories:
  - Guides
tags:
  - Grid
  - Column
---

# Brief Guide on Grid Column

The Grid Column system in OpenMage provides a powerful way to create and customize admin grid interfaces. Grid columns are essential components of the admin panel that display data in a tabular format, allowing for sorting, filtering, copying, formatting, and other operations on the data. This guide explains how to work with grid columns using the `Mage_Adminhtml_Block_Widget_Grid_Column` class, see [source code](https://github.com/OpenMage/magento-lts/blob/main/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column.php).

<!-- more -->

## Understanding the `addColumn()` Method

The `addColumn()` method is the primary way to add columns to a grid in OpenMage. See code [here](https://github.com/OpenMage/magento-lts/blob/71f38e9f9e1ec98bdea12d00a8e29622df594455/app/code/core/Mage/Adminhtml/Block/Widget/Grid.php#L328-L328).

### Parameters:

1. `$columnId` (string): A unique identifier for the column. This ID is used to reference the column elsewhere in the code.
2. `$column` (array): An array of column attributes that define the column's behavior and appearance.

## Column Attributes

The `$column` parameter accepts various attributes that determine how the column behaves and appears in the grid. It is used to set the data in the class `Mage_Adminhtml_Block_Widget_Grid_Column`. Here are the most commonly used attributes:

### Basic Attributes:

- `header`: The text displayed in the column header
- `index`: The field name from the collection that provides the data for this column, if missing, it is default to `$columnId`
- `width`: The width of the column (in pixels or percentage), if missing, the width is auto adjusted
- `type`: The column type (e.g., `text`, `number`, `date`, `options`, etc.), if missing, it is default to `text`

#### Common Column Types

The 'type' attribute defines the column type. Some common types are:

- `text`: Simple text display
- `number`: Numeric values with optional formatting
- `price`: Price values with currency formatting
- `date`: Date values
- `datetime`: Date and time values
- `options`: Drop-down selection with predefined options
- `action`: Column with action buttons
- `longtext`: Text that may be truncated with a "more" link

For a complete list, see this [folder](https://github.com/OpenMage/magento-lts/tree/main/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer).

### Functionality Attributes:

- `sortable`: (bool) Whether the column can be sorted
- `filter`: (bool) Whether the column can be filtered
- `copyable`: (bool) Whether the column's content can be copied to clipboard
- `filter_index`: The database field to use for filtering (useful when 'index' is a calculated field)
- `renderer`: A custom render class for the column
- `filter_condition_callback`: A callback function for custom filtering logic
- `no_link`: (bool) Prevents rendering links in the column

### Data Formatting Attributes:

- `align`: Text alignment within the column (`left`, `center`, `right`)
- `frame_callback`: A callback function to format the column's data
- `column_css_class`: Add additional CSS classes

### Attributes Specific to Column Types
There are attributes that are applied to specific column types:

- `actions`: Array of actions for an `actions` column type
- `options`: Array of options for an `options` column type
- `format`: Format for date/time column types
- `nl2br`: (bool) Whether to convert newlines to `<br>` tags for a `longtext` column type

## Example Usage

Here's a basic example of adding columns to a grid:

```php
protected function _prepareColumns()
{
    $this->addColumn('entity_id', [
        'header'    => $this->__('ID'),
        'align'     => 'right',
        'width'     => '50px',
        'index'     => 'entity_id',
        'type'      => 'number',
        'sortable'  => true,
    ]);

    $this->addColumn('name', [
        'header'    => $this->__('Name'),
        'index'     => 'name',
        'type'      => 'text',
        'copyable'  => true,
    ]);

    $this->addColumn('created_at', [
        'header'    => $this->__('Created At'),
        'index'     => 'created_at',
        'type'      => 'datetime',
        'filter'    => true,
        'sortable'  => true,
    ]);

    $this->addColumn('status', [
        'header'    => $this->__('Status'),
        'index'     => 'status',
        'type'      => 'options',
        'options'   => [
            1 => 'Enabled',
            0 => 'Disabled',
        ],
    ]);

    return parent::_prepareColumns();
}
```

## Advanced Usage: Custom Render and Filter classes

For more complex column requirements, you can create custom render and filter classes:

```php
$this->addColumn('custom_field', [
    'header'    => $this->__('Custom Field'),
    'renderer'  => 'Namespace_Module_Block_Adminhtml_Grid_Renderer_Custom',
    'filter'    => 'Namespace_Module_Block_Adminhtml_Grid_Filter_Custom',
]);

```

The render and filter classes are reusable for other grids. If there is no such need, you can use the callback attributes:

```php
// in addColumn()
    'frame_callback' => [$this, '_decorateUserUpdatedAt'],
    'filter_condition_callback' => [$this, '_findInSet'],
// more code

/**
 * @param string $value
 * @param Varien_Object $row
 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
 * @param bool $isExport
 * @return string
 */
protected function _decorateUserUpdatedAt($value, $row, $column, $isExport)
{
    if (!$isExport) {
        return $value > $row->getAdminUpdatedAt())
            ? '<strong><span class="not-available">' . $value . '</span></strong>'
            : '<span class="available">' . $value . '</span>';
    }

    return $value;
}

/**
 * @param Varien_Data_Collection_Db $collection
 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
 * @return void
 */
protected function _findInSet($collection, $column)
{
    if ($value = $column->getFilter()->getValue()) {
        $collection->addFieldToFilter('apply_to', ['finset' => $value]);
    }
}
```

## Conclusion

The Grid Column system in OpenMage provides a flexible and powerful way to display and manipulate data in the admin panel. By understanding the various attributes and options available, you can create highly customized and functional grid interfaces for your admin users.

