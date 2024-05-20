<?php

namespace RingleSoft\LaravelSelectable;

use Illuminate\Support\Collection;

class Selectable
{
    private Collection $_collection;
    public function __construct(Collection $collection)
    {
        $this->_collection  = $collection;
    }


    /**
     * @param Collection $collection
     * @param string|null $text
     * @param string|null $value
     * @param null $selected
     * @return string
     */
    public static function collectionToSelectOptions (
        Collection $collection,
        string|null $text = null,
        string|null $value = null,
        $selected = null,
    ): string
    {
        $html = "";
        $collection->each(static function ($item) use (&$html, $value, $selected, $text) {
            $html .= '<option value="'. (($item->{$value ?? 'id'}) ?? '') .'"';
            if(is_array($selected)) {
                foreach ($selected as $selectedItem) {
                    if(is_object($selectedItem)) {
                        if((string) $selectedItem->{$value ?? 'id'} === (string) $item->{$value ?? 'id'}) {
                            $html .= " selected";
                        }
                    } else if(is_array($selectedItem)) {
                        if(array_key_exists(($value ?? 'id'), $selectedItem) && (string)$selectedItem[$value ?? 'id'] === (string)$item->{$value ?? 'id'}) {
                            $html .= " selected";
                        }
                    } else if((string) $selectedItem === (string) $item->{$value ?? 'id'}) {
                        $html .= " selected";
                    }
                }
            }  else if ((string) $selected === (string) $value) {
                $html .= " selected";
            }
            $html .= ">". (($item->{$text ?? 'name'}) ?? 'N/A')."</option>";
        });
        return $html;
    }

    // toSelectable
    // toCollection
    public static function fromCollection(Collection $collection): self
    {
        return new self($collection);
    }
}
