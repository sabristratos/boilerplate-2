@props(['model', 'modelType'])

@if($model->hasRevisions())
    <flux:button 
        variant="ghost" 
        size="xs"
        href="{{ route('admin.revisions.show', ['modelType' => $modelType, 'modelId' => $model->id]) }}"
        tooltip="{{ __('revisions.view_history') }}"
        icon="clock"
    >
    </flux:button>
@endif 