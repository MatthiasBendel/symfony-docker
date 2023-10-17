<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;

class SvgController extends AbstractController
{
    #[Route('/svg', name: 'app_svg')]
    public function index(): Response
    {
        $directory = '';

        // the template path is the relative file path from `templates/`
        return $this->render('mysite.html.twig', [
            'directory' => $directory,
        ]);
    }

    #[Route('/vote/{vote}/{item}', name: 'app_vot')]
    public function vote($vote, $item):  JsonResponse
    {
        if (in_array($vote, ['accept', 'tolerate', 'ignore', 'decline', 'select'])) {
            $file = 'test.json';
            $str = file_get_contents($file);
            $json = json_decode($str, true); 
            $time = time();
            $json[$time] = array("vote" => $vote, "item" => $item);
            file_put_contents($file, json_encode($json));
            
            return $this->json([
                'message' => 'Voted '. $vote .' for ' . $item . ' (at ' . date('m/d/Y h:i:s a', $time) . '!'
            ]);
        }
        return $this->json([
            'error' => 'Invalid vote: ' . $vote
        ]);
    }

    #[Route('/json', name: 'app_json')]
    public function getJson(): JsonResponse
    {
        $str = file_get_contents('http://example.com/example.json/');
        $json = json_decode($str, true); 
        return $this->json([
            'message' => 'Welcome to your json controller!',
            'path' => 'src/Controller/SvgController.php',
            'content' => $json
        ]);
    }
}
