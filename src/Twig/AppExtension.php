<?php


namespace App\Twig;

use Symfony\Component\Validator\Constraints\DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
          new TwigFilter('age', [$this, 'dateToAge']),
          new TwigFilter('list', [$this, 'stringToList'])
        ];
    }

    public function dateToAge($date) {
        if (!$date instanceof \DateTime) {
            // turn $date into a valid \DateTime object or let return
            return null;
        }
        $today = date("Y-m-d");
        $diff = date_diff(date_create($date->format('Y-m-d')), date_create($today));
        return $diff->format('%y');
    }

    public function stringToList($string) {
        if (!$string) {
            return null;
        }
        $listeContenu = explode("\n", $string);
        return $listeContenu;
    }
}