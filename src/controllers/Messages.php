<?php

namespace App\Controllers;

use App\Models\SqlConnect;
use PDO;
use PDOException;

class Messages extends SqlConnect {
    protected array $params;
    protected string $reqMethod;

    public function __construct($params) {
        parent::__construct();
        $this->params = $params;
        $this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);

        $this->run();
    }


    protected function getMessages($id = null) {
    
      if ($id !== null) {
          $conv = 'bot'.$id;
          try {
              $query = "SELECT * FROM $conv";
              $statement = $this->db->prepare($query);
              $statement->execute();
  
              $messages = $statement->fetchAll(PDO::FETCH_ASSOC);
  
              return $messages;
          } catch (PDOException $e) {
            
              echo "Erreur PDO : " . $e->getMessage();
          }
      } else {
         
          return ["message" => "Aucun ID n'a été fourni"];
      }
  }
  

  protected function postMessages($id = null) {

       

    if ($id !== null) {
        $conv = 'bot'.$id;
        $data = json_decode(file_get_contents('php://input'), true);

       
        $message = $data['message'];
        $heure = $data['heure'];
        $type = $data['type'];

        if (isset($data['url'])) {
          $url = $data['url'];
        } else {
          $url = '';
        }

      
        try {
            $query = "INSERT INTO $conv (message, heure, type , image) VALUES (:message, :heure, :type, :image)";
            $statement = $this->db->prepare($query);
             $statement->bindParam(':message', $message);
            $statement->bindParam(':heure', $heure);
            $statement->bindParam(':type', $type);
            $statement->bindParam(':image', $url);
            $statement->execute();
            return ["success" => true, "message" => "Message inséré avec succès"];
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return ["success" => false, "message" => "Erreur lors de l'insertion du message"];
        }
    } else {
        return ["success" => false, "message" => "Aucun ID n'a été fourni"];
    }
}  

  

protected function cors() {

  if (isset($_SERVER['HTTP_ORIGIN'])) {
      
      header("Access-Control-Allow-Origin: *");
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Max-Age: 86400');  
  }

  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

      exit(0);
  }

 
}

protected function header() {
  header('Access-Control-Allow-Origin: *');
  header('Content-type: application/json; charset=utf-8');
  header("Access-Control-Allow-Headers: X-Requested-With");

}


    protected function ifMethodExist() {
        $method = $this->reqMethod.'Messages';

        if (method_exists($this, $method)) {
            echo json_encode($this->$method($this->params['id']));
            return;
        }

        header('HTTP/1.0 404 Not Found');
        echo json_encode([
            'code' => '404',
            'message' => 'Not Found'
        ]);
        return;
    }

    protected function run() {
      $this->cors();
        $this->header();
        $this->ifMethodExist();
    }
}
