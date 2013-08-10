<?php

/**
 * Movie Analyser
 * Given a list of films, looks them up on TheMovieDatabase to tell you
 * about what kind of films you like to watch.
 *
 * @author Daniel G Wood <https://github.com/danielgwood>
 */

// Facebook PHP SDK
// https://github.com/facebook/facebook-php-sdk
require 'facebook/facebook.php';

// API configuration
require 'apikey.php';

// You must define Facebook API constants in apikey.php
if(!defined('FACEBOOK_API_ID') || !defined('FACEBOOK_API_SECRET')) {
    die("You must define Facebook API constants before using the Facebook integration. Get these from https://developers.facebook.com/");
}

// List of movie titles this user has marked "watched" on Facebook
$movies = array();

// Start up the Facebook API
$fb = new Facebook(array(
    'appId' => FACEBOOK_API_ID,
    'secret' => FACEBOOK_API_SECRET
));

$user = $fb->getUser();
if($user) {
    try {
        // Make the first request for movies
        getMovieData('/me/video.watches?fields=data');

        // Return all the movie titles found
        header('Content-type: application/json');
        echo json_encode(array('movies' => $movies));

    } catch (FacebookApiException $e) {
        // Doh!
        header('Content-type: application/json');
        echo json_encode(array('error' => 'Facebook API is unavailable right now.'));
    }

} else {
    // Need to first login to FB and get permission for the data
    $fbLoginUrl = $fb->getLoginUrl(array('scope' => 'user_actions.video'));
    echo '<a href="' . $fbLoginUrl . '">Login to Facebook</a>';
}




function getMovieData($url)
{
    global $fb, $movies;

    // Remove unnecessary bits from FB's paging URLs
    $url = str_replace('https://graph.facebook.com', '', $url);

    // Ask for data
    $response = $fb->api($url);

    if(isset($response['data']) && count($response['data']) > 0) {
        foreach($response['data'] as $item) {
            if($item['data']['movie']['type'] === 'video.movie') {
                // Yup, it's a movie
                $movies[] = $item['data']['movie']['title'];
            }
        }
    }

    // More to fetch?
    if(isset($response['paging']['next'])) {
        getMovieData($response['paging']['next']);
    }
}