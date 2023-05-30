class LLMAPS{
    constructor(mapConfigs) {
    var defaultConfigs = {
            center: [0, 0],
            zoom: 14, //1 is zoom out and zoom in as number increased
            map_type: 'default',
            marker_icon: "",
            draggable : false,
            ondrag : '',
        }
        mapConfigs = jQuery.extend(defaultConfigs, mapConfigs);
        this.mapConfigs = mapConfigs;
    }

    llmapsInitiate(mapInitID){
        
        var mapConfigsArray = this.mapConfigs;
        var mapMarkersArray = this.mapMarkersArray;
        
        var thisMapConf = {
        center: this.mapConfigs.center,
            zoom: this.mapConfigs.zoom
        }
        var map = new L.map(mapInitID, thisMapConf);
        var layer = new L.TileLayer(this.llmaps_type(this.mapConfigs.map_type));

        jQuery.each(mapMarkersArray, function(key, markerData) {
            var markerLatitude = markerData.latitude;
            var markerLongitude = markerData.longitude;
            
            if (typeof markerData.icon != "undefined"){
                var markerIcon = markerData.icon;
            }else{
                var markerIcon  = mapConfigsArray.marker_icon;
            }
            
            if( markerIcon != ''){
                var customIcon = {
                    iconUrl:markerIcon,
                    iconSize:[40, 40]
                }
                var myIcon = L.icon(customIcon);
                var iconOptions = {
                    icon:myIcon,
                    draggable: mapConfigsArray.draggable
                }
            }else{
                var iconOptions = {
                    draggable: mapConfigsArray.draggable
                }
            }
            if( markerLatitude > 0 && markerLatitude > 0){
                var marker = new L.Marker([markerLatitude, markerLongitude], iconOptions);
                marker.addTo(map);
                if( mapConfigsArray.ondrag != ''){
                    marker.on('dragend', function (e) {
                        window[mapConfigsArray.ondrag](marker);
                        var LatLngObject = marker.getLatLng();
                        var latitude = LatLngObject.lat;
                        var longitude = LatLngObject.lng;
                        marker.setLatLng([latitude,longitude]);
                        map.panTo([latitude,longitude]);
                    });
                }


                if (typeof markerData.popup_template != "undefined"){
                    var popup_template = markerData.popup_template;
                    marker.bindPopup(popup_template).openPopup();
                }
                marker.addTo(map);
            
            }
        });
        
        map.addLayer(layer);
        
        var LLMAPSObject = {
            mapObject: map,
        }
        return LLMAPSObject;
        
    }
    
    registerMarkers(mapMarkersArray){
        this.mapMarkersArray    = mapMarkersArray;
    }


    llmaps_type(map_type){
        switch (map_type){
        case "default":
            return 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
        break;
        case "humanitarian":
            return 'https://a.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
        break;
        case "watercolor":
            return 'https://stamen-tiles.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg';
        break;
        case "transport":
            return 'https://tile.memomaps.de/tilegen/{z}/{x}/{y}.png';
        break;
        case "light":
            return 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png';
        break;
        case "dark":
            return 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png';
        break;
        case "lima":
            return 'https://cdn.lima-labs.com/{z}/{x}/{y}.png?api=demo';
        break;
        }
    }
}

function OSMAPAutoComplete(AutoCompleteConfigs){
    var inputInitID = AutoCompleteConfigs.inputInitID;
    var callbackFunc = AutoCompleteConfigs.callbackFunc;
    
    jQuery(document).on('keyup', '#'+inputInitID, function () {
        var search_keyword = jQuery(this).val();
        if( search_keyword.length < 4){
            return;
        }
        var thisObj = jQuery(this);
        jQuery('.llmaps_'+inputInitID).remove();
        
        jQuery.get('https://nominatim.openstreetmap.org/search?format=json&limit=10&q=' + search_keyword, function(data){
            jQuery('.llmaps_'+inputInitID).remove();
            var responseHtml = '<div class="llmaps-autocomplete llmaps_'+inputInitID+'"><ul>';
            jQuery.each(data, function(key, locationsData) {
                var address = locationsData.display_name;
                var lat = locationsData.lat;
                var lon = locationsData.lon;
                responseHtml += '<li data-lat="'+lat+'" data-lon="'+lon+'">'+address+'</li>';
            });
            responseHtml += '</ul></div>';
            thisObj.closest('div').append(responseHtml);
        });
        
    });
    
    jQuery(document).on('click', '.llmaps_'+inputInitID+' ul li', function () {
        var address = jQuery(this).html();
        var lat = jQuery(this).data('lat');
        var lon = jQuery(this).data('lon');
        jQuery("#"+inputInitID).val(address);
        jQuery('.llmaps_'+inputInitID).remove();
        if (typeof AutoCompleteConfigs.callbackFunc != "undefined"){
            window[callbackFunc](lat,lon,address);
        }
        
    });
    
    
}