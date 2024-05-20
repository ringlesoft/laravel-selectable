<?php

namespace RingleSoft\LaravelSelectable;

use Illuminate\Support\Collection;

class Selectable
{
    private Collection $_collection;
    private string $_value;
    private string $_text;
    private mixed $_selected = null;

    public function __construct(Collection $collection, string|null $text = null, string|null $value = null, mixed $selected = null)
    {
        $this->_collection  = $collection;
        $this->_text = $text ?? 'name';
        $this->_value = $value ?? 'name';
        $this->_selected = $selected ?? null;
    }


    /**
     * Generate select options from a Collection instance
     * @param Collection $collection the collection instance to be used
     * @param string|null $text the field to be used as the main text of the option (default is 'name')
     * @param string|null $value the field to be used as value of the option (default is 'id')
     * @param mixed $selected selected value/values
     * @return string
     */
    public static function collectionToSelectOptions (
        Collection $collection,
        string|null $text = null,
        string|null $value = null,
        mixed $selected = null,
    ): string
    {
        return (new self($collection, $text, $value, $selected))->toSelectOptions();
    }

    /**
     * Generate select options from this instance
     * @return string
     */
    public function toSelectOptions(): string
    {

        $html = "";
        foreach ($this->_collection as $index => $item) {
            $lineText  = $item->{$this->_text} ?? "N/A";
            $lineValue = $item->{$this->_value} ?? "";
            $html .= "<option value=\"{$lineValue}\"";
            if(is_array($this->_selected)) {
                foreach ($this->_selected as $selectedItem) {
                    if(is_object($selectedItem)) {
                        if((string) $selectedItem->{$this->_value} === (string) $lineValue) {
                            $html .= " selected";
                        }
                    } else if(is_array($selectedItem)) {
                        if(array_key_exists($this->_value, $selectedItem) && (string)$selectedItem[$this->_value] === (string) $lineValue) {
                            $html .= " selected";
                        }
                    } else if((string) $selectedItem === (string) $lineValue) {
                        $html .= " selected";
                    }
                }
            } else if ((string) $this->_selected === (string) $lineValue) {
                $html .= " selected";
            }
            $html .= ">{$lineText}</option>";
        }
        return $html;
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
