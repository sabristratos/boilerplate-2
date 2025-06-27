<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contactForm = Form::create([
            'name' => 'Contact Us',
            'slug' => 'contact-us',
            'title' => ['en' => 'Contact Us', 'fr' => 'Nous Contacter'],
            'description' => ['en' => 'Please fill out the form below to get in touch.', 'fr' => 'Veuillez remplir le formulaire ci-dessous pour nous contacter.'],
            'recipient_email' => 'admin@example.com',
            'success_message' => ['en' => 'Thank you for your message!', 'fr' => 'Merci pour votre message!'],
            'send_notification' => true,
            'has_captcha' => true,
            'submit_button_options' => ['label' => ['en' => 'Send Message', 'fr' => 'Envoyer le message']],
        ]);

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'text',
            'name' => 'name',
            'label' => ['en' => 'Full Name', 'fr' => 'Nom et prénom'],
            'placeholder' => ['en' => 'Enter your full name', 'fr' => 'Entrez votre nom complet'],
            'validation_rules' => 'required|string|max:255',
            'sort_order' => 1,
        ]);

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email Address', 'fr' => 'Adresse e-mail'],
            'placeholder' => ['en' => 'Enter your email', 'fr' => 'Entrez votre email'],
            'validation_rules' => 'required|email',
            'sort_order' => 2,
        ]);

        FormField::create([
            'form_id' => $contactForm->id,
            'type' => 'textarea',
            'name' => 'message',
            'label' => ['en' => 'Message', 'fr' => 'Message'],
            'placeholder' => ['en' => 'Your message here...', 'fr' => 'Votre message ici...'],
            'validation_rules' => 'required|string|min:10',
            'sort_order' => 3,
        ]);

        $feedbackForm = Form::create([
            'name' => 'Website Feedback',
            'slug' => 'website-feedback',
            'title' => ['en' => 'Website Feedback', 'fr' => 'Commentaires sur le site'],
            'description' => ['en' => 'Let us know what you think about our website.', 'fr' => 'Dites-nous ce que vous pensez de notre site.'],
            'recipient_email' => 'feedback@example.com',
            'success_message' => ['en' => 'Thanks for your feedback!', 'fr' => 'Merci pour vos commentaires!'],
        ]);

        FormField::create([
            'form_id' => $feedbackForm->id,
            'type' => 'select',
            'name' => 'rating',
            'label' => ['en' => 'How would you rate your experience?', 'fr' => 'Comment évalueriez-vous votre expérience?'],
            'options' => [
                ['value' => '5', 'label' => ['en' => 'Excellent', 'fr' => 'Excellent']],
                ['value' => '4', 'label' => ['en' => 'Good', 'fr' => 'Bien']],
                ['value' => '3', 'label' => ['en' => 'Average', 'fr' => 'Moyen']],
                ['value' => '2', 'label' => ['en' => 'Poor', 'fr' => 'Pauvre']],
                ['value' => '1', 'label' => ['en' => 'Terrible', 'fr' => 'Terrible']],
            ],
            'validation_rules' => 'required',
            'sort_order' => 1,
        ]);

        FormField::create([
            'form_id' => $feedbackForm->id,
            'type' => 'textarea',
            'name' => 'comments',
            'label' => ['en' => 'Additional Comments', 'fr' => 'Commentaires supplémentaires'],
            'validation_rules' => 'nullable|string',
            'sort_order' => 2,
        ]);

        $jobApplicationForm = Form::create([
            'name' => 'Job Application',
            'slug' => 'job-application',
            'title' => ['en' => 'Job Application', 'fr' => 'Candidature d\'emploi'],
            'description' => ['en' => 'Apply for a position at our company.', 'fr' => 'Postulez pour un poste dans notre entreprise.'],
            'recipient_email' => 'hr@example.com',
            'success_message' => ['en' => 'Your application has been submitted successfully!', 'fr' => 'Votre candidature a été soumise avec succès!'],
            'send_notification' => true,
            'has_captcha' => true,
            'submit_button_options' => ['label' => ['en' => 'Submit Application', 'fr' => 'Soumettre la candidature']],
        ]);

        FormField::create([
            'form_id' => $jobApplicationForm->id,
            'type' => 'text',
            'name' => 'full_name',
            'label' => ['en' => 'Full Name', 'fr' => 'Nom complet'],
            'validation_rules' => 'required|string|max:255',
            'sort_order' => 1,
        ]);

        FormField::create([
            'form_id' => $jobApplicationForm->id,
            'type' => 'email',
            'name' => 'email',
            'label' => ['en' => 'Email', 'fr' => 'Email'],
            'validation_rules' => 'required|email',
            'sort_order' => 2,
        ]);

        FormField::create([
            'form_id' => $jobApplicationForm->id,
            'type' => 'text',
            'name' => 'phone',
            'label' => ['en' => 'Phone', 'fr' => 'Téléphone'],
            'validation_rules' => 'nullable|string|max:20',
            'sort_order' => 3,
        ]);

        FormField::create([
            'form_id' => $jobApplicationForm->id,
            'type' => 'text',
            'name' => 'position',
            'label' => ['en' => 'Position Applied For', 'fr' => 'Poste souhaité'],
            'validation_rules' => 'required|string|max:255',
            'sort_order' => 4,
        ]);

        FormField::create([
            'form_id' => $jobApplicationForm->id,
            'type' => 'textarea',
            'name' => 'cover_letter',
            'label' => ['en' => 'Cover Letter', 'fr' => 'Lettre de motivation'],
            'validation_rules' => 'nullable|string',
            'sort_order' => 5,
        ]);

        FormField::create([
            'form_id' => $jobApplicationForm->id,
            'type' => 'file',
            'name' => 'resume',
            'label' => ['en' => 'Resume/CV', 'fr' => 'CV'],
            'validation_rules' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'sort_order' => 6,
        ]);
    }
}