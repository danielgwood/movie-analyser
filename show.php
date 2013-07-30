<?php

/**
 * Movie Analyser
 * Given a list of films, looks them up on TheMovieDatabase to tell you
 * about what kind of films you like to watch.
 *
 * @author Daniel G Wood <https://github.com/danielgwood>
 */

require 'lib/MovieAnalyser.php';
require 'lib/Helper.php';

$set = MovieAnalyser::getSet($_GET['id']);

// Simple numbers
$totalMovies = $set['totalMovies'];
$totalRunTime = $set['totalRunningTime'];

// Best/worst genres
arsort($set['genres']);
$favouriteGenre = key(array_slice($set['genres'], 0, 1));

$topGenres = array_slice($set['genres'], 0, 5);

$leastFavouriteGenres = false;
if(count($set['genres']) > 5) {
    asort($set['genres']);
    $leastFavouriteGenres = array_slice($set['genres'], 0, 5);
}

// Collate years
$decades = array();
$years = array();

$currentYear = date('Y', $set['oldestFilm']['date']);
$lastYear = date('Y', $set['newestFilm']['date']);
while($currentYear <= $lastYear) {
    $years[$currentYear] = 0;
    $currentYear++;
}

foreach($set['releaseDates'] as $stamp) {
    $year = date('Y', $stamp);
    $decade = floor($year/10)*10;

    $years[$year]++;

    if(!isset($decades[$decade])) {
        $decades[$decade] = 0;
    }
    $decades[$decade]++;
}
ksort($years);
arsort($decades);
$favouriteDecade = key($decades);

// Favourite directors
arsort($set['directors']);
$directorsToShow = array_slice($set['directors'], 0, 5);

// Favourite cast
arsort($set['cast']);
$castToShow = array_slice($set['cast'], 0, min(count($set['cast']), 10));

// Ratings
$averageRating = (array_sum($set['ratings']) / count($set['ratings']));

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />

        <title>My Movie Collection</title>

        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' />
        <link rel="stylesheet" href="assets/css/all.css" />
    </head>

    <body>
        <header>
            <h1>My Movie Collection</h1>
        </header>

        <section>
            <div class="grid_4 size">
                <h2>Viewing time</h2>

                <p>There are <span class="stat"><?php echo Helper::escape($totalMovies); ?> movies</span> in my collection, with a total running time of <span class="stat"><?php echo number_format($totalRunTime); ?></span> minutes.</p>
                <p>That&apos;s <span class="stat"><?php echo Helper::minsToDays($totalRunTime); ?> days</span> of back&ndash;to&ndash;back viewing!</p>
            </div>

            <div class="grid_8 genres">
                <h2><?php echo Helper::escape($favouriteGenre); ?> is my favourite genre</h2>


                <div id="genre-chart" class="grid_4 alpha"></div>

                <div class="grid_4 omega">
                    <?php if($leastFavouriteGenres): ?>
                    <h3>I'm not so keen on&hellip;</h3>
                        <ul>
                            <?php foreach($leastFavouriteGenres as $genre => $count): ?><li><?php echo Helper::escape($genre); ?></li><?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                    <p>My tastes aren&apos;t very diverse!</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section>
            <div class="grid_4 directors">
                <h2>Directors</h2>
                <?php if(count($directorsToShow) > 0): ?>
                <ol>
                <?php

                foreach($directorsToShow as $director => $numberOfFilms) {
                    echo '<li>' . Helper::escape($director) . ' (' . $numberOfFilms . ' films)</li>';
                }

                ?>
                </ol>
                <?php endif; ?>
            </div>

            <div class="grid_8 actors">
                <h2>Actresses/actors</h2>
                <?php if(count($castToShow) > 0): ?>
                <ol class="two-column">
                <?php

                foreach($castToShow as $actor => $numberOfFilms) {
                    echo '<li>' . Helper::escape($actor) . '</li>';
                }

                ?>
                </ol>
                <?php endif; ?>
            </div>
        </section>

        <section class="years">
            <h2>I really like the <?php echo $favouriteDecade; ?>s</h2>

            <div id="years-chart"></div>

            <div class="grid_6">
                <h3>Oldest film</h3>
                <p><?php echo Helper::escape($set['oldestFilm']['title']) . ' (' . date('Y', $set['oldestFilm']['date']) . ')'; ?></p>
            </div>

            <div class="grid_6">
                <h3>Newest film</h3>
                <p><?php echo Helper::escape($set['newestFilm']['title']) . ' (' . date('Y', $set['newestFilm']['date']) . ')'; ?></p>
            </div>
        </section>

        <section class="ratings">
            <h2>Average rating: <?php echo Helper::escape(round($averageRating, 1)); ?>/10</h2>

            <div class="movie">
                <img src="<?php echo $set['bestFilm']['poster']; ?>" class="movie-poster">
                <h3>Best film</h3>
                <h4><?php echo Helper::escape($set['bestFilm']['title']); ?></h4>
                <span class="movie-rating"><?php echo Helper::escape($set['bestFilm']['rating']); ?>/10</span>
            </div>

            <div class="movie">
                 <img src="<?php echo $set['worstFilm']['poster']; ?>" class="movie-poster">
                <h3>Worst film</h3>
                <h4><?php echo Helper::escape($set['worstFilm']['title']); ?></h4>
                <span class="movie-rating"><?php echo Helper::escape($set['worstFilm']['rating']); ?>/10</span>
            </div>
        </section>

        <footer>
            <p>Data courtesy of <a href="http://www.themoviedb.org/"><img src="assets/img/tmdb.png" alt="TheMovieDb.org" /></a>, generated using <a href="https://github.com/danielgwood/movie-analyser">movie-analyser</a>.</p>
            <p>Icons by <a href="http://dribbble.com/SAMURAY">Nikolay Kuchkarov</a>. Additional thanks to <a href="https://github.com/glamorous">Jonas De Smet</a>.</p>
        </footer>

        <script src="http://d3js.org/d3.v3.min.js"></script>
        <script>

        var genres = [
            <?php

            for($i = 0; $i < count($topGenres); $i++) {
                $isLast = $i == count($topGenres)-1;

                echo '{"label": "' . Helper::escape(key($topGenres)) . '", "value": ' . ((int)current($topGenres)) . '}' . ((!$isLast) ? ',' : '') . "\n";

                next($topGenres);
            }

            ?>
        ];

        var width = 300;
        var height = 170;
        var radius = Math.min(width, height) / 2;
        var color = d3.scale.category20();
        var labelPad = 3;

        var vis = d3.select("#genre-chart")
            .append("svg:svg")
            .data([genres])
            .attr("width", width)
            .attr("height", height)
            .append("svg:g")
            .attr("transform", "translate(" + (width / 2) + "," + radius + ")");

        var arc = d3.svg.arc()
            .outerRadius(radius)
            .innerRadius(radius/2);

        var pie = d3.layout.pie()
            .value(function(d) { return d.value; });

        var arcs = vis.selectAll("g.slice")
            .data(pie)
            .enter()
                .append("svg:g")
                .attr("class", "slice");

        arcs.append("svg:path")
                .attr("fill", function(d, i) { return color(i); } )
                .attr("d", arc);

        arcs.append("svg:text")
            .attr("transform", function(d) {
                d.innerRadius = radius/2;
                d.outerRadius = radius;
                return "translate(" + arc.centroid(d) + ")";
            })
            .attr('class', 'labeltext')
            .attr("text-anchor", "middle")
            .text(function(d, i) { return genres[i].label; })
            .attr("fill", "white")
            .attr("font-size", "12px")

        arcs.insert("rect", ".labeltext")
                .attr("width", function() { return d3.select(this.parentNode).select("text").node().getBBox().width+(labelPad*2); })
                .attr("height", function() { return d3.select(this.parentNode).select("text").node().getBBox().height+(labelPad*2); })
                .attr("x", function(d, i) {
                    return arc.centroid(d)[0] - this.getAttribute('width')/2;
                })
                .attr("y", function(d) {
                    return arc.centroid(d)[1] - this.getAttribute('height')/1.5;
                })
                .attr("fill", function(d) {
                    return "rgba(20, 20, 20, 0.8)";
                });








        var width = 940;
        var height = 200;
        var barPadding = 1;
        var bottomMargin = 40;
        var scale = 20;

        var dataset = [
            <?php

            for($i = 0; $i < count($years); $i++) {
                $isLast = $i == count($years)-1;

                echo '{"label": "' . Helper::escape(key($years)) . '", "value": ' . ((int)current($years)) . '}' . ((!$isLast) ? ',' : '') . "\n";

                next($years);
            }

            ?>
        ];

        var svg = d3.select("#years-chart")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height);

        svg.selectAll("rect")
            .data(dataset)
            .enter()
            .append("rect")
            .attr("x", function(d, i) {
                return i * (width / dataset.length);
            })
            .attr("y", function(d) {
                return (height - bottomMargin) - (d.value * scale);
            })
            .attr("width", width / dataset.length - barPadding)
            .attr("height", function(d) {
                return d.value * scale;
            })
            .attr("fill", function(d) {
                return "rgb(167, 34, 46)";
            });

        svg.selectAll("text")
            .data(dataset)
            .enter()
            .append("text")
            .text(function(d) {
                return d.label;
            })
            .attr("text-anchor", "middle")
            .attr('transform', 'rotate(-90)')
            .attr("x", function(d, i) {
                return -(height-(bottomMargin/2));
            })
            .attr("y", function(d, i) {
                return i * (width / dataset.length) + (width / dataset.length - barPadding) / 1.4;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "10px")
            .attr("fill", "white");

        svg.append("g");
        svg.select("g")
            .selectAll("text")
            .data(dataset)
            .enter()
            .append("text")
            .text(function(d) {
                return (d.value > 0) ? d.value : '';
            })
            .attr("text-anchor", "middle")
            .attr("x", function(d, i) {
                return i * (width / dataset.length) + (width / dataset.length - barPadding) / 2.2;
            })
            .attr("y", function(d, i) {
                return (height-(d.value * scale))-27;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("font-weight", "bold")
            .attr("fill", "rgb(25, 25, 25)");

        </script>
    </body>
</html>