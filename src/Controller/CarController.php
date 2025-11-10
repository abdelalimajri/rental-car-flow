<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CarController extends AbstractController
{
    #[Route('/cars', name: 'app_car_index')]
    public function index(): Response
    {
        return $this->render('car/index.html.twig');
    }
}

