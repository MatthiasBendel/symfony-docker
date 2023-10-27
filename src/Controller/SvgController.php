<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Entity;

use DateTime;

class SvgController extends AbstractController
{
    private $file = 'test.json';
    private $svgFile = 'test.svg';

    #[Route('/svg/{selected}', name: 'app_svg')]
    public function index($selected): Response
    {
        $entities = [];
        $jsonResponse = $this->vote("select", $selected);
        foreach (json_decode($jsonResponse->getContent(), true)['entities'] as $entity) {
          if (gettype($entity) == "array" && isset($entity['text']))
            array_push($entities, new Entity($entity, $selected));
        }

        return new Response(
            $this->createSvg($entities),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    #[Route('/vote/{vote}/{item}', name: 'app_vote')]
    public function vote($vote, $item):  JsonResponse
    {
      if ($item == null)
        $item = $entity->text;
      if (in_array($vote, ['accept', 'tolerate', 'ignore', 'decline', 'select'])) {
          $str = file_get_contents($this->file);
          $json = json_decode($str, true); 
          $time = time();
          $json[$time] = array("vote" => $vote, "item" => $item);
          if (isset($entity)) 
            $json[$item] = $entity;
          if (!isset($json[$item]['showAs']))
            $json[$item]['showAs'] = 'Entity';
          #if (!isset($json[$item]['showAsSvg']))
          #  $json[$item]['showAsSvg'] = $this->getSvgItem(30, 30, new Entity($json[$item], $item));
          file_put_contents($this->file, json_encode($json));
          return $this->json([
              'message' => 'Voted '. $vote .' for ' . $item . ' (at ' . date('m.d.Y h:i:s a', $time) . ' (and added to ' . $this->file . '!',
              'entities' => $json
          ]);
        }
        return $this->json([
            'error' => 'Invalid vote: ' . $vote
        ]);
    }

    #[Route('/json', name: 'app_json')]
    public function getJson(): JsonResponse
    {
        $str = file_get_contents($this->file);
        $json = json_decode($str, true); 
        return $this->json([
            'message' => 'This is ' . $this->file . '!',
            'content' => $json
        ]);
    }

    public function createSvg($entities)
    {
        $rx = 40;
        $ry = 20;

        $htmlStyle = "<style>
        body {
        background-color: #3A3A3A
        }
        </style>";
        $svg = "<svg version\"1.1\" width=\"2530px\" height=\"1100px\" viewBox=\"-0.5 -0.5 481 321\" class=\"ge-export-svg-dark\">
        <defs>
          <style type=\"text/css\">
            svg.ge-export-svg-dark &gt;
      
            * {
              filter: invert(100%) hue-rotate(180deg);
            }
      
            &#xa;
      
            svg.ge-export-svg-dark image {
              filter: invert(100%) hue-rotate(180deg)
            }
          </style>
        </defs>
        <g>";

        foreach (array_reverse($entities) as $entity)
            if ($entity->isSelected)
              $selectedEntity = $entity;
            #else
            #  $svg .= $this->getSvgConnection(++$rx, ++$ry, $entity->x, $entity->y, $entity->text, $entity->isSelected);

        foreach (array_reverse($entities) as $entity){
          $entity->rx = ++$rx;
          $entity->ry = ++$ry;
          $svg .= $this->getSvgItem(++$rx, ++$ry, $entity);
        }
        
        $svg .= "</svg>";
        return "<!DOCTYPE html><html><head>" . $htmlStyle . "<body>" . $svg . "</body> </html>";   
    }

    public function getSvgItem($rx, $ry, $entity)
    {
        $font = "Courier New";
        $link = "https://localhost/svg/" . $entity->text;
        $svg = $entity->show();
        if ($entity->isSelected)
          $svg .= "<foreignObject pointer-events=\"none\" width=\"100%\" height=\"100%\" style=\"overflow: visible; text-align: left;\">
            <div style=\"display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: " . $entity->y . "px; margin-left: " . $entity->x - 57 . "px;\">
              <div data-drawio-colors=\"color: rgb(0, 0, 0); \" style=\"box-sizing: border-box; font-size: 0px; text-align: center;\">
                <div style=\"display: inline-block; font-size: 12px; font-family: " . $font . "; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;\">
                  <font face=\"" . $font . "\">
                    <u>
                      <a href=\"" . $link . "\" target=\"_top\">" . $entity->text . "</a>
                    </u>
                  </font>
                </div>
              </div>
            </div>
          </foreignObject>";
        else
           $svg .= "<a href=\"" . $link . "\" target=\"_top\">" . "<text x=\"" . $entity->x . "\" y=\"" . $entity->y . "\" fill=\"rgb(0, 0, 0)\" font-family=\"" . $font . "\" font-size=\"12px\" text-anchor=\"middle\">" . $entity->text . "</text>" . "</a>";

        
        return $svg;   
    }

    public function getSvgConnection($rx, $ry, $x, $y, $text, $selected)
    {
        $font = "Courier New";
        $link = "https://localhost/svg/" . $text;
        $svg = "<path d=\"M " . $x . " " . $y . " Q 137.44 228.19 240 150\" fill=\"none\" stroke=\"rgb(0, 0, 0)\" stroke-miterlimit=\"10\" pointer-events=\"stroke\" />";        
        $svg .= "<ellipse cx=\"" . $x . "\" cy=\"" . $y . "\" rx=\"" . $rx . "\" ry=\"" . $ry . 
                "\" fill=\"rgb(255, 255, 255)\" stroke=\"rgb(200, 200, 200)\" pointer-events=\"all\" />";
        return $svg;   
    }
}
