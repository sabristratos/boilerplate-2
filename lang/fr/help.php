<?php

return [
    'welcome_message' => 'Bienvenue dans votre panneau d\'administration ! Ce guide vous aidera à comprendre et utiliser toutes les fonctionnalités disponibles pour gérer votre site web.',

    'quick_start' => [
        'title' => 'Guide de démarrage rapide',
        'description' => 'Nouveau dans le panneau d\'administration ? Commencez ici pour vous familiariser avec les bases :',
        'dashboard' => [
            'title' => 'Aperçu du tableau de bord',
            'description' => 'Votre tableau de bord affiche les statistiques clés et un accès rapide aux fonctionnalités importantes. Consultez-le régulièrement pour surveiller l\'activité de votre site web.',
        ],
        'content_management' => [
            'title' => 'Gestion du contenu',
            'description' => 'Créez et modifiez des pages, gérez des formulaires et organisez vos fichiers multimédias. C\'est là que vous passerez la plupart de votre temps.',
        ],
        'pro_tip' => [
            'title' => 'Conseil Pro',
            'description' => 'Utilisez la barre de recherche en haut de la barre latérale pour trouver rapidement n\'importe quelle page ou fonctionnalité. Vous pouvez également utiliser les raccourcis clavier comme Ctrl+F (Cmd+F sur Mac) pour rechercher dans les pages.',
        ],
    ],

    'content_management' => [
        'title' => 'Gestion du contenu',
        'pages' => [
            'title' => 'Pages',
            'description' => 'Créez et modifiez des pages web à l\'aide de notre constructeur de pages visuel.',
            'how_to_create' => [
                'title' => 'Comment créer une page :',
                'steps' => [
                    'Allez dans Pages dans la barre latérale',
                    'Cliquez sur "Créer une nouvelle page"',
                    'Ajoutez un titre et du contenu',
                    'Utilisez le constructeur de pages pour ajouter des sections',
                    'Enregistrez et publiez quand vous êtes prêt',
                ],
            ],
        ],
        'forms' => [
            'title' => 'Formulaires',
            'description' => 'Créez des formulaires personnalisés pour collecter des informations auprès de vos visiteurs.',
            'features' => [
                'title' => 'Fonctionnalités du constructeur de formulaires :',
                'items' => [
                    'Éléments de formulaire glisser-déposer',
                    'Plusieurs types de champs (texte, email, téléchargement de fichiers, etc.)',
                    'Règles de validation personnalisées',
                    'Mode aperçu pour tester les formulaires',
                    'Voir et exporter les soumissions',
                ],
            ],
        ],
        'media_library' => [
            'title' => 'Médiathèque',
            'description' => 'Organisez et gérez toutes vos images, documents et autres fichiers.',
            'tips' => [
                'title' => 'Conseils de gestion des médias :',
                'items' => [
                    'Téléchargez des fichiers en les faisant glisser ou en cliquant sur télécharger',
                    'Ajoutez des médias à partir d\'URL pour les fichiers externes',
                    'Recherchez et filtrez par type de fichier',
                    'Les images sont automatiquement optimisées',
                    'Utilisez des collections pour organiser les fichiers',
                ],
            ],
        ],
    ],

    'platform_tools' => [
        'title' => 'Outils de plateforme',
        'settings' => [
            'title' => 'Paramètres',
            'description' => 'Configurez l\'apparence de votre site web, les paramètres d\'email et plus encore.',
            'categories' => [
                'title' => 'Catégories de paramètres clés :',
                'items' => [
                    'Général : Nom du site, URL, langues',
                    'Apparence : Thème, couleurs, logo',
                    'Email : Paramètres SMTP, notifications',
                    'Sécurité : Politiques de mot de passe, 2FA',
                    'SEO : Balises meta, paramètres de sitemap',
                ],
            ],
        ],
        'translations' => [
            'title' => 'Traductions',
            'description' => 'Gérez plusieurs langues pour le contenu de votre site web.',
            'features' => [
                'title' => 'Fonctionnalités de traduction :',
                'items' => [
                    'Ajoutez facilement de nouvelles langues',
                    'Traduisez le texte de l\'interface',
                    'Gérez les traductions de contenu',
                    'Définissez les langues par défaut et de secours',
                    'Exportez/importez des fichiers de traduction',
                ],
            ],
        ],
        'database_backup' => [
            'title' => 'Sauvegarde de base de données',
            'description' => 'Créez des sauvegardes de vos données de site web pour la sécurité.',
            'best_practices' => [
                'title' => 'Meilleures pratiques de sauvegarde :',
                'items' => [
                    'Créez des sauvegardes régulières (hebdomadaire recommandé)',
                    'Téléchargez les sauvegardes sur votre ordinateur',
                    'Testez périodiquement la restauration des sauvegardes',
                    'Conservez plusieurs versions de sauvegarde',
                    'Stockez les sauvegardes dans un endroit sécurisé',
                ],
            ],
        ],
    ],

    'tutorials' => [
        'title' => 'Tutoriels étape par étape',
        'create_first_page' => [
            'title' => 'Comment créer votre première page',
            'steps' => [
                'Naviguez vers Pages dans le menu de la barre latérale',
                'Cliquez sur le bouton "Créer une nouvelle page"',
                'Remplissez les informations de base :',
                'Utilisez le constructeur de pages pour ajouter des sections de contenu',
                'Cliquez sur "Enregistrer brouillon" pour sauvegarder votre travail, ou "Publier" pour le rendre public',
            ],
            'basic_info' => [
                'Titre de la page (apparaîtra dans l\'onglet du navigateur)',
                'Slug URL (l\'adresse web de votre page)',
                'Meta description (pour les moteurs de recherche)',
            ],
        ],
        'build_contact_form' => [
            'title' => 'Créer un formulaire de contact',
            'steps' => [
                'Allez dans Formulaires dans la barre latérale',
                'Cliquez sur "Créer un nouveau formulaire"',
                'Utilisez le constructeur de formulaires pour ajouter des champs :',
                'Configurez les règles de validation (champs requis, format email, etc.)',
                'Testez votre formulaire en utilisant le mode aperçu',
                'Enregistrez et intégrez le formulaire sur votre site web',
            ],
            'fields' => [
                'Champ texte pour le nom',
                'Champ email pour l\'adresse email',
                'Zone de texte pour le message',
                'Bouton d\'envoi',
            ],
        ],
        'manage_media_library' => [
            'title' => 'Gérer votre médiathèque',
            'steps' => [
                'Accédez à la Médiathèque depuis la barre latérale',
                'Téléchargez des fichiers en :',
                'Organisez les fichiers en utilisant des collections (dossiers)',
                'Recherchez et filtrez les fichiers par nom, type ou date',
                'Utilisez les fichiers dans vos pages et formulaires en les sélectionnant depuis le sélecteur de médias',
            ],
            'upload_methods' => [
                'Faisant glisser les fichiers directement dans la zone de téléchargement',
                'Cliquant sur "Télécharger" et sélectionnant des fichiers',
                'Ajoutant des fichiers à partir d\'une URL',
            ],
        ],
        'configure_settings' => [
            'title' => 'Configurer les paramètres du site web',
            'steps' => [
                'Allez dans Paramètres dans la barre latérale',
                'Commencez par les paramètres généraux :',
                'Personnalisez l\'apparence :',
                'Configurez les paramètres d\'email pour les notifications',
                'Configurez les paramètres SEO pour une meilleure visibilité dans les moteurs de recherche',
            ],
            'general_settings' => [
                'Définissez le nom et l\'URL de votre site web',
                'Choisissez votre langue par défaut',
                'Définissez le fuseau horaire et le format de date',
            ],
            'appearance_settings' => [
                'Téléchargez votre logo et favicon',
                'Choisissez vos couleurs de marque',
                'Sélectionnez un thème (mode clair/sombre)',
            ],
        ],
    ],

    'common_tasks' => [
        'title' => 'Tâches courantes',
        'quick_actions' => [
            'title' => 'Actions rapides',
            'add_page' => 'Ajouter une nouvelle page : Pages → Créer une nouvelle page',
            'create_form' => 'Créer un formulaire : Formulaires → Créer un nouveau formulaire',
            'upload_media' => 'Télécharger des médias : Médiathèque → Bouton Télécharger',
            'change_settings' => 'Modifier les paramètres : Paramètres → Sélectionner une catégorie',
        ],
        'troubleshooting' => [
            'title' => 'Dépannage',
            'page_not_saving' => 'La page ne s\'enregistre pas ? Vérifiez que tous les champs requis sont remplis',
            'form_not_working' => 'Le formulaire ne fonctionne pas ? Vérifiez que les paramètres d\'email sont configurés',
            'images_not_loading' => 'Les images ne se chargent pas ? Vérifiez la taille du fichier (max 10MB) et le format',
            'cant_login' => 'Impossible de se connecter ? Essayez de réinitialiser votre mot de passe',
        ],
    ],

    'best_practices' => [
        'title' => 'Conseils et meilleures pratiques',
        'security' => [
            'title' => 'Sécurité',
            'items' => [
                'Utilisez des mots de passe forts et uniques',
                'Activez l\'authentification à deux facteurs',
                'Mettez régulièrement à jour vos identifiants de connexion',
                'Créez des sauvegardes régulières de vos données',
            ],
        ],
        'performance' => [
            'title' => 'Performance',
            'items' => [
                'Optimisez les images avant de les télécharger',
                'Utilisez des noms de fichiers descriptifs',
                'Gardez les pages ciblées et concises',
                'Testez les formulaires avant de les publier',
            ],
        ],
        'seo' => [
            'title' => 'SEO',
            'items' => [
                'Écrivez des titres de page descriptifs',
                'Ajoutez des meta descriptions à toutes les pages',
                'Utilisez du texte alternatif pour les images',
                'Créez une structure de site logique',
            ],
        ],
    ],

    'keyboard_shortcuts' => [
        'title' => 'Raccourcis clavier',
        'general' => [
            'title' => 'Raccourcis généraux',
            'save' => 'Enregistrer la page/formulaire actuel',
            'search' => 'Rechercher dans la page',
            'undo' => 'Annuler la dernière action',
            'redo' => 'Rétablir la dernière action',
        ],
        'navigation' => [
            'title' => 'Raccourcis de navigation',
            'help' => 'Aller à la page d\'aide',
            'dashboard' => 'Aller au tableau de bord',
            'pages' => 'Aller aux pages',
            'media' => 'Aller à la médiathèque',
        ],
    ],

    'support' => [
        'title' => 'Besoin d\'aide supplémentaire ?',
        'questions' => [
            'title' => 'Vous avez encore des questions ?',
            'description' => 'Si vous ne trouvez pas ce que vous cherchez dans ce guide d\'aide, n\'hésitez pas à contacter le support. Notre équipe est là pour vous aider à réussir avec votre site web.',
        ],
        'contact' => [
            'title' => 'Contacter le support',
            'email' => 'Email : :email',
            'response_time' => 'Temps de réponse : Sous 24 heures',
            'include_screenshots' => 'Incluez des captures d\'écran pour une aide plus rapide',
            'mention_username' => 'Mentionnez votre nom d\'utilisateur administrateur',
        ],
        'pro_tip' => [
            'title' => 'Conseil Pro',
            'description' => 'Avant de contacter le support, essayez d\'utiliser la fonction de recherche de cette page d\'aide ou vérifiez si votre question est répondue dans les tutoriels étape par étape ci-dessus. La plupart des problèmes courants peuvent être résolus rapidement !',
        ],
    ],
];
