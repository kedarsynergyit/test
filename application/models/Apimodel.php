<?php
Class Apimodel extends CI_Model{
	
	/**
	 * SignIn
	 * @param $params
	 */
	function signin($params){
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		// now check if the login details matches in db, and send response accordingly
		$email = $params['email'];
		$password = $params['password'];
		
		// first check if the user is customer
		$this->db->select("*");
        $this->db->from("customer");
        $this->db->where('emailid', $email);
        $this->db->where('password', base64_encode($password));
        $this->db->where('status', '1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
        	$row = $query->row();
        	
        	// now check if the login lock is on or off.
            $thirty_minutes_from_lock_time = date("Y-m-d H:i:s",strtotime($row->login_locked_time." +30 minutes"));
            if($row->login_locked==1 && (($current_time<=$thirty_minutes_from_lock_time))){
            	// if login is locked and current login time is within/equals to thirty minutes from the time when login locked, then don't allow to login.
            	// Your login is blocked for 30 minutes, due to continuous 3 failed login attempts.
            	$response_array['error']['code']=401;
            	$response_array['error']['message'] = "Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts!";
            }else if($row->login_locked==1 && (($current_time>$thirty_minutes_from_lock_time))){
            	// set login_locked=0
            	$login_lock_data = array("login_locked"=>'0');
            	$login_lock_update = $this->portalmodel->update_query('customer', $login_lock_data, $row->customerid, 'customerid');
            }else{
            	// unset session variables if set for failed login attempt
	            if(isset($_SESSION['login_failed'][$row->customerid]['cnt'])){
	            	unset($_SESSION['login_failed'][$row->customerid]['cnt']);
	            	unset($_SESSION['login_failed'][$row->customerid]['time']);
	            }
	            
	            // now response
	            $response_array['result']['code'] = 200;				// 200 means status is OK. web service ran successfully without error
	            $response_array['result']['id'] = $row->customerid;		// id of the logged in customer
	            $response_array['result']['name'] = $row->first_name;	// name of customer
	            $response_array['result']['email'] = $row->emailid;		// email id of customer
	            $response_array['result']['profile_picture'] = (isset($row->profile_picture) && !empty($row->profile_picture))?base_url().$row->profile_picture:"";		// email id of customer
	            $response_array['result']['is_customer'] = 1;			// flag value for further use
            }
        }else{
        	/**
        	 * CHECKING CUSTOMER FAILED LOGIN ATTEMPT START
        	 */
        	$this->db->select("*");
	        $this->db->from("customer");
	        $this->db->where('emailid', $email);
	        $this->db->where('status', '1');
	        $query_check = $this->db->get();
	        if ($query_check->num_rows() > 0) {
	        	$row_check = $query_check->row();
	        	
	        	if(!isset($_SESSION['login_failed'][$row_check->customerid]['cnt']) || empty($_SESSION['login_failed'][$row_check->customerid]['cnt'])){
	            	// set session as 1
	            	$_SESSION['login_failed'][$row_check->customerid]['cnt']=1;
	            	
	            	// also set the time of first failed login attempt
	            	$_SESSION['login_failed'][$row_check->customerid]['time']=date("Y-m-d H:i:s");
	            	
	            	// return error that invalid email or password
		        	$response_array['error']['code']=401;
            		$response_array['error']['message'] = "Invalid email or password!";
	            }else if(!empty($_SESSION['login_failed'][$row_check->customerid]['cnt']) && $_SESSION['login_failed'][$row_check->customerid]['cnt']<3){
	            	// increase counter till it becomes 3
	            	$_SESSION['login_failed'][$row_check->customerid]['cnt'] = intval($_SESSION['login_failed'][$row_check->customerid]['cnt'])+1;
	            	
	            	if($_SESSION['login_failed'][$row_check->customerid]['cnt']==3){
	            		// now check if the time of first failed login and third failed login is not more than 5 minutes, then lock his account.
	            		$five_minutes_from_last_failed_login_attempt = date("Y-m-d H:i:s",strtotime($_SESSION['login_failed'][$row_check->customerid]['time']." +5 minutes"));
	            		if(($current_time<=$five_minutes_from_last_failed_login_attempt)){
	            			// its a continued 3rd failed login attempt. Block the login.
	            			$lock_login_data = array("login_locked"=>'1',"login_locked_time"=>$_SESSION['login_failed'][$row_check->customerid]['time']);
            				$lock_login_update = $this->portalmodel->update_query('customer', $lock_login_data, $row_check->customerid, 'customerid');
            				
            				// return error on third continuous failed login attempt
            				$response_array['error']['code']=401;
            				$response_array['error']['message'] = "Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts!";
	            		}
	            	}
	            }else if(!empty($_SESSION['login_failed'][$row_check->customerid]['cnt']) && $_SESSION['login_failed'][$row_check->customerid]['cnt']==3){
	            	// return error on third continuous failed login attempt
            		$response_array['error']['code']=401;
            		$response_array['error']['message'] = "Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts!";
	            }
	            // CHECKING FAILED LOGIN ATTEMPT ENDS
	        }else{
		        // check if the logged in user is employee
		        $this->db->select("*");
	        	$this->db->from("user");
		        $this->db->where('email', $email);
		        $this->db->where('password', base64_encode($password));
		        $this->db->where('status', '1');
	          	$query = $this->db->get();
	          	if ($query->num_rows() > 0) {
	          		$row = $query->row();
	          		
	          		// now check if the login lock is on or off.
		            $thirty_minutes_from_lock_time = date("Y-m-d H:i:s",strtotime($row->login_locked_time." +30 minutes"));
		            if($row->login_locked==1 && (($current_time<=$thirty_minutes_from_lock_time))){
		            	// if login is locked and current login time is within/equals to thirty minutes from the time when login locked, then don't allow to login.
		            	$response_array['error']['code']=401;
	            		$response_array['error']['message'] = "Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts!";
		            }else if($row->login_locked==1 && (($current_time>$thirty_minutes_from_lock_time))){
		            	// set login_locked=0
		            	$login_lock_data = array("login_locked"=>'0');
		            	$login_lock_update = $this->portalmodel->update_query('user', $login_lock_data, $row->id, 'id');
		            }else{
			            $response_array['result']['code'] = 200;							// 200 means status is OK. web service ran successfully without error
			            $response_array['result']['id'] = $row->id;
			            $response_array['result']['name'] = $row->first_name;
			            $response_array['result']['email'] = $row->email;
			            $response_array['result']['profile_picture'] = (isset($row->userprofile) && !empty($row->userprofile))?base_url().$row->userprofile:"";		// email id of customer
			            $response_array['result']['role'] = $row->role;
			            $response_array['result']['internal_user_external_user'] = $row->internal_user_external_user;
			            $response_array['result']['is_customer'] = 0;
		            }
	          	}else{
	          		/**
		        	 * CHECKING USER FAILED LOGIN ATTEMPT START
		        	 */
	          		$this->db->select("*");
			        $this->db->from("user");
			        $this->db->where('email', $email);
			        $this->db->where('status', '1');
			        $query_check = $this->db->get();
			        if ($query_check->num_rows() > 0) {
			        	$row_check = $query_check->row();
			        	
			        	if(!isset($_SESSION['login_failed'][$row_check->employeeid]['cnt']) || empty($_SESSION['login_failed'][$row_check->employeeid]['cnt'])){
			            	// set session as 1
			            	$_SESSION['login_failed'][$row_check->employeeid]['cnt']=1;
			            	
			            	// also set the time of first failed login attempt
			            	$_SESSION['login_failed'][$row_check->employeeid]['time']=date("Y-m-d H:i:s");
			            	
			            	// return error that invalid email or password
				        	$response_array['error']['code']=401;
		            		$response_array['error']['message'] = "Invalid email or password!";
			            }else if(!empty($_SESSION['login_failed'][$row_check->employeeid]['cnt']) && $_SESSION['login_failed'][$row_check->employeeid]['cnt']<3){
			            	// increase counter till it becomes 3
				            $_SESSION['login_failed'][$row_check->employeeid]['cnt'] = intval($_SESSION['login_failed'][$row_check->employeeid]['cnt'])+1;
				            
				            if($_SESSION['login_failed'][$row_check->employeeid]['cnt']==3){
				            	// now check if the time of first failed login and third failed login is not more than 5 minutes, then lock his account.
			            		$five_minutes_from_last_failed_login_attempt = date("Y-m-d H:i:s",strtotime($_SESSION['login_failed'][$row_check->employeeid]['time']." +5 minutes"));
			            		if(($current_time<=$five_minutes_from_last_failed_login_attempt)){
			            			// its a continued 3rd failed login attempt. Block the login.
			            			$lock_login_data = array("login_locked"=>'1',"login_locked_time"=>$_SESSION['login_failed'][$row_check->employeeid]['time']);
		            				$lock_login_update = $this->portalmodel->update_query('user', $lock_login_data, $row_check->id, 'id');
		            				
		            				// return error
		            				$response_array['error']['code']=401;
	            					$response_array['error']['message'] = "Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts!";
			            		}
				            }
			            }else if(!empty($_SESSION['login_failed'][$row_check->employeeid]['cnt']) && $_SESSION['login_failed'][$row_check->employeeid]['cnt']==3){
			            	// return error
		            		$response_array['error']['code']=401;
	            			$response_array['error']['message'] = "Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts!";
			            }
			        }else{
			        	// return error that invalid email or password
			        	$response_array['error']['code']=401;
	            		$response_array['error']['message'] = "Invalid email or password!";
			        }
			        // CHECKING FAILED LOGIN ATTEMPT ENDS
	          	}
	        }
	        
        }
        
        return $response_array;
	}
	
	/**
	 * forgotPassword
	 */
	function forgotPassword($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$email = $params['email'];
		
		if(!empty($email)){
			// get user id from email for customer
	    	$condition = "emailid = '".$email."'";
	    	$user_id = $this->portalmodel->select_name('customer', 'customerid', $condition);
	    	
	    	if(!empty($user_id) && $user_id!='N/A'){
	    		$user_name = $this->portalmodel->select_name('customer', 'first_name', "customerid='".$user_id."'");
	    		$business_email = $this->portalmodel->select_name('customer', 'business_email', "customerid='".$user_id."'");
	    		
	    		// send email for reset password link
	    		$param = md5("customerid")."=".md5($user_id);
	    		$link = base_url('index.php/customerportal/resetpassword/?'.$param);
	    		
	    		$to = $email;
	    		$subject = "SynergyInteract : Reset Password!";
	    		$message = "Hello ".$user_name.",<br /><br />";
	    		$message .= "We have received your request to reset your password!<br /><br />";
	    		$message .= "Click on below link or copy and paste it in browser to reset your password.<br /><br />";
	    		$message .= "<a href=".$link." target='_blank'>".$link."</a><br /><br />";
	    		$message .= "Thank You<br />- Synergy IT Team.";
	    		
	    		if($this->email($to, $subject, $message)){
		    		if(!empty($business_email) && $business_email!='N/A'){
		    			$this->email($business_email, $subject, $message);
		    		}
		    		
		    		$response_array['result']['code'] = 200;	// 200 means status is OK. web service ran successfully without error
		    		$response_array['result']['message'] = "We have sent you an email to reset your password!";
	    		}else{
	    			// return error
					$response_array['error']['code']=503;
		            $response_array['error']['message'] = "Error sending email. Please try again later!";
	    		}
	    	}else{
	    		// check in user table
	    		$condition = "email = '".$email."'";
			    $user_id = $this->portalmodel->select_name('user', 'id', $condition);
			    
			    if(!empty($user_id) && $user_id!='N/A'){
			    	$user_name = $this->portalmodel->select_name('user', 'first_name', "id='".$user_id."'");
			    	$business_email = $this->portalmodel->select_name('user', 'business_email', "id='".$user_id."'");
			    	
			    	// send email for reset password link
		    		$param = md5("user_id")."=".md5($user_id);
		    		$link = base_url('index.php/customerportal/resetpassword/?'.$param);
		    		
		    		$to = $email;
		    		$subject = "SynergyInteract : Reset Password!";
		    		$message = "Hello ".$user_name.",<br /><br />";
		    		$message .= "We have received your request to reset your password!<br /><br />";
		    		$message .= "Click on below link or copy and paste it in browser to reset your password.<br /><br />";
		    		$message .= "<a href=".$link." target='_blank'>".$link."</a><br /><br />";
		    		$message .= "Thank You<br />- Synergy IT Team.";
		    		
		    		if($this->email($to, $subject, $message)){
		    			if(!empty($business_email) && $business_email!='N/A'){
			    			$this->email($business_email, $subject, $message);
			    		}
			    		
			    		$response_array['result']['code'] = 200;	// 200 means status is OK. web service ran successfully without error
		    			$response_array['result']['message'] = "We have sent you an email to reset your password!";
		    		}else{
		    			// return error
						$response_array['error']['code']=503;
			            $response_array['error']['message'] = "Error sending email. Please try again later!";
		    		}
			    }else{
			    	// return error
					$response_array['error']['code']=401;
		            $response_array['error']['message'] = "Email is not registered!";
			    }
	    	}
		}else{
			// return error
			$response_array['error']['code']=401;
            $response_array['error']['message'] = "Please provide valid email!";
		}
		
		return $response_array;
	}
	
	/**
	 * listProjects
	 */
	function listProjects($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$li = array();
		$index_key="A";
		if($is_customer){
			// get projects list for customer
			
			// select company
	        $where_company = " customerid='$id'";
	        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
			
			$where = "customerid=$company_id";
			$list = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
			
			if (!empty($list)) {
				foreach ($list as $d) {
					
					// project details
					$row['id'] = $d->id;
					$row['title'] = $d->title;
					$row['details'] = $d->details;
					$row['start_date'] = date('Y-m-d', strtotime($d->created_on));
					if ($d->end_date == "" || $d->end_date == '0000-00-00') {
		                $row['end_date'] = "--";
		            } else {
		                $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
		            }
		            
		            $status = $d->status;
					if ($status == 1) {
		                $row['status'] = 'Open';
		            }
		            if ($status == 2) {
		                $row['status'] = 'Closed';
		            }
		            
		            
		            // count of tasks associated with project
		            $where1 = "projectid=$d->id and show_customer=0";
		            $row['nooftask'] = $this->portalmodel->record_count_where('task', $where1);
		            
		            // now also get open tasks count and completed task count
		            $row['open_task_count'] = $this->portalmodel->record_count_where('task', "projectid=$d->id and show_customer=0 and status IN (0,1,2,3)");
		            $row['completed_task_count'] = $this->portalmodel->record_count_where('task', "projectid=$d->id and show_customer=0 and status=4");
		            
		            $li[$index_key] = $row;
		            $index_key++;
				}
			}
		}else{
			
			$user_role = $this->portalmodel->select_name('user', 'role', "id='".$id."'");
			
			if($user_role==1){
				// its admin
				$list = $this->portalmodel->select('', '', 'project','','id','DESC');
				
				if(!empty($list)){
	            	foreach ($list as $d) {
	            		$row['id'] = $d->id;
	            		$coust = "id=".$d->customerid;
	            		$row['customer'] = $this->portalmodel->select_name('customer_company', 'name', $coust);
	            		$row['title'] = $d->title;
	            		$row['details'] = $d->details;
	            		$row['start_date'] = date('Y-m-d', strtotime($d->created_on));
		            	if ($d->end_date == "" || $d->end_date == '0000-00-00') {
		                    $row['end_date'] = "--";
		                } else {
		                    $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
		                }
		                
		                $where1 = "projectid=$d->id ";
		                $row['nooftask'] = $this->portalmodel->record_count_where('task', $where1);
		                
		                $role = "";
		                
		            	if ($d->accountmanager == $id) {
		                    $role = "Account Manager";
		                }
		            	if ($d->projectmanager == $id) {
		                    if ($role == "")
		                        $role = "Project Manager";
		                    else
		                        $role = $role . "," . "Project Manager";
		                }
		                $developer = explode(',', $d->developer);
		                $r = "";
		            	if (!empty($developer)) {
		                    foreach ($developer as $dev) {
		                        $r[] = $dev;
		                    }
		                }
		                if (in_array($id, $r)) {
		                    if ($role === "")
								$role = "Techs";
			                else
								$role = $role . "," . "Techs";
		                }
		                $row['role'] = $role;
		                
		                $status = $d->status;
	            		if ($status == 1) {
			                $row['status'] = 'Open';
			            }
			            if ($status == 2) {
			                $row['status'] = 'Closed';
			            }
			            
			            $li[$index_key] = $row;
			            $index_key++;
	            	}
	            }
			}else{
				// its employee/tech
				
				// get projects list for employee
				$where = "(accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ) or FIND_IN_SET( $id, developer ) ";
	            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
	            
	            if(!empty($list)){
	            	foreach ($list as $d) {
	            		$row['id'] = $d->id;
	            		$coust = "id=".$d->customerid;
	            		$row['customer'] = $this->portalmodel->select_name('customer_company', 'name', $coust);
	            		$row['title'] = $d->title;
	            		$row['details'] = $d->details;
	            		$row['start_date'] = date('Y-m-d', strtotime($d->created_on));
		            	if ($d->end_date == "" || $d->end_date == '0000-00-00') {
		                    $row['end_date'] = "--";
		                } else {
		                    $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
		                }
		                
		                $where1 = "projectid=$d->id ";
		                $row['nooftask'] = $this->portalmodel->record_count_where('task', $where1);
		                
		                $role = "";
		                
		            	if ($d->accountmanager == $id) {
		                    $role = "Account Manager";
		                }
		            	if ($d->projectmanager == $id) {
		                    if ($role == "")
		                        $role = "Project Manager";
		                    else
		                        $role = $role . "," . "Project Manager";
		                }
		                $developer = explode(',', $d->developer);
		                $r = "";
		            	if (!empty($developer)) {
		                    foreach ($developer as $dev) {
		                        $r[] = $dev;
		                    }
		                }
		                if (in_array($id, $r)) {
		                    if ($role === "")
								$role = "Techs";
			                else
								$role = $role . "," . "Techs";
		                }
		                $row['role'] = $role;
		                
		                $status = $d->status;
	            		if ($status == 1) {
			                $row['status'] = 'Open';
			            }
			            if ($status == 2) {
			                $row['status'] = 'Closed';
			            }
			            
			            $li[$index_key] = $row;
			            $index_key++;
	            	}
	            }
			}
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $li;
				
		return $response_array;
	}
	
	/**
	 * Add Project
	 */
	function addProject($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$projectDetails = $params['projectDetails'];
		$title = $projectDetails['title'];
		$start_date = date("Y-m-d",strtotime($projectDetails['start_date']));
		$end_date = date("Y-m-d",strtotime($projectDetails['end_date']));
		
		// this is actually a customer user id, so we have to get it's company id
		$customer_user_id = $projectDetails['customer_id'];
		$customer_id = $this->portalmodel->select_name('customer', 'fk_company_id', "customerid='".$customer_user_id."'");
		
		$accountmanager_id = $projectDetails['accountmanager_id'];
		
		$primary_tech = $projectDetails['primary_tech'];
		
		$tech_leads = $projectDetails['tech_leads'];
		$tech_leads = array_filter($tech_leads, function($value){ return $value !== ''; });
        $tech_leads = (!empty($tech_leads))?implode(",", $tech_leads):"";
        
		$description = $projectDetails['description'];
		$project_status = $projectDetails['project_status'];
		
		$data = array(
            'customerid' => $customer_id,	// this is actually a company id for customer
            'title' => $title,
            'accountmanager' => $accountmanager_id,
			'primary_tech' => $primary_tech,
            'developer' => $tech_leads,
            'details' => $description,
            'created_on' => $start_date,
            'end_date' => $end_date,
            'status' => $project_status,
            'created_by' => $id
        );
        
        // insert project
        $result = $this->portalmodel->insert_query_('project', $data);
        
		$files = (isset($projectDetails['files']))?$projectDetails['files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/projects/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/projects/".$file_name;
        			$data1              = array(
	                    'image_path' => $path,
	                    'project_id' => $result,
	                    'created_by' => $id
	                );
	                $result1    = $this->portalmodel->insert_query_('project_images', $data1);
        		}
        	}
        }
		
        if($result){
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Project added successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }
        
        return $response_array;		
	}
	
	/**
	 * get Project details
	 */
	function getProjectDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$project_id = $params['project_id'];
		
		// get details for project
		$project_details = array();
		$where = "id=$project_id";
		$data = $this->portalmodel->select_where('', '', 'project', $where);
		
		// set data in variable
		$project_details['id'] = $data[0]->id;
		$project_details['customer_id'] = $data[0]->customerid;
		$project_details['title'] = $data[0]->title;
		$project_details['description'] = $data[0]->details;
		$project_details['accountmanager_id'] = $data[0]->accountmanager;
		$project_details['primary_tech'] = $data[0]->primary_tech;
		$project_details['tech_leads'] = $data[0]->developer;
		$project_details['project_status'] = $data[0]->status;
		$project_details['start_date'] = date("m/d/Y",strtotime($data[0]->created_on));
		$project_details['end_date'] = date("m/d/Y",strtotime($data[0]->end_date));
		
		// now get files.
		$files_condition = "project_id=" . $data[0]->id;
		$files = $this->portalmodel->select_where('', '', 'project_images', $files_condition);
		$li = array();
		$index_key = "A";
		foreach ($files as $eachFile){
			if(isset($eachFile->image_path) && !empty($eachFile->image_path)){
				$li[$index_key] = base_url().$eachFile->image_path;
				$index_key++;
			}
		}
		$project_details['files']=$li;
		
		
		$response_array['result']['code'] = 200;
        $response_array['result']['details'] = $project_details;
        
        return $response_array;		
	}
	
	/**
	 * Update Project
	 */
	function updateProject($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$projectDetails = $params['projectDetails'];
		$project_id = $projectDetails['project_id'];
		$title = $projectDetails['title'];
		$start_date = date("Y-m-d",strtotime($projectDetails['start_date']));
		$end_date = date("Y-m-d",strtotime($projectDetails['end_date']));
		
		$primary_tech = $projectDetails['primary_tech'];
		
		$tech_leads = $projectDetails['tech_leads'];
		$tech_leads = array_filter($tech_leads, function($value){ return $value !== ''; });
        $tech_leads = (!empty($tech_leads))?implode(",", $tech_leads):"";
        
		$description = $projectDetails['description'];
		$project_status = $projectDetails['project_status'];
		
		$data = array(
            'title' => $title,
			'primary_tech' => $primary_tech,
            'developer' => $tech_leads,
            'details' => $description,
            'created_on' => $start_date,
            'end_date' => $end_date,
            'status' => $project_status,
            'modified_by' => $id
        );
        //echo FCPATH."/projects/"; exit;
        // update project
        $result = $this->portalmodel->update_query('project', $data, $project_id, 'id');
        
        $files = (isset($projectDetails['files']))?$projectDetails['files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/projects/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/projects/".$file_name;
        			$data1              = array(
	                    'image_path' => $path,
	                    'project_id' => $project_id,
	                    'created_by' => $id
	                );
	                $result1    = $this->portalmodel->insert_query_('project_images', $data1);
        		}
        	}
        }
		
        $response_array['result']['code'] = 200;
        $response_array['result']['message'] = "Project updated successfully!";
        
        return $response_array;		
	}
	
	/**
	 * listTickets
	 */
	function listTickets($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$li = array();
		$index_key = "A";
		if($is_customer){
			// get task for customers
			$cond="";
			
			// filter for open tickets or specific status tickets
			if (isset($params['filter']['status']) && !empty($params['filter']['status'])) {
				$status = $params['filter']['status'];
				$cond=" and status='".$status."'";
			}else {
				$cond=" and status in(0,1,2,3)";
			}
			
			// filter for category
			if(isset($params['filter']['fk_category_id']) && !empty($params['filter']['fk_category_id'])){
				$fk_category_id = $params['filter']['fk_category_id'];
				$cond.=" AND fk_category_id=".$fk_category_id;
			}
			
			// get company id
			$company_id = $this->portalmodel->select_username("fk_company_id AS name", 'customerid', $id, 'customer');
			//$where = "customerid='".$id."' and show_customer=0".$cond;
			$where = "projectid IN (SELECT id FROM project WHERE customerid='".$company_id."') AND show_customer=0".$cond;
			$list = $this->portalmodel->select_where_cond('', '', 'task', $where,"id","desc");
			
			foreach ($list as $d) {
				// project details
				$where1 = "id='".$d->projectid."'";
				$prj = $this->portalmodel->select_where('','','project',  $where1);
				$row['project_name'] =(isset($prj[0]->title))?$prj[0]->title:"";
				$row['project_id'] =(isset($prj[0]->id))?$prj[0]->id:0;
				$row['accountmanager_id'] = (isset($prj[0]->accountmanager))?$prj[0]->accountmanager:0;
				
				// task details
				$row['id'] = $d->id;
				$row['taskid'] = $d->taskid;
                $row['title'] = $d->title;
                
                $where7 = "id=".$d->priority;
                $row['priority'] = $this->portalmodel->select_name('taskpriority', 'priority', $where7);
                
				$row['created_on'] = date('Y-m-d', strtotime($d->created_on));
                if ($d->expected_date == "" || $d->expected_date == '0000-00-00') {
                    $row['start_date'] = "--";
                } else {
                    $row['start_date'] = date('Y-m-d', strtotime($d->expected_date));
                }
                if ($d->expected_end == "" || $d->expected_end == '0000-00-00') {
                    $row['end_date'] = "--";
                } else {
                    $row['end_date'] = date('Y-m-d', strtotime($d->expected_end));
                }
                
                $name = "CONCAT(first_name,' ',last_name) as name";
                $row['assigned_to'] = $this->portalmodel->select_username($name, 'id', $d->assigned_to, 'user');
                
                $status = $d->status;
				if ($status == 1) {
                    $row['status'] = 'Started';
                }
                if ($status == 2) {
                    $row['status'] = 'In-Progress';
                }
                if ($status == 3) {
                    $row['status'] = 'On-Hold';
                }
                if ($status == 4) {
                    $row['status'] = 'Completed';
                } else {
                    $row['status'] = 'Not Started';
                }
                
                $where_category = "id=$d->fk_category_id";
                $row['category_name'] = $this->portalmodel->select_name('categories', 'name', $where_category);
                
                $li[$index_key] = $row;
                $index_key++;
			}
			
		}else{
			// get tasks for employee/tech
			$cond = "";
		
			// filter for open tickets or specific status tickets
			if (isset($params['filter']['status']) && !empty($params['filter']['status'])) {
				$status = $params['filter']['status'];
				$cond=" and status='".$status."'";
			}else {
				$cond=" and status in(0,1,2,3)";
			}
			
			// filter for category
			if(isset($params['filter']['fk_category_id']) && !empty($params['filter']['fk_category_id'])){
				$fk_category_id = $params['filter']['fk_category_id'];
				$cond.=" AND fk_category_id=".$fk_category_id;
			}
			
			// filter created by (Me or Others)
			if(isset($params['filter']['filter_created_by']) && !empty($params['filter']['filter_created_by'])){
				$filter_created_by = $params['filter']['filter_created_by'];
				
				if($filter_created_by==1){
					// created by me
					$cond.=" AND c_by=1 AND created_by='".$id."' ";
				}else if($filter_created_by==2){
					// created by others
					$cond.=" AND created_by!='".$id."' ";
				}
			}
			
			if(!empty($cond)){
				$where = '1'.$cond;
			}else{
				$where = array();
			}
			
			if($internal_user_external_user==1){
	           	$condition_assigned_projects='1';
			}else{
	           	$condition_assigned_projects="(assigned_to='".$id."' OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager )))) ";
			}
			
			$where = $condition_assigned_projects.$cond;
			
			$list = $this->portalmodel->select_where_cond('', '', 'task', $where,"id","desc");
			
			foreach ($list as $d) {
				// project details associated with task
				$where1 = "id=".$d->projectid;
				$prj = $this->portalmodel->select_where('','','project',  $where1);
				$row['project_name'] =(isset($prj[0]->title))?$prj[0]->title:"";
				$row['project_id'] =(isset($prj[0]->id))?$prj[0]->id:0;
				$row['accountmanager_id'] = (isset($prj[0]->accountmanager))?$prj[0]->accountmanager:0;
				
				// task details
				$row['id'] = $d->id;
				$row['taskid'] = $d->taskid;
				$row['title'] = $d->title;
				
				$where7 = "id=" . $d->priority;
				$row['priority'] = $this->portalmodel->select_name('taskpriority', 'priority', $where7);
				
				$row['created_on'] = date('Y-m-d', strtotime($d->created_on));
				
				if ($d->expected_date == "" || $d->expected_date == '0000-00-00') {
                    $row['start_date'] = "--";
                } else {
                    $row['start_date'] = date('Y-m-d', strtotime($d->expected_date));
                }
                
				if ($d->expected_end == "" || $d->expected_end == '0000-00-00') {
                    $row['end_date'] = "--";
                } else {
                    $row['end_date'] = date('Y-m-d', strtotime($d->expected_end));
                }
                
                $name = "CONCAT(first_name,' ',last_name) as name";
                $row['assigned_to'] = $this->portalmodel->select_username($name, 'id', $d->assigned_to, 'user');
                
                $status = $d->status;
				if ($status == 1) {
                    $row['status'] = 'Started';
                }else if ($status == 2) {
                    $row['status'] = 'In-Progress';
                }else if ($status == 3) {
                    $row['status'] = 'On-Hold';
                }else if ($status == 4) {
                    $row['status'] = 'Completed';
                } else {
                    $row['status'] = 'Not Started';
                }
                
                $row['show_customer'] = $d->show_customer;
                
                $where_category = "id=$d->fk_category_id";
                $row['category_name'] = $this->portalmodel->select_name('categories', 'name', $where_category);
                
				// get created by customer / employee name
                if($d->c_by==0){
                	// customer
                	$row['created_by_name'] = $this->portalmodel->select_name('customer', 'companyname', "customerid='".$d->created_by."'");
                }else{
                	// employee
                	$row['created_by_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $d->created_by, 'user');
                }
                
                $li[$index_key] = $row;
                $index_key++;
			}
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $li;
				
		return $response_array;
		
	}
	
	/**
	 * getTicketDetails
	 */
	function getTicketDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$ticket_id = $params['ticket_id'];
		
		$where = "id=$ticket_id";
		$details = $this->portalmodel->select_where('', '', 'task', $where);
		
		$list = array();
		
		$list['ticket_id'] = $details[0]->id;
		//$list['taskid'] = $details[0]->taskid;
		$list['fk_category_id'] = $details[0]->fk_category_id;
		$list['project_id'] = $details[0]->projectid;
		$list['title'] = $details[0]->title;
		$list['priority'] = $details[0]->priority;
		$list['description'] = $details[0]->description;
		$list['start_date'] = $details[0]->start;
		$list['expected_start_date'] = $details[0]->expected_date;
		$list['expected_end_date'] = $details[0]->expected_end;
		$list['status'] = $details[0]->status;
		$list['resolution'] = $details[0]->resolution;
		$list['tech'] = $details[0]->assigned_to;
		$list['show_to_customer'] = $details[0]->show_customer;
		$list['hours_spent'] = $details[0]->hours;
		
		/*foreach ($details as $eachCategory){
			$list[$index_key] = $eachCategory;
			$index_key++;
		}*/
		
		// now get files.
		$files_condition = "task_id=" . $ticket_id;
		$files = $this->portalmodel->select_where('', '', 'task_images', $files_condition);
		$li = array();
		$index_key = "A";
		foreach ($files as $eachFile){
			if(isset($eachFile->image_path) && !empty($eachFile->image_path)){
				$li[$index_key] = base_url().$eachFile->image_path;
				$index_key++;
			}
		}
		$list['files']=$li;
		
		// now get files.
		$files_condition = "fk_task_id=" . $ticket_id;
		$files = $this->portalmodel->select_where('', '', 'task_files', $files_condition);
		$li = array();
		$index_key = "A";
		foreach ($files as $eachFile){
			if(isset($eachFile->file) && !empty($eachFile->file)){
				$li[$index_key] = base_url().$eachFile->file;
				$index_key++;
			}
		}
		$list['workorder_file']=$li;
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * addTicket
	 */
	function addTicket($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// ticket details
		$project_id = $params['ticket_details']['project_id'];
		$title = $params['ticket_details']['title'];
		$fk_category_id = $params['ticket_details']['fk_category_id'];
		$priority = $params['ticket_details']['priority'];
		$description = $params['ticket_details']['description'];
		$expected_start_date = date("Y-m-d",strtotime($params['ticket_details']['expected_start_date']));
		$expected_end_date = date("Y-m-d",strtotime($params['ticket_details']['expected_end_date']));
		
		$customerid = $this->portalmodel->select_name('project', 'customerid', "id='".$project_id."'");
		
		$max_id = $this->portalmodel->maxid('task', 'id');
        $num = $max_id[0]->id + 1;
        $taskid = 'T00' . $num;
		
		if($is_customer){
			
			// to prefix the customer name in description
	        $customer_first_name = $this->portalmodel->select_name('customer', 'first_name', "customerid='".$id."'");
	        $customer_last_name = $this->portalmodel->select_name('customer', 'last_name', "customerid='".$id."'");
	        $customer_company_name = $this->portalmodel->select_name('customer', 'companyname', "customerid='".$id."'");
	    	$priority_text = "Low Priority";
	        if($this->input->post('priority')=="2"){
	        	$priority_text = "High Priority";
	        }else if($this->input->post('priority')=="3"){
	        	$priority_text = "Medium Priority";
	        }
			
			// add ticket by customer
			$data     = array(
	            'taskid' => $taskid,
	        	'fk_category_id' => $fk_category_id,
	            'projectid' => $project_id,
	            'customerid' => $customerid,
				'expected_date' => $expected_start_date,
	            'expected_end' => $expected_end_date,
	            'title' => "(".$priority_text.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : ".$title,
	            'priority' => $priority,
	            'description' => "(".$priority_text.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : ".$description,
	            'status' => 0,
	            'show_customer' => 0,
	            'c_by' => 0,
	            'created_by' => $id
	        );
	        
	        $result = $this->portalmodel->insert_query_('task', $data);
			
	        if($result){
	        	$response_array['result']['code'] = 200;
	        	$response_array['result']['message'] = "Ticket added successfully!";
	        	
	        	$files = (isset($params['ticket_details']['files']))?$params['ticket_details']['files']:'';
		        if(isset($files) && !empty($files)){
		        	foreach ($files as $eachFile){
		        		$target_dir = FCPATH."/tasks/";
		        		
		        		$file_parts = explode(";base64,", $eachFile);
		        		$eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
		        		
		        		$file_name = $this->upload_file($target_dir, $eachFile);
		        		if(!empty($file_name)){
		        			$path = "/tasks/".$file_name;
		        			$data1              = array(
			                    'image_path' => $path,
			                    'task_id' => $result,
			                    'created_by' => $id
			                );
			                $result1    = $this->portalmodel->insert_query_('task_images', $data1);
		        		}
		        	}
		        }
	        	
	        	// now send email
	        	$subject = "New Task " . $taskid;
	        	
	        	$where  = 'customerid=' . $id;
            	$to = $this->portalmodel->select_name('customer', 'emailid', $where);
            	
            	$message = 'Dear Customer,<br/><br/>We have received your support request and assigned it a ticket ID of <b>' . $taskid . '</b> One of our team members will respond to your ticket shortly.<br/>
							<br/><br/>
							Thank you,<br/>
							
							- Synergy IT Team
							';
		        if ($to != "" && $to!="N/A") {
	                $email = $this->email($to, $subject, $message);
	            }
	            
	            // send to business email
	            $to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
		        if(!empty($to_business_email) && $to_business_email!='N/A'){
	            	$email = $this->email($to_business_email, $subject, $message);
	            }
	            
	            // send email to employee
	            $messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b>' . $taskid . '</b> .Please respond to ticket shortly.<br/>
								<br/><br/>
								Thank you,<br/>
								
								- Synergy IT Team
								';
	            
	            $where1 = "id=$project_id";
				$prj = $this->portalmodel->select_where('', '', 'project', $where1);
				
				$accid=$prj[0]->accountmanager;
				$primary_tech_id = $prj[0]->primary_tech;
	        	$arrIdsIn = array();
	        	if(isset($primary_tech_id) && !empty($primary_tech_id)){
					$arrIdsIn[] = $primary_tech_id;
				}
				if(isset($accid) && !empty($accid)){
					$arrIdsIn[] = $accid;
				}
				$strIds = implode(",", $arrIdsIn);
				$where1 = "id IN (".$strIds.")";
				
				$tolist = $this->portalmodel->select_where('','','user',  $where1);
		        if (!empty($tolist)) {
		            foreach ($tolist as $d){ 
						//echo $d->email.'<br/>';
		         		$email=$this->email($d->email, $subject, $messagestaff);
		         		
		         		if(!empty($d->business_email) && $d->business_email!='N/A'){
		         			$email=$this->email($d->business_email, $subject, $messagestaff);
		         		}
		            }
				}
				
				// now also send email of a new task to support@synergyit.ca
				$email=$this->email("support@synergyit.ca", $subject, $messagestaff);
				
				$dev = $prj[0]->developer;
				$arrDevelopers = explode(",", $dev);
				$tech = $arrDevelopers[0];
				
				if(isset($prj[0]->primary_tech) && !empty($prj[0]->primary_tech)){
					$tech = $prj[0]->primary_tech;
				}
				
				$data   = array(
					'assigned_to' => $tech
				);
				$update = $this->portalmodel->update_query('task', $data, $result, 'id');
            	
	        }else{
	        	$response_array['error']['code'] = 500;
	        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
	        }
		}else{
			// employee
			$customerid = $this->portalmodel->select_name('project', 'customerid', "id='".$project_id."'");
			$developer = $params['ticket_details']['tech'];
			$status = $params['ticket_details']['status'];
			$show_to_customer = $params['ticket_details']['show_to_customer'];
			
			// to prefix the customer name in description
	        $customer_first_name = $this->portalmodel->select_name('customer', 'first_name', "customerid='".$customerid."'");
	        $customer_last_name = $this->portalmodel->select_name('customer', 'last_name', "customerid='".$customerid."'");
	        $customer_company_name = $this->portalmodel->select_name('customer', 'companyname', "customerid='".$customerid."'");
	        $priority_text = "Low Priority";
	        if($this->input->post('priority')=="2"){
	        	$priority_text = "High Priority";
	        }else if($this->input->post('priority')=="3"){
	        	$priority_text = "Medium Priority";
	        }
			
			$data      = array(
	            'taskid' => $taskid,
	        	'fk_category_id' => $fk_category_id,
	            'projectid' => $project_id,
	            'customerid' => $customerid,
	            'title' => "(".$priority_text.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : ".$title,
	            'priority' => $priority,
	            'description' => "(".$priority_text.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : ".$description,
	            'expected_date' => $expected_start_date,
	            'expected_end' => $expected_end_date,
	            'assigned_to' => $developer,
	            'assigned_by' => $id,
	            'status' => $status,
	            'show_customer' => $show_to_customer,
	            'c_by' => 1,
	            'created_by' => $id
	        );
	        
	        $result = $this->portalmodel->insert_query_('task', $data);
			
		if($result){
	        	$response_array['result']['code'] = 200;
	        	$response_array['result']['message'] = "Ticket added successfully!";
	        	
	        	// now send email
	        	$subject = "New Task " . $taskid;
	        	
	        	if($show_to_customer==0){
	        		//$where = 'customerid=' . $customerid;
            		//$to = $this->portalmodel->select_name('customer', 'emailid', $where);
            		$message = 'Dear Customer,<br/><br/>We have received your support request and assigned it a ticket ID of <b>' . $taskid . '</b> One of our team members will respond to your ticket shortly.<br/>
								<br/><br/>
								Thank you,<br/>
								
								- Synergy IT Team
								';
            		
		        	// get list of associated customer users for the selected project
		            $toCustomerArray = array();
		        	$customer_users_for_company = $this->portalmodel->select('', '', 'customer','fk_company_id="'.$customerid.'"','customerid','DESC');
					foreach ($customer_users_for_company as $each_customer_user){
						if(isset($each_customer_user->emailid) && !empty($each_customer_user->emailid)){
							$toCustomerArray[] = $each_customer_user->emailid;
						}
						
						$customer_business_email = $each_customer_user->business_email;
						if(!empty($customer_business_email) && $customer_business_email!='N/A'){
							$toCustomerArray[] = $customer_business_email;
						}
					}
		            
		            if (!empty($toCustomerArray)) {
		            	foreach ($toCustomerArray as $each_customer_email){
		            		$email = $this->email($each_customer_email, $subject, $message);
		            	}
		            }
            		
		        	/*if($to != "" && $to!="N/A") {
		                $email = $this->email($to, $subject, $message);
		            }
		            
	        		// also send email to business email of customer.
		            $to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
		        	if ($to_business_email != "" && $to_business_email!="N/A") {
		                $to_business_email = $this->email($to_business_email, $subject, $message);
		            }*/
	        	}
	        	
	        	$messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b>' . $taskid . '</b> .Please respond to ticket shortly.<br/>
								<br/><br/>
								Thank you,<br/>
								
								- Synergy IT Team
								';
	        	
	        	$where1 = "id=$project_id";
				$prj = $this->portalmodel->select_where('', '', 'project', $where1);
				
				$devid=$developer;
				$accid=$prj[0]->accountmanager;
				$arrIds = array();
				if(isset($devid) && !empty($devid)){
					$arrIds[] = $devid;
				}
				if(isset($accid) && !empty($accid)){
					$arrIds[] = $accid;
				}
				
				$where1 = "id IN (".implode(',', $arrIds).")";
				$tolist = $this->portalmodel->select_where('','','user',  $where1);
				if (!empty($tolist)) {
		            foreach ($tolist as $d){ 
						$email=$this->email($d->email, $subject, $messagestaff);
			         	
			         	// send to business email
			         	if(isset($d->business_email) && !empty($d->business_email)){
							$email=$this->email($d->business_email, $subject, $messagestaff);
			         	}
		            }
				}
				
				// now also send email of a new task to support@synergyit.ca
				$email=$this->email("support@synergyit.ca", $subject, $messagestaff);
            	
	        }else{
	        	$response_array['error']['code'] = 500;
	        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
	        }
		}
		
		return $response_array;
	}
	
	/**
	 * updateTicket
	 */
	function updateTicket($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// ticket details
		$ticket_id = $params['ticket_details']['ticket_id'];
		$expected_start_date = (isset($params['ticket_details']['expected_start_date']) && !empty($params['ticket_details']['expected_start_date']))?date("Y-m-d",strtotime($params['ticket_details']['expected_start_date'])):"";
		$expected_end_date = (isset($params['ticket_details']['expected_end_date']) && !empty($params['ticket_details']['expected_end_date']))?date("Y-m-d",strtotime($params['ticket_details']['expected_end_date'])):"";
		$start_date = (isset($params['ticket_details']['start_date']) && !empty($params['ticket_details']['start_date']))?date("Y-m-d",strtotime($params['ticket_details']['start_date'])):"";
		$status = $params['ticket_details']['status'];
		$completedon=(isset($status) && $status==4)?date('Y-m-d H:i:s'):"0000-00-00 00:00:00";
		$modifiedon=date('Y-m-d H:i:s');
		$new_priority = $params['ticket_details']['priority'];
		$new_resolution = $params['ticket_details']['resolution'];
		$assigned_to = $params['ticket_details']['tech'];
		$fk_category_id = $params['ticket_details']['fk_category_id'];
		$show_to_customer = $params['ticket_details']['show_to_customer'];
		$hours_spent = $params['ticket_details']['hours_spent'];
		
		// get details of task before save.
		$old_task_details = $this->portalmodel->select_where('', '', 'task', "id=$ticket_id");
		$old_priority = $old_task_details[0]->priority;
		$old_expected_start_date = $old_task_details[0]->expected_date;
		$old_expected_end_date = $old_task_details[0]->expected_end;
		$old_resolution = $old_task_details[0]->resolution;
		$old_show_to_customer = $old_task_details[0]->show_customer;
		
		$what_changed = array();
		
		if((empty($old_priority) && !empty($new_priority)) || (!empty($old_priority) && empty($new_priority)) || ($old_priority!=$new_priority)){
			$what_changed[]="Priority";
		}
		if(strtotime($old_expected_start_date)!=strtotime($expected_start_date)){
			$what_changed[]="Expected Start Date";
		}
		if(strtotime($old_expected_end_date)!=strtotime($expected_end_date)){
			$what_changed[]="Expected End Date";
		}
		if($old_resolution!=$new_resolution){
			$what_changed[]="Resolution";
		}
		
		// now if anything changed, then add in task_notification table for Tech, Account manager and client.
		if(!empty($what_changed)){
			// prepare email Subject and Body.
			$subject = "Task Updated ".$old_task_details[0]->taskid;
			$body = "Hi,<br />The Task ".$old_task_details[0]->taskid." has been updated.<br /><br />Please find below updates<br />";
			$body .= implode(", ", $what_changed);
			$body .= "<br/><br/>Thank you,<br/>- Synergy IT Team";
			
			// get the project id of the task
			$task_project_id = $old_task_details[0]->projectid;
			$projectDetails = $this->portalmodel->select_where('', '', 'project', "id=$task_project_id");
			
			// send email to customer.
			if($old_show_to_customer==0){
				
				$customer_users_for_company = $this->portalmodel->select('', '', 'customer','fk_company_id="'.$projectDetails[0]->customerid.'"','customerid','DESC');
				foreach ($customer_users_for_company as $each_customer_user){
					$customer_email = $each_customer_user->emailid;
					$send_email = $this->email($customer_email, $subject, $body);
					
					$customer_business_email = $each_customer_user->business_email;
					if(!empty($customer_business_email) && $customer_business_email!='N/A'){
						$send_email = $this->email($customer_business_email, $subject, $body);
					}
				}
				
				/*$customer_email = $this->portalmodel->select_name('customer', 'emailid', "customerid='".$old_task_details[0]->customerid."'");
				if(!empty($customer_email) && $customer_email!='N/A'){
					$email = $this->email($customer_email, $subject, $body);
					
					$customer_business_email = $this->portalmodel->select_name('customer', 'business_email', "customerid='".$old_task_details[0]->customerid."'");
					if(!empty($customer_business_email) && $customer_business_email!='N/A'){
						$email = $this->email($customer_business_email, $subject, $body);
					}
				}*/
			}
			
			// send email to account manager.
			//$accountmanagerid = $this->portalmodel->select_name('customer', 'accountmanagerid', "customerid='".$old_task_details[0]->customerid."'");
			$accountmanagerid = $projectDetails[0]->accountmanager;
			if(!empty($accountmanagerid) && $accountmanagerid!='N/A'){
				$accountmanager_email = $this->portalmodel->select_name('user', 'email', "id='".$accountmanagerid."'");
				if(!empty($accountmanager_email) && $accountmanager_email!='N/A'){
					$email = $this->email($accountmanager_email, $subject, $body);
				}
				
				$accountmanager_business_email = $this->portalmodel->select_name('user', 'business_email', "id='".$accountmanagerid."'");
				if(!empty($accountmanager_business_email) && $accountmanager_business_email!='N/A'){
					$email = $this->email($accountmanager_business_email, $subject, $body);
				}
			}
			
			// send email to tech
			$tech_email = $this->portalmodel->select_name("user","email","id='".$assigned_to."'");
			if(!empty($tech_email) && $tech_email!='N/A'){
				$email = $this->email($tech_email, $subject, $body);
			}
			
			$tech_business_email = $this->portalmodel->select_name("user","business_email","id='".$assigned_to."'");
			if(!empty($tech_business_email) && $tech_business_email!='N/A'){
				$email = $this->email($tech_business_email, $subject, $body);
			}
			
			// now save to task_notifications for all
			$changes = implode(", ", $what_changed)." have been updated";
			
			// save notification for customer
			//$customer_varchar_id = $this->portalmodel->select_name('customer', 'username', "customerid='".$old_task_details[0]->customerid."'");
			$customer_varchar_id = $this->portalmodel->select('', '', 'customer','fk_company_id="'.$projectDetails[0]->customerid.'"','customerid','DESC');
			if(!empty($customer_varchar_id) && $customer_varchar_id!='N/A'){
				
				foreach ($customer_varchar_id as $d){
					$data_task_notification = array(
						"fk_task_id"=>$this->input->post('taskid'),
						"user_type"=>"C",
						"fk_customer_or_employee_id"=>$d->username,
						"changes"=>$changes,
						"read_unread"=>0
					);
				
					$insert_for_customer = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);
				}
				
				/*$data_task_notification = array(
					"fk_task_id"=>$ticket_id,
					"user_type"=>"C",
					"fk_customer_or_employee_id"=>$customer_varchar_id,
					"changes"=>$changes,
					"read_unread"=>0
				);
				
				$insert_for_customer = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);*/
			}
			
			// save notification for account manager
			if(!empty($accountmanagerid) && $accountmanagerid!='N/A'){
				$accountmanager_varchar_id = $this->portalmodel->select_name('user', 'employeeid', "id='".$accountmanagerid."'");
				if(!empty($accountmanager_varchar_id) && $accountmanager_varchar_id!='N/A'){
					$data_task_notification = array(
						"fk_task_id"=>$ticket_id,
						"user_type"=>"E",
						"fk_customer_or_employee_id"=>$accountmanager_varchar_id,
						"changes"=>$changes,
						"read_unread"=>0
					);
					
					$insert_for_am = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);
				}
			}
			
			// save notification for tech
			$tech_varchar_id = $this->portalmodel->select_name("user","employeeid","id='".$assigned_to."'");
			if(!empty($tech_varchar_id) && $tech_varchar_id!='N/A'){
				$data_task_notification = array(
					"fk_task_id"=>$ticket_id,
					"user_type"=>"E",
					"fk_customer_or_employee_id"=>$tech_varchar_id,
					"changes"=>$changes,
					"read_unread"=>0
				);
				
				$insert_for_tech = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);
			}
		}
		
		// now save ticket changes
		$data   = array(
        	'fk_category_id' => $fk_category_id,
            'priority' => $new_priority,
            'start' => $start_date,
            'expected_date' => $expected_start_date,
			'expected_end'=> $expected_end_date,
			'end_date'=>$completedon,
            'status' => $status,
            'show_customer' => $show_to_customer,
            'hours' => $hours_spent,
            'modified_by' => $id,
            'assigned_to' => $assigned_to,
			'modified_on'=>$modifiedon,
			'resolution'=>$new_resolution,
        );
        
        $result = $this->portalmodel->update_query('task', $data, $ticket_id, 'id');
        
		$files = (isset($params['ticket_details']['workorder_file']))?$params['ticket_details']['workorder_file']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/tasks/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/tasks/".$file_name;
        			$data1              = array(
	                    'fk_task_id'=>$ticket_id,
						'file_name'=>"",
						'file' => $path,
						'created_on' => date("Y-m-d H:i:s"),
						'created_by' => $id
	                );
	                $result1    = $this->portalmodel->insert_query_('task_files', $data1);
        		}
        	}
        }
        
        $taskid = $this->portalmodel->select_name("task","taskid","id='".$ticket_id."'");
        
        // send email to assigned tech
        if($status==0){
        	$tech_email = $this->portalmodel->select_name("user","email","id='".$assigned_to."'");
        	$subjectstaff = "New Task ".$taskid;
        	$messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b>' . $taskid . '</b> .Please respond to ticket shortly.<br/>
							<br/><br/>
							Thank you,<br/>
							- Synergy IT Team
							';
        	$email = $this->email($tech_email, $subjectstaff, $messagestaff);
        	
        	// also send to tech business email
        	$tech_business_email = $this->portalmodel->select_name("user","business_email","id='".$assigned_to."'");
        	if(!empty($tech_business_email) && $tech_business_email!='N/A'){
        		$email = $this->email($tech_business_email, $subjectstaff, $messagestaff);
        	}
        }
        
		// now if status is completed(4) then send customer a notification mail that their ticket has been completed
        if($status==4 && $show_to_customer==0 && !empty($customerid)){
        	
        	// get the project id of the task
			$task_project_id = $old_task_details[0]->projectid;
			$projectDetails = $this->portalmodel->select_where('', '', 'project', "id=$task_project_id");
        	
        	$customer_users_for_company = $this->portalmodel->select('', '', 'customer','fk_company_id="'.$projectDetails[0]->customerid.'"','customerid','DESC');
			foreach ($customer_users_for_company as $each_customer_user){
				$cust_email = $each_customer_user->emailid;
				
				if(!empty($cust_email)){
	        		$subject = "Task Completed ".$taskid;
		        	$message = 'Dear Customer,<br/><br/>This ticket <b>' . $taskid . '</b> has been completed. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/><br/><br/>Thank you,<br/>- Synergy IT Team';
			        $email = $this->email($cust_email, $subject, $message);
			        
			        // send to customer business email
			        $cust_business_email = $each_customer_user->business_email;
			        if(isset($cust_business_email) && !empty($cust_business_email) && $cust_business_email!='N/A'){
			        	$email = $this->email($cust_business_email, $subject, $message);
			        }
	        	}
			}
			
        	/*$customerid = $this->portalmodel->select_name("task","customerid","id='".$ticket_id."'");
        	
        	// get customer's email id
        	$cust_email = $this->portalmodel->select_name("customer","emailid","customerid='".$customerid."'");
        	if(!empty($cust_email)){
        		$subject = "Task Completed ".$taskid;
	        	$message = 'Dear Customer,<br/><br/>This ticket <b>' . $taskid . '</b> has been completed. Please login to the SynergyInteract for the details.<a href="http://supervisabrampton.com/">http://supervisabrampton.com</a><br/><br/><br/>Thank you,<br/>- Synergy IT Team';
		        $email = $this->email($cust_email, $subject, $message);
		        
		        // send to customer business email
		        $cust_business_email = $this->portalmodel->select_name("customer","business_email","customerid='".$customerid."'");
		        if(isset($cust_business_email) && !empty($cust_business_email) && $cust_business_email!='N/A'){
		        	$email = $this->email($cust_business_email, $subject, $message);
		        }
        	}*/
        }
        
        $response_array['result']['code'] = 200;
		$response_array['result']['message'] = "Ticket updated successfully!";
		
		return $response_array;
	}
	
	/**
	 * listTicketCategories
	 */
	function listTicketCategories($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$where = array();
		$details = $this->portalmodel->select('', '', 'categories', $where,'name','ASC');
		
		$list = array();
		$index_key = "A";
		foreach ($details as $eachCategory){
			$list[$index_key] = $eachCategory;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getCategoryDetails
	 */
	function getCategoryDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$category_id = $params['category_id'];
		
		$where = "id=$category_id";
		$details = $this->portalmodel->select_where('', '', 'categories', $where);
		
		$list = array();
		$index_key = "A";
		$list['id'] = $details[0]->id;
		$list['name'] = $details[0]->name;
		/*foreach ($details as $eachCategory){
			$list[$index_key] = $eachCategory;
			$index_key++;
		}*/
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * Update Category
	 */
	function updateCategory($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$categoryDetails = $params['category_details'];
		$category_id = $categoryDetails['category_id'];
		$name = $categoryDetails['name'];
		
		$data = array(
            'name' => $name
        );
        
        // update project
        $result = $this->portalmodel->update_query('categories', $data, $category_id, 'id');
		
        $response_array['result']['code'] = 200;
        $response_array['result']['message'] = "Category updated successfully!";
        
        return $response_array;		
	}
	
	/**
	 * addTicketCategory
	 */
	function addTicketCategory($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// category details
		$category_name = $params['category_details']['category_name'];
		$data = array(
            'name' => $category_name
        );
        $result = $this->portalmodel->insert_query_('categories', $data);
        
		if($result){
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Category added successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }
		
		return $response_array;
	}
	
	/**
	 * listInvoices
	 */
	function listInvoices($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$li = array();
		$index_key = "A";
		
		if($is_customer){
			
			$where_company = " customerid='$id'";
        	$company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
			
			$where = "fk_client_id='".$company_id."'";
			$list = $this->portalmodel->select_where_cond('', '', 'invoices', $where,"id","DESC");
			
			if(!empty($list)){
				foreach($list as $d){
					$row['id'] = $d->id;
					$row['invoice_number'] = $d->invoice_number;
					
					$customer = "id=".$d->fk_client_id;
	                $row['customer'] = $this->portalmodel->select_name('customer_company', 'name', $customer);
	                
	                if(isset($d->fk_task_id) && !empty($d->fk_task_id)){
	                	$condition_task_id = 'id='.$d->fk_task_id;
		                $row['ticket_id'] = $this->portalmodel->select_name('task', 'taskid', $condition_task_id);
		                $row['task'] = $this->portalmodel->select_name('task', 'title', $condition_task_id);
	                }else{
	                	$row['ticket_id'] = "";
	                	$row['task'] = "";
	                }
	                
	                $row['invoice_date'] = date("d F, Y",strtotime($d->invoice_date));
	                $row['amount'] = $d->amount;
	                $row['remaining_amount'] = $d->remaining_amount;
	                $row['invoice_status'] = ($d->invoice_status=="U")?"Un-paid":"Paid";
	                $row['description'] = $d->description;
	                
	                $li[$index_key] = $row;
	                $index_key++;
				}
			}
		}else{
			// list invoices for employees
			$where = "created_by='".$id."'";
			$list = $this->portalmodel->select_where_cond('', '', 'invoices', $where,"id","DESC");
			
			if (!empty($list)) {
				foreach ($list as $d) {
					$row['id'] = $d->id;
					$row['invoice_number'] = $d->invoice_number;
					
					$customer = "id=".$d->fk_client_id;
                	$row['customer']  = $this->portalmodel->select_name('customer_company', 'name', $customer);
                	
                	if(isset($d->fk_task_id) && !empty($d->fk_task_id)){
                		$condition_task_id = 'id='.$d->fk_task_id;
		                $row['ticket_id'] = $this->portalmodel->select_name('task', 'taskid', $condition_task_id);
		                $row['task'] = $this->portalmodel->select_name('task', 'title', $condition_task_id);
                	}else{
                		$row['ticket_id'] = "";
                		$row['task'] = "";
                	}
                	
                	$row['invoice_date'] = date("d M, Y",strtotime($d->invoice_date));
                	$row['amount'] = $d->amount;
                	$row['remaining_amount'] = $d->remaining_amount;
                	$row['invoice_status'] = ($d->invoice_status=="U")?"Un-paid":"Paid";
                	$row['description'] = $d->description;
                	
                	$li[$index_key] = $row;
	                $index_key++;
				}
			}
		}
		
		$response_array['result']['code'] = 200;
        $response_array['result']['list'] = $li;
        
		return $response_array;
	}
	
	/**
	 * getInvoiceDetails
	 */
	function getInvoiceDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$invoice_id = $params['invoice_id'];
		
		$where = "id=$invoice_id";
		$details = $this->portalmodel->select_where('', '', 'invoices', $where);
		
		$list = array();
		
		$list['invoice_id'] = $details[0]->id;
		$list['invoice_number'] = $details[0]->invoice_number;
		
		// this is actually a company id, so get the customer user id from this company id
		$company_id = $details[0]->fk_client_id;
		$list['fk_client_id'] = $this->portalmodel->select_name('customer', 'customerid', "fk_company_id='".$company_id."' AND status=1 AND accountmanagerid='".$id."'");
		
		
		$list['fk_task_id'] = $details[0]->fk_task_id;
		$list['invoice_date'] = $details[0]->invoice_date;
		$list['amount'] = $details[0]->amount;
		$list['remaining_amount'] = $details[0]->remaining_amount;
		$list['invoice_status'] = $details[0]->invoice_status;
		$list['description'] = $details[0]->description;
		
		// get received payments.
        $where_invoice_paid = "fk_invoice_id='".$invoice_id."'";
        $payments = $this->portalmodel->select_where_cond('', '', 'invoice_paid', $where_invoice_paid,"paid_date","DESC");
		
        $index_key="A";
		foreach ($payments as $each){
			$list['payments'][$index_key]['paid_for_task'] = $each->task;
			$list['payments'][$index_key]['paid_on_date'] = $each->paid_date;
			$list['payments'][$index_key]['paid_amount'] = $each->amount;
			$index_key++;
		}
		
		$where_files = "fk_invoice_id=$invoice_id";
		$files = $this->portalmodel->select_where('', '', 'invoice_files', $where_files);
		
		$li = array();
		$index_key_inner = "A";
		foreach ($files as $eachFile){
			if(isset($eachFile->file) && !empty($eachFile->file)){
				$li[$index_key_inner] = base_url().$eachFile->file;
				$index_key_inner++;
			}
		}
		$list['files']=$li;
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * listPaymentHistory
	 */
	function listPaymentHistory($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$filter_invoice_id = (isset($params['filter']['fk_invoice_id']) && !empty($params['filter']['fk_invoice_id']))?$params['filter']['fk_invoice_id']:0;
		
		$li = array();
		$index_key = "A";
		
		if($is_customer){
			
			$where_company = " customerid='$id'";
        	$company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
			
			$where = "fk_invoice_id IN (SELECT id FROM invoices WHERE fk_client_id='".$company_id."')";
			if(isset($filter_invoice_id) && !empty($filter_invoice_id)){
				$where .= " AND fk_invoice_id=".$filter_invoice_id;
			}
			
			$list = $this->portalmodel->select_where_cond('', '', 'invoice_paid', $where,"paid_date","DESC");
			
			if(!empty($list)){
				foreach($list as $d){
					$row['id'] = $d->id;
					
					$condition_invoice_number = "id=".$d->fk_invoice_id;
                	$row['invoice_number'] = $this->portalmodel->select_name('invoices', 'invoice_number', $condition_invoice_number);
					
                	$row['task'] = $d->task;
                	$row['paid_date'] = date("d M, Y",strtotime($d->paid_date));
                	$row['amount'] = $d->amount;
                	
	                $li[$index_key] = $row;
	                $index_key++;
				}
			}
		}else{
			// list invoice history for employees
			$where = array();
			
			if(isset($filter_invoice_id) && !empty($filter_invoice_id)){
	        	$where = "fk_invoice_id=".$filter_invoice_id;
	        }
			
	        $list = $this->portalmodel->select_where_cond('', '', 'invoice_paid', $where,"paid_date","DESC");
	        
			if (!empty($list)) {
				foreach ($list as $d) {
					$row['id'] = $d->id;
					
					$condition_invoice_number = "id=".$d->fk_invoice_id;
                	$row['invoice_number'] = $this->portalmodel->select_name('invoices', 'invoice_number', $condition_invoice_number);
                	
                	$row['task'] = $d->task;
                	$row['paid_date'] = date("d M, Y",strtotime($d->paid_date));
                	$row['amount'] = $d->amount;
                	
                	$li[$index_key] = $row;
	                $index_key++;
				}
			}
		}
		
		$response_array['result']['code'] = 200;
        $response_array['result']['list'] = $li;
        
		return $response_array;
	}
	
	/**
	 * addInvoice
	 */
	function addInvoice($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// invoice details
		$invoice_number = $params['invoice_details']['invoice_number'];
		
		// this is actually a customer user id, so we have to get it's company id
		$customer_user_id = $params['invoice_details']['fk_client_id'];
		$fk_client_id = $this->portalmodel->select_name('customer', 'fk_company_id', "customerid='".$customer_user_id."'");
		
		$fk_task_id = $params['invoice_details']['fk_task_id'];
		$invoice_date = date('Y-m-d', strtotime($params['invoice_details']['invoice_date']));
		$amount = $params['invoice_details']['amount'];
		$remaining_amount = $params['invoice_details']['amount'];
		$invoice_status = $params['invoice_details']['invoice_status'];
		$description = $params['invoice_details']['description'];
		
		$data = array(
            "invoice_number" => $invoice_number,
			"fk_client_id" => $fk_client_id,
			"fk_task_id" => $fk_task_id,
			"invoice_date" => $invoice_date,
			"amount" => $amount,
			"remaining_amount" => $remaining_amount,
			"invoice_status" => $invoice_status,
			"description" => $description,
			"created_by" => $id
        );
        $result = $this->portalmodel->insert_query_('invoices', $data);
        
		if($result){
			
			$files = (isset($params['invoice_details']['files']))?$params['invoice_details']['files']:'';
	        if(isset($files) && !empty($files)){
	        	foreach ($files as $eachFile){
	        		$target_dir = FCPATH."/projects/";
	        		
	        		$file_parts = explode(";base64,", $eachFile);
		        	$eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
	        		
	        		$file_name = $this->upload_file($target_dir, $eachFile);
	        		if(!empty($file_name)){
	        			$path = "/projects/".$file_name;
	        			$data1              = array(
		                    'file' => $path,
		                    'fk_invoice_id' => $result,
		                    'created_by' => $id
		                );
		                $result1    = $this->portalmodel->insert_query_('invoice_files', $data1);
	        		}
	        	}
	        }
			
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Invoice added successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }
		
		return $response_array;
	}
	
	/**
	 * updateInvoice
	 */
	function updateInvoice($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// invoice details
		$invoice_id = $params['invoice_details']['invoice_id'];
		$invoice_number = $params['invoice_details']['invoice_number'];
		
		// this is actually a customer user id, so we have to get it's company id
		$customer_user_id = $params['invoice_details']['fk_client_id'];
		$fk_client_id = $this->portalmodel->select_name('customer', 'fk_company_id', "customerid='".$customer_user_id."'");
		
		
		$fk_task_id = $params['invoice_details']['fk_task_id'];
		$invoice_date = date('Y-m-d', strtotime($params['invoice_details']['invoice_date']));
		$amount = $params['invoice_details']['amount'];
		$remaining_amount = $params['invoice_details']['remaining_amount'];
		$invoice_status = $params['invoice_details']['invoice_status'];
		$description = $params['invoice_details']['description'];
		$payments = $params['invoice_details']['payments'];
		
		$data = array(
            "invoice_number" => $invoice_number,
			"fk_client_id" => $fk_client_id,
			"fk_task_id" => $fk_task_id,
			"invoice_date" => $invoice_date,
			"amount" => $amount,
			"remaining_amount" => $remaining_amount,
			"invoice_status" => $invoice_status,
			"description" => $description,
			'modified_on'=>date("Y-m-d H:i:s"),
            'modified_by' => $id
        );
        $result = $this->portalmodel->update_query('invoices', $data, $invoice_id, 'id');
        
		$files = (isset($params['invoice_details']['files']))?$params['invoice_details']['files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/projects/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/projects/".$file_name;
        			$data1              = array(
	                    'file' => $path,
	                    'fk_invoice_id' => $invoice_id,
	                    'created_by' => $id
	                );
	                $result1    = $this->portalmodel->insert_query_('invoice_files', $data1);
        		}
        	}
        }
        
        foreach ($payments as $eachPayment){
        	$data2 = array(
				'fk_invoice_id' => $invoice_id,
				'task' => $eachPayment['paid_for_task'],
				'paid_date' => date("Y-m-d",strtotime($eachPayment['paid_on_date'])),
				'amount'=>$eachPayment['paid_amount']
			);
			$result2 = $this->portalmodel->insert_query_('invoice_paid', $data2);
        }
        
        $response_array['result']['code'] = 200;
        $response_array['result']['message'] = "Invoice updated successfully!";
        
		/*if($result){
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Invoice updated successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }*/
		
		return $response_array;
	}
	
	/**
	 * listProfiles
	 */
	function listProfiles($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$user_role = $this->portalmodel->select_name('user', 'role', "id='".$id."'");
		
		$type = strtolower($params['type']);
		
		$active_inactive = (isset($params['filter']['active_inactive']))?$params['filter']['active_inactive']:0;
		
		$list = array();
		$index_key = "A";
		
		if($type=="e"){
			// employee profiles
			$condition = "1 AND internal_user_external_user=1 AND role!=1";
			
			if(isset($active_inactive) && !empty($active_inactive)){
	        	$condition.=" AND status=".$active_inactive;
	        }
	        
	        $details = $this->portalmodel->select('', '', 'user', $condition,'first_name','ASC');
	        
			foreach ($details as $each){
				if($user_role==1){
					// provide record id as well
					$list[$index_key]['id'] = $each->id;
				}
				$list[$index_key]['first_name'] = $each->first_name;
				$list[$index_key]['last_name'] = $each->last_name;
				$list[$index_key]['email'] = $each->email;
				$list[$index_key]['phone'] = $each->phone;
				
				if($each->status==1){
					$list[$index_key]['status'] = "Active";
				}else if($each->status==2){
					$list[$index_key]['status'] = "In-Active";
				}else if($each->status==3){
					$list[$index_key]['status'] = "Deleted";
				}
				
				$index_key++;
			}
			
		}else if($type=="t"){
			// tech profiles
			
			$condition = "1 AND internal_user_external_user=2 AND role!=1";
			
			if(isset($active_inactive) && !empty($active_inactive)){
	        	$condition.=" AND status=".$active_inactive;
	        }
	        
	        $details = $this->portalmodel->select('', '', 'user', $condition,'first_name','ASC');
	        
			foreach ($details as $each){
				if($user_role==1){
					// provide record id as well
					$list[$index_key]['id'] = $each->id;
				}
				$list[$index_key]['first_name'] = $each->first_name;
				$list[$index_key]['last_name'] = $each->last_name;
				$list[$index_key]['email'] = $each->email;
				$list[$index_key]['phone'] = $each->phone;
				
				if($each->status==1){
					$list[$index_key]['status'] = "Active";
				}else if($each->status==2){
					$list[$index_key]['status'] = "In-Active";
				}else if($each->status==3){
					$list[$index_key]['status'] = "Deleted";
				}
				
				$index_key++;
			}
			
		}else if($type=="c"){
			// customer profiles
			$condition = ($user_role==1)?array():"1 AND accountmanagerid= $id";
			
			if(isset($active_inactive) && !empty($active_inactive)){
	        	$condition.=" AND status=".$active_inactive;
	        }
			
	        $details = $this->portalmodel->select('', '', 'customer',$condition,'companyname','ASC');
	        
			foreach ($details as $each){
				
				if($user_role==1){
					// provide record id as well
					$list[$index_key]['id'] = $each->customerid;
				}
				
				$list[$index_key]['first_name'] = $each->first_name;
				$list[$index_key]['last_name'] = $each->last_name;
				$list[$index_key]['companyname'] = $each->companyname;
				$list[$index_key]['emailid'] = $each->emailid;
				$list[$index_key]['phone'] = $each->contactno;
				
				$name = "CONCAT(first_name,' ',last_name) as name";
				$accountmanager = $this->portalmodel->select_username($name, 'id', $each->accountmanagerid, 'user');
				$list[$index_key]['accountmanager'] = $accountmanager;
				
				if($each->status==1){
					$list[$index_key]['status'] = "Active";
				}else if($each->status==2){
					$list[$index_key]['status'] = "In-Active";
				}else if($each->status==3){
					$list[$index_key]['status'] = "Deleted";
				}
				
				$index_key++;
			}
		}else if($type=="m"){
			// customer profiles
			$condition = array();
			
			$details = $this->portalmodel->select('', '', 'customer_company',$condition,'name','ASC');
	        
			foreach ($details as $each){
				
				if($user_role==1){
					// provide record id as well
					$list[$index_key]['id'] = $each->id;
				}
				$list[$index_key]['name'] = $each->name;
				
				$index_key++;
			}
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * listMessagesInbox
	 */
	function listMessagesInbox($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$list = array();
		$index_key = "A";
		
		if($is_customer){
			$where = array();
        	$where = " to='".$id."' AND to_type='C' AND from_type='U'";
        	
        	$details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        	
			foreach ($details as $key=>$values){
				$list[$index_key]['message_id'] = $values->id;
				
				$from_id = $this->portalmodel->select_username("employeeid as name", 'id', $values->created_by, 'user');
				
				$list[$index_key]['from_id'] = $from_id;
				$list[$index_key]['from_type'] = $values->from_type;
				$list[$index_key]['subject'] = $values->subject;
				
	        	$list[$index_key]['from_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->created_by, 'user');
	        	
	        	$list[$index_key]['read_unread'] = $values->read_unread;
	        	
	        	$index_key++;
	        }
		}else{
			$where = array();
	        $where = " to='".$id."'";
	        
	        $details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
			
	        foreach ($details as $key=>$values){
	        	$list[$index_key]['message_id'] = $values->id;
	        	
	        	if($values->from_type=='C'){
	        		$from_id = $this->portalmodel->select_username("username as name", 'customerid', $values->created_by, 'customer');
	        	}else{
	        		$from_id = $this->portalmodel->select_username("employeeid as name", 'id', $values->created_by, 'user');
	        	}
	        	
	        	$list[$index_key]['from_id'] = $from_id;
	        	$list[$index_key]['from_type'] = $values->from_type;
	        	$list[$index_key]['subject'] = $values->subject;
	        	
	        	if($values->from_type=='C'){
	        		$list[$index_key]['from_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'customerid', $values->created_by, 'customer');
	        		
		        	// also select the company name
	        		$fk_company_id = $this->portalmodel->select_username("fk_company_id as name", 'customerid', $values->created_by, 'customer');
	        		if(!empty($fk_company_id)){
	        			$company_name = $this->portalmodel->select_username("name", 'id', $fk_company_id, 'customer_company');
	        			if(!empty($company_name)){
	        				$list[$index_key]['from_name'] = $list[$index_key]['from_name']." (".$company_name.")";
	        			}
	        		}
	        		
	        	}else{
	        		$list[$index_key]['from_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->created_by, 'user');
	        	}
	        	
	        	$list[$index_key]['read_unread'] = $values->read_unread;
	        	
	        	$index_key++;
	        }
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * listMessagesSent
	 */
	function listMessagesSent($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$list = array();
		$index_key = "A";
		
		if($is_customer){
			$where = array();
        	$where = " created_by='".$id."' AND from_type='C'";
        	
        	$details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        	
			foreach ($details as $key=>$values){
				$list[$index_key]['message_id'] = $values->id;
				$list[$index_key]['to_id'] = $values->to;
				$list[$index_key]['to_type'] = $values->to_type;
				$list[$index_key]['subject'] = $values->subject;
				
	        	$list[$index_key]['to_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->to, 'user');
	        	
	        	$index_key++;
	        }
		}else{
			$where = array();
        	$where = " created_by='".$id."' AND from_type='U'";
        	
        	$details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        	
			foreach ($details as $key=>$values){
				$list[$index_key]['message_id'] = $values->id;
				$list[$index_key]['to_id'] = $values->to;
				$list[$index_key]['to_type'] = $values->to_type;
	        	$list[$index_key]['subject'] = $values->subject;
	        	
	        	if($values->to_type=='C'){
	        		$list[$index_key]['to_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'customerid', $values->to, 'customer');
	        		
		        	// also select the company name
	        		$fk_company_id = $this->portalmodel->select_username("fk_company_id as name", 'customerid', $values->to, 'customer');
	        		if(!empty($fk_company_id)){
	        			$company_name = $this->portalmodel->select_username("name", 'id', $fk_company_id, 'customer_company');
	        			if(!empty($company_name)){
	        				$list[$index_key]['to_name'] = $list[$index_key]['to_name']." (".$company_name.")";
	        			}
	        		}
	        	}else{
	        		$list[$index_key]['to_name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->to, 'user');
	        	}
	        	
	        	$index_key++;
	        }
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * addMessage
	 */
	function addMessage($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// variables
		$subject = $params['message_details']['subject'];
		$message = $params['message_details']['message'];
		
		if($is_customer){
			
			// get account manager name
			$condition = "customerid = '".$id."'";
	    	$to = $this->portalmodel->select_name('customer', 'accountmanagerid', $condition);
			
			// add message for customer
			$data           = array(
	            'to' => $to,
	            'subject' => $subject,
	            'message' => $message,
	            'created_by' => $id,
	        	'from_type' => 'C',
	        	'to_type' => 'U'
	        );
	        
	        $result = $this->portalmodel->insert_query_('messages', $data);
	        
	        // now also send email to the provided email for 'To'
			$to_email = $this->portalmodel->select_name("user","email","id='".$to."'");
	        if(!empty($to_email) && $to_email!="N/A"){
	        	$email = $this->email($to_email, $subject, $message);
	        }
	        
			// also send email to business email
			$to_business_email = $this->portalmodel->select_name("user","business_email","id='".$to."'");
	        if(!empty($to_business_email) && $to_business_email!="N/A"){
	        	$email = $this->email($to_business_email, $subject, $message);
	        }
	        
		}else{
			// add message for employee/tech
			$to = $params['message_details']['to'];
			$tmp_to_arr[0] = substr($to, 0, 1);
			
			if($tmp_to_arr[0]=='E'){
				// take to id from user
	        	$to_id = $this->portalmodel->select_name('user', 'id', "employeeid='".$to."'");
	        	$to_type = 'U';
	        	
	        	// take to id from user
	        	$to_email = $this->portalmodel->select_name('user', 'email', "employeeid='".$to."'");
	        	
	        	// to business email
	        	$to_business_email = $this->portalmodel->select_name('user', 'business_email', "employeeid='".$to."'");
			}else {
				// its customer
	        	$to_id = $to_id = $this->portalmodel->select_name('customer', 'customerid', "username='".$to."'");
	        	$to_type = 'C';
	        	
	        	// its customer
	        	$to_email = $this->portalmodel->select_name('customer', 'emailid', "username='".$to."'");
	        	
	        	// to business email
	        	$to_business_email = $this->portalmodel->select_name('customer', 'business_email', "username='".$to."'");
			}
			
			$data = array(
	            'to' => $to_id,
	            'subject' => $subject,
	            'message' => $message,
	            'created_by' => $id,
	        	'from_type' => 'U',
	        	'to_type' => $to_type
	        );
	        
	        // now send email
			if(!empty($to_email) && $to_email!="N/A"){
	        	$email = $this->email($to_email, $subject, $message);
	        }
	        
	        if(!empty($to_business_email) && $to_business_email!='N/A'){
	        	$email = $this->email($to_business_email, $subject, $message);
	        }
	        
	        $result = $this->portalmodel->insert_query_('messages', $data);
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['message'] = "Message sent successfully!";
			
		return $response_array;
		
	}
	
	/**
	 * saveSettings
	 */
	function saveSettings($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$current_password = $params['profile_details']['current_password'];
		$new_password = $params['profile_details']['new_password'];
		$confirm_new_password = $params['profile_details']['confirm_new_password'];
		
		if($is_customer){
			
			$companyname = $params['profile_details']['companyname'];
			$first_name = $params['profile_details']['first_name'];
			$last_name = $params['profile_details']['last_name'];
			$contact_number = $params['profile_details']['contact_number'];
			$business_email = $params['profile_details']['business_email'];
			$media = $params['profile_details']['files'];
			
			if(empty($current_password)){
				// save settings only
				if(isset($companyname) && !empty($companyname)){
					$data['companyname'] = $companyname;
				}
				if(isset($first_name) && !empty($first_name)){
					$data['first_name'] = $first_name;
				}
				if(isset($last_name) && !empty($last_name)){
					$data['last_name'] = $last_name;
				}
				if(isset($contact_number) && !empty($contact_number)){
					$data['contactno'] = $contact_number;
				}
				if(isset($business_email) && !empty($business_email)){
					$data['business_email'] = $business_email;
				}
				$data['modified_by'] = $id;
				
				/*$data = array(
	                'companyname' => $companyname,
	                'first_name' => $first_name,
	                'last_name' => $last_name,
	                'contactno' => $contact_number,
	            	'business_email' => $business_email,
	                'modified_by' => $id
	            );*/
	            $update = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
	            
	            // if the file for profile picture is there.
	            if(isset($media) && !empty($media)){
		            $filename = strtotime("now");
		            $file_extension=".jpg";
		            $quality="100";
		            $image_parts = explode(";base64,", $media);
		            $newPic=base64_decode($image_parts[1]);
		            $im = imagecreatefromstring($newPic);
		            if ($im !== false){
		            	$path=$_SERVER['DOCUMENT_ROOT']."/user/".$filename.$file_extension;
						imagejpeg($im,$path,$quality);
						imagedestroy($im);
						$filename = "user/".$filename.$file_extension;
						
						if(!empty($filename)){
							$data1              = array(
			                    'profile_picture' => $filename
			                );
			                $result = $this->portalmodel->update_query('customer', $data1, $id, 'customerid');
						}
		            }
	            }
	            
	            
	            /*if(isset($_FILES['files'][0]['name']) && !empty($_FILES['files'][0]['name'])){
	            	$filename = strtotime("now");
	            	$this->load->library('upload');
	            	
	            	$_FILES['userfile']['name']     = $_FILES['files'][0]['name'];
		            $_FILES['userfile']['type']     = $_FILES['files'][0]['type'];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files'][0]['tmp_name'];
		            $_FILES['userfile']['error']    = $_FILES['files'][0]['error'];
		            $_FILES['userfile']['size']     = $_FILES['files'][0]['size'];
		            
		            $dir_path  = './user/';
		            $ext = pathinfo($_FILES['files'][0]['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path = 'user/' . $filename . '.' . $ext;
		            $config = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif',
		                'max_size' => 3000,
		                'overwrite' => FALSE,
		                'upload_path' => $dir_path
		            );
		            $this->upload->initialize($config);
		            if (!$this->upload->do_upload()):
		                $error = array(
		                    'error' => $this->upload->display_errors()
		                );
		            else:
		                $final_files_data[] = $this->upload->data();
		                $data1              = array(
		                    'profile_picture' => $path
		                );
		                $result = $this->portalmodel->update_query('customer', $data1, $id, 'customerid');
		            endif;
	            }*/
	            
				$response_array['result']['code'] = 200;
				$response_array['result']['message'] = "Settings saved successfully!";
	            
			}else{
				// save settings with update password
				
				$where = 'customerid='.$id;
				$current_password_in_db = $this->portalmodel->select_name('customer', 'password', $where);
				if(base64_encode($current_password)==$current_password_in_db){
					$new_password = base64_encode($new_password);
					
					if(isset($companyname) && !empty($companyname)){
						$data['companyname'] = $companyname;
					}
					if(isset($first_name) && !empty($first_name)){
						$data['first_name'] = $first_name;
					}
					if(isset($last_name) && !empty($last_name)){
						$data['last_name'] = $last_name;
					}
					if(isset($contact_number) && !empty($contact_number)){
						$data['contactno'] = $contact_number;
					}
					if(isset($business_email) && !empty($business_email)){
						$data['business_email'] = $business_email;
					}
					if(isset($new_password) && !empty($new_password)){
						$data['password'] = $new_password;
					}
					$data['modified_by'] = $id;
					
					/*$data = array(
	                    'companyname' => $companyname,
	                    'first_name' => $first_name,
	                    'last_name' => $last_name,
	                    'contactno' => $contact_number,
	                    'business_email' => $business_email,
	                    'password' => $new_password,
	                    'modified_by' => $id
	                );*/
	                
	                $update = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
	                
				// if the file for profile picture is there.
				if(isset($media) && !empty($media)){
		            $filename = strtotime("now");
		            $file_extension=".jpg";
		            $quality="100";
		            $image_parts = explode(";base64,", $media);
		            $newPic=base64_decode($image_parts[1]);
		            $im = imagecreatefromstring($newPic);
		            if ($im !== false){
		            	$path=$_SERVER['DOCUMENT_ROOT']."/user/".$filename.$file_extension;
						imagejpeg($im,$path,$quality);
						imagedestroy($im);
						$filename = "user/".$filename.$file_extension;
						
						if(!empty($filename)){
							$data1              = array(
			                    'profile_picture' => $filename
			                );
			                $result = $this->portalmodel->update_query('customer', $data1, $id, 'customerid');
						}
		            }
	            }
	            
	            /*if(isset($_FILES['files'][0]['name']) && !empty($_FILES['files'][0]['name'])){
	            	$filename = strtotime("now");
	            	$this->load->library('upload');
	            	
	            	$_FILES['userfile']['name']     = $_FILES['files'][0]['name'];
		            $_FILES['userfile']['type']     = $_FILES['files'][0]['type'];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files'][0]['tmp_name'];
		            $_FILES['userfile']['error']    = $_FILES['files'][0]['error'];
		            $_FILES['userfile']['size']     = $_FILES['files'][0]['size'];
		            
		            $dir_path  = './user/';
		            $ext = pathinfo($_FILES['files'][0]['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path = 'user/' . $filename . '.' . $ext;
		            $config = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif',
		                'max_size' => 3000,
		                'overwrite' => FALSE,
		                'upload_path' => $dir_path
		            );
		            $this->upload->initialize($config);
		            if (!$this->upload->do_upload()):
		                $error = array(
		                    'error' => $this->upload->display_errors()
		                );
		            else:
		                $final_files_data[] = $this->upload->data();
		                $data1              = array(
		                    'profile_picture' => $path
		                );
		                $result = $this->portalmodel->update_query('customer', $data1, $id, 'customerid');
		            endif;
	            }*/
	                
					$response_array['result']['code'] = 200;
					$response_array['result']['message'] = "Settings saved successfully!";
				}else {
					$response_array['error']['code'] = 401;
					$response_array['error']['message'] = "Current Password do not match!";
				}
			}
		}else{
			
			$first_name = $params['profile_details']['first_name'];
			$last_name = $params['profile_details']['last_name'];
			$contact_number = $params['profile_details']['contact_number'];
			$business_email = $params['profile_details']['business_email'];
			$media = $params['profile_details']['files'];
			
			// for employees / tech
			if(empty($current_password)){
				// save settings only
				
				if(isset($first_name) && !empty($first_name)){
					$data['first_name'] = $first_name;
				}
				if(isset($last_name) && !empty($last_name)){
					$data['last_name'] = $last_name;
				}
				if(isset($contact_number) && !empty($contact_number)){
					$data['phone'] = $contact_number;
				}
				if(isset($business_email) && !empty($business_email)){
					$data['business_email'] = $business_email;
				}
				$data['modified_by'] = $id;
				
				/*$data   = array(
	                'first_name' => $first_name,
	                'last_name' => $last_name,
	                'phone' => $contact_number,
	            	'business_email' =>$business_email,
	                'modified_by' => $id
	            );*/
	            $update = $this->portalmodel->update_query('user', $data, $id, 'id');
	            
				// if the file for profile picture is there.
				if(isset($media) && !empty($media)){
		            $filename = strtotime("now");
		            $file_extension=".jpg";
		            $quality="100";
		            $image_parts = explode(";base64,", $media);
		            $newPic=base64_decode($image_parts[1]);
		            $im = imagecreatefromstring($newPic);
		            if ($im !== false){
		            	$path=$_SERVER['DOCUMENT_ROOT']."/user/".$filename.$file_extension;
						imagejpeg($im,$path,$quality);
						imagedestroy($im);
						$filename = "user/".$filename.$file_extension;
						
						if(!empty($filename)){
							$data1              = array(
			                    'userprofile' => $filename
			                );
			                $result = $this->portalmodel->update_query('user', $data1, $id, 'id');
						}
		            }
	            }
	            
				// other document files associated with logged in user
		        $files = (isset($params['profile_details']['other_files']))?$params['profile_details']['other_files']:'';
		        if(isset($files) && !empty($files)){
		        	foreach ($files as $eachFile){
		        		
		        		$file_name = (!empty($eachFile['file_name']))?$eachFile['file_name']:"";
		        		$file = $eachFile['file'];
		        		
		        		$file_parts = explode(";base64,", $file);
		        		$file = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
		        		
		        		$target_dir = FCPATH."/user/contracts/";
		        		$fileName = $this->upload_file($target_dir, $file);
		        		if(!empty($fileName)){
		        			$path = "/user/contracts/".$fileName;
		        			$data1              = array(
			                    'fk_user_id'=>$id,
			                    'file_name' => $file_name,
			                	'file' => $path,
				                'created_by' => $id,
					            'created_on' => date("Y-m-d H:i:s"),
					            'modified_by' => '0',
					            'modified_on' => "0000-00-00 00:00:00"
			                );
			                $result1    = $this->portalmodel->insert_query_('user_contracts', $data1);
		        		}
		        	}
		        }
	            
                /*if(isset($_FILES['files'][0]['name']) && !empty($_FILES['files'][0]['name'])){
                	$filename = strtotime("now");
                	$this->load->library('upload');
                	
                	$_FILES['userfile']['name']     = $_FILES['files'][0]['name'];
		            $_FILES['userfile']['type']     = $_FILES['files'][0]['type'];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files'][0]['tmp_name'];
		            $_FILES['userfile']['error']    = $_FILES['files'][0]['error'];
		            $_FILES['userfile']['size']     = $_FILES['files'][0]['size'];
		            $dir_path                       = './user/';
		            $ext                            = pathinfo($_FILES['files'][0]['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/' . $filename . '.' . $ext;
		            $config = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif',
		                'max_size' => 3000,
		                'overwrite' => FALSE,
		                'upload_path' => $dir_path
		            );
		            $this->upload->initialize($config);
		            if (!$this->upload->do_upload()):
		                $error = array(
		                    'error' => $this->upload->display_errors()
		                );
		            else:
		                $final_files_data[] = $this->upload->data();
		                $data1 = array(
		                    'userprofile' => $path
		                );
		                $result = $this->portalmodel->update_query('user', $data1, $id, 'id');
		            endif;
                }*/
	            
	            $response_array['result']['code'] = 200;
				$response_array['result']['message'] = "Settings saved successfully!";
	            
			}else{
				// save settings with update password
				$where = 'id='.$id;
				$current_password_in_db = $this->portalmodel->select_name('user', 'password', $where);
				if(base64_encode($current_password)==$current_password_in_db){
					$new_password = base64_encode($new_password);
					
					if(isset($first_name) && !empty($first_name)){
						$data['first_name'] = $first_name;
					}
					if(isset($last_name) && !empty($last_name)){
						$data['last_name'] = $last_name;
					}
					if(isset($contact_number) && !empty($contact_number)){
						$data['phone'] = $contact_number;
					}
					if(isset($business_email) && !empty($business_email)){
						$data['business_email'] = $business_email;
					}
					if(isset($new_password) && !empty($new_password)){
						$data['password'] = $new_password;
					}
					$data['modified_by'] = $id;
					
					/*$data   = array(
		                'first_name' => $first_name,
		                'last_name' => $last_name,
		                'phone' => $contact_number,
		            	'business_email' =>$business_email,
						'password' => $new_password,
		                'modified_by' => $id
		            );*/
		            $update = $this->portalmodel->update_query('user', $data, $id, 'id');
		            
				// if the file for profile picture is there.
				if(isset($media) && !empty($media)){
		            $filename = strtotime("now");
		            $file_extension=".jpg";
		            $quality="100";
		            $image_parts = explode(";base64,", $media);
		            $newPic=base64_decode($image_parts[1]);
		            $im = imagecreatefromstring($newPic);
		            if ($im !== false){
		            	$path=$_SERVER['DOCUMENT_ROOT']."/user/".$filename.$file_extension;
						imagejpeg($im,$path,$quality);
						imagedestroy($im);
						$filename = "user/".$filename.$file_extension;
						
						if(!empty($filename)){
							$data1              = array(
			                    'userprofile' => $filename
			                );
			                $result = $this->portalmodel->update_query('user', $data1, $id, 'id');
						}
		            }
	            }
	            
				// other document files associated with logged in user
		        $files = (isset($params['profile_details']['other_files']))?$params['profile_details']['other_files']:'';
		        if(isset($files) && !empty($files)){
		        	foreach ($files as $eachFile){
		        		
		        		$file_name = $eachFile['file_name'];
		        		$file = $eachFile['file'];
		        		
		        		$file_parts = explode(";base64,", $file);
		        		$file = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
		        		
		        		$target_dir = FCPATH."/user/contracts/";
		        		$fileName = $this->upload_file($target_dir, $file);
		        		if(!empty($fileName)){
		        			$path = "/user/contracts/".$fileName;
		        			$data1              = array(
			                    'fk_user_id'=>$id,
			                    'file_name' => $file_name,
			                	'file' => $path,
				                'created_by' => $id,
					            'created_on' => date("Y-m-d H:i:s"),
					            'modified_by' => '0',
					            'modified_on' => "0000-00-00 00:00:00"
			                );
			                $result1    = $this->portalmodel->insert_query_('user_contracts', $data1);
		        		}
		        	}
		        }
	            
                /*if(isset($_FILES['files'][0]['name']) && !empty($_FILES['files'][0]['name'])){
                	$filename = strtotime("now");
                	$this->load->library('upload');
                	
                	$_FILES['userfile']['name']     = $_FILES['files'][0]['name'];
		            $_FILES['userfile']['type']     = $_FILES['files'][0]['type'];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files'][0]['tmp_name'];
		            $_FILES['userfile']['error']    = $_FILES['files'][0]['error'];
		            $_FILES['userfile']['size']     = $_FILES['files'][0]['size'];
		            $dir_path                       = './user/';
		            $ext                            = pathinfo($_FILES['files'][0]['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/' . $filename . '.' . $ext;
		            $config = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif',
		                'max_size' => 3000,
		                'overwrite' => FALSE,
		                'upload_path' => $dir_path
		            );
		            $this->upload->initialize($config);
		            if (!$this->upload->do_upload()):
		                $error = array(
		                    'error' => $this->upload->display_errors()
		                );
		            else:
		                $final_files_data[] = $this->upload->data();
		                $data1 = array(
		                    'userprofile' => $path
		                );
		                $result = $this->portalmodel->update_query('user', $data1, $id, 'id');
		            endif;
                }*/
		            
		            $response_array['result']['code'] = 200;
					$response_array['result']['message'] = "Settings saved successfully!";
		            
				}else{
					$response_array['error']['code'] = 401;
					$response_array['error']['message'] = "Current Password do not match!";
				}
			}
		}
		
		return $response_array;
		
	}
	
	/**
	 * dashboardCounters
	 */
	function dashboardCounters($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// get all counters
		$list = array();
		$ticket_progress = array("notstarted"=>0,"started"=>0,"inprogress"=>0,"onhold"=>0,"completed"=>0);
		$open=0;
		
		if($is_customer){
			// get customer dashboard
			
			// my projects
			// select company
	        $where_company = " customerid='$id'";
	        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
	        
			$where_my_project_count = "customerid='".$company_id."'";
        	$list['my_projects'] = $this->portalmodel->record_count_where('project', $where_my_project_count);
        	
        	// also get sum of pending invoices
        	$list['pending_invoice_amount'] = $this->portalmodel->select_name('invoices', 'SUM(remaining_amount)', "fk_client_id='".$company_id."' AND invoice_status='U'");
        	
        	// new messages
        	$list['new_messages'] = $this->portalmodel->select_name('messages', 'count(id)', "to='".$id."' AND read_unread=0 AND to_type='C'");
        	
        	// task notifications
        	$customer_or_employee_id = $this->portalmodel->select_username("username AS name","customerid",$id,"customer");
        	$list['notifications'] = $this->portalmodel->select_name("task_notifications","COUNT(id)","fk_customer_or_employee_id='".$customer_or_employee_id."' AND read_unread=0 AND user_type='C'");
        	
        	
        	// get name of customer
        	$list['name'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) AS name","customerid",$id,"customer");
        	
        	// get profile picture
        	$profile_picture = $this->portalmodel->select_username("profile_picture AS name","customerid",$id,"customer");
        	$list['profile_picture'] = (!empty($profile_picture) && $profile_picture!="N/A")?base_url().$profile_picture:"";
        	
        	// top bar notifications for tasks progress
        	$where = " customerid='$id' and show_customer=0";
            $names = "status, COUNT(status)as count";
            $taskstatus = $this->portalmodel->select_group('task', $names, 'status', $where);
            $count = 0;$open=0;
			if (!empty($taskstatus)) {
                foreach ($taskstatus as $d) {
                    if ($d->status == 0) {
                        $ticket_progress['notstarted'] = $d->count;
                    }
                    if ($d->status == 1) {
                        $ticket_progress['started'] = $d->count;
                    }
                    if ($d->status == 2) {
                        $ticket_progress['inprogress'] = $d->count;
                    }
                    if ($d->status == 3) {
                        $ticket_progress['onhold'] = $d->count;
                    }
                    if ($d->status == 4) {
                        $ticket_progress['completed'] = $d->count;
                    }
                }
            }
            $list['ticket_progress'] = $ticket_progress;
        	
		}else{
			$user_role = $this->portalmodel->select_name('user', 'role', "id='".$id."'");
			
			if($user_role==1){
				// its admin.
				
				// TOTAL PROFILES
				$total_profiles = 0;
				
				// user profiles
				$this->db->select("count(id) AS total_profiles");
        		$this->db->from("user");
				$query = $this->db->get();
		        $query->num_rows();
		        if ($query->num_rows() > 0) {
		        	$row = $query->result();
		        	$total_profiles = intval($total_profiles) + intval($row[0]->total_profiles);
		        }
		        
		        // customer profiles
				$this->db->select("count(customerid) AS total_profiles");
		        $this->db->from("customer");
		        $query = $this->db->get();
		        $query->num_rows();
		        if ($query->num_rows() > 0) {
		        	$row = $query->result();
		        	$total_profiles = intval($total_profiles) + intval($row[0]->total_profiles);
		        }
		        
		        $list['total_profiles'] = $total_profiles;
		        
		        // TOTAL PROJECTS
				$condition_count_of_projects = array();
		        $countofprojects = $this->portalmodel->select_name("project","COUNT(id)",$condition_count_of_projects);
	        	if(!empty($countofprojects) && $countofprojects!="N/A"){
	        		$list['total_projects']=$countofprojects;
	        	}else{
	        		$list['total_projects']=0;
	        	}
	        	
	        	// NEW MESSAGES
	        	$list['new_messages'] = $this->portalmodel->select_name('messages', 'count(id)', "to='".$id."' AND read_unread=0 AND to_type='U'");
			}else{
				// its employee/tech
				// get employee or tech dashboard
				
				// ticket progress
				$where = " assigned_to='$id' ";
				$where.=" OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ))) ";
				$names = "status, COUNT(status)as count";
				$taskstatus = $this->portalmodel->select_group('task', $names, 'status', $where);
				if (!empty($taskstatus)) {
					foreach ($taskstatus as $d) {
						if ($d->status == 0) {
	                        $ticket_progress['notstarted'] = $d->count;
	                        $open=$open+ $d->count;
	                    }
	                    if ($d->status == 1) {
	                        $ticket_progress['started'] = $d->count;
	                        $open=$open+ $d->count;
	                    }
	                    if ($d->status == 2) {
	                        $ticket_progress['inprogress'] = $d->count;
	                        $open=$open+ $d->count;
	                    }
	                    if ($d->status == 3) {
	                        $ticket_progress['onhold'] = $d->count;
	                        $open=$open+ $d->count;
	                    }
	                    if ($d->status == 4) {
	                        $ticket_progress['completed'] = $d->count;
	                    }
					}
				}
				$list['ticket_progress'] = $ticket_progress;
				
				// get notifications for ticket/task updates
				$customer_or_employee_id = $this->portalmodel->select_username("employeeid AS name","id",$id,"user");;
		        $ticket_changes_notification = $this->portalmodel->select_name("task_notifications","COUNT(id)","fk_customer_or_employee_id='".$customer_or_employee_id."' AND read_unread=0 AND user_type='E'");
		        if(!empty($ticket_changes_notification) && $ticket_changes_notification!='N/A'){
		        	$list['notifications']=$ticket_changes_notification;
		        }else{
		        	$list['notifications']=0;
		        }
		        
		        // open tickets
		        $list['open_tickets'] = $open;
		        
		        // new messages
		        $list['new_messages'] = $this->portalmodel->select_name('messages', 'count(id)', "to='".$id."' AND read_unread=0 AND to_type='U'");
				
				if($internal_user_external_user==1){
					// employee
					// count of project
					$condition_count_of_projects = "(accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ) or FIND_IN_SET( $id, developer )";
					$countofprojects = $this->portalmodel->select_name("project","COUNT(id)",$condition_count_of_projects);
					$list['open_projects'] = (!empty($countofprojects) && $countofprojects!="N/A")?$countofprojects:"0";
					
					// open workorders
					$condition_count_of_workorders = "status!=4";
					$condition_count_of_workorders.=" and created_by = '".$id."'";
					$countofworkorders = $this->portalmodel->select_name("workorder","COUNT(id_workorder)",$condition_count_of_workorders);
					$list['open_workorders'] = (!empty($countofworkorders) && $countofworkorders!="N/A")?$countofworkorders:"0";
				}else{
					// tech
					// open workorders
					$condition_count_of_workorders = "status!=4";
					$condition_count_of_workorders.=" and fk_assigned_to = '".$id."'";
					$countofworkorders = $this->portalmodel->select_name("workorder","COUNT(id_workorder)",$condition_count_of_workorders);
					$list['open_workorders'] = (!empty($countofworkorders) && $countofworkorders!="N/A")?$countofworkorders:"0";
				}
				
				// get profile picture
	        	$profile_picture = $this->portalmodel->select_username("userprofile AS name","id",$id,"user");
	        	$list['profile_picture'] = (!empty($profile_picture) && $profile_picture!="N/A")?base_url().$profile_picture:"";
			}
		}
		
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getAccountManager
	 */
	function getAccountManager($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$fk_customer_id = $params['fk_customer_id'];
		
		$where = "customerid=$fk_customer_id";
		$cust  = $this->portalmodel->select_where('', '', 'customer', $where);
		$cond  = "CONCAT(first_name,' ',last_name) as name";
		if (!empty($cust)) {
			$where = "id=" . $cust[0]->accountmanagerid;
			$acc = $this->portalmodel->select('', '', 'user',$where,'first_name','ASC');
		}else{
			// get all
			$acc = $this->portalmodel->select('', '', 'user','','first_name','ASC');//$this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '');
		}
		
		$list = array();
		$index_key = "A";
		foreach ($acc as $eachAccountManager){
			$list[$index_key]['id'] = $eachAccountManager->id;
			$list[$index_key]['code'] = $eachAccountManager->employeeid;
			$list[$index_key]['name'] = $eachAccountManager->first_name." ".$eachAccountManager->last_name;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getTech
	 */
	function getTech($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$project_id = (isset($params['project_id']) && !empty($params['project_id']))?$params['project_id']:"";
		
		$where2 = "internal_user_external_user=2";
		if(!empty($project_id) && $internal_user_external_user==2){
			$where1 = "id=$project_id";
			$prj = $this->portalmodel->select_where('', '', 'project', $where1);
			$dev = $prj[0]->developer;
			
			$arr = explode(",", $dev);
	        $arr = array_filter($arr, function($value){ return $value !== ''; });
	        
	        $dev = implode(",", $arr);
	        
	        $where2 .= " AND id in ($dev)";
		}
        
        //if(!empty($dev)){
        	//$where2 = array();//"id in ($dev)";
        	$cond = "CONCAT(first_name,' ',last_name) as name";
        	$developer  = $this->portalmodel->select('', '', 'user',$where2,'first_name','ASC');
        /*}else{
        	$developer = array();
        }*/
		
		$list = array();
		$index_key = "A";
		foreach ($developer as $eachDev){
			$list[$index_key]['id'] = $eachDev->id;
			$list[$index_key]['code'] = $eachDev->employeeid;
			$list[$index_key]['name'] = $eachDev->first_name." ".$eachDev->last_name;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * listTicketComments
	 */
	function listTicketComments($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$ticket_id = $params['ticket_id'];
		
		$where = "taskid=$ticket_id ";
		
		if($is_customer){
			$where.=" AND show_customer=0";
		}
		
		$details = $this->portalmodel->select_where_cond('', '', 'taskcomments', $where,"created_on","DESC");
		
		$list = array();
		$index_key = "A";
		foreach ($details as $d){
			
			$arr = array();
			
			$arr['comments'] = $d->comments;
			
			if ($d->commented_by == 1) {
				$name = "CONCAT(first_name,' ',last_name) as name";
				$arr['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
			} else {
				$name = "CONCAT(first_name,' ',last_name) as name";
				$arr['commented_by'] = $this->portalmodel->select_username($name, 'customerid', $d->created_by, 'customer');
			}
			
			$arr['commented_on'] = $d->created_on;
			
			// get files
			$where_files = "comment_id=$d->id";
			$files = $this->portalmodel->select_where('', '', 'comment_images', $where_files);
			//$arr['files'] = ($files)?$files:array();
			$li = array();
			$index_key_inner = "A";
			foreach ($files as $eachFile){
				if(isset($eachFile->image_path) && !empty($eachFile->image_path)){
					$li[$index_key_inner] = base_url().$eachFile->image_path;
					$index_key_inner++;
				}
			}
			$arr['files']=$li;
			
			
			$list[$index_key] = $arr;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * addTicketComments
	 */
	function addTicketComments($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// comment details
		$task_id = $params['comment_details']['task_id'];
		$comments = $params['comment_details']['comments'];
		
		// now get project id from ticket id
		$condition = "id = '".$task_id."'";
	    $project_id = $this->portalmodel->select_name('task', 'projectid', $condition);
		
		
		
		if($is_customer){
			$show = 0;
			$commented_by = 0;
		}else{
			$show = $params['comment_details']['show_to_customer'];
			$commented_by = 1;
		}
		
		$data   = array(
            'taskid' => $task_id,
            'comments' => $comments,
            'project_id' => $project_id,
            'show_customer' => $show,
            'commented_by' => $commented_by,
            'created_by' => $id
        );
        $result = $this->portalmodel->insert_query_('taskcomments', $data);
        
		if($result){
			
			$files = (isset($params['comment_details']['files']))?$params['comment_details']['files']:'';
	        if(isset($files) && !empty($files)){
	        	foreach ($files as $eachFile){
	        		$target_dir = FCPATH."/comments/";
	        		
	        		$file_parts = explode(";base64,", $eachFile);
		        	$eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
	        		
	        		$file_name = $this->upload_file($target_dir, $eachFile);
	        		if(!empty($file_name)){
	        			$path = "/comments/".$file_name;
	        			$data1              = array(
		                    'image_path' => $path,
		                    'comment_id' => $result,
		                    'created_by' => $id
		                );
		                $result1    = $this->portalmodel->insert_query_('comment_images', $data1);
	        		}
	        	}
	        }
			
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Comment added successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }
		
		return $response_array;
	}
	
	/**
	 * getTickets
	 */
	function getTickets($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$customer_id = $params['customer_id'];
		
		$where = "customerid=$customer_id";
		$combined = "CONCAT(taskid,' - ',title) AS name";
		$tickets = $this->portalmodel->select_where('', '', 'task', $where);
		
		$list = array();
		$index_key = "A";
		foreach ($tickets as $d){
			$list[$index_key]['id'] = $d->id;
			$list[$index_key]['name'] = $d->taskid." - ".$d->title;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * listTicketNotifications
	 */
	function listTicketNotifications($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$where = array();
        if($is_customer){
			$customer_or_employee_id = $this->portalmodel->select_username("username AS name","customerid",$id,"customer");
			$where = " fk_customer_or_employee_id='".$customer_or_employee_id."'";
			$where .= " AND user_type='C'";
		}else{
			$customer_or_employee_id = $this->portalmodel->select_username("employeeid AS name","id",$id,"user");;
			$where = " fk_customer_or_employee_id='".$customer_or_employee_id."'";
			$where .= " AND user_type='E'";
		}
		
		// get ticket notification for customers
		$details = $this->portalmodel->select('', '', 'task_notifications', $where,'created_on','DESC');
		
		$list = array();
		$index_key = "A";
		foreach ($details as $d){
			$list[$index_key]['fk_task_id'] = $d->fk_task_id;
			$list[$index_key]['changes'] = $d->changes;
			$list[$index_key]['created_on'] = $d->created_on;
			$list[$index_key]['ticket_id'] = $this->portalmodel->select_username("taskid as name", 'id', $d->fk_task_id, 'task');
			
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getListToSendMessage
	 */
	function getListToSendMessage($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$to_customer = array();
		
		$where = "id!=".$id;
        $to_user = $this->portalmodel->select('', '', 'user',$where,'first_name','ASC');
        
		if(!$is_customer && $internal_user_external_user==1){
			// list for account managers
			$where = "accountmanagerid='".$id."'";
			$to_customer = $this->portalmodel->select('', '', 'customer',$where,'companyname','ASC');
		}
		
		$list = array();
		$index_key = "A";
		foreach ($to_user as $d){
			$list[$index_key]['id'] = $d->employeeid;
			$list[$index_key]['name'] = $d->first_name." ".$d->last_name;
			$index_key++;
		}
		foreach ($to_customer as $d){
			$list[$index_key]['id'] = $d->username;
			$list[$index_key]['name'] = $d->companyname;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getProfileReports
	 */
	function getProfileReports($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$newData = array();
		$newData['Active']=0;
		$newData['In-Active']=0;
		$newData['Closed']=0;
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		
		// GET REPORTS ROW DATA
		$arrstatus = array(0=>"Active",1=>"Active",2=>"In-Active",3=>"Closed");	// 0 active is for customer
		$arrmonths = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September",10=>"October",11=>"November",12=>"December");
		$overalltotal = 0;
		
		$data = array();
		
		$data['active_inactive_users'] = (isset($params['filter']['active_inactive_users']))?$params['filter']['active_inactive_users']:"-1";
    	$data['start_date'] = (isset($params['filter']['start_date']))?$params['filter']['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($params['filter']['end_date']))?$params['filter']['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	
    	$data['start_date'] = date("Y-m-d",strtotime($data['start_date']));
    	$data['end_date'] = date("Y-m-d",strtotime($data['end_date']));
    	
		// get count of total user profiles group by status
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($data['active_inactive_users']) && $data['active_inactive_users']!="0"){
    		$condition .= " AND status='".$data['active_inactive_users']."'";
    	}
    	
    	$profiles = $this->portalmodel->select_group("user","COUNT(id) AS total,status,MONTH(`created_on`) AS month","status,MONTH(`created_on`)",$condition);
    	$arrProfiles = array();
    	$percentage_profile = array();
    	$tmp_arrX = array();
		foreach ($profiles as $each_profile){
    		$tmpmonth = (strlen($each_profile->month)==1)?"0".$each_profile->month:$each_profile->month;
    		$arrProfiles[$tmpmonth][$arrstatus[$each_profile->status]] = intval($arrProfiles[$tmpmonth][$arrstatus[$each_profile->status]])+intval($each_profile->total);
    		$percentage_profile[$arrstatus[$each_profile->status]] = intval($percentage_profile[$arrstatus[$each_profile->status]])+intval($each_profile->total);
    		
    		$overalltotal = intval($overalltotal)+intval($each_profile->total);
    	}
    	
		// now get total customers
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($data['active_inactive_users']) && $data['active_inactive_users']!="-1"){
    		$condition .= " AND status='".$data['active_inactive_users']."'";
    	}
    	$profiles = $this->portalmodel->select_group("customer","COUNT(customerid) AS total,status,MONTH(`created_on`) AS month","status,MONTH(`created_on`)",$condition);
    	foreach ($profiles as $each_profile){
    		$tmpmonth = (strlen($each_profile->month)==1)?"0".$each_profile->month:$each_profile->month;
    		$arrProfiles[$tmpmonth][$arrstatus[$each_profile->status]] = intval($arrProfiles[$tmpmonth][$arrstatus[$each_profile->status]])+intval($each_profile->total);
    		$percentage_profile[$arrstatus[$each_profile->status]] = intval($percentage_profile[$arrstatus[$each_profile->status]])+intval($each_profile->total);
    		
    		$overalltotal = intval($overalltotal)+intval($each_profile->total);
    	}
    	
    	$data['arrProfiles'] = $arrProfiles;
    	
		if($data['active_inactive_users']=="-1"){
    		$data['percentage']['active'] = (is_float(($percentage_profile[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_profile[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_profile[$arrstatus[1]]*100)/$overalltotal."%";
	    	$data['percentage']['inactive'] = (is_float(($percentage_profile[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_profile[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_profile[$arrstatus[2]]*100)/$overalltotal."%";
	    	$data['percentage']['closed'] = (is_float(($percentage_profile[$arrstatus[3]]*100)/$overalltotal))?number_format(($percentage_profile[$arrstatus[3]]*100)/$overalltotal,2)."%":($percentage_profile[$arrstatus[3]]*100)/$overalltotal."%";
    	}else{
    		if($data['active_inactive_users']==1){
    			$data['percentage']['active'] = (is_float(($percentage_profile[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_profile[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_profile[$arrstatus[1]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==2){
    			$data['percentage']['inactive'] = (is_float(($percentage_profile[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_profile[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_profile[$arrstatus[2]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==3){
    			$data['percentage']['closed'] = (is_float(($percentage_profile[$arrstatus[3]]*100)/$overalltotal))?number_format(($percentage_profile[$arrstatus[3]]*100)/$overalltotal,2)."%":($percentage_profile[$arrstatus[3]]*100)/$overalltotal."%";
    		}
    	}
    	
		// now set the heading and graph title. if the start and end month is different, then title will be changed
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
    	if($end_month==$start_month){
    		$data['heading'] = "TOTAL PROFILES IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    		if($data['active_inactive_users']=="-1"){
    			$data['arrX'] = array('"Active"', '"In-Active"', '"Closed"');
    		}else if($data['active_inactive_users']=="1"){
    			$data['arrX'] = array('"Active"');
    		}else if($data['active_inactive_users']=="2"){
    			$data['arrX'] = array('"In-Active"');
    		}else if($data['active_inactive_users']=="3"){
    			$data['arrX'] = array('"Closed"');
    		}
    		$data['arrY'][0] = $arrProfiles[$start_month];
    	}else{
    		
    		// change title only
    		if(($start_month!=1 || $end_month!=12) && $end_month!=$start_month){
    			$data['heading'] = "COMPARISON";
    			$data['graph_title'] = "COMPARISON PROFILE REPORT ( ".strtoupper(date("F",strtotime($data['start_date'])))." - ".strtoupper(date("F",strtotime($data['end_date'])))." )";
    		}else if($start_month==1 && $end_month==12){
	    		$data['heading'] = "COMPARISON";
	    		$data['graph_title'] = "PROFILES YEARLY REPORT";
	    	}
	    	
	    	// data
    		for ($i=$start_month;$i<=$end_month;$i++){
    			$tmpi = (strlen($i)==1)?"0".$i:$i;
    			$data['arrX'][] = '"'.$arrmonths[$tmpi].'"';
    			
    			$tmp_arrX[$tmpi] = $tmpi;
    		}
    		
    		// now set active, inactive and closed for arrY for each month then will add data to arrY
    		// set color codes
    		if($data['active_inactive_users']=="-1"){
    			$data['arrY']['Active']['color'] = "#1bdb07";
	    		$data['arrY']['In-Active']['color'] = "#989898";
	    		$data['arrY']['Closed']['color'] = "#db0707";
	    		
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Active']['details'][$eachmonth] = 0;
	    			$data['arrY']['In-Active']['details'][$eachmonth] = 0;
	    			$data['arrY']['Closed']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="1"){
    			$data['arrY']['Active']['color'] = "#1bdb07";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Active']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="2"){
    			$data['arrY']['In-Active']['color'] = "#989898";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['In-Active']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="3"){
    			$data['arrY']['Closed']['color'] = "#db0707";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Closed']['details'][$eachmonth] = 0;
	    		}
    		}
    		
    		// now put profiles data
    		foreach ($arrProfiles as $month=>$each_profile){
    			foreach ($each_profile as $status=>$status_count){
    				$data['arrY'][$status]['details'][$month] = $status_count;
    			}
    		}
    	}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $data;
		
		// set newData formating
		/*$tmpNewData = array();
		$i=0;
		foreach ($newData as $key=>$value){
			if(array_key_exists($key,$data['arrY'])){
				foreach ($data['arrY'][$key]['details'] as $totalcount){
					$newData[$key] = intval($newData[$key])+intval($totalcount);
					$tmpNewData[$i] = $newData[$key];
				}
				
			}else{
				$tmpNewData[$i] = $newData[$key];
			}
			$i++;
		}
		
		$response_array['result']['list'] = $tmpNewData;*/
		
		return $response_array;
		
	}
	
	/**
	 * getProjectReports
	 */
	function getProjectReports($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$newData = array();
		$newData['Open']=0;
		$newData['Closed']=0;
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		
		// GET REPORTS ROW DATA
		$arrstatus = array(1=>"Open",2=>"Closed");
		$arrmonths = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September",10=>"October",11=>"November",12=>"December");
		$overalltotal = 0;
		
		$data = array();
		
		$data['open_close'] = (isset($params['filter']['open_close']))?$params['filter']['open_close']:"-1";
    	$data['start_date'] = (isset($params['filter']['start_date']))?$params['filter']['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($params['filter']['end_date']))?$params['filter']['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	
    	$data['start_date'] = date("Y-m-d",strtotime($data['start_date']));
    	$data['end_date'] = date("Y-m-d",strtotime($data['end_date']));
    	
		// QUERY CONDITION
    	$condition = "modified_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($data['open_close']) && $data['open_close']!="0"){
    		$condition .= " AND status='".$data['open_close']."'";
    	}
    	$condition.=" AND ( (accountmanager=".$id.") or FIND_IN_SET(".$id.", projectmanager) or FIND_IN_SET(".$id.", developer) )";
    	
		// GETTING DATA
    	$projects = $this->portalmodel->select_group("project","COUNT(id) AS total,status,MONTH(`modified_on`) AS month","status,MONTH(`modified_on`)",$condition);
    	$arrProjects = array();
    	$percentage_project = array();
    	$tmp_arrX = array();
    	foreach ($projects as $each_project){
    		$tmpmonth = (strlen($each_project->month)==1)?"0".$each_project->month:$each_project->month;
    		$arrProjects[$tmpmonth][$arrstatus[$each_project->status]] = intval($arrProjects[$tmpmonth][$arrstatus[$each_project->status]])+intval($each_project->total);
    		$percentage_project[$arrstatus[$each_project->status]] = intval($percentage_project[$arrstatus[$each_project->status]])+intval($each_project->total);
    		
    		$overalltotal = intval($overalltotal)+intval($each_project->total);
    	}
    	
    	$data['arrProjects'] = $arrProjects;
    	
		// NOW SET PERCENTAGE DATA
    	if($data['open_close']=="0"){
    		$data['percentage']['open'] = (is_float(($percentage_project[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[1]]*100)/$overalltotal."%";
	    	$data['percentage']['closed'] = (is_float(($percentage_project[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[2]]*100)/$overalltotal."%";
    	}else{
    		if($data['open_close']==1){
    			$data['percentage']['open'] = (is_float(($percentage_project[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[1]]*100)/$overalltotal."%";
    		}else if($data['open_close']==2){
    			$data['percentage']['closed'] = (is_float(($percentage_project[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[2]]*100)/$overalltotal."%";
    		}
    	}
    	
		// now set the heading and graph title. if the start and end month is different, then title will be changed. Also set the X and Y data for graph
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
    	if($end_month==$start_month){
    		$data['heading'] = "TOTAL PROJECTS IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    		if($data['open_close']=="0"){
    			$data['arrX'] = array('"Open"', '"Closed"');
    		}else if($data['open_close']=="1"){
    			$data['arrX'] = array('"Open"');
    		}else if($data['open_close']=="2"){
    			$data['arrX'] = array('"Closed"');
    		}
    		
    		$data['arrY'][0]['Open'] = (isset($arrProjects[$start_month]['Open']))?$arrProjects[$start_month]['Open']:0;
    		$data['arrY'][0]['Closed'] = (isset($arrProjects[$start_month]['Closed']))?$arrProjects[$start_month]['Closed']:0;
    		
    		//$data['arrY'][0] = $arrProjects[$start_month];
    	}else{
    		
    		// change title only
    		if(($start_month!=1 || $end_month!=12) && $end_month!=$start_month){
    			$data['heading'] = "COMPARISON";
    			$data['graph_title'] = "COMPARISON PROJECT REPORT ( ".strtoupper(date("F",strtotime($data['start_date'])))." - ".strtoupper(date("F",strtotime($data['end_date'])))." )";
    		}else if($start_month==1 && $end_month==12){
	    		$data['heading'] = "COMPARISON";
	    		$data['graph_title'] = "PROJECTS YEARLY REPORT";
	    	}
	    	
	    	// data
    		for ($i=$start_month;$i<=$end_month;$i++){
    			$tmpi = (strlen($i)==1)?"0".$i:$i;
    			$data['arrX'][] = '"'.$arrmonths[$tmpi].'"';
    			
    			$tmp_arrX[$tmpi] = $tmpi;
    		}
    		
    		// now set active, inactive and closed for arrY for each month then will add data to arrY
    		// set color codes
    		if($data['open_close']=="0"){
    			$data['arrY']['Open']['color'] = "#1bdb07";
	    		$data['arrY']['Closed']['color'] = "#db0707";
	    		
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Open']['details'][$eachmonth] = 0;
	    			$data['arrY']['Closed']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['open_close']=="1"){
    			$data['arrY']['Open']['color'] = "#1bdb07";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Open']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['open_close']=="2"){
    			$data['arrY']['Closed']['color'] = "#db0707";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Closed']['details'][$eachmonth] = 0;
	    		}
    		}
    		
    		// now put projects data
    		foreach ($arrProjects as $month=>$each_project){
    			foreach ($each_project as $status=>$status_count){
    				$data['arrY'][$status]['details'][$month] = $status_count;
    			}
    		}
    	}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $data;
		
		// set new formating
		/*$tmpNewData = array();
		$i=0;
		foreach ($data['arrY'][0] as $eachValue){
			$tmpNewData[$i] = $eachValue;
			$i++;
		}
		$response_array['result']['list'] = $tmpNewData;*/
				
		return $response_array;
		
	}
	
	/**
	 * getTicketReports
	 */
	function getTicketReports($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		
		// GET REPORTS ROW DATA
		$arrstatus = array(0=>"Not-Started",1=>"Started",2=>"In-Progress",3=>"On-Hold",4=>"Completed");
		$arrmonths = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September",10=>"October",11=>"November",12=>"December");
		$overalltotal = 0;
		
		$data = array();
		
		$data['status'] = (isset($params['filter']['status']))?$params['filter']['status']:"-1";
    	$data['start_date'] = (isset($params['filter']['start_date']))?$params['filter']['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($params['filter']['end_date']))?$params['filter']['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	
    	$data['start_date'] = date("Y-m-d",strtotime($data['start_date']));
    	$data['end_date'] = date("Y-m-d",strtotime($data['end_date']));
    	
    	$data['customer'] = (isset($params['filter']['customer']))?$params['filter']['customer']:"";
    	
		// QUERY CONDITION
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($data['status']) && $data['status']!="-1"){
    		$condition .= " AND status='".$data['status']."'";
    	}
    	
    	// get user role
    	$urole = $this->portalmodel->select_name('user', 'role', "id='".$id."'");
    	
    	if($urole!=1){
    		$condition.=" AND (assigned_to='".$id."' OR projectid IN (SELECT id FROM project WHERE ((accountmanager=".$id.") or FIND_IN_SET( ".$id.", projectmanager ))))";
    	}
    	
		if(!empty($data['customer'])){
    		$condition.=" AND customerid='".$data['customer']."'";
    	}
    	
    	// GETTING DATA
    	$tickets = $this->portalmodel->select_group("task","COUNT(id) AS total,status,MONTH(`created_on`) AS month","status,MONTH(`created_on`)",$condition);
    	$arrTickets = array();
    	$percentage_ticket = array();
    	$tmp_arrX = array();
    	foreach ($tickets as $each_ticket){
    		$tmpmonth = (strlen($each_ticket->month)==1)?"0".$each_ticket->month:$each_ticket->month;
    		$arrTickets[$tmpmonth][$arrstatus[$each_ticket->status]] = intval($arrTickets[$tmpmonth][$arrstatus[$each_ticket->status]])+intval($each_ticket->total);
    		$percentage_ticket[$arrstatus[$each_ticket->status]] = intval($percentage_ticket[$arrstatus[$each_ticket->status]])+intval($each_ticket->total);
    		
    		$overalltotal = intval($overalltotal)+intval($each_ticket->total);
    	}
    	
    	$data['arrTickets'] = $arrTickets;
    	
		// NOW SET PERCENTAGE DATA
    	if($data['status']=="-1"){
    		$data['percentage']['notstarted'] = (is_float(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[0]]*100)/$overalltotal."%";
	    	$data['percentage']['started'] = (is_float(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[1]]*100)/$overalltotal."%";
	    	$data['percentage']['inprogress'] = (is_float(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[2]]*100)/$overalltotal."%";
	    	$data['percentage']['onhold'] = (is_float(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[3]]*100)/$overalltotal."%";
	    	$data['percentage']['completed'] = (is_float(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[4]]*100)/$overalltotal."%";
    	}else{
    		if($data['status']==0){
    			$data['percentage']['notstarted'] = (is_float(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[0]]*100)/$overalltotal."%";
    		}else if($data['status']==1){
    			$data['percentage']['started'] = (is_float(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[1]]*100)/$overalltotal."%";
    		}else if($data['status']==2){
    			$data['percentage']['inprogress'] = (is_float(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[2]]*100)/$overalltotal."%";
    		}else if($data['status']==3){
    			$data['percentage']['onhold'] = (is_float(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[3]]*100)/$overalltotal."%";
    		}else if($data['status']==4){
    			$data['percentage']['completed'] = (is_float(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[4]]*100)/$overalltotal."%";
    		}
    	}
    	
		// now set the heading and graph title. if the start and end month is different, then title will be changed. Also set the X and Y data for graph
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
    	if($end_month==$start_month){
    		$data['heading'] = "TOTAL TICKETS IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    		if($data['status']=="-1"){
    			$data['arrX'] = array('"Not-Started"', '"Started"', '"In-Progress"', '"On-Hold"', '"Completed"');
    		}else if($data['status']=="0"){
    			$data['arrX'] = array('"Not-Started"');
    		}else if($data['status']=="1"){
    			$data['arrX'] = array('"Started"');
    		}else if($data['status']=="2"){
    			$data['arrX'] = array('"In-Progress"');
    		}else if($data['status']=="3"){
    			$data['arrX'] = array('"On-Hold"');
    		}else if($data['status']=="4"){
    			$data['arrX'] = array('"Completed"');
    		}
    		
    		$data['arrY'][0]['Not-Started'] = (isset($arrTickets[$start_month]['Not-Started']))?$arrTickets[$start_month]['Not-Started']:0;
    		$data['arrY'][0]['Started'] = (isset($arrTickets[$start_month]['Started']))?$arrTickets[$start_month]['Started']:0;
    		$data['arrY'][0]['In-Progress'] = (isset($arrTickets[$start_month]['In-Progress']))?$arrTickets[$start_month]['In-Progress']:0;
    		$data['arrY'][0]['On-Hold'] = (isset($arrTickets[$start_month]['On-Hold']))?$arrTickets[$start_month]['On-Hold']:0;
    		$data['arrY'][0]['Completed'] = (isset($arrTickets[$start_month]['Completed']))?$arrTickets[$start_month]['Completed']:0;
    		
    	}else{
    		
    		// change title only
    		if(($start_month!=1 || $end_month!=12) && $end_month!=$start_month){
    			$data['heading'] = "COMPARISON";
    			$data['graph_title'] = "COMPARISON TICKET REPORT ( ".strtoupper(date("F",strtotime($data['start_date'])))." - ".strtoupper(date("F",strtotime($data['end_date'])))." )";
    		}else if($start_month==1 && $end_month==12){
	    		$data['heading'] = "COMPARISON";
	    		$data['graph_title'] = "TICKETS YEARLY REPORT";
	    	}
	    	
	    	// data
    		for ($i=$start_month;$i<=$end_month;$i++){
    			$tmpi = (strlen($i)==1)?"0".$i:$i;
    			$data['arrX'][] = '"'.$arrmonths[$tmpi].'"';
    			
    			$tmp_arrX[$tmpi] = $tmpi;
    		}
    		
    		// now set active, inactive and closed for arrY for each month then will add data to arrY
    		// set color codes
    		if($data['status']=="-1"){
    			$data['arrY']['Not-Started']['color'] = "#b1b1b1";
	    		$data['arrY']['Started']['color'] = "#ff64dd";
	    		$data['arrY']['In-Progress']['color'] = "#64d4ff";
	    		$data['arrY']['On-Hold']['color'] = "#ffbf08";
	    		$data['arrY']['Completed']['color'] = "#20ff08";
	    		
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Not-Started']['details'][$eachmonth] = 0;
	    			$data['arrY']['Started']['details'][$eachmonth] = 0;
	    			$data['arrY']['In-Progress']['details'][$eachmonth] = 0;
	    			$data['arrY']['On-Hold']['details'][$eachmonth] = 0;
	    			$data['arrY']['Completed']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['status']=="0"){
    			$data['arrY']['Not-Started']['color'] = "#b1b1b1";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Not-Started']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['status']=="1"){
    			$data['arrY']['Started']['color'] = "#ff64dd";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Started']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['status']=="2"){
    			$data['arrY']['In-Progress']['color'] = "#64d4ff";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['In-Progress']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['status']=="3"){
    			$data['arrY']['On-Hold']['color'] = "#ffbf08";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['On-Hold']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['status']=="4"){
    			$data['arrY']['Completed']['color'] = "#20ff08";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Completed']['details'][$eachmonth] = 0;
	    		}
    		}
    		
    		// now put projects data
    		foreach ($arrTickets as $month=>$each_ticket){
    			foreach ($each_ticket as $status=>$status_count){
    				$data['arrY'][$status]['details'][$month] = $status_count;
    			}
    		}
    	}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $data;
		
		// set new formating
		/*$tmpNewData = array();
		$i=0;
		foreach ($data['arrY'][0] as $eachValue){
			$tmpNewData[$i] = $eachValue;
			$i++;
		}
		$response_array['result']['list'] = $tmpNewData;*/
				
		return $response_array;
		
	}
	
	/**
	 * listWorkorders
	 */
	function listWorkorders($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$details = $this->portalmodel->getWorkorderList($id,$internal_user_external_user);
		
		$list = array();
		$index_key = "A";
		foreach ($details as $each){
			$list[$index_key]['id_workorder'] = $each['id_workorder'];
			$list[$index_key]['workorder_number'] = $each['workorder_number'];
			$list[$index_key]['customer_name'] = $each['customer_name'];
			$list[$index_key]['assigned_to'] = $each['first_name']." ".$each['last_name'];
			$list[$index_key]['file'] = (isset($each['path']) && !empty($each['path']))?base_url().$each['path']:"";
			$list[$index_key]['signed_wo'] = (isset($each['signed_wo']) && !empty($each['signed_wo']))?base_url().$each['signed_wo']:"";
			$list[$index_key]['tech_invoice'] = (isset($each['tech_invoice']) && !empty($each['tech_invoice']))?base_url().$each['tech_invoice']:"";
			$list[$index_key]['added_info'] = (isset($each['added_info']) && !empty($each['added_info']))?base_url().$each['added_info']:"";
			
			$status="Not Started";
			if($each['status']==2){
				$status="In Progress";
			}else if($each['status']==3){
				$status="Pending";
			}else if($each['status']==4){
				$status="Completed";
			}else if($each['status']==5){
				$status="Cancelled";
			}
			
			$list[$index_key]['status'] = $status;
			
			$pending_info = array();
			if(empty($each['signed_wo'])){
				$pending_info[] = "Signed W/O";
			}
			if(empty($each['tech_invoice'])){
				$pending_info[] = "Tech Invoice";
			}
			if(empty($each['added_info'])){
				$pending_info[] = "Added Info";
			}
			
			$list[$index_key]['pending_info_alert'] = implode(",", $pending_info);
			$list[$index_key]['created_by'] = $each['cb_first_name']." ".$each['cb_last_name'];
			$list[$index_key]['created_on'] = $each['created_on'];
			
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * listCompletedWorkorders
	 */
	function listCompletedWorkorders($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$details = $this->portalmodel->getCompletedWorkorderList($id,$internal_user_external_user);
		
		$list = array();
		$index_key = "A";
		foreach ($details as $each){
			$list[$index_key]['id_workorder'] = $each['id_workorder'];
			$list[$index_key]['workorder_number'] = $each['workorder_number'];
			$list[$index_key]['customer_name'] = $each['customer_name'];
			$list[$index_key]['assigned_to'] = $each['first_name']." ".$each['last_name'];
			$list[$index_key]['signed_wo'] = $each['signed_wo'];
			$list[$index_key]['tech_invoice'] = $each['tech_invoice'];
			$list[$index_key]['added_info'] = $each['added_info'];
			
			$status="Not Started";
			if($each['status']==2){
				$status="In Progress";
			}else if($each['status']==3){
				$status="Pending";
			}else if($each['status']==4){
				$status="Completed";
			}else if($each['status']==5){
				$status="Cancelled";
			}
			
			$list[$index_key]['status'] = $status;
			
			$pending_info = array();
			if(empty($each['signed_wo'])){
				$pending_info[] = "Signed W/O";
			}
			if(empty($each['tech_invoice'])){
				$pending_info[] = "Tech Invoice";
			}
			if(empty($each['added_info'])){
				$pending_info[] = "Added Info";
			}
			
			$list[$index_key]['pending_info_alert'] = implode(",", $pending_info);
			$list[$index_key]['created_by'] = $each['cb_first_name']." ".$each['cb_last_name'];
			$list[$index_key]['created_on'] = $each['created_on'];
			
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getSettings
	 */
	function getSettings($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$list = array();
		
		if($is_customer){
			$where = "customerid=$id";
			$details = $this->portalmodel->select_where('', '', 'customer', $where);
			
			$list['companyname'] = $details[0]->companyname;
			$list['first_name'] = $details[0]->first_name;
			$list['last_name'] = $details[0]->last_name;
			$list['contact_number'] = $details[0]->contactno;
			$list['business_email'] = $details[0]->business_email;
			$list['profile_picture'] = (!empty($details[0]->profile) && $details[0]->profile!="N/A")?base_url().$details[0]->profile:"";
		}else{
			$where = "id=$id";
			$details = $this->portalmodel->select_where('', '', 'user', $where);
			
			$where_files = "fk_user_id='".$id."'";
			$contractfiles = $this->portalmodel->select_where('', '', 'user_contracts', $where_files);
			
			$list['first_name'] = $details[0]->first_name;
			$list['last_name'] = $details[0]->last_name;
			$list['contact_number'] = $details[0]->phone;
			$list['business_email'] = $details[0]->business_email;
			$list['profile_picture'] = (!empty($details[0]->userprofile) && $details[0]->userprofile!="N/A")?base_url().$details[0]->userprofile:"";
			
			$index="A";
			foreach ($contractfiles as $eachFile){
				$list['other_files'][$index]['file_id']=$eachFile->id;
				$list['other_files'][$index]['file_name'] = $eachFile->file_name;
				$list['other_files'][$index]['file'] = base_url().$eachFile->file;
				$index++;
			}
			
		}
		
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getWorkorderDetails
	 */
	function getWorkorderDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$workorder_id = $params['workorder_id'];
		
		// get workorder details
		$details = $this->portalmodel->getWorkorderDetails($workorder_id,$id,$internal_user_external_user);
		
		$list['workorder_id'] = $details['id_workorder'];
		$list['workorder_number'] = $details['workorder_number'];
		$list['customer_name'] = $details['customer_name'];
		$list['assigned_to'] = $details['first_name']." ".$details['last_name'];
		
		$status_state = "not started";
		if($details['status']==2){
			$status_state = "in progress";
		}else if($details['status']==3){
			$status_state = "pending";
		}else if($details['status']==4){
			$status_state = "completed";
		} else if($details['status']==5){
			$status_state = "cancelled";
		}
		$list['status'] = $status_state;
		
		//print_r($details['files']); exit;
		
		// now workorder files
		$index_key = "A";
		foreach ($details["files"] AS $eachFile){
			$list["files"][$index_key]=base_url().$eachFile;
			$index_key++;
		}
		
		// now signed workorder files
		$index_key = "A";
		foreach ($details["signed_workorder_file"] AS $file_id=>$eachFile){
			$list["signed_workorder_file"][$index_key]['file_id']=$file_id;
			$list["signed_workorder_file"][$index_key]['file']=base_url().$eachFile;
			$index_key++;
		}
		
		// now tech invoice files
		$index_key = "A";
		foreach ($details["tech_invoice_file"] AS $file_id=>$eachFile){
			$list["tech_invoice_file"][$index_key]['file_id']=$file_id;
			$list["tech_invoice_file"][$index_key]['file']=base_url().$eachFile;
			$index_key++;
		}
		
		// now added info files
		$index_key = "A";
		foreach ($details["added_info_file"] AS $file_id=>$eachFile){
			$list["added_info_file"][$index_key]['file_id']=$file_id;
			$list["added_info_file"][$index_key]['file']=base_url().$eachFile;
			$index_key++;
		}
		
		// add comments
		$where4 = "fk_workorder_id=$workorder_id ";
        $comments = $this->portalmodel->select_where_cond('', '', 'workordercomments', $where4,"created_on","DESC");
		
		$li = array();
		$index_key="A";
		$li = array();
		if (!empty($comments)){
			foreach ($comments as $d) {
                $l['comments'] = $d->comments;
                $name = "CONCAT(first_name,' ',last_name) as name";
                $l['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                $l['created_on'] = date("Y-m-d H:i A",strtotime($d->created_on));
				$li[$index_key] = $l;
				
				$index_key++;
            }
		}
		$list["comments"] = $li;
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * Update Workorder
	 */
	function updateWorkorder($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$workorderDetails = $params['workorder_details'];
		
		$workorder_id = $workorderDetails['workorder_id'];
		$workorder_status = $workorderDetails['workorder_status'];
		$customer_name = $workorderDetails['customer_name'];
		
		// update workorder status to Cancelled if it is passed and not empty
		if(!empty($workorder_status)){
        	$data = array(
	            'status' => $workorder_status
	        );
	        $result_update_workorder = $this->portalmodel->update_query('workorder', $data, $workorder_id, 'id_workorder');
        }
		
		// update customer name
		if(!empty($customer_name)){
        	$data = array(
	            'customer_name' => $customer_name
	        );
	        $result_update_workorder = $this->portalmodel->update_query('workorder', $data, $workorder_id, 'id_workorder');
        }
        
		// workorder files
        $files = (isset($workorderDetails['workorder_files']))?$workorderDetails['workorder_files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/workorderfiles/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/workorderfiles/".$file_name;
        			$data1              = array(
	                    'path' => $path,
	                    'fk_workorder_id' => $workorder_id
	                );
	                $result1    = $this->portalmodel->insert_query_('workorderfiles', $data1);
        		}
        	}
        }
        
		// signed WO files
        $files = (isset($workorderDetails['signed_wo_files']))?$workorderDetails['signed_wo_files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/workorderfiles/signed_wo/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/workorderfiles/signed_wo/".$file_name;
        			$data1              = array(
	                    'fk_workorder_id'=>$workorder_id,
	                    'file_name' => '',
	                	'file' => $path,
		                'created_by' => $id,
			            'created_on' => date("Y-m-d H:i:s"),
			            'modified_by' => '0',
			            'modified_on' => "0000-00-00 00:00:00"
	                );
	                $result1    = $this->portalmodel->insert_query_('wo_signed', $data1);
        		}
        	}
        }
        
		// tech invoice files
        $files = (isset($workorderDetails['tech_invoice_files']))?$workorderDetails['tech_invoice_files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/workorderfiles/tech_invoice/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/workorderfiles/tech_invoice/".$file_name;
        			$data1              = array(
	                    'fk_workorder_id'=>$workorder_id,
	                    'file_name' => '',
	                	'file' => $path,
		                'created_by' => $id,
			            'created_on' => date("Y-m-d H:i:s"),
			            'modified_by' => '0',
			            'modified_on' => "0000-00-00 00:00:00"
	                );
	                $result1    = $this->portalmodel->insert_query_('wo_tech_invoice', $data1);
        		}
        	}
        }
        
		// added info files
        $files = (isset($workorderDetails['added_info_files']))?$workorderDetails['added_info_files']:'';
        if(isset($files) && !empty($files)){
        	foreach ($files as $eachFile){
        		$target_dir = FCPATH."/workorderfiles/added_info/";
        		
        		$file_parts = explode(";base64,", $eachFile);
		        $eachFile = (isset($file_parts[1]))?$file_parts[1]:$file_parts[0];
        		
        		$file_name = $this->upload_file($target_dir, $eachFile);
        		if(!empty($file_name)){
        			$path = "/workorderfiles/added_info/".$file_name;
        			$data1              = array(
	                    'fk_workorder_id'=>$workorder_id,
	                    'file_name' => '',
	                	'file' => $path,
		                'created_by' => $id,
			            'created_on' => date("Y-m-d H:i:s"),
			            'modified_by' => '0',
			            'modified_on' => "0000-00-00 00:00:00"
	                );
	                $result1    = $this->portalmodel->insert_query_('wo_added_info', $data1);
        		}
        	}
        }
		
		
		$response_array['result']['code'] = 200;
        $response_array['result']['message'] = "Workorder updated successfully!";
        
        return $response_array;		
	}
	
	/**
	 * addWorkorderComments
	 */
	function addWorkorderComments($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$workorder_id = $params['workorder_id'];
		$comments = $params['comments'];
		
		$data   = array(
            'fk_workorder_id' => $workorder_id,
            'comments' => $comments,
            'commented_by' => 1,
            'created_by' => $id,
        	'created_on' => date("y-m-d H:i:s")
        );
		
        $result = $this->portalmodel->insert_query_('workordercomments', $data);
        
		if($result){
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Comment added successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }
		
		return $response_array;
	}
	
	/**
	 * getCustomers
	 */
	function getCustomers($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$where = "accountmanagerid= $id AND status!='3'";
		$data = $this->portalmodel->select_where('', '', 'customer', $where);
		
		$list = array();
		$index_key = "A";
		foreach ($data as $d){
			$list[$index_key]['id'] = $d->customerid;
			$list[$index_key]['code'] = $d->username;
			$list[$index_key]['name'] = $d->companyname;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getCustomerCompany
	 */
	function getCustomerCompany($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// get user role
		$urole = $this->portalmodel->select_name('user', 'role', "id='".$id."'");
	    
	    if($urole==1){
	    	// its admin. show all companies
	    	$where = array();
	    }else{
	    	// its employee/tech
    		if($internal_user_external_user==1){
    			// its employee
    			$where = "id IN (SELECT fk_company_id FROM customer WHERE accountmanagerid= $id AND status!=3)";
    		}else{
    			// its tech
    			$where = "id IN (SELECT customerid FROM project WHERE FIND_IN_SET( $id, developer ))";
    		}
	    }
		
		$data = $this->portalmodel->select_where_cond('', '', 'customer_company', $where, "name", "ASC");
		
		$list = array();
		$index_key = "A";
		foreach ($data as $d){
			$list[$index_key]['id'] = $d->id;
			$list[$index_key]['name'] = $d->name;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getInboxMessageDetails
	 */
	function getInboxMessageDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$message_id = $params['message_id'];
		
		$where = "id=$message_id";
		$details = $this->portalmodel->select_where('', '', 'messages', $where);
		
		$list = array();
		$index_key = "A";
		
		$list['id'] = $details[0]->id;
		$list['from_type'] = $details[0]->from_type;
		if($details[0]->from_type=='C'){
			$list['from'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'customerid', $details[0]->created_by, 'customer');
			
			// also select the company name
        	$fk_company_id = $this->portalmodel->select_username("fk_company_id as name", 'customerid', $details[0]->created_by, 'customer');
        	if(!empty($fk_company_id)){
        		$company_name = $this->portalmodel->select_username("name", 'id', $fk_company_id, 'customer_company');
        		if(!empty($company_name)){
        			$list['from'] = $list['from']." (".$company_name.")";
        		}
        	}
			
			$from_id = $this->portalmodel->select_username("username as name", 'customerid', $details[0]->created_by, 'customer');
		}else{
			$list['from'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $details[0]->created_by, 'user');
			$from_id = $this->portalmodel->select_username("employeeid as name", 'id', $details[0]->created_by, 'user');
		}
		$list['from_id'] = $from_id;
		$list['subject'] = $details[0]->subject;
		$list['message'] = $details[0]->message;
		/*foreach ($details as $eachCategory){
			$list[$index_key] = $eachCategory;
			$index_key++;
		}*/
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * getSentMessageDetails
	 */
	function getSentMessageDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$message_id = $params['message_id'];
		
		$where = "id=$message_id";
		$details = $this->portalmodel->select_where('', '', 'messages', $where);
		
		$list = array();
		$index_key = "A";
		
		$list['id'] = $details[0]->id;
		$list['to_id'] = $details[0]->to;
		$list['to_type'] = $details[0]->to_type;
		if($details[0]->to_type=='C'){
			$list['to'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'customerid', $details[0]->to, 'customer');
			
			// also select the company name
        	$fk_company_id = $this->portalmodel->select_username("fk_company_id as name", 'customerid', $details[0]->to, 'customer');
        	if(!empty($fk_company_id)){
        		$company_name = $this->portalmodel->select_username("name", 'id', $fk_company_id, 'customer_company');
        		if(!empty($company_name)){
        			$list['to'] = $list['to']." (".$company_name.")";
        		}
        	}
		}else{
			$list['to'] = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $details[0]->to, 'user');
		}
		$list['subject'] = $details[0]->subject;
		$list['message'] = $details[0]->message;
		/*foreach ($details as $eachCategory){
			$list[$index_key] = $eachCategory;
			$index_key++;
		}*/
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $list;
				
		return $response_array;
		
	}
	
	/*
	 * removeWorkorderFiles
	 */
	function removeWorkorderFiles($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$type = $params['type'];
		$file_id = $params['file_id'];
		
		if($type=="signed_workorder_file"){
			$table = "wo_signed";
		}else if($type=="tech_invoice_file"){
			$table = "wo_tech_invoice";
		}else if($type=="added_info_file"){
			$table = "wo_added_info";
		}
		
		$this->portalmodel->deleteid($table,"id",$file_id);
		
		$response_array['result']['code'] = 200;
		$response_array['result']['message'] = "File removed successfully!";
				
		return $response_array;
		
	}
	
	/*
	 * removeUserFiles
	 */
	function removeUserFiles($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$file_id = $params['file_id'];
		
		$table = "user_contracts";
		$this->portalmodel->deleteid($table,"id",$file_id);
		
		$response_array['result']['code'] = 200;
		$response_array['result']['message'] = "File removed successfully!";
				
		return $response_array;
		
	}
	
	/*
	 * markMessageAsRead
	 */
	function markMessageAsRead($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$message_id = $params['message_id'];
		
		$data=array('read_unread'=>'1');
        $result = $this->portalmodel->update_query('messages', $data, $message_id, 'id');
		
		$response_array['result']['code'] = 200;
		$response_array['result']['message'] = "Message marked as read!";
				
		return $response_array;
		
	}
	
	/**
	 * getProjects
	 */
	function getProjects($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		if($is_customer){
			// get company id
        	$company_id = $this->portalmodel->select_username("fk_company_id AS name", 'customerid', $id, 'customer');
        	$where = "id IN (SELECT id FROM project WHERE customerid='".$company_id."')";
			$data = $this->portalmodel->select_where('', '', 'project', $where);
		}else{
			$where = "(accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ) or FIND_IN_SET( $id, developer )";
			$data = $this->portalmodel->select_where('', '', 'project', $where);
		}
		
		$list = array();
		$index_key = "A";
		foreach ($data as $d){
			$list[$index_key]['id'] = $d->id;
			$list[$index_key]['name'] = $d->title;
			$index_key++;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $list;
				
		return $response_array;
		
	}
	
	/**
	 * responseTimeGraph
	 */
	function responseTimeGraph($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$data = array();
		
		$data['start_date'] = (isset($params['filter']['start_date']))?$params['filter']['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($params['filter']['end_date']))?$params['filter']['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	$data['customer'] = (isset($params['filter']['customer']))?$params['filter']['customer']:"";
    	
    	// QUERY CONDITION
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	$condition .= " AND status=1";
    	
		if($internal_user_external_user==2){
			$condition.=" AND (assigned_to='".$uid."' OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, developer )))) ";
		}
		
		if(!empty($data['customer'])){
    		$condition.=" AND customerid='".$data['customer']."'";
    	}
    	
    	// GETTING DATA
    	$tickets = $this->portalmodel->select_where("","","task",$condition);
    	
		// now go through tickets and get the differences in finaltickets
    	$finaltickets = array();
    	if(!empty($tickets)){
    		$i=0;
    		
    		foreach ($tickets as $eachTicket){
    			if($eachTicket->status==1){
    				$finaltickets[$i]['taskid'] = $eachTicket->taskid;
    				
    				// get first response time
    				$first_response_time = $this->portalmodel->select(1,0,"taskcomments","taskid='".$eachTicket->id."' AND commented_by=1","created_on","ASC");
    				if(!empty($first_response_time)){
    					
    					$first_response_time = $first_response_time[0]->created_on;
    					
	    				// get time difference in hours,minutes,seconds
	    				$diff = strtotime($first_response_time)-strtotime($eachTicket->created_on);
	    				$timediff = $this->format_time($diff);
	    				$finaltickets[$i]['time_difference'] = $timediff;
    				}
    				$i++;
    			}
    		}
    	}
    	
    	// now set the heading and graph title. if the start and end month is different, then title will be changed. Also set the X and Y data for graph
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
		foreach ($finaltickets as $eachFinalticket){
    		$data['arrX'][] = $eachFinalticket['taskid'];
    		$data['arrY'][0][$eachFinalticket['taskid']]=$eachFinalticket['time_difference'];
    	}
    	
		if($end_month==$start_month){
    		$data['heading'] = "RESPONSE TIME IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    	}else{
    		
    		// change title only
    		if(($start_month!=1 || $end_month!=12) && $end_month!=$start_month){
    			$data['heading'] = "COMPARISON";
    			$data['graph_title'] = "COMPARISON RESPONSE TIME REPORT ( ".strtoupper(date("F",strtotime($data['start_date'])))." - ".strtoupper(date("F",strtotime($data['end_date'])))." )";
    		}else if($start_month==1 && $end_month==12){
	    		$data['heading'] = "COMPARISON";
	    		$data['graph_title'] = "RESPONSE TIME YEARLY REPORT";
	    	}
    	}
    	
    	$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $data;
		
		return $response_array;
	}
	
	/**
	 * resolutionTimeGraph
	 */
	function resolutionTimeGraph($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$data = array();
		
		$data['start_date'] = (isset($params['filter']['start_date']))?$params['filter']['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($params['filter']['end_date']))?$params['filter']['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	$data['customer'] = (isset($params['filter']['customer']))?$params['filter']['customer']:"";
    	
    	// QUERY CONDITION
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	$condition .= " AND status=4";
    	
		if($internal_user_external_user==2){
			$condition.=" AND (assigned_to='".$uid."' OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, developer )))) ";
		}
		
		if(!empty($data['customer'])){
    		$condition.=" AND customerid='".$data['customer']."'";
    	}
    	
    	// GETTING DATA
    	$tickets = $this->portalmodel->select_where("","","task",$condition);
    	
		// now go through tickets and get the differences in finaltickets
    	$finaltickets = array();
    	if(!empty($tickets)){
    		$i=0;
    		
    		foreach ($tickets as $eachTicket){
    			if($eachTicket->status==4){
    				$finaltickets[$i]['taskid'] = $eachTicket->taskid;
    				
    				// get first response time
    				$first_response_time = $this->portalmodel->select(1,0,"taskcomments","taskid='".$eachTicket->id."' AND commented_by=1","created_on","ASC");
    				if(!empty($first_response_time)){
    					
    					$first_response_time = $first_response_time[0]->created_on;
    					
	    				// get time difference in hours,minutes,seconds
	    				$diff = strtotime($eachTicket->modified_on)-strtotime($first_response_time);
	    				$timediff = $this->format_time($diff);
	    				$finaltickets[$i]['time_difference'] = $timediff;
    				}
    				$i++;
    			}
    		}
    	}
    	
    	// now set the heading and graph title. if the start and end month is different, then title will be changed. Also set the X and Y data for graph
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
		foreach ($finaltickets as $eachFinalticket){
    		$data['arrX'][] = $eachFinalticket['taskid'];
    		$data['arrY'][0][$eachFinalticket['taskid']]=$eachFinalticket['time_difference'];
    	}
    	
		if($end_month==$start_month){
    		$data['heading'] = "RESPONSE TIME IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    	}else{
    		
    		// change title only
    		if(($start_month!=1 || $end_month!=12) && $end_month!=$start_month){
    			$data['heading'] = "COMPARISON";
    			$data['graph_title'] = "COMPARISON RESPONSE TIME REPORT ( ".strtoupper(date("F",strtotime($data['start_date'])))." - ".strtoupper(date("F",strtotime($data['end_date'])))." )";
    		}else if($start_month==1 && $end_month==12){
	    		$data['heading'] = "COMPARISON";
	    		$data['graph_title'] = "RESPONSE TIME YEARLY REPORT";
	    	}
    	}
    	
    	$response_array['result']['code'] = 200;
		$response_array['result']['list'] = $data;
		
		return $response_array;
	}
	
	function getProfileDetails($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$profile_id = $params['profile_id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		$type = strtolower($params['type']);
		
		$details = array();
		
		if($type=="e" || $type=="t"){
			// employee/tech profiles
			$where = "id=$profile_id";
			
			$tmp_details = $this->portalmodel->select_where('', '', 'user', $where);
	        
			$details["id"] = $tmp_details[0]->id;
			$details["first_name"] = $tmp_details[0]->first_name;
			$details["last_name"] = $tmp_details[0]->last_name;
			$details["email"] = $tmp_details[0]->email;
			$details["business_email"] = $tmp_details[0]->business_email;
			$details["phone"] = $tmp_details[0]->phone;
			$details["status"] = $tmp_details[0]->status;
			$details["profile_picture"] = (isset($tmp_details[0]->userprofile) && !empty($tmp_details[0]->userprofile))?base_url().$tmp_details[0]->userprofile:"";
			
		}else if($type=="c"){
			// customer details
			$where = "customerid=$profile_id";
			
			$tmp_details = $this->portalmodel->select_where('', '', 'customer', $where);
			
			$details["id"] = $tmp_details[0]->customerid;
			$details["fk_company_id"] = $tmp_details[0]->fk_company_id;
			$details["first_name"] = $tmp_details[0]->first_name;
			$details["last_name"] = $tmp_details[0]->last_name;
			$details["accountmanagerid"] = $tmp_details[0]->accountmanagerid;
			$details["email"] = $tmp_details[0]->emailid;
			$details["business_email"] = $tmp_details[0]->business_email;
			$details["phone"] = $tmp_details[0]->contactno;
			$details["status"] = $tmp_details[0]->status;
			$details["profile_picture"] = (isset($tmp_details[0]->profile_picture) && !empty($tmp_details[0]->profile_picture))?base_url().$tmp_details[0]->profile_picture:"";
			
		}else if($type=="m"){
			// customer company profiles
			$where = "id=$profile_id";
			
			$tmp_details = $this->portalmodel->select_where('', '', 'customer_company', $where);
	        
			$details["id"] = $tmp_details[0]->id;
			$details["name"] = $tmp_details[0]->name;
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['details'] = $details;
				
		return $response_array;
		
	}
	
	function updateProfile($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$type = strtolower($params['type']);
		$details = $params['details'];
		
		$data_update = array();
		$profile_id = $details['id'];
		
		if($type=="e" || $type=="t"){
			// employee/tech profiles
			$data_update['first_name'] = $details['first_name'];
			$data_update['last_name'] = $details['last_name'];
			$data_update['business_email'] = $details['business_email'];
			$data_update['phone'] = $details['phone'];
			$data_update['status'] = $details['status'];
			if(isset($details['profile_picture'])){
				$data_update['userprofile'] = $details['profile_picture'];
			}
			if(isset($details['password'])){
				$data_update['password'] = base64_encode($details['password']);
			}
			
			$this->portalmodel->update_query('user', $data_update, $profile_id, 'id');
			
		}else if($type=="c"){
			// customer details
			$data_update["fk_company_id"] = $details['fk_company_id'];
			
			// also get company name from the id
			$coust = "id=".$data_update["fk_company_id"];
	        $company_name = $this->portalmodel->select_name('customer_company', 'name', $coust);
	        $data_update["companyname"] = $company_name;
			
			$data_update["first_name"] = $details['first_name'];
			$data_update["last_name"] = $details['last_name'];
			$data_update["accountmanagerid"] = $details['accountmanagerid'];
			//$data_update["email"] = $details['emailid'];
			$data_update["business_email"] = $details['business_email'];
			$data_update["contactno"] = $details['contactno'];
			$data_update["status"] = $details['status'];
			
			if(isset($details['profile_picture'])){
				$data_update["profile_picture"] = (isset($details['profile_picture']) && !empty($details['profile_picture']))?$details['profile_picture']:"";
			}
			if(isset($details['password'])){
				$data_update['password'] = base64_encode($details['password']);
			}
			
			$this->portalmodel->update_query('customer', $data_update, $profile_id, 'customerid');
			
		}else if($type=="m"){
			// customer company profiles
			
			$data_update["name"] = $details['name'];
	        $this->portalmodel->update_query('customer_company', $data_update, $profile_id, 'id');
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['message'] = "Profile updated successfully!";
				
		return $response_array;
		
	}
	
	function addProfile($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		$type = strtolower($params['type']);
		$details = $params['details'];
		
		$data_add = array();
		
		if($type=="e" || $type=="t"){
			$existing_user_id = $this->portalmodel->select_name('user', 'id', "email='".$email."'");
			if(empty($existing_user_id) || $existing_user_id=="N/A"){
				
				$maxid = $this->portalmodel->maxid('user', 'id');
				$num = $maxid[0]->id + 1;
				$employeeid = 'E00' . $num;
				
				// employee/tech profiles
				$data_add['employeeid'] = $employeeid;
				$data_add['first_name'] = $details['first_name'];
				$data_add['last_name'] = $details['last_name'];
				$data_add['email'] = $details['email'];
				$data_add['business_email'] = $details['business_email'];
				$data_add['phone'] = $details['phone'];
				$data_add['status'] = $details['status'];
				$data_add['created_by'] = $id;
				$data_add['role'] = 2;
				$data_add['internal_user_external_user'] = ($type=="e")?1:2;
				if(isset($details['profile_picture'])){
					$data_add['userprofile'] = $details['profile_picture'];
				}
				if(isset($details['password'])){
					$data_add['password'] = base64_encode($details['password']);
				}
				
				$this->portalmodel->insert_query_('user', $data_add);
			}
			
			
		}else if($type=="c"){
			// customer details
			$maxid = $this->portalmodel->maxid('customer', 'customerid');
	        $num = $maxid[0]->id + 1;
	        $customerid = 'C00' . $num;
			
			$data_add["fk_company_id"] = $details['fk_company_id'];
			
			// also get company name from the id
			$coust = "id=".$data_add["fk_company_id"];
	        $company_name = $this->portalmodel->select_name('customer_company', 'name', $coust);
	        
	        $data_add["username"] = $customerid;
			$data_add["first_name"] = $details['first_name'];
			$data_add["last_name"] = $details['last_name'];
			$data_add["companyname"] = $company_name;
			$data_add["accountmanagerid"] = $details['accountmanagerid'];
			$data_add["emailid"] = $details['email'];
			$data_add["business_email"] = $details['business_email'];
			$data_add["contactno"] = $details['contactno'];
			$data_add["status"] = $details['status'];
			$data_add['created_by'] = $id;
			
			if(isset($details['profile_picture'])){
				$data_add["profile_picture"] = (isset($details['profile_picture']) && !empty($details['profile_picture']))?$details['profile_picture']:"";
			}
			if(isset($details['password'])){
				$data_add['password'] = base64_encode($details['password']);
			}
			
			$this->portalmodel->insert_query_('customer', $data_add);
			
		}else if($type=="m"){
			// customer company profiles
			$data_add["name"] = $details['name'];
	        $this->portalmodel->insert_query_('customer_company', $data_add);
		}
		
		$response_array['result']['code'] = 200;
		$response_array['result']['message'] = "Profile added successfully!";
				
		return $response_array;
		
	}
	
	function format_time($t,$f=':'){ // t = seconds, f = separator 
		//return ($t< 0 ? '-' : '') . sprintf("%02d%s%02d%s%02d", floor(abs($t)/3600), $f, (abs($t)/60)%60, $f, abs($t)%60);
		
		$seconds = abs($t)%60;
		$minutes = (abs($t)/60)%60;
	  
		if($seconds>30){
			$minutes = $minutes+1;
		}
		return sprintf("%02d",$minutes);//($t< 0 ? '-' : '') . sprintf("%02d%s%02d%s%02d", floor(abs($t)/3600), $f, (abs($t)/60)%60, $f, abs($t)%60);
    }
	
	/**
	 * saveImage
	 */
	/*function saveImage($params){
		// load portalmodel
		$this->load->model('portalmodel');
		
		$response_array = array();
		
		$current_time = date("Y-m-d H:i:s");
		
		$id = $params['id'];
		$is_customer = $params['is_customer'];
		$internal_user_external_user = (isset($params['internal_user_external_user']))?$params['internal_user_external_user']:0;
		
		// category details
		$media = $params['profile_details']['files'];
		$image_parts = explode(";base64,", $media);
		
		if(isset($media) && !empty($media)){
            $filename = strtotime("now");
            $file_extension=".jpg";
            $quality="100";
            $newPic=base64_decode($image_parts[1]);
            $im = imagecreatefromstring($newPic);
            if ($im !== false){
            	$path=$_SERVER['DOCUMENT_ROOT']."synergyit/portal/user/".$filename.$file_extension;
				imagejpeg($im,$path,$quality);
				imagedestroy($im);
				$filename = "user/".$filename.$file_extension;
            }
		}
        
		if($result){
        	$response_array['result']['code'] = 200;
        	$response_array['result']['message'] = "Image saved successfully!";
        }else{
        	$response_array['error']['code'] = 500;
        	$response_array['error']['message'] = "Something went wrong. Please try again after sometime!";
        }
		
		return $response_array;
	}*/
	
	/**
	 * GENERAL FUNCTIONS
	 */
	
	/**
	 * send email
	 * @param $to
	 * @param $subject
	 * @param $message
	 */
	function email($to, $subject, $message){
        $config['wordwrap'] = TRUE;
        $config['newline']  = '\n';
        $config['mailtype'] = 'html';
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('info@synergyit.ca', 'SynergyInteract');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        if ($this->email->send()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Upload base64 encoded files
     */
	function upload_file($target_dir,$encoded_string){
		
		/*$encoded_string = explode(";base64,", $encoded_string);
		$encoded_string=base64_decode($encoded_string[1]);*/
		
	    //$target_dir = ''; // add the specific path to save the file
	    $decoded_file = base64_decode($encoded_string); // decode the file
	    $mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
	    $extension = $this->mime2ext($mime_type); // extract extension from mime type
	    $file = uniqid() .'.'. $extension; // rename file as a unique name
	    $file_dir = $target_dir . $file;
	    file_put_contents($file_dir, $decoded_file); // save
	    /*try {
	        file_put_contents($file_dir, $decoded_file); // save
	        database_saving($file);
	        header('Content-Type: application/json');
	        echo json_encode("File Uploaded Successfully");
	    } catch (Exception $e) {
	        header('Content-Type: application/json');
	        echo json_encode($e->getMessage());
	    }*/
		return $file;
	}
	
	/*
	to take mime type as a parameter and return the equivalent extension
	*/
	function mime2ext($mime){
	    $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp",
	    "image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp",
	    "image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp",
	    "application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg",
	    "image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],
	    "wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],
	    "ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg",
	    "video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],
	    "kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],
	    "rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application",
	    "application\/x-jar"],"zip":["application\/x-zip","application\/zip",
	    "application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
	    "7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],
	    "svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],
	    "mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],
	    "webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],
	    "pdf":["application\/pdf","application\/octet-stream"],
	    "pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
	    "ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
	    "application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
	    "xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
	    "xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel",
	    "application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
	    "xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo",
	    "video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],
	    "log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],
	    "wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],
	    "tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop",
	    "image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],
	    "mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar",
	    "application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40",
	    "application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
	    "cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary",
	    "application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],
	    "ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],
	    "wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],
	    "dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php",
	    "application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
	    "swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],
	    "mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],
	    "rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],
	    "jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],
	    "eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],
	    "p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],
	    "p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
	    $all_mimes = json_decode($all_mimes,true);
	    foreach ($all_mimes as $key => $value) {
	        if(array_search($mime,$value) !== false) return $key;
	    }
	    return false;
	}
    /****************************************  GENERAL FUNCTIONS END ***********************************************/
}
?>