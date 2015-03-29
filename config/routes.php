<?php

$routes->get('/rekisterointi', function() {
    HakutarkoitusController::tarkoitukset();
});

$routes->get('/', function() {
    HelloWorldController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/kirjautumissivu', function() {
    HelloWorldController::login();
});

$routes->get('/rekisterointiMalli', function() {
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

$routes->get('/kayttajienListaukset', function() {
    KayttajaController::index();
});

$routes->post('/kayttaja', function() {
    KayttajaController::store();
});

$routes->get('/rekisterointi', function() {
    KayttajaController::luo();
});

$routes->get('/julkinenProfiilisivu/:id', function($id) {
    KayttajaController::nayta($id);
});

$routes->get('/julkinenProfiilisivu/:id', function($id) {
    HakutarkoitusController::naytaTarkoitus($id);
});
