
<?php

function curlRequest($data) {
    $api_url = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
    
    // Log the request data
    // log_message("Sending to API: " . print_r($data, true));
    
    $curl = curl_init($api_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Only for debugging, remove in production
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Only for debugging, remove in production
    
    $response = curl_exec($curl);
    
    // Log any cURL errors
    if (curl_errno($curl)) {
        $curlError = curl_error($curl);
        // log_message("cURL Error: " . $curlError, 'ERROR');
    }
    
    // Log the raw response
    // log_message("API Response: " . $response);
    
    curl_close($curl);
    
    $decoded = json_decode($response, true);
    
    // Log if JSON decode failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        $jsonError = json_last_error_msg();
        // log_message("JSON Decode Error: " . $jsonError, 'ERROR');
    }
    
    return $decoded;
}

function fetchAllUserLeads($data){
    $data["action"] = "fetchAllUserLeads";
    return curlRequest($data);
}
function fetchAllTeamMembers($data){
    $data["action"] = "fetchAllTeamMembers";
    return curlRequest($data);
}
function fetchSingleLead($data){
    $data["action"] = "fetchSingleLead";
    return curlRequest($data);
}
function updateLead($data){
    $data["action"] = "update_lead";
    return curlRequest($data);
}
function userLogin($data){
    $data["action"] = "user_login";
    return curlRequest($data);
}
function fetchRoles(){
    $data["action"] = "fetch_roles";
    return curlRequest($data);
}
function fetchEmailTemplates(){
    $data["action"] = "fetch_email_templates";
    return curlRequest([]);
}
function getEmailTemplate($data){
    $data["action"] = "get_email_template";
    return curlRequest($data);
}
function saveEmailTemplate($data){
    $data["action"] = "save_email_template";
    return curlRequest($data);
}
function deleteEmailTemplate($data){
    $data["action"] = "delete_email_template";
    return curlRequest($data);
}
function fetchTasks($data){
    $data["action"] = "fetch_tasks";
    return curlRequest($data);
}
function saveTask($data){
    $data["action"] = "save_task";
    return curlRequest($data);
}
function updateTaskStatus($data){
    $data["action"] = "update_task_status";
    return curlRequest($data);
}
function deleteTask($data){
    $data["action"] = "delete_task";
    return curlRequest($data);
}
function fetchVehicles($data){
    $data["action"] = "fetch_vehicles";
    return curlRequest($data);
}
function saveVehicle($data){
    $data["action"] = "save_vehicle";
    return curlRequest($data);
}
function getVehicle($data){
    $data["action"] = "get_vehicle";
    return curlRequest($data);
}
function deleteVehicle($data){
    $data["action"] = "delete_vehicle";
    return curlRequest($data);
}