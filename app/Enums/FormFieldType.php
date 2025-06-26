<?php

namespace App\Enums;

enum FormFieldType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case EMAIL = 'email';
    case NUMBER = 'number';
    case DATE = 'date';
    case TIME = 'time';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case FILE = 'file';
    case SECTION = 'section';
} 