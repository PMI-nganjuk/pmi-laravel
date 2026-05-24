@props([
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteConfirmText' => null,
    'editLabel' => 'Edit',
    'deleteLabel' => 'Hapus',
])

<x-atoms.table-cell class="text-right">
    <div class="inline-flex items-center justify-end gap-2">
        @if($editUrl)
            <x-atoms.button as="a" :href="$editUrl" variant="secondary" size="sm" :aria-label="'Edit: ' . $editLabel">
                {{ $editLabel }}
            </x-atoms.button>
        @endif

        @if($deleteUrl)
            <form action="{{ $deleteUrl }}" method="POST" data-confirm-submit="{{ $deleteConfirmText }}">
                @csrf
                @method('DELETE')
                <x-atoms.button type="submit" variant="danger" size="sm" :aria-label="'Delete: ' . $deleteLabel">
                    {{ $deleteLabel }}
                </x-atoms.button>
            </form>
        @endif
    </div>
</x-atoms.table-cell>
