<?php

require 'config/Database.php';
require 'classes/Courses.class.php';

/*Headers med inställningar för din REST webbtjänst*/

//Gör att webbtjänsten går att komma åt från alla domäner (asterisk * betyder alla)
header('Access-Control-Allow-Origin: *');

//Talar om att webbtjänsten skickar data i JSON-format
header('Content-Type: application/json');

//Vilka metoder som webbtjänsten accepterar, som standard tillåts bara GET.
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');

//Vilka headers som är tillåtna vid anrop från klient-sidan, kan bli problem med CORS (Cross-Origin Resource Sharing) utan denna.
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

//Läser in vilken metod som skickats och lagrar i en variabel
$method = $_SERVER['REQUEST_METHOD'];

//Om en parameter av code finns i urlen lagras det i en variabel
if(isset($_GET['code'])) {
    $code = $_GET['code'];
}

// Skapar en ny instans av databasklassen och kör funktionen för att ansluta till databasen
$database = new Database();
$db = $database->connect();

// Skapar instans av klassen för att skicka SQL-frågor till databasen
$courses = new Courses($db); // Databasanslutning skickas med som parameter

switch($method) {
    case 'GET':
        // Om en kod är skickad
        if (isset($code)) {
            // Kör funktionen för att läsa en kurs med en specifik kod
            $response = $courses->readOne($code);
        }
        // Om en kod inte är skickad
        else {
            // Kör funktionen för att läsa ut alla kurser
            $response = $courses->read();
        }

        // Kontrollerar om resultatet har några rader
        if ($response !== null) {
            // Skickar en "HTTP response status code"
            http_response_code(200); // OK - The request has succeeded
        }
        else {
            http_response_code(404); // Not found - The request has failed
            // Lagrar ett meddelande som sedan skickas tillbaka till anroparen
            $response = array("message" => "No course found");
        }
        break;
    case 'POST':
        // Läser in JSON-data skickad med anropet och omvandlar till ett objekt.
        $data = json_decode(file_get_contents("php://input"));

        // Skickar de medskickade egenskaperna till klassen och sparar dessa i klassens egenskaper
        $courses->code = $data->code;
        $courses->name = $data->name;
        $courses->progression = $data->progression;
        $courses->syllabus = $data->syllabus;

        // Kör funktionen för att skapa en ny kurs
        if ($courses->create()) {
            http_response_code(201); // Created
            $response = array("message" => "Course created");
        }
        else {
            http_response_code(503); // Server error
            $response = array("message" => "Course not created");
        }
        break;
    case 'PUT':
        // Om ingen kod är medskickat, skicka felmeddelande
        if(!isset($code)) {
            http_response_code(400); // Bad Request - The server could not understand the request due to invalid syntax.
            $response = array("message" => "No code was sent");  
        }
        // Om en kod är skickad
        else {
            $data = json_decode(file_get_contents("php://input"));

            $courses->name = $data->name;
            $courses->progression = $data->progression;
            $courses->syllabus = $data->syllabus;

            // Kör funktionen för att uppdatera en kurs
            if ($courses->update($code)) {
                http_response_code(200);
                $response = array("message" => "Course with code = $code is updated");
            }
            else {
                http_response_code(503);
                $response = array("message" => "Course not updated");
            }
        }
        break;
    case 'DELETE':
        if (!isset($code)) {
            http_response_code(400);
            $response = array("message" => "No code was sent");  
        } 
        else {
            // Kör funktionen för att ta bort en kurs
            if ($courses->delete($code)) {
                http_response_code(200);
                $response = array("message" => "Course with code = $code has been deleted");
            }
            else {
                http_response_code(503);
                $response = array("message" => "Course not deleted");
            }
        }
        break;        
}

// Skickar svar tillbaka till avsändaren
echo json_encode($response);
