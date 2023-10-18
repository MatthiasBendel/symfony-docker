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

    #[Route('/svg/{selected}', name: 'app_svg')]
    public function index($selected): Response
    {
        return new Response(
            $this->createSvg($selected),
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

    public function createSvg($selected)
    {
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
        for ($i = 0; $i < 6; $i++)
            $svg .= $this->getSvgItem($rx[$i], $ry[$i], $x[$i], $y[$i], $text[$i], $text[$i] === $selected);
        
        $svg .= "</svg>";
        return $svg;   
    }

    public function getSvgItem($rx, $ry, $x, $y, $text, $selected)
    {
        $font = "Courier New";
        $link = "https://localhost/svg/" . $text;
        $svg = "<ellipse cx=\"" . $x . "\" cy=\"" . $y . "\" rx=\"" . $rx . "\" ry=\"" . $ry . 
                "\" fill=\"rgb(255, 255, 255)\" stroke=\"rgb(0, 0, 0)\" pointer-events=\"all\" />";
       # $svg .= "<text x=\"" . $x . "\" y=\"" . $y . "\" fill=\"rgb(0, 0, 0)\" font-family=\"" . $font . "\" font-size=\"12px\" text-anchor=\"middle\">" . $text . "</text>";
       if ($selected)
           $svg .= "<foreignObject pointer-events=\"none\" width=\"100%\" height=\"100%\" style=\"overflow: visible; text-align: left;\">
            <div style=\"display: flex; align-items: unsafe center; justify-content: unsafe center; width: 118px; height: 1px; padding-top: " . $y . "px; margin-left: " . $x - 57 . "px;\">
              <div data-drawio-colors=\"color: rgb(0, 0, 0); \" style=\"box-sizing: border-box; font-size: 0px; text-align: center;\">
                <div style=\"display: inline-block; font-size: 12px; font-family: " . $font . "; color: rgb(0, 0, 0); line-height: 1.2; pointer-events: all; white-space: normal; overflow-wrap: normal;\">
                  <font face=\"" . $font . "\">
                    <u>
                      <a href=\"https://localhost/svg\" target=\"_top\">" . $text . "</a>
                    </u>
                  </font>
                </div>
              </div>
            </div>
          </foreignObject>";
       else
           $svg .= "<a href=\"" . $link . "\" target=\"_top\">" . "<text x=\"" . $x . "\" y=\"" . $y . "\" fill=\"rgb(0, 0, 0)\" font-family=\"" . $font . "\" font-size=\"12px\" text-anchor=\"middle\">" . $text . "</text>" . "</a>";

        return $svg;   
    }
}
