<?php
class Customerportal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('javascript');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library("pagination");
        $this->load->library('calendar');
        //$this->load->library('email');
        date_default_timezone_set("America/Toronto");
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('America/Toronto');
        }
    }
    public function common()
    {
        date_default_timezone_set("America/Toronto");
        if (!(isset($this->session->userdata['id']))) {
            redirect('customerportal/logout');
        } else {
            $this->load->model('portalmodel');
            $uid        = $this->session->userdata('id');
            
            // select company
	        $where_company = " customerid='$uid'";
	        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
            
            //$where      = " customerid='$uid' and show_customer=0";
            $where      = " customerid='$company_id' and show_customer=0";
            $names      = "status, COUNT(status)as count";
            $taskstatus = $this->portalmodel->select_group('task', $names, 'status', $where);
            $count      = 0;$open=0;
            if (!empty($taskstatus)) {
                foreach ($taskstatus as $d) {
                    if ($d->status == 0) {
                        $data['notstarted'] = $d->count;
                        $count              = $count + $d->count;
                         $open=$open+ $d->count;
                    }
                    if ($d->status == 1) {
                        $data['started'] = $d->count;
                        $count           = $count + $d->count;
                         $open=$open+ $d->count;
                    }
                    if ($d->status == 2) {
                        $data['inprogress'] = $d->count;
                        $count              = $count + $d->count;
                         $open=$open+ $d->count;
                    }
                    if ($d->status == 3) {
                        $data['onhold'] = $d->count;
                        $count          = $count + $d->count;
                         $open=$open+ $d->count;
                    }
                    if ($d->status == 4) {
                        $data['completed'] = $d->count;
                        $count             = $count + $d->count;
                    }
                }
            }
            
            $is_customer = $this->session->userdata('is_customer');
            
            /* getting projects for chat */
            
            $current_project_id = $_REQUEST['project_id'];
            $chat_project_list = array();
            // else part commented because there's no customer user for project chat, as the chat module has been removed
            //if($is_customer){
            	
            	// first get project name for current project
            	
            	//$where = "customerid='".$uid."' AND id='".$current_project_id."'";
            	$where = "customerid='".$company_id."' AND id='".$current_project_id."'";
            	$list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
            	if (!empty($list)) {
		        	foreach ($list as $d) {
		        		$row = array();
		        		
		        		$row['project_id'] = $d->id;
		        		$row['project_name'] = $d->title;
		        		
		        		$chat_project_list[] = $row;
		        	}
            	}
            	
            	// now get project names other than the current project
	            //$where = "customerid='".$uid."' AND id!='".$current_project_id."'";
	            $where = "customerid='".$company_id."' AND id!='".$current_project_id."'";
	            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
	            if (!empty($list)) {
		        	foreach ($list as $d) {
		        		$row = array();
		        		
		        		$row['project_id'] = $d->id;
		        		$row['project_name'] = $d->title;
		        		
		        		// also get the number of new chats if any for the projects
		        		$num_chat_notification=0;
		        		$last_chat_id = $this->portalmodel->select_name("last_chat_project_customer","fk_chat_id","fk_project_id='".$d->id."' AND fk_user_id='".$uid."'");
		        		$condition_new_chats = "fk_project_id='".$d->id."'";
		        		if(!empty($last_chat_id) && $last_chat_id!="N/A"){
		        			$condition_new_chats.=" AND chat_id>'".$last_chat_id."'";
		        		}
		        		// get the num count for new chat for this project
		        		$new_chats = $this->portalmodel->select_name("chat_projects","COUNT(chat_id)",$condition_new_chats);
		        		if(!empty($new_chats) && $new_chats!="N/A"){
		        			$num_chat_notification=$new_chats;
		        		}
		        		$row['num_chat_notification']=$num_chat_notification;
		        		
		        		$chat_project_list[] = $row;
		        	}
		        }
            /*}else{
            	
            	// get project name for current project id
            	$project_ids = $this->portalmodel->select_name("customer_user_project_chat","project_ids","id='".$uid."'");
            	if(!empty($project_ids) && $project_ids!="N/A"){
            		$where = "id IN (".$project_ids.") AND id='".$current_project_id."'";
		            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
		            
            		if (!empty($list)) {
			        	foreach ($list as $d) {
			        		$row = array();
			        		
			        		$row['project_id'] = $d->id;
			        		$row['project_name'] = $d->title;
			        		
			        		$chat_project_list[] = $row;
			        	}
		            }
            	}
            	
            	// now get project names other than current project id
            	// its a user only for chat
            	$project_ids = $this->portalmodel->select_name("customer_user_project_chat","project_ids","id='".$uid."'");
            	if(!empty($project_ids) && $project_ids!="N/A"){
            		$where = "id IN (".$project_ids.") AND id!='".$current_project_id."'";
		            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
		            
		            if (!empty($list)) {
			        	foreach ($list as $d) {
			        		$row = array();
			        		
			        		$row['project_id'] = $d->id;
			        		$row['project_name'] = $d->title;
			        		
			        		$chat_project_list[] = $row;
			        	}
		            }
            	}
            }*/
            
	        $data['chat_project_list'] = $chat_project_list;
	        /* getting chat project list ends */
            
	        $data['is_customer'] = $is_customer;
            $data['countoftask'] = $count;
             $data['open']=$open;
             $data['logged_in_clientname']= $this->session->userdata('customer_name');
             $data['logged_in_userid'] = $uid;
             $data['profile_picture']= $this->session->userdata('profile_picture');
             
        	// get notifications for ticket/task updates
			$customer_or_employee_id = $this->session->userdata('customer_or_employee_id');
	        $ticket_changes_notification = $this->portalmodel->select_name("task_notifications","COUNT(id)","fk_customer_or_employee_id='".$customer_or_employee_id."' AND read_unread=0 AND user_type='C'");
	        if(!empty($ticket_changes_notification) && $ticket_changes_notification!='N/A'){
	        	$data['ticket_changes_notification']=$ticket_changes_notification;
	        }else{
	        	$data['ticket_changes_notification']=0;
	        }
             
            $this->load->view('templates/header', $data);
        }
    }
    public function index()
    {
        $this->login();
    }
    function login($try = NULL)
    {
        $this->load->view('index');
    }
    public function user_login()
    {
        $this->load->model("portalmodel");
        $result = $this->portalmodel->login_validate();
        //call function in model to check user is valid or not
        if (!$result) {
            $this->session->set_flashdata('error_message', 'Please enter valid Email and Password!'); //display the flashdata using session
            redirect('customerportal/index'); //user is not valid so goto login page again                  
        } else {
        	if($result==1){
        		// customer / user login
            	redirect('customerportal/dashboard'); //user is valid and role=0 so goto dashboard
        	}else if($result==2){
        		// employee login ($result = 2)
        		redirect('portal/dashboard');
        	}else if($result==3){
        		$this->session->set_flashdata('error_message', 'Your login has been blocked for 30 minutes, due to continuous 3 failed login attempts.!'); //display the flashdata using session
            	redirect('customerportal/index'); //login blocked
        	}
        }
    }
    public function logout()
    {
    	
    	// delete this user from onlinemembers table.
    	$this->load->model("portalmodel");
        $uid = $this->session->userdata('id');
        $is_customer = $this->session->userdata('is_customer');
        if($is_customer){
        	$this->portalmodel->deleteid("onlinecustomers","customer_id",$uid);
        }else{
        	$this->portalmodel->deleteid("online_customer_users","customer_user_id",$uid);
        }
    	
        $this->session->unset_userdata('name');
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('current_number_of_comments_client');
        $this->session->unset_userdata('current_number_of_blogs');
        //$this->session->sess_destroy();
        $this->session->set_flashdata("logoutmsg", "You Have Been Successfully Logged Out.");
        redirect("customerportal/index");
    }
	  public function email($to, $subject, $message)
    {
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
    function dashboard()
    {
        $this->common();
        $id                 = $this->session->userdata('id');
        $where              = " customerid='$id'";
        $accmgr             = $this->portalmodel->select_name('customer', 'accountmanagerid', $where);
        $cond               = "id='$accmgr'";
        $data['accdetails'] = $this->portalmodel->select_where_cond('', '', 'user', $cond, '', '');
        
        // select company
        $where_company = " customerid='$id'";
        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
        
        $where1             = " customerid='$company_id'";
        $taskcomm           = $this->portalmodel->select_where('', '', 'project', $where1);
        
        $prj = "";
		  if (!empty($taskcomm)) {
        foreach ($taskcomm as $t) {
            $prj = $prj . ',' . $t->id;
        }
		  }
        $prj      = substr($prj, 1);
		if($prj!=""){
        $cond1    = "project_id in ($prj) and  show_customer=0";
        $comments = $this->portalmodel->select_where_cond('15', '0', 'taskcomments', $cond1, 'id', 'DESC');
		}else{  $comments ="";}
        $li       = array();
        if (!empty($comments)) {
            foreach ($comments as $d) {
                $l['comments'] = $d->comments;
                $l['taskid']   = $d->taskid;
			
			    // also get the taskid from tasks table, which will be displayed for comments reference
                $l['taskid_for_comment'] = $this->portalmodel->select_name('task','taskid',"id='".$l['taskid']."'");
				
				$l['commentedby']=$d->commented_by;
                if ($d->commented_by == 1) {
                    $name              = "CONCAT(first_name,' ',last_name) as name";
                    $l['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                } else {
                    $name              = "CONCAT(first_name,' ',last_name) as name";
                    $l['commented_by'] = $this->portalmodel->select_username($name, 'customerid', $d->created_by, 'customer');
                }
                // $l['commented_by']=$this->portalmodel->select_username($name,'id',$d->commented_by,'user');
                $l['created_on'] = $d->created_on;
                $where5          = "comment_id=$d->id";
                $li[]            = $l;
            }
        }
        $data['taskcomm'] = $li;
        
        // now get news
        $current_date = date("Y-m-d");
        $condition_news    = "show_customer=1 AND from<='".$current_date."' AND to>='".$current_date."'";
        $news_items = $this->portalmodel->select_where_cond('5', '0', 'notification', $condition_news, 'id', 'DESC');
        
        $news = array();
        if (!empty($news_items)) {
            foreach ($news_items as $d) {
                $l['news_text'] = $d->notification;
                $l['news_id']   = $d->id;
                $name = "CONCAT(first_name,' ',last_name) as name";
                $l['created_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                $l['created_on'] = $d->created_on;
                $news[] = $l;
            }
        }
        $data['news'] = $news;
        
        // for dashboard counters
        $where_my_project_count = "customerid='".$company_id."'";
        $data['myprojects'] = $this->portalmodel->record_count_where('project', $where_my_project_count);
        
        // also get sum of pending invoices
        //$data['pending_invoice_amount'] = $this->portalmodel->select_name('invoices', 'SUM(remaining_amount)', "fk_client_id='".$id."' AND invoice_status='U'");
        $data['pending_invoice_amount'] = $this->portalmodel->select_name('invoices', 'SUM(remaining_amount)', "fk_client_id='".$company_id."' AND invoice_status='U'");
        
        $data['new_messages'] = $this->portalmodel->select_name('messages', 'count(id)', "to='".$id."' AND read_unread=0 AND to_type='C'");
        
        $this->load->view('dashboard', $data);
    }
    function projects()
    {
        $this->common();
        $id    = $this->session->userdata('id');
        
        // select company
        $where_company = " customerid='$id'";
        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
        
        $where = "customerid=$company_id";
        $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
        $li    = array();
		 if (!empty($list)) {
        foreach ($list as $d) {
            $row['id']         = $d->id;
            $row['title']      = $d->title;
            $row['details']    = $d->details;
            $row['created_by'] = date('Y-m-d', strtotime($d->created_on));
            if ($d->end_date == "" || $d->end_date == '0000-00-00') {
                $row['end_date'] = "<b>--</b>";
            } else {
                $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
            }
            $where1          = "projectid=$d->id and show_customer=0";
            $row['nooftask'] = $this->portalmodel->record_count_where('task', $where1);
            $status          = $d->status;
			if ($status == 0) {
                $row['status'] = 'Not Started';
            }
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
            }
            
            // now also get open tasks count and completed task count
            $row['open_task_count'] = $this->portalmodel->record_count_where('task', "projectid=$d->id and show_customer=0 and status IN (0,1,2,3)");
            $row['completed_task_count'] = $this->portalmodel->record_count_where('task', "projectid=$d->id and show_customer=0 and status=4");
            
            $li[] = $row;
        }
		 }
        $data['list'] = $li;
        $this->load->view('projects', $data);
        $this->load->view('templates/footer');
    }
    function task()
    {
        $this->common();
        $id               = $this->input->get('id');
        $where            = "projectid=$id and show_customer=0";
        
    	$fk_category_id=$this->input->get('fk_category_id');
        if(!empty($fk_category_id)){
        	$where.=" AND fk_category_id=".$fk_category_id;
        }
        
        // show open / completed task based on the passed parameter
        if(null!=$this->input->get('show')){
        	if($this->input->get('show')=="open"){
        		$where.=" AND status IN (0,1,2,3)";
        	}else if($this->input->get('show')=="completed"){
        		$where.=" AND status IN (4)";
        	}
        }
        
        $list             = $this->portalmodel->select_where_cond('', '', 'task', $where,"id","desc");
        $where1           = "id=$id";
         
        $prj = $this->portalmodel->select_where('','','project',  $where1);
		$data['prjname'] =$prj[0]->title;
		$data['accountmanager']    = $prj[0]->accountmanager;
		$data['projectmanager']    = $prj[0]->projectmanager;
        $data['nooftask'] = $this->portalmodel->record_count_where('task', $where);
        $li               = array();
        if (!empty($list)) {
            foreach ($list as $d) {
                $row['id']         = $d->id;
                $row['taskid']     = $d->taskid;
                $row['title']      = $d->title;
                $where7            = "id=" . $d->priority;
                $row['priority']   = $this->portalmodel->select_name('taskpriority', 'priority', $where7);
                $row['created_on'] = date('Y-m-d', strtotime($d->created_on));
                if ($d->expected_date == "" || $d->expected_date == '0000-00-00') {
                    $row['expected_date'] = "<b>--</b>";
                } else {
                    $row['expected_date'] = date('Y-m-d', strtotime($d->expected_date));
                }
                if ($d->end_date == "" || $d->end_date == '0000-00-00') {
                    $row['end_date'] = "<b>--</b>";
                } else {
                    $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
                }
                $where2              = "id=$d->priority";
                $data['priority']    = $this->portalmodel->select_name('taskpriority', 'priority', $where2);
                $name                = "CONCAT(first_name,' ',last_name) as name";
                $data['assigned_to'] = $this->portalmodel->select_username($name, 'id', $d->assigned_to, 'user');
                $status              = $d->status;
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
                
                $where_category = "id=$d->fk_category_id";
                $row['category_name'] = $this->portalmodel->select_name('categories', 'name', $where_category);
                
                $li[] = $row;
            }
        }
        $data['list'] = $li;
        
        // get categories
        $data['categories']  = $this->portalmodel->get_dropdown_list('categories', 'id', 'name', "");
        $data['fk_category_id']=$fk_category_id;
        $data['project_id']=$id;
        
        $this->load->view('tasks', $data);
        $this->load->view('templates/footer');
    }
	function alltask()
    {
        $this->common();
		$cond="";
		$ticket=$this->input->get('ticket');
		if($ticket="open")
		{
			$cond=" and status in(0,1,2,3)";
		}
		
		// when clicked on the status from notification bar
		$status = $this->input->get('status');
		if(isset($status)){
			$cond=" and status='".$status."'";
		}
		
		$fk_category_id=$this->input->get('fk_category_id');
    	if(!empty($fk_category_id)){
			$cond.=" AND fk_category_id=".$fk_category_id;
		}
		
		$id             = $this->session->userdata('id');
		
		// get company id
		$company_id = $this->portalmodel->select_username("fk_company_id AS name", 'customerid', $id, 'customer');

		//$where            = "customerid='".$id."' and show_customer=0".$cond;
        //$where = "customerid='".$company_id."' AND show_customer=0".$cond;
        $where = "projectid IN (SELECT id FROM project WHERE customerid='".$company_id."') AND show_customer=0".$cond;
        $list             = $this->portalmodel->select_where_cond('', '', 'task', $where,"id","desc");
        
        
        $li               = array();
        if (!empty($list)) {
        	foreach ($list as $d) {
				$where1           = "id='".$d->projectid."'";
         
				$prj = $this->portalmodel->select_where('','','project',  $where1);
				$row['prjname'] =(isset($prj[0]->title))?$prj[0]->title:"";
				$row['prjid'] =(isset($prj[0]->id))?$prj[0]->id:0;
				$row['accountmanager']    = (isset($prj[0]->accountmanager))?$prj[0]->accountmanager:0;
				$row['projectmanager']    = (isset($prj[0]->projectmanager))?$prj[0]->projectmanager:0;
                $row['id']         = $d->id;
                $row['taskid']     = $d->taskid;
                $row['title']      = $d->title;
                $where7            = "id=" . $d->priority;
                $row['priority']   = $this->portalmodel->select_name('taskpriority', 'priority', $where7);
                $row['created_on'] = date('Y-m-d', strtotime($d->created_on));
                if ($d->expected_date == "" || $d->expected_date == '0000-00-00') {
                    $row['expected_date'] = "<b>--</b>";
                } else {
                    $row['expected_date'] = date('Y-m-d', strtotime($d->expected_date));
                }
                if ($d->end_date == "" || $d->end_date == '0000-00-00') {
                    $row['end_date'] = "<b>--</b>";
                } else {
                    $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
                }
                $where2              = "id=$d->priority";
                $data['priority']    = $this->portalmodel->select_name('taskpriority', 'priority', $where2);
                $name                = "CONCAT(first_name,' ',last_name) as name";
                $data['assigned_to'] = $this->portalmodel->select_username($name, 'id', $d->assigned_to, 'user');
                $status              = $d->status;
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
                
                $where_category = "id=$d->fk_category_id";
                $row['category_name'] = $this->portalmodel->select_name('categories', 'name', $where_category);
                
                $li[] = $row;
            }
        }
        $data['list'] = $li;
        
        // get categories
        $data['categories']  = $this->portalmodel->get_dropdown_list('categories', 'id', 'name', "");
        $data['fk_category_id']=$fk_category_id;
        
        $this->load->view('alltask', $data);
        $this->load->view('templates/footer');
    }
    function taskdetails()
    {
        $this->common();
        $id               = $this->input->get('id');
        $where            = "id=$id and show_customer=0";
        $list             = $this->portalmodel->select_where('', '', 'task', $where);
        $where1           = "projectid=" . $list[0]->projectid . " and show_customer=0";
        $where6           = "id=" . $list[0]->status;
        $data['status']   = $this->portalmodel->select_name('status_project', 'status', $where6);
        $where7           = "id=" . $list[0]->priority;
        $data['priority'] = $this->portalmodel->select_name('taskpriority', 'priority', $where7);
		  $name               = "CONCAT(first_name,' ',last_name) as name";
		 $data['assignedto'] = $this->portalmodel->select_username($name, 'id', $list[0]->assigned_to, 'user');
		$data['assignedtoid']=$list[0]->assigned_to;
		$where_category = "id=" . $list[0]->fk_category_id;
        $data['category_name'] = $this->portalmodel->select_name('categories', 'name', $where_category);
        if ($list[0]->c_by == 1) {
          
            $data['created_by'] = $this->portalmodel->select_username($name, 'id', $list[0]->created_by, 'user');
        } else {
          
            $data['created_by'] = $this->portalmodel->select_username($name, 'customerid', $list[0]->created_by, 'customer');
        }
       
		
		 $prjwhere           = "id=" . $list[0]->projectid;
        $prj                = $this->portalmodel->select_where('', '', 'project', $prjwhere);
         $data['prjid']       = $list[0]->projectid;
		$data['accountmanager']    = $prj[0]->accountmanager;
		$data['projectmanager']    = $prj[0]->projectmanager;
        $data['prjname']     = $prj[0]->title; 
        $data['nooftask']   = $this->portalmodel->record_count_where('task', $where1);
        $data['task']       = $list;
        $where3             = "task_id=$id ";
        $data['taskimages'] = $this->portalmodel->select_where('', '', 'task_images', $where3);
        $where4             = "taskid=$id and show_customer=0";
        $comments           = $this->portalmodel->select_where_cond('', '', 'taskcomments', $where4,"created_on","DESC");
        $li                 = array();
        if (!empty($comments)) {
            foreach ($comments as $d) {
                $l['comments'] = $d->comments;
                if ($d->commented_by == 1) {
                    $name              = "CONCAT(first_name,' ',last_name) as name";
                    $l['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                } else {
                    $name              = "CONCAT(first_name,' ',last_name) as name";
                    $l['commented_by'] = $this->portalmodel->select_username($name, 'customerid', $d->created_by, 'customer');
                }
                //$name="CONCAT(first_name,' ',last_name) as name";
                //$l['commented_by']=$this->portalmodel->select_username($name,'id',$d->commented_by,'user');
                $l['created_on'] = $d->created_on;
                $where5          = "comment_id=$d->id";
                $l['images']     = $this->portalmodel->select_where('', '', 'comment_images', $where5);
                $li[]            = $l;
            }
        }
        $data['comment'] = $li;
        $this->load->view('taskdetails', $data);
        $this->load->view('templates/footer');
    }
    function taskdetailsajax()
    {
        $this->load->model("portalmodel");
        $id     = $this->input->post('id');
        $taskid = $this->input->post('taskid');
        $prjid  = $this->input->post('prjid');
		$accountmanager=$this->input->post('accountmanager');
		$projectmanager=$this->input->post('projectmanager');
        $where  = "taskid=$id and show_customer=0";
        $list   = $this->portalmodel->select_where_cond('', '', 'taskcomments', $where,"created_on","DESC");
        $li     = '<div class="text-right"><span class="badge bg-info">  <i class="fa fa fa-hand-o-right"></i></span>                      	
            <a href ="taskdetails?id=' . $id . '" >('.$taskid.')  More Details...</a></div>';
        
    	if($this->session->flashdata('error_message')) {
    		$li .= '<br /><center><span class="error_flash">';
    		$li .= $this->session->flashdata('error_message');
    		$li .= '</span></center>';
    	}
        
        if (empty($list)) {
            $li .= '<div class="desc" id="details">                  
                                 No Comments Available for ' . $taskid . '                      
                        </div>';
        } else {
            foreach ($list as $d) {
                if ($d->commented_by == 1) {
                    $name                 = "CONCAT(first_name,' ',last_name) as name";
                    $data['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                } else {
                    $name                 = "CONCAT(first_name,' ',last_name) as name";
                    $data['commented_by'] = $this->portalmodel->select_username($name, 'customerid', $d->created_by, 'customer');
                }
                $datetime = date('Y-m-d  H:i A', strtotime($d->created_on));
                $li .= ' <div class="desc">                
                      	<div class="thumb">
                      		<span class="badge bg-theme"><i class="fa fa-comments"></i></span>
                      	</div>                     
                      	<div class="details each_comment_display">' . $d->comments . ' <br/>  
                            <p><b>-</b><muted><a> ' . $data['commented_by'] . '</a><br/>' . $datetime . '</muted></p>
                      	</div>                        
                    </div>';
            }
        }
        $li .= '<div class="desc">                
                      	<div >
                      		<span class="badge bg-theme"><i class="fa fa-comments"></i></span>
							 <input type="hidden" class="form-control" name="accountmanager" id="accountmanager" value="' .$accountmanager.'">
							<input type="hidden" class="form-control" name="projectmanager" id="projectmanager" value="' .$projectmanager.'">
                      	</div>                     
                      	<div  id="details">
                            <label>Add Comment</label>
                            <input type="hidden" value="' . $taskid . '"  id="taskid_c" name="taskid_c">
                            <input type="hidden" value="' . $id . '"  id="id_c" name="id_c">
                             <input type="hidden" value="' . $prjid . '"  id="prjid" name="prjid">
                            <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
                            <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">300</span> characters left</label><br />
                            <input type="hidden" value="300" id="txt_character_limit_hidden">
                            <div class="form-group">
                                            <label style="margin-left: 15px;">File input <i>(maximum file size 3MB)</i></label>
											<div class="ml-15" id="fileadd0">
											<input type="file" name="files[]" id="upload" class="pull-left">
	                                            <button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button> 
	                                        </div>
                                        </div>
                            <div  id="addfiles">
                                        </div>
                            <input type="hidden" name="num" id="num" value="1" >
                            <div class="pull-left"><button type="button" class="btn btn-primary btn-xs btn-block" onclick="addMoreFiles()">Add More</button></div>
                    		<div class="cl">&nbsp;</div><div class="cl">&nbsp;</div>
                            <div class="pull-left">
                                <button type="submit" name="submits" id="submits" class="btn btn-block btn-primary btn-sm">Submit</button>
                            </div>
                        </div> 
                    </div>';
        
        ?>
        <script>
	        var maxLength = $("#txt_character_limit_hidden").val();
	        $('#comment').keyup(function() {
				var textlen = maxLength - $(this).val().length;
				if (textlen < 0) {
					$(this).val($(this).val().substring(0, maxLength));
				} else {
            		$("#txtarea_character_limit").html(textlen);
				}
	        });
        </script>
        <?php
        
        //$li.= print_r($list);
        echo $li;
    }
    function addtask()
    {
        $this->common();
        $id = $this->input->get('id');
        
        $prj = array();
        if(!empty($id)){
        	// get current project details
			$where1 = "id=$id";
			$prj = $this->portalmodel->select_where('', '', 'project', $where1);
			
			$data['prjname']    = $prj[0]->title;
	        /*$data['accountmanager']    = $prj[0]->accountmanager;
			$data['projectmanager']    = $prj[0]->projectmanager;*/
		
			$where            = "projectid=$id and show_customer=0";
			$data['nooftask'] = $this->portalmodel->record_count_where('task', $where);
		}
				
        $s_id             = $this->session->userdata('id'); //session ID
        
        // get company id
        $company_id = $this->portalmodel->select_username("fk_company_id AS name", 'customerid', $s_id, 'customer');

        $where2 = "id IN (SELECT id FROM project WHERE customerid='".$company_id."')";
        //$where2           = "customerid=$s_id";
        $data['project']  = $this->portalmodel->get_dropdown_list('project', 'id', 'title', $where2);
        
        
        $s_id             = $this->session->userdata('id'); //session ID
        $where2           = "customerid=$s_id";
        $data['priority'] = $this->portalmodel->get_dropdown_list('taskpriority', 'id', 'priority', '');
        
        // get categories
        $data['categories']  = $this->portalmodel->get_dropdown_list('categories', 'id', 'name', "");
        
        $this->load->view('addtask', $data);
        $this->load->view('templates/footer');
    }
    function insert_task()
    {
        $this->load->model("portalmodel");
        $prjid    = $this->input->post('projectid');
        $s_id     = $this->session->userdata('id');
		 $datetime  = date('Y-m-d  ', strtotime($this->input->post('dob')));
        $datetime1 = date('Y-m-d ', strtotime($this->input->post('doi')));
        $id       = $this->portalmodel->maxid('task', 'id');
        $num      = $id[0]->id + 1;
        $taskid   = 'T00' . $num;
        $customerid = $this->portalmodel->select_name('project', 'customerid', "id='".$prjid."'");
        
		// to prefix the customer name in description
        $customer_first_name = $this->portalmodel->select_name('customer', 'first_name', "customerid='".$s_id."'");
        $customer_last_name = $this->portalmodel->select_name('customer', 'last_name', "customerid='".$s_id."'");
        $customer_company_name = $this->portalmodel->select_name('customer', 'companyname', "customerid='".$s_id."'");
    	$priority = "Low Priority";
        if($this->input->post('priority')=="2"){
        	$priority = "High Priority";
        }else if($this->input->post('priority')=="3"){
        	$priority = "Medium Priority";
        }
        
        $data     = array(
            'taskid' => $taskid,
        	'fk_category_id' => $this->input->post('fk_category_id'),
            'projectid' => $prjid,
            'customerid' => $customerid,
			'expected_date' => $datetime,
            'expected_end' => $datetime1,
            'title' => "(".$priority.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : ".$this->input->post('title'),
            'priority' => $this->input->post('priority'),
            'description' => "(".$priority.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : ".$this->input->post('desc'),
            'status' => 0,
            'show_customer' => 0,
            'c_by' => 0,
            'created_by' => $s_id,
        	'created_on' => date("Y-m-d H:i:s")
        );
        $result   = $this->portalmodel->insert_query_('task', $data);
        $filename = strtotime("now");
        $this->load->library('upload');
        $number_of_files_uploaded = count($_FILES['files']['name']);
        //  upload calls to $_FILE
        for ($i = 0; $i < $number_of_files_uploaded; $i++):
            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
            $dir_path                       = './tasks/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/tasks/' . $filename . $i . '.' . $ext;
            $config                         = array(
                'file_name' => $filename . $i,
                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx|xls',
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
                    'image_path' => $path,
                    'task_id' => $result,
                    'created_by' => $this->session->userdata('id')
                );
                $result_taskimages             = $this->portalmodel->insert_query_('task_images', $data1);
            endif;
        endfor;
		
		  $subject = "(".$priority.")".$customer_first_name." ".$customer_last_name." (".$customer_company_name.") "." : "."New Task " . $taskid;
        
            $where   = 'customerid=' . $s_id;
            $to      = $this->portalmodel->select_name('customer', 'emailid', $where);
            $message = 'Dear Customer,<br/><br/>We have received your support request and assigned it a ticket ID of <b>' . $taskid . '</b> One of our team members will respond to your ticket shortly.<br/>
<br/><br/>
Thank you,<br/>

- Synergy IT Team
';
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
            
            // send to business email
            $to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
            if(!empty($to_business_email) && $to_business_email!='N/A'){
            	$email = $this->email($to_business_email, $subject, $message);
            }
        
        $messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b><a href="http://synergytechportal.com/index.php/portal/taskdetails?id='.$result.'" target="_blank">' . $taskid . '</a></b>.<br />Click on the Ticket ID to see the ticket Details. Please respond to ticket shortly.<br/>
<br/><br/>
Thank you,<br/>

- Synergy IT Team
';

// now get accountmanager and projectmanager from $prjid;
$where1 = "id=$prjid";
$prj = $this->portalmodel->select_where('', '', 'project', $where1);
        
$accid=$prj[0]->accountmanager;//$this->input->post('accountmanager');
$pmid=$prj[0]->projectmanager;//$this->input->post('projectmanager');
$primary_tech = $prj[0]->primary_tech;

$arrIdsIn = array();
if(isset($primary_tech) && !empty($primary_tech)){
	$arrIdsIn[] = $primary_tech;
}
if(isset($accid) && !empty($accid)){
	$arrIdsIn[] = $accid;
}
if(isset($pmid) && !empty($pmid)){
	$arrIdsIn[] = $pmid;
}
$strIds = implode(",", $arrIdsIn);
$where1 = "id IN (".$strIds.")";

//$where1       = 'id in ('.$accid.','.$pmid.')';
		
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
		
		// now assign one developer/tech to the ticket by default.
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
		
        redirect("customerportal/task?id=" . $prjid);
    }
    function addtaskcomments()
    {
        $this->load->model("portalmodel");
        $id     = $this->input->post('id_c');
        $s_id   = $this->session->userdata('id');
        $data   = array(
            'taskid' => $id,
            'comments' => $this->input->post('comment'),
            'project_id' => $this->input->post('prjid'),
            'show_customer' => 0,
            'commented_by' => 0,
            'created_by' => $s_id
        );
        $result = $this->portalmodel->insert_query_('taskcomments', $data);
        $filen  = strtotime("now");
        $this->load->library('upload');
        $number_of_files_uploaded = count($_FILES['files']['name']);
        //  upload calls to $_FILE
        for ($i = 0; $i < $number_of_files_uploaded; $i++):
            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
            $extension                      = $_FILES['files']['name'][$i];
            $ext                            = $ext = pathinfo($extension, PATHINFO_EXTENSION);
            $filename                       = $filen . $i;
            $path                           = '/comments/' . $filename . '.' . $ext;
            $config                         = array(
                'file_name' => $filename,
                'allowed_types' => '*',
                'max_size' => 3000,
                'overwrite' => FALSE,
                'upload_path' => './comments/'
            );
            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $error = array(
                    'error' => $this->upload->display_errors()
                );
                
                //$this->session->set_flashdata('error_message', $this->upload->display_errors()); //display the flashdata using session
                //redirect("customerportal/taskdetails?id=" . $id);
                //exit;
            else:
                $final_files_data[] = $this->upload->data();
                $data1              = array(
                    'image_path' => $path,
                    'comment_id' => $result,
                    'created_by' => $this->session->userdata('id')
                );
                $result1            = $this->portalmodel->insert_query_('comment_images', $data1);
            endif;
        endfor;
		$cond    = "id=$id";
        $task    = $this->portalmodel->select_where('', '', 'task', $cond);
		$subject = "New Comment " . $task[0]->taskid;
        $message = 'Dear Customer,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';
      
            $where = 'customerid=' . $s_id;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
            
            // send to business email
    		$to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
            if ($to_business_email != "") {
                $email = $this->email($to_business_email, $subject, $message);
            }
       
		 $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';

$devid=$this->input->post('assignedto');
$accid=$this->input->post('accountmanager');
$pmid=$this->input->post('projectmanager');

if(isset($devid) && !empty($devid)){
	$arrIdsIn[] = $devid;
}
if(isset($accid) && !empty($accid)){
	$arrIdsIn[] = $accid;
}
if(isset($pmid) && !empty($pmid)){
	$arrIdsIn[] = $pmid;
}
$strIds = implode(",", $arrIdsIn);
$where1 = "id IN (".$strIds.")";
/*if($devid==''||$devid=="N/A")
{
	$where1       = 'id in ('.$accid.','.$pmid.')';
}
else{
	$where1       = 'id in ('.$devid.','.$accid.','.$pmid.')';
}*/

        
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
		
		
		
        redirect("customerportal/taskdetails?id=" . $id);
    }
    function addtaskcommentsajax()
    {
        $this->load->model("portalmodel");
        $id     = $this->input->post('id_c');
        $s_id   = $this->session->userdata('id');
        $data   = array(
            'taskid' => $id,
            'comments' => $this->input->post('comment'),
            'project_id' => $this->input->post('prjid'),
            'show_customer' => 0,
            'commented_by' => 0,
            'created_by' => $s_id
        );
        $result = $this->portalmodel->insert_query_('taskcomments', $data);
        $filen  = strtotime("now");
        $this->load->library('upload');
        $number_of_files_uploaded = count($_FILES['files']['name']);
        //  upload calls to $_FILE
        for ($i = 0; $i < $number_of_files_uploaded; $i++):
            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
            $extension                      = $_FILES['files']['name'][$i];
            $ext                            = $ext = pathinfo($extension, PATHINFO_EXTENSION);
            $filename                       = $filen . $i;
            $path                           = '/comments/' . $filename . '.' . $ext;
            $config                         = array(
                'file_name' => $filename,
                'allowed_types' => '*',
                'max_size' => 3000,
                'overwrite' => FALSE,
                'upload_path' => './comments/'
            );
            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $error = array(
                    'error' => $this->upload->display_errors()
                );
                
                //$this->session->set_flashdata('error_message', $this->upload->display_errors()); //display the flashdata using session
               // redirect("customerportal/taskdetails?id=" . $id);
                exit;
            else:
                $final_files_data[] = $this->upload->data();
                $data1              = array(
                    'image_path' => $path,
                    'comment_id' => $result,
                    'created_by' => $this->session->userdata('id')
                );
                $result1            = $this->portalmodel->insert_query_('comment_images', $data1);
            endif;
        endfor;
		
				$cond    = "id=$id";
        $task    = $this->portalmodel->select_where('', '', 'task', $cond);
		$subject = "New Comment " . $task[0]->taskid;
        $message = 'Dear Customer,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';
      
            $where = 'customerid=' . $s_id;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
            
            // send to business email
    		$to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
            if (!empty($to_business_email) && $to_business_email!='N/A') {
                $email = $this->email($to_business_email, $subject, $message);
            }
       
		 $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';

$devid=$this->input->post('assignedto');
$accid=$this->input->post('accountmanager');
$pmid=$this->input->post('projectmanager');

if(isset($devid) && !empty($devid)){
	$arrIdsIn[] = $devid;
}
if(isset($accid) && !empty($accid)){
	$arrIdsIn[] = $accid;
}
if(isset($pmid) && !empty($pmid)){
	$arrIdsIn[] = $pmid;
}
$strIds = implode(",", $arrIdsIn);
$where1 = "id IN (".$strIds.")";

/*if($devid==''||$devid=="N/A")
{
	$where1       = 'id in ('.$accid.','.$pmid.')';
}
else{
	$where1       = 'id in ('.$devid.','.$accid.','.$pmid.')';
}*/

        
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
		
		
		
    }
    function form()
    {
        $this->common();
        $this->load->view('contactform');
    }
    function settings()
    {
        $this->common();
        $id              = $this->session->userdata('id');
        $where           = "customerid=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'customer', $where);
        $this->load->view('settings', $data);
        $this->load->view('templates/footer');
    }
    function updatesettings()
    {
        $this->load->model("portalmodel");
        $id       = $this->session->userdata('id');
        $where    = 'customerid=' . $id;
        $password = $this->input->post('password');
        if ($password == "") {
            $data   = array(
                'companyname' => $this->input->post('company'),
                'first_name' => $this->input->post('firstname'),
                'last_name' => $this->input->post('lastname'),
                'contactno' => $this->input->post('contactno'),
                'emailid' => $this->input->post('emailid'),
            	'business_email' => $this->input->post('business_emailid'),
                'modified_by' => $this->session->userdata('id')
            );
            $update = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
            $this->session->set_flashdata('success', 'Sucessfully updated');
            
            // change name in session
			$userdata['customer_name'] = $this->input->post('firstname')." ".$this->input->post('lastname');
			$this->session->set_userdata($userdata);
        } else {
            $password      = $this->portalmodel->select_name('customer', 'password', $where);
            $givenpassword = base64_encode($this->input->post('password'));
            if ($password == $givenpassword) {
                $newpassword = base64_encode($this->input->post('newpassword'));
                $data        = array(
                    'companyname' => $this->input->post('company'),
                    'first_name' => $this->input->post('firstname'),
                    'last_name' => $this->input->post('lastname'),
                    'contactno' => $this->input->post('contactno'),
                    'emailid' => $this->input->post('emailid'),
                    'password' => $newpassword,
                    'modified_by' => $this->session->userdata('id')
                );
                $this->session->set_flashdata('success', 'Sucessfully updated');
                $update = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
                
                // change name in session
                $userdata['customer_name'] = $this->input->post('firstname')." ".$this->input->post('lastname');
            	$this->session->set_userdata($userdata);
            }else{
            	$this->session->set_flashdata('failed', 'Please enter correct current password');
            }
        }
        
    	$filename = strtotime("now");
        $this->load->library('upload');
        $path1 = $this->input->post('path');
        if (!empty($_FILES['files']['name'])) {
            /* if($path1!=""){
            unlink($path1); 
            }*/
            $_FILES['userfile']['name']     = $_FILES['files']['name'];
            $_FILES['userfile']['type']     = $_FILES['files']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['files']['error'];
            $_FILES['userfile']['size']     = $_FILES['files']['size'];
            $dir_path                       = './user/';
            $ext                            = pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = 'user/' . $filename . '.' . $ext;
            $config                         = array(
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
                $result             = $this->portalmodel->update_query('customer', $data1, $id, 'customerid');
                
                $userdata['profile_picture'] = $path;
				$this->session->set_userdata($userdata);
            endif;
        }
        
        redirect("customerportal/settings");
    }
	function newsdetails(){
    	$this->common();
    	$this->load->model("portalmodel");
    	$id = $this->input->get('id');
    	
    	$condition_news    = "id=".$id;
        $news_items = $this->portalmodel->select_where('', '', 'notification', $condition_news);
        
        $news = array();
        if (!empty($news_items)) {
            foreach ($news_items as $d) {
                $l['news_text'] = $d->notification;
                $l['news_id']   = $d->id;
                $l['image']   = $d->image;
                $name = "CONCAT(first_name,' ',last_name) as name";
                $l['created_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                $l['created_on'] = $d->created_on;
                $news = $l;
            }
        }
        $data['news'] = $news;
        
        $this->load->view('newsdetails', $data);
        $this->load->view('templates/footer');
    }
    
	/**
     * following function will reset the session 
     */
    function updateCommentsSession(){
    	$condition_customer_projects = " project_id IN (SELECT id FROM project WHERE customerid = '".$_SESSION['id']."') AND show_customer=0";
        $this->db->select("count(id) AS number_of_comments");
        $this->db->from("taskcomments");
        $this->db->where($condition_customer_projects);
        $query = $this->db->get();
        
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_comments;
        }
        
    	if(!empty($comments_from_db)){
    		$data = $_SESSION;
    		$data['current_number_of_comments_client'] = $comments_from_db;
    		$this->session->set_userdata($data);
    	}
    }
    
	/**
     * following function will get if any new comments has added and it will show a bell icon on the top menu
     */
    function checkNewComments(){
    	// first get the count from session
    	$current_number_of_comments = $this->session->userdata('current_number_of_comments_client');
    	
    	// now get current comments from db
    	$condition_customer_projects = " project_id IN (SELECT id FROM project WHERE customerid = '".$_SESSION['id']."') AND show_customer=0";
        $this->db->select("count(id) AS number_of_comments");
        $this->db->from("taskcomments");
        $this->db->where($condition_customer_projects);
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_comments;
        }
    	
        $difference = intval($comments_from_db)-intval($current_number_of_comments);
        
    	// echo the difference
        echo $difference;    	
    }
    
	/**
     * following function will be used for chat for projects
     */
    function chat_project(){
    	$this->common();
    	
    	$project_id = $this->input->get('project_id');
    	$is_customer = $this->session->userdata('is_customer');
    	
    	// now get project related chat
    	$data['project_name'] = $this->portalmodel->select_name('project', 'title', "id='".$project_id."'");
    	$data['project_id'] = $project_id;
    	
    	// get list of current online members
    	$tmp_online_member_ids = $this->portalmodel->list_select1("onlinemembers");
    	$online_member_ids = array();
    	foreach ($tmp_online_member_ids as $eachid){
    		$online_member_ids[]=$eachid->user_id;
    	}
    	
    	
    	// now get associated members of this project.
    	$associated_members_ids = $this->portalmodel->list_select("project","id",$project_id);
    	
    	$tmpArr = array();
    	if(isset($associated_members_ids[0]['accountmanager']) && !empty($associated_members_ids[0]['accountmanager'])){
    		$tmpArr[]=$associated_members_ids[0]['accountmanager'];
    	}
    	if(isset($associated_members_ids[0]['projectmanager']) && !empty($associated_members_ids[0]['projectmanager'])){
    		$tmpArr[]=$associated_members_ids[0]['projectmanager'];
    	}
    	if(isset($associated_members_ids[0]['developer']) && !empty($associated_members_ids[0]['developer'])){
    		$tmpArr[]=$associated_members_ids[0]['developer'];
    	}
    	
    	//$tmp_members_ids = implode(",", array($associated_members_ids[0]['accountmanager'],$associated_members_ids[0]['projectmanager'],$associated_members_ids[0]['developer']));
    	$tmp_members_ids = implode(",", $tmpArr);
    	$arr_member_names = $this->portalmodel->select_where('', '', 'user', "id IN (".$tmp_members_ids.")");
    	$associated_member_names = array();
    	foreach ($arr_member_names as $eachRecordDetail){
    		if(in_array($eachRecordDetail->id, $online_member_ids)){
    			$associated_member_names[] = '<i class="fa fa-circle" aria-hidden="true"></i> '.$eachRecordDetail->first_name;
    		}else{
    			$associated_member_names[] = $eachRecordDetail->first_name;
    		}
    	}
    	$associated_member_names = implode(", ", $associated_member_names);
    	$data['associated_member_names'] = $associated_member_names;
    	/* getting associated members list ends */
    	
    	// now get the member list for adding new members.
    	$already_member_ids = $this->portalmodel->list_select("chat_project_members","fk_project_id",$project_id);
    	$arr_already_member_ids = array();
    	foreach ($already_member_ids as $eachrecord){
    		$arr_already_member_ids[]=$eachrecord['fk_user_id'];
    	}
    	$arr_already_member_ids[]=1001; // here we are adding 1001 for the admin, because he doesn't need to be selected
    	$in_already_member_ids = implode(",", $arr_already_member_ids);
    	
    	$combined_ids = $tmp_members_ids.",".$in_already_member_ids;
    	$condition_new_members = " id NOT IN (".$combined_ids.")";
    	
    	$add_new_member_list = $this->portalmodel->get_dropdownuser_list_without_blank("user","id","CONCAT(first_name,' ',last_name) AS name",$condition_new_members);
    	asort($add_new_member_list);
    	$data['add_new_member_list']=$add_new_member_list;
    	//////////////////
    	
    	/// now update associated member's list with the project chat members
    	$arr_member_names = $this->portalmodel->select_where('', '', 'user', "id IN (".$in_already_member_ids.")");
    	$associated_member_names = array();
    	foreach ($arr_member_names as $eachRecordDetail){
    		if(in_array($eachRecordDetail->id, $online_member_ids)){
    			$associated_member_names[] = '<i class="fa fa-circle" aria-hidden="true"></i> '.$eachRecordDetail->first_name;
    		}else{
    			$associated_member_names[] = $eachRecordDetail->first_name;
    		}
    	}
    	$associated_member_names = implode(", ", $associated_member_names);
    	$data['associated_member_names'] = $data['associated_member_names'].", ".$associated_member_names;
    	
    	// now also add customer name as well
    	$associated_customer_id = $this->portalmodel->select_name('project', 'customerid', "id='".$project_id."'");
    	if(!empty($associated_customer_id)){
    		$associated_customer_name = $this->portalmodel->select_name('customer', 'companyname', "customerid='".$associated_customer_id."'");
    		
    		// also get list of onlinecustomers to highlight
	    	$tmp_online_user_ids = $this->portalmodel->list_select1("onlinecustomers");
	    	$online_customer_ids = array();
	    	foreach ($tmp_online_user_ids as $eachid){
	    		$online_customer_ids[]=$eachid->customer_id;
	    	}
	    	
	    	if (in_array($associated_customer_id, $online_customer_ids)){
	    		$associated_customer_name = '<i class="fa fa-circle" aria-hidden="true"></i> '.$associated_customer_name;
	    	}
	    	
	    	$data['associated_member_names'] = $data['associated_member_names'].", ".$associated_customer_name;
    	}
    	
    	// now also get the associated customer(member) names with this project
    	// also get list of online customer users to highlight
    	$tmp_online_customer_user_ids = $this->portalmodel->list_select1("online_customer_users");
    	$online_customer_user_ids = array();
    	foreach ($tmp_online_customer_user_ids as $eachid){
    		$online_customer_user_ids[]=$eachid->customer_user_id;
    	}
    	
    	$associated_users = $this->portalmodel->select_where('', '', 'customer_user_project_chat', "FIND_IN_SET( $project_id, project_ids )");
    	$arr_associated_users = array();
    	foreach ($associated_users as $each_associated_user){
    		if(in_array($each_associated_user->id, $online_customer_user_ids)){
    			$arr_associated_users[] = '<i class="fa fa-circle" aria-hidden="true"></i> '.$each_associated_user->first_name;
    		}else{
    			$arr_associated_users[] = $each_associated_user->first_name;
    		}
    	}
    	$data['associated_member_names'] = $data['associated_member_names'].", ".implode(",", $arr_associated_users);
    	
    	//$tmp_associated_members = explode(", ", strtolower($data['associated_member_names']));
    	//asort($tmp_associated_members);
    	//$data['associated_member_names'] = implode(", ", $tmp_associated_members);
    	///
    	
    	$chat_data = $this->portalmodel->getProjectChat($project_id);
    	$data['chat_data'] = $chat_data;
    	
    	/* storing latest chat id for notification STARTS */
    	$fk_user_id = $this->session->userdata('id');
    	$last_chat_id = $this->portalmodel->select_name("last_chat_project_customer","id","fk_user_id='".$fk_user_id."' AND fk_project_id='".$project_id."'");
    	
    	// get latest chat id
    	if($is_customer){
	    	$fk_chat_id = $this->portalmodel->select_name("chat_projects","MAX(chat_id)","fk_project_id='".$project_id."'");
	    	
	    	if((empty($last_chat_id) || $last_chat_id=="N/A") && (!empty($fk_chat_id) && $fk_chat_id!="N/A")){
	    		// insert new
	    		$datainsert = array(
	    			"fk_user_id"=>$fk_user_id,
	    			"fk_chat_id"=>$fk_chat_id,
	    			"fk_project_id"=>$project_id
	    		);
	    		$insert = $this->portalmodel->insert_query_("last_chat_project_customer",$datainsert);
	    	}else{
	    		// update with latest chat id for this project
	    		$dataupdate = array(
	    			"fk_chat_id"=>$fk_chat_id
	    		);
	    		$this->portalmodel->update_query("last_chat_project_customer",$dataupdate,$last_chat_id,"id");
	    	}
    	}
    	/* storing latest chat id for notification ENDS */
    	
    	$this->load->view('chat_project', $data);
        $this->load->view('templates/footer');
    }
    
	/**
     * following function will check for automatically chat update for project chat
     */
    function chat_check_project(){
    	$this->load->model("portalmodel");
    	$fk_project_id = $this->input->post('project_id');
    	$txt_search = $this->input->post('txt_search');
    	$fk_user_id = $this->session->userdata('id');
    	$is_customer = $this->session->userdata('is_customer');
    	
    	$chat_data = $this->portalmodel->getProjectChat($fk_project_id,$txt_search);
    	$data['chat_data'] = $chat_data;
    	$data['logged_in_userid'] = $fk_user_id;
    	
    	/* storing latest chat id for notification STARTS */
    	if($is_customer){
	    	$last_chat_id = $this->portalmodel->select_name("last_chat_project_customer","id","fk_user_id='".$fk_user_id."' AND fk_project_id='".$fk_project_id."'");
	    	
	    	// get latest chat id
	    	$fk_chat_id = $this->portalmodel->select_name("chat_projects","MAX(chat_id)","fk_project_id='".$fk_project_id."'");
	    	
	    	if((empty($last_chat_id) || $last_chat_id=="N/A") && (!empty($fk_chat_id) && $fk_chat_id!="N/A")){
	    		// insert new
	    		$datainsert = array(
	    			"fk_user_id"=>$fk_user_id,
	    			"fk_chat_id"=>$fk_chat_id,
	    			"fk_project_id"=>$fk_project_id
	    		);
	    		$insert = $this->portalmodel->insert_query_("last_chat_project_customer",$datainsert);
	    	}else{
	    		// update with latest chat id for this project
	    		$dataupdate = array(
	    			"fk_chat_id"=>$fk_chat_id
	    		);
	    		$this->portalmodel->update_query("last_chat_project_customer",$dataupdate,$last_chat_id,"id");
	    	}
    	}
    	/* storing latest chat id for notification ENDS */
    	
    	// load ajax file to view the chat data
    	$this->load->view('chat_project_ajax', $data);
    }
    
	/**
     * adding new comment in project chat
     */
    function chat_add_project(){
    	$this->load->model("portalmodel");
    	$message = urldecode($this->input->post('message'));
        $fk_project_id = $this->input->post('project_id');
        $fk_user_id = $this->session->userdata('id');
        $is_customer = $this->session->userdata('is_customer');
        
        if($is_customer){
        	$data = array(
	        	'fk_project_id'=>$fk_project_id,
	        	'fk_user_id'=>0,
	        	'fk_customer_id'=>$fk_user_id,
	        	'message'=>$message,
	        	'created_on'=>date("Y-m-d H:i:s")
	        );
        }else{
        	$data = array(
	        	'fk_project_id'=>$fk_project_id,
	        	'fk_user_id'=>0,
	        	'fk_customer_id'=>0,
        		'fk_customer_user_id'=>$fk_user_id,
	        	'message'=>$message,
	        	'created_on'=>date("Y-m-d H:i:s")
	        );
        }
        
        $result   = $this->portalmodel->insert_query_('chat_projects', $data);
        
        // now get list of added comments in asc order and display with ajax
    	$chat_data = $this->portalmodel->getProjectChat($fk_project_id);
    	$data['chat_data'] = $chat_data;
    	$data['logged_in_userid'] = $fk_user_id;
    	
    	/* storing latest chat id for notification STARTS */
    	$last_chat_id = $this->portalmodel->select_name("last_chat_project_customer","id","fk_user_id='".$fk_user_id."' AND fk_project_id='".$fk_project_id."'");
    	
    	// get latest chat id
    	$fk_chat_id = $this->portalmodel->select_name("chat_projects","MAX(chat_id)","fk_project_id='".$fk_project_id."'");
    	
    	if((empty($last_chat_id) || $last_chat_id=="N/A") && (!empty($fk_chat_id) && $fk_chat_id!="N/A")){
    		// insert new
    		$datainsert = array(
    			"fk_user_id"=>$fk_user_id,
    			"fk_chat_id"=>$fk_chat_id,
    			"fk_project_id"=>$fk_project_id
    		);
    		$insert = $this->portalmodel->insert_query_("last_chat_project_customer",$datainsert);
    	}else{
    		// update with latest chat id for this project
    		$dataupdate = array(
    			"fk_chat_id"=>$fk_chat_id
    		);
    		$this->portalmodel->update_query("last_chat_project_customer",$dataupdate,$last_chat_id,"id");
    	}
    	/* storing latest chat id for notification ENDS */
    	
    	// load ajax file to view the chat data
    	$this->load->view('chat_project_ajax', $data);
    }
    
	/**
     * Following function will upload file for chat
     */
    function upload_project_chat_file_ajax(){
    	$this->load->model("portalmodel");
    	
    	$fk_project_id = $this->input->post('project_id');
	    $fk_user_id = $this->session->userdata('id');
    	
    	for ($i=0;$i<count($_FILES['files']['name']);$i++){
    		// first we will add a new record for new chat, and then will add new file with chat reference number
	    	$message = "added file...";
	    	$data_file_add_message = array(
	        	'fk_project_id'=>$fk_project_id,
	        	'fk_user_id'=>0,
	    		'fk_customer_id'=>$fk_user_id,
	        	'message'=>$message,
	        	'created_on'=>date("Y-m-d H:i:s")
	        );
	        $fk_chat_id = $this->portalmodel->insert_query_('chat_projects', $data_file_add_message);
	        
	        // upload file and add one chat message for file upload
	    	$filename = strtotime("now");
	        $this->load->library('upload');
	        
	    	$_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
			$_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
			$_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
			$_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
			$_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
			$dir_path                       = './project_chat_files/';
			$ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
			$path                           = '/project_chat_files/' . $filename . '.' . $ext;
			$config                         = array(
				'file_name' => $filename,
				'allowed_types' => '*',
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
					'filepath' => $path,
					'fk_chat_id' => $fk_chat_id
				);
				$result1 = $this->portalmodel->insert_query_('chat_project_files', $data1);
			endif;
    	}
    	
    }
    
	/**
     * Forgot Password
     */
    function forgotpassword(){
    	$this->load->model("portalmodel");
    	
    	$email = $this->input->post('email_forgotpassword');
    	
    	if(!empty($email)){
    		// get user id from email
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
	    		
	    		$forgotpassword_send_email = $this->email($to, $subject, $message);
	    		
	    		if(!empty($business_email) && $business_email!='N/A'){
	    			$send_to_business = $this->email($business_email, $subject, $message);
	    		}
	    		
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'We have sent you an email to reset your password!'); //display the flashdata using session
	            redirect('customerportal/index'); //go to login page
	    		
	    	}else{
	    		
	    		// check in customer_user_project_chat table
	    		$condition = "email = '".$email."'";
		    	$user_id = $this->portalmodel->select_name('customer_user_project_chat', 'id', $condition);
		    	
		    	if(!empty($user_id) && $user_id!='N/A'){
		    		$user_name = $this->portalmodel->select_name('customer_user_project_chat', 'first_name', "id='".$user_id."'");
		    		
		    		// send email for reset password link
		    		$param = md5("customer_user_id")."=".md5($user_id);
		    		$link = base_url('index.php/customerportal/resetpassword/?'.$param);
		    		
		    		$to = $email;
		    		$subject = "SynergyInteract : Reset Password!";
		    		$message = "Hello ".$user_name.",<br /><br />";
		    		$message .= "We have received your request to reset your password!<br /><br />";
		    		$message .= "Click on below link or copy and paste it in browser to reset your password.<br /><br />";
		    		$message .= "<a href=".$link." target='_blank'>".$link."</a><br /><br />";
		    		$message .= "Thank You<br />- Synergy IT Team.";
		    		
		    		$forgotpassword_send_email = $this->email($to, $subject, $message);
		    		
		    		// set error message
		    		$this->session->set_flashdata('error_message', 'We have sent you an email to reset your password!'); //display the flashdata using session
		            redirect('customerportal/index'); //go to login page
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
			    		
			    		$forgotpassword_send_email = $this->email($to, $subject, $message);
			    		
			    		if(!empty($business_email) && $business_email!='N/A'){
			    			$send_to_business = $this->email($business_email, $subject, $message);
			    		}
			    		
			    		// set error message
			    		$this->session->set_flashdata('error_message', 'We have sent you an email to reset your password!'); //display the flashdata using session
			            redirect('customerportal/index'); //go to login page
			    	}else {
			    		// set error message
			    		$this->session->set_flashdata('error_message', 'Email not registered!'); //display the flashdata using session
			            redirect('customerportal/index'); //user is not valid so goto login page again
			    	}
		    	}
	    		
	    		// set error message
	    		//$this->session->set_flashdata('error_message', 'Email not registered!'); //display the flashdata using session
	            //redirect('customerportal/index'); //user is not valid so goto login page again
	    	}
    	}else{
    		// set error message
    		$this->session->set_flashdata('error_message', 'Please enter valid Email!'); //display the flashdata using session
            redirect('customerportal/index'); //user is not valid so goto login page again
    	}
    }
    
	/**
     * Reset Password
     */
    function resetpassword(){
    	$this->load->model("portalmodel");
    	
    	if(isset($_GET[md5('customerid')])){
    		// its customer
    		$id = $this->input->get(md5('customerid'));
    		
    		// get user id of the received id, and if that user doesn't exists, then display error invalid request
    		$user_id = $this->portalmodel->select_name('customer', 'customerid', "md5(customerid)='".$id."'");
    		
    		$data['customer_id']=$user_id;
    		
	    	if(!empty($user_id) && $user_id!="N/A"){
	    		// load view for reset password
	    		$this->load->view('resetpassword',$data);
	    	}else{
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'Invalid Request!'); //display the flashdata using session
	            redirect('customerportal/index'); //user is not valid so goto login page again
	    	}
    	}else if (isset($_GET[md5('customer_user_id')])){
    		// its customer user
    		$id = $this->input->get(md5('customer_user_id'));
    		
    		// get user id of the received id, and if that user doesn't exists, then display error invalid request
    		$user_id = $this->portalmodel->select_name('customer_user_project_chat', 'id', "md5(id)='".$id."'");
    		
    		$data['customer_user_id']=$user_id;
    		
	    	if(!empty($user_id) && $user_id!="N/A"){
	    		// load view for reset password
	    		$this->load->view('resetpassword',$data);
	    	}else{
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'Invalid Request!'); //display the flashdata using session
	            redirect('customerportal/index'); //user is not valid so goto login page again
	    	}
    	}else if (isset($_GET[md5('user_id')])){
    		// its user (admin/employee/tech)
    		$id = $this->input->get(md5('user_id'));
    		
    		// get user id of the received id, and if that user doesn't exists, then display error invalid request
    		$user_id = $this->portalmodel->select_name('user', 'id', "md5(id)='".$id."'");
    		
    		$data['user_id']=$user_id;
    		
	    	if(!empty($user_id) && $user_id!="N/A"){
	    		// load view for reset password
	    		$this->load->view('resetpassword',$data);
	    	}else{
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'Invalid Request!'); //display the flashdata using session
	            redirect('customerportal/index'); //user is not valid so goto login page again
	    	}
    	}
    }
    
	/**
     * Update New Password
     */
    function updatepassword(){
    	$this->load->model("portalmodel");
    	
    	$new_password = $this->input->post('new_password');
    	$confirm_password = $this->input->post('confirm_password');
    	
    	if(isset($_POST['customer_id'])){
    		// its customer
	    	$user_id = $this->input->post('customer_id');
	    	if(empty($new_password)){
	    		// set error message and reload the resetpassword page
	    		$data['customer_id'] = $user_id;
	    		$this->session->set_flashdata('error_message', 'Please enter new password!'); //display the flashdata using session
	            $this->load->view('resetpassword',$data);
	    	}else if($new_password!=$confirm_password){
	    		// set error message and reload the resetpassword page
	    		$data['customer_id'] = $user_id;
	    		$this->session->set_flashdata('error_message', 'Confirm Password did not match!'); //display the flashdata using session
	            $this->load->view('resetpassword',$data);
	    	}else{
	    		$data     = array(
		            'password' => base64_encode($new_password)
		        );
		        $result   = $this->portalmodel->update_query('customer', $data, $user_id, 'customerid');
		        $this->session->set_flashdata('error_message', 'Password reset successfully!'); //display the flashdata using session
		        redirect('customerportal/index');
	    	}
    	}else if(isset($_POST['customer_user_id'])){
    		// its customer user for chat
    		$user_id = $this->input->post('customer_user_id');
	    	if(empty($new_password)){
	    		// set error message and reload the resetpassword page
	    		$data['customer_user_id'] = $user_id;
	    		$this->session->set_flashdata('error_message', 'Please enter new password!'); //display the flashdata using session
	            $this->load->view('resetpassword',$data);
	    	}else if($new_password!=$confirm_password){
	    		// set error message and reload the resetpassword page
	    		$data['customer_user_id'] = $user_id;
	    		$this->session->set_flashdata('error_message', 'Confirm Password did not match!'); //display the flashdata using session
	            $this->load->view('resetpassword',$data);
	    	}else{
	    		$data     = array(
		            'password' => base64_encode($new_password)
		        );
		        $result   = $this->portalmodel->update_query('customer_user_project_chat', $data, $user_id, 'id');
		        $this->session->set_flashdata('error_message', 'Password reset successfully!'); //display the flashdata using session
		        redirect('customerportal/index');
	    	}
    	}else if(isset($_POST['user_id'])){
    		// its user like admin, employee or tech
    		$user_id = $this->input->post('user_id');
	    	if(empty($new_password)){
	    		// set error message and reload the resetpassword page
	    		$data['user_id'] = $user_id;
	    		$this->session->set_flashdata('error_message', 'Please enter new password!'); //display the flashdata using session
	            $this->load->view('resetpassword',$data);
	    	}else if($new_password!=$confirm_password){
	    		// set error message and reload the resetpassword page
	    		$data['user_id'] = $user_id;
	    		$this->session->set_flashdata('error_message', 'Confirm Password did not match!'); //display the flashdata using session
	            $this->load->view('resetpassword',$data);
	    	}else{
	    		$data     = array(
		            'password' => base64_encode($new_password)
		        );
		        $result   = $this->portalmodel->update_query('user', $data, $user_id, 'id');
		        $this->session->set_flashdata('error_message', 'Password reset successfully!'); //display the flashdata using session
		        redirect('customerportal/index');
	    	}
    	}
    }
    
	/**
     * following function will reset the session 
     */
    function updateBlogsSession(){
    	
    	$this->load->model("portalmodel");
    	
    	$current_date = date("Y-m-d");
        $condition_news    = "show_customer=1 AND from<='".$current_date."' AND to>='".$current_date."'";
        
        // just update the latest id in table last_blog_notification
    	$logged_in_user_id = $this->session->userdata('id');
        
    	// get latest id
    	$this->db->select("max(id) AS maxid");
        $this->db->from("notification");
        $this->db->where($condition_news);
        $query = $this->db->get();
        
    	$query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$maxid= $row[0]->maxid;
        }
        
        if(!empty($maxid)){
        	// update maxid in table last_blog_notification
        	
        	// first check if the entry exists, then update, otherwise insert
        	
        	$fk_customer_id = $this->portalmodel->select_name("last_blog_notification","fk_customer_id","fk_customer_id='".$logged_in_user_id."'");
        	if(!empty($fk_customer_id) && $fk_customer_id!="N/A"){
	        	$data = array("fk_blog_id"=>$maxid);
	        	$this->portalmodel->update_query('last_blog_notification', $data, $logged_in_user_id, 'fk_customer_id');
        	}else{
        		// insert
        		$data = array("fk_customer_id"=>$logged_in_user_id,"fk_blog_id"=>$maxid);
        		$this->portalmodel->insert_query_('last_blog_notification', $data);
        	}
        }
    	
        /*$this->db->select("count(id) AS number_of_blogs");
        $this->db->from("notification");
        $this->db->where($condition_news);
        $query = $this->db->get();
        
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_blogs;
        }
        
    	if(!empty($comments_from_db)){
    		$data = $_SESSION;
    		$data['current_number_of_blogs'] = $comments_from_db;
    		$this->session->set_userdata($data);
    	}*/
    }
    
	/**
     * following function will get if any new blog has added and it will show a bell icon on the top menu
     */
    function checkNewBlogs(){
    	
    	$this->load->model("portalmodel");
    	
    	$logged_in_user_id = $this->session->userdata('id');
    	
    	// get last blog id
    	$last_blog_id = $this->portalmodel->select_name("last_blog_notification","fk_blog_id","fk_customer_id='".$logged_in_user_id."'");
    	
    	// now get current comments from db
    	$current_date = date("Y-m-d");
        $condition_news = "show_customer=1 AND from<='".$current_date."' AND to>='".$current_date."' AND id>'".$last_blog_id."'";
        
        $this->db->select("count(id) AS number_of_blogs");
        $this->db->from("notification");
        $this->db->where($condition_news);
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_blogs;
        }
    	
        $difference = $comments_from_db;//intval($comments_from_db)-intval($current_number_of_blogs);
        
    	// echo the difference
        echo $difference;    	
    }
    
    // check currently online members, customers and customer users/members for chat project
    public function check_online_members(){
    	$this->load->model("portalmodel");
    	
    	$project_id = $this->input->post('project_id');
    	
    	$final_names = "";
    	
    	// get list of current online members
    	$tmp_online_member_ids = $this->portalmodel->list_select1("onlinemembers");
    	$online_member_ids = array();
    	foreach ($tmp_online_member_ids as $eachid){
    		$online_member_ids[]=$eachid->user_id;
    	}
    	
    	$associated_members_ids = $this->portalmodel->list_select("project","id",$project_id);
    	$tmp_members_ids = implode(",", array($associated_members_ids[0]['accountmanager'],$associated_members_ids[0]['projectmanager'],$associated_members_ids[0]['developer']));
    	$arr_member_names = $this->portalmodel->select_where('', '', 'user', "id IN (".$tmp_members_ids.")");
    	$associated_member_names = array();
    	foreach ($arr_member_names as $eachRecordDetail){
    		if(in_array($eachRecordDetail->id, $online_member_ids)){
    			$associated_member_names[] = '<i class="fa fa-circle" aria-hidden="true"></i> '.$eachRecordDetail->first_name;
    		}else{
    			$associated_member_names[] = $eachRecordDetail->first_name;
    		}
    	}
    	$final_names = implode(", ", $associated_member_names);
    	
    	// now get the customer member list
    	$already_member_ids = $this->portalmodel->list_select("chat_project_members","fk_project_id",$project_id);
    	$arr_already_member_ids = array();
    	foreach ($already_member_ids as $eachrecord){
    		$arr_already_member_ids[]=$eachrecord['fk_user_id'];
    	}
    	$arr_already_member_ids[]=1001; // here we are adding 1001 for the admin, because he doesn't need to be selected
    	$in_already_member_ids = implode(",", $arr_already_member_ids);
    	
    	$arr_member_names = $this->portalmodel->select_where('', '', 'user', "id IN (".$in_already_member_ids.")");
    	$associated_member_names = array();
    	foreach ($arr_member_names as $eachRecordDetail){
    		if(in_array($eachRecordDetail->id, $online_member_ids)){
    			$associated_member_names[] = '<i class="fa fa-circle" aria-hidden="true"></i> '.$eachRecordDetail->first_name;
    		}else{
    			$associated_member_names[] = $eachRecordDetail->first_name;
    		}
    	}
    	$associated_member_names = implode(", ", $associated_member_names);
    	$final_names = (!empty($final_names))?$final_names.", ".$associated_member_names:$associated_member_names;
    	
    	// online customers
    	$associated_customer_id = $this->portalmodel->select_name('project', 'customerid', "id='".$project_id."'");
    	if(!empty($associated_customer_id)){
    		$associated_customer_name = $this->portalmodel->select_name('customer', 'companyname', "customerid='".$associated_customer_id."'");
    		
    		// also get list of onlinecustomers to highlight
	    	$tmp_online_user_ids = $this->portalmodel->list_select1("onlinecustomers");
	    	$online_customer_ids = array();
	    	foreach ($tmp_online_user_ids as $eachid){
	    		$online_customer_ids[]=$eachid->customer_id;
	    	}
	    	
	    	if (in_array($associated_customer_id, $online_customer_ids)){
	    		$associated_customer_name = '<i class="fa fa-circle" aria-hidden="true"></i> '.$associated_customer_name;
	    	}
	    	
	    	$final_names = (!empty($final_names))?$final_names.", ".$associated_customer_name:$associated_customer_name;
    	}
    	
    	// online customer users
    	$tmp_online_customer_user_ids = $this->portalmodel->list_select1("online_customer_users");
    	$online_customer_user_ids = array();
    	foreach ($tmp_online_customer_user_ids as $eachid){
    		$online_customer_user_ids[]=$eachid->customer_user_id;
    	}
    	
    	$associated_users = $this->portalmodel->select_where('', '', 'customer_user_project_chat', "FIND_IN_SET( $project_id, project_ids )");
    	$arr_associated_users = array();
    	foreach ($associated_users as $each_associated_user){
    		if(in_array($each_associated_user->id, $online_customer_user_ids)){
    			$arr_associated_users[] = '<i class="fa fa-circle" aria-hidden="true"></i> '.$each_associated_user->first_name;
    		}else{
    			$arr_associated_users[] = $each_associated_user->first_name;
    		}
    	}
    	
    	$associated_member_names = implode(",", $arr_associated_users);
    	
    	$final_names = (!empty($final_names))?$final_names.", ".$associated_member_names:$associated_member_names;
    	
    	echo $final_names;
    }
    
	public function tasknotifications(){
    	$this->common();
        
    	$customer_or_employee_id = $this->session->userdata('customer_or_employee_id');
        
        $where = array();
        $where = " fk_customer_or_employee_id='".$customer_or_employee_id."' AND user_type='C'";
        
        $details = $this->portalmodel->select('', '', 'task_notifications', $where,'created_on','DESC');
        
    	foreach ($details as $key=>$values){
        	$details[$key]->ticket_id = $this->portalmodel->select_username("taskid as name", 'id', $values->fk_task_id, 'task');
        }
        
        $data['details'] = $details;
        
        $this->load->view('tasknotifications', $data);
        $this->load->view('templates/footer');
    }
    
    public function markTicketNotificationAsRead(){
    	$this->load->model("portalmodel");
        
    	$id = $this->input->post('id');
    	
    	$data=array('read_unread'=>'1');
        $result = $this->portalmodel->update_query('task_notifications', $data, $id, 'id');
    }
    
    public function invoices(){
		$this->common();
		
		$id = $this->session->userdata('id');

		$where_company = " customerid='$id'";
        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);
        
		$where = "fk_client_id='".$company_id."'";
		$list  = $this->portalmodel->select_where_cond('', '', 'invoices', $where,"id","DESC");
        
        $li = array();
        if (!empty($list)) {
            foreach ($list as $d) {
                $row['id'] = $d->id;
                $row['invoice_number'] = $d->invoice_number;
                
                $customer = "id=".$d->fk_client_id;
                $row['customer']  = $this->portalmodel->select_name('customer_company', 'name', $customer);
                
            	$row['fk_task_id'] = $d->fk_task_id;
                if(isset($d->fk_task_id) && !empty($d->fk_task_id)){
	                $condition_task_id = 'id='.$d->fk_task_id;
	                $row['ticket_id'] = $this->portalmodel->select_name('task', 'taskid', $condition_task_id);
	                $row['task'] = $this->portalmodel->select_name('task', 'title', $condition_task_id);
	                $row['show_to_customer'] = $this->portalmodel->select_name('task', 'show_customer', $condition_task_id);
                }else {
                	$row['ticket_id'] = "";
                	$row['task'] = "";
                	$row['show_to_customer']=1;	// don't show the link
                }
                
                //$row['task']      = $d->task;
                $row['invoice_date']    = date("d F, Y",strtotime($d->invoice_date));
                $row['amount'] = $d->amount;
                $row['remaining_amount'] = $d->remaining_amount;
                $row['invoice_status'] = ($d->invoice_status=="U")?"Un-paid":"Paid";
                $row['description'] = $d->description;
                
                
                $li[] = $row;
            }
        }
        $data['list'] = $li;
        $this->load->view('invoices', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Message Module
     */
	public function addmessage(){
        $this->common();
        
        $id = $this->session->userdata('id');
        
        $accountmanager_id = $this->portalmodel->select_name('customer', 'accountmanagerid', " customerid='$id'");
        if(!empty($accountmanager_id) && $accountmanager_id!='N/A'){
        	$accountmanager_email = $this->portalmodel->select_name('user', 'email', " id='$accountmanager_id'");
        	$accountmanager_name = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) AS name", 'id', $accountmanager_id, 'user');
        }
        
        $data['accountmanager_id'] = $accountmanager_id;
        $data['accountmanager_email'] = $accountmanager_email;
        $data['accountmanager_name'] = $accountmanager_name;
        
        $this->load->view('addmessage',$data);
        $this->load->view('templates/footer');
    }
    
	public function insert_message(){
    	$this->load->model("portalmodel");
        $s_id           = $this->session->userdata('id');
        
        $id             = $this->portalmodel->maxid('messages', 'id');
        $num            = $id[0]->id + 1;
        
        $data           = array(
            'to' => $this->input->post('to'),
            'subject' => $this->input->post('subject'),
            'message' => $this->input->post('message'),
            'created_by' => $s_id,
        	'from_type' => 'C',
        	'to_type' => 'U'
        );
        
        $result         = $this->portalmodel->insert_query_('messages', $data);
        
        // now also send email to the provided email for 'To'
        $to = $this->input->post('to');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        
        $to_email = $this->portalmodel->select_name("user","email","id='".$to."'");
        if(!empty($to_email) && $to_email!="N/A"){
        	$email = $this->email($to_email, $subject, $message);
        }
        
        // also send email to business email
		$to_business_email = $this->portalmodel->select_name("user","business_email","id='".$to."'");
        if(!empty($to_business_email) && $to_business_email!="N/A"){
        	$email = $this->email($to_business_email, $subject, $message);
        }
        
        redirect("customerportal/messages");
    }
    
	public function messages(){
    	$this->common();
        $id = $this->session->userdata('id');
        
        $where = array();
        $where = " to='".$id."' AND to_type='C' AND from_type='U'";
        
        $details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        
        foreach ($details as $key=>$values){
        	$details[$key]->from_name = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->created_by, 'user');
        }
        
        $data['details'] = $details;
        $this->load->view('messages', $data);
        $this->load->view('templates/footer');
    }
    
    public function markMessageAsRead(){
    	$this->load->model("portalmodel");
        
    	$id = $this->input->post('id');
    	
    	$data=array('read_unread'=>'1');
        $result = $this->portalmodel->update_query('messages', $data, $id, 'id');
    }
    
	public function viewmessagedetails(){
    	$this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $details = $this->portalmodel->select_where('', '', 'messages', $where);
    	foreach ($details as $key=>$values){
        	$details[$key]->from_email = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->created_by, 'user');
        }
        $data['details'] = $details;
        $this->load->view('viewmessagedetails', $data);
        $this->load->view('templates/footer');
    }
    
	public function replyTo(){
    	$this->common();
        
    	$id = $this->input->get('id');
    	
    	$data['reply_to'] = $this->portalmodel->select_name('messages', 'created_by', "id='".$id."'");
    	$subject = $this->portalmodel->select_name('messages', 'subject', "id='".$id."'");
    	$data['subject'] = "Re: ".$subject;
    	
    	$accountmanager_id = $data['reply_to'];
        if(!empty($accountmanager_id) && $accountmanager_id!='N/A'){
        	$accountmanager_email = $this->portalmodel->select_name('user', 'email', " id='$accountmanager_id'");
        	$accountmanager_name = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) AS name", 'id', $accountmanager_id, 'user');
        }
        
        $data['accountmanager_id'] = $accountmanager_id;
        $data['accountmanager_email'] = $accountmanager_email;
        $data['accountmanager_name'] = $accountmanager_name;
    	
    	$this->load->view('addmessage',$data);
        $this->load->view('templates/footer');
    }
    
	public function sentmessages(){
    	$this->common();
        $id = $this->session->userdata('id');
        
        $where = array();
        $where = " created_by='".$id."' AND from_type='C'";
        
		$details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        
        foreach ($details as $key=>$values){
        	$details[$key]->to_name = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->to, 'user');
        }
        
        $data['details'] = $details;
        $this->load->view('sentmessages', $data);
        $this->load->view('templates/footer');
    }
    
    public function viewsentmessagedetails(){
    	
    	$this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $details = $this->portalmodel->select_where('', '', 'messages', $where);
    	foreach ($details as $key=>$values){
        	if($values->to_type=='C'){
        		$details[$key]->to_name = $this->portalmodel->select_username("companyname as name", 'customerid', $values->to, 'customer');
        	}else{
        		$details[$key]->to_name = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->to, 'user');
        	}
        }
        $data['details'] = $details;
        $this->load->view('viewsentmessagedetails', $data);
        $this->load->view('templates/footer');
    
    }
    
    public function paymenthistory(){
		$this->common();
		
		$id = $this->session->userdata('id');
        
		$where_company = " customerid='$id'";
        $company_id = $this->portalmodel->select_name('customer', 'fk_company_id', $where_company);

		$where = "fk_invoice_id IN (SELECT id FROM invoices WHERE fk_client_id='".$company_id."')";
    	$data['fk_invoice_id'] = (isset($_GET['fk_invoice_id']))?$_GET['fk_invoice_id']:"";
        if(isset($data['fk_invoice_id']) && !empty($data['fk_invoice_id'])){
        	$where .= " AND fk_invoice_id=".$data['fk_invoice_id'];
        }
        
        $list  = $this->portalmodel->select_where_cond('', '', 'invoice_paid', $where,"paid_date","DESC");
        
        $li = array();
        if (!empty($list)) {
            foreach ($list as $d) {
                $row['id'] = $d->id;
                
                $condition_invoice_number = "id=".$d->fk_invoice_id;
                $row['invoice_number'] = $this->portalmodel->select_name('invoices', 'invoice_number', $condition_invoice_number);
                
                $row['task'] = $d->task;
                $row['paid_date'] = date("d M, Y",strtotime($d->paid_date));
                
                $row['amount'] = $d->amount;
                
                $li[] = $row;
            }
        }
        
        $data['list'] = $li;
        
        // get list of invoice numbers
        $where_invoices = "fk_client_id='".$company_id."'";
        $data['invoices'] = $this->portalmodel->get_dropdownuser_list('invoices', 'id', "invoice_number AS name", $where_invoices, "invoice_number","ASC");
		
        $this->load->view('paymenthistory', $data);
        $this->load->view('templates/footer');
    }
}