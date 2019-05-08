<?php

require 'vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('unicorns');
$log->pushHandler(new StreamHandler('visits.log', Logger::INFO));
$client = new \GuzzleHttp\Client();

function getUnicorn($id, $client)
{
  try {
    $response = $client->request('GET', "http://unicorns.idioti.se/$id", ['headers' => [
      'Accept' => 'application/json']])->getBody();
    $unicorn = json_decode($response, true);
    return $unicorn;
  }
  catch (GuzzleHttp\Exception\ClientException $error) {
    $response = $error->getResponse();
    $unicornNotFound = $response->getBody();
    echo $unicornNotFound;
  };
}

function getUnicorns($client)
{
  try {
    $response = $client->request('GET', "http://unicorns.idioti.se/", ['headers' => [ 'Accept' => 'application/json']])->getBody();
    $unicorns = json_decode($response, true);
    $validUnicorns = validateUnicorns($unicorns);
    return $validUnicorns;
  }
  catch (GuzzleHttp\Exception\ClientException $error) {
    $response = $error->getResponse();
    $unicornsNotFound = $response->getBody();
    echo $unicornsNotFound;
  };
}

function validateUnicorns($unicorns)
{
  $validUnicorns = [];
  foreach ($unicorns as $unicorn) {
    if ($unicorn["name"]) {
      array_push($validUnicorns, $unicorn);
    };
  }
  return $validUnicorns;
}

function renderData($client, $log)
{
  $unicornId = isset($_GET["id"]) ? $_GET["id"] : false;
  if ($unicornId) {
    $unicorn = getUnicorn($unicornId, $client);
    if ($unicorn == "") {
      $content = "<p>Fanns ingen enhörning med det Id:t, dubbelkolla i listan vilka enhörnings id:n som är tillgängliga</p>";
    } else{
      extract($unicorn);
      $content = "<div class=\"card mb-3\" style=\"max-width: 100%\">
                    <div class=\"row no-gutters\">
                      <div class=\"col-md-4\">
                        <img src=\"$image\" class=\"card-img\" alt=\"...\">
                      </div>
                      <div class=\"col-md-8\">
                        <div class=\"card-body\">
                          <h5 class=\"card-title\">$name</h5>
                          <small class=\"text-muted\">$spottedWhen</small>
                          <p class=\"card-text\">$description</p>
                          <p class=\"card-text\"><small class=\"text-muted\">Rapporterad av: $reportedBy</small></p>
                        </div>
                      </div>
                    </div>
                  </div>";
      $log->info("Requested info about: $name");
    }
    echo $content;
    } else {
      $log->info("Requested info about all unicorns");
      $unicorns = getUnicorns($client);
      foreach($unicorns as $unicorn){
        extract($unicorn);
        $content = "<div class=\"card mb-3\" style=\"max-width: 100%\">
                      <div class=\"row no-gutters\">
                        <div class=\"col-md-12\">
                          <div class=\"card-body unicorn-card\">
                            <h5 class=\"card-title\">$id: $name</h5>
                            <button class=\"btn btn-outline-dark\"><a class=\"link\" href=\"/?id=$id\">Läs mer</a></button>
                          </div>
                        </div>
                      </div>
                    </div>";
        echo $content;
      }  
    }
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
    <input type="number" id="unicorn-id" name="id" class="form-control">
    <button type="submit" id="show-unicorn" class="btn btn-primary">Visa enhörning</button>
    <button type="submit" class="btn btn-outline-primary"><a class="link" href="/">Visa alla enhörningar</a></button>
  </form>
  <section>
    <?php
       renderData($client, $log);
      ?>
  </section>
  <script src="script.js"></script>
</body>

</html>