<?php

namespace App\Enums;

enum GigCategory: string
{
    case CAMERA = 'Camera';
    case EDITING = 'Editing';
    case SOUND = 'Sound';
    case PRODUCTION = 'Production';
    case ANIMATION = 'Animation';

    public function label(): string
    {
        return $this->value;
    }

    public function slug(): string
    {
        return strtolower($this->value);
    }

    public static function selectOptions(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function filterOptions(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->slug()] = $case->label();
        }

        return $options;
    }

    public static function values(): array
    {
        return array_map(static fn(self $case): string => $case->value, self::cases());
    }

    public static function isValid(string $category): bool
    {
        return self::tryFrom($category) !== null;
    }
}