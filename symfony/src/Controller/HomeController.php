<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Sensor;
use App\Entity\Room;
use App\Entity\Facility;
use App\Form\FacilityType;
use App\Domain\Module\WeatherModule;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name:'app_home')]
    public function home(): Response
    {

        /* use the module to make the API calls */
        $weatherModule = new WeatherModule();
        $data = $weatherModule->getWeatherData();
        
        // Give data to the html to display it
        return $this->render('home/index2.html.twig', [
            'statusCode' => $data['statusCode'],
            'data' => $data['weatherData'],
            'date' => $data['date'],
            'gaz' => $data['gazData']
        ]);
    }
    
    
    #[Route('/liste_batiments', name:'facility_list')]
    public function facility_list(ManagerRegistry $registry): Response
    {
        // Get all the facilities and order them by id ascendant
        $facilities = $registry->getRepository(Facility::class)->findBy([], ['id' => 'ASC']);
        
        // Give data to the html to display it
        return $this->render('home/facility_list.html.twig', [
            'facilities' => $facilities,
        ]);
    }

}
