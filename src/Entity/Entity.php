<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;

class Entity
{
    public static $file = 'test.json';

    public array $values = [];

    public function __construct($jsonEntity, $selected)
    {
        #dd($jsonEntity);
        $this->values = $jsonEntity;
        $this->values['font'] = "Courier New";
        $this->values['isSelected'] = isset($this->values['text']) && $selected === $this->values['text'];
        if (isset($this->values['showAs']))
            $this->values['showAs'] = new Entity($this->findInJsonFile($this->values['showAs']), $selected);
        #$this->values['link_prefix'] = getenv("SERVER_NAME") . "/svg/";
    }

    public static function createEntity($selected) {
        return new Entity(["text" => $selected, "showAs" => "Entity", "x" => rand(30, 450), "y" => rand(20, 290)], $selected);
    }

    public function toJson(): array {
        return $this->values;
    }

    public function show() {
        $showAs = "";
        if (isset($this->values['showAs']))
            $showAs .= $this->values['showAs']->values['showAsSvg'];

        if (isset($this->values['showAsSvg']))
            $showAs .= $this->values['showAsSvg'];

        if ($this->values['isSelected']) {
            #if (!isset($this->values['showAsSvgIfSelected'])
            #    $this->values['showAsSvgIfSelected'] = $this->values['showAs']->values['showAsSvgIfSelected'];

            $showAs = $this->replaceXY(0, 0, $showAs);
            $showAs .= $this->values['showAs']->values['showAsSvgIfSelected'];
            $showAs = $this->replaceXY(-59, -5, $showAs);
            #dd(['showAs'=>$showAs, 'this' => $this]);
        }
        elseif (isset($this->values['showAsSvgIfNotSelected']))
            $showAs .= $this->values['showAsSvgIfNotSelected'];
        elseif (isset($this->values['showAs']->values['showAsSvgIfNotSelected']))
              $showAs .= $this->values['showAs']->values['showAsSvgIfNotSelected'];

        if (isset($this->values['text'])){ # ToDo fix this!
            $showAs = str_replace('{{ text }}', $this->values['text'], $showAs);
            $this->values['link'] = $this->values['link_prefix']. $this->values['text'];
            $showAs = str_replace('{{ link }}', $this->values['link'] , $showAs);

        #dd(['showAs'=>$showAs, 'this' => $this]);
        }

        if (isset($this->values['x']))
            $showAs = str_replace('{{ x }}', $this->values['x'], $showAs);
        if (isset($this->values['y']))
            $showAs = str_replace('{{ y }}', $this->values['y'], $showAs);
        if (isset($this->rx))
            $showAs = str_replace('{{ rx }}', $this->rx, $showAs);
        if (isset($this->ry))
            $showAs = str_replace('{{ ry }}', $this->ry, $showAs);
        if (isset($this->values['font']))
            $showAs = str_replace('{{ font }}', $this->values['font'], $showAs);

        #if (isset($this->link))

        #foreach($this->values as $key)
        #    $showAs = str_replace('{{ ' . $key . ' }}', $this->values[$key], $showAs);

        return $showAs;
    }

    public function replaceXY($deltaX, $deltaY, $showAs) {
        if (isset($this->values['x']))
            $showAs = str_replace('{{ x }}', $this->values['x'] + $deltaX, $showAs);
        if (isset($this->values['y']))
            $showAs = str_replace('{{ y }}', $this->values['y'] + $deltaY, $showAs);
        return $showAs;
    }

    public function getSvg() {
        if (isset($this->values['svg']))
            return $this->values['svg'];
        if (isset($this->values['showAs']->values['svg']))
            return $this->values['showAs']->values['svg'];
    }

    public function getSvgClosure() {
        if (isset($this->values['svgClosure']))
            return $this->values['svgClosure'];
        if (isset($this->values['showAs']->values['svgClosure']))
            return $this->values['showAs']->values['svgClosure'];
    }

    public static function findInJsonFile($title) {
        #dd($title);
        $str = file_get_contents(Entity::$file);
        $json = json_decode($str, true);
        return $json[$title];
    }
}
