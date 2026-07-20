<?php

namespace RingleSoft\LaravelSelectable\Tests;

use Illuminate\Support\Collection;
use RingleSoft\LaravelSelectable\Selectable;

class SelectableTest extends TestCase
{
    public function test_collection_macros_render_default_options(): void
    {
        $options = collect([
            (object) ['id' => 1, 'name' => 'Ada'],
            (object) ['id' => 2, 'name' => 'Grace'],
        ])->toSelectOptions();

        $this->assertSame('<option value="1">Ada</option><option value="2">Grace</option>', $options);
    }

    public function test_html_output_escapes_every_dynamic_value(): void
    {
        $options = Selectable::fromCollection(collect([
            ['id' => '" onmouseover="alert(1)', 'name' => '<img src=x onerror=alert(2)>', 'state' => 'x" y'],
        ]))
            ->withDataAttribute('state', 'state')
            ->withClass(fn (): string => 'choice" unsafe')
            ->withId('option" id')
            ->toSelectOptions();

        $this->assertSame(
            '<option value="&quot; onmouseover=&quot;alert(1)" id="option&quot; id" data-state="x&quot; y" class="choice&quot; unsafe">&lt;img src=x onerror=alert(2)&gt;</option>',
            $options,
        );
    }

    public function test_selected_and_disabled_object_lists_check_every_candidate(): void
    {
        $items = collect([
            (object) ['id' => 1, 'name' => 'One'],
            (object) ['id' => 2, 'name' => 'Two'],
        ]);

        $options = Selectable::fromCollection($items)
            ->withSelected([(object) ['id' => 999], (object) ['id' => 2]])
            ->withDisabled([(object) ['id' => 999], (object) ['id' => 1]])
            ->toSelectOptions();

        $this->assertSame('<option value="1" disabled>One</option><option value="2" selected>Two</option>', $options);
    }

    public function test_select_items_uses_the_same_normalized_data_for_arrays(): void
    {
        $items = Selectable::fromCollection(collect([
            ['id' => 7, 'name' => 'Seven', 'state' => 'active'],
        ]))
            ->withDataAttribute('state', 'state')
            ->withClass(fn (array $item): string => 'state-' . $item['state'])
            ->withSelected([7])
            ->toSelectItems();

        $this->assertSame([
            [
                'value' => 7,
                'label' => 'Seven',
                'isSelected' => true,
                'isDisabled' => false,
                'data' => ['state' => 'active'],
                'classes' => ['state-active'],
                'id' => null,
            ],
        ], $items->all());
    }

    public function test_grouped_collections_render_optgroups_and_preserve_item_metadata(): void
    {
        $selectable = Selectable::fromCollection(new Collection([
            'Active' => collect([(object) ['id' => 1, 'name' => 'Ada']]),
        ]));

        $this->assertSame('<optgroup label="Active"><option value="1">Ada</option></optgroup>', $selectable->toSelectOptions());
        $this->assertSame('Ada', $selectable->toSelectItems()->get('Active')->first()['label']);
    }
}
