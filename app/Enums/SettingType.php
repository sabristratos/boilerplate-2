<?php

namespace App\Enums;

enum SettingType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case SELECT = 'select';
    case FILE = 'file';
    case MEDIA = 'media';
    case COLOR = 'color';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case EMAIL = 'email';
    case NUMBER = 'number';
    case PASSWORD = 'password';
    case RANGE = 'range';
    case TEL = 'tel';
    case TIME = 'time';
    case URL = 'url';
    case REPEATER = 'repeater';
}
