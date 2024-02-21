<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Entity;

use DateTime;

class SvgController extends AbstractController
{
    private $file = 'test.json';
    private $svgFile = 'test.svg';
    private $v2_jsonFfile = 'v2.json';
    private $v2_svgFile = 'Werte_v3.6_controls.svg';

    public function __construct()
    {
        //$env = parse_ini_file('../.env');
        $this->serverName = 'https://' . "multidimensional.online";//$env["SERVER_NAME"];
    }

    #[Route('/v2/{selected}', name: 'app_v2')]
    public function v2($selected): Response
    {
        $svg = "";
        $json = $this->getJsonResponse($this->v2_jsonFfile);
        $entities = $json['person_1']['entities'];
       //dd($entities);
        foreach ($entities as $key => $entity) {
            foreach (['text'] as $replacement) {
                if (array_key_exists($replacement, $entity)) {
                    $entitySvg = str_replace('{{ ' . $replacement . ' }}', $entity[$replacement], $this->getSvg($entities, $entity));
                }
            }
            $svg .= str_replace('{{ class }}', $key, $entitySvg);
        }
        // Generate random values for randomTop and randomLeft
        $randomTop = rand(0, 600); // Replace 500 with the maximum top value
        $randomLeft = rand(0, 1200); // Replace 500 with the maximum left value

        //$svg = XmlUtils::loadFile($this->v2_svgFile);
        //...
//dd($entities);
        // Pass the randomTop and randomLeft variables to the Twig template
        return $this->render('draganddrop.html.twig', [
            'randomTop' => $randomTop,
            'randomLeft' => $randomLeft,
            'svg' => $svg,
            'font' => 'Courier New',
            'iFrame' => "<iframe src=\"https://www.audio.com/pukpuk\" width=\"100%\" height=\"200\" style=\"border:none;\">
                  </iframe>",
            'js_scripts' => [
                    "js/KeyboardReader.js",
                    "js/SvgMover.js",
                    "js/DragAndDropEllipses.js"
                ],
            'scripts' => [],
            'link' => 'localhost/v2/',
            'entities' => json_encode($entities)
        ]);
    }

    private function getSvg($entities, $entity) {
        if (array_key_exists('svg', $entity)) {
            return $entity['svg'];
        } else {
            return $this->getSvg($entities, $entities[$entity['show_as']]);
        }
    }

    private function prepareEntities($selected, $jsonFile) {
      $entities = [];
      $json = $this->getJsonResponse($jsonFile);
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

    #[Route('/svg/{selected}', name: 'app_svg')]
    public function index($selected): Response
    {
      $entities = $this->prepareEntities($selected, $this->file);

        return new Response(
            $this->createHtml($entities),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    #[Route('/import/{selected}', name: 'app_import')]
    public function import($selected): Response
    {
      //TODO: import file!
      $entities = $this->prepareEntities($selected, $this->file);

        return new Response(
            $this->createHtml($entities),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    #[Route('/python/{selected}', name: 'app_python')]
    public function python($selected): Response
    {
        $entities = $this->prepareEntities($selected, $this->file);
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
        $entities = $this->prepareEntities($selected, $this->file);
        // Generate random values for randomTop and randomLeft
        $randomTop = rand(0, 600); // Replace 500 with the maximum top value
        $randomLeft = rand(0, 1200); // Replace 500 with the maximum left value

        // Pass the randomTop and randomLeft variables to the Twig template
        return $this->render('controls.html.twig', [
            'randomTop' => $randomTop,
            'randomLeft' => $randomLeft,
            'svg' => $this->createSvg($entities, $this->serverName . '/twig/'),
            'font' => 'Courier New'
        ]);
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
          $json = $this->getJsonResponse($this->file);
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

    /**
     * @Route("/save", name="app_save", methods={"POST"})
     */
    public function save(Request $request)
    {
            return new JsonResponse(['message' => 'Data saved successfully']);

        // Get the JSON data from the request
        $jsonData = json_decode($request->getContent(), true);

        $json = getJsonResponse();
        $json['person_1']['entities']['ellipse']['x'] = $jsonData['x'];
        $json['person_1']['entities']['ellipse']['y'] = $jsonData['y'];

        // Return a JSON response
        return new JsonResponse(['message' => 'Data saved successfully']);
    }

    private function getJsonResponse($jsonFile) {
      if (!isset($this->jsonResponse)){
        $str = file_get_contents($jsonFile);
        $this->jsonResponse = json_decode($str, true);
      }
      return $this->jsonResponse;
    }

    private function createHtml($entities, $link_prefix='/svg/')
    {
        $link_prefix = $this->serverName . $link_prefix;
        foreach (array_reverse($entities) as $entity)
            if ($entity->toJson()['showAs'])
              $selectedEntity = $entity;
        $html = $selectedEntity->toJson()['showAs']->toJson()['html'];
        $html = str_replace('{{ style }}', $selectedEntity->toJson()['showAs']->toJson()['style'] , $html);
        $html = str_replace('{{ svg }}', $this->createSvg($entities, $link_prefix) , $html);
        $html = str_replace('<body>', "<body>\n" .
                    "<p>Welcome!</p>", $html);
        if ($selectedEntity == null)
            dd($selectedEntity);
        return $html;
    }
}
