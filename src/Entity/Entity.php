<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;

class Entity
{
    public static $file = 'test.json';

    public ?string $text = null;
    public ?string $font = null;
    public ?string $link = null;
    public ?int $x = null;
    public ?int $y = null;
    public bool $isSelected = false;
    public float $accepted = 0;
    public float $ignored = 0;
    public float $tolerated = 0;
    public float $declined = 0;
    public ?string $showAsSvg = null;
    public ?Entity $showAs = null;

    public function __construct($jsonEntity, $selected)
    {
        #dd($jsonEntity);
        if (isset($jsonEntity['text']))
            $this->text = $jsonEntity['text'];
        if (isset($jsonEntity['x']))
            $this->x    = $jsonEntity['x'];
        if (isset($jsonEntity['y']))
            $this->y    = $jsonEntity['y'];
        $this->isSelected = $selected === $this->text;
        if (isset($jsonEntity['showAsSvg']))
            $this->showAsSvg = $jsonEntity['showAsSvg'];
        if (isset($jsonEntity['showAsSvgIfSelected']))
            $this->showAsSvgIfSelected = $jsonEntity['showAsSvgIfSelected'];
        if (isset($jsonEntity['showAsSvgIfNotSelected']))
            $this->showAsSvgIfNotSelected = $jsonEntity['showAsSvgIfNotSelected'];
        if (isset($jsonEntity['showAs']))
            $this->showAs = new Entity($this->findInJsonFile($jsonEntity['showAs']), $selected);
    }

    public function toJson() {
        return [
            'text' => $this->text,
            'x' => $this->x,
            'y' => $this->y,
            'accpted' => $this->accepted,
            'ignored' => $this->ignored,
            'tolerated' => $this->tolerated,
            'declined' => $this->declined,
            'showAs' => $this->showAs,
            'showAsSvg' => $this->showAsSvg
        ];
    }

    public function show() {
        $showAs = "";
        if (isset($this->showAs))
            $showAs .= $this->showAs->show();
        if (isset($this->showAsSvg))
            $showAs .= $this->showAsSvg;
        if ($this->isSelected && isset($this->showAsSvgIfSelected))
            $showAs .= $this->showAsSvgIfSelected;
        elseif (isset($this->showAsSvgIfNotSelected))
            $showAs .= $this->showAsSvgIfNotSelected;
#dd($showAs);

        $this->font = "Courier New";


        if (isset($this->text)){ # ToDo fix this!
            $showAs = str_replace('{{ text }}', $this->text, $showAs);
            dd($showAs);
            $this->link = "https://localhost/svg/" . $this->text;
            $showAs = str_replace('{{ link }}', $this->link, $showAs);
        }
        if (isset($this->x))
            $showAs = str_replace('{{ x }}', $this->x, $showAs);
        if (isset($this->y))
            $showAs = str_replace('{{ y }}', $this->y, $showAs);
        if (isset($this->rx))
            $showAs = str_replace('{{ rx }}', $this->rx, $showAs);
        if (isset($this->ry))
            $showAs = str_replace('{{ ry }}', $this->ry, $showAs);
        if (isset($this->font))
            $showAs = str_replace('{{ font }}', $this->font, $showAs);
        if (isset($this->link))
        return $showAs;
    }

    public static function findInJsonFile($title) {
        #dd($title);
        $str = file_get_contents(Entity::$file);
        $json = json_decode($str, true);
        return $json[$title];
    }
}