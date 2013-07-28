(function($) {
    var errorTimeout = null;
    var searchTimeout = null;
    var titles = [];
    var counter = 0;
    var setId = null;

    function showError(message)
    {
        window.clearTimeout(errorTimeout);
        $('#titles').before('<p class="error">' + message + '</p>');
        errorTimeout = window.setTimeout("$('.error').fadeOut()", 5000);
    }

    function showResults()
    {
        $('#indicator').html('Compiling results&hellip;');

        window.location.href = '/show.php?id=' + setId;
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
                showError(data.error);
            }

            if(data.setId) {
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
            $('#search').before('<div class="progress"><p id="indicator">Starting&hellip;</p></div>');
            $('#search').hide();
            searchNextTitle();

        } else {
            showError('You must provide at least 2 films to analyse!</p>');
        }
    });

    $(document).ready(function() {
        $('#search').show();
    })

})(jQuery);