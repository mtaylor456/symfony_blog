<?php


namespace AppBundle\Service;


class Slugify
{
    public function slugify($string)
    {
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(strip_tags($string))), '-');
    }
}