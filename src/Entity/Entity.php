<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;

class Entity
{
    public static $file = 'test.json';

    private array $values = [
        'text' => "",
        'x' => "0",
        'y' => "0",
        'isSelected' => false,
        'accepted' => 0,
        'tolerated' => 0,
        'ignored' => 0,
        'declined' => 0,
        'showAsSvg' => "",
        'showAs' => "",
        'font' => "Courier New",
        'link' => "",
        'file' => 'test.json',
    ];

    public function __construct($jsonEntity, $selected)
    {
        #dd($jsonEntity);
        $this->values = $jsonEntity;
        $this->values['isSelected'] = isset($this->values['text']) && $selected === $this->values['text'];
        if (isset($this->values['showAs']))
            $this->values['showAs'] = new Entity($this->findInJsonFile($this->values['showAs']), $selected);
    }

    public function toJson(): array {
        return $this->values;
    }

    public function show() {
        $showAs = "";
        if (isset($this->values['showAs']))
            $showAs .= $this->values['showAs']->show();

        if (isset($this->values['showAsSvg']))
            $showAs .= $this->values['showAsSvg'];

        if ($this->values['isSelected'] && isset($this->values['showAsSvgIfSelected']))
            $showAs .= $this->values['showAsSvgIfSelected'];
        elseif (isset($this->values['showAsSvgIfNotSelected']))
            $showAs .= $this->values['showAsSvgIfNotSelected'];

        if (isset($this->values['text'])){ # ToDo fix this!
            $showAs = str_replace('{{ text }}', $this->values['text'], $showAs);
            #dd($showAs);
            $this->values['link'] = "https://localhost/svg/" . $this->values['text'];
            $showAs = str_replace('{{ link }}', $this->values['link'] , $showAs);
        }

        if (isset($this->values['x']))
            $showAs = str_replace('{{ x }}', $this->values['x'], $showAs);
        if (isset($this->values['y']))
            $showAs = str_replace('{{ y }}', $this->values['y'], $showAs);
        if (isset($this->values['rx']))
            $showAs = str_replace('{{ rx }}', $this->values['rx'], $showAs);
        if (isset($this->values['ry']))
            $showAs = str_replace('{{ ry }}', $this->values['ry'], $showAs);
        if (isset($this->values['font']))
            $showAs = str_replace('{{ font }}', $this->values['font'], $showAs);
        #if (isset($this->link))

        #foreach($this->values as $key)
        #    $showAs = str_replace('{{ ' . $key . ' }}', $this->values[$key], $showAs);

        #dd($showAs);
        return $showAs;
    }

    public static function findInJsonFile($title) {
        #dd($title);
        $str = file_get_contents(Entity::$file);
        $json = json_decode($str, true);
        return $json[$title];
    }
}
