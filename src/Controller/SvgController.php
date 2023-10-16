<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/json', name: 'app_json')]
    public function getJson(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your json controller!',
            'path' => 'src/Controller/SvgController.php',
        ]);
    }
}
