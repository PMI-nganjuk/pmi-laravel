@props([
    'isEmpty' => false,
    'emptyTitle' => 'Tidak Ada Data',
    'emptyMessage' => 'Belum ada data yang tersedia.',
])

<div class="overflow-x-auto border border-surface-border bg-surface-base shadow-sm">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        <thead>
            <tr class="border-b border-surface-border bg-background-base">
                {{ $headers }}
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-muted">
            @if($isEmpty)
                <tr>
                    <td colspan="100%">
                        <x-molecules.empty-state :title="$emptyTitle" :message="$emptyMessage">
                            {{ $emptyAction ?? '' }}
                        </x-molecules.empty-state>
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>