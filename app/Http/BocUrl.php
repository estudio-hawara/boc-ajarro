<?php

namespace App\Http;

enum BocUrl: string
{
    case Root = 'https://www.gobiernodecanarias.org';
    case Robots = 'https://www.gobiernodecanarias.org/robots.txt';
    case Archive = 'https://www.gobiernodecanarias.org/boc/archivo/';
    case YearIndex = 'https://www.gobiernodecanarias.org/boc/{year}/';
    case BulletinIndex = 'https://www.gobiernodecanarias.org/boc/{year}/{bulletin}/';
    case BulletinArticle = 'https://www.gobiernodecanarias.org/boc/{year}/{bulletin}/{article}.html';

    public function pattern(): string
    {
        return match($this) {
            BocUrl::YearIndex => '/https:\/\/www.gobiernodecanarias.org\/boc(\/archivo)?\/\d{4,}(\/)?$/',
            BocUrl::BulletinIndex => '/https:\/\/www.gobiernodecanarias.org\/boc(\/archivo)?\/\d{4,}\/\d{3,}(\/)?$/',
            BocUrl::BulletinArticle => '/https:\/\/www.gobiernodecanarias.org\/boc(\/archivo)?\/\d{4,}\/\d{3,}\/\d{3,}.html$/',
            default => '/'. str_replace('/', '\/', rtrim($this->value, '/')) . '/',
        };
    }
}
