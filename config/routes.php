<?php

$routes->get('/kirjautuminen', function() {
    UserController::login();
});

$routes->post('/kirjautuminen', function() {
    UserController::handle_login();
});

$routes->get('/rekisterointi', function() {
    HakutarkoitusController::tarkoitukset();
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

$routes->get('/', function() {
    KayttajaController::etusivu();
});

$routes->get('/kirjautuneenEtusivu', function() {
    KayttajaController::kirjautuneenEtusivu();
});

$routes->get('/kayttajienListaukset', function() {
    KayttajaController::index();
});

$routes->get('/haku', function() {
    KayttajaController::haku();
});

//$routes->get('/rekisterointi', function() {
//    KayttajaController::tarkoitukset();
//});

$routes->post('/kayttaja', function() {
    KayttajaController::store();
});

$routes->get('/rekisterointi', function() {
    KayttajaController::luo();
});

$routes->get('/julkinenProfiilisivu/:id', function($id) {
    KayttajaController::nayta($id);
});

$routes->get('/omaProfiilisivu', function() {
    KayttajaController::naytaOmaSivu();
});

$routes->get('/muokkaa', function() {
    KayttajaController::edit();
});

$routes->post('/muokkaa', function() {
    KayttajaController::update();
});

$routes->get('/vaihdaSalasana', function() {
    KayttajaController::muutaSalasana();
});

$routes->post('/vaihdaSalasana', function() {
    KayttajaController::salasananMuutos();
});

$routes->get('/poistaTunnus', function() {
    KayttajaController::poistaTunnus();
});

$routes->post('/poistaTunnus', function() {
    KayttajaController::poista();
});

$routes->get('/saapuneetViestit', function() {
    ViestitController::kaikkiViestit();
});

$routes->get('/lahetetytViestit', function() {
    ViestitController::kaikkiLahetetytViestit();
});

$routes->post('/viestinSisalto/:id', function() {
    ViestitController::lahetaViesti();
});

$routes->post('/julkinenProfiilisivu/:id', function() {
    ViestitController::lahetaViesti();
});

$routes->get('/viestinSisalto/:id', function($id) {
    ViestitController::lueViesti($id);
});

$routes->post('/saapuneetViestit/:id/lukematon', function($id) {
    ViestitController::asetaLukemattomaksi($id);
});

$routes->post('/saapuneetViestit/:id', function($id) {
    ViestitController::poistaViesti($id);
});

$routes->post('/lahetetytViestit/:id', function($id) {
    ViestitController::poistaViesti($id);
});

$routes->post('/logout', function() {
    UserController::logout();
});

