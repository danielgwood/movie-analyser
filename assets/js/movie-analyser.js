(function($) {
    var errorTimeout = null;
    var searchTimeout = null;
    var titles = [];
    var counter = 0;
    var setId = null;

    function showError(message)
    {
        window.clearTimeout(errorTimeout);
        if($('#titles').length > 0) {
            $('#titles').before('<p class="error">' + message + '</p>');

        } else {
            $('#indicator').after('<p class="error">' + message + '</p>');
        }
        errorTimeout = window.setTimeout("$('.error').fadeOut()", 5000);
    }

    function showResults()
    {
        $('#indicator').html('Compiling results&hellip;');

        setTimeout(function() {
            window.location.href = 'show.php?id=' + setId;
        }, 800);
    }

    function searchTitle(film)
    {
        $('#indicator').html('Looking for &lsquo;' + film + '&rsquo;, title ' + (counter+1) + ' of ' + titles.length + '&hellip;');

        var query = {
            title: film
        };

        if(setId) {
            query.setId = setId;
        }

        $.getJSON('fetch.php', query)
        .done(function(data) {
            if(data.error) {
                // Probably couldn't find the film
                showError(data.error);

            } else {
                // Found it! Animate adding the poster to the shelf
                var img = $('<img src="' + data.poster + '" alt="' + data.title + ' poster" title="' + data.title + '" style="margin-left: 1200px" height="60" />');
                $(img).load(function() {
                    $('.shelf').width(function(index, width) {
                        return width + 55;
                    });

                    $('.shelf').append(img);
                    img.animate({ marginLeft: 0 }, 'slow');

                    if(counter > 17) {
                        $('.shelf').animate({ 'left': '-=55px' });
                    }
                });
            }

            if(!setId && data.setId) {
                // First film? Save the setId
                setId = data.setId;
            }

            searchTimeout = setTimeout(searchNextTitle, 500);
        });
    }

    function searchNextTitle()
    {
        if(counter < titles.length) {
            searchTitle(titles[counter]);
            counter++;

        } else {
            showResults();
        }
    }

    $('body').on('submit', '#search', function(event) {
        event.preventDefault();

        $('.error').remove();

        titles = $('#titles').val().split("\n");
        if(titles.length > 1) {
            $('#search').after('<div class="shelf-container"><div class="shelf"></div></div>');
            $('#search').before('<div class="progress"><p id="indicator">Starting&hellip;</p></div>');
            $('#search').remove();
            searchNextTitle();

        } else {
            showError('You must provide at least 2 films to analyse!</p>');
        }
    });

    $(document).ready(function() {
        $('#search').show();
    })

})(jQuery);