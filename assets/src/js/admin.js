jQuery(function ($) {
    var width = 0;
    $('.prodii-overview-list .column-shortcode input').each(function () {
        width = width < $(this).val().length ? $(this).val().length : width;
    }).css('width', width * 8).click(function () {
        $(this).select();
    });

    var toc      = $('.prodii-reference #toc'),
        headings = [],
        names    = [];

    headings = getHeadings($('.prodii-reference > .section'), '');

    toc.append(addToTOC(headings));

    function addToTOC(headings) {
        var list = $('<ul></ul>');
        $.each(headings, function () {
            var title       = this.text,
                elem        = this.elem,
                subHeadings = this.subHeadings;

            var line  = $('<li></li>'),
                inner = $('<span></span>');

            inner.html(title);
            inner.on('click', function () {
                elem.trigger('click');
            });

            line.append(inner);

            if (subHeadings.length !== 0) {
                line.append(addToTOC(subHeadings));
            }

            list.append(line);
        });
        return list;
    }

    function getHeadings(elems, parent) {
        var headings = [],
            parent   = parent !== '' ? parent + '-' : parent;
        elems.each(function () {
            var _        = $(this),
                heading  = _.find('> h2:first-of-type, > h3:first-of-type, > h4:first-of-type, > h5:first-of-type, > h6:first-of-type'),
                title    = heading.html(),
                baseText = title.toLowerCase().split(' ').join('-');

            var iterator = 0,
                text     = '';
            do {
                text = parent + baseText + '-' + iterator;
                iterator++;
            } while (names.indexOf(text) !== -1);
            names.push(text);

            _.attr('data-section', text);

            heading.on('click', function () {
                goToSection(text);
            }).wrap('<a class="section-heading" href="#' + text + '"></a>');

            var subHeadings = getHeadings(_.find('> .section'), text);

            headings.push(
                {
                    text       : title,
                    elem       : heading,
                    subHeadings: subHeadings
                }
            )
        });

        return headings;
    }

    var autoScrolling = false;

    function goToSection(name) {
        var section = $('.prodii-reference').find('[data-section="' + name + '"]'),
            admbar  = $('#wpadminbar');

        if (section.length) {
            autoScrolling = true;
            $('body, html').stop().animate(
                {
                    scrollTop: section.offset().top - admbar.height()
                }, 250, function () {
                    autoScrolling = false;
                }
            )
        }
    }

    if (location.hash) {
        goToSection(location.hash.substr(1));
    }

    $(document).scroll(function () {
        $('.prodii-reference .section > a.section-heading').each(function () {
            if (!autoScrolling) {
                var _   = $(this),
                    top = _.offset().top;

                if ($(window).scrollTop() < top) {
                    location.hash = _.attr('href').substr(1);
                    return false;
                }
            }
        });
    });
});