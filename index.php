<?php

require 'vendor/autoload.php';

use App\Router;
use App\Controllers\Messages;
use App\Controllers\Bot;



new Router([
  'messages/:id' => Messages::class,
  'bot' => Bot::class
  
]);