# Revision System Usage Guide

This guide shows you how to access and work with revisions in your Laravel application.

## 1. **Programmatic Access**

### Basic Revision Access
```php
// Get all revisions for any model
$revisions = $model->revisions; // Returns collection
$revisions = $model->revisions()->get(); // Same as above

// Get latest revision
$latest = $model->latestRevision();

// Get latest published revision
$published = $model->latestPublishedRevision();

// Get revision count
$count = $model->revision_count; // Accessor
$count = $model->revisions()->count(); // Query

// Check if model has revisions
if ($model->hasRevisions()) {
    // Do something
}
```

### Specific Model Examples
```php
// For Forms
$form = Form::find(1);
$formRevisions = $form->revisions;
$latestFormRevision = $form->latestRevision();

// For Pages
$page = Page::find(1);
$pageRevisions = $page->revisions;
$latestPageRevision = $page->latestPublishedRevision();

// For Content Blocks
$block = ContentBlock::find(1);
$blockRevisions = $block->revisions;
$blockRevisionCount = $block->revision_count;
```

### Creating Manual Revisions
```php
// Create a revision for a custom action
$model->createManualRevision('custom_action', 'Description of what changed');

// Create with metadata
$model->createManualRevision('publish', 'Published changes', [
    'published_by' => auth()->id(),
    'publish_date' => now()->toISOString()
]);
```

### Reverting to a Previous Revision
```php
$revision = $model->latestRevision();
$success = $model->revertToRevision($revision);

if ($success) {
    // Model has been reverted
    // A new revision is automatically created for the revert action
}
```

## 2. **Web Interface Access**

### Direct URLs
```
/admin/revisions/form/1           // Form ID 1
/admin/revisions/page/5           // Page ID 5  
/admin/revisions/content-block/10 // ContentBlock ID 10
```

### Using Blade Components
```php
// In any admin view
<x-revision-link :model="$form" model-type="form" />
<x-revision-link :model="$page" model-type="page" />
<x-revision-link :model="$block" model-type="content-block" />
```

### Using Livewire Components
```php
// In any Livewire component view
<livewire:admin.revision-history :model="$form" />
<livewire:admin.revision-history :model="$page" />
<livewire:admin.revision-history :model="$block" />
```

## 3. **Revision Data Structure**

### Revision Model Properties
```php
$revision = $model->latestRevision();

$revision->id;                    // Revision ID
$revision->action;                // 'create', 'update', 'delete', 'publish', 'revert'
$revision->action_description;    // Human-readable action description
$revision->version;               // '1.0.0', '1.0.1', etc.
$revision->formatted_version;     // 'v1.0.0', 'v1.0.1', etc.
$revision->data;                  // Full model data snapshot
$revision->changes;               // Only changed fields
$revision->metadata;              // Additional metadata
$revision->description;           // Custom description
$revision->is_published;          // Whether this is a published revision
$revision->user_id;               // User who created the revision
$revision->user;                  // User relationship
$revision->created_at;            // When revision was created
```

### Example: Working with Revision Data
```php
$revision = $form->latestRevision();

// Get the form data as it was at this revision
$formData = $revision->data;

// Get only what changed in this revision
$changes = $revision->changes;

// Check if this was a publish action
if ($revision->action === 'publish') {
    // Handle publish-specific logic
}

// Get user who made the change
$user = $revision->user;
```

## 4. **Comparing Revisions**

### Using the Revision Service
```php
use App\Services\RevisionService;

$revisionService = app(RevisionService::class);

// Compare two revisions
$differences = $revisionService->compareRevisions($revision1, $revision2);

// $differences will contain:
// [
//     'field_name' => [
//         'from' => 'old_value',
//         'to' => 'new_value'
//     ]
// ]
```

### Example: Comparing Form Revisions
```php
$form = Form::find(1);
$revision1 = $form->revisions()->skip(1)->first(); // Second latest
$revision2 = $form->latestRevision(); // Latest

$revisionService = app(RevisionService::class);
$differences = $revisionService->compareRevisions($revision1, $revision2);

foreach ($differences as $field => $diff) {
    echo "Field: {$field}\n";
    echo "Changed from: " . json_encode($diff['from']) . "\n";
    echo "Changed to: " . json_encode($diff['to']) . "\n";
}
```

## 5. **Customizing Revision Tracking**

### Override Revision Data in Models
```php
class CustomModel extends Model
{
    use HasRevisions;

    public function getRevisionData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            // Only track specific fields
        ];
    }

    public function getRevisionExcludedFields(): array
    {
        return [
            'created_at',
            'updated_at',
            'deleted_at',
            'last_draft_at',
            'temporary_field', // Exclude temporary fields
        ];
    }

    public function getRevisionTrackedFields(): ?array
    {
        return [
            'name',
            'status',
            'content',
            // Only track these specific fields
        ];
    }
}
```

### Skip Automatic Revisions
```php
// Temporarily disable automatic revision creation
$model->skipRevision = true;
$model->save(); // No revision will be created
$model->skipRevision = false;
```

## 6. **Integration Examples**

### In Controllers
```php
class FormController extends Controller
{
    public function show(Form $form)
    {
        $revisions = $form->revisions()->paginate(10);
        
        return view('forms.show', compact('form', 'revisions'));
    }

    public function revert(Form $form, Revision $revision)
    {
        $success = $form->revertToRevision($revision);
        
        if ($success) {
            return redirect()->back()->with('success', 'Form reverted successfully');
        }
        
        return redirect()->back()->with('error', 'Failed to revert form');
    }
}
```

### In Livewire Components
```php
class FormEditor extends Component
{
    public Form $form;
    public $selectedRevisionId;

    public function selectRevision($revisionId)
    {
        $this->selectedRevisionId = $revisionId;
        $revision = $this->form->revisions()->find($revisionId);
        
        // Show revision data in UI
        $this->formData = $revision->data;
    }

    public function revertToRevision($revisionId)
    {
        $revision = $this->form->revisions()->find($revisionId);
        $success = $this->form->revertToRevision($revision);
        
        if ($success) {
            $this->dispatch('revision-reverted');
        }
    }

    public function render()
    {
        return view('livewire.form-editor', [
            'revisions' => $this->form->revisions()->paginate(10)
        ]);
    }
}
```

## 7. **Best Practices**

### Performance Considerations
- Use `with('user')` when loading revisions to avoid N+1 queries
- Paginate revision lists for large datasets
- Consider caching revision counts for frequently accessed models

### Security
- Always check permissions before allowing revision access
- Validate revision ownership before allowing reverts
- Log all revision operations for audit trails

### User Experience
- Show revision counts in admin interfaces
- Provide clear revision history navigation
- Use descriptive revision descriptions
- Show differences between revisions when possible 