<?php

declare(strict_types=1);

namespace App\Services\FormBuilder;

class OptionParserService
{
    /**
     * Parse options string into array format
     * Supports both simple format (one option per line) and value|label format
     */
    public function parseOptions(string $options): array
    {
        if (empty($options)) {
            return [];
        }

        $lines = explode("\n", trim($options));
        $parsedOptions = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (str_contains($line, '|')) {
                [$value, $label] = explode('|', $line, 2);
                $parsedOptions[] = [
                    'value' => trim($value),
                    'label' => trim($label),
                ];
            } else {
                $parsedOptions[] = [
                    'value' => $line,
                    'label' => $line,
                ];
            }
        }

        return $parsedOptions;
    }

    /**
     * Parse options for preview mode (returns empty array if no options)
     */
    public function parseOptionsForPreview(string $options): array
    {
        $parsedOptions = $this->parseOptions($options);

        // If no options were parsed, return at least one empty option
        if (empty($parsedOptions)) {
            return [
                [
                    'value' => '',
                    'label' => '',
                ],
            ];
        }

        return $parsedOptions;
    }
}
