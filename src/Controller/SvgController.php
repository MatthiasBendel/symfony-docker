<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;

class SvgController extends AbstractController
{
    private $file = 'test.json';
    private $svgFile = 'test.svg';

    #[Route('/svg', name: 'app_svg')]
    public function index(): Response
    {
        return new Response(
            $this->createSvg(),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    #[Route('/vote/{vote}/{item}', name: 'app_vote')]
    public function vote($vote, $item):  JsonResponse
    {
        if (in_array($vote, ['accept', 'tolerate', 'ignore', 'decline', 'select'])) {
            $str = file_get_contents($this->file);
            $json = json_decode($str, true); 
            $time = time();
            $json[$time] = array("vote" => $vote, "item" => $item);
            file_put_contents($this->file, json_encode($json));
            
            return $this->json([
                'message' => 'Voted '. $vote .' for ' . $item . ' (at ' . date('m.d.Y h:i:s a', $time) . ' (and added to ' . $this->file . '!'
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

    public function createSvg()
    {
        $cx = [240, 120, 360, 120, 360, 60];
        $cy = [150, 70, 70, 230, 230, 280];
        $rx = [60, 60, 60, 60, 60, 40];
        $ry = [40, 40, 40, 40, 40, 20];
        $text = ['selected', 'accepted', 'declined', 'tolerated', 'ignored', '1. Test'];
        $x = [240, 120, 360, 120, 360, 60];
        $y = [150, 70, 70, 230, 230, 280];
        $svg = "<svg version\"1.1\" width=\"2545px\" height=\"1000x\" viewBox=\"-0.5 -0.5 481 321\" class=\"ge-export-svg-dark\" style=\"background-color: rgb(18, 18, 18);\">
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

        for ($i = 0; $i < 6; $i++) {
            $svg .= $this->getSvgItem($cx[$i], $cy[$i], $rx[$i], $ry[$i], $x[$i], $y[$i], $text[$i]);
        }

        $svg .= "</svg>";
        return $svg;   
    }

    public function getSvgItem($cx, $cy, $rx, $ry, $x, $y, $text)
    {
        $svg = "<ellipse cx=\"" . $cx . "\" cy=\"" . $cy . "\" rx=\"" . $rx . "\" ry=\"" . $ry . 
                "\" fill=\"rgb(255, 255, 255)\" stroke=\"rgb(0, 0, 0)\" pointer-events=\"all\" />";
        $svg .= "<text x=\"" . $x . "\" y=\"" . $y . "\" fill=\"rgb(0, 0, 0)\" font-family=\"Helvetica\" font-size=\"12px\" text-anchor=\"middle\">" . $text . "</text>";
        
        return $svg;   
    }
}
