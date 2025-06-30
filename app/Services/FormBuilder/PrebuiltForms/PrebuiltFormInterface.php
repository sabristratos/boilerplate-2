<?php

namespace App\Services\FormBuilder\PrebuiltForms;

interface PrebuiltFormInterface
{
    public function getName(): string;
    public function getDescription(): string;
    public function getElements(): array;
    public function getSettings(): array;
} 