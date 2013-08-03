<?php

/**
 * Movie Analyser
 * Given a list of films, looks them up on TheMovieDatabase to tell you
 * about what kind of films you like to watch.
 *
 * @author Daniel G Wood <https://github.com/danielgwood>
 */

// TMDb API class, courtesy of Jonas De Smet
// https://github.com/glamorous/TMDb-PHP-API
require 'lib/TMDb.php';

// API configuration
require 'apikey.php';

// Libs
require 'lib/MovieAnalyser.php';
require 'lib/Helper.php';

if(isset($_GET['title']) && !empty($_GET['title'])) {
    // Get some uniqueish ID to store the data under
    $setId = preg_replace('/[^a-z0-9.]+/i', '', (isset($_GET['setId']) ? $_GET['setId'] : ''));
    if(empty($setId)) {
        $setId = uniqid();
    }

    // Make sure client-side knows set ID
    $response = array(
        'setId' => $setId
    );

    // Attempt to strip problem characters whilst remaining as flexible as possible
    // TODO this could probably use some more thought
    $title = preg_replace('/[^-a-z0-9!$()\'\;\:\?\.\,\s]+/i', '', $_GET['title']);

    // Look up the movie
    $details = MovieAnalyser::getDetails($title);
    if($details) {
        // Found! Save it
        MovieAnalyser::addToSet($setId, $details);
        $response['ok'] = 1;
        $response['title']  = Helper::escape($details['title']);
        $response['poster']  = $details['poster'];

    } else {
        // Doh
        $response['error'] = 'Title &lsquo;' . $title . '&rsquo; not recognised.';
    }

    header('Content-type: application/json');
    echo json_encode($response);

} else {
    header('Content-type: application/json');
    echo json_encode(array('error' => 'You must provide a film title!'));
}