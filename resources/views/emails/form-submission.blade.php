<!DOCTYPE html>
<html>
<head>
    <title>New Form Submission</title>
</head>
<body>
    <h1>New Submission for {{ $submission->form->name }}</h1>
    <p>{{ __('mail.form_submission.email_body') }}</p>
    <ul>
        @foreach($submission->data as $key => $value)
            <li><strong>{{ Str::title(str_replace('_', ' ', $key)) }}:</strong> {{ is_array($value) ? implode(', ', $value) : $value }}</li>
        @endforeach
    </ul>
</body>
</html> 