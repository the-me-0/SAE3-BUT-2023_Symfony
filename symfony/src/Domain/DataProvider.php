<?php

namespace App\Domain;
use App\Entity\Room;
use App\Domain\Datum; 

use DateTime;

class DataProvider
{

    private Room $room;
    // private string $jsonFile;

    public function __construct()
    {
        /* get the content from the JSON file used as an API return until we have access to the API */
        // $this->jsonFile = file_get_contents(__DIR__ . '/../../resources/data.json');
    }

    /** @return DataProvider */
    public function setRoom(Room $room):self
    {
        $this->room = $room;
        return $this;
    }
    
    /* This function is a reference to  */
    /** @return Datum[] */
    public function provide($tag, $name, $limit, $date1, $date2):iterable {

        /* Decode the json file to read it's content as an array */
        // $json = json_decode($this->jsonFile, true);

        /* Call: API */
        $json = json_decode($this->callAPI($name, $tag, $limit, $date1, $date2), true);

        /* Safeguard */
        /* If $json is null, it means the application couldn't achieve the connection or the call failed in some way */
        if ($json == null) {
            return ["La requête à l'API a échouée. La connexion au réseau peut être en cause."];
        }

        /* Create an array to store the selected data */
        $selected_data = array();

        /* Loop through the json array to select the data that match the name and tag parameters */
        foreach ($json as $datum) {

            /* This exclusion is used to limit the number of data returned */
            if($limit != null && count($selected_data) >= $limit) {
                break;
            }

            $validDatum = true;

            if($name != '' && $name != $datum['nom']) {
                $validDatum = false;
            }
            if($tag != null && $tag != $datum['tag']) {
                $validDatum = false;
            }
            if($validDatum) {
                $datumObject = $this->arrayToDatum($datum);

                /* Safeguard */
                if($datumObject->getValue() == -1) 
                    $selected_data[] = 'La valeur du capteur de la salle ' . $datumObject->getRoom()->getName() . ' semble poser problème.';
                else if($datumObject->getDate() == 'bad_format')
                    $selected_data[] = 'La lecture de la date semble poser problème sur le capteur de la salle ' . $datumObject->getRoom()->getName();
                else
                    $selected_data[] = $datumObject;
            }
        }

        /* Return the selected data */
        return $selected_data;
    }

    /* This function is used to convert an array to a Datum object */
    /** @return Datum */
    private function arrayToDatum($datum) {

        /* Change the date to the right format */
        $datum['dateCapture'] = DateTime::createFromFormat('Y-m-d H:i:s', $datum['dateCapture']);

        /* Safeguard */
        /* If the date format isn't valid, createFromFormat has returned false. This creates an error
            that we can prevent : */
        if ($datum['dateCapture'] === false) {
            $datum['dateCapture'] = 'bad_format';
        } else {
            $datum['dateCapture'] = $datum['dateCapture']->format('d-m-Y H:i:s');
        }

        /* Safeguard */
        /* If the value isn't of type float, it means it's a string. We don't want to display strings
            on the graph, so we set the value to 0. */
        $convertedFloat = floatval($datum['valeur']);
        if ($convertedFloat == 0 && $datum['valeur'] != '0') {
            $datum['valeur'] = -1;
        } else {
            $datum['valeur'] = $convertedFloat;
        }

        /* Create a new Datum object */
        $datumObject = new Datum(
            $this->room,
            $datum['dateCapture'],
            $datum['nom'],
            $datum['description'],
            $datum['valeur']
        );

        /* Return the Datum object */
        return $datumObject;
    }

    /* This function is used to call the API */
    /* Built but not used yet, waiting for the API to be ready */
    private function callAPI($name, $tag, $limit, $date1, $date2)
    {
        // create & initialize a curl session 
        $curl = curl_init();

        if ($limit!=0 && ($date1 == '' || $date2 == '')) {
            // set our url with curl_setopt()
            curl_setopt($curl, CURLOPT_URL, "https://sae34.k8s.iut-larochelle.fr/api/captures/last");
        } else if ($limit==0 && ($date1 == '' || $date2 == '')) {
            curl_setopt($curl, CURLOPT_URL, "https://sae34.k8s.iut-larochelle.fr/api/captures");
        } else {
            curl_setopt($curl, CURLOPT_URL, "https://sae34.k8s.iut-larochelle.fr/api/captures/interval");
        }

        // complete the request with name, tag and limit parameters if they are not null
        if($name != '') {
            curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "?name=" . $name);
        }
        if($tag != 0) {
            if ($name == '')
                curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "?tag=" . $tag);
            else
                curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "&tag=" . $tag);
        }
        if($limit != 0) {
            if ($name == '' && $tag == 0)
                curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "?limit=" . $limit);
            else
                curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "&limit=" . $limit);
        }
        if($date1 != '' && $date2 != '') {
            if ($name == '' && $tag == 0 && $limit == 0)
                curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "?date1=" . $date1 . "&date2=" . $date2);
            else
                curl_setopt($curl, CURLOPT_URL, curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "&date1=" . $date1 . "&date2=" . $date2);
        }

        // set the request method to GET with curl_setopt()
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

        // set the dbname header parameter with environment variable depending on the tag provided
        $dbname = $this->tagToTeam($tag);
        if ($dbname == null) {
            $dbname = "x2eq1";
        }

        // set custom headers with curl_setopt()
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'accept: application/json',
            'dbname: sae34bd' . $dbname,
            'username: x2eq1',
            'userpass: 2dxc3S45jHFUXA68',
         ));

        // return the transfer as a string, instead of outputting it out directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        // curl_exec() executes the started curl session and $output contains the output string 
        $output = curl_exec($curl);

        // close curl resource to free up system resources and (deletes the variable made by curl_init) 
        curl_close($curl);

        return $output;
    }

    /* This function is used to convert a tag to a team name */
    private function tagToTeam($tag) {
        switch ($tag) {
            case 1:
                return "x1eq1";
                break;
            case 2:
                return "x1eq2";
                break;
            case 3:
                return "x1eq3";
                break;
            case 4:
                return "x2eq1";
                break;
            case 5:
                return "x2eq2";
                break;
            case 6:
                return "x2eq3";
                break;
            case 7:
                return "y1eq1";
                break;
            case 8:
                return "y1eq2";
                break;
            case 9:
                return "y1eq3";
                break;
            case 10:
                return "y2eq1";
                break;
            case 11:
                return "y2eq2";
                break;
            case 12:
                return "y2eq3";
                break;
            case 13:
                return "z1eq1";
                break;
            case 14:
                return "z1eq2";
                break;
            case 15:
                return "z1eq3";
                break;
            default:
                return null;
                break;
        }
    }
}
