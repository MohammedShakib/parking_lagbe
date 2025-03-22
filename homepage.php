<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parking Lagbe - Find Parking</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Leaflet.js for Map Integration -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="shortcut icon" href="img/parked-car.png" type="image/x-icon">
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
    /* Background car animation styles */
    @keyframes moveCars {
      0% {
        transform: translate(-50px, 0) rotate(0deg);
      }
      100% {
        transform: translate(calc(100vw + 50px), 0) rotate(0deg);
      }
    }

    @keyframes moveCarsReverse {
      0% {
        transform: translate(calc(100vw + 50px), 0) rotate(180deg);
      }
      100% {
        transform: translate(-50px, 0) rotate(180deg);
      }
    }

    .background-animation {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      overflow: hidden;
    }

    .car {
      position: absolute;
      width: 30px;
      height: 15px;
      opacity: 0.6;
    }

    .car:before {
      content: "";
      position: absolute;
      top: 0;
      width: 60%;
      height: 60%;
      background-color: currentColor;
    }

    .car:after {
      content: "";
      position: absolute;
      top: 0;
      width: 25%;
      height: 40%;
      background-color: currentColor;
    }

    .car-right {
      animation: moveCars 15s infinite linear;
    }

    .car-right:before {
      left: 10%;
      border-radius: 20% 50% 0 0;
    }

    .car-right:after {
      right: 10%;
      border-radius: 50% 20% 0 0;
    }

    .car-left {
      animation: moveCarsReverse 15s infinite linear;
    }

    .car-left:before {
      right: 10%;
      border-radius: 50% 20% 0 0;
    }

    .car-left:after {
      left: 10%;
      border-radius: 20% 50% 0 0;
    }
    
    /* Different colors and speeds for cars */
    .car:nth-child(1) {
      top: 20%;
      color: rgba(0, 123, 255, 0.6);
      animation-duration: 20s;
    }

    .car:nth-child(2) {
      top: 35%;
      color: rgba(220, 53, 69, 0.6);
      animation-duration: 15s;
      animation-delay: 2s;
    }

    .car:nth-child(3) {
      top: 50%;
      color: rgba(40, 167, 69, 0.6);
      animation-duration: 25s;
      animation-delay: 5s;
    }

    .car:nth-child(4) {
      top: 65%;
      color: rgba(255, 193, 7, 0.6);
      animation-duration: 18s;
      animation-delay: 8s;
    }

    .car:nth-child(5) {
      top: 80%;
      color: rgba(111, 66, 193, 0.6);
      animation-duration: 22s;
      animation-delay: 3s;
    }

    /* Center the form */
    .form-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 60vh;
      padding: 2rem 0;
      position: relative;
    }

    /* Toggle button style */
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }
    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: #2563eb;
    }
    input:checked + .slider:before {
      transform: translateX(26px);
    }

    /* Map container */
    .map-container {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    /* Map size */
    #map { 
      width: 100%;
      height: 400px;
      margin-top: 1rem;
      border-radius: 0.5rem;
      border: 2px solid #ddd;
      display: none;
    }
  </style>
</head>
<body class="bg-gray-100 relative">

  <!-- Background animation with cars -->
  <div class="background-animation">
    <div class="car car-right" style="top: 20%"></div>
    <div class="car car-left" style="top: 35%"></div>
    <div class="car car-right" style="top: 50%"></div>
    <div class="car car-left" style="top: 65%"></div>
    <div class="car car-right" style="top: 80%"></div>
  </div>

  <!-- Navbar -->
  <nav class="bg-black text-white px-4 py-2 flex justify-between items-center">
    <div class="text-xl font-bold">Parking Lagbe</div>
    <div>
      <a href="index.php" class="text-white px-4 py-2 hover:bg-gray-700 rounded">Home</a>
      <a href="#" class="text-white px-4 py-2 hover:bg-gray-700 rounded">About</a>
      <a href="#" class="text-white px-4 py-2 hover:bg-gray-700 rounded">Contact</a>
      <a href="login.php" class="text-white px-4 py-2 hover:bg-gray-700 rounded">Login</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container mx-auto px-4">
    <div class="form-container">
      <div class="w-full md:w-1/3 bg-white p-6 shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-4 text-blue-600 text-center">Find Parking</h2>

        <form id="location-form">
          <!-- Pickup Location -->
          <label for="pickup-location" class="block text-gray-700 font-semibold mb-2">Search Location</label>
          <input type="text" id="pickup-location" name="pickup_location" placeholder="Enter Location" class="w-full p-3 border border-gray-300 rounded-lg mb-4" required>

          <!-- Date and Time Toggle -->
          <div class="flex items-center mb-4">
            <span class="text-gray-700 font-semibold mr-2">For Later</span>
            <label class="toggle-switch">
              <input type="checkbox" id="for-later">
              <span class="slider"></span>
            </label>
          </div>

          <div id="date-time-section" class="hidden mb-4">
            <!-- Date and Time -->
            <label for="date-time" class="block text-gray-700 font-semibold mb-2">Date & Time</label>
            <div class="flex space-x-2">
              <input type="date" id="date-input" name="date_time" class="w-1/2 p-3 border border-gray-300 rounded-lg" />
              <input type="time" id="time-input" name="time" class="w-1/2 p-3 border border-gray-300 rounded-lg" />
            </div>
          </div>

          <button type="button" id="search-button" class="w-full py-3 mt-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">Search</button>
        </form>
      </div>
    </div>

    <!-- Map Section -->
    <div class="map-container">
      <div id="map-status" class="text-center hidden mb-2 font-medium text-gray-700"></div>
      <div id="map"></div>
    </div>
  </div>

  <!-- Map Script -->
  <script>
    // Set default date and time (current date, current time + 2 hours)
    function setDefaultDateTime() {
      const now = new Date();
      const dateInput = document.getElementById('date-input');
      const timeInput = document.getElementById('time-input');
      
      // Set current date in YYYY-MM-DD format
      const year = now.getFullYear();
      const month = (now.getMonth() + 1).toString().padStart(2, '0');
      const day = now.getDate().toString().padStart(2, '0');
      dateInput.value = `${year}-${month}-${day}`;
      
      // Set time to current time + 2 hours
      now.setHours(now.getHours() + 2);
      const hours = now.getHours().toString().padStart(2, '0');
      const minutes = now.getMinutes().toString().padStart(2, '0');
      timeInput.value = `${hours}:${minutes}`;
    }

    // Initialize the map when needed
    let map = null;
    let marker = null;
    const mapElement = document.getElementById('map');
    const mapStatus = document.getElementById('map-status');
    
    function initMap() {
      if (!map) {
        map = L.map('map').setView([23.8103, 90.4125], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
      }
      mapElement.style.display = 'block';
      map.invalidateSize();
    }

    // Toggle Date and Time section and set default values
    document.getElementById('for-later').addEventListener('change', function() {
      const dateTimeSection = document.getElementById('date-time-section');
      if (this.checked) {
        dateTimeSection.style.display = 'block';
        setDefaultDateTime();
      } else {
        dateTimeSection.style.display = 'none';
      }
    });

    // Handle search button click
    document.getElementById('search-button').addEventListener('click', function() {
      searchLocation();
    });
    
    // Handle form submission (for Enter key)
    document.getElementById('location-form').addEventListener('submit', function(e) {
      e.preventDefault();
      searchLocation();
    });
    
    // Search location using Nominatim API directly
    function searchLocation() {
      const locationInput = document.getElementById('pickup-location').value.trim();
      
      if (!locationInput) {
        alert("Please enter a location.");
        return;
      }
      
      mapStatus.textContent = "Searching for location...";
      mapStatus.classList.remove('hidden');
      
      initMap();
      
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationInput)}`)
        .then(response => response.json())
        .then(data => {
          if (data && data.length > 0) {
            const result = data[0];
            const lat = parseFloat(result.lat);
            const lon = parseFloat(result.lon);
            
            map.setView([lat, lon], 16);
            
            if (marker) {
              map.removeLayer(marker);
            }
            
            marker = L.marker([lat, lon]).addTo(map);
            marker.bindPopup(`<b>${locationInput}</b><br>Lat: ${lat.toFixed(5)}, Lon: ${lon.toFixed(5)}`)
                  .openPopup();
            
            mapStatus.textContent = `Found: ${result.display_name}`;
          } else {
            mapStatus.textContent = "Location not found. Please try a different search term.";
          }
        })
        .catch(error => {
          console.error("Error searching for location:", error);
          mapStatus.textContent = "Error searching for location. Please try again.";
        });
    }
  </script>
</body>
</html>