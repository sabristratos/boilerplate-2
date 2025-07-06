<div>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl">
                        {{ __('revisions.title') }} - {{ class_basename($model) }}
                    </flux:heading>
                    <flux:text class="text-gray-600 mt-1">
                        {{ __('revisions.subtitle', ['model' => class_basename($model), 'count' => $model->revision_count]) }}
                    </flux:text>
                </div>
                
                <div class="flex items-center space-x-3">
                    <flux:button 
                        type="button"
                        variant="ghost" 
                        onclick="history.back()">
                        {{ __('buttons.back') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <livewire:admin.revision-history :model="$model" />
    </div>
</div> 