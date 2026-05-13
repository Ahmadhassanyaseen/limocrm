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

function fetchAllLeads($data){
    $data["action"] = "fetchAllLeads";
    return curlRequest($data);
}
function fetchAllUserLeads($data){
    $data["action"] = "fetchAllUserLeads";
    return curlRequest($data);
}

function createNote($data){
    $data["action"] = "createNote";
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
function updateLeadAfterPayment($data){
    $data["action"] = "update_lead_after_payment";
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
function fetch_workflows($data){
    $data["action"] = "fetch_workflows";
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
function fetchPricingDefaults($data) {
    $data["action"] = "fetch_pricing_defaults";
    return curlRequest($data);
}
function savePricingDefaults($data) {
    $data["action"] = "save_pricing_defaults";
    return curlRequest($data);
}
function updateVehiclePricing($data) {
    $data["action"] = "update_vehicle_pricing";
    return curlRequest($data);
}
function deleteworkflow($data){
    $data["action"] = "delete_workflow";
    return curlRequest($data);
}

function create_role($data){
    $data["action"] = "create_role";
    return curlRequest($data);
}
function fetch_roles($data){
    $data["action"] = "fetch_roles";
    return curlRequest($data);
}
function delete_role($data){
    $data["action"] = "delete_role";
    return curlRequest($data);
}
function update_role($data){
    $data["action"] = "update_role";
    return curlRequest($data);
}
function get_module_template($data){
    $data["action"] = "get_module_template";
    return curlRequest($data);
}
function getUserIdByEmail($data){
    $data["action"] = "getUserIdByEmail";
    return curlRequest($data);
}
function fetchCurrentUserPermissions($data = []) {
    $data["action"] = "fetch_current_user_permissions";
    return curlRequest($data);
}

function create_user($data = []) {
    $data["action"] = "create_user";
    return curlRequest($data);
}

function delete_user($data = []) {
    $data["action"] = "delete_user";
    return curlRequest($data);
}

function update_user($data = []) {
    $data["action"] = "update_user";
    return curlRequest($data);
}

function sendLeadEmail($data) {
    $data["action"] = "send_lead_email";
    return curlRequest($data);
}

function sendFormalQuoteEmail($data) {
    $id = trim((string)($data['id'] ?? $data['lead_id'] ?? ''));
    return curlRequest([
        'action' => 'send_formal_quote_email',
        'id'     => $id,
    ]);
}

function sendAgreementEmail($data) {
    $data["action"] = "send_agreement_email";
    return curlRequest($data);
}

function saveLead($data) {
    $data["action"] = "save_lead";
    return curlRequest($data);
}

function fetchLeadEmails($data) {
    $data["action"] = "fetch_lead_emails";
    return curlRequest($data);
}

function fetchSingleEmail($data) {
    $data["action"] = "fetch_single_email";
    return curlRequest($data);
}

function fetchUserEmailAnalytics($data) {
    $data["action"] = "fetch_user_email_analytics";
    return curlRequest($data);
}

function fetchNotes($data) {
    $data["action"] = "fetch_notes";
    return curlRequest($data);
}

function saveNote($data) {
    $data["action"] = "save_note";
    return curlRequest($data);
}

function updateNoteApi($data) {
    $data["action"] = "update_note";
    return curlRequest($data);
}

function deleteNote($data) {
    $data["action"] = "delete_note";
    return curlRequest($data);
}

function fetchContacts($data) {
    $data["action"] = "fetch_contacts";
    return curlRequest($data);
}

function fetchContactsList($data) {
    $data["action"] = "fetch_contacts_list";
    return curlRequest($data);
}

function fetchContactDetail($data) {
    $data["action"] = "fetch_contact_detail";
    return curlRequest($data);
}

function saveContactRecord($data) {
    $data["action"] = "save_contact";
    return curlRequest($data);
}

function updateContactRecord($data) {
    $data["action"] = "update_contact";
    return curlRequest($data);
}

function deleteContactRecord($data) {
    $data["action"] = "delete_contact";
    return curlRequest($data);
}

function fetchEmbeddedDomains($data) {
    $data["action"] = "fetch_embedded_domains";
    return curlRequest($data);
}

/** Public signing page preview (validated by token on server). */
function fetchAgreementLeadData($data) {
    $data["action"] = "fetch_agreement_lead";
    return curlRequest($data);
}

/** Payment + signature persisted in SuiteCRM (Stripe PaymentMethod id from Stripe.js). */
function submitAgreementPayment($data) {
    $data["action"] = "submit_agreement";
    return curlRequest($data);
}

/** Staff-only: signed URL for agreement.php ?l=&t= */
function getAgreementSigningLink($data) {
    $data["action"] = "get_agreement_signing_link";
    return curlRequest($data);
}

function fetchLeadStripeKey($data) {
    $data["action"] = "fetch_lead_stripe_key";
    return curlRequest($data);
}

function fetchUserStripeKeys($data) {
    $data["action"] = "fetch_user_stripe_keys";
    return curlRequest($data);
}
function fetchPaymentMethods($data) {
    $data["action"] = "fetch_payment_methods";
    return curlRequest($data);
}

function saveUserStripeKeys($data) {
    $data["action"] = "save_user_stripe_keys";
    return curlRequest($data);
}

function deleteUserStripeKeys($data) {
    $data["action"] = "delete_user_stripe_keys";
    return curlRequest($data);
}

function saveUserPaymentPreference($data) {
    $data["action"] = "save_user_payment_preference";
    return curlRequest($data);
}

function saveUserPaypalKeys($data) {
    $data["action"] = "save_user_paypal_keys";
    return curlRequest($data);
}

function deleteUserPaypalKeys($data) {
    $data["action"] = "delete_user_paypal_keys";
    return curlRequest($data);
}

function fetchUserTransactions($data) {
    $data["action"] = "fetch_user_transactions";
    return curlRequest($data);
}

function fetchOutboundEmailAccounts($data = []) {
    $data["action"] = "fetch_outbound_email_accounts";
    return curlRequest($data);
}

function fetchOutboundEmailAccountDetail($data) {
    $data["action"] = "fetch_outbound_email_account_detail";
    return curlRequest($data);
}

function saveOutboundEmailAccount($data) {
    $data["action"] = "save_outbound_email_account";
    return curlRequest($data);
}

function deleteOutboundEmailAccount($data) {
    $data["action"] = "delete_outbound_email_account";
    return curlRequest($data);
}

function testOutboundEmailAccountConnection($data) {
    $data["action"] = "test_outbound_email_account_connection";
    return curlRequest($data);
}
