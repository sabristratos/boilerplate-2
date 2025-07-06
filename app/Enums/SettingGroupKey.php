<?php

namespace App\Enums;

enum SettingGroupKey: string
{
    case GENERAL = 'general';
    case APPEARANCE = 'appearance';
    case EMAIL = 'email';
    case SECURITY = 'security';
    case SOCIAL = 'social';
    case ADVANCED = 'advanced';
    case CONTACT = 'contact';
    case CONTENT = 'content';
    case NAVIGATION = 'navigation';
    case SEO = 'seo';
}
