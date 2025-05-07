     // Global variables
        var map;
        var marker;

        var defultLat = 39.6295; // Latitude for Morgantown, WV
        var defultLon = -79.9559; // Longitude for Morgantown, WV

        const weatherApiKey = "120cf3c767eb4028a06194801242212";

        map = L.map("map").setView([defultLat, defultLon], 12); // Adjusted zoom level for Morgantown

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

                
        },
        function () {
            // If geolocation fails, default to Morgantown
            map.setView([defultLat, defultLon], 12);
            marker = L.marker([defultLat, defultLon]).addTo(map);
            getWeatherByCoords(defultLat, defultLon);
        });

        const button = document.getElementById("searchButton");

        //handle search on button click w/ nominatim
        button.addEventListener("click", function(event) {
            var query = document.getElementById("searchBox").value.trim();

            var url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;

            fetch(url).then(response => response.json())
            .then(data => {

            if(data.length === 0){
                alert("no results found, please refine search");
                return;
            }

            var result = data[0];
            var lat = result.lat;
            var lon = result.lon;
            var displayName = result.name;

            if(marker){
                map.removeLayer(marker);
            }   

            map.setView([lat, lon], 12);
            marker = L.marker([lat, lon]).addTo(map);

        
            })
            
        });

        // FUNCTION TO DISPLAY POSTS ON FEED MAP
        export function displayPostLocation(){

            //creates a new marker for the post location using custom object
            var currentMarker = L.marker([feed.postLocation.lat, feed.postLocation.lon]).addTo(map);
            
            //CREATES POPUP FOR MARKER:
            //get current post
            var currentPost = document.getElementById(feed.postLocation.postID);
            
            //clone current post and extract image and caption
            var cpClone = currentPost.cloneNode(true);
            var cpImage = cpClone.querySelector(".post-image");
            var cpTxt = cpClone.querySelector("." + feed.postLocation.postID + "-caption");
            var viewPostBttn = cpClone.querySelector(".view-post-form");

            //create thumbnail (new div) for the post to appear on map
            var postThumbnail = document.createElement("div");
            postThumbnail.appendChild(cpImage);
            postThumbnail.appendChild(cpTxt);
            postThumbnail.appendChild(viewPostBttn);

            //create popup for the post
            currentMarker.bindPopup(postThumbnail).openPopup();

        }

