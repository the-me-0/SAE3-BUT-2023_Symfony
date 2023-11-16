<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Domain\Module\WeatherModule;

class WeatherController extends AbstractController
{
    #[Route('/meteo', name: 'app_weather')]
    public function index(): Response
    {
        /* use the module to make the API calls */
        $weatherModule = new WeatherModule();
        $data = $weatherModule->getWeatherData();

        return $this->render('/weather/index.html.twig', [
            'statusCode' => $data['statusCode'],
            'data' => $data['weatherData'],
            'date' => $data['date'],
            'gaz' => $data['gazData']
        ]);
    }


}