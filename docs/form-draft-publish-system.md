# Form Draft/Publish System

This document explains how the form draft/publish system works in the application.

## Overview

The form builder implements a clear draft/publish workflow that allows users to:
- Work on form changes without affecting the published version
- Save work-in-progress as drafts
- Publish changes when ready
- Discard draft changes and revert to the published version
- Track all changes through a revision system

## Core Components

### 1. Form Status Enum (`FormStatus`)

The form can be in one of three states:

- **DRAFT**: Form is in development, not accessible to users
- **PUBLISHED**: Form is live and accessible to users for submissions
- **ARCHIVED**: Form is no longer accepting submissions

### 2. Revision System (`HasRevisions` trait)

Every form change is tracked through revisions:
- **Published revisions**: Represent the live version of the form
- **Unpublished revisions**: Represent draft work
- Each revision contains the complete form state (elements, settings, name)

### 3. Actions

Three main actions handle the workflow:

#### `SaveDraftFormAction`
- Saves current form state as an unpublished revision
- Updates the form data in the database
- Creates a revision with `is_published = false`
- Used for work-in-progress saves

#### `PublishFormAction`
- Publishes the current form state
- Updates form status to `PUBLISHED`
- Creates a published revision with `is_published = true`
- Makes the form accessible to users

#### `DiscardFormDraftAction`
- Reverts to the latest published revision
- Discards all unpublished changes
- Creates a revision to track the discard action
- Used to abandon draft work

## Workflow

### 1. Creating a New Form
```
Form Created → Status: DRAFT → Initial published revision created
```

### 2. Working on Draft Changes
```
User makes changes → Save Draft → Unpublished revision created
User continues editing → Save Draft → New unpublished revision
```

### 3. Publishing Changes
```
User clicks Publish → Form status: PUBLISHED → Published revision created
Form becomes live and accessible to users
```

### 4. Making More Changes
```
User makes changes → Save Draft → New unpublished revision
Published version remains unchanged
```

### 5. Discarding Draft Changes
```
User clicks Discard → Revert to latest published revision
All draft work is lost
```

## UI Components

### Form Builder Header

The header shows:
- **Form name and ID**
- **Status badge**: Shows current form status (Draft/Published/Archived)
- **Draft changes badge**: Shows when there are unpublished changes
- **Action buttons**:
  - **Save Draft**: Saves current state as unpublished revision
  - **Publish**: Publishes current state (only shown when can publish)
  - **Discard**: Reverts to published version (only shown when has draft changes)

### Button States

- **Save Draft**: Always enabled when there are changes
- **Publish**: Only enabled when form has elements and is not already published
- **Discard**: Only enabled when there are draft changes

## Technical Implementation

### FormBuilder Livewire Component

Key methods:
- `saveDraft()`: Saves current state as draft
- `publish()`: Publishes current state
- `discardDraft()`: Reverts to published version
- `hasDraftChanges()`: Checks if there are unpublished revisions
- `canPublish()`: Checks if form can be published
- `canDiscardDraft()`: Checks if draft can be discarded

### Form Model

Key methods:
- `hasDraftChanges()`: Returns true if latest revision is unpublished
- `isPublished()`: Returns true if status is PUBLISHED
- `isDraft()`: Returns true if status is DRAFT
- `latestRevision()`: Gets the most recent revision
- `latestPublishedRevision()`: Gets the most recent published revision

### Database Structure

- **forms table**: Contains current form data and status
- **revisions table**: Contains all form versions with metadata
- **revisionable_type**: 'App\Models\Form'
- **revisionable_id**: Form ID
- **is_published**: Boolean indicating if revision is published

## Best Practices

### For Users
1. **Save frequently**: Use "Save Draft" to preserve work
2. **Test before publishing**: Use preview mode to test forms
3. **Review changes**: Check revision history before publishing
4. **Use discard carefully**: Discarding permanently removes draft work

### For Developers
1. **Always validate**: Validate form data before saving/publishing
2. **Handle errors gracefully**: Show clear error messages
3. **Log actions**: Log all draft/publish actions for audit trail
4. **Test edge cases**: Test scenarios like discarding with no published version

## Error Handling

The system handles various error scenarios:
- **Validation errors**: Form data is validated before saving
- **Database errors**: Graceful error handling with user feedback
- **Revision conflicts**: Proper handling of concurrent edits
- **Missing published version**: Creates initial published revision if needed

## Security Considerations

- **User permissions**: Only form owners can edit/publish
- **Data integrity**: Revisions provide audit trail
- **Validation**: All form data is validated before saving
- **Access control**: Published forms are accessible, drafts are not

## Future Enhancements

Potential improvements:
- **Auto-save**: Automatic draft saving
- **Collaboration**: Multiple users editing same form
- **Approval workflow**: Require approval before publishing
- **Scheduled publishing**: Publish forms at specific times
- **Version comparison**: Visual diff between versions 