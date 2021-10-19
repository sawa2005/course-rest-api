<?php

class Courses {
    // Egenskaper
    public $code;
    public $name;
    public $progression;
    public $syllabus;

    // Sparar databasanslutningen för användning
    function __construct($db) {
        $this->db = $db;
    }
        
    // Metoder
    public function read() {
        // SQL-fråga för att läsa ut allt (*) från tabellen kurser
        $sql = "SELECT * FROM kurser;";
        $result = mysqli_query($this->db, $sql);
            
        // Loopa genom alla rader
        while($row = mysqli_fetch_assoc($result)) {
            $arrResult[] = $row;
        }

        // Returnera resultatet
        return $arrResult;     
    }

    public function readOne($code) {
        // SQL-fråga för att läsa ut en kurs med en specifik kod från tabellen kurser
        $sql = "SELECT * FROM kurser WHERE code = '$code';";
        $result = mysqli_query($this->db, $sql);

        while($row = mysqli_fetch_assoc($result)) {
            $arrResult[] = $row;
        }

        // Om resultatet inte är tomt
        if(isset($arrResult)) {
            return $arrResult;
        }
    }

    public function create() {
        // SQL-fråga för att skapa en kurs med specifika värden
        $sql = "INSERT INTO kurser(code, name, progression, syllabus) VALUES('$this->code', '$this->name', '$this->progression', '$this->syllabus');";
        $result = mysqli_query($this->db, $sql);

        return $result;
    }

    public function update($code) {
        // SQL-fråga för att uppdatera en specifik kurs
        $sql = "UPDATE kurser SET name = '$this->name', progression = '$this->progression', syllabus = '$this->syllabus' WHERE code = '$code'";
        $result = mysqli_query($this->db, $sql);

        return $result;
    }

    public function delete($code) {
        // SQL-fråga för att ta bort en specifik kurs
        $sql = "DELETE FROM kurser WHERE code='$code'";
        $result = mysqli_query($this->db, $sql);

        return $result;
    }
}