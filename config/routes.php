<?php

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/kirjautumissivu', function() {
    HelloWorldController::login();
});

$routes->get('/rekisterointi', function() {
    HelloWorldController::rekisterointi();
});

$routes->get('/etusivu', function() {
    HelloWorldController::etusivu();
});

$routes->get('/kayttajienListaus', function() {
    HelloWorldController::listaus();
});

$routes->get('/muokkaaProfiilia', function() {
    HelloWorldController::muokkaa();
});
