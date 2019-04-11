<?php

require 'vendor/autoload.php';
$client = new \GuzzleHttp\Client();

function getUnicorn($id, $client){
  $response = $client->request('GET', "http://unicorns.idioti.se/$id", ['headers' => [
    'Accept' => 'application/json']])->getBody();
  $unicorn = json_decode($response, true);
  return $unicorn;
}

function getUnicorns($client){
  $response = $client->request('GET', "http://unicorns.idioti.se/", ['headers' => [ 'Accept' => 'application/json']])->getBody();
  $unicorns = json_decode($response, true);
  return $unicorns;
}

$unicornId = $_GET["id"];
if ($unicornId) {
  $unicorn = getUnicorn($unicornId, $client);
} else {
  $unicorns = getUnicorns($client);
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Enhörningar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body class="container">
  <h1>Enhörningar</h1>
  <hr>
  <form action="/" method="get">
    <label for="unicorn-id">Skriv in enhörnings id: </label>
    <input type="text" id="unicorn-id" name="id" class="form-control">
    <button type="submit" class="btn btn-primary">Visa enhörning</button>
    <button type="submit" class="btn btn-outline-primary"><a class="link" href="/">Visa alla enhörningar</a></button>
  </form>
  <section>
    <?php
        if ($unicorn) {
          $name = $unicorn["name"];
          $description = $unicorn["description"];
          $dateTime = $unicorn["spottedWhen"];
          $date = substr($dateTime, 0, 10);
          $image = $unicorn["image"];
          $reportedBy = $unicorn["reportedBy"];
          echo "<div class=\"card mb-3\" style=\"max-width: 100%\">
                  <div class=\"row no-gutters\">
                    <div class=\"col-md-4\">
                      <img src=\"$image\" class=\"card-img\" alt=\"...\">
                    </div>
                    <div class=\"col-md-8\">
                      <div class=\"card-body\">
                        <h5 class=\"card-title\">$name</h5>
                        <small class=\"text-muted\">$date</small>
                        <p class=\"card-text\">$description</p>
                        <p class=\"card-text\"><small class=\"text-muted\">Rapporterad av: $reportedBy</small></p>
                      </div>
                    </div>
                  </div>
                </div>";
        } else {
        echo "<h5>Alla Enhörningar</h5>";
        $i = 0;
        foreach($unicorns as $unicorn){
          $i++;
          $name = $unicorn["name"];
          if (!$name == "") {
            $id = $unicorn["id"];
            $details = $unicorn["details"];
            echo "<div class=\"card mb-3\" style=\"max-width: 100%\">
                  <div class=\"row no-gutters\">
                    <div class=\"col-md-12\">
                        <div class=\"card-body unicorn-card\">
                            <h5 class=\"card-title\">$i: $name</h5>
                            <button class=\"btn btn-outline-dark\"><a class=\"link\" href=\"/?id=$id\">Läs mer</a></button>
                    
                      </div>
                    </div>
                  </div>
                </div>";
            }
          }
        }
        
      ?>
    </sectio>
</body>

</html>