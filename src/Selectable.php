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
    private string|Closure|null $_id = null;

    public function __construct(
        Collection $collection,
        string|Closure|null $label = null,
        string|Closure|null $value = null,
        mixed $selected = null,
        mixed $disabled = null,
        string|Closure|null $id = null,
    ) {
        $this->_collection = $collection;
        $this->_label = $label ?? 'name';
        $this->_value = $value ?? 'id';
        $this->_selected = $selected;
        $this->_disabled = $disabled;
        $this->_id = $id;
    }

    /**
     * Resolve a configured accessor against objects, arrays, and scalar collections.
     */
    private function resolveAccessor(string|Closure $accessor, mixed $item, int|string|null $index, mixed $default = null): mixed
    {
        if ($accessor instanceof Closure) {
            return $accessor($item, $index);
        }

        if (is_array($item) || is_object($item)) {
            return data_get($item, $accessor, $default);
        }

        return $default;
    }

    private function optionValue(mixed $item, int|string|null $index): mixed
    {
        if ($this->_value instanceof Closure) {
            return $this->resolveAccessor($this->_value, $item, $index);
        }

        if (is_array($item)) {
            return $this->resolveAccessor($this->_value, $item, $index, reset($item));
        }

        if (is_object($item)) {
            return $this->resolveAccessor($this->_value, $item, $index, '');
        }

        return is_string($index) && is_string($item) ? $index : $item;
    }

    private function optionLabel(mixed $item, int|string|null $index): mixed
    {
        if ($this->_label instanceof Closure) {
            return $this->resolveAccessor($this->_label, $item, $index);
        }

        if (is_array($item)) {
            return $this->resolveAccessor($this->_label, $item, $index, reset($item));
        }

        if (is_object($item)) {
            return $this->resolveAccessor($this->_label, $item, $index, 'N/A');
        }

        return $item;
    }

    private function matches(mixed $configured, mixed $item, int|string|null $index): bool
    {
        if ($configured === null) {
            return false;
        }

        if ($configured instanceof Closure) {
            return (bool) $configured($item, $index);
        }

        $optionValue = $this->optionValue($item, $index);
        $candidates = $configured instanceof Collection ? $configured->all() : (is_array($configured) ? $configured : [$configured]);

        foreach ($candidates as $candidate) {
            $candidateValue = (is_array($candidate) || is_object($candidate))
                ? $this->optionValue($candidate, null)
                : $candidate;

            if ((string) $candidateValue === (string) $optionValue) {
                return true;
            }
        }

        return false;
    }

    private function _shouldSelect(mixed $item, int|string|null $index = null): bool
    {
        return $this->matches($this->_selected, $item, $index);
    }

    private function _shouldDisable(mixed $item, int|string|null $index = null): bool
    {
        return $this->matches($this->_disabled, $item, $index);
    }

    private function _getDataAttributes(mixed $item, int|string|null $index = null): array
    {
        $dataAttributes = [];

        foreach ($this->_dataAttributes as $attributeItem) {
            $attribute = $attributeItem['attribute'] instanceof Closure
                ? $this->resolveAccessor($attributeItem['attribute'], $item, $index)
                : $attributeItem['attribute'];
            $value = $attributeItem['value'] instanceof Closure
                ? $this->resolveAccessor($attributeItem['value'], $item, $index)
                : (is_string($attributeItem['value'])
                    ? $this->resolveAccessor($attributeItem['value'], $item, $index, '')
                    : $attributeItem['value']);

            $dataAttributes[$this->dataAttributeName($attribute)] = $value;
        }

        return $dataAttributes;
    }

    private function dataAttributeName(mixed $attribute): string
    {
        $attribute = preg_replace('/[^A-Za-z0-9_.:-]+/', '-', (string) $attribute) ?? '';
        $attribute = trim($attribute, '-');

        return $attribute === '' ? 'value' : $attribute;
    }

    private function optionClasses(mixed $item, int|string|null $index): array
    {
        $classes = [];

        foreach ($this->_classes as $class) {
            $resolved = $class instanceof Closure ? $class($item, $index) : $class;
            $classes = [...$classes, ...preg_split('/\s+/', trim((string) $resolved), -1, PREG_SPLIT_NO_EMPTY)];
        }

        return $classes;
    }

    /**
     * This representation is shared by HTML and API-oriented consumers.
     */
    private function optionFor(mixed $item, int|string|null $index): array
    {
        return [
            'value' => $this->optionValue($item, $index),
            'label' => $this->optionLabel($item, $index),
            'isSelected' => $this->_shouldSelect($item, $index),
            'isDisabled' => $this->_shouldDisable($item, $index),
            'data' => $this->_getDataAttributes($item, $index),
            'classes' => $this->optionClasses($item, $index),
            'id' => $this->_id instanceof Closure ? $this->_id($item, $index) : $this->_id,
        ];
    }

    private function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function _generateOptions(Collection $collection): string
    {
        $html = '';

        foreach ($collection as $index => $item) {
            if ($item instanceof Collection) {
                $html .= '<optgroup label="' . $this->escape($index) . '">';
                $html .= $this->_generateOptions($item);
                $html .= '</optgroup>';

                continue;
            }

            $option = $this->optionFor($item, $index);
            $attributes = ['value="' . $this->escape($option['value']) . '"'];

            if ($option['id'] !== null) {
                $attributes[] = 'id="' . $this->escape($option['id']) . '"';
            }
            if ($option['isSelected']) {
                $attributes[] = 'selected';
            }
            if ($option['isDisabled']) {
                $attributes[] = 'disabled';
            }
            foreach ($option['data'] as $key => $value) {
                $attributes[] = 'data-' . $this->escape($key) . '="' . $this->escape($value) . '"';
            }
            if ($option['classes'] !== []) {
                $attributes[] = 'class="' . $this->escape(implode(' ', $option['classes'])) . '"';
            }

            $html .= '<option ' . implode(' ', $attributes) . '>' . $this->escape($option['label']) . '</option>';
        }

        return $html;
    }

    public static function collectionToSelectOptions(
        Collection $collection,
        string|Closure|null $label = null,
        string|Closure|null $value = null,
        mixed $selected = null,
        mixed $disabled = null,
    ): string {
        return (new self($collection, $label, $value, $selected, $disabled))->toSelectOptions();
    }

    public static function fromCollection(Collection $collection): self
    {
        return new self($collection);
    }

    public function toSelectOptions(): string
    {
        return $this->_generateOptions($this->_collection);
    }

    public function toSelectItems(): Collection
    {
        return $this->_collection->map(function (mixed $item, int|string $index): mixed {
            if ($item instanceof Collection) {
                return $this->itemsFor($item);
            }

            return $this->optionFor($item, $index);
        });
    }

    private function itemsFor(Collection $collection): Collection
    {
        return $collection->map(function (mixed $item, int|string $index): mixed {
            return $item instanceof Collection ? $this->itemsFor($item) : $this->optionFor($item, $index);
        });
    }

    public function withLabel(string|Closure $label): self
    {
        $this->_label = $label;

        return $this;
    }

    public function withValue(string|Closure $value): self
    {
        $this->_value = $value;

        return $this;
    }

    public function withSelected(mixed $selected): self
    {
        $this->_selected = $selected;

        return $this;
    }

    public function withDisabled(mixed $disabled): self
    {
        $this->_disabled = $disabled;

        return $this;
    }

    public function withDataAttribute(string|Closure $attribute, mixed $value): self
    {
        $this->_dataAttributes[] = ['attribute' => $attribute, 'value' => $value];

        return $this;
    }

    public function withClass(string|array|Closure $class): self
    {
        $classes = $class instanceof Closure ? [$class] : (is_array($class) ? $class : explode(' ', $class));
        $this->_classes = [...$this->_classes, ...$classes];

        return $this;
    }

    public function withId(string|Closure $id): self
    {
        $this->_id = $id;

        return $this;
    }

    public function toCollection(): Collection
    {
        return $this->_collection;
    }

    public function __call(string $name, array $arguments): self
    {
        $allowedMethods = [
            'groupBy', 'add', 'zip', 'unique', 'range', 'merge', 'diff', 'diffUsing', 'diffAssoc',
            'diffAssocUsing', 'diffKeys', 'diffKeysUsing', 'forget', 'mergeRecursive', 'combine', 'union',
            'nth', 'only', 'select', 'prepend', 'push', 'concat', 'put', 'random', 'replace',
            'replaceRecursive', 'reverse', 'shuffle', 'sliding', 'skip', 'skipUntil', 'skipWhile', 'slice',
            'split', 'splitIn', 'chunk', 'chunkWhile', 'sort', 'sortDesc', 'sortBy', 'sortByMany',
            'sortByDesc', 'sortKeys', 'sortKeysDesc', 'sortKeysUsing', 'splice', 'take', 'takeUntil',
            'takeWhile', 'transform', 'dot', 'undot', 'values', 'pad', 'getIterator', 'countBy', 'toBase',
        ];

        if (in_array($name, $allowedMethods, true) && method_exists($this->_collection, $name)) {
            $result = $this->_collection->{$name}(...$arguments);
            if ($result instanceof Collection) {
                $this->_collection = $result;
            }
        }

        return $this;
    }
}
