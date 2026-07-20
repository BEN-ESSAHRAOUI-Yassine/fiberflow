<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Audited = 'audited';
    case Validated = 'validated';
    case Archived = 'archived';
}
