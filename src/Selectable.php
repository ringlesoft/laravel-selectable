<?php

namespace RingleSoft\LaravelSelectable;

use Illuminate\Support\Collection;

class Selectable
{
    private Collection $_collection;
    private string $_value;
    private string $_label;
    private mixed $_selected = null;
    private mixed $_disabled = null;

    public function __construct(Collection $collection, string|null $label = null, string|null $value = null, mixed $selected = null, mixed $disabled = null)
    {
        $this->_collection = $collection;
        $this->_label = $label ?? 'name';
        $this->_value = $value ?? 'id';
        $this->_selected = $selected ?? null;
        $this->_disabled = $disabled ?? null;
    }


    /**
     * Generate select options from a Collection instance
     * @param Collection $collection the collection instance to be used
     * @param string|null $label the field to be used as the main text of the option (default is 'name')
     * @param string|null $value the field to be used as value of the option (default is 'id')
     * @param mixed $selected selected value/values
     * @param mixed|null $disabled
     * @return string
     */
    public static function collectionToSelectOptions(
        Collection  $collection,
        string|null $label = null,
        string|null $value = null,
        mixed       $selected = null,
        mixed       $disabled = null,
    ): string
    {
        return (new self($collection, $label, $value, $selected, $disabled))->toSelectOptions();
    }


    /**
     * Generate select options from this instance
     * @return string
     */
    public function toSelectOptions(): string
    {


        $html = "";
        foreach ($this->_collection as $index => $item) {
            $lineLabel = $item->{$this->_label} ?? "N/A";
            $lineValue = $item->{$this->_value} ?? "";
            $html .= "<option value=\"{$lineValue}\"";
            if($this->_shouldSelect($item)){
                $html .= " selected";
            }
            if($this->_shouldDisable($item)){
                $html .= " disabled";
            }
            $html .= " >{$lineLabel}</option>";
        }
        return $html;
    }

    /**
     * Check if the item should be selected
     * @param object $item
     * @return bool
     */
    private function _shouldSelect(object $item): bool
    {
        $lineValue = $item->{$this->_value} ?? "";
        if (is_callable($this->_selected)) {
            if (call_user_func($this->_selected, $item) === true) {
                return true;
            }
        } else if(is_object($this->_selected)){
            if((string)$this->_selected->{$this->_value} === (string)$lineValue){
                return true;
            }
        } else if (is_array($this->_selected)) {
            foreach ($this->_selected as $selectedItem) {
                if (is_object($selectedItem)) {
                    if ((string)$selectedItem->{$this->_value} === (string)$lineValue) {
                        return true;
                    }
                } else if (is_array($selectedItem)) {
                    if (array_key_exists($this->_value, $selectedItem) && (string)$selectedItem[$this->_value] === (string)$lineValue) {
                        return true;
                    }
                } else if ((string)$selectedItem === (string)$lineValue) {
                    return true;
                }
            }
        } else if ((string)$this->_selected === (string)$lineValue) {
            return true;
        }
        return false;
    }

    /**
     * Check if the item should be selected
     * @param object $item
     * @return bool
     */
    private function _shouldDisable(object $item): bool
    {
        $lineValue = $item->{$this->_value} ?? "";
        if (is_callable($this->_disabled)) {
            if (call_user_func($this->_disabled, $item) === true) {
                return true;
            }
        } else if(is_object($this->_disabled)){
            if((string)$this->_disabled->{$this->_value} === (string)$lineValue){
                return true;
            }
        } else if (is_array($this->_disabled)) {
            foreach ($this->_disabled as $disabledItem) {
                if (is_object($disabledItem)) {
                    if ((string)$disabledItem->{$this->_value} === (string)$lineValue) {
                        return true;
                    }
                } else if (is_array($disabledItem)) {
                    if (array_key_exists($this->_value, $disabledItem) && (string)$disabledItem[$this->_value] === (string)$lineValue) {
                        return true;
                    }
                } else if ((string)$disabledItem === (string)$lineValue) {
                    return true;
                }
            }
        } else if ((string) $this->_disabled === (string) $lineValue) {
            return true;
        }
        return false;
    }

    /**
     * Return a collection of selectable items (for use in spa components)
     * @return Collection
     */
    public function toSelectItems(): Collection
    {
        return $this->_collection->map(function ($item) {
            return [
                'value' => $item->{$this->_value} ?? "",
                'label' => $item->{$this->_label},
                'isSelected' => $this->_shouldSelect($item),
                'isDisabled' => $this->_shouldDisable($item),
            ];
        });
    }

    //// Builders

    /**
     * @param string $label name of the field to be used as label
     * @return $this
     */
    public function withLabel(string $label): self
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * @param string $value name of the field to be used as value
     * @return $this
     */
    public function withValue(string $value): self
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * @param mixed $selected
     * @return $this
     */
    public function withSelected(mixed $selected): self
    {
        $this->_selected = $selected;
        return $this;
    }

    public function withDisabled(mixed $disabled): self
    {
        $this->_disabled = null;
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
     * Create Selectable instance from a collection instance
     * @param Collection $collection
     * @return self
     */
    public static function fromCollection(Collection $collection): self
    {
        return new self($collection);
    }

}
