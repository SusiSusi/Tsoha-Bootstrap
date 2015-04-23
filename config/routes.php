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

$routes->get('/omaProfiilisivu/:id', function($id) {
    KayttajaController::naytaOmaSivu($id);
});

$routes->get('/:id/muokkaa', function($id) {
    KayttajaController::edit($id);
});

$routes->post('/:id/muokkaa', function($id) {
    KayttajaController::update($id);
});

$routes->get('/:id/poistaTunnus', function($id) {
    KayttajaController::poistaTunnus($id);
});

$routes->post('/:id/poistaTunnus', function($id) {
    KayttajaController::poista($id);
});

$routes->get('/saapuneetViestit/:id', function($id) {
    ViestitController::kaikkiViestit($id);
});

$routes->get('/lahetetytViestit/:id', function($id) {
    ViestitController::kaikkiLahetetytViestit($id);
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

$routes->post('/saapuneetViestit/:id', function($id) {
    ViestitController::poistaViesti($id);
});

$routes->post('/saapuneetViestit/:id', function($id) {
    ViestitController::asetaLukemattomaksi($id);
});

$routes->post('/lahetetytViestit/:id', function($id) {
    ViestitController::poistaViesti($id);
});

$routes->post('/logout', function() {
    UserController::logout();
});

