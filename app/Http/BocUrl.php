<?php

namespace App\Http;

enum BocUrl: string
{
    case Root = 'https://www.gobiernodecanarias.org';
    case Archive = 'https://www.gobiernodecanarias.org/boc/archivo/';
    case YearIndex = 'https://www.gobiernodecanarias.org/boc/{year}/';
    case BulletinIndex = 'https://www.gobiernodecanarias.org/boc/{year}/{bulletin}/';
    case BulletinArticle = 'https://www.gobiernodecanarias.org/boc/{year}/{bulletin}/{article}.html';
}
