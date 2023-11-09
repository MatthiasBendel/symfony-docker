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
          if (gettype($entity) == "array" && isset($entity['text'])){
            $entity['x'] = rand($entity['x'] - 30, $entity['x'] + 30);
            $entity['y'] = rand($entity['y'] - 20, $entity['y'] + 20);
            array_push($entities, new Entity($entity, $selected));
          }
        }

        return new Response(
            $this->createHtml($entities),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    #[Route('/twig/{selected}', name: 'app_twig')]
    public function twig($selected): Response
    {
        $entities = [];
        $jsonResponse = $this->vote("select", $selected);
        foreach (json_decode($jsonResponse->getContent(), true)['entities'] as $entity) {
          if (gettype($entity) == "array" && isset($entity['text'])){
            $entity['x'] = rand($entity['x'] - 30, $entity['x'] + 30);
            $entity['y'] = rand($entity['y'] - 20, $entity['y'] + 20);
            array_push($entities, new Entity($entity, $selected));
          }
        }
        return $this->render('time_sequence.html.twig', [
          'svg' => $this->createSvg($entities, 'https://localhost/twig/')
        ]);
    }

    #[Route('/vote/{vote}/{item}', name: 'app_vote')]
    public function vote($vote, $item):  JsonResponse
    {
      if ($item == null)
        $item = $entity->getJson['text'];
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
          file_put_contents($this->file, json_encode($json, JSON_PRETTY_PRINT));
          return $this->json([
              'message' => 'Voted '. $vote .' for ' . $item . ' (at ' . date('m.d.Y h:i:s a', $time) . ' (and added to ' . $this->file . '!',
              'entities' => $json
          ]);
        }
        return $this->json([
            'error' => 'Invalid vote: ' . $vote
        ]);
    }

    public function createHtml($entities, $linkPrefix='https://localhost/svg/')
    {
        foreach (array_reverse($entities) as $entity)
            if ($entity->toJson()['showAs'])
              $selectedEntity = $entity;
        $html = $selectedEntity->toJson()['showAs']->toJson()['html'];
        $html = str_replace('{{ style }}', $selectedEntity->toJson()['showAs']->toJson()['style'] , $html);
        $html = str_replace('{{ svg }}', $this->createSvg($entities, $linkPrefix) , $html);
        $html = str_replace('<body>', "<body>\n" .
                    "<p>Welc    kkkkjjjjjjj\n\n\njjjjjjjjome!</p>", $html);
        #dd($html);
        if ($selectedEntity == null)
            dd($selectedEntity);
        return $html;
    }

    public function createSvg($entities, $linkPrefix='https://localhost/svg/')
    {
        $rx = 40;
        $ry = 20;

        $svg = "<svg width=\"3030px\" height=\"1500px\" viewBox=\"-0.5 -0.5 400 321\" class=\"ge-export-svg-dark\">
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
        <g id=\"all\">";

        foreach (array_reverse($entities) as $entity)
            if ($entity->toJson()['showAs'])
              $selectedEntity = $entity;


        # $entities->trim($showEntityCount);
        #$svg = $selectedEntity->getSvg();
        foreach (array_reverse($entities) as $entity){
          $entity->values['link_prefix'] = $linkPrefix;
          $entity->rx = ++$rx;
          $entity->ry = ++$ry;
          $svg .= $entity->show();
        }

        #$svg .= $selectedEntity->getSvgClosure();
        #dd($svg);
        $svg .= "</svg>";
        return $svg;
    }
}
