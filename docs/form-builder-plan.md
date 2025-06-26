Here is the complete, phased plan to build your dedicated form builder, with placeholders for you to track your progress.

Phase 1: Backend Foundation
This phase establishes the core database structure and models.

Step 1: Create Database Migrations [ ]

Create a migration for the forms table with columns for name (string), title (JSON), description (JSON, nullable), recipient_email (string, nullable), success_message (JSON), and send_notification (boolean).

Create a migration for the form_fields table with columns for form_id (foreign key), type (string), name (string), label (JSON), placeholder (JSON, nullable), options (JSON, nullable), validation_rules (JSON, nullable), and order (integer).

Create a migration for the form_submissions table with columns for form_id (foreign key), data (JSON), and submitted_at (timestamp).

Step 2: Create Eloquent Models [ ]

Create the app/Models/Form.php model.

Create the app/Models/FormField.php model.

Create the app/Models/FormSubmission.php model.

Step 3: Configure Model Relationships & Traits [ ]

In Form.php, establish the hasMany relationships to FormField and FormSubmission.

In FormField.php and FormSubmission.php, establish the inverse belongsTo relationship to Form.

In Form.php, add the Spatie\Translatable\HasTranslations trait and define $translatable = ['title', 'description', 'success_message'];.

In FormField.php, add the HasTranslations trait with $translatable = ['label', 'placeholder', 'options']; and the Spatie\EloquentSortable\SortableTrait.

In FormSubmission.php, cast the data attribute to an array.

Phase 2: Admin UI - The Form Builder
This phase focuses on building the dedicated interface for creating and managing forms.

Step 1: Establish Routes and Navigation [ ]

In routes/web.php, within the auth middleware group, define the routes for the form builder UI: /admin/forms, /admin/forms/{form}/builder, and /admin/forms/{form}/submissions.

In the admin sidebar view (resources/views/components/layouts/app/sidebar.blade.php), add a new flux:navlist.item linking to the admin.forms.index route, using an appropriate icon (e.g., "rectangle-group").

Step 2: Build the Form Index Page [ ]

Create the app/Livewire/Forms/FormIndex.php Livewire component.

Implement the render method to fetch and display all created forms.

Add a public method to create a new Form and redirect to the builder route.

Create the resources/views/livewire/forms/form-index.blade.php view, displaying the forms in a table with "Edit" and "Submissions" links for each.

Step 3: Build the Core FormBuilder Component [ ]

Create the app/Livewire/Forms/FormBuilder.php Livewire component.

Define public properties: Form $form to hold the current form, and $activeLocale to manage translations.

In the mount(Form $form) method, load the form model and initialize the $activeLocale from a locale query parameter, defaulting to your app's main locale.

Create the resources/views/livewire/forms/form-builder.blade.php view with a two-panel layout.

Step 4: Implement the Builder UI & Logic [ ]

Locale Switcher: In the builder view, add a tabbed interface or dropdown to switch the $activeLocale between the locales defined in your settings.

Form Settings Panel: Create inputs for form properties (title, description, etc.) and use wire:model="form.title.{{ $activeLocale }}" to bind them to the translatable data, mimicking the PageManager.

Field Canvas: In the main panel, loop through $form->formFields and render a representation of each field. For now, a simple list item showing the field's label and type is sufficient.

Field Management:

Add an "Add Field" button that calls a component method addField(string $type).

Each field on the canvas should have "Edit" and "Remove" buttons. The "Edit" button should call a selectField(int $fieldId) method to set the currently active field for editing.

Field Settings Panel: When a field is selected via selectField, display a new set of inputs for its properties (label, placeholder, validation_rules, etc.), also bound to the active locale: wire:model="selectedField.label.{{ $activeLocale }}".

Save Action: Create a primary save() method that persists all changes to the $form model and its related formFields, using setTranslation() for localized attributes.

Step 5: Build the Submissions Viewer [ ]

Create the app/Livewire/Forms/SubmissionIndex.php component and its corresponding view.

In its mount(Form $form) method, load the form and its submissions.

Display the submissions in a table. Include a "View" button for each that opens a modal (flux:modal) to show the full, formatted submission data.

Phase 3: Frontend Integration
This phase connects the forms you build to the public-facing website via your Blocks system.

Step 1: Integrate Forms into Blocks [ ]

Identify a block, such as app/Blocks/ContactSectionBlock (hypothetical, based on your structure), that should contain a form.

Modify its getAdminView() method to include a Select field populated with all available forms (Form::all()->pluck('name', 'id')). This will save a form_id to the block's data.

Step 2: Create the Frontend FormDisplay Component [ ]

Create a new app/Livewire/Frontend/FormDisplay.php component that accepts a public property $formId.

In its mount() method, use the $formId to load the Form and its FormFields.

Create a public array property $formData to bind to the form inputs.

Create the resources/views/livewire/frontend/form-display.blade.php view.

In the view, loop through the FormFields and dynamically render the correct flux:input, flux:textarea, etc., based on each field's type. Bind each to the state with wire:model="formData.{{ $field->name }}".

Step 3: Implement Frontend Submission Logic [ ]

In the FormDisplay component, create a submit() method.

Inside submit(), dynamically build a validation rules array from the validation_rules of your FormFields and validate $this->formData.

On success, create a new FormSubmission record with the $this->formData.

Step 4: Implement Email Notifications [ ]

Create a new Mailable, app/Mail/FormSubmissionNotification.php.

This Mailable should accept the FormSubmission model.

Design a Blade template for the email that neatly presents the submitted data.

In the FormDisplay component's submit() method, after saving the submission, dispatch the Mailable to the recipient_email specified on the Form model if send_notification is true.

Phase 4: Permissions and Final Touches
This final phase secures the new section and ensures it's ready for use.

Step 1: Secure the Form Builder with Permissions [ ]

In database/seeders/RolesAndPermissionsSeeder.php, define new permissions: view forms, create forms, edit forms, delete forms, and view form submissions.

Assign these new permissions to the appropriate roles (e.g., admin).

In the new Form Builder Livewire components, use ->authorize() checks in methods like mount, save, and delete to protect them.

On the routes in web.php, you can add middleware like ->middleware('can:view forms').

Step 2: Write Internal Documentation [ ]

Since this is a system for you and your team, create a README.md or internal wiki page explaining how to use the new Form Builder, how to add a form to a Block, and the purpose of each component. 