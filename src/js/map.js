  // Global variables
  var map;
  let widgetExpanded = false;
  var marker;

  var defultLat = 39.6295; // Latitude for Morgantown, WV
  var defultLon = -79.9559; // Longitude for Morgantown, WV

  const weatherApiKey = "120cf3c767eb4028a06194801242212";

  map = L.map("map").setView([defultLat, defultLon], 12); // Adjusted zoom level for Morgantown

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map);

  toggleWeatherWidget();

  //load map view to current location
  navigator.geolocation.getCurrentPosition(function(position) {
      var userLat = position.coords.latitude;
      var userLon = position.coords.longitude;

      map.setView([userLat, userLon], 13);
      marker = L.marker([userLat, userLon]).addTo(map);

      getWeatherByCoords(userLat, userLon);
          
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

      getWeatherByCoords(lat, lon);
      toggleWeatherWidget();
      
      }) 
  });

  //handle weather widget expansion/collapse button
  const toggleButton = document.getElementById("weather-toggle");
  toggleButton.addEventListener("click", toggleWeatherWidget);
  //TODO: JQUERY Animation for menu

  //Toggles weather widget
  function toggleWeatherWidget() {
      const weatherWidget = document.getElementById("weather-widget");
      const weatherContent = document.getElementById("weather-content");
      const toggleButton = document.getElementById("weather-toggle");
      
      if (widgetExpanded) {
          // Collapse widget
          weatherContent.classList.remove("expanded");
          weatherWidget.classList.remove("expanded");
          toggleButton.textContent = "+";
      } else {
          // Expand widget
          weatherContent.classList.add("expanded");
          weatherWidget.classList.add("expanded");
          toggleButton.textContent = "-";
      }
      
      widgetExpanded = !widgetExpanded;
  }

  //remove weather widget by making css invisible
  function removeWeatherWidget() {
      const weatherWidget = document.getElementById("weather-widget");
      if (weatherWidget) {
          weatherWidget.style.display = "none"; // Make the widget invisible instead of removing it
      }
  }

  function addWeatherWidget() {
      const weatherWidget = document.getElementById("weather-widget");
      if (weatherWidget) {
          weatherWidget.style.display = "block"; // Make the widget visible again
      }

  }

  // Function to get weather by coordinates
  function getWeatherByCoords(lat, lng) {
      // Construct the API URL
      const weatherApiUrl = `https://api.weatherapi.com/v1/current.json?key=${weatherApiKey}&q=${lat},${lng}&aqi=no`;
      
      // Fetch weather data
      fetch(weatherApiUrl)
          .then(response => {
              if (!response.ok) {
                  throw new Error('Weather data not available');
              }
              return response.json();
          })
          .then(data => {
              // Update the weather widget with data
              updateWeatherWidget(data);
          })
          .catch(error => {
              console.error('Error fetching weather:', error);
          });
  }

  // Function to update the weather widget with fetched data
  function updateWeatherWidget(data) {
      // Extract location data
      const location = data.location;
      const current = data.current;
      
      // Update location name
      document.getElementById("weather-location").textContent = `${location.name}, ${location.country}`;
      
      // Update weather icon
      document.getElementById("weather-icon").src = `https:${current.condition.icon}`;
      document.getElementById("weather-icon").alt = current.condition.text;
      
      // Update temperature
      document.getElementById("weather-temp").textContent = `${current.temp_f}°F`;
      
      // Update weather description
      document.getElementById("weather-description").textContent = current.condition.text;
      
      // Update other weather details
      document.getElementById("weather-feels").textContent = `${current.feelslike_f}°F`;
      document.getElementById("weather-humidity").textContent = `${current.humidity}%`;
      document.getElementById("weather-wind").textContent = `${current.wind_mph} mp/h ${current.wind_dir}`;
  }