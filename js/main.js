var map = L.map('map', { zoomControl:true, maxZoom:18, animate:true, duration:5.0, center: [45.4933, -122.6862], zoom: 12});
var hash = new L.Hash(map);
var base = L.tileLayer("http://d.sm.mapstack.stamen.com/((toner,$fff[@60],$fff[hsl-saturation@10]),(mapbox-water,$3399cc[hsl-color@40])[@50],parks[multiply],(buildings,$fff[@80],$73778c[hsl-color@60])[multiply])/{z}/{x}/{y}.png", { 
attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data: &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors,<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'});	
base.addTo(map);

clusterOn = false;
clusterZoom = 17;
loadnumber = 1;
var noHighlight = [];

console.log(updated);

function destroyButton() {
    $( "div" ).remove( ".destroyer" );
    $("#legend").css("visibility", "visible");
}

// Add colors for map elements
function doStylenbo(feature) {
    return {
        color: 'orange',
        fillColor: 'yellow',
        weight: 5,
        dashArray: '9',
        opacity: 1.0,
        fillOpacity: 0.08
    };
}

function doStyleALL(feature) {
    dataName = feature.properties.NAME.replace("/", " ");
    var story_count = 0;
    $(updated).each(function(){
        if(String(this) == dataName){
            story_count+=1;
        }
    });
    if($.inArray(dataName, updated) > -1){
        color = 'blue';
        fillOpacity = 0.1*story_count;
    } else {
        color = "yellow";
        fillOpacity = 0.00;
    }
    return {
        color: 'orange',
        fillColor: color,
        weight: 1,
        opacity: 1.0,
        fillOpacity: fillOpacity
    };
}

function doStyleTrees(x, located){
    var contains = false;
    var story_count = 0;
    $(updated_trees).each(function(){
        if($(this)[0] == x.id && $(this)[1] == located){
            contains = true;
        }
        if($(this)[1] == located){
            story_count++;
        }
    });
    var opacity = 0.2 * story_count;
    if(contains){
        return {
            radius: 7.0,
            fillColor: '#1919ff',
            fillOpacity: opacity,
        }
    }
    if (x.properties.MAX_GRID_C >= 50.0 && x.properties.MAX_GRID_C <= 62.9233396667) {
            return {
                radius: 3.0,
                fillColor: '#94de83',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 62.9233396667 && x.properties.MAX_GRID_C <= 73.609985) {
            return {
                radius: 3.4,
                fillColor: '#85CB75',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 73.609985 && x.properties.MAX_GRID_C <= 83.159973) {
            return {
                radius: 3.8,
                fillColor: '#76B868',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 83.159973 && x.properties.MAX_GRID_C <= 92.26001) {
            return {
                radius: 4.2,
                fillColor: '#67A55B',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 92.26001 && x.properties.MAX_GRID_C <= 101.209991) {
            return {
                radius: 4.6,
                fillColor: '#58934E',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 101.209991 && x.properties.MAX_GRID_C <= 111.110046) {
            return {
                radius: 5.0,
                fillColor: '#4A8041',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 111.110046 && x.properties.MAX_GRID_C <= 123.51001) {
            return {
                radius: 5.3,
                fillColor: '#3B6D34',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 123.51001 && x.properties.MAX_GRID_C <= 141.430003) {
            return {
                radius: 5.6,
                fillColor: '#2C5B27',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 141.430003 && x.properties.MAX_GRID_C <= 220.0) {
            return {
                radius: 5.9,
                fillColor: '#1D481A',
                fillOpacity: 0.8,
            }
        } else if (x.properties.MAX_GRID_C >= 220.0 && x.properties.MAX_GRID_C <= 300.0) {
            return {
                radius: 6.3,
                fillColor: 'red',
                fillOpacity: 0.8,
            }
        };
    };

function highlight(e) {
    if (noHighlight[0] == e.target.feature.properties.asd){
        info.update(e.target.feature.properties);
    } else {
    var asdf = e.target
        asdf.setStyle({
            color: 'orange',
            fillColor: 'yellow',
            weight: 5,
            opacity: 1.0,
            fillOpacity: 0.6
        });
    info.update(asdf.feature.properties);
    }
}

function dehighlight(e) {
    dataName = e.target.feature.properties.NAME.replace("/", " ");
    var story_count = 0;
    $(updated).each(function(){
        if(String(this) == dataName){
            story_count+=1;
        }
    });
    if($.inArray(dataName, updated) > -1){
        color = 'blue';
        fillOpacity = 0.1*story_count;
    } else {
        color = "yellow";
        fillOpacity = 0.00;
    }
    if (noHighlight[0] == e.target.feature.properties.asd){
    } else {
    var asdf = e.target
        asdf.setStyle({
            color: 'orange',
            fillColor: color,
            weight: 1,
            opacity: 1.0,
            fillOpacity: fillOpacity
        });
    info.update();
    }
}

function zoomToFeature(e) {
    /////////////////////////////
    //AJAX LOADING GIF
    /////////////////////////////
    var loadingGIF = function(){
        $.ajaxSetup({async: true})
        var gifURL = "pictures/ajax-loader.gif"
        var gif = '<div class="cont" style="z-index:9999999999999999;position:absolute;margin:auto;top:0;right:0;bottom:0;left:0;width:30%;height: 75px;background-color:rgba(255,255,255,0.85);border-radius:25px;"><h1 style="text-align:center;color:darkgreen;"><b>LOADING...</b></h1></div>';//<img style="z-index:9999999999999999;position:absolute;margin:auto;top:0;right:0;bottom:0;left:0;" src="pictures/ajax-loader.gif">
        $('body').append(gif);
        $( ".cont" ).ready(function(){
        $.ajax({async: true})

        // This is where the logic for UI updates relating to
        // new posts will be put

        });
    };

    if (noHighlight[0] == e.target.feature.properties.asd){
    } else {
        setTimeout(loadingGIF(),1)
        if(map.getZoom() >17){
            //console.log(map.getZoom())
        } else{
            map.fitBounds(e.target.getBounds());
        }
        nbo_polygons.setStyle({
        fillOpacity: 0.0,
        weight: 1
    });
    var y = e.target.feature.properties.asd;
    e.target.setStyle({
        color: 'orange',
        fillColor: 'yellow',
        weight: 5,
        opacity: 1.0,
        fillOpacity: 0.15
    });

    nboChange(y);
    }
}

// Info div logic
var info = L.control();
info.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'info');
    this.update();
    return this._div;
};

// Update hover text
info.update = function (e) {
    this._div.innerHTML = (e ?
    '<b>' + e.NAME + '</b>'
    : 'Hover Over Neighborhood');
};

info.addTo(map);
var aoi_line = new L.geoJson(exp_metro,{
    style: {
        color: 'purple',
        weight: 5,
        opacity: 0.5,
        fillOpacity: 0
    }
}).addTo(map);

var nbo_polygons = new L.geoJson(exp_cutnbo,{
    style: doStyleALL,
    onEachFeature: function (feature,layer){
        var asd_var = feature.properties.asd
        popcont = '</td></tr><tr><th scope="row">treesborhood: </th><td>' + Autolinker.link(String(feature.properties['asd'])) + '</td></tr></table>';
        layer.on({
                mouseover: highlight,
                mouseout: dehighlight,
                click: zoomToFeature
        });
    }
});
nbo_polygons.addTo(map);
if (clusterOn){
    var markers = L.markerClusterGroup({
    //disableClusteringAtZoom: clusterZoom,
    showCoverageOnHover: false,
    removeOutsideVisibleBounds: true
    });
} else {
    var visibleLayers = L.layerGroup();
    visibleLayers.addTo(map);
};

// Get the popup content
var popcont = "";
function getPopupContent(){
    try {
        if(logged_in){
            // User is logged in to social media
            // and has approved this application,
            // so get the treestory form and put
            // it in the popcont variable
            $.ajax({
                url: base_dir+"popup.php",
                type: 'get',
                async: true,
                success: function(html){
                    popcont = html;
                }
            });
            console.log("LOGGED IN");
        }
        else {
            // User is not logged into social media.
            // Fill popcont with login form.
            $.ajax({
                url: base_dir+"libs/login.php",
                type: 'get',
                async: true,
                success: function(html){
                    popcont = html;
                }
            });
            console.log("NOT LOGGED IN");
        }
    }
    catch(ReferenceError){
        // Things most likely havent loaded yet.
        setTimeout(function(){ getPopupContent()}, 250);
    }
}
getPopupContent();

function nboChange(x){
    noHighlight.splice(0)
    if (clusterOn){
        if (loadnumber !== 1){
            markers.clearLayers();
        };
    } else {
        if (typeof visibleLayers  !== 'undefined'){
            visibleLayers.clearLayers();
        };
    };
    loadnumber=2
    var sel = x;
    noHighlight.push(sel);
    dataName = sel.replace(/ /g, "_");
    dataName2 = sel.replace(/_/g, " ");
    dataPath = String("data/" + dataName + ".geojson");
    var trees = new L.GeoJSON.AJAX((dataPath), {
            style: function(feature){
            return{color:'green'}
        },
            pointToLayer: function(feature, latlng){
            return new L.CircleMarker(latlng, doStyleTrees(feature, dataName2))//{radius:6,color:'green',fillOpacity:0.7})
        },
            onEachFeature: function (feature,layer){
            var h = Math.round(feature.properties.MAX_GRID_C)
            var idNumber = String(feature.id);
            var colLen = dataName2.length + 9;
            if (colLen > 30){
                colLen = 30;
            }
            if (colLen < 21) {
                colLen = 21;
            }

            // Get tree info
            var height = Autolinker.link(String(h) + 'ft');
            var located = Autolinker.link(dataName2);
            var id = Autolinker.link(idNumber);
            // Create template for info
            template = '<div id="popup" ';
            if(logged_in==false){
                template += 'style="max-width: 201px;">';
            } else {
                template += 'style="max-width: 258px;">';
            }

	    var tree_info = '<div class="row"><div class="col-md-4"><strong>Height</strong></div><div class="col-md-8">' + height + '</div></div><div class="row"><div class="col-md-4"><strong>Location</strong></div><div class="col-md-8">' + located + '</div></div><div class="row"><div class="col-md-4"><strong>Tree ID</strong></div><div id="tree-id" class="col-md-8">' + id + '</div></div>';

        var contains = false;
        var story_count = 0;
	    var image_url = [];
        $(updated_trees).each(function(){
            if($(this)[0] == id && $(this)[1] == located){
                contains = true;
                story_count++;
                image_url.push([id, $(this)[2], $(this)[3]]);
            }
        });
        if(contains){
            var scrolldiv = createCarousel(image_url, located, base_dir);
            template += scrolldiv;
        } else {
            template += "<div id='popup-link' class='text-center'><a href='"+base_dir+"list.php?location="+located+"' class='text-center'>No Stories For This Tree.</a></div>";
        }
        template += tree_info;
        if(logged_in){
            // Make sure the tree id gets passed to the update script
            target = "value=\"";
            var querystring = id + " " + located + ", ";
            var position = popcont.indexOf(target) + target.length;
            popcont = [popcont.slice(0, position), querystring, popcont.slice(position)].join('');
        }

        // Update popup content
        template += popcont;
        layer.bindPopup(template);

        jCount= [];
        jCount.push(idNumber);

        }
    });

    if (clusterOn){
            trees.on('data:loaded', function (){
            markers.addLayer(trees);
        });
        map.addLayer(markers);
    } else {
        visibleLayers.addLayer(trees);
    };

    trees.on('data:loaded', function (){
        $( ".cont" ).remove();
    });

    // Get the script having to do with uploads,
    // but only when an upload needs to be done
    $.getScript(base_dir+'/js/upload.js');
};

$('#menu').css('margin-top', $('.leaflet-control-zoom-out').height()*2 + 20);
$('#menu').css('margin-left', 10);
