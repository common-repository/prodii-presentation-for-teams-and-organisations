jQuery(document).ready(function ($) {
    function get(params, callback, errorCallback) {
        var url = prodii_settings.ajax_url;

        $.ajax(
            {
                url    : url,
                method : 'POST',
                data   : params,
                success: callback,
                error  : function (e) {
                    errorCallback !== undefined ? errorCallback(e.responseJSON) : callback(e.responseJSON)
                }
            }
        );
    }

    window.prodii = {
        getHtml        : function (template, type, id, callback, errorCallback) {
            get(
                {
                    action  : 'prodii_get_html',
                    template: template,
                    type    : type,
                    ids     : id
                }, callback, errorCallback
            )
        }, getMapSimple: function (identity, lat, lng, zoom, pan_offset) {
            var myLatlng = new google.maps.LatLng(lat, lng);

            var simpleOptions = {
                zoom             : zoom,
                zoomControl      : false,
                panControl       : false,
                scaleControl     : false,
                scrollwheel      : false,
                streetViewControl: false,
                mapTypeControl   : false,
                center           : myLatlng,
                mapTypeId        : google.maps.MapTypeId.ROADMAP
            };

            var map = new google.maps.Map(document.getElementById(identity), simpleOptions);

            var infowindow = new google.maps.InfoWindow({
                                                            content: ""
                                                        });

            var marker = new google.maps.Marker(
                {
                    position: myLatlng,
                    map     : map,
                    title   : ""
                }
            );

            pan_offset = pan_offset === undefined ? {x: 0, y: 0} : pan_offset;
            map.panBy(pan_offset.x, pan_offset.y);
        }
    };
});

jQuery(function ($) {
    $(document).on('click', '[prodii-action], .prodii-wrapper', function () {
        var target = $(this).data('prodii-target');
        console.log(target);
    });
});