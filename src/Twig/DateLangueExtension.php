<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateLangueExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('datelangue', [$this, 'formatDateLangue']),
        ];
    }

    public function formatDateLangue(string $date, string $locale): string
    {
        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE
        );

        $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
        if ($dateTime === false) {
            return $date;
        }

        return $formatter->format($dateTime);
    }
}
