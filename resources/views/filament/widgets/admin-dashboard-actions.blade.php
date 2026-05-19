<x-filament-widgets::widget>
    <x-filament::section heading="Admin tools"  collapsible>
        <div class="flex flex-wrap gap-3 w-full">
            @foreach ($this->actions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
