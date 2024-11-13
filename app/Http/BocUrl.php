<?php

namespace App\Http;

enum BocUrl: string
{
    case Root = 'https://www.gobiernodecanarias.org';
    case Robots = 'https://www.gobiernodecanarias.org/robots.txt';
    case Archive = 'https://www.gobiernodecanarias.org/boc/archivo/';
    case YearIndex = 'https://www.gobiernodecanarias.org/boc/archivo/{year}/';
    case BulletinIndex = 'https://www.gobiernodecanarias.org/boc/{year}/{bulletin}/';
    case BulletinArticle = 'https://www.gobiernodecanarias.org/boc/{year}/{bulletin}/{article}.html';

    public static function fromName(string $name): ?BocUrl
    {
        foreach (self::cases() as $case) {
            if ($case->name == $name) {
                return $case;
            }
        }

        return null;
    }

    public function pattern(): string
    {
        return match ($this) {
            BocUrl::YearIndex => '/https:\/\/www.gobiernodecanarias.org\/boc(\/archivo)?\/(?P<year>\d{4,})(\/)?(index.html)?$/',
            BocUrl::BulletinIndex => '/https:\/\/www.gobiernodecanarias.org\/boc(\/archivo)?\/(?P<year>\d{4,})\/(?P<bulletin>\d{3,})(\/)?(index.html)?$/',
            BocUrl::BulletinArticle => '/https:\/\/www.gobiernodecanarias.org\/boc(\/archivo)?\/(?P<year>\d{4,})\/(?P<bulletin>\d{3,})\/(?P<article>\d{3,}).html$/',
            default => '/'.str_replace('/', '\/', rtrim($this->value, '/')).'/',
        };
    }

    public function contains(): ?BocUrl
    {
        return match ($this) {
            BocUrl::Archive => BocUrl::YearIndex,
            BocUrl::YearIndex => BocUrl::BulletinIndex,
            BocUrl::BulletinIndex => BocUrl::BulletinArticle,
            default => null,
        };
    }

    public function foundIn(): ?BocUrl
    {
        return match ($this) {
            BocUrl::YearIndex => BocUrl::Archive,
            BocUrl::BulletinIndex => BocUrl::YearIndex,
            BocUrl::BulletinArticle => BocUrl::BulletinIndex,
            default => null,
        };
    }
}
