<x-filament-widgets::widget>
    <x-filament::section heading="Admin tools" collapsible>
        <div class="flex flex-wrap gap-3 w-full">
            {{ $this->clearCacheAction }}
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>