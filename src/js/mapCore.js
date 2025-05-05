var defultLat = 39.82; 
var defultLon = -98.57;

var map = L.map("map").setView([defultLat, defultLon], 5);
var marker;

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

//load map view to current location
 navigator.geolocation.getCurrentPosition(function(position) {
    var userLat = position.coords.latitude;
    var userLon = position.coords.longitude;

    map.setView([userLat, userLon], 13);
    marker = L.marker([userLat, userLon]).addTo(map);
    marker.bindPopup("Your Location").openPopup();
                
    });


    const button = document.getElementById("city-submit-button");

    // Handle search on button click
    button.addEventListener("click", function(event) {
        handleSearch();
    });

    // Handle search on Enter key press
    document.getElementById("search-input").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            handleSearch();
        }
    });

    function handleSearch() {
        var query = document.getElementById("search-input").value.trim();

        var url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;

        fetch(url).then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                alert("No results found, please refine search");
                return;
            }

            var result = data[0];
            var lat = result.lat;
            var lon = result.lon;

            if (marker) {
                map.removeLayer(marker);
            }

            map.setView([lat, lon], 13);
            marker = L.marker([lat, lon]).addTo(map);
        });
    }

    
