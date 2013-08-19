<?php

/**
 * Movie Analyser library class.
 *
 * @author Daniel G Wood <https://github.com/danielgwood>
 */
class MovieAnalyser
{
    /**
     * Write data here (relative to repo root, no trailing slash)
     */
    public static $SAVE_DIR = 'data';

    /**
     * Get details of a movie.
     *
     * @param  string $title Title of a film, such as "True Grit (2010)"
     * @return array|false
     */
    public static function getDetails($title)
    {
        $tmdb = new TMDb(\API_KEY);

        $searchResult = $tmdb->searchMovie($title);
        if($searchResult && isset($searchResult['results']) && count($searchResult['results']) > 0) {
            // Found a film, yeah!
            $movieId = $searchResult['results'][0]["id"];

            // Get basic details
            $movieDetails = $tmdb->getMovie($movieId);
            $movie = array(
                'id' => $movieId,
                'title' => $movieDetails['title'],
                'date' => strtotime($movieDetails['release_date']),
                'runtime' => (int)$movieDetails['runtime'],
                'rating' => $movieDetails['vote_average'],
                'ratings' => $movieDetails['vote_count'],
                'genres' => array(),
                'poster' => $tmdb->getImageUrl($movieDetails['poster_path'], TMDb::IMAGE_POSTER, 'w92'),
                'cast' => array()
            );

            if(isset($movieDetails['genres'])) {
                foreach($movieDetails['genres'] as $genre) {
                    $movie['genres'][] = $genre['name'];
                }
            }

            // Get cast & crew details
            $moviePeople = $tmdb->getMovieCast($movieId);
            if(isset($moviePeople['crew'])) {
                foreach($moviePeople['crew'] as $crewMember) {
                    if($crewMember['job'] === 'Director') {
                        $movie['director'] = $crewMember['name'];
                    }
                }
            }

            if(isset($moviePeople['cast'])) {
                foreach($moviePeople['cast'] as $castMember) {
                    $movie['cast'][] = $castMember['name'];
                }
            }

            return $movie;
        }

        return false;
    }

    /**
     * Add details of a movie to a set.
     *
     * @param string $id   ID of a set
     * @param array $movie Details of a movie
     */
    public static function addToSet($id, array $movie)
    {
        // Load existing data
        $set = self::getSet($id);

        // Collate the information we want
        $set['totalMovies']++;
        $set['totalRunningTime'] += $movie['runtime'];
        $set['releaseDates'][] = $movie['date'];
        $set['ratings'][] = $movie['rating'];

        if(!$set['oldestFilm'] || $set['oldestFilm']['date'] > $movie['date']) {
            $set['oldestFilm'] = array(
                'date' => $movie['date'],
                'rating' => $movie['rating'],
                'title' => $movie['title'],
                'poster' => $movie['poster']
            );
        }
        if(!$set['newestFilm'] || $set['newestFilm']['date'] < $movie['date']) {
            $set['newestFilm'] = array(
                'date' => $movie['date'],
                'rating' => $movie['rating'],
                'title' => $movie['title'],
                'poster' => $movie['poster']
            );
        }

        if($movie['ratings'] > 0) {
            if(!$set['bestFilm'] || $set['bestFilm']['rating'] < $movie['rating']) {
                $set['bestFilm'] = array(
                    'date' => $movie['date'],
                    'rating' => $movie['rating'],
                    'title' => $movie['title'],
                    'poster' => $movie['poster']
                );
            }
            if(!$set['worstFilm'] || $set['worstFilm']['rating'] > $movie['rating']) {
                $set['worstFilm'] = array(
                    'date' => $movie['date'],
                    'rating' => $movie['rating'],
                    'title' => $movie['title'],
                    'poster' => $movie['poster']
                );
            }
        }

        foreach($movie['genres'] as $genre) {
            if(!isset($set['genres'][$genre])) {
                $set['genres'][$genre] = 0;
            }

            $set['genres'][$genre]++;
        }

        if(isset($movie['director'])) {
            if(!isset($set['directors'][$movie['director']])) {
                $set['directors'][$movie['director']] = 0;
            }
            $set['directors'][$movie['director']]++;
        }

        foreach($movie['cast'] as $castMember) {
            if(!isset($set['cast'][$castMember])) {
                $set['cast'][$castMember] = 0;
            }
            $set['cast'][$castMember]++;
        }

        // Save the data back
        self::saveSet($id, $set);
    }

    /**
     * Load all the informatiom in a set.
     *
     * @param  string $id ID of a set
     * @return array
     */
    public static function getSet($id)
    {
        // Load all the existing data
        $filename = self::getDir() . $id;
        if(file_exists($filename)) {
            $data = @unserialize(file_get_contents($filename));
            if($data) {
                return $data;
            }
        }

        // Create a new set
        return array(
            'totalMovies' => 0,
            'totalRunningTime' => 0,
            'oldestFilm' => array(),
            'newestFilm' => array(),
            'bestFilm' => array(),
            'worstFilm' => array(),
            'genres' => array(),
            'releaseDates' => array(),
            'ratings' => array(),
            'directors' => array(),
            'cast' => array()
        );
    }

    /**
     * Persist a set back to the store.
     *
     * @param  string $id   ID of a set
     * @param  array  $data
     * @return bool
     */
    public static function saveSet($id, $data)
    {
        file_put_contents(self::getDir() . $id, serialize($data));
    }

    /**
     * Get the file save location.
     *
     * @return string
     */
    private static function getDir()
    {
        return dirname(__DIR__) . \DIRECTORY_SEPARATOR . self::$SAVE_DIR . \DIRECTORY_SEPARATOR;
    }
}