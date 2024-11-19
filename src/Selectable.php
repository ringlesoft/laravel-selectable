<?php

namespace RingleSoft\LaravelSelectable;

use Closure;
use Illuminate\Support\Collection;

class Selectable
{
    private Collection $_collection;
    private string|Closure $_value;
    private string|Closure $_label;
    private mixed $_selected = null;
    private mixed $_disabled = null;
    private array $_dataAttributes = [];
    private array $_classes = [];

    /**
     * @param Collection $collection
     * @param string|Closure|null $label
     * @param string|Closure|null $value
     * @param mixed|null $selected
     * @param mixed|null $disabled
     */
    public function __construct(Collection $collection, string|Closure|null $label = null, string|Closure|null $value = null, mixed $selected = null, mixed $disabled = null)
    {
        $this->_collection = $collection;
        $this->_label = $label ?? 'name';
        $this->_value = $value ?? 'id';
        $this->_selected = $selected ?? null;
        $this->_disabled = $disabled ?? null;
    }


    /**
     * Check if the item should be selected
     * @param object $item
     * @param int|string|null $index
     * @return bool
     */
    private function _shouldSelect(mixed $item, int|string|null $index = null): bool
    {
        if ($this->_selected instanceof Closure) {
            return (bool)call_user_func($this->_selected, $item, $index);
        }

        if($this->_value instanceof Closure) {
            $optionValue = call_user_func($this->_value, $item, $index);
        } else {
            $optionValue = (is_object($item) ? ($item->{$this->_value} ?? "") : $item);
            if(is_array($item)) {
                $optionValue = $item[$this->_value] ?? reset($item);
            }
        }
        if (is_object($this->_selected)) {
            return ((string)$this->_selected->{$this->_value} === (string)$optionValue);
        }

        if (is_array($this->_selected)) {
            foreach ($this->_selected as $selectedItem) {
                if (is_object($selectedItem)) {
                    return ((string)$selectedItem->{$this->_value} === (string)$optionValue);
                }
                if (is_array($selectedItem)) {
                    return (array_key_exists($this->_value, $selectedItem) && (string)$selectedItem[$this->_value] === (string)$optionValue);
                }
                if ((string)$selectedItem === (string)$optionValue) {
                    return true;
                }
            }
        } else if ((string)$this->_selected === (string)$optionValue) {
            return true;
        }
        return false;
    }

    /**
     * Check if the item should be selected
     * @param mixed $item
     * @param int|string|null $index
     * @return bool
     */
    private function _shouldDisable(mixed $item, int|string|null $index = null): bool
    {
        if ($this->_disabled instanceof Closure) {
            return (bool)call_user_func($this->_disabled, $item, $index);
        }

        if ($this->_value instanceof Closure) {
            $lineValue = call_user_func($this->_value, $item, $index);
        } else {
            $lineValue = (is_object($item) ? ($item->{$this->_value} ?? "") : $item);
            if(is_array($item)){
                $lineValue = $item[$this->_value] ??reset($item);
            }
        }

        if (is_object($this->_disabled)) {
            return ((string)$this->_disabled->{$this->_value} === (string)$lineValue);
        }

        if (is_array($this->_disabled)) {
            foreach ($this->_disabled as $disabledItem) {
                if (is_object($disabledItem)) {
                    return ((string)$disabledItem->{$this->_value} === (string)$lineValue);
                }
                if (is_array($disabledItem)) {
                    return (array_key_exists($this->_value, $disabledItem) && (string)$disabledItem[$this->_value] === (string)$lineValue);
                }
                if ((string)$disabledItem === (string)$lineValue) {
                    return true;
                }
            }
        } else if ((string)$this->_disabled === (string)$lineValue) {
            return true;
        }
        return false;
    }

    /**
     * Prepare data attributes
     * @param mixed $item
     * @param int|null $index
     * @return array
     */
    private function _getDataAttributes(mixed $item, int|null $index = null): array
    {
        $dataAttributes = [];
        if (count($this->_dataAttributes) > 0) {
            foreach ($this->_dataAttributes as $attribute => $value) {
                $dataAttributes[$attribute] = ($value instanceof Closure) ? $value($item) : ($item->{$value} ?? '');
            }
        }
        return $dataAttributes;
    }

    /**
     * Generate select options from a Collection instance
     * @param Collection $collection
     * @param int $lastIndex
     * @return string
     */
    private function _generateOptions(Collection $collection, int $lastIndex = 0): string
    {
        $html = "";
        foreach ($collection as $index => $item) {
            if (($item instanceof Collection)) { // Grouped options
                $html .= "<optgroup label=\"{$index}\">";
                $html .= $this->_generateOptions($item);
                $html .= "</optgroup>";
            } else {
                if ($this->_label instanceof Closure) {
                    $optionLabel = call_user_func($this->_label, $item, $index);
                } else {
                    $optionLabel = is_object($item) ? ($item->{$this->_label} ?? "N/A") : ($item);
                    if(is_array($item)) {
                        $optionLabel = $item[$this->_label] ?? array_keys($item)[0];
                    }
                }
                if ($this->_value instanceof Closure) {
                    $optionValue = call_user_func($this->_value, $item, $index);
                } else {
                    $optionValue = is_object($item) ? ($item->{$this->_value} ?? "") : $item;
                    if(is_array($item)) {
                        $optionValue = $item[$this->_value] ?? reset($item);
                    }
                    if (is_string($index) && is_string($item)) {
                        $optionValue = $index;
                    }
                }
                $html .= "<option value=\"{$optionValue}\"";
                if ($this->_shouldSelect($item, $index)) {
                    $html .= " selected";
                }
                if ($this->_shouldDisable($item, $index)) {
                    $html .= " disabled";
                }
                if (count($this->_dataAttributes) > 0) {
                    foreach ($this->_getDataAttributes($item, $index) as $key => $value) {
                        $html .= " data-{$key}=\"{$value}\"";
                    }
                }
                if (count($this->_classes) > 0) {
                    $html .= " class=\"" . (implode(' ', $this->_classes)) . "\"";
                }
                $html .= " >{$optionLabel}</option>";
            }
        }
        return $html;
    }

    /**
     * Generate select options from a Collection instance
     * @param Collection $collection the collection instance to be used
     * @param string|Closure|null $label the field to be used as the label for the option (default is 'name')
     * @param string|Closure|null $value the field to be used as value of the option (default is 'id')
     * @param mixed $selected selected value/values
     * @param mixed|null $disabled
     * @return string
     */
    public static function collectionToSelectOptions(
        Collection          $collection,
        string|Closure|null $label = null,
        string|Closure|null $value = null,
        mixed               $selected = null,
        mixed               $disabled = null,
    ): string
    {
        return (new self($collection, $label, $value, $selected, $disabled))->toSelectOptions();
    }

    /**
     * Create Selectable instance from a collection instance
     * @param Collection $collection
     * @return self
     */
    public static function fromCollection(Collection $collection): self
    {
        return new self($collection);
    }

    /**
     * Generate select options from this instance
     * @return string
     */
    public function toSelectOptions(): string
    {
        return $this->_generateOptions($this->_collection);
    }

    /**
     * Return a collection of selectable items
     * @return Collection
     */
    public function toSelectItems(): Collection
    {
        return $this->_collection->map(function ($item, $index) {
            if ($this->_label instanceof Closure) {
                $optionLabel = call_user_func($this->_label, $item, $index);
            } else {
                $optionLabel = is_object($item) ? ($item->{$this->_label} ?? "N/A") : ($item);
            }
            if ($this->_value instanceof Closure) {
                $optionValue = call_user_func($this->_value, $item, $index);
            } else {
                $optionValue = is_object($item) ? ($item->{$this->_value} ?? "") : $item;
                if (is_string($index) && is_string($item)) {
                    $optionValue = $index;
                }
            }
            return [
                'value' => $optionValue,
                'label' => $optionLabel,
                'isSelected' => $this->_shouldSelect($item, $index),
                'isDisabled' => $this->_shouldDisable($item, $index),
                'data' => $this->_getDataAttributes($item, $index),
                'classes' => $this->_classes
            ];
        });
    }

    /**
     * Specify the label for the selectable items
     * @param string|Closure $label name of the field to be used as label
     * @return $this
     */
    public function withLabel(string|Closure $label): self
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * Specify the value for the selectable items
     * @param string|Closure $value name of the field to be used as value
     * @return $this
     */
    public function withValue(string|Closure $value): self
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Specify the selected values for the selectable items
     * @param mixed $selected
     * @return $this
     */
    public function withSelected(mixed $selected): self
    {
        $this->_selected = $selected;
        return $this;
    }

    /**
     * Specify the disabled values for the selectable items
     * @param mixed $disabled
     * @return $this
     */
    public function withDisabled(mixed $disabled): self
    {
        $this->_disabled = $disabled;
        return $this;
    }

    /**
     * Specify a data attribute for the selectable items
     * @param string $attribute
     * @param string|Closure $value
     * @return $this
     */
    public function withDataAttribute(string $attribute, string|Closure $value): self
    {
        $this->_dataAttributes[$attribute] = $value;
        return $this;
    }

    public function withClass(string $class): self
    {
        $this->_classes = array_unique([...$this->_classes, ...explode(' ', $class)]);
        return $this;
    }

    /**
     * Convert a Selectable instance back to a Collection instance
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return $this->_collection;
    }

    /**
     * Call a method on the collection
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {
        $allowedMethods = [
            'groupBy', 'add', 'zip', 'unique', 'range', 'merge',
            'diff', 'diffUsing', 'diffAssoc', 'diffAssocUsing',
            'diffKeys', 'diffKeysUsing', 'forget', 'merge', 'mergeRecursive', 'combine',
            'union', 'nth', 'only', 'select', 'prepend', 'push', 'concat', 'put', 'random',
            'replace', 'replaceRecursive', 'reverse', 'shuffle', 'sliding', 'skip',
            'skipUntil', 'skipWhile', 'slice', 'split', 'splitIn', 'chunk', 'chunkWhile',
            'sort', 'sortDesc', 'sortBy', 'sortByMany', 'sortByDesc', 'sortKeys',
            'sortKeysDesc', 'sortKeysUsing', 'splice', 'take', 'takeUntil', 'takeWhile',
            'transform', 'dot', 'undot', 'unique', 'values', 'zip', 'pad', 'getIterator',
            'countBy', 'add', 'toBase',
        ];
        if (in_array($name, $allowedMethods) && method_exists($this->_collection, $name)) {
            $res = $this->_collection->{$name}(...$arguments);
            if ($res instanceof Collection) {
                $this->_collection = $res;
            }
        }
        return $this;
    }

}
