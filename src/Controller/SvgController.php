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

    public function __construct()
    {
        //$env = parse_ini_file('../.env');
        $this->serverName = 'https://' . "multidimensional.online";//$env["SERVER_NAME"];
    }

    #[Route('/svg/{selected}', name: 'app_svg')]
    public function index($selected): Response
    {
      $entities = $this->prepareEntities($selected);

        return new Response(
            $this->createHtml($entities),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    #[Route('/python/{selected}', name: 'app_python')]
    public function python($selected): Response
    {
        $entities = $this->prepareEntities($selected);
        $newPythonEntity = Entity::createEntity("binance_client.py"); # todo search for already existing one
        array_push($entities, $newPythonEntity);
        if ($selected === "binance_client.py") {
            exec("docker exec -it symfony-docker-python-1 pip install -r /scripts/requirements.txt");
            exec("docker exec -it symfony-docker-python-1 python /scripts/binance_client.py");
        }
        return $this->render('time_sequence.html.twig', [
          'svg' => $this->createSvg($entities, $this->serverName . '/python/')
        ]);
    }

    #[Route('/twig/{selected}', name: 'app_twig')]
    public function twig($selected): Response
    {
        $entities = $this->prepareEntities($selected);
        return $this->render('input.html.twig', [
          'svg' => $this->createSvg($entities, $this->serverName . '/twig/'),
          'font' => 'Courier New'
        ]);
    }

    private function prepareEntities($selected) {
      $entities = [];
      $json = $this->getJsonResponse();
      foreach ($json as $entity) {
        if (gettype($entity) == "array" && isset($entity['text'])){
          $entity['x'] = rand($entity['x'] - 30, $entity['x'] + 30);
          $entity['y'] = rand($entity['y'] - 20, $entity['y'] + 20);
          $newSelectedEntity = new Entity($entity, $selected);
          array_push($entities, $newSelectedEntity);
          if ($entity['text'] === $selected)
            $jsonResponse = $this->vote("select", $newSelectedEntity);
        }
      }
      if (!$this->containsEntity($entities, $selected)) {
        $newSelectedEntity = Entity::createEntity($selected);
        array_push($entities, $newSelectedEntity);
        $jsonResponse = $this->vote("select", $newSelectedEntity);
      }
      return $entities;
    }

    private function containsEntity($entities, $selected) {
      foreach ($entities as $entity) {
        if ($entity->values['text'] === $selected)
          return true;
      }
      return false;
    }

    #[Route('/vote/{vote}/{item}', name: 'app_vote')]
    public function vote($vote, $entity):  JsonResponse
    {
      $item = $entity->values['text'];
      if ($item == null)
        $item = $entity->getJson['text'];
      if (in_array($vote, ['accept', 'tolerate', 'ignore', 'decline', 'select'])) {
          $json = $this->getJsonResponse();
          $time = time();
          $json[$time] = array("vote" => $vote, "item" => $item);
          //if (isset($entity))
            //$json[$item] = $entity;
          if (!isset($json[$item]))
            $json[$item] = $entity->values;
          if (!isset($json[$item]->values['showAs']))
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

    private function getJsonResponse() {
      if (!isset($this->jsonResponse)){
        $str = file_get_contents($this->file);
        $this->jsonResponse = json_decode($str, true);
      }
      return $this->jsonResponse;
    }

    private function createHtml($entities, $linkPrefix='/svg/')
    {
        $link_prefix = $this->serverName . $link_prefix;
        foreach (array_reverse($entities) as $entity)
            if ($entity->toJson()['showAs'])
              $selectedEntity = $entity;
        $html = $selectedEntity->toJson()['showAs']->toJson()['html'];
        $html = str_replace('{{ style }}', $selectedEntity->toJson()['showAs']->toJson()['style'] , $html);
        $html = str_replace('{{ svg }}', $this->createSvg($entities, $linkPrefix) , $html);
        $html = str_replace('<body>', "<body>\n" .
                    "<p>Welcome!</p>", $html);
        if ($selectedEntity == null)
            dd($selectedEntity);
        return $html;
    }

    private function createSvg($entities, $linkPrefix='/svg/')
    {
        #$linkPrefix = $this->serverName . $linkPrefix;
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

/*        foreach (array_reverse($entities) as $entity)
            if ($entity->toJson()['showAs'])
              $selectedEntity = $entity;
*/

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
