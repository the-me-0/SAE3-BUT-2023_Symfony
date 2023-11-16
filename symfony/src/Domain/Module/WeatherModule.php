<?php

namespace App\Domain\Module;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Intl\IntlBundle\IntlBundle;

use Symfony\Component\HttpClient\Exception\TransportException;

class WeatherModule
{
    private $httpClient;
    private $weatherLink = "https://api.openweathermap.org/data/2.5/weather?q=La%20Rochelle,fr";
    private $gazLink = "http://api.openweathermap.org/data/2.5/air_pollution?lat=46.1667&lon=-1.15";

    public function getWeatherData() {
        /* Create the http client */
        $this->httpClient = HttpClient::create();

        /* Complete the links with the api key */
        $weather_link = $this->weatherLink . "&appid=" . $_ENV['WEATHER_API_KEY'];
        $gaz_link = $this->gazLink . "&appid=" . $_ENV['WEATHER_API_KEY'];

        /* Get the datafrom the api */
        $weatherResponse = $this->httpClient->request('GET', $weather_link);
        $gazResponse = $this->httpClient->request('GET', $gaz_link);
            
        /* Get the status code */
        $weatherCode = $weatherResponse->getStatusCode();
        $gazCode = $gazResponse->getStatusCode();

        /* Decode the data */
        $gazData = json_decode($gazResponse->getContent(), true);
        $weatherData = json_decode($weatherResponse->getContent(), true);

        /* Convert the temperature from Kelvin to Celsius */
        $weatherData['main']['temp'] = round($weatherData['main']['temp']-273.15);
        $weatherData['main']['temp_min'] = round($weatherData['main']['temp_min']-273.15);
        $weatherData['main']['temp_max'] = round($weatherData['main']['temp_max']-273.15);

        /* Get the date */
        $date = date('l j F Y');

        /* Return the data */
        return [
            'statusCode' => $weatherCode,
            'weatherData' => $weatherData,
            'gazData' => $gazData,
            'date' => $date
        ];
    }


    public function getTemp()
    {
        $weatherData = $this->getWeatherData();
        return $weatherData['weatherData']['main']['temp'];
    }

    public function getHum()
    {
        $weatherData = $this->getWeatherData();
        return $weatherData['weatherData']['main']['humidity'];
    }

    public function getCo2()
    {
        $weatherData = $this->getWeatherData();
        return $weatherData['gazData']['list'][0]['components']['co'];
    }

}