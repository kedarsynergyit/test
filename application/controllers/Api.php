<?php
class Api extends CI_Controller{
	
	/**
	 * Constructor
	 */
	public function __construct(){
		parent::__construct();
		
		// set variables for CORS
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}
		
		date_default_timezone_set("America/Toronto");
        if (!ini_get('date.timezone')) {
            date_default_timezone_set("America/Toronto");
        }
	}
	
	/**
	 * Main function to call
	 */
	public function request(){
		$log_date=date("Y-m-d");
		$log_date_time=date("Y-m-d H:i:s");
		
		$request_input_stream = array();
		$body = @file_get_contents('php://input');
		
		if(!empty($body)) {
			$request_input_stream = json_decode($body, true);
			
			// logging
			@error_log("\n------Request ".$log_date_time."---------\n".$body, 3, "./logs/".$log_date."logfile.log");
		}
		
		$objRequestArray = array();
		$response_array = array();
		$is_array = true;
		
		$objRequestArray = $request_input_stream;
		
		$jsonrpc = $objRequestArray['jsonrpc'];
		$id = $objRequestArray['id'];
		$method = $objRequestArray['method'];
		if(empty($jsonrpc) || empty($id) || empty($method)){
			$response_array['error']['code'] = 401;
			if(empty($jsonrpc)){
				$response_array["error"]["message"] = "Required parameter 'jsonrpc' is missing!";
			}else if(empty($id)){
				$response_array["error"]["message"] = "Required parameter 'id' is missing!";
			}else if(empty($method)){
				$response_array["error"]["message"] = "Required parameter 'method' is missing!";
			}
		}else{
			// set default response variables.
			$response_array['jsonrpc'] = $jsonrpc;
			$response_array['id'] = $id;
			
			// check which method requested and call function based on it.
			if($method=="signin"){
				$response = $this->signin($objRequestArray,$jsonrpc,$id);
			}else if($method=="forgotPassword"){
				$response = $this->forgotPassword($objRequestArray,$jsonrpc,$id);
			}else if($method=="listProjects"){
				$response = $this->listProjects($objRequestArray,$jsonrpc,$id);
			}else if($method=="addProject"){
				$response = $this->addProject($objRequestArray,$jsonrpc,$id);
			}else if($method=="getProjectDetails"){
				$response = $this->getProjectDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="updateProject"){
				$response = $this->updateProject($objRequestArray,$jsonrpc,$id);
			}else if($method=="listTickets"){
				$response = $this->listTickets($objRequestArray,$jsonrpc,$id);
			}else if($method=="addTicket"){
				$response = $this->addTicket($objRequestArray,$jsonrpc,$id);
			}else if($method=="updateTicket"){
				$response = $this->updateTicket($objRequestArray,$jsonrpc,$id);
			}else if($method=="listTicketCategories"){
				$response = $this->listTicketCategories($objRequestArray,$jsonrpc,$id);
			}else if($method=="addTicketCategory"){
				$response = $this->addTicketCategory($objRequestArray,$jsonrpc,$id);
			}else if($method=="getCategoryDetails"){
				$response = $this->getCategoryDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="listTicketComments"){
				$response = $this->listTicketComments($objRequestArray,$jsonrpc,$id);
			}else if($method=="addTicketComments"){
				$response = $this->addTicketComments($objRequestArray,$jsonrpc,$id);
			}else if($method=="listInvoices"){
				$response = $this->listInvoices($objRequestArray,$jsonrpc,$id);
			}else if($method=="listPaymentHistory"){
				$response = $this->listPaymentHistory($objRequestArray,$jsonrpc,$id);
			}else if($method=="addInvoice"){
				$response = $this->addInvoice($objRequestArray,$jsonrpc,$id);
			}else if($method=="getInvoiceDetails"){
				$response = $this->getInvoiceDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="updateInvoice"){
				$response = $this->updateInvoice($objRequestArray,$jsonrpc,$id);
			}else if($method=="listProfiles"){
				$response = $this->listProfiles($objRequestArray,$jsonrpc,$id);
			}else if($method=="listMessagesInbox"){
				$response = $this->listMessagesInbox($objRequestArray,$jsonrpc,$id);
			}else if($method=="listMessagesSent"){
				$response = $this->listMessagesSent($objRequestArray,$jsonrpc,$id);
			}else if($method=="addMessage"){
				$response = $this->addMessage($objRequestArray,$jsonrpc,$id);
			}else if($method=="getSettings"){
				$response = $this->getSettings($objRequestArray,$jsonrpc,$id);
			}else if($method=="saveSettings"){
				$response = $this->saveSettings($objRequestArray,$jsonrpc,$id);
			}else if($method=="dashboardCounters"){
				$response = $this->dashboardCounters($objRequestArray,$jsonrpc,$id);
			}else if($method=="getAccountManager"){
				$response = $this->getAccountManager($objRequestArray,$jsonrpc,$id);
			}else if($method=="getTech"){
				$response = $this->getTech($objRequestArray,$jsonrpc,$id);
			}else if($method=="getTickets"){
				$response = $this->getTickets($objRequestArray,$jsonrpc,$id);
			}else if($method=="listTicketNotifications"){
				$response = $this->listTicketNotifications($objRequestArray,$jsonrpc,$id);
			}else if($method=="getListToSendMessage"){
				$response = $this->getListToSendMessage($objRequestArray,$jsonrpc,$id);
			}else if($method=="getProfileReports"){
				$response = $this->getProfileReports($objRequestArray,$jsonrpc,$id);
			}else if($method=="getProjectReports"){
				$response = $this->getProjectReports($objRequestArray,$jsonrpc,$id);
			}else if($method=="getTicketReports"){
				$response = $this->getTicketReports($objRequestArray,$jsonrpc,$id);
			}else if($method=="updateCategory"){
				$response = $this->updateCategory($objRequestArray,$jsonrpc,$id);
			}else if($method=="getTicketDetails"){
				$response = $this->getTicketDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="listWorkorders"){
				$response = $this->listWorkorders($objRequestArray,$jsonrpc,$id);
			}else if($method=="listCompletedWorkorders"){
				$response = $this->listCompletedWorkorders($objRequestArray,$jsonrpc,$id);
			}else if($method=="getWorkorderDetails"){
				$response = $this->getWorkorderDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="updateWorkorder"){
				$response = $this->updateWorkorder($objRequestArray,$jsonrpc,$id);
			}else if($method=="addWorkorderComments"){
				$response = $this->addWorkorderComments($objRequestArray,$jsonrpc,$id);
			}else if($method=="getCustomers"){
				$response = $this->getCustomers($objRequestArray,$jsonrpc,$id);
			}else if($method=="getCustomerCompany"){
				$response = $this->getCustomerCompany($objRequestArray,$jsonrpc,$id);
			}else if($method=="getInboxMessageDetails"){
				$response = $this->getInboxMessageDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="getSentMessageDetails"){
				$response = $this->getSentMessageDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="removeWorkorderFiles"){
				$response = $this->removeWorkorderFiles($objRequestArray,$jsonrpc,$id);
			}else if($method=="removeUserFiles"){
				$response = $this->removeUserFiles($objRequestArray,$jsonrpc,$id);
			}else if($method=="markMessageAsRead"){
				$response = $this->markMessageAsRead($objRequestArray,$jsonrpc,$id);
			}else if($method=="getProjects"){
				$response = $this->getProjects($objRequestArray,$jsonrpc,$id);
			}else if($method=="responseTimeGraph"){
				$response = $this->responseTimeGraph($objRequestArray,$jsonrpc,$id);
			}else if($method=="resolutionTimeGraph"){
				$response = $this->resolutionTimeGraph($objRequestArray,$jsonrpc,$id);
			}else if($method=="getProfileDetails"){
				$response = $this->getProfileDetails($objRequestArray,$jsonrpc,$id);
			}else if($method=="updateProfile"){
				$response = $this->updateProfile($objRequestArray,$jsonrpc,$id);
			}else if($method=="addProfile"){
				$response = $this->addProfile($objRequestArray,$jsonrpc,$id);
			}else {
				$response['error']['code'] = 401;
				$response["error"]["message"] = "Requested method was not found!";
		 	}
		}
	 	
		$response_array = array_merge($response_array,$response);
	 	
	 	$json_output = json_encode($response_array);
		if(mb_detect_encoding($json_output, 'UTF-8', true) === FALSE) {
			$json_output = utf8_encode($json_output);
		}
		
		// send the response
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: text/json');
		header('Content-type: application/json');
		header('Content-encoding: zip');
		header("Content-length: ".strlen($json_output));
		echo $json_output;
	}
	
	/**
	 * METHOD : signin
	 * this webservice will be used for customer, admin, account manager/employee(internal user) and tech(external user) login
	 */
	public function signin($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['email']) || empty($objRequestArray['params']['email'])){
			$required_params[]="email";
		}
		if(!isset($objRequestArray['params']['password']) || empty($objRequestArray['params']['password'])){
			$required_params[]="password";
		}
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->signin($objRequestArray['params']);
		}
		
		return $response_array;
	}
	
	/**
	 * METHOD : forgotPassword
	 * This webservice will be used to reset the password.
	 */
	public function forgotPassword($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['email']) || empty($objRequestArray['params']['email'])){
			$required_params[]="email";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->forgotPassword($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listProjects
	 * This webservice will be used to get the list of projects.
	 */
	public function listProjects($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listProjects($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addProject
	 * This webservice will be used to add a new project.
	 * Add Project will be available for Employee
	 */
	public function addProject($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['projectDetails']['title']) || empty($objRequestArray['params']['projectDetails']['title'])){
			$required_params[]="title";
		}
		if(!isset($objRequestArray['params']['projectDetails']['start_date']) || empty($objRequestArray['params']['projectDetails']['start_date'])){
			$required_params[]="start_date";
		}
		if(!isset($objRequestArray['params']['projectDetails']['end_date']) || empty($objRequestArray['params']['projectDetails']['end_date'])){
			$required_params[]="end_date";
		}
		if(!isset($objRequestArray['params']['projectDetails']['customer_id']) || empty($objRequestArray['params']['projectDetails']['customer_id'])){
			$required_params[]="customer_id";
		}
		if(!isset($objRequestArray['params']['projectDetails']['accountmanager_id']) || empty($objRequestArray['params']['projectDetails']['accountmanager_id'])){
			$required_params[]="accountmanager_id";
		}
		if(!isset($objRequestArray['params']['projectDetails']['tech_leads']) || empty($objRequestArray['params']['projectDetails']['tech_leads'])){
			$required_params[]="tech_leads";
		}
		if(!isset($objRequestArray['params']['projectDetails']['description']) || empty($objRequestArray['params']['projectDetails']['description'])){
			$required_params[]="description";
		}
		if(!isset($objRequestArray['params']['projectDetails']['project_status']) || empty($objRequestArray['params']['projectDetails']['project_status'])){
			$required_params[]="project_status";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addProject($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getProjectDetails
	 * This webservice will be used to get details of a project.
	 * get Project details will be available for Employee
	 */
	public function getProjectDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['project_id']) || empty($objRequestArray['params']['project_id'])){
			$required_params[]="project_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getProjectDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : updateProject
	 * This webservice will be used to update details of a project.
	 * Update Project will be available for Employee
	 */
	public function updateProject($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['projectDetails']['project_id']) || empty($objRequestArray['params']['projectDetails']['project_id'])){
			$required_params[]="project_id";
		}
		if(!isset($objRequestArray['params']['projectDetails']['title']) || empty($objRequestArray['params']['projectDetails']['title'])){
			$required_params[]="title";
		}
		if(!isset($objRequestArray['params']['projectDetails']['start_date']) || empty($objRequestArray['params']['projectDetails']['start_date'])){
			$required_params[]="start_date";
		}
		if(!isset($objRequestArray['params']['projectDetails']['end_date']) || empty($objRequestArray['params']['projectDetails']['end_date'])){
			$required_params[]="end_date";
		}
		if(!isset($objRequestArray['params']['projectDetails']['tech_leads']) || empty($objRequestArray['params']['projectDetails']['tech_leads'])){
			$required_params[]="tech_leads";
		}
		if(!isset($objRequestArray['params']['projectDetails']['description']) || empty($objRequestArray['params']['projectDetails']['description'])){
			$required_params[]="description";
		}
		if(!isset($objRequestArray['params']['projectDetails']['project_status']) || empty($objRequestArray['params']['projectDetails']['project_status'])){
			$required_params[]="project_status";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->updateProject($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listTickets
	 * This webservice will be used to get the list of projects.
	 */
	public function listTickets($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listTickets($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addTicket
	 * This webservice will be used to get the list of projects.
	 */
	public function addTicket($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addTicket($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : updateTicket
	 * This webservice will be used to update a ticket
	 * It is available for only Employee and Tech
	 */
	public function updateTicket($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['ticket_details'])){
			$required_params[]="ticket_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->updateTicket($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listTicketCategories
	 * This webservice will be used to get the list of Ticket Categories.
	 */
	public function listTicketCategories($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listTicketCategories($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getCategoryDetails
	 * This webservice will be used to get the details of Ticket Categories.
	 */
	public function getCategoryDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getCategoryDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addTicketCategory
	 * This webservice will be used to get add Ticket Categories.
	 */
	public function addTicketCategory($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addTicketCategory($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listInvoices
	 * This webservice will be used to get the list of invoices.
	 */
	public function listInvoices($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listInvoices($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : paymentHistory
	 * This webservice will be used to get the list of payment history.
	 */
	public function listPaymentHistory($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listPaymentHistory($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getInvoiceDetails
	 * This webservice will be used to get the invoice details.
	 */
	public function getInvoiceDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['invoice_id'])){
			$required_params[]="invoice_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getInvoiceDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addInvoice
	 * This webservice will be used to add the invoice.
	 */
	public function addInvoice($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addInvoice($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : updateInvoice
	 * This webservice will be used to update the invoice.
	 */
	public function updateInvoice($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['invoice_details'])){
			$required_params[]="invoice_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->updateInvoice($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listProfiles
	 * This webservice will be used to get the list of profiles.
	 */
	public function listProfiles($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['type'])){
			$required_params[]="type";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listProfiles($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listMessagesInbox
	 * This webservice will be used to get the list of messages received.
	 */
	public function listMessagesInbox($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listMessagesInbox($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listMessagesSent
	 * This webservice will be used to get the list of messages sent.
	 */
	public function listMessagesSent($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listMessagesSent($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addMessage
	 * This webservice will be used to get the list of messages sent.
	 */
	public function addMessage($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['message_details'])){
			$required_params[]="message_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addMessage($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : saveSettings
	 * This webservice will be used to get the list of messages sent.
	 */
	public function saveSettings($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['profile_details'])){
			$required_params[]="profile_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->saveSettings($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getSettings
	 * This webservice will be used to get the list of messages sent.
	 */
	public function getSettings($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getSettings($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : dashboardCounters
	 * This webservice will be used to get the counts on dashboard
	 */
	public function dashboardCounters($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->dashboardCounters($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getAccountManager
	 * This webservice will be used to get the account manager associated with the customer id
	 */
	public function getAccountManager($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['fk_customer_id'])){
			$required_params[]="fk_customer_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getAccountManager($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getTech
	 * This webservice will be used to get the tech list
	 */
	public function getTech($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		/*if(!isset($objRequestArray['params']['project_id'])){
			$required_params[]="project_id";
		}*/
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getTech($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listTicketComments
	 * This webservice will be used to get the list of Ticket Comments.
	 */
	public function listTicketComments($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['ticket_id'])){
			$required_params[]="ticket_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listTicketComments($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addTicketComments
	 * This webservice will be used to get the list of Ticket Comments.
	 */
	public function addTicketComments($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['comment_details'])){
			$required_params[]="comment_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addTicketComments($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getTickets
	 * This webservice will be used to get the tickets associated with the customer id
	 */
	public function getTickets($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['customer_id'])){
			$required_params[]="customer_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getTickets($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listTicketNotifications
	 * This webservice will be used to get the list of projects.
	 */
	public function listTicketNotifications($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listTicketNotifications($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getListToSendMessage
	 * This webservice will be used to get the list of projects.
	 */
	public function getListToSendMessage($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getListToSendMessage($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getProfileReports
	 * This webservice will be used to get row data for reports
	 */
	public function getProfileReports($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getProfileReports($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getProjectReports
	 * This webservice will be used to get row data for reports
	 */
	public function getProjectReports($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getProjectReports($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getTicketReports
	 * This webservice will be used to get row data for reports
	 */
	public function getTicketReports($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getTicketReports($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : updateCategory
	 * This webservice will be used to update a ticket category
	 * It is available for only Employee
	 */
	public function updateCategory($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['category_details'])){
			$required_params[]="category_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->updateCategory($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getTicketDetails
	 * This webservice will be used to get a ticket details
	 * It is available for only Employee and Tech
	 */
	public function getTicketDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['ticket_id'])){
			$required_params[]="ticket_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getTicketDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listWorkorders
	 * This webservice will be used to get the list of open workorders.
	 */
	public function listWorkorders($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listWorkorders($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : listCompletedWorkorders
	 * This webservice will be used to get the list of completed workorders.
	 */
	public function listCompletedWorkorders($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->listCompletedWorkorders($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getWorkorderDetails
	 * This webservice will be used to get the details of workorders
	 */
	public function getWorkorderDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getWorkorderDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : updateWorkorder
	 * This webservice will be used to get the details of workorders
	 */
	public function updateWorkorder($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['workorder_details'])){
			$required_params[]="workorder_details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->updateWorkorder($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : addWorkorderComments
	 * This webservice will be used to add workorder comment
	 */
	public function addWorkorderComments($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['workorder_id'])){
			$required_params[]="workorder_id";
		}
		if(!isset($objRequestArray['params']['comments'])){
			$required_params[]="comments";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addWorkorderComments($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getCustomers
	 * This webservice will be used to get list of customers
	 */
	public function getCustomers($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getCustomers($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getCustomers
	 * This webservice will be used to get list of customers
	 */
	public function getCustomerCompany($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getCustomerCompany($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getMessageDetails
	 * This webservice will be used to get message details
	 */
	public function getInboxMessageDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['message_id'])){
			$required_params[]="message_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getInboxMessageDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getSentMessageDetails
	 * This webservice will be used to get message details
	 */
	public function getSentMessageDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['message_id'])){
			$required_params[]="message_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getSentMessageDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : removeWorkorderFiles
	 * This webservice will be used to delete workorder files for signed wo, tech invoice, added info
	 */
	public function removeWorkorderFiles($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['type'])){
			$required_params[]="type";
		}
		if(!isset($objRequestArray['params']['file_id'])){
			$required_params[]="file_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->removeWorkorderFiles($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : removeUserFiles
	 * This webservice will be used to delete user files
	 */
	public function removeUserFiles($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['file_id'])){
			$required_params[]="file_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->removeUserFiles($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : markMessageAsRead
	 * This webservice will be used to delete user files
	 */
	public function markMessageAsRead($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['message_id'])){
			$required_params[]="message_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->markMessageAsRead($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getProjects
	 * This webservice will be used to get list of projects for dropdown
	 */
	public function getProjects($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getProjects($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : responseTimeGraph
	 * This webservice will be used to get row data for reports
	 */
	public function responseTimeGraph($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->responseTimeGraph($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : resolutionTimeGraph
	 * This webservice will be used to get row data for reports
	 */
	public function resolutionTimeGraph($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
			
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->resolutionTimeGraph($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * METHOD : getProfileDetails
	 * This webservice will be used to get profile details for Employees/Tech/Customer/CustomerCompany
	 */
	public function getProfileDetails($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['type'])){
			$required_params[]="type";
		}
		
		if(!isset($objRequestArray['params']['profile_id'])){
			$required_params[]="profile_id";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->getProfileDetails($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * updateProfile
	 */
	public function updateProfile($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['type'])){
			$required_params[]="type";
		}
		
		if(!isset($objRequestArray['params']['details'])){
			$required_params[]="details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->updateProfile($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
	
	/**
	 * addProfile
	 */
	public function addProfile($objRequestArray,$jsonrpc,$id){
		// load model
		$this->load->model("apimodel");
		
		// common variables to be used in signin function
		$response_array = array();
		
		// check required parameters
		$process = 1;
		$required_params = array();
		if(!isset($objRequestArray['params']['id']) || empty($objRequestArray['params']['id'])){
			$required_params[]="id";
		}
		if(!isset($objRequestArray['params']['is_customer'])){
			$required_params[]="is_customer";
		}
		if((isset($objRequestArray['params']['is_customer']) && empty($objRequestArray['params']['is_customer'])) && (!isset($objRequestArray['params']['internal_user_external_user']) || empty($objRequestArray['params']['internal_user_external_user']))){
			$required_params[]="internal_user_external_user";
		}
		if(!isset($objRequestArray['params']['type'])){
			$required_params[]="type";
		}
		
		if(!isset($objRequestArray['params']['details'])){
			$required_params[]="details";
		}
		
		if(!empty($required_params)){
			$process=0;
			
			$response_array['error']['code'] = 401;
			$response_array["error"]["message"] = "Required parameter missing : ".implode(",", $required_params);
		}
		
		if($process){
			// call the function
			$response_array = $this->apimodel->addProfile($objRequestArray['params']);
		}
		
		return $response_array;
		
	}
}