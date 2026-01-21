<?php

// print_r($_POST);

$pickup = $_POST['pickup'] ?? '';
$destination = $_POST['destination'] ?? '';
$service_type = $_POST['service-type'] ?? '';
$service_length = $_POST['service_length'] ?? '5';
$passengers = $_POST['passengers'] ?? '20';
$pickup_date = $_POST['pickup-date'] ?? date('Y-m-d');
$vehicles = array();
// $devUrl = 'http://localhost/suitecrm7/index.php?entryPoint=CustomEntryPoint';
$prodUrl = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
if(isset($_POST) && !empty($_POST)){
$data['action'] = "get_vehicles";
$api_url = $prodUrl;
$curl = curl_init($api_url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($curl);
curl_close($curl);
// print_r($response);
$vehicles =  json_decode($response, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limogen - Premium Chauffeur Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #ec268f;
            --primary-light: #ff60b3;
            --primary-dark: #c3046a;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1);
            --radius-sm: 8px;
            --radius: 14px;
            --radius-lg: 20px;
            --font-main: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: var(--font-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Hero & Form Section */
        .hero {
            max-width: 1280px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            padding: 0 24px;
        }

        .hero-left {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 32px;
            border: 1px solid var(--border);
        }

        .hero-left h1 {
            font-family: var(--font-heading);
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 24px;
            color: var(--text-main);
            line-height: 1.1;
        }

        .hero-left h1 span {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }

        .hero-left h1 span::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--primary);
            opacity: 0.15;
            z-index: -1;
        }

        .booking-form .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .field input, .field select {
            width: 100%;
            padding: 12px 16px;
            background: #f1f5f9;
            border: 1px solid transparent;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .field input:focus, .field select:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(236, 38, 143, 0.1);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(236, 38, 143, 0.39);
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 38, 143, 0.23);
        }

        .btn:active {
            transform: translateY(0);
        }

        .hero-right {
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            height: 100%;
            min-height:400px;
        }

        /* Vehicle Grid Section */
        .vehicle_grid {
            max-width: 1280px;
            margin: 60px auto;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            padding: 0 24px;
        }

        .vehicle_sidebar {
            background: var(--surface);
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            height: max-content;
            border: 1px solid var(--border);
            position: sticky;
            top: 24px;
        }

        .vehicle_sidebar h4 {
            font-family: var(--font-heading);
            color: var(--text-main);
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vehicle_sidebar h4::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--primary);
            border-radius: 2px;
        }

        .check-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .check-group:hover {
            background: #f1f5f9;
        }

        .check-group input {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
        }

        /* Cars grid */
        .cars {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }

        .card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .conthead {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .mini {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .card h3 {
            font-family: var(--font-heading);
            color: var(--text-main);
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .type {
            background: rgba(236, 38, 143, 0.1);
            color: var(--primary);
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .details {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info span {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .info strong {
            font-size: 1rem;
            color: var(--text-main);
        }

        .facilities_section {
            margin-bottom: 24px;
        }

        .facilities_section h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .facilities_section ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .facilities_section li {
            font-size: 0.8rem;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            color: var(--text-main);
            font-weight: 500;
        }

        .price-row {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .price-row .price-wrap {
            display: flex;
            flex-direction: column;
        }

        .price-row .amount {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 800;
            font-family: var(--font-heading);
        }

        .price-row .unit {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .round {
            background: var(--text-main);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--radius);
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .round:hover {
            background: var(--primary);
            transform: translateX(4px);
        }

        /* Modal Content */
        .popup-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .popup-content {
            background: var(--surface);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            position: relative;
            animation: modalIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: var(--text-main);
        }

        .popup-content h3 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 32px;
            text-align: center;
        }

        .popup-content .field {
            margin-bottom: 20px;
        }

        /* Responsive Improvements */
        @media (max-width: 1024px) {
            .hero {
                grid-template-columns: 1fr;
            }
            .hero-right {
                height: 350px;
            }
            .vehicle_grid {
                grid-template-columns: 1fr;
            }
            .vehicle_sidebar {
                position: relative;
                top: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                padding-bottom: 32px;
            }
            .vehicle_sidebar h4 {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .booking-form .row {
                grid-template-columns: 1fr;
            }
            .hero-left {
                padding: 24px;
            }
            .hero-left h1 {
                font-size: 1.75rem;
            }
            .price-row {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            .round {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>


    <div class="hero">
        <div class="hero-left">
            <h1>Premium <span>Chauffeur</span> Service</h1>
            <form class="booking-form" id="booking-form" action="check.php" method="post">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id'] ?? ''; ?>" />
                
                <div class="field">
                    <label>Pickup Location</label>
                    <input type="text" id="pickup" name="pickup" placeholder="Enter pickup address" required value="<?php echo $pickup; ?>">
                </div>

                <div class="field" style="margin-top: 20px;">
                    <label>Dropoff Location</label>
                    <input type="text" id="destination" name="destination" placeholder="Enter destination address" required value="<?php echo $destination; ?>">
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="field">
                        <label>Service Type</label>
                        <select name="service-type" id="service_type" required>
                            <option value="">Select service</option>
                        <?php 
                        $services = ["Airport", "Bachelor Party", "Bachelorette Party", "Birthday", "Casino", "Church Function", "Concert", "Construction Shuttle", "Convention", "Corporate Event", "Cruise Transfers", "Family Reunion", "General Day Trip", "Golf Outing", "Homecoming", "Night out on Town", "Over the Road", "Prom", "School Trip", "Shuttle Service", "Sports Event", "Theme Park", "Transfer", "Wedding", "Wedding Wire", "Wine Tour"];
                        foreach($services as $s) {
                            $selected = ($service_type == $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $selected>$s</option>";
                        }
                        ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Passengers</label>
                        <input type="number" id="passengers" name="passengers" min="1" max="100" value="<?php echo $passengers; ?>" required />
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="field">
                        <label>Pickup Date</label>
                        <input type="date" id="pickup-date" name="pickup-date" required value="<?php echo $pickup_date; ?>" />
                    </div>
                    <div class="field">
                        <label>Service Length (Hours)</label>
                        <input type="number" id="serviceLength" name="service_length" min="1" max="24" required value="<?php echo $service_length; ?>" />
                    </div>
                </div>

                <button type="submit" class="btn" style="margin-top: 20px;">Search Vehicles</button>
            </form>
            <div id="responseMsg" style="margin-top: 10px"></div>
        </div>
        <div class="hero-right">
            <?php 
            $p = !empty($pickup) ? $pickup : "Broward County, FL, USA";
            $p_enc = urlencode($p);
            $d_enc = urlencode($destination);
            $iframe_src = "https://www.google.com/maps?q=from+{$p_enc}+to+{$d_enc}&output=embed";
            ?>
            <iframe
                src="<?php echo $iframe_src; ?>"
                width="100%"
                height="100%"
                style="border:0;"
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
<script>


    const today = new Date().toISOString().split("T")[0];

                                        // Get the input element
                                        const pickupDateInput = document.getElementById("pickup-date");

                                        // Set default and minimum date to today
                                        pickupDateInput.value = today;
                                        pickupDateInput.min = today;

                                 document.addEventListener('DOMContentLoaded', function() {


                                        // Load Google Maps API with Places library
                                        const script = document.createElement('script');
                                        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyA-XmAtSvNj2CjcYT7VRfnIk58aGsdeh7k&libraries=places&callback=initAutocomplete`;
                                        script.async = true;
                                        script.defer = true;
                                        document.head.appendChild(script);
                                    });

                                    // This function will be called when the Google Maps API is loaded
                                     window.initAutocomplete = function() {
                                        const pickup = document.getElementById('pickup');
                                        const destination = document.getElementById('destination');
                                    

                                        // Create autocomplete object with city and postal code suggestions
                                        const autocomplete = new google.maps.places.Autocomplete(pickup, {
                                            types: ['(regions)'],
                                            componentRestrictions: {country: 'us'}, // Restrict to US addresses
                                            fields: ['address_components', 'geometry', 'formatted_address', 'name', 'postal_code']
                                        });

                                        const autocomplete2 = new google.maps.places.Autocomplete(destination, {
                                            types: ['(regions)'],
                                            componentRestrictions: {country: 'us'}, // Restrict to US addresses
                                            fields: ['address_components', 'geometry', 'formatted_address', 'name', 'postal_code']
                                        });

                                        // When a place is selected
                                        autocomplete.addListener('place_changed', function() {
                                            const place = autocomplete.getPlace();
                                            if (!place.geometry) {
                                                console.log('No details available for input: ' + place.name);
                                                return;
                                            }
                                            
                                            // Get address components
                                            let city = '';
                                            let state = '';
                                            let postalCode = '';
                                            
                                            // Find city, state, and postal code from address components
                                            for (const component of place.address_components) {
                                                const componentType = component.types[0];
                                                if (componentType === 'locality') {
                                                    city = component.long_name;
                                                }
                                                if (componentType === 'administrative_area_level_1') {
                                                    state = component.short_name;
                                        }
                                        if (componentType === 'postal_code') {
                                            postalCode = component.long_name;
                                        }
                                    }
                                    
                                    // If input was a zip code, format as "ZIP, City, State"
                                    if (pickup.value.match(/^\d{5}(-\d{4})?$/) && city && state) {
                                        pickup.value = `${postalCode || pickup.value}, ${city}, ${state}`;
                                        return;
                                    }
                                    pickup.value = place.formatted_address;
                                    if (destination.value.match(/^\d{5}(-\d{4})?$/) && city && state) {
                                        destination.value = `${postalCode || destination.value}, ${city}, ${state}`;
                                        return;
                                    }
                                    destination.value = place.formatted_address;
                                    
                                    console.log('Selected location:', place.formatted_address);
                                                    });
                                                    
                                                    // Prevent form submission on enter key in the search input
                                                    pickup.addEventListener('keydown', function(e) {
                                                        if (e.key === 'Enter') {
                                                            e.preventDefault();
                                                        }
                                                    });

                                                    
                                                    destination.addEventListener('keydown', function(e) {
                                                        if (e.key === 'Enter') {
                                                            e.preventDefault();
                                                        }
                                                    });

                                                   

                                                

                                                
                                                };

                                
                            </script>
      <script>
            document.getElementById('booking-form').addEventListener('submit', function(e) {
                                            e.preventDefault();  // Still prevent default initially for validation

                                            // Get form values
                                            let formError = document.getElementById('formError');
                                            const pickup = document.getElementById('pickup').value;
                                            const destination = document.getElementById('destination').value;
                                            const pickupDate = document.getElementById('pickup-date').value;
                                            const serviceType = document.getElementById('service_type').value;
                                            const passengers = document.getElementById('passengers').value;

                                            // Validation (unchanged)
                                            if (pickup === '' || destination === '' || pickupDate === '' || serviceType === '' || passengers === '') {
                                                formError.textContent = 'Please fill in all fields';
                                                return;
                                            }
                                           

                                            // If validation passes, submit the form (this will POST data and redirect to quote.php)
                                            this.submit();
                                        });
                                
      </script>

     
    </section>

                   <?php if(isset($vehicles) && !empty($vehicles)){ ?>

                    <div class="vehicle_grid">
                        <div class="vehicle_sidebar">
                            <h4>Filter Vehicles</h4>
                            <?php 
                            $categories = ["Sedan", "SUV", "Mini Bus", "Stretch Limo", "Stretch SUV Limo", "Motor Coach"];
                            foreach($categories as $cat) {
                                ?>
                                <label class="check-group">
                                    <input type="checkbox" class="vehicle-filter" value="<?php echo $cat; ?>" /> <?php echo $cat; ?>
                                </label>
                                <?php
                            }
                            ?>
                        </div>
                        <div>
                            <div class="cars">
                                <?php
                                
                                foreach($vehicles as $vehicle){
                                     $category = str_replace("_", " ", $vehicle['vehicle_cetagory']);  
                                    ?>
                                    <div class="card" data-category="<?php echo htmlspecialchars($category); ?>">
                                        <?php $img_src = !empty($vehicle['images_c']) ? $vehicle['images_c'] : "https://zabrin.xyz/limogen/index.php?entryPoint=vehicle_image&id=" . $vehicle['id'] . "&type=vehicle_xl"; ?>
                                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>" onerror="this.src='https://via.placeholder.com/400x250?text=No+Image+Available'" />
                                <div class="conthead">
                                <div class="mini">
                                    <h3><?php echo $vehicle['name']; ?></h3>
                                                <div class="type"><?php echo $category; ?></div>
                                </div>

                                <!-- Doors & Passengers (updated layout) -->
                                            <div class="details">
                                                <div class="info">
                                                    <span><i class="ri-user-fill"></i> Passengers</span>
                                                    <strong><?php echo $vehicle['passenger']; ?></strong>
                                                </div>
                                                <div class="info">
                                                    <span><i class="ri-briefcase-4-fill"></i> Bags</span>
                                                    <strong><?php echo $vehicle['bags']; ?></strong>
                                                </div>
                                            </div>
                                            <?php if (!empty($vehicle['facilities'])): ?>
                                            <div class="facilities_section">
                                                <h4>Facilities</h4>
                                                <ul>
                                                    <?php 
                                                    $facilities = explode(',', $vehicle['facilities']);
                                                    foreach(array_slice($facilities, 0, 3) as $f): 
                                                        $f = str_replace("^", "", trim($f));
                                                        $f = str_replace("_", " ", $f);
                                                        ?>
                                                        <li><?php echo $f; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <?php endif; ?>

                                            <div class="price-row">
                                                <div class="price-wrap">
                                                    <span class="unit">From</span>
                                                    <span class="amount">$<?php echo $vehicle['rate_c']; ?></span>
                                                </div>
                                                <button class="round" onclick='openQuoteModal(<?php echo json_encode($vehicle); ?>)'>
                                                    Get Quote <i class="ri-arrow-right-line"></i>
                                                </button>
                                            </div>
                                </div>
                            </div>
                                    
                                    <?php
                                    
                                }
                                
                                
                                ?>
                            

                           
                            </div>

                            
                        </div>
                    </div>

                    <?php } ?>

                    <!-- Add this after the vehicle grid, e.g., around line 3250 -->
<div id="quotePopup" class="popup-modal">
    <div class="popup-content">
        <i class="ri-close-line close-btn"></i>
        <h3>Get Quote for <br><span id="popupVehicleName" style="color: var(--primary);"></span></h3>
        <form id="quoteForm">
            <div class="field">
                <label>First Name</label>
                <input type="text" name="first_name" required placeholder="Your first name">
            </div>
            
            <div class="field">
                <label>Last Name</label>
                <input type="text" name="last_name" required placeholder="Your last name">
            </div>
            
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Your email">
            </div>
            
            <div class="field">
                <label>Phone Number</label>
                <input type="tel" name="phone" required placeholder="Your phone number">
            </div>

            <input type="hidden" name="pickup" value="<?php echo $pickup ?>" />
            <input type="hidden" name="destination" value="<?php echo $destination ?>" />
            <input type="hidden" name="service_type" value="<?php echo $service_type ?>" />
            <input type="hidden" name="passengers" value="<?php echo $passengers ?>" />
            <input type="hidden" name="pickup_date" value="<?php echo $pickup_date ?>" />
            <input type="hidden" name="service_length" value="<?php echo $service_length ?>" />
            
            <button type="submit" class="btn" style="margin-top: 10px;">Submit Quote Request</button>
        </form>
        <div id="popupMessage"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.vehicle-filter');
        const cards = document.querySelectorAll('.card');

        function filterVehicles() {
            const selectedCategories = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value.toLowerCase());

            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category').toLowerCase();
                if (selectedCategories.length === 0 || selectedCategories.includes(cardCategory)) {
                    card.style.display = 'block';  // Show matching or all if none selected
                } else {
                    card.style.display = 'none';  // Hide non-matching
                }
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', filterVehicles);
        });
    });
</script>

<script>
    let currentVehicle = {};

    function openQuoteModal(vehicle) {
        currentVehicle = vehicle;
        document.getElementById('quotePopup').style.display = 'flex';
        document.getElementById('popupVehicleName').textContent = vehicle.name;
        document.getElementById('popupMessage').textContent = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const popup = document.getElementById('quotePopup');
        const closeBtn = document.querySelector('.close-btn');
        const quoteForm = document.getElementById('quoteForm');

        // Close popup
        closeBtn.addEventListener('click', () => popup.style.display = 'none');
        window.addEventListener('click', (e) => { if (e.target === popup) popup.style.display = 'none'; });

        // Handle form submission and AJAX
        quoteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            const formData = new FormData(this);
            formData.append('vehicle_id', currentVehicle.id);
            formData.append('action', 'save_lead');

            fetch('https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Quote request submitted successfully!',
                        icon: 'success',
                        confirmButtonColor: '#ec268f'
                    }).then(() => {
                        quoteForm.reset();
                        popup.style.display = 'none';
                    });
                } else {
                    throw new Error(data.message || 'Submission failed');
                }
            })
            .catch(error => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                console.error(error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Error submitting request. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#ec268f'
                });
            });
        });
    });
</script>

    
</body>
</html>