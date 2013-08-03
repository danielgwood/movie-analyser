movie-analyser
==============

Takes a list of films and collates some stats, such as popular genres, actors, directors, eras and ratings and displays it in a neat infographicy style.

This extends on an earlier experiment, https://github.com/danielgwood/movie-analyser-script.

Demo
----
Try it out here: http://danielgwood.com/lab/movies

Installing
----------
1. Clone repo
2. Get an API key for TMDB: http://docs.themoviedb.apiary.io/
3. Add "apikey.php" at repo root, and define the `API_KEY` constant in there
4. At present, the storage mechanism is serialised text files. Make sure the path defined in `\lib\MovieAnalyser::$SAVE_DIR` is writable by PHP

Contributing
------------
I'm particularly interested to see more data/statistics derived (there's a whole load more data in TMDB which I'm not even accessing). Please forgive any code wibbles - I left this project unfinished for a while, and couldn't actually remember what I was doing when I came back to it.

See the official TODO list: https://github.com/danielgwood/movie-analyser/blob/master/TODO.md

License
-------
Go nuts, but please keep the attributions for TMDB, https://github.com/glamorous, and Nikolay Kuchkarov on display. I'd also appreciate it if you let me know what you do!