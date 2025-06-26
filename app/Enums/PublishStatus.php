<?php

namespace App\Enums;

enum PublishStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
} 