@props(['model', 'modelType'])

@if($model->hasRevisions())
    <flux:button 
        variant="ghost" 
        size="sm"
        href="{{ route('admin.revisions.show', ['modelType' => $modelType, 'modelId' => $model->id]) }}"
        title="{{ __('revisions.view_history') }}"
    >
        <flux:icon name="clock" class="w-4 h-4 mr-2" />
        {{ __('revisions.history') }}
        @if($model->revision_count > 0)
            <flux:badge size="sm" class="ml-2">{{ $model->revision_count }}</flux:badge>
        @endif
    </flux:button>
@endif 