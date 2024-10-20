<?php

namespace App\Http;

enum BocUrl: string
{
    case Root = 'https://www.gobiernodecanarias.org';
    case Archive = 'https://www.gobiernodecanarias.org/boc/archivo/';
    case YearIndex = 'https://www.gobiernodecanarias.org/boc/{year}/';
}
