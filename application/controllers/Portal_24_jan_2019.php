<?php
class Portal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('javascript');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library("pagination");
        $this->load->library('calendar');
        //  $this->load->library('email');
        date_default_timezone_set("America/Toronto");
        if (!ini_get('date.timezone')) {
            date_default_timezone_set("America/Toronto");
        }
    }
    public function common()
    {
        date_default_timezone_set("America/Toronto");
        if (!(isset($this->session->userdata['sid']))) {
            redirect('portal/logout');
        } else {
            $this->load->model('portalmodel');
            $uid        = $this->session->userdata('sid');
			 $urole=$this->session->userdata('srole');
			if($urole==1){
				  $where      = array();
			}else{
            	$where      = " assigned_to='$uid' ";
            	
            	// also get tasks for only assigned projects
            	//$where.=" OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer ))) ";
            	$where.=" OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ))) ";
			}
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
            
            // get countofprojects
            $condition_count_of_projects = array();
	        if($urole!=1){
				$condition_count_of_projects = "(accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer )";
			}
            $countofprojects = $this->portalmodel->select_name("project","COUNT(id)",$condition_count_of_projects);
        	if(!empty($countofprojects) && $countofprojects!="N/A"){
        		$data['countofprojects']=$countofprojects;
        	}else{
        		$data['countofprojects']=0;
        	}
        	
        	// get countofworkorders
        	$condition_count_of_workorders = "status!=4";
			
        	$internal_user_external_user = $this->session->userdata('internal_user_external_user');
	        if($internal_user_external_user!=1){
	    		// count only assigned
	    		$condition_count_of_workorders.=" and fk_assigned_to = '".$uid."'";
	    	}else if($internal_user_external_user==1 && $urole==2){
	    		// internal user and not admin, then display only created by that user
	    		$condition_count_of_workorders.=" and created_by = '".$uid."'";
	    	}
        	
	        $countofworkorders = $this->portalmodel->select_name("workorder","COUNT(id_workorder)",$condition_count_of_workorders);
        	if(!empty($countofworkorders) && $countofworkorders!="N/A"){
        		$data['countofworkorders']=$countofworkorders;
        	}else{
        		$data['countofworkorders']=0;
        	}
        	
            $data['countoftask'] = $count;
            $data['open']=$open;
            $data['logged_in_username']= $this->session->userdata('first_name');
            $data['profile_picture']= $this->session->userdata('profile_picture');
            $data['logged_in_userid'] = $uid;
            $data['current_number_of_comments'] = $this->session->userdata('current_number_of_comments');
            $data['current_number_of_workorder_comments'] = $this->session->userdata('current_number_of_workorder_comments');
            $data['internal_user_external_user'] = $this->session->userdata('internal_user_external_user');
            $data['customer_or_employee_id'] = $this->session->userdata('customer_or_employee_id');
            $data['urole'] = $urole;
            
            $chat_project_list = array();
            
        	// now get project names for chat with the current selected project
        	$current_project_id = $_REQUEST['project_id'];
	        if ($urole == 1) {
	            $list = $this->portalmodel->select('', '', 'project','id="'.$current_project_id.'"','id','DESC');
	        } else {
	            $where = "((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer ) ";
	            
	            // for newly added members condition
	            $where.=" OR id IN (SELECT fk_project_id FROM chat_project_members WHERE fk_user_id='".$uid."') )";
	            
	            // add condition for current project id
	            $where.=' AND id="'.$current_project_id.'"';
	            
	            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"id","DESC");
	        }
	        
	        if (!empty($list)) {
	        	foreach ($list as $d) {
	        		$row = array();
	        		
	        		$row['project_id'] = $d->id;
	        		$row['project_name'] = $d->title;
	        		
	        		$chat_project_list[] = $row;
	        	}
	        }
            
        	// now get project names for chat without the current selected project
	        if ($urole == 1) {
	            $list = $this->portalmodel->select('', '', 'project','id!="'.$current_project_id.'"','id','DESC');
	        } else {
	            $where = "((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer ) ";
	            
	            // for newly added members condition
	            $where.=" OR id IN (SELECT fk_project_id FROM chat_project_members WHERE fk_user_id='".$uid."'))";
	            
	            // add condition for current project id
	            $where.=' AND id!="'.$current_project_id.'"';
	            
	            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"id","DESC");
	        }
            
	        if (!empty($list)) {
	        	foreach ($list as $d) {
	        		$row = array();
	        		
	        		$row['project_id'] = $d->id;
	        		$row['project_name'] = $d->title;
	        		
	        		// also get the number of new chats if any for the projects
	        		$num_chat_notification=0;
	        		$last_chat_id = $this->portalmodel->select_name("last_chat_project","fk_chat_id","fk_project_id='".$d->id."' AND fk_user_id='".$uid."'");
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
	        
	        $data['chat_project_list'] = $chat_project_list;
	        /* end getting projects for chat */
	        
	        // get notifications for ticket/task updates
			$customer_or_employee_id = $this->session->userdata('customer_or_employee_id');
	        $ticket_changes_notification = $this->portalmodel->select_name("task_notifications","COUNT(id)","fk_customer_or_employee_id='".$customer_or_employee_id."' AND read_unread=0 AND user_type='E'");
	        if(!empty($ticket_changes_notification) && $ticket_changes_notification!='N/A'){
	        	$data['ticket_changes_notification']=$ticket_changes_notification;
	        }else{
	        	$data['ticket_changes_notification']=0;
	        }
            
            $this->load->view('templates/staffheader', $data);
        }
    }
    public function index()
    {
        $this->login();
    }
    function login($try = NULL)
    {
        $this->load->view('staff/index');
    }
    public function user_login()
    {
        $this->load->model("portalmodel");
        $result = $this->portalmodel->login_staff();
        //call function in model to check user is valid or not
        $this->session->userdata('sid');
        if ($result == 'fail') {
            $this->session->set_flashdata('error_message', 'Please enter valid Email or Password!'); //display the flashdata using session
            //redirect('portal/index'); //user is not valid so goto login page again                  
            redirect('/'); //user is not valid so goto login page again
        } else {
            redirect('portal/dashboard'); //user is valid and role=0 so goto dashboard
        }
    }
    public function logout()
    {
    	
    	// delete this user from onlinemembers table.
    	$this->load->model("portalmodel");
        $uid = $this->session->userdata('sid');
        $this->portalmodel->deleteid("onlinemembers","user_id",$uid);
    	
        $this->session->unset_userdata('sname');
        $this->session->unset_userdata('sid');
        $this->session->unset_userdata('suser_email');
        $this->session->unset_userdata('srole');
        $this->session->unset_userdata('current_assigned_projects');
        $this->session->unset_userdata('current_number_of_comments');
        $this->session->unset_userdata('current_number_of_workorder_comments');
        $this->session->unset_userdata('last_comment_id');
        //$this->session->sess_destroy();
        $this->session->set_flashdata("logoutmsg", "You Have Been Successfully Logged Out.");
        //redirect("portal/index");
        redirect("/");
    }
    public function email($to, $subject, $message)
    {
        $config['wordwrap'] = TRUE;
        $config['newline']  = '\n';
        $config['mailtype'] = 'html';
        
        /*$config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "kedarsynergyit@gmail.com";
        $config['smtp_pass'] = "India@2018";*/
        
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
        //echo "<pre>"; print_r($_SESSION); exit;
        $id       = $this->session->userdata('sid');
        $data['loggedinuserid'] = $id;
    	$userrole = $this->session->userdata('srole');
        if($userrole==1){
        	// if admin, then display all projects
        	$where1 = array();
        }else{
        	// specific to that person
        	$where1   = "(accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ) or FIND_IN_SET( $id, developer ) OR id IN(SELECT projectid FROM task WHERE assigned_to='".$id."')";
        }
        $taskcomm = $this->portalmodel->select_where('', '', 'project', $where1);
        $prj      = "";
        if (!empty($taskcomm)) {
            foreach ($taskcomm as $t) {
                $prj = $prj . ',' . $t->id;
            }
       
        $prj= substr($prj, 1);
        $cond1    = "project_id in ($prj) ";
        $comments = $this->portalmodel->select_where_cond('15', '0', 'taskcomments', $cond1, 'id', 'DESC');
		 }
        $li       = array();
        if (!empty($comments)) {
            foreach ($comments as $d) {
                $l['comments'] = $d->comments;
                $l['chat_id'] = $d->id;
                $l['taskid']   = $this->portalmodel->select_username('taskid as name', 'id', $d->taskid, 'task');
                $l['task_id'] = $d->taskid;
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
                //$where5          = "comment_id=$d->id";
                $li[]            = $l;
            }
        }
        $data['taskcomm'] = $li;
        
        // now get news
        $current_date = date("Y-m-d");
        $condition_news    = "show_user=1 AND from<='".$current_date."' AND to>='".$current_date."'";
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
        
        // NOW GET WORKORDER COMMENTS
        $condition_workorder_comments = array();
        
        $internal_user_external_user=$this->session->userdata('internal_user_external_user');
        $sessionrole=$this->session->userdata('srole');
        
        if($internal_user_external_user==2){
        	// only assigned
        	$condition_workorder_comments = "fk_workorder_id IN (SELECT id_workorder FROM workorder WHERE fk_assigned_to='".$id."')";
        }else if($internal_user_external_user==1 && $sessionrole==2){
        	// internal user and not admin, then display only created by that user
        	$condition_workorder_comments = "fk_workorder_id IN (SELECT id_workorder FROM workorder WHERE created_by='".$id."')";
        }
        
        $wo_comments = $this->portalmodel->select_where_cond('15', '0', 'workordercomments', $condition_workorder_comments, 'id', 'DESC');
        $workorder_comments = array();
        if (!empty($wo_comments)) {
        	$l = array();
            foreach ($wo_comments as $d) {
                $l['comments'] = $d->comments;
                $l['fk_workorder_id'] = $d->fk_workorder_id;
				$l['commentedby']=$d->commented_by;
                if ($d->commented_by == 1) {
                    $name = "CONCAT(first_name,' ',last_name) as name";
                    $l['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                } else {
                    $name = "CONCAT(first_name,' ',last_name) as name";
                    $l['commented_by'] = $this->portalmodel->select_username($name, 'customerid', $d->created_by, 'customer');
                }
                $l['created_on'] = $d->created_on;
                
                // now get customer name and workorder number
                $l['customer_name'] = $this->portalmodel->select_name('workorder', "customer_name", 'id_workorder='.$d->fk_workorder_id);
                $l['workorder_number'] = $this->portalmodel->select_name('workorder', "workorder_number", 'id_workorder='.$d->fk_workorder_id);
                
                $workorder_comments[]            = $l;
            }
        }
        $data['workorder_comments'] = $workorder_comments;
        
        // get total profiles from users
        $total_profiles = 0;
    	// from user table
        $this->db->select("count(id) AS total_profiles");
        $this->db->from("user");
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$total_profiles = intval($total_profiles) + intval($row[0]->total_profiles);
        }
        
    	// from customer table
        $this->db->select("count(customerid) AS total_profiles");
        $this->db->from("customer");
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$total_profiles = intval($total_profiles) + intval($row[0]->total_profiles);
        }
        $data['total_profiles'] = $total_profiles;
        // get last comment id
        /*if(!isset($_SESSION['last_comment_id'])){
        	$data_session = $_SESSION;
        	$current_number_of_comments = $this->session->userdata('current_number_of_comments');
    		$last_comment_id = $this->portalmodel->select($current_number_of_comments,0,"taskcomments","","id","DESC");
	        $data_session['last_comment_id'] = $last_comment_id[0]->id;
	        $this->session->set_userdata($data_session);
        }*/
        $data['last_comment_id'] = $this->session->userdata('last_comment_id');
        
        $data['new_messages'] = $this->portalmodel->select_name('messages', 'count(id)', "to='".$id."' AND read_unread=0 AND to_type='U'");
        
        //echo "<pre>"; print_r($data); exit;
        $this->load->view('staff/dashboard', $data);
    }
    function projects()
    {
        $this->common();
        $id          = $this->session->userdata('sid');
        $sessionrole = $this->session->userdata('srole');
        if ($sessionrole == 1) {
            $list = $this->portalmodel->select('', '', 'project','','id','DESC');
        } else {
            $where = "(accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ) or FIND_IN_SET( $id, developer ) ";
            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"id","DESC");
        }
        $li = array();
        if (!empty($list)) {
            foreach ($list as $d) {
                $row['id']         = $d->id;
                $coust              = "customerid=".$d->customerid;
    
                $row['customer']  = $this->portalmodel->select_name('customer', 'companyname', $coust);
                $row['title']      = $d->title;
                $row['details']    = $d->details;
                $row['created_by'] = date('Y-m-d', strtotime($d->created_on));
                if ($d->end_date == "" || $d->end_date == '0000-00-00') {
                    $row['end_date'] = "<b>--</b>";
                } else {
                    $row['end_date'] = date('Y-m-d', strtotime($d->end_date));
                }
                $where1          = "projectid=$d->id ";
                $row['nooftask'] = $this->portalmodel->record_count_where('task', $where1);
                $status          = $d->status;
                $role            = "";
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
                $r         = "";
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
            	
                if ($status == 1) {
                    $row['status'] = 'Open';
                }
            	if ($status == 2) {
                    $row['status'] = 'Closed';
                }
                /*if ($status == 0) {
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
                }*/
                $li[] = $row;
            }
        }
        $data['list'] = $li;
        $this->load->view('staff/projects', $data);
        $this->load->view('templates/footer');
    }
    function addproject()
    {
        $this->common();
        $cond              = "CONCAT(first_name,' ',last_name) as name";
        
        // technicians (internal & external users)
        $data['developer'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '','first_name','ASC');
        
        // account manager (internal users)
        $data['account_manager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, 'internal_user_external_user=1','first_name','ASC');
        
        // project manager (internal and external)
        $data['project_manager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '','first_name','ASC');
        
        $data['customer']  = $this->portalmodel->get_dropdown_list('customer', 'customerid', 'companyname', '','companyname','ASC');
        $data['status']    = $this->portalmodel->get_dropdown_list('status_project', 'id', 'status', '');
        $this->load->view('staff/addproject', $data);
        $this->load->view('templates/footer');
    }
    function insert_project()
    {
        $this->load->model("portalmodel");
        $s_id      = $this->session->userdata('sid');
        $datetime  = date('Y-m-d  ', strtotime($this->input->post('dob')));
        $datetime1 = date('Y-m-d  ', strtotime($this->input->post('doi')));
        
        $deveoper  = $this->input->post('developer');
        $deveoper = array_filter($deveoper, function($value){ return $value !== ''; });
        $deveoper = (!empty($deveoper))?implode(",", $deveoper):"";
        
        
        
        /*foreach ($this->input->post('developer') as $dev) {
            $deveoper = $dev . ',' . $deveoper;
        }*/
        $data     = array(
            'customerid' => $this->input->post('customer'),
            'title' => $this->input->post('title'),
            'accountmanager' => $this->input->post('accountmanager'),
            //'projectmanager' => $this->input->post('projectmanager'),
            'developer' => $deveoper,
            'details' => $this->input->post('desc'),
            'created_on' => $datetime,
            'end_date' => $datetime1,
            'status' => $this->input->post('status'),
            'created_by' => $s_id
        );
        $result   = $this->portalmodel->insert_query_('project', $data);
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
            $dir_path                       = './projects/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/projects/' . $filename . $i . '.' . $ext;
            $config                         = array(
                'file_name' => $filename . $i,
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
                $data1              = array(
                    'image_path' => $path,
                    'project_id' => $result,
                    'created_by' => $this->session->userdata('sid')
                );
                $result1            = $this->portalmodel->insert_query_('project_images', $data1);
            endif;
        endfor;
        redirect("portal/projects");
    }
    function projectdetails()
    {
        $this->common();
        $id                = $this->input->get('id');
        $where             = "id=$id";
        $data['details']   = $this->portalmodel->select_where('', '', 'project', $where);
        $coust             = "customerid=" . $data['details'][0]->customerid;
        $data['company']   = $this->portalmodel->select_name('customer', 'companyname', $coust);
        
        $cond              = "CONCAT(first_name,' ',last_name) as name";
        
        // technicians (internal & external users)
        $data['developer'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '','first_name','ASC');
        
        // account manager and project manager(internal users)
        //$data['account_project_manager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, 'internal_user_external_user=1','first_name','ASC');
        
        // account manager (internal users)
        $data['account_manager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, 'internal_user_external_user=1','first_name','ASC');
        
        // project manager (internal and external)
        $data['project_manager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '','first_name','ASC');
        
        $img               = "project_id=" . $data['details'][0]->id;
        $data['images']    = $this->portalmodel->select_where('', '', 'project_images', $img);
        $data['status']    = $this->portalmodel->get_dropdown_list('status_project', 'id', 'status', '');
        $this->load->view('staff/projectdetails', $data);
        $this->load->view('templates/footer');
    }
    function update_project()
    {
        $this->load->model("portalmodel");
        $id        = $this->input->post('id');
        $s_id      = $this->session->userdata('sid');
        $datetime  = date('Y-m-d', strtotime($this->input->post('dob')));
        $datetime1 = date('Y-m-d ', strtotime($this->input->post('doi')));
        /*$deveoper  = '';
        foreach ($this->input->post('developer') as $dev) {
            $deveoper = $dev . ',' . $deveoper;
        }*/
        
        $deveoper  = $this->input->post('developer');
        $deveoper = array_filter($deveoper, function($value){ return $value !== ''; });
        $deveoper = (!empty($deveoper))?implode(",", $deveoper):"";
        
        $data   = array(
            'title' => $this->input->post('title'),
            //'projectmanager' => $this->input->post('projectmanager'),
            'developer' => $deveoper,
            'details' => $this->input->post('desc'),
            'created_on' => $datetime,
            'end_date' => $datetime1,
            'status' => $this->input->post('status'),
            'modified_by' => $s_id
        );
        $result = $this->portalmodel->update_query('project', $data, $id, 'id');
        $this->output->enable_profiler(TRUE);
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
            $dir_path                       = './projects/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/projects/' . $filename . $i . '.' . $ext;
            $config                         = array(
                'file_name' => $filename . $i,
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
                $data1              = array(
                    'image_path' => $path,
                    'project_id' => $id,
                    'created_by' => $this->session->userdata('sid')
                );
                $result1    = $this->portalmodel->insert_query_('project_images', $data1);
            endif;
        endfor;
        redirect("portal/projects");
    }
    function task()
    {
        $this->common();
        $id               = $this->input->get('id');
        $where            = "projectid=$id ";
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
                $row['show_customer'] = $d->show_customer;
                $li[]                 = $row;
            }
        }
        $data['list'] = $li;
        $this->load->view('staff/tasks', $data);
        $this->load->view('templates/footer');
    }
	function alltask()
    {
        $this->common();
       $uid        = $this->session->userdata('sid');
	    $urole      = $this->session->userdata('srole');
	   $cond="";
		$ticket=$this->input->get('ticket');
		
	   if($ticket=="open")
		{
			$cond=" and status in(0,1,2,3)";
		}
		
		// when clicked on the status from notification bar
		$status = $this->input->get('status');
		if(isset($status)){
			$cond=" and status='".$status."'";
		}
		
		if($urole==1){
			if(!empty($cond)){
				$where = '1'.$cond;
			}else{
				$where = array();
			}
			/*if($ticket=="open"){
				$where      = '1'.$cond;
			}else{
				$where=array();
			}
			
			if(isset($status)){
				$where      = '1'.$cond;
			}else{
				$where=array();
			}*/
		}
		else { 
			// also get tasks for only assigned projects
            //$condition_assigned_projects=" OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer ))) ";
            $condition_assigned_projects="(assigned_to='".$uid."' OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager )))) ";
            $where      = $condition_assigned_projects.$cond;
			/*if($ticket=="open"){
				$where      = " assigned_to='".$uid."'".$condition_assigned_projects.$cond;
			}else{
				$where      = " assigned_to='".$uid."'".$condition_assigned_projects.$cond;
			}*/
		}
        $list             = $this->portalmodel->select_where_cond('', '', 'task', $where,"id","desc");
        

        $data['nooftask'] = $this->portalmodel->record_count_where('task', $where);
        $li               = array();
        $li2 = array();
        if (!empty($list)) {
            foreach ($list as $d) {
		$where1           = "id=".$d->projectid;
		$prj = $this->portalmodel->select_where('','','project',  $where1);
		$row['prjname'] =(isset($prj[0]->title))?$prj[0]->title:"";
		$row['prjid'] =(isset($prj[0]->id))?$prj[0]->id:0;
		$row['accountmanager'] = (isset($prj[0]->accountmanager))?$prj[0]->accountmanager:0;
		$row['projectmanager'] = (isset($prj[0]->projectmanager))?$prj[0]->projectmanager:0;
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
                $row['show_customer'] = $d->show_customer;
                $li[]                 = $row;
            }
        }else{
        	// get assigned project
        	$where=" 1 AND ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer )) ";
        	$prj = $this->portalmodel->select_where('','','project',  $where);
        	
			$row['prjname'] =(isset($prj[0]->title))?$prj[0]->title:"";
			$row['prjid'] =(isset($prj[0]->id))?$prj[0]->id:0;
			$row['accountmanager'] = (isset($prj[0]->accountmanager))?$prj[0]->accountmanager:0;
			$row['projectmanager'] = (isset($prj[0]->projectmanager))?$prj[0]->projectmanager:0;
			$li2[] = $row;
        }
        $data['list'] = $li;
        $data['list2'] = $li2;
        $this->load->view('staff/alltask', $data);
        $this->load->view('templates/footer');
    }
    function taskdetails()
    {
        $this->common();
        $id                 = $this->input->get('id');
        $where              = "id=$id";
        $list               = $this->portalmodel->select_where('', '', 'task', $where);
        $where1             = "projectid=" . $list[0]->projectid . " and show_customer=0";
        $where6             = "id=" . $list[0]->status;
        $data['statuss']    = $this->portalmodel->select_name('status_project', 'status', $where6);
        $where7             = "id=" . $list[0]->priority;
        $data['prioritys']  = $this->portalmodel->select_name('taskpriority', 'priority', $where7);
        $data['status']     = $this->portalmodel->get_dropdown_list('status_project', 'id', 'status', '');
        $data['priority']   = $this->portalmodel->get_dropdown_list('taskpriority', 'id', 'priority', '');
        $name               = "CONCAT(first_name,' ',last_name) as name";
        $prjwhere           = "id=" . $list[0]->projectid;
        $prj                = $this->portalmodel->select_where('', '', 'project', $prjwhere);
        
        if(!empty($prj[0]->developer)){
        	$where8 = "id in (" . $prj[0]->developer . ")";
        }else{
        	$where8 = "";
        }
        
        if(isset($list[0]->assigned_to) && !empty($list[0]->assigned_to)){
        	$where8.=" OR id in (".$list[0]->assigned_to.")";
        }
        $data['assignedto'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $name, $where8);
        //$data['assignedto'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $name, '', 'first_name',"ASC");
        if ($list[0]->c_by == 1) {
            $data['created_by'] = $this->portalmodel->select_username($name, 'id', $list[0]->created_by, 'user');
        } else {
            $data['created_by'] = $this->portalmodel->select_username($name, 'customerid', $list[0]->created_by, 'customer');
        }
        $data['assigned_to'] = $this->portalmodel->select_username($name, 'id', $list[0]->assigned_to, 'user');
        //$where2="id=".$list[0]->projectid;
        $data['prjid']       = $list[0]->projectid;
		$data['accountmanager']    = $prj[0]->accountmanager;
		$data['projectmanager']    = $prj[0]->projectmanager;
        $data['prjname']     = $prj[0]->title; //$this->portalmodel->select_name('project','title',$where2);
        $data['nooftask']    = $this->portalmodel->record_count_where('task', $where1);
        $data['task']        = $list;
        $where3              = "task_id=$id ";
        $data['taskimages']  = $this->portalmodel->select_where('', '', 'task_images', $where3);
        $where4              = "taskid=$id ";
        $comments            = $this->portalmodel->select_where_cond('', '', 'taskcomments', $where4,"created_on","DESC");
        $li                  = array();
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
        
        // wo files
        $where              = "fk_task_id=$id";
        $list_wo_files      = $this->portalmodel->select_where('', '', 'task_files', $where);
        $data['list_wo_files'] = $list_wo_files;
        
        $this->load->view('staff/taskdetails', $data);
        $this->load->view('templates/footer');
    }
    function taskdetailsajax()
    {
        $this->load->model("portalmodel");
        $id     = $this->input->post('id');
        $taskid = $this->input->post('taskid');
        $prjid  = $this->input->post('prjid');
        $show   = $this->input->post('show');
		$accountmanager=$this->input->post('accountmanager');
		$projectmanager=$this->input->post('projectmanager');
        $where  = "taskid=$id ";
        $list   = $this->portalmodel->select_where_cond('', '', 'taskcomments', $where,"created_on","DESC");
        $li     = '<div class="text-right"><span class="badge bg-info">  <i class="fa fa fa-hand-o-right"></i></span>                      	
            <a href ="taskdetails?id=' . $id . '" >(' . $taskid . ') More Details...</a></div>';
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
                $datetime = date('Y-m-d  ', strtotime($d->created_on));
                $li .= ' <div class="desc">                
                      	<div class="thumb">
                      		<span class="badge bg-theme"><i class="fa fa-comments"></i></span>
                      	</div>                     
                      	<div class="details">' . $d->comments . ' <br/>  
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
							<input type="hidden" value="' . $show . '"  id="shows" name="shows">
                            <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>                           
                            <label class="textarea-character-limit pull-right"><span id="txtarea_character_limit">300</span> characters left</label><br />
                            <input type="hidden" value="300" id="txt_character_limit_hidden">
							<div class="form-group">
                                            <label style="margin-left: 15px;">File input</label>
                                            <div  id="addfiles">
											<div class="ml-15" id="fileadd0">
											<input type="file" name="files[]" id="upload" class="pull-left">
	                                            <button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button> 
	                                            <br />
	                                        </div>
	                                        </div>
                                        </div>
                            
                                        
                            <input type="hidden" name="num" id="num" value="1" >
                            <div class="pull-left"><button type="button" class="btn btn-primary btn-xs btn-block" onclick="addMoreFiles()">Add More</button></div><br /><br />';
        if ($show == "0") {
            $li .= '<div class="pull-left"><label>Show Customer</label>&nbsp;
                            <input type="radio" name="custshow" id="cust_show" value="0" checked="checked" >Yes &nbsp;
                            <input type="radio" name="custshow" id="cust_show" value="1" >No</div>';
        }else{
        	$li .= '<input type="hidden" name="custshow" id="cust_show" value="1" >';	// defualting a comment to not show, as it gives error when the task is set to not show to customer
        }
        $li .= '  <br /><br /><div class="pull-left">
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
        $id                 = $this->input->get('id');
        $s_id               = $this->session->userdata('sid'); //session ID
        $sessionrole = $this->session->userdata('srole');
		$data['internal_user_external_user'] = $this->session->userdata('internal_user_external_user');
		if(!empty($id)){
			$where              = "projectid=$id ";
	        $where1             = "id=$id";
	        $prj                = $this->portalmodel->select_where('', '', 'project', $where1);
	        $data['prjname']    = $prj[0]->title;
	        $data['customerid'] = $prj[0]->customerid;
			//$data['accountmanager']    = $prj[0]->accountmanager;
			//$data['projectmanager']    = $prj[0]->projectmanager;
			$data['nooftask']   = $this->portalmodel->record_count_where('task', $where);
			$dev                = $prj[0]->developer;
			$where2             = "id in ($dev)";
	        $cond               = "CONCAT(first_name,' ',last_name) as name";
	        $data['developer']  = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, $where2);
		}else{
			$cond               = "CONCAT(first_name,' ',last_name) as name";
			$where2 = array();
			if($sessionrole!=1){
				$where2 = "id=$s_id";
			}
	        $data['developer']  = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, $where2);
		}
        
        // get project list
        if($sessionrole==1){
        	$where2           = array();
        }else{
        	$where2           = "(accountmanager=" . $s_id . ") or FIND_IN_SET( $s_id, projectmanager ) or FIND_IN_SET( $s_id, developer )";
        }
        $data['project']  = $this->portalmodel->get_dropdown_list('project', 'id', 'title', $where2);
        
        $data['status']     = $this->portalmodel->get_dropdown_list('status_project', 'id', 'status', '');
        $data['priority']   = $this->portalmodel->get_dropdown_list('taskpriority', 'id', 'priority', '');
        
        $data['get_project_id'] = $id;
        
        $this->load->view('staff/addtask', $data);
        $this->load->view('templates/footer');
    }
    function insert_task()
    {
        $this->load->model("portalmodel");
        $prjid     = $this->input->post('projectid');
        $s_id      = $this->session->userdata('sid');
        $id        = $this->portalmodel->maxid('task', 'id');
        $datetime  = date('Y-m-d  ', strtotime($this->input->post('dob')));
        $datetime1 = date('Y-m-d ', strtotime($this->input->post('doi')));
        $num       = $id[0]->id + 1;
        $taskid    = 'T00' . $num;
        $customerid = $this->portalmodel->select_name('project', 'customerid', "id='".$prjid."'");
        $data      = array(
            'taskid' => $taskid,
            'projectid' => $prjid,
            'customerid' => $customerid,
            'title' => $this->input->post('title'),
            'priority' => $this->input->post('priority'),
            'description' => $this->input->post('desc'),
            'expected_date' => $datetime,
            'expected_end' => $datetime1,
            'assigned_to' => $this->input->post('developer'),
            'assigned_by' => $s_id,
            'status' => $this->input->post('status'),
            'show_customer' => $this->input->post('cust_show'),
            'c_by' => 1,
            'created_by' => $s_id
        );
        $result    = $this->portalmodel->insert_query_('task', $data);
        $filename  = strtotime("now");
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
                    'created_by' => $this->session->userdata('sid')
                );
                $result1            = $this->portalmodel->insert_query_('task_images', $data1);
            endif;
        endfor;
        $subject = "New Task " . $taskid;
        if ($this->input->post('cust_show') == 0) {
            $where   = 'customerid=' . $this->input->post('customerid');
            $to      = $this->portalmodel->select_name('customer', 'emailid', $where);
            $message = 'Dear Customer,<br/><br/>We have received your support request and assigned it a ticket ID of <b>' . $taskid . '</b> One of our team members will respond to your ticket shortly.<br/>
<br/><br/>
Thank you,<br/>

- Synergy IT Team
';
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
            
            // also send email to business email of customer.
            $to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
        	if ($to_business_email != "") {
                $to_business_email = $this->email($to_business_email, $subject, $message);
            }
            
        }
        $messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b>' . $taskid . '</b> .Please respond to ticket shortly.<br/>
<br/><br/>
Thank you,<br/>

- Synergy IT Team
';
$where1 = "id=$prjid";
$prj = $this->portalmodel->select_where('', '', 'project', $where1);

$devid=$this->input->post('developer');
$accid=$prj[0]->accountmanager;//$this->input->post('accountmanager');
$pmid=$prj[0]->projectmanager;//$this->input->post('projectmanager');

$arrIds = array();
if(isset($devid) && !empty($devid)){
	$arrIds[] = $devid;
}
if(isset($accid) && !empty($accid)){
	$arrIds[] = $accid;
}
if(isset($pmid) && !empty($pmid)){
	$arrIds[] = $pmid;
}

        $where1       = "id IN (".implode(',', $arrIds).")";//'id in ('.$devid.','.$accid.','.$pmid.')';//'id in ('.$accid.','.$pmid.')';
		
        $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if (!empty($tolist)) {
            foreach ($tolist as $d){ 
				//echo $d->email.'<br/>';
	         	$email=$this->email($d->email, $subject, $messagestaff);
	         	
	         	// send to business email
	         	if(isset($d->business_email) && !empty($d->business_email)){
					$email=$this->email($d->business_email, $subject, $messagestaff);
	         	}
            }
		}
     redirect("portal/task?id=" . $prjid);
    }
    function updatetaskdetails()
    {
        $this->load->model("portalmodel");
        $id   = $this->input->post('taskid');
        $s_id = $this->session->userdata('sid');
		$modifiedon=$this->input->post('modifiedon');
		$completedon=$this->input->post('completed');
        if ($this->input->post('expeted_start') == "") {
            $expeted_start = "";
        } else {
            $expeted_start = date('Y-m-d  ', strtotime($this->input->post('expeted_start')));
        }
        if ($this->input->post('expected_end') == "") {
            $expected_end = "";
        } else {
            $expected_end = date('Y-m-d ', strtotime($this->input->post('expected_end')));
        }
		 if ($this->input->post('startdate') == "") {
            $startdate = "";
        } else {
            $startdate = date('Y-m-d ', strtotime($this->input->post('startdate')));
        }
		 if ( ($this->input->post('status')=='4')&&(($completedon=='')||($completedon=='0000-00-00 00:00:00'))) {
          $completedon=date('Y-m-d H:i:s');
        }
		 else if ( $this->input->post('status')!='4')
		 {
			$completedon="0000-00-00 00:00:00" ;
		 }
		else{			
			$completedon = date('Y-m-d ', strtotime($this->input->post('completed')));
		}

		
		
		if(($modifiedon=="")||($modifiedon=='0000-00-00 00:00:00'))
		{
			$modifiedon=date('Y-m-d H:i:s');
		}
		
		/**
		 * check with existing data, if any of the following is changed or not. and if changed, then insert into notification table for client, account manager and tech
		 */

			// get details of task before save.
			$old_task_details = $this->portalmodel->select_where('', '', 'task', "id=$id");
			$old_priority = $old_task_details[0]->priority;
			$old_expected_start_date = $old_task_details[0]->expected_date;
			$old_expected_end_date = $old_task_details[0]->expected_end;
			$old_resolution = $old_task_details[0]->resolution;
			
			$what_changed = array();
			
			// check priority
			$new_priority = $this->input->post('priority');
			if((empty($old_priority) && !empty($new_priority)) || (!empty($old_priority) && empty($new_priority)) || ($old_priority!=$new_priority)){
				$what_changed[]="Priority";
			}
			
			// check expected start date
			if(strtotime($old_expected_start_date)!=strtotime($expeted_start)){
				$what_changed[]="Expected Start Date";
			}
			
			// check expected end date
			if(strtotime($old_expected_end_date)!=strtotime($expected_end)){
				$what_changed[]="Expected End Date";
			}
			
			// check resolution
			$new_resolution = $this->input->post('resolution');
			if($old_resolution!=$new_resolution){
				$what_changed[]="Resolution";
			}
		
			// now if anything changed, then add in task_notification table for Tech, Account manager and client.
			$loggedin_user_id = $this->session->userdata('sid');
			if(!empty($what_changed)){
				// prepare email Subject and Body.
				$subject = "Task Updated ".$old_task_details[0]->taskid;
				$body = "Hi,<br />The Task ".$old_task_details[0]->taskid." has been updated.<br /><br />Please find below updates<br />";
				$body .= implode(", ", $what_changed);
				$body .= "<br/><br/>Thank you,<br/>- Synergy IT Team";
				
				// send email to customer.
				$customer_email = $this->portalmodel->select_name('customer', 'emailid', "customerid='".$old_task_details[0]->customerid."'");
				if(!empty($customer_email) && $customer_email!='N/A'){
					$email = $this->email($customer_email, $subject, $body);
					
					$customer_business_email = $this->portalmodel->select_name('customer', 'business_email', "customerid='".$old_task_details[0]->customerid."'");
					if(!empty($customer_business_email) && $customer_business_email!='N/A'){
						$email = $this->email($customer_business_email, $subject, $body);
					}
				}
				
				// send email to account manager.
				$accountmanagerid = $this->portalmodel->select_name('customer', 'accountmanagerid', "customerid='".$old_task_details[0]->customerid."'");
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
				$tech_email = $this->portalmodel->select_name("user","email","id='".$this->input->post('assignedto')."'");
				if(!empty($tech_email) && $tech_email!='N/A'){
					$email = $this->email($tech_email, $subject, $body);
				}
				
				$tech_business_email = $this->portalmodel->select_name("user","business_email","id='".$this->input->post('assignedto')."'");
				if(!empty($tech_business_email) && $tech_business_email!='N/A'){
					$email = $this->email($tech_business_email, $subject, $body);
				}
				
				/**
				 * now save to task_notifications for all
				 */ 
				
				$changes = implode(", ", $what_changed)." have been updated";
				
				// save notification for customer
				$customer_varchar_id = $this->portalmodel->select_name('customer', 'username', "customerid='".$old_task_details[0]->customerid."'");
				if(!empty($customer_varchar_id) && $customer_varchar_id!='N/A'){
					$data_task_notification = array(
						"fk_task_id"=>$this->input->post('taskid'),
						"user_type"=>"C",
						"fk_customer_or_employee_id"=>$customer_varchar_id,
						"changes"=>$changes,
						"read_unread"=>0
					);
					
					$insert_for_customer = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);
				}
				
				// save notification for account manager
				if(!empty($accountmanagerid) && $accountmanagerid!='N/A'){
					$accountmanager_varchar_id = $this->portalmodel->select_name('user', 'employeeid', "id='".$accountmanagerid."'");
					if(!empty($accountmanager_varchar_id) && $accountmanager_varchar_id!='N/A'){
						$data_task_notification = array(
							"fk_task_id"=>$this->input->post('taskid'),
							"user_type"=>"E",
							"fk_customer_or_employee_id"=>$accountmanager_varchar_id,
							"changes"=>$changes,
							"read_unread"=>0
						);
						
						$insert_for_am = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);
					}
				}
				
				// save notification for tech
				$tech_varchar_id = $this->portalmodel->select_name("user","employeeid","id='".$this->input->post('assignedto')."'");
				if(!empty($tech_varchar_id) && $tech_varchar_id!='N/A'){
					$data_task_notification = array(
						"fk_task_id"=>$this->input->post('taskid'),
						"user_type"=>"E",
						"fk_customer_or_employee_id"=>$tech_varchar_id,
						"changes"=>$changes,
						"read_unread"=>0
					);
					
					$insert_for_tech = $this->portalmodel->insert_query_('task_notifications', $data_task_notification);
				}
				
			}
			
		/* END checking for ticket notification */
		
		
        $data   = array(
            'priority' => $this->input->post('priority'),
            'start' => $startdate,
            'expected_date' => $expeted_start,
			'expected_end'=> $expected_end,
			'end_date'=>$completedon,
            'status' => $this->input->post('status'),
            'show_customer' => $this->input->post('cust_show'),
            'hours' => $this->input->post('hrspent'),
            'modified_by' => $s_id,
            'assigned_to' => $this->input->post('assignedto'),
			'modified_on'=>$modifiedon,
			'resolution'=>$this->input->post('resolution'),
        );
        $result = $this->portalmodel->update_query('task', $data, $id, 'id');
        
        // also save wo file if selected
        $filename  = strtotime("now");
        $this->load->library('upload');
        //  upload calls to $_FILE
        //for ($i = 0; $i < $number_of_files_uploaded; $i++):
        $i=0;
		$_FILES['userfile']['name']     = $_FILES['wofile']['name'];
		$_FILES['userfile']['type']     = $_FILES['wofile']['type'];
		$_FILES['userfile']['tmp_name'] = $_FILES['wofile']['tmp_name'];
		$_FILES['userfile']['error']    = $_FILES['wofile']['error'];
		$_FILES['userfile']['size']     = $_FILES['wofile']['size'];
		$dir_path                       = './tasks/';
		$ext                            = pathinfo($_FILES['wofile']['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		$path                           = '/tasks/' . $filename . $i . '.' . $ext;
		$config                         = array(
			'file_name' => $filename . $i,
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
			$data1              = array(
				'fk_task_id'=>$id,
				'file_name'=>"",
				'file' => $path,
				'created_on' => date("Y-m-d H:i:s"),
				'created_by' => $this->session->userdata('sid')
			);
			$result1            = $this->portalmodel->insert_query_('task_files', $data1);
		endif;
        //endfor;
        
        // now if status is completed(4) then send customer a notification mail that their ticket has been completed
        $status = $this->input->post('status');
        $cust_show = $this->input->post('cust_show');
        $customerid = $this->portalmodel->select_name("task","customerid","id='".$id."'");
        $taskid = $this->portalmodel->select_name("task","taskid","id='".$id."'");
        
        // send email to assigned tech
        if($status==0){
        $tech_email = $this->portalmodel->select_name("user","email","id='".$this->input->post('assignedto')."'");
        $subjectstaff = "New Task ".$taskid;
        $messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b>' . $taskid . '</b> .Please respond to ticket shortly.<br/>
<br/><br/>
Thank you,<br/>

- Synergy IT Team
';
        	$email = $this->email($tech_email, $subjectstaff, $messagestaff);
        	
        	// also send to tech business email
        	$tech_business_email = $this->portalmodel->select_name("user","business_email","id='".$this->input->post('assignedto')."'");
        	if(!empty($tech_business_email) && $tech_business_email!='N/A'){
        		$email = $this->email($tech_business_email, $subjectstaff, $messagestaff);
        	}
        }
        
        if($status==4 && $this->input->post('cust_show')==0 && !empty($customerid)){
        	// get customer's email id
        	$cust_email = $this->portalmodel->select_name("customer","emailid","customerid='".$customerid."'");
        	if(!empty($cust_email)){
        		$subject = "Task Completed ".$taskid;
	        	$message = 'Dear Customer,<br/><br/>This ticket <b>' . $taskid . '</b> has been completed. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/><br/><br/>Thank you,<br/>- Synergy IT Team';
		        $email = $this->email($cust_email, $subject, $message);
		        
		        // send to customer business email
		        $cust_business_email = $this->portalmodel->select_name("customer","business_email","customerid='".$customerid."'");
		        if(isset($cust_business_email) && !empty($cust_business_email) && $cust_business_email!='N/A'){
		        	$email = $this->email($cust_business_email, $subject, $message);
		        }
        	}
        }
    }
    function addtaskcomments()
    {
        $this->load->model("portalmodel");
        $id   = $this->input->post('id_c');
        $s_id = $this->session->userdata('sid');
        if ($this->input->post('cust_show') == "") {
            $show = 1;
        } else {
            $show = $this->input->post('cust_show');
        }
        $data   = array(
            'taskid' => $id,
            'comments' => $this->input->post('comment'),
            'project_id' => $this->input->post('prjid'),
            'show_customer' => $show,
            'commented_by' => 1,
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
            else:
                $final_files_data[] = $this->upload->data();
                $data1              = array(
                    'image_path' => $path,
                    'comment_id' => $result,
                    'created_by' => $this->session->userdata('sid')
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
        if ($show == 0) {
            $where = 'customerid=' . $task[0]->customerid;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
                
                // send to customer business email
                $to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
                if(!empty($to_business_email) && $to_business_email!='N/A'){
                	$email = $this->email($to_business_email, $subject, $message);
                }
            }
        }
		 $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';

$devid=$task[0]->assigned_to;
$accid=$this->input->post('accountmanager');
$pmid=$this->input->post('projectmanager');
       
				   $arrIn = array();
			if(!empty($devid)){
				$arrIn[] = $devid;
			}
			if(!empty($accid)){
				$arrIn[] = $accid;
			}
			if(!empty($pmid)){
				$arrIn[] = $pmid;
			}

			$where1='1';
			if(!empty($arrIn)){
        	//$where1 = 'id in ('.$devid.','.$accid.','.$pmid.')';
        	$where1.=" AND id IN (".implode(',', $arrIn).")";
	}
		 $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if (!empty($tolist)) {
            foreach ($tolist as $d){ 
				//echo $d->email.'<br/>';
				$email=$this->email($d->email, $subject, $messagestaff);
				
				// also send to business_email
				if(isset($d->business_email) && !empty($d->business_email) && $d->business_email!='N/A'){
					$email=$this->email($d->business_email, $subject, $messagestaff);
				}
            }
		}
       
        redirect("portal/taskdetails?id=" . $id);
    }
    function addtaskcommentsajax()
    {
        $this->load->model("portalmodel");
        $id     = $this->input->post('id_c');
        $s_id   = $this->session->userdata('sid');
		$show=$this->input->post('custshow');
        $data   = array(
            'taskid' => $id,
            'comments' => $this->input->post('comment'),
            'project_id' => $this->input->post('prjid'),
            'show_customer' => $this->input->post('custshow'),
            'commented_by' => 1,
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
            else:
                $final_files_data[] = $this->upload->data();
                $data1              = array(
                    'image_path' => $path,
                    'comment_id' => $result,
                    'created_by' => $this->session->userdata('sid')
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
        if ($show == 0) {
            $where = 'customerid=' . $task[0]->customerid;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
                
                // also send to business email
                $to_business_email = $this->portalmodel->select_name('customer', 'business_email', $where);
                if(!empty($to_business_email) && $to_business_email!="N/A"){
                	$email = $this->email($to_business_email, $subject, $message);
                }
            }
        }
		        $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the SynergyInteract for the details.<a href="http://synergytechportal.com/">http://synergytechportal.com</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';
        
      
$devid=$task[0]->assigned_to;
$accid=$this->input->post('accountmanager');
$pmid=$this->input->post('projectmanager');

		$arrIn = array();
		if(!empty($devid)){
			$arrIn[] = $devid;
		}
    	if(!empty($accid)){
			$arrIn[] = $accid;
		}
    	if(!empty($pmid)){
			$arrIn[] = $pmid;
		}
		 
		$where1       = "id in (".implode(',', $arrIn).")";
		
        $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if(!empty($tolist)){		 foreach ($tolist as $toe) { 
        	$this->email($toe->email, $subject, $messagestaff);
        	
        	// also send to business email
        	if(!empty($toe->business_email) && $toe->business_email!='N/A'){
        		$this->email($toe->business_email, $subject, $messagestaff);
        	}
		 }
		}
        ;
    }
    function customers()
    {
        $this->common();
        $id          = $this->session->userdata('sid');
        $sessionrole = $this->session->userdata('srole');
        if ($sessionrole == 1) {
            $list = $this->portalmodel->select('', '', 'customer','','companyname','ASC');
        } else {
            $where = "accountmanagerid= $id";
            //$list  = $this->portalmodel->select_where('', '', 'customer', $where);
            $list = $this->portalmodel->select('', '', 'customer',$where,'companyname','ASC');
        }
        $li = array();
        if (!empty($list)) {
            foreach ($list as $d) {
                $row['id']          = $d->customerid;
                $row['first_name']  = $d->first_name;
                $row['last_name']   = $d->last_name;
                $row['username']    = $d->username;
                $row['emailid']     = $d->emailid;
                $row['contactno']   = $d->contactno;
                $row['companyname'] = $d->companyname;
                $status             = $d->status;
                
                if ($status == 1) {
                    $row['status'] = 'Active';
                } else if ($status == 2) {
                    $row['status'] = 'In-Active';
                } else if ($status == 3) {
                    $row['status'] = 'Deleted';
                }
                
                $row['created_by']     = date('Y-m-d', strtotime($d->created_on));
                $name                  = "CONCAT(first_name,' ',last_name) as name";
                $row['accountmanager'] = $this->portalmodel->select_username($name, 'id', $d->accountmanagerid, 'user');
                
                // now get project manager id
                $project_manager_id = $this->portalmodel->select_name('project', "projectmanager", 'customerid='.$d->customerid,'1','0');
                if(!empty($project_manager_id)){
                	// get project manager name
                	$name                  = "CONCAT(first_name,' ',last_name) as name";
                	$row['projectmanager'] = $this->portalmodel->select_username($name, 'id', $project_manager_id, 'user');
                }else{
                	$row['projectmanager'] = "";
                }
                
                // now also get associated members list to displa in list customers page
                $connected_customer_user_ids = $this->portalmodel->get_dropdownuser_list_without_blank('customer_user_connection','id','fk_user_id AS name',"fk_customer_id='".$d->customerid."'");
                if(!empty($connected_customer_user_ids)){
                	// get all customer user's names
                	$associated_members = $this->portalmodel->get_dropdownuser_list_without_blank('customer_user_project_chat','id', 'first_name AS name', "id IN (".implode(',', $connected_customer_user_ids).")");
                	if(!empty($associated_members)){
                		$row['associated_members'] = implode(", ", $associated_members);
                	}else{
                		$row['associated_members']="";
                	}
                }else{
                	$row['associated_members']="";
                }
                
                $li[]                  = $row;
            }
        }
        $data['list'] = $li;
        $this->load->view('staff/customer', $data);
        $this->load->view('templates/footer');
    }
    function customerdetails()
    {
        $this->common();
        $id                     = $this->input->get('id');
        $where                  = "customerid=$id";
        $data['details']        = $this->portalmodel->select_where('', '', 'customer', $where);
        $cond                   = "CONCAT(first_name,' ',last_name) as name";
        //$data['accountmanager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '','first_name','ASC');
        $data['accountmanager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, 'internal_user_external_user=1','first_name','ASC');
        // now get customer users (for chat)
        // get this customers project ids
        $arrProjectIds = $this->portalmodel->get_dropdownuser_list_without_blank('project', 'id', 'id as name', 'customerid='.$id);
		
		// now get all customer_user_project_chat, and then we will filter
		$arrCustomerUsers = $this->portalmodel->get_dropdownuser_list_without_blank('customer_user_project_chat', 'id', 'project_ids as name', "");
		
		$arrSelectedUsers = array();
		foreach ($arrProjectIds as $eachProject){
			foreach ($arrCustomerUsers as $user_id=>$associatedProjects){
				$arr_associated_projects = explode(",",$associatedProjects);
				if(in_array($eachProject, $arr_associated_projects)){
					$arrSelectedUsers[$user_id]=$user_id;
				}
			}
		}
		
		$strUsers = implode(',', $arrSelectedUsers);
		
		if(!empty($strUsers)){
			$customer_users = $this->portalmodel->get_dropdownuser_list_without_blank('customer_user_project_chat', 'id', 'first_name as name', "id IN (".$strUsers.")");
		}else{
        	$customer_users=array();
		}
		$data['customer_users']=$customer_users;
        
        // already associated customer users ids
        $already_added_customer_users = $this->portalmodel->get_dropdownuser_list_without_blank('customer_user_connection','id','fk_user_id AS name',"fk_customer_id='".$id."'");
        $data['already_added_customer_users']=(!empty($already_added_customer_users))?$already_added_customer_users:array();
        
        $this->load->view('staff/customerdetails', $data);
        $this->load->view('templates/footer');
    }
    function update_customer()
    {
        $this->load->model("portalmodel");
        $id       = $this->input->post('id');
        $s_id     = $this->session->userdata('sid');
        $password = $this->input->post('newpassword');
        if ($password != "") {
            $p = base64_encode($this->input->post('newpassword'));
        } else {
            $p = $this->input->post('password');
        }
        
    	// now check if the company already exists or not.
        $companyname = $this->input->post('company');
        $existing_id = $this->portalmodel->select_name("customer","customerid","TRIM(LOWER(companyname))='".trim(strtolower($companyname))."' AND customerid!='".$id."'");
        
        if(!empty($existing_id) && $existing_id!='N/A'){
        	$this->session->set_flashdata('error_message', "Can not update customer. Company name already exists!"); //display the flashdata using session
        	redirect("portal/customerdetails?id=" . $id);
        	exit;
        }
        
        $data   = array(
            'first_name' => $this->input->post('firstname'),
            'last_name' => $this->input->post('lastname'),
            'emailid' => $this->input->post('emailid'),
        	'business_email' => $this->input->post('business_emailid'),
            'contactno' => $this->input->post('contactno'),
            'modified_by' => $s_id,
            'companyname' => $this->input->post('company'),
            'status' => $this->input->post('status'),
            'accountmanagerid' => $this->input->post('accountmanager'),
            'password' => $p
        );
        $result = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
    	$filename = strtotime("now");
        $this->load->library('upload');
        $path1 = $this->input->post('path');
        if (!empty($_FILES['files']['name'])) {
            if ($path1 != "") {
                unlink($path1);
            }
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
            endif;
        }
        
		$data1=array('accountmanager'=>$this->input->post('accountmanager'));
		$result = $this->portalmodel->update_query('project ', $data1, $id, 'customerid');
		
		/* update associated members start */
		// first remove old connections, for this customer and add new
		$this->portalmodel->deleteid("customer_user_connection","fk_customer_id",$id);		
		$members = $this->input->post('members');
		foreach ($members as $fk_user_id){
			$data_add_connection = array(
				"fk_customer_id"=>$id,
				"fk_user_id"=>$fk_user_id
			);
			$insert_connection = $this->portalmodel->insert_query_("customer_user_connection",$data_add_connection);
		}
		
		// now we also need to add/remove the project access as well for the selected members
		// first we will get associated project ids for this customers
		$arrProjectIds = $this->portalmodel->get_dropdownuser_list_without_blank('project', 'id', 'id as name', 'customerid='.$id);
		
		// now get list of all associated users for above project ids
		$arrAssociatedUsersToProject = array();
		foreach ($arrProjectIds as $eachProjectId){
			$associated_users = $this->portalmodel->select_where('', '', 'customer_user_project_chat', "FIND_IN_SET( $eachProjectId, project_ids )");
			foreach ($associated_users as $each_associated_user){
				$arrAssociatedUsersToProject[$each_associated_user->id]=$each_associated_user->id;
	    	}
		}
		
		// now go through all associated users and check if that user exists in the posted members list.
		// if exists in the posted member list, then do nothing, else remove all associated projects with that user for this customer
		foreach ($arrAssociatedUsersToProject as $eachAssociatedUsersToProject){
			if(!in_array($eachAssociatedUsersToProject, $members)){
				$data = array(
					"project_ids"=>""
				);
				$this->portalmodel->update_query('customer_user_project_chat', $data, $eachAssociatedUsersToProject, 'id');
			}
		}
		/* update associated member ends */
		
		
        redirect("portal/customers");
    }
    function addcustomer()
    {
        $this->common();
        $cond                   = "CONCAT(first_name,' ',last_name) as name";
        $data['accountmanager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, 'internal_user_external_user=1','first_name','ASC');
        
		// get customer users to add connection
		// $customer_users = $this->portalmodel->get_dropdownuser_list_without_blank('customer_user_project_chat', 'id', 'first_name as name', '');
		//$data['customer_users']=$customer_users;
        
        $this->load->view('staff/addcustomer', $data);
        $this->load->view('templates/footer');
    }
    function insert_customer()
    {
        $this->load->model("portalmodel");
        
        // now check if the company already exists or not.
        $companyname = $this->input->post('company');
        $existing_id = $this->portalmodel->select_name("customer","customerid","TRIM(LOWER(companyname))='".trim(strtolower($companyname))."'");
        
        if(!empty($existing_id) && $existing_id!='N/A'){
        	$this->session->set_flashdata('error_message', "Can not add customer. Company name already exists!"); //display the flashdata using session
        	redirect("portal/addcustomer");
        	exit;
        }
        
        $s_id       = $this->session->userdata('sid');
        $id         = $this->portalmodel->maxid('customer', 'customerid');
        $num        = $id[0]->id + 1;
        $customerid = 'C00' . $num;
        $data       = array(
            'username' => $customerid,
            'first_name' => $this->input->post('firstname'),
            'last_name' => $this->input->post('lastname'),
            'companyname' => $this->input->post('company'),
            'emailid' => $this->input->post('emailid'),
        	'business_email' => $this->input->post('business_emailid'),
            'contactno' => $this->input->post('contactno'),
            'password' => base64_encode($this->input->post('newpassword')),
            'created_by' => $s_id,
            'accountmanagerid' => $this->input->post('accountmanager'),
            'status' => $this->input->post('status')
        );
        $result     = $this->portalmodel->insert_query_('customer', $data);
        
        $filename   = strtotime("now");
        $this->load->library('upload');
        $number_of_files_uploaded = count($_FILES['files']['name']);
        //  upload calls to $_FILE
        for ($i = 0; $i < $number_of_files_uploaded; $i++):
            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
            $dir_path                       = './user/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/user/' . $filename . '.' . $ext;
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
                $result             = $this->portalmodel->update_query('customer', $data1, $customerid, 'username');
            endif;
        endfor;
        
        /*if($result!='False'){
	        $members = $this->input->post('members');
			foreach ($members as $fk_user_id){
				$data_add_connection = array(
					"fk_customer_id"=>$result,
					"fk_user_id"=>$fk_user_id
				);
				$insert_connection = $this->portalmodel->insert_query_("customer_user_connection",$data_add_connection);
			}
        }*/
        
        //redirect("portal/customerdetails?id=" . $num);
        redirect("portal/customers");
    }
    function users()
    {
        $this->common();
        $id              = $this->session->userdata('sid');
        
        $condition = "1";
        
		$srole = $this->session->userdata('srole');
		$internal_external = $this->session->userdata('internal_user_external_user');
		
		if($internal_external==1 && $srole==2){
			// if user is internal and not admin, then show only external users
			//$condition.=" AND internal_user_external_user=2";
		}
        
        $active_inactive = (isset($_GET['active_inactive_users']))?$_GET['active_inactive_users']:0;
        if(isset($active_inactive) && !empty($active_inactive)){
        	$condition.=" AND status=".$active_inactive;
        }
        
        if(empty($condition) || $condition=="1"){
        	$condition = array();
        }
        
        $data['details'] = $this->portalmodel->select('', '', 'user', $condition,'first_name','ASC');
        $data['active_inactive']=$active_inactive;
        $this->load->view('staff/users', $data);
        $this->load->view('templates/footer');
    }
    function userdetails()
    {
        $this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'user', $where);
        
        // get user files
        $where_files = "fk_user_id='".$id."'";
        $data['contracts_files'] = $this->portalmodel->select_where('', '', 'user_contracts', $where_files);
        $data['waivers_files'] = $this->portalmodel->select_where('', '', 'user_waivers', $where_files);
        $data['insurance_files'] = $this->portalmodel->select_where('', '', 'user_insurance', $where_files);
        $data['certifications_files'] = $this->portalmodel->select_where('', '', 'user_certifications', $where_files);
        $data['other_files'] = $this->portalmodel->select_where('', '', 'user_other', $where_files);
        
        $this->load->view('staff/userdetails', $data);
        $this->load->view('templates/footer');
    }
    function update_user()
    {
        $this->load->model("portalmodel");
        $id       = $this->input->post('id');
        $s_id     = $this->session->userdata('sid');
        $password = $this->input->post('newpassword');
        if ($password != "") {
            $p = base64_encode($this->input->post('newpassword'));
        } else {
            $p = $this->input->post('password');
        }
        $data     = array(
            'first_name' => $this->input->post('firstname'),
            'last_name' => $this->input->post('lastname'),
            'email' => $this->input->post('emailid'),
            'phone' => $this->input->post('contactno'),
            'created_by' => $s_id,
            'role' => $this->input->post('role'),
            'status' => $this->input->post('status'),
            'internal_user_external_user' => $this->input->post('inexuser'),
            'password' => $p,
        	'username'=>$this->input->post('username')
        );
        $result   = $this->portalmodel->update_query('user', $data, $id, 'id');
        $filename = strtotime("now");
        $this->load->library('upload');
        $path1 = $this->input->post('path');
        if (!empty($_FILES['files']['name'])) {
            if ($path1 != "") {
                unlink($path1);
            }
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
                    'userprofile' => $path
                );
                $result             = $this->portalmodel->update_query('user', $data1, $id, 'id');
            endif;
        }
        redirect("portal/users");
    }
    function adduser()
    {
        $this->common();
        $this->load->view('staff/adduser');
        $this->load->view('templates/footer');
    }
    function insert_user()
    {
		if (!(isset($this->session->userdata['sid']))) {
            redirect('portal/logout');
        }
        $this->load->model("portalmodel");
        
        // first check if the user exists or not, and if not, then only add that user
        $email = $this->input->post('emailid');
        $username = $this->input->post('userid');
        $existing_user_id = $this->portalmodel->select_name('user', 'id', "email='".$email."' OR username='".$username."'");
        
		if(empty($existing_user_id) || $existing_user_id=="N/A"){
			$s_id       = $this->session->userdata('sid');
	        $id         = $this->portalmodel->maxid('user', 'id');
	        $num        = $id[0]->id + 1;
	        $employeeid = 'E00' . $num;
	        $data       = array(
	            'employeeid' => $employeeid,
	            'first_name' => $this->input->post('firstname'),
	            'last_name' => $this->input->post('lastname'),
	            'username' => $this->input->post('userid'),
	            'email' => $this->input->post('emailid'),
	            'phone' => $this->input->post('contactno'),
	            'password' => base64_encode($this->input->post('newpassword')),
	            'created_by' => $s_id,
	            'role' => $this->input->post('role'),
	            'status' => $this->input->post('status'),
	            'internal_user_external_user' => $this->input->post('inexuser')
	        );
	        $result     = $this->portalmodel->insert_query_('user', $data);
	        $filename   = strtotime("now");
	        $this->load->library('upload');
	        $number_of_files_uploaded = count($_FILES['files']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
	            $dir_path                       = './user/';
	            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = '/user/' . $filename . '.' . $ext;
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
	                    'userprofile' => $path
	                );
	                $result             = $this->portalmodel->update_query('user', $data1, $employeeid, 'employeeid');
	            endif;
	        endfor;
		}
        redirect("portal/users");
    }
    function settings()
    {
        $this->common();
        $id              = $this->session->userdata('sid');
        $where           = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'user', $where);
        
        // get contracts for users
        $where_files = "fk_user_id='".$id."'";
        
        $data['contracts_files'] = $this->portalmodel->select_where('', '', 'user_contracts', $where_files);
        $data['waivers_files'] = $this->portalmodel->select_where('', '', 'user_waivers', $where_files);
        $data['insurance_files'] = $this->portalmodel->select_where('', '', 'user_insurance', $where_files);
        $data['certifications_files'] = $this->portalmodel->select_where('', '', 'user_certifications', $where_files);
        $data['other_files'] = $this->portalmodel->select_where('', '', 'user_other', $where_files);
        
        $this->load->view('staff/settings', $data);
        $this->load->view('templates/footer');
    }
    function updatesettings()
    {
    	/*echo "<pre>";
    	print_r($_POST);
    	print_r($_FILES); 
    	exit;*/
    	
        $this->load->model("portalmodel");
        $id       = $this->session->userdata('sid');
        $where    = 'id=' . $id;
        $password = $this->input->post('password');
        if ($password == "") {
            $data   = array(
                'first_name' => $this->input->post('firstname'),
                'last_name' => $this->input->post('lastname'),
                'phone' => $this->input->post('contactno'),
                'email' => $this->input->post('emailid'),
            	'business_email' =>$this->input->post('business_emailid'),
            	'skills' => $this->input->post('skills'),
                'modified_by' => $this->session->userdata('sid')
            );
            $update = $this->portalmodel->update_query('user', $data, $id, 'id');
            $this->session->set_flashdata('success', 'Sucessfully updated');
            
            // change name in session
			$userdata['first_name'] = $this->input->post('firstname');//." ".$this->input->post('lastname');
			$this->session->set_userdata($userdata);
        } else {
            $password      = $this->portalmodel->select_name('user', 'password', $where);
            $givenpassword = base64_encode($this->input->post('password'));
            if ($password == $givenpassword) {
                $newpassword = base64_encode($this->input->post('newpassword'));
                $data        = array(
                    'first_name' => $this->input->post('firstname'),
                    'last_name' => $this->input->post('lastname'),
                    'phone' => $this->input->post('contactno'),
                    'email' => $this->input->post('emailid'),
                	'skills' => $this->input->post('skills'),
                    'password' => $newpassword,
                    'modified_by' => $this->session->userdata('sid')
                );
                $this->session->set_flashdata('success', 'Sucessfully updated');
                $update = $this->portalmodel->update_query('user', $data, $id, 'id');
                
                // change name in session
                $userdata['first_name'] = $this->input->post('firstname');//." ".$this->input->post('lastname');
				$this->session->set_userdata($userdata);
            } else {
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
                    'userprofile' => $path
                );
                $result             = $this->portalmodel->update_query('user', $data1, $id, 'id');
                
                $userdata['profile_picture'] = $path;
				$this->session->set_userdata($userdata);
            endif;
        }
        
        // now save contracts if posted
        if(isset($_POST['num_contracts']) && !empty($_POST['num_contracts'])){
        	// load library
        	$this->load->library('upload');
        	
        	for($i=0;$i<$_POST['num_contracts'];$i++){
        		
        		$file_name = $_POST['files_contracts_name'][$i];
        		$filename = strtotime("now").$i;
        		
        		// now save photo or file
        		if (!empty($_FILES['files_contracts']['name'][$i])) {
		            /* if($path1!=""){
		            unlink($path1); 
		            }*/
		            $_FILES['userfile']['name']     = $_FILES['files_contracts']['name'][$i];
		            $_FILES['userfile']['type']     = $_FILES['files_contracts']['type'][$i];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files_contracts']['tmp_name'][$i];
		            $_FILES['userfile']['error']    = $_FILES['files_contracts']['error'][$i];
		            $_FILES['userfile']['size']     = $_FILES['files_contracts']['size'][$i];
		            $dir_path                       = './user/contracts/';
		            $ext                            = pathinfo($_FILES['files_contracts']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/contracts/' . $filename . '.' . $ext;
		            $config                         = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx|xlsx',
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
		                	'fk_user_id'=>$id,
		                    'file_name' => $file_name,
		                	'file' => $path,
			                'created_by' => $id,
				            'created_on' => date("Y-m-d H:i:s"),
				            'modified_by' => '0',
				            'modified_on' => "0000-00-00 00:00:00"
		                );
		                // insert query
	        			$result = $this->portalmodel->insert_query_('user_contracts', $data1);
		            endif;
		        }
        	}
        }
        
    	// now save waivers if posted
        if(isset($_POST['num_waivers']) && !empty($_POST['num_waivers'])){
        	// load library
        	$this->load->library('upload');
        	
        	for($i=0;$i<$_POST['num_waivers'];$i++){
        		
        		$file_name = $_POST['files_waivers_name'][$i];
        		$filename = strtotime("now").$i;
        		
        		// now save photo or file
        		if (!empty($_FILES['files_waivers']['name'][$i])) {
		            /* if($path1!=""){
		            unlink($path1); 
		            }*/
		            $_FILES['userfile']['name']     = $_FILES['files_waivers']['name'][$i];
		            $_FILES['userfile']['type']     = $_FILES['files_waivers']['type'][$i];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files_waivers']['tmp_name'][$i];
		            $_FILES['userfile']['error']    = $_FILES['files_waivers']['error'][$i];
		            $_FILES['userfile']['size']     = $_FILES['files_waivers']['size'][$i];
		            $dir_path                       = './user/waivers/';
		            $ext                            = pathinfo($_FILES['files_waivers']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/waivers/' . $filename . '.' . $ext;
		            $config                         = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx|xlsx',
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
		                	'fk_user_id'=>$id,
		                    'file_name' => $file_name,
		                	'file' => $path,
			                'created_by' => $id,
				            'created_on' => date("Y-m-d H:i:s"),
				            'modified_by' => '0',
				            'modified_on' => "0000-00-00 00:00:00"
		                );
		                // insert query
	        			$result = $this->portalmodel->insert_query_('user_waivers', $data1);
		            endif;
		        }
        	}
        }
        
    	// now save Insurance if posted
        if(isset($_POST['num_insurance']) && !empty($_POST['num_insurance'])){
        	// load library
        	$this->load->library('upload');
        	
        	for($i=0;$i<$_POST['num_insurance'];$i++){
        		
        		$file_name = $_POST['files_insurance_name'][$i];
        		$filename = strtotime("now").$i;
        		
        		// now save photo or file
        		if (!empty($_FILES['files_insurance']['name'][$i])) {
		            /* if($path1!=""){
		            unlink($path1); 
		            }*/
		            $_FILES['userfile']['name']     = $_FILES['files_insurance']['name'][$i];
		            $_FILES['userfile']['type']     = $_FILES['files_insurance']['type'][$i];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files_insurance']['tmp_name'][$i];
		            $_FILES['userfile']['error']    = $_FILES['files_insurance']['error'][$i];
		            $_FILES['userfile']['size']     = $_FILES['files_insurance']['size'][$i];
		            $dir_path                       = './user/insurance/';
		            $ext                            = pathinfo($_FILES['files_insurance']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/insurance/' . $filename . '.' . $ext;
		            $config                         = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx|xlsx',
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
		                	'fk_user_id'=>$id,
		                    'file_name' => $file_name,
		                	'file' => $path,
			                'created_by' => $id,
				            'created_on' => date("Y-m-d H:i:s"),
				            'modified_by' => '0',
				            'modified_on' => "0000-00-00 00:00:00"
		                );
		                // insert query
	        			$result = $this->portalmodel->insert_query_('user_insurance', $data1);
		            endif;
		        }
        	}
        }
        
    	// now save certifications if posted
        if(isset($_POST['num_certifications']) && !empty($_POST['num_certifications'])){
        	// load library
        	$this->load->library('upload');
        	
        	for($i=0;$i<$_POST['num_certifications'];$i++){
        		
        		$file_name = $_POST['files_certifications_name'][$i];
        		$filename = strtotime("now").$i;
        		
        		// now save photo or file
        		if (!empty($_FILES['files_certifications']['name'][$i])) {
		            /* if($path1!=""){
		            unlink($path1); 
		            }*/
		            $_FILES['userfile']['name']     = $_FILES['files_certifications']['name'][$i];
		            $_FILES['userfile']['type']     = $_FILES['files_certifications']['type'][$i];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files_certifications']['tmp_name'][$i];
		            $_FILES['userfile']['error']    = $_FILES['files_certifications']['error'][$i];
		            $_FILES['userfile']['size']     = $_FILES['files_certifications']['size'][$i];
		            $dir_path                       = './user/certifications/';
		            $ext                            = pathinfo($_FILES['files_certifications']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/certifications/' . $filename . '.' . $ext;
		            $config                         = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx|xlsx',
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
		                	'fk_user_id'=>$id,
		                    'file_name' => $file_name,
		                	'file' => $path,
			                'created_by' => $id,
				            'created_on' => date("Y-m-d H:i:s"),
				            'modified_by' => '0',
				            'modified_on' => "0000-00-00 00:00:00"
		                );
		                // insert query
	        			$result = $this->portalmodel->insert_query_('user_certifications', $data1);
		            endif;
		        }
        	}
        }
        
    	// now save other if posted
        if(isset($_POST['num_other']) && !empty($_POST['num_other'])){
        	// load library
        	$this->load->library('upload');
        	
        	for($i=0;$i<$_POST['num_other'];$i++){
        		
        		$file_name = $_POST['files_other_name'][$i];
        		$filename = strtotime("now").$i;
        		
        		// now save photo or file
        		if (!empty($_FILES['files_other']['name'][$i])) {
		            /* if($path1!=""){
		            unlink($path1); 
		            }*/
		            $_FILES['userfile']['name']     = $_FILES['files_other']['name'][$i];
		            $_FILES['userfile']['type']     = $_FILES['files_other']['type'][$i];
		            $_FILES['userfile']['tmp_name'] = $_FILES['files_other']['tmp_name'][$i];
		            $_FILES['userfile']['error']    = $_FILES['files_other']['error'][$i];
		            $_FILES['userfile']['size']     = $_FILES['files_other']['size'][$i];
		            $dir_path                       = './user/other/';
		            $ext                            = pathinfo($_FILES['files_other']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
		            $path                           = 'user/other/' . $filename . '.' . $ext;
		            $config                         = array(
		                'file_name' => $filename,
		                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx|xlsx',
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
		                	'fk_user_id'=>$id,
		                    'file_name' => $file_name,
		                	'file' => $path,
			                'created_by' => $id,
				            'created_on' => date("Y-m-d H:i:s"),
				            'modified_by' => '0',
				            'modified_on' => "0000-00-00 00:00:00"
		                );
		                // insert query
	        			$result = $this->portalmodel->insert_query_('user_other', $data1);
		            endif;
		        }
        	}
        }
        
        redirect("portal/settings");
    }
    function getaccountmanger()
    {
        $this->load->model("portalmodel");
        $id    = $this->input->post('customer');
        $where = "customerid=$id";
        $cust  = $this->portalmodel->select_where('', '', 'customer', $where);
        $cond  = "CONCAT(first_name,' ',last_name) as name";
        if (!empty($cust)) {
            $where = "id=" . $cust[0]->accountmanagerid;
            $acc   = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, $where);
            echo form_dropdown('accountmanager', $acc, $cust[0]->accountmanagerid, 'class="form-control" id="accountmanager"');
        } else {
            $developer = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '');
            echo form_dropdown('accountmanager', $developer, '', 'class="form-control" id="accountmanager"');
        }
    }
    /**
     * NEWS SECTION RELATED FUNCTIONS
     */
    // LISTING NEWS
    function news()
    {
        $this->common();
        $id              = $this->session->userdata('sid');
        $srole = $this->session->userdata('srole');
        $internal_external = $this->session->userdata('internal_user_external_user');
        $where = array();
        if($internal_external==1 && $srole==2){
        	// if user is internal and not admin, then show only his blogs
        	$where = " created_by='".$id."'";
        }
        $data['details'] = $this->portalmodel->select('', '', 'notification', $where,'created_on','DESC');
        $this->load->view('staff/news', $data);
        $this->load->view('templates/footer');
    }
    // ADD NEWS
    function addnews()
    {
        $this->common();
        $this->load->view('staff/addnews');
        $this->load->view('templates/footer');
    }
    // INSERT NEW NEWS ENTRY IN DATABASE
    function insert_news()
    {
        $this->load->model("portalmodel");
        $s_id           = $this->session->userdata('sid');
        $id             = $this->portalmodel->maxid('notification', 'id');
        $num            = $id[0]->id + 1;
        $news_from_date = date('Y-m-d  ', strtotime($this->input->post('news_from_date')));
        $news_to_date   = date('Y-m-d  ', strtotime($this->input->post('news_to_date')));
        $data           = array(
            'notification' => $this->input->post('news_title'),
            'from' => $news_from_date,
            'to' => $news_to_date,
            'show_customer' => $this->input->post('cust_show'),
            'show_user' => $this->input->post('user_show'),
            'created_by' => $s_id
        );
        $result         = $this->portalmodel->insert_query_('notification', $data);
        $filename       = strtotime("now");
        $this->load->library('upload');
        $number_of_files_uploaded = count($_FILES['files']['name']);
        // upload calls to $_FILE
        for ($i = 0; $i < $number_of_files_uploaded; $i++):
            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
            $dir_path                       = './notification/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/notification/' . $filename . '.' . $ext;
            $config                         = array(
                'file_name' => $filename,
                'allowed_types' => '*',
                'max_size' => 5000,
                'overwrite' => FALSE,
                'upload_path' => $dir_path
            );
            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $error = array(
                    'error' => $this->upload->display_errors()
                );
                //$this->session->set_flashdata('error_message', $this->upload->display_errors()); //display the flashdata using session
            else:
                $final_files_data[] = $this->upload->data();
                $data1              = array(
                    'image' => $path
                );
                $result             = $this->portalmodel->update_query('notification', $data1, $num, 'id');
            endif;
        endfor;
        redirect("portal/news");
    }
    // OPEN NEWS DETAIL PAGE FOR EDIT
    function newsdetails()
    {
        $this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'notification', $where);
        $this->load->view('staff/newsdetails', $data);
        $this->load->view('templates/footer');
    }
    // UPDATE NEWS DETAILS
    function update_news()
    {
        $this->load->model("portalmodel");
        $id             = $this->input->post('id');
        $s_id           = $this->session->userdata('sid');
        $news_from_date = date('Y-m-d  ', strtotime($this->input->post('news_from_date')));
        $news_to_date   = date('Y-m-d  ', strtotime($this->input->post('news_to_date')));
        $data           = array(
            'notification' => $this->input->post('news_title'),
            'from' => $news_from_date,
            'to' => $news_to_date,
            'show_customer' => $this->input->post('cust_show'),
            'show_user' => $this->input->post('user_show'),
            'created_by' => $s_id
        );
        $result         = $this->portalmodel->update_query('notification', $data, $id, 'id');
        $filename       = strtotime("now");
        $this->load->library('upload');
        $path1 = $this->input->post('path');
        if (!empty($_FILES['files']['name'])) {
            if ($path1 != "") {
                unlink($path1);
            }
            $_FILES['userfile']['name']     = $_FILES['files']['name'];
            $_FILES['userfile']['type']     = $_FILES['files']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['files']['error'];
            $_FILES['userfile']['size']     = $_FILES['files']['size'];
            $dir_path                       = './notification/';
            $ext                            = pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = 'notification/' . $filename . '.' . $ext;
            $config                         = array(
                'file_name' => $filename,
                'allowed_types' => '*',
                'max_size' => 5000,
                'overwrite' => FALSE,
                'upload_path' => $dir_path
            );
            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $error = array(
                    'error' => $this->upload->display_errors()
                );
                //$this->session->set_flashdata('error_message', $this->upload->display_errors()); //display the flashdata using session
            else:
                $final_files_data[] = $this->upload->data();
                $data1              = array(
                    'image' => $path
                );
                $result             = $this->portalmodel->update_query('notification', $data1, $id, 'id');
            endif;
        }
        redirect("portal/news");
    }
	function newsdetailsview(){
    	$this->common();
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
        
        $this->load->view('staff/newsdetailsview', $data);
        $this->load->view('templates/footer');
    }
	/**
	 * WORKORDER RELATED FUNCTIONS START
     * Following function will be used to get the request from CRM backend when any workorder is created
     * add a new entry in Portal db when any workorder is created and emailed to any tech
     */
    function syncWorkOrder(){
    	// load the portalmodel to use its functions later in this function
    	$this->load->model('portalmodel');
    	
    	$request_input_stream = @file_get_contents('php://input');
		
    	$data_request = json_decode($request_input_stream, true);
		
    	// now copy the file from another server to this server
    	$fileUrl = $_SERVER['DOCUMENT_ROOT']."/".$data_request['workorder_file'];
    	$newFileName = $data_request['workorder_number'].".pdf";
    	$newFilePath = $_SERVER['DOCUMENT_ROOT']."/workorderfiles/".$newFileName;
    	copy($fileUrl, $newFilePath);
    	$data_request['workorder_file'] = $newFileName;
    	
		// now that we have got data, we can proceed further to add workorder and workorder files
		$workorder_status = $data_request['status'];
		$workorder_number = $data_request['workorder_number'];
		
		// customer data
		$customer_name = $data_request['customer_name'];
		$customer_phone = $data_request['customer_phone'];
		$customer_email = $data_request['customer_email'];
		
		// user data
		$user_name = $data_request['user_name'];
		$user_email = $data_request['user_email'];
		$user_password = $data_request['user_password'];
		$user_phone = $data_request['user_phone'];
		
		// workorder file
		$workorder_file = $data_request['workorder_file'];
		
		// add/get account manager id to attach to customer
		$fk_account_manager_id = "0";
		if(!empty($account_manager_email)){
			$account_manager_email = $data_request['account_manager_email'];
			$account_manager_name = $data_request['account_manager_name'];
			$fk_account_manager_id = $this->portalmodel->select_name('user', 'id', "email='".$account_manager_email."'");
			if(empty($fk_account_manager_id) || $fk_account_manager_id=="N/A"){
				
				// separate first name last name
				$arrName = explode(" ", $account_manager_name);
				$arrUserName = array();
				if(count($arrName)==1){
					$arrUserName['first_name'] = $arrName[0];
					$arrUserName['last_name'] = "";
				}else if(count($arrName)==2){
					$arrUserName['first_name'] = $arrName[0];
					$arrUserName['last_name'] = $arrName[1];
				}else if(count($arrName)==3){
					$arrUserName['first_name'] = $arrName[0]." ".$arrName[1];
					$arrUserName['last_name'] = $arrName[2];
				}
				
				// add new user and get its id
				$id = $this->portalmodel->maxid('user', 'id');
		        $num = $id[0]->id + 1;
		        $employeeid = 'E00' . $num;
		        $data_am       = array(
		            'employeeid' => $employeeid,
		            'first_name' => $arrUserName['first_name'],
		            'last_name' => $arrUserName['last_name'],
		            'username' => $account_manager_email,		// keep email id as their usernam/login id
		            'email' => $account_manager_email,
		            'phone' => '',
		            'password' => base64_encode("user@123"),
		            'created_by' => 1001,	// admin created this user
		            'role' => 2,
		            'status' => 1,
		            'internal_user_external_user' => 2	// will be external user by default. if new created
		        );
		        
		        // insert query
		        $result = $this->portalmodel->insert_query_('user', $data_am);
		        
		        $fk_account_manager_id = $num;
			}
		}
		
		/* first get cutomer id for the customer name, if exists, otherwise add new customer and get its id */
		$fk_customer_id = "0";
		/*if(!empty($customer_email)){
			$fk_customer_id = $this->portalmodel->select_name('customer', 'customerid', "companyname='".$customer_name."'");
			if(empty($fk_customer_id) || $fk_customer_id=="N/A"){
				// add new record for customer
				$id = $this->portalmodel->maxid('customer', 'customerid');
		        $num = $id[0]->id + 1;
		        $customerid = 'C00' . $num;
		        $data_customer = array(
		            'username' => $customerid,
		            'first_name' => '',
		            'last_name' => '',
		            'companyname' => $customer_name,
		            'emailid' => $customer_email,
		            'contactno' => $customer_phone,
		            'password' => base64_encode("customer@123"),
		            'created_by' => 1001,	// admin has created this customer
		            'accountmanagerid' => $fk_account_manager_id,	// account manager id taken from CRM
		            'status' => 1
		        );
		        
		        // insert query
		        $result = $this->portalmodel->insert_query_('customer', $data_customer);
		        
		        $fk_customer_id = $num;
			}
		}*/
		
		/* NOW ADD OR GET USER ID */
		$fk_assigned_to = "0";
		if(!empty($user_email)){
			$fk_assigned_to = $this->portalmodel->select_name('user', 'id', "email='".$user_email."'");
			if(empty($fk_assigned_to) || $fk_assigned_to=="N/A"){
				
				// separate first name last name
				$arrName = explode(" ", $user_name);
				$arrUserName = array();
				if(count($arrName)==1){
					$arrUserName['first_name'] = $arrName[0];
					$arrUserName['last_name'] = "";
				}else if(count($arrName)==2){
					$arrUserName['first_name'] = $arrName[0];
					$arrUserName['last_name'] = $arrName[1];
				}else if(count($arrName)==3){
					$arrUserName['first_name'] = $arrName[0]." ".$arrName[1];
					$arrUserName['last_name'] = $arrName[2];
				}
				
				// add new user and get its id
				$id = $this->portalmodel->maxid('user', 'id');
		        $num = $id[0]->id + 1;
		        $employeeid = 'E00' . $num;
		        $data_at       = array(
		            'employeeid' => $employeeid,
		            'first_name' => $arrUserName['first_name'],
		            'last_name' => $arrUserName['last_name'],
		            'username' => $user_email,		// keep email id as their usernam/login id
		            'email' => $user_email,
		            'phone' => $user_phone,
		            'password' => base64_encode("user@123"),
		            'created_by' => 1001,	// admin created this user
		            'role' => 2,
		            'status' => 1,
		            'internal_user_external_user' => 2	// will be external user by default. if new created
		        );
		        
		        // insert query
		        $result = $this->portalmodel->insert_query_('user', $data_at);
		        
		        $fk_assigned_to = $num;
			}
		}
		
		/* add/get created by name and email */
		$created_by_name = $data_request['created_by_name'];
		$created_by_email = $data_request['created_by_email'];
		$created_by_id = "0";
		if(!empty($created_by_email)){
			$created_by_id = $this->portalmodel->select_name('user', 'id', "email='".$created_by_email."'");
			if(empty($created_by_id) || $created_by_id=="N/A"){
				
				// separate first name last name
				$arrName = explode(" ", $created_by_name);
				$arrUserName = array();
				if(count($arrName)==1){
					$arrUserName['first_name'] = $arrName[0];
					$arrUserName['last_name'] = "";
				}else if(count($arrName)==2){
					$arrUserName['first_name'] = $arrName[0];
					$arrUserName['last_name'] = $arrName[1];
				}else if(count($arrName)==3){
					$arrUserName['first_name'] = $arrName[0]." ".$arrName[1];
					$arrUserName['last_name'] = $arrName[2];
				}
				
				// add new user and get its id
				$id = $this->portalmodel->maxid('user', 'id');
		        $num = $id[0]->id + 1;
		        $employeeid = 'E00' . $num;
		        $data_cb       = array(
		            'employeeid' => $employeeid,
		            'first_name' => $arrUserName['first_name'],
		            'last_name' => $arrUserName['last_name'],
		            'username' => $created_by_email,		// keep email id as their usernam/login id
		            'email' => $created_by_email,
		            'phone' => "",
		            'password' => base64_encode("user@123"),
		            'created_by' => 1001,	// admin created this user
		            'role' => 2,
		            'status' => 1,
		            'internal_user_external_user' => 1	// will be internal user for created by user, as its coming from CRM and internal users only can create workorder
		        );
		        
		        // insert query
		        $result = $this->portalmodel->insert_query_('user', $data_cb);
		        
		        $created_by_id = $num;
			}
		}
		
		/** END CREATED BY **/
		
		// now that we have got the fk_customer_id and fk_assigned_to, then we can move on to add workorder
		
		// first check if the workorder already exists or not, if it doesn't exists, then only insert new.
		if(!empty($workorder_number)){
			$fk_workorder_id = $this->portalmodel->select_name('workorder', 'id_workorder', "workorder_number='".$workorder_number."'");
			if(empty($fk_workorder_id) || $fk_workorder_id=="N/A"){
				// add new workorder
				$id = $this->portalmodel->maxid('workorder', 'id_workorder');
		        $num = $id[0]->id + 1;
		        $data_wo       = array(
		            'workorder_number' => $workorder_number,
		            'fk_customer_id' => $fk_customer_id,
		        	'customer_name' => $customer_name,
		            'fk_assigned_to' => $fk_assigned_to,
		            'status' => $workorder_status,
		            'created_by' => $created_by_id,
		            'created_on' => date("Y-m-d"),
		            'modified_by' => '0',
		            'modified_on' => "0000-00-00"
		        );
		        
		        // insert query
		        $result = $this->portalmodel->insert_query_('workorder', $data_wo);
		        
		        $fk_workorder_id = $num;
		        
		        // now add pdf file name to workorderfiles table
		        $id = $this->portalmodel->maxid('workorderfiles', 'id');
		        $num = $id[0]->id + 1;
		        $data_wofiles       = array(
		            'fk_workorder_id' => $fk_workorder_id,
		            'path' => $workorder_file
		        );
		        // insert query
		        $result = $this->portalmodel->insert_query_('workorderfiles', $data_wofiles);
			}
		}
		
		
    }
    
	// LISTING WORKORDERS
    function workorders()
    {
        $this->common();
        $id = $this->session->userdata('sid');
		$internal_user_external_user = $this->session->userdata('internal_user_external_user');
        $data['details'] = $this->portalmodel->getWorkorderList($id,$internal_user_external_user);
        $data['logged_in_user_id']=$id;
        
        $this->load->view('staff/workorders', $data);
        $this->load->view('templates/footer');
    }
    
    // listing completed workorders
    function completedworkorders(){
    	$this->common();
        $id = $this->session->userdata('sid');
		$internal_user_external_user = $this->session->userdata('internal_user_external_user');
        $data['details'] = $this->portalmodel->getCompletedWorkorderList($id,$internal_user_external_user);
        $data['logged_in_user_id']=$id;
        
        $this->load->view('staff/completedworkorders', $data);
        $this->load->view('templates/footer');
    }
    
     /* WORKORDER RELATED FUNCTION ENDS */
    
    /**
     * following function is used to check user assigned projects in the specified interval of time
     */
    function checkProjectAssignments(){
    	$sid = $this->session->userdata('sid');
    	$sess_current_assigned_projects = $this->session->userdata('current_assigned_projects');
    	
    	// now get project ids which are currently assigned in db
    	$this->db->select("id");
        $this->db->from("project");
        $where = " ((accountmanager=" . $sid . ") or FIND_IN_SET( $sid, projectmanager ) or FIND_IN_SET( $sid, developer ))";
        $this->db->where($where);
        $query = $this->db->get();
        $query->num_rows();
        $projects_assigned_from_db=array();
        if ($query->num_rows() > 0) {
        	foreach ($query->result() as $row) {
                $projects_assigned_from_db[] = $row->id;
            }
        }
        $diff=array_diff($sess_current_assigned_projects, $projects_assigned_from_db);
        $count_diff = count($diff);
        $redirect = false;
        if(!empty($count_diff)){
        	// logout this user now
        	$redirect = true;
        }else{
        	// compare in reverse
        	$diff=array_diff($projects_assigned_from_db,$sess_current_assigned_projects);
        	$count_diff = count($diff);
        	if(!empty($count_diff)){
        		$redirect = true;
        	}
        }
        echo $redirect;
        exit;
        /*if ($redirect==true){
        	redirect('portal/logout');
        	exit;
        }*/
    }
    
    /**
     * following function will get if any new comments has added and it will show a bell icon on the top menu
     */
    function checkNewComments(){
    	$this->load->model("portalmodel");
    	
    	// first get the count from session
    	$current_number_of_comments = $this->session->userdata('current_number_of_comments');
    	
    	// now get current comments from db
    	$this->db->select("count(id) AS number_of_comments");
        $this->db->from("taskcomments");
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_comments;
        }
    	
        $difference = intval($comments_from_db)-intval($current_number_of_comments);
        
        /********** HIGHLIGHT NEW TASK NOTIFICATIONS **************/
        $data = $_SESSION;
    	$last_comment_id = $this->portalmodel->select($current_number_of_comments,0,"taskcomments","","id","ASC");
    	$data['last_comment_id'] = $last_comment_id[$current_number_of_comments-1]->id;
    	$this->session->set_userdata($data);
    	/*************/
        
    	// echo the difference
        echo $difference;    	
    }
    
	/**
     * following function will get if any new workorder comments has added and it will show a bell icon on the top menu
     */
    function checkNewWorkorderComments(){
    	// first get the count from session
    	$current_number_of_workorder_comments = $this->session->userdata('current_number_of_workorder_comments');
    	
    	// now get current comments from db
    	$this->db->select("count(id) AS number_of_comments");
        $this->db->from("workordercomments");
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_comments;
        }
    	
        $difference = intval($comments_from_db)-intval($current_number_of_workorder_comments);
        
        echo $difference;    	
    }
    
	/**
     * following function will reset the session 
     */
    function updateCommentsSession(){
    	$this->load->model("portalmodel");
    	
    	$this->db->select("count(id) AS number_of_comments");
        $this->db->from("taskcomments");
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_comments;
        }
        
    	if(!empty($comments_from_db)){
    		
    		$data = $_SESSION;
    		
    		// first we will get the last comment id for highlighting new comments
    		/*$current_number_of_comments = $this->session->userdata('current_number_of_comments');
    		$last_comment_id = $this->portalmodel->select($current_number_of_comments,0,"taskcomments","","id","DESC");
    		$data['last_comment_id'] = $last_comment_id[0]->id;*/
    		
    		$data['current_number_of_comments'] = $comments_from_db;
    		$this->session->set_userdata($data);
    	}
    }
    
	/**
     * following function will reset the session 
     */
    function updateWorkorderCommentsSession(){
    	$this->db->select("count(id) AS number_of_comments");
        $this->db->from("workordercomments");
        $query = $this->db->get();
        $query->num_rows();
        if ($query->num_rows() > 0) {
        	$row = $query->result();
        	$comments_from_db= $row[0]->number_of_comments;
        }
        
    	if(!empty($comments_from_db)){
    		$data = $_SESSION;
    		$data['current_number_of_workorder_comments'] = $comments_from_db;
    		$this->session->set_userdata($data);
    	}
    }
    
    /**
     * below function will mark workorder as done
     */
    function markWorkorderAsDone(){
    	$this->load->model("portalmodel");
    	
    	$id_workorder = $this->input->post('id_workorder');
    	$data   = array(
            'status' => 4,
    		'completed_on'=>date("Y-m-d H:i:s")
        );
        $result = $this->portalmodel->update_query('workorder', $data, $id_workorder, 'id_workorder');
        
        // after updating change attributes, here response is separated by double || and sequence is status_state,a_title and status_class
        echo "completed||Completed||workorder_status_completed";
    }
    
    /**
     * workorder details
     */
	function workorderdetails(){
        $this->common();
        $id = $this->input->get('id');
        $sid = $this->session->userdata('sid');
		$internal_user_external_user = $this->session->userdata('internal_user_external_user');
		
        $data['details'] = $this->portalmodel->getWorkorderDetails($id,$sid,$internal_user_external_user);
        
        $where2 = array();
        $data['customer_list']  = $this->portalmodel->get_dropdown_list('customer', 'customerid', 'companyname', $where2);
        
        $data['internal_user_external_user'] = $internal_user_external_user;
        
        $where4              = "fk_workorder_id=$id ";
        $comments = $this->portalmodel->select_where_cond('', '', 'workordercomments', $where4,"created_on","DESC");
        $li                  = array();
        if (!empty($comments)) {
            foreach ($comments as $d) {
                $l['comments'] = $d->comments;
                $name = "CONCAT(first_name,' ',last_name) as name";
                $l['commented_by'] = $this->portalmodel->select_username($name, 'id', $d->created_by, 'user');
                $l['created_on'] = $d->created_on;
				$li[]            = $l;
            }
        }
        $data['comment'] = $li;
        
        $this->load->view('staff/workorderdetails', $data);
        $this->load->view('templates/footer');
        
    }
    
    /**
     * Update workorder details to add files
     */
    function update_workorder(){
    	$this->load->model("portalmodel");
        $id_workorder = $this->input->post('id_workorder');
        $s_id      = $this->session->userdata('sid');
        
        // update workorder status
        $workorder_status = $this->input->post('workorder_status');
        if(!empty($workorder_status)){
        	$data     = array(
	            'status' => $workorder_status
	        );
	        $result_update_workorder = $this->portalmodel->update_query('workorder', $data, $id_workorder, 'id_workorder');
        }
        
        // now update workorder customer
        /*$fk_customer_id = $this->input->post('fk_customer_id');
    	if(!empty($fk_customer_id)){
        	$data     = array(
	            'fk_customer_id' => $fk_customer_id
	        );
	        $result_update_workorder = $this->portalmodel->update_query('workorder', $data, $id_workorder, 'id_workorder');
        }*/
        
        $customer_name = $this->input->post('customer_name');
    	if(!empty($customer_name)){
        	$data     = array(
	            'customer_name' => $customer_name
	        );
	        $result_update_workorder = $this->portalmodel->update_query('workorder', $data, $id_workorder, 'id_workorder');
        }
        
        $wo_files_email = 0;
        $filename = strtotime("now");
        $this->load->library('upload');
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
        	
        	$wo_files_email=1;
        	
        	$number_of_files_uploaded = count($_FILES['files']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
	            $dir_path                       = './workorderfiles/';
	            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = $filename . $i . '.' . $ext;
	            $config                         = array(
	                'file_name' => $filename . $i,
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
	                $data1              = array(
	                    'path' => $path,
	                    'fk_workorder_id' => $id_workorder
	                );
	                $result1 = $this->portalmodel->insert_query_('workorderfiles', $data1);
	            endif;
	        endfor;
        }
        
        // save Signed W/O
        /*if(isset($_FILES['signed_wo_file']) && !empty($_FILES['signed_wo_file'])){
        	$_FILES['userfile']['name']     = $_FILES['signed_wo_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['signed_wo_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['signed_wo_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['signed_wo_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['signed_wo_file']['size'];
            $dir_path                       = './workorderfiles/signed_wo/';
            $ext                            = pathinfo($_FILES['signed_wo_file']['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = $filename.'.'.$ext;
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
                	'fk_workorder_id'=>$id_workorder,
                    'file_name' => '',
                	'file' => $path,
	                'created_by' => $s_id,
		            'created_on' => date("Y-m-d H:i:s"),
		            'modified_by' => '0',
		            'modified_on' => "0000-00-00 00:00:00"
                );
                $result1 = $this->portalmodel->insert_query_('wo_signed', $data1);
            endif;
        }*/
        
        $signed_wo_email = 0;
    	$this->load->library('upload');
        if(isset($_FILES['files_signedwo']) && !empty($_FILES['files_signedwo'])){
        	
        	$signed_wo_email = 1;
        	
        	$number_of_files_uploaded = count($_FILES['files_signedwo']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	        
	        	$filename = strtotime("now");
	        
	            $_FILES['userfile']['name']     = $_FILES['files_signedwo']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files_signedwo']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files_signedwo']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files_signedwo']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files_signedwo']['size'][$i];
	            $dir_path                       = './workorderfiles/signed_wo/';
	            $ext                            = pathinfo($_FILES['files_signedwo']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = $filename . $i . '.' . $ext;
	            $config                         = array(
	                'file_name' => $filename . $i,
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
	                	'fk_workorder_id'=>$id_workorder,
	                    'file_name' => '',
	                	'file' => $path,
		                'created_by' => $s_id,
			            'created_on' => date("Y-m-d H:i:s"),
			            'modified_by' => '0',
			            'modified_on' => "0000-00-00 00:00:00"
	                );
	                $result1 = $this->portalmodel->insert_query_('wo_signed', $data1);
	            endif;
	        endfor;
        }
        
    	// save Tech Invoice
        /*if(isset($_FILES['tech_invoice_file']) && !empty($_FILES['tech_invoice_file'])){
        	$_FILES['userfile']['name']     = $_FILES['tech_invoice_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['tech_invoice_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['tech_invoice_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['tech_invoice_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['tech_invoice_file']['size'];
            $dir_path                       = './workorderfiles/tech_invoice/';
            $ext                            = pathinfo($_FILES['tech_invoice_file']['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = $filename.'.'.$ext;
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
                	'fk_workorder_id'=>$id_workorder,
                    'file_name' => '',
                	'file' => $path,
	                'created_by' => $s_id,
		            'created_on' => date("Y-m-d H:i:s"),
		            'modified_by' => '0',
		            'modified_on' => "0000-00-00 00:00:00"
                );
                $result1 = $this->portalmodel->insert_query_('wo_tech_invoice', $data1);
            endif;
        }*/
        
        $tech_invoice_email = 0;
    	$this->load->library('upload');
        if(isset($_FILES['files_techinvoice']) && !empty($_FILES['files_techinvoice'])){
        	
        	$tech_invoice_email = 1;
        	
        	$number_of_files_uploaded = count($_FILES['files_techinvoice']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	        	$filename = strtotime("now");
	        	
	            $_FILES['userfile']['name']     = $_FILES['files_techinvoice']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files_techinvoice']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files_techinvoice']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files_techinvoice']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files_techinvoice']['size'][$i];
	            $dir_path                       = './workorderfiles/tech_invoice/';
	            $ext                            = pathinfo($_FILES['files_techinvoice']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = $filename . $i . '.' . $ext;
	            $config                         = array(
	                'file_name' => $filename . $i,
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
	                	'fk_workorder_id'=>$id_workorder,
	                    'file_name' => '',
	                	'file' => $path,
		                'created_by' => $s_id,
			            'created_on' => date("Y-m-d H:i:s"),
			            'modified_by' => '0',
			            'modified_on' => "0000-00-00 00:00:00"
	                );
	                $result1 = $this->portalmodel->insert_query_('wo_tech_invoice', $data1);
	            endif;
	        endfor;
        }
        
    	// save Added Info
        /*if(isset($_FILES['added_info_file']) && !empty($_FILES['added_info_file'])){
        	$_FILES['userfile']['name']     = $_FILES['added_info_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['added_info_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['added_info_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['added_info_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['added_info_file']['size'];
            $dir_path                       = './workorderfiles/added_info/';
            $ext                            = pathinfo($_FILES['added_info_file']['name'], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = $filename.'.'.$ext;
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
                	'fk_workorder_id'=>$id_workorder,
                    'file_name' => '',
                	'file' => $path,
	                'created_by' => $s_id,
		            'created_on' => date("Y-m-d H:i:s"),
		            'modified_by' => '0',
		            'modified_on' => "0000-00-00 00:00:00"
                );
                $result1 = $this->portalmodel->insert_query_('wo_added_info', $data1);
            endif;
        }*/
        
        $added_info_email = 0;
    	$this->load->library('upload');
        if(isset($_FILES['files_addedinfo']) && !empty($_FILES['files_addedinfo'])){
        	
        	$added_info_email = 1;
        	
        	$number_of_files_uploaded = count($_FILES['files_addedinfo']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	        	$filename = strtotime("now");
	        	
	            $_FILES['userfile']['name']     = $_FILES['files_addedinfo']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files_addedinfo']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files_addedinfo']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files_addedinfo']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files_addedinfo']['size'][$i];
	            $dir_path                       = './workorderfiles/added_info/';
	            $ext                            = pathinfo($_FILES['files_addedinfo']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = $filename . $i . '.' . $ext;
	            $config                         = array(
	                'file_name' => $filename . $i,
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
	                	'fk_workorder_id'=>$id_workorder,
	                    'file_name' => '',
	                	'file' => $path,
		                'created_by' => $s_id,
			            'created_on' => date("Y-m-d H:i:s"),
			            'modified_by' => '0',
			            'modified_on' => "0000-00-00 00:00:00"
	                );
	                $result1 = $this->portalmodel->insert_query_('wo_added_info', $data1);
	            endif;
	        endfor;
        }
        
        /* CHANGE STATUS TO PENDING, IF BOTH THE FILES SIGNED WO AND TECH INVOICE HAS BEEN UPLOADED */
        // now check, if the signed w/o and tech invoice is there in db, then change status of the workorder to 3 (PENDING)
        $has_signed_wo_file  = $this->portalmodel->select_name('wo_signed', 'id', "fk_workorder_id='".$id_workorder."'");
        $has_tech_invoice_file  = $this->portalmodel->select_name('wo_tech_invoice', 'id', "fk_workorder_id='".$id_workorder."'");
        $current_wo_status = $this->portalmodel->select_name('workorder', 'status', "id_workorder='".$id_workorder."'");
        
        if((!empty($has_signed_wo_file) && $has_signed_wo_file!="N/A") && (!empty($has_tech_invoice_file) && $has_tech_invoice_file!="N/A") && $current_wo_status==2){
        	$data=array('status'=>'3');
        	$result = $this->portalmodel->update_query('workorder', $data, $id_workorder, 'id_workorder');
        }
        /* CHANGING STATUS OF THE WORKORDER TO PENDING ENDS HERE */
        
        // now send email notification to the creater of the workorder if any of the signed wo/tech invoice/added info file is added
        $arrmsg = array();
        if($wo_files_email){
        	$arrmsg[] = "Workorder File";
        }
        if($signed_wo_email){
        	$arrmsg[] = "Signed Workorder";
        }
    	if($tech_invoice_email){
        	$arrmsg[] = "Tech Invoice";
        }
    	if($added_info_email){
        	$arrmsg[] = "Added Info";
        }
        $strFiles = implode(",", $arrmsg);
        
        if(!empty($strFiles)){
        	$cond = "id_workorder=".$id_workorder;
	        $workorder_number = $this->portalmodel->select_name('workorder', 'workorder_number', $cond);
	        
	        $cond = "id_workorder=".$id_workorder;
	        $created_by_id = $this->portalmodel->select_name('workorder', 'created_by', $cond);
	        
	        if(!empty($created_by_id) && $created_by_id!="N/A"){
	        	$cond = "id=".$created_by_id;
		        $to = $this->portalmodel->select_name('user', 'email', $cond);
	        
	        	$subject = "Workorder file(s) added!";
	        	$message = "Hello, <br />Workorder file(s) have been added for ".$strFiles."<br /> to workorder number : ".$workorder_number."<br /><br />Thank you,<br />- Synergy IT Team";
	        	//echo $to."<br />".$subject."<br />".$message; exit;
	        	$email = $this->email($to, $subject, $message);
	        	
	        	// also send to business email
	        	$to_business_email = $this->portalmodel->select_name('user', 'business_email', $cond);
	        	if(!empty($to_business_email) && $to_business_email!='N/A'){
	        		$email = $this->email($to_business_email, $subject, $message);
	        	}
	        }
        	
        }
        
        // send email ends
        
        redirect("portal/workorders");
    }
    
    function removeExistingUserFile(){
    	$this->load->model("portalmodel");
        
    	$id = $this->input->post('id');
        $from_table = $this->input->post('from_table');
        
        $this->portalmodel->deleteid($from_table,"id",$id);
        
        echo "File has been removed successfully!";
    }
    
    function removeWoFiles(){
    	$this->load->model("portalmodel");
    	
    	$id_workorder = $this->input->post('id_workorder');
        $table = $this->input->post('table');
        $id = $this->input->post('id');
        
        $this->portalmodel->deleteid($table,"id",$id);
        
        echo "File has been removed successfully!";
    }
    
    function changeWorkOrderStatus(){
    	$this->load->model("portalmodel");
        
    	$id_workorder = $this->input->post('id_workorder');
    	$status_to_change = $this->input->post('status_to_change');
    	
    	$data=array('status'=>$status_to_change);
        $result = $this->portalmodel->update_query('workorder', $data, $id_workorder, 'id_workorder');
    }
    
    /**
     * add workorder comments
     */
	function addworkordercomments(){
        $this->load->model("portalmodel");
        $fk_workorder_id = $this->input->post('fk_workorder_id');
        $s_id = $this->session->userdata('sid');
        $data   = array(
            'fk_workorder_id' => $fk_workorder_id,
            'comments' => $this->input->post('comment'),
            'commented_by' => 1,
            'created_by' => $s_id,
        	'created_on' => date("y-m-d H:i:s")
        );
        $result = $this->portalmodel->insert_query_('workordercomments', $data);
        
		/* send email to created by start */
        $created_by = $this->portalmodel->select_name("workorder","created_by","id_workorder='".$fk_workorder_id."'");
        if(!empty($created_by) && $created_by!="N/A" && $created_by!=$s_id){
        	// get email of created by user and send email
        	$emailid = $this->portalmodel->select_name("user","email","id='".$created_by."'");
        	if(!empty($emailid) && $emailid!="N/A"){
        		// get workorder number for subject and message body
        		$workordernumber = $this->portalmodel->select_name("workorder","workorder_number","id_workorder='".$fk_workorder_id."'");
        		if(!empty($workordernumber) && $workordernumber!="N/A"){
        			
        			$comment_added_by_name = $this->session->userdata('first_name');
        			
        			// send email
        			$subject = "Workorder Comment Added for Workorder Number ".$workordernumber;
        			$body = "Hello,<br /><br />";
        			$body .= $comment_added_by_name." has added comment to workorder number ".$workordernumber."<br /><br />";
        			$body .= "Comment : <br />".$this->input->post('comment');
        			$body .= "<br /><br />Thank you,<br />- Synergy IT Team";
        			$send_workorder_comment_email = $this->email($emailid, $subject, $body);
        			
        			// also send email to business email
        			$business_emailid = $this->portalmodel->select_name("user","business_email","id='".$created_by."'");
        			if(!empty($business_emailid) && $business_emailid!='N/A'){
						$send_workorder_comment_email = $this->email($business_emailid, $subject, $body);
        			}
        		}
        	}
        }
        /* send email to created by end */
		
		redirect("portal/workorderdetails?id=" . $fk_workorder_id);
    }
    
    /**
     * update technician dropdown on changing project
     */
    function updatetechniciandropdown(){
    	$this->load->model("portalmodel");
        $project_id = $this->input->post('project_id');
        
        $where1             = "id=$project_id";
        $prj                = $this->portalmodel->select_where('', '', 'project', $where1);
        $dev                = $prj[0]->developer;
        
        $arr = explode(",", $dev);
        $arr = array_filter($arr, function($value){ return $value !== ''; });
        
        $dev = implode(",", $arr);
        if(!empty($dev)){
        	$where2             = "id in ($dev)";
	        $cond               = "CONCAT(first_name,' ',last_name) as name";
	        $developer  = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, $where2);
        }else{
        	$developer = array(""=>"Please Select");
        }
        
        echo form_dropdown('developer', $developer,'','class="form-control" id="developer"');
    }
    
    /**
     * following function will be used for chat for projects
     */
    function chat_project(){
    	$this->common();
    	
    	$project_id = $this->input->get('project_id');
    	
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
    	
    	// now get customer list as well to add for members(customers)
    	$customer_list_dropdown = $this->portalmodel->get_dropdownuser_list_without_blank("customer","customerid","companyname as name",'',"companyname","ASC");
    	$data['customer_list_dropdown'] = $customer_list_dropdown;
    	
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
    	$fk_user_id = $this->session->userdata('sid');
    	$last_chat_id = $this->portalmodel->select_name("last_chat_project","id","fk_user_id='".$fk_user_id."' AND fk_project_id='".$project_id."'");
    	
    	// get latest chat id
    	$fk_chat_id = $this->portalmodel->select_name("chat_projects","MAX(chat_id)","fk_project_id='".$project_id."'");
    	
    	if((empty($last_chat_id) || $last_chat_id=="N/A") && (!empty($fk_chat_id) && $fk_chat_id!="N/A")){
    		// insert new
    		$datainsert = array(
    			"fk_user_id"=>$fk_user_id,
    			"fk_chat_id"=>$fk_chat_id,
    			"fk_project_id"=>$project_id
    		);
    		$insert = $this->portalmodel->insert_query_("last_chat_project",$datainsert);
    	}else{
    		// update with latest chat id for this project
    		$dataupdate = array(
    			"fk_chat_id"=>$fk_chat_id
    		);
    		$this->portalmodel->update_query("last_chat_project",$dataupdate,$last_chat_id,"id");
    	}
    	/* storing latest chat id for notification ENDS */
    	
    	$this->load->view('staff/chat_project', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * adding new comment in project chat
     */
    function chat_add_project(){
    	$this->load->model("portalmodel");
    	$message = urldecode($this->input->post('message'));
        $fk_project_id = $this->input->post('project_id');
        $fk_user_id = $this->session->userdata('sid');
        
        $data = array(
        	'fk_project_id'=>$fk_project_id,
        	'fk_user_id'=>$fk_user_id,
        	'message'=>$message,
        	'created_on'=>date("Y-m-d H:i:s")
        );
        $result   = $this->portalmodel->insert_query_('chat_projects', $data);
        
        // now get list of added comments in asc order and display with ajax
    	$chat_data = $this->portalmodel->getProjectChat($fk_project_id);
    	$data['chat_data'] = $chat_data;
    	$data['logged_in_userid'] = $fk_user_id;
    	
    	/* storing latest chat id for notification STARTS */
    	$last_chat_id = $this->portalmodel->select_name("last_chat_project","id","fk_user_id='".$fk_user_id."' AND fk_project_id='".$fk_project_id."'");
    	
    	// get latest chat id
    	$fk_chat_id = $this->portalmodel->select_name("chat_projects","MAX(chat_id)","fk_project_id='".$fk_project_id."'");
    	
    	if((empty($last_chat_id) || $last_chat_id=="N/A") && (!empty($fk_chat_id) && $fk_chat_id!="N/A")){
    		// insert new
    		$datainsert = array(
    			"fk_user_id"=>$fk_user_id,
    			"fk_chat_id"=>$fk_chat_id,
    			"fk_project_id"=>$fk_project_id
    		);
    		$insert = $this->portalmodel->insert_query_("last_chat_project",$datainsert);
    	}else{
    		// update with latest chat id for this project
    		$dataupdate = array(
    			"fk_chat_id"=>$fk_chat_id
    		);
    		$this->portalmodel->update_query("last_chat_project",$dataupdate,$last_chat_id,"id");
    	}
    	/* storing latest chat id for notification ENDS */
    	
    	// load ajax file to view the chat data
    	$this->load->view('staff/chat_project_ajax', $data);
    }
    
    /**
     * following function will check for automatically chat update for project chat
     */
    function chat_check_project(){
    	$this->load->model("portalmodel");
    	$fk_project_id = $this->input->post('project_id');
    	$txt_search = $this->input->post('txt_search');
    	$fk_user_id = $this->session->userdata('sid');
    	
    	$chat_data = $this->portalmodel->getProjectChat($fk_project_id,$txt_search);
    	$data['chat_data'] = $chat_data;
    	$data['logged_in_userid'] = $fk_user_id;
    	
    	/* storing latest chat id for notification STARTS */
    	$last_chat_id = $this->portalmodel->select_name("last_chat_project","id","fk_user_id='".$fk_user_id."' AND fk_project_id='".$fk_project_id."'");
    	
    	// get latest chat id
    	$fk_chat_id = $this->portalmodel->select_name("chat_projects","MAX(chat_id)","fk_project_id='".$fk_project_id."'");
    	
    	if((empty($last_chat_id) || $last_chat_id=="N/A") && (!empty($fk_chat_id) && $fk_chat_id!="N/A")){
    		// insert new
    		$datainsert = array(
    			"fk_user_id"=>$fk_user_id,
    			"fk_chat_id"=>$fk_chat_id,
    			"fk_project_id"=>$fk_project_id
    		);
    		$insert = $this->portalmodel->insert_query_("last_chat_project",$datainsert);
    	}else{
    		// update with latest chat id for this project
    		$dataupdate = array(
    			"fk_chat_id"=>$fk_chat_id
    		);
    		$this->portalmodel->update_query("last_chat_project",$dataupdate,$last_chat_id,"id");
    	}
    	/* storing latest chat id for notification ENDS */
    	
    	// load ajax file to view the chat data
    	$this->load->view('staff/chat_project_ajax', $data);
    }
    
    /**
     * Following function will upload file for chat
     */
    function upload_project_chat_file_ajax(){
    	$this->load->model("portalmodel");
    	
    	$fk_project_id = $this->input->post('project_id');
	    $fk_user_id = $this->session->userdata('sid');
    	
    	for ($i=0;$i<count($_FILES['files']['name']);$i++){
    		// first we will add a new record for new chat, and then will add new file with chat reference number
	    	$message = "added file...";
	    	$data_file_add_message = array(
	        	'fk_project_id'=>$fk_project_id,
	        	'fk_user_id'=>$fk_user_id,
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
     * following functin will add member to project chat
     */
    function add_member_to_project_chat(){
    	$this->load->model("portalmodel");
    	
    	$fk_project_id = $this->input->post('project_id');
    	$fk_user_id = $this->input->post('fk_user_id');
    	
    	$data_add_member = array(
        	'fk_project_id'=>$fk_project_id,
        	'fk_user_id'=>$fk_user_id
        );
        $add_member_id = $this->portalmodel->insert_query_('chat_project_members', $data_add_member);
    }
    
    /**
     * Forgot Password
     */
    function forgotpassword(){
    	$this->load->model("portalmodel");
    	
    	$email = $this->input->post('email_forgotpassword');
    	
    	if(!empty($email)){
    		// get user id from email
	    	$condition = "email = '".$email."'";
	    	$user_id = $this->portalmodel->select_name('user', 'id', $condition);
	    	
	    	if(!empty($user_id) && $user_id!='N/A'){
	    		$user_name = $this->portalmodel->select_name('user', 'first_name', "id='".$user_id."'");
	    		
	    		// send email for reset password link
	    		$param = md5("id")."=".md5($user_id);
	    		$link = base_url('index.php/portal/resetpassword/?'.$param);
	    		
	    		$to = $email;
	    		$subject = "SynergyInteract : Reset Password!";
	    		$message = "Hello ".$user_name.",<br /><br />";
	    		$message .= "We have received your request to reset your password!<br /><br />";
	    		$message .= "Click on below link or copy and paste it in browser to reset your password.<br /><br />";
	    		$message .= "<a href=".$link." target='_blank'>".$link."</a><br /><br />";
	    		$message .= "Thank You<br />- Synergy IT Team.";
	    		
	    		$forgotpassword_send_email = $this->email($to, $subject, $message);
	    		
	    		// also send link to business email
	    		$business_email = $this->portalmodel->select_name('user', 'business_email', "id='".$user_id."'");
	    		if(!empty($business_email) && $business_email!='N/A'){
	    			$forgotpassword_send_email = $this->email($business_email, $subject, $message);
	    		}
	    		
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'We have sent you an email to reset your password!'); //display the flashdata using session
	            //redirect('portal/index'); //user is not valid so goto login page again
	    		redirect('/'); //user is not valid so goto login page again
	    	}else{
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'Email not registered!'); //display the flashdata using session
	            //redirect('portal/index'); //user is not valid so goto login page again
	            redirect('/'); //user is not valid so goto login page again
	    	}
    	}else{
    		// set error message
    		$this->session->set_flashdata('error_message', 'Please enter valid Email!'); //display the flashdata using session
            redirect('/'); //user is not valid so goto login page again
    	}
    }
    
    /**
     * Reset Password
     */
    function resetpassword(){
    	$this->load->model("portalmodel");
    	
    	$id = $this->input->get(md5('id'));
    	
    	// get user id of the received id, and if that user doesn't exists, then display error invalid request
    	
    	$user_id = $this->portalmodel->select_name('user', 'id', "md5(id)='".$id."'");
    	
    	$data['user_id']=$user_id;
    	
    	if(!empty($user_id) && $user_id!="N/A"){
    		// load view for reset password
    		$this->load->view('staff/resetpassword',$data);
    	}else{
    		// set error message
    		$this->session->set_flashdata('error_message', 'Invalid Request!'); //display the flashdata using session
            //redirect('portal/index'); //user is not valid so goto login page again
            redirect('/'); //user is not valid so goto login page again
    	}
    }
    
    /**
     * Update New Password
     */
    function updatepassword(){
    	$this->load->model("portalmodel");
    	
    	$user_id = $this->input->post('user_id');
    	$new_password = $this->input->post('new_password');
    	$confirm_password = $this->input->post('confirm_password');
    	
    	if(empty($new_password)){
    		// set error message and reload the resetpassword page
    		$data['user_id'] = $user_id;
    		$this->session->set_flashdata('error_message', 'Please enter new password!'); //display the flashdata using session
            $this->load->view('staff/resetpassword',$data);
    	}else if($new_password!=$confirm_password){
    		// set error message and reload the resetpassword page
    		$data['user_id'] = $user_id;
    		$this->session->set_flashdata('error_message', 'Confirm Password did not match!'); //display the flashdata using session
            $this->load->view('staff/resetpassword',$data);
    	}else{
    		$data     = array(
	            'password' => base64_encode($new_password)
	        );
	        $result   = $this->portalmodel->update_query('user', $data, $user_id, 'id');
	        $this->session->set_flashdata('error_message', 'Password reset successfully!'); //display the flashdata using session
	        //redirect('portal/index');
	        redirect('/');
    	}
    }
    
	/**
     * following functin will add member to project chat
     */
    function add_customer_to_project_chat(){
    	$this->load->model("portalmodel");
    	
    	$project_id = $this->input->post('project_id');
    	$fk_customer_id = $this->input->post('fk_customer_id');
    	$customer_first_name = $this->input->post('customer_first_name');
    	$customer_email = $this->input->post('customer_email');
    	$customer_password = $this->input->post('customer_password');
    	
    	// check if record already exists
    	$id = $this->portalmodel->select_name("customer_user_project_chat","id","email='".$customer_email."'");
    	if(empty($id) || $id=="N/A"){
    		// add new record
    		$data_add_member = array(
    			'first_name'=>$customer_first_name,
	        	'email'=>$customer_email,
    			'password'=>base64_encode($customer_password),
    			'project_ids'=>$project_id
	        );
	        
	        $inserted_id = $this->portalmodel->insert_query_('customer_user_project_chat', $data_add_member);
	        
	        if($inserted_id!="False"){
	        	// insert in customer_user_connection for this customer connection with the member added
	        	$data_add_customer_user_connection = array(
	        		'fk_customer_id'=>$fk_customer_id,
	        		'fk_user_id'=>$inserted_id
	        	);
	        	
	        	$customer_user_connection_id = $this->portalmodel->insert_query_('customer_user_connection', $data_add_customer_user_connection);
	        }
    	}else{
    		// update existing.
    		
    		// first check if the selected project exists or not
    		$project_ids = $this->portalmodel->select_name("customer_user_project_chat","project_ids","id='".$id."'");
    		if(empty($project_ids) || $project_ids=="N/A"){
    			$data_update=array("project_ids"=>$project_id);
    			$result = $this->portalmodel->update_query('customer_user_project_chat', $data_update, $id, 'id');
    		}else{
    			$arr_project_ids = explode(",", $project_ids);
    			if(!in_array($project_id, $arr_project_ids)){
    				$arr_project_ids[]=$project_id;
    			}
    			$update_project_ids = implode(",", $arr_project_ids);
    			
    			$data_update=array("project_ids"=>$update_project_ids);
    			$result = $this->portalmodel->update_query('customer_user_project_chat', $data_update, $id, 'id');
    		}
    	}
    }
    
    /*public function send_email(){
    	$this->load->model("portalmodel");
    	
    	$to = "kedar@platinait.com";
    	$subject = "Portal Email Test!";
    	$message = "This is testing!";
    	$email = $this->email($to, $subject, $message);
    	$email = $this->email("kedarsynergyit@gmail.com", $subject, $message);
    	//$email = $this->email("kiranjeet@platinait.com", $subject, $message);
    	
    	if($email){
    		echo "Email Sent Successfully!";
    	}else{
    		echo "Error sending email!";
    	}
    	exit;
    }*/
    
	/**
     * following function will reset the session 
     */
    function updateBlogsSession(){
    	
    	$this->load->model("portalmodel");
    	
    	$current_date = date("Y-m-d");
    	
    	$condition_news    = "show_user=1 AND from<='".$current_date."' AND to>='".$current_date."'";
    	
    	// just update the latest id in table last_blog_notification
    	$logged_in_user_id = $this->session->userdata('sid');
    	
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
        	
        	$fk_user_id = $this->portalmodel->select_name("last_blog_notification","fk_user_id","fk_user_id='".$logged_in_user_id."'");
        	if(!empty($fk_user_id) && $fk_user_id!="N/A"){
	        	$data = array("fk_blog_id"=>$maxid);
	        	$this->portalmodel->update_query('last_blog_notification', $data, $logged_in_user_id, 'fk_user_id');
        	}else{
        		// insert
        		$data = array("fk_user_id"=>$logged_in_user_id,"fk_blog_id"=>$maxid);
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
    	
    	$logged_in_user_id = $this->session->userdata('sid');
    	
    	// get last blog id
    	$last_blog_id = $this->portalmodel->select_name("last_blog_notification","fk_blog_id","fk_user_id='".$logged_in_user_id."'");
    	
    	// now get current comments from db
    	$current_date = date("Y-m-d");
        $condition_news = "show_user=1 AND from<='".$current_date."' AND to>='".$current_date."' AND id>'".$last_blog_id."'";
        
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
    
    public function execute_query(){
    	$this->load->model("portalmodel");
    	
    	/*$qry = "ALTER TABLE `customer` CHANGE `companyname` `companyname` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
    	$this->portalmodel->executeQuery($qry);
    	
    	$qry = "ALTER TABLE `customer` CHANGE `first_name` `first_name` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL, CHANGE `last_name` `last_name` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
    	$this->portalmodel->executeQuery($qry);
    	
    	$qry = "ALTER TABLE `project` CHANGE `title` `title` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
    	$this->portalmodel->executeQuery($qry);
    	
    	$qry = "ALTER TABLE `notification` CHANGE `notification` `notification` VARCHAR(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
    	$this->portalmodel->executeQuery($qry);
    	
    	$qry = "CREATE TABLE IF NOT EXISTS `last_blog_notification` (`fk_user_id` int(11) NOT NULL,`fk_customer_id` int(11) NOT NULL,`fk_blog_id` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
    	$this->portalmodel->executeQuery($qry);
    	
    	// increase limit of comments from 250 to 300
    	$qry = "ALTER TABLE `taskcomments` CHANGE `comments` `comments` VARCHAR(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
    	$this->portalmodel->executeQuery($qry);*/
    	
    	echo "Done!";
    	exit;
    }
    
    // check duplicate company name
    public function checkduplicatecompanyname(){
    	$this->load->model("portalmodel");
    	
    	$companyname = $this->input->post('companyname');
        $existing_id = $this->portalmodel->select_name("customer","customerid","TRIM(LOWER(companyname))='".trim(strtolower($companyname))."'");
        
        if(!empty($existing_id) && $existing_id!='N/A'){
        	echo 1;
        }else{
        	echo 0;
        }
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
    	
    	// now get associated users of this project.
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
    
    // check duplicate userid, while adding users
    public function checkduplicateuserid(){
		$this->load->model("portalmodel");
    	
		$userid = $this->input->post('userid');
		$existing_id = $this->portalmodel->select_name("user","id","TRIM(LOWER(username))='".trim(strtolower($userid))."'");

		if(!empty($existing_id) && $existing_id!='N/A'){
			echo 1;
		}else{
			echo 0;
		}
    }
    
    // dashboard chat notification for all project chat from employee, customer and customer user/member
    public function dashboard_chat_notifications(){
    	$this->load->model("portalmodel");
    	$fk_user_id = $this->session->userdata('sid');
    	$urole=$this->session->userdata('srole');
    	
    	$chat_data = $this->portalmodel->getAllProjectChat($fk_user_id,$urole);
    	
    	$data['chat_data'] = $chat_data;
    	$data['logged_in_userid'] = $fk_user_id;
    	
    	// load ajax file to view the chat data
    	$this->load->view('staff/chat_project_dashboard_ajax', $data);
    }
    
    // export projects
    public function exportprojects(){
    	// load model
    	// $this->load->model('Export', 'export');
    	$this->load->model('portalmodel');
    	
    	// get project data
    	$id = $this->session->userdata('sid');
        $sessionrole = $this->session->userdata('srole');
        
        if ($sessionrole == 1) {
        	$where="";
        }else{
        	$where = "(accountmanager=" . $id . ") or FIND_IN_SET( $id, projectmanager ) or FIND_IN_SET( $id, developer ) ";
        }
        
    	$list = $this->portalmodel->select('', '', 'project',$where,'title','ASC');
        
        // file name
        $fileName = 'Projects.xlsx';
        
        // load excel library
        $this->load->library('excel');
        
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

		// Create Columns
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', 'CUSTOMER')
			->setCellValue('B1', 'PROJECT TITLE')
			->setCellValue('C1', 'START DATE')
			->setCellValue('D1', 'EXPECTED COMPLETION DATE')
			->setCellValue('E1', 'ACCOUNT MANAGER')
			->setCellValue('F1', 'PROJECT MANAGER')
			->setCellValue('G1', 'TECHS')
			->setCellValue('H1', 'DESCRIPTION');
			
		// set active sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		// set title
		$objPHPExcel->getActiveSheet()->setTitle("Projects");
		
		// set heading of columns to bold
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
		
		$row = 2;
		
		foreach ($list as $d) {
			$col = 'A';
			
			$customer_name = $this->portalmodel->select_name('customer', 'companyname', "customerid=".$d->customerid);
			$project_title = $d->title;
			$startdate = $d->created_on;
			$enddate = $d->end_date;
			
			$name = "CONCAT(first_name,' ',last_name) as name";
            $accountmanager = $this->portalmodel->select_username($name, 'id', $d->accountmanager, 'user');
            $projectmanager = $this->portalmodel->select_username($name, 'id', $d->projectmanager, 'user');
            
            $techs = "";
            if(isset($d->developer) && !empty($d->developer)){
            	$tech_list = $this->portalmodel->select('', '', 'user',"id IN (".$d->developer.")",'first_name','ASC');
            	$arrTechs = array();
            	if(!empty($tech_list)){
	            	foreach ($tech_list as $each_tech){
	            		$arrTechs[] = $each_tech->first_name." ".$each_tech->last_name;
	            	}
	            	
	            	$techs = implode(", ", $arrTechs);
            	}
            }
            
            $description = $d->details;
            
            // add data to columns
            $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($col++.$row, $customer_name)
				->setCellValue($col++.$row, $project_title)
				->setCellValue($col++.$row, $startdate)
				->setCellValue($col++.$row, $enddate)
				->setCellValue($col++.$row, $accountmanager)
				->setCellValue($col++.$row, $projectmanager)
				->setCellValue($col++.$row, $techs)
				->setCellValue($col++.$row, $description);
				
			$row++;
		}
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Redirect output to a client�s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$fileName);
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
    }
    
    // PROFILE REPORT
    public function profilereport(){
    	$this->common();
    	
    	$arrstatus = array(0=>"Active",1=>"Active",2=>"In-Active",3=>"Closed");	// 0 active is for customer
    	$arrmonths = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September",10=>"October",11=>"November",12=>"December");
    	$overalltotal = 0;
    	
    	$data = array();
    	
    	// set data
    	$data['active_inactive_users'] = (isset($_POST['active_inactive_users']))?$_POST['active_inactive_users']:"-1";
    	$data['start_date'] = (isset($_POST['start_date']))?$_POST['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($_POST['end_date']))?$_POST['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	
    	// get count of total user profiles group by status
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($_POST['active_inactive_users']) && $_POST['active_inactive_users']!="-1"){
    		$condition .= " AND status='".$_POST['active_inactive_users']."'";
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
    	if(isset($_POST['active_inactive_users']) && $_POST['active_inactive_users']!="-1"){
    		$condition .= " AND status='".$_POST['active_inactive_users']."'";
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
    	    	
    	$this->load->view('staff/profilereport', $data);
        $this->load->view('templates/footer');
    }
    
    // PROJECT REPORT
    public function projectreport(){
		$this->common();
		
		$sessionrole = $this->session->userdata('srole');
		$loggedin_user_id = $this->session->userdata('sid');
		
		$arrstatus = array(1=>"Open",2=>"Closed");
		$arrmonths = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September",10=>"October",11=>"November",12=>"December");
		
		$overalltotal = 0;
		
		$data = array();
		
		// SET VARIABLES
		$data['active_inactive_users'] = (isset($_POST['active_inactive_users']))?$_POST['active_inactive_users']:"-1";
		$data['start_date'] = (isset($_POST['start_date']))?$_POST['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($_POST['end_date']))?$_POST['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	
    	// QUERY CONDITION
    	$condition = "modified_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($_POST['active_inactive_users']) && $_POST['active_inactive_users']!="-1"){
    		$condition .= " AND status='".$_POST['active_inactive_users']."'";
    	}
    	
    	// CONDITION FOR INTERNAL OR EXTERNAL USER , OTHER THAN ADMIN
    	if($sessionrole!=1) {
			$condition.=" AND ( (accountmanager=".$loggedin_user_id.") or FIND_IN_SET(".$loggedin_user_id.", projectmanager) or FIND_IN_SET(".$loggedin_user_id.", developer) )";
    	}
    	
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
    	if($data['active_inactive_users']=="-1"){
    		$data['percentage']['open'] = (is_float(($percentage_project[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[1]]*100)/$overalltotal."%";
	    	$data['percentage']['closed'] = (is_float(($percentage_project[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[2]]*100)/$overalltotal."%";
    	}else{
    		if($data['active_inactive_users']==1){
    			$data['percentage']['open'] = (is_float(($percentage_project[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[1]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==2){
    			$data['percentage']['closed'] = (is_float(($percentage_project[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_project[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_project[$arrstatus[2]]*100)/$overalltotal."%";
    		}
    	}
    	
    	// now set the heading and graph title. if the start and end month is different, then title will be changed. Also set the X and Y data for graph
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
    	if($end_month==$start_month){
    		$data['heading'] = "TOTAL PROJECTS IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    		if($data['active_inactive_users']=="-1"){
    			$data['arrX'] = array('"Open"', '"Closed"');
    		}else if($data['active_inactive_users']=="1"){
    			$data['arrX'] = array('"Open"');
    		}else if($data['active_inactive_users']=="2"){
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
    		if($data['active_inactive_users']=="-1"){
    			$data['arrY']['Open']['color'] = "#1bdb07";
	    		$data['arrY']['Closed']['color'] = "#db0707";
	    		
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Open']['details'][$eachmonth] = 0;
	    			$data['arrY']['Closed']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="1"){
    			$data['arrY']['Open']['color'] = "#1bdb07";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Open']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="2"){
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
    	
    	$this->load->view('staff/projectreport', $data);
        $this->load->view('templates/footer');
    }
    
    // TICKET REPORT
    public function ticketreport(){
    	$this->common();
    	
    	$uid = $this->session->userdata('sid');
	    $urole = $this->session->userdata('srole');
		
		$arrstatus = array(0=>"Not-Started",1=>"Started",2=>"In-Progress",3=>"On-Hold",4=>"Completed");
		$arrmonths = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September",10=>"October",11=>"November",12=>"December");
		
		$overalltotal = 0;
		
		$data = array();
		
		$data['active_inactive_users'] = (isset($_POST['active_inactive_users']))?$_POST['active_inactive_users']:"-1";
		$data['start_date'] = (isset($_POST['start_date']))?$_POST['start_date']:date("m")."/01/".date("Y");
    	$data['end_date'] = (isset($_POST['end_date']))?$_POST['end_date']:date("m/d/Y",strtotime('last day of this month', time()));
    	
    	// QUERY CONDITION
    	$condition = "created_on BETWEEN '".date("Y-m-d H:i:s",strtotime($data['start_date']))."' AND '".date("Y-m-d",strtotime($data['end_date']))." 11:59:59'";
    	if(isset($_POST['active_inactive_users']) && $_POST['active_inactive_users']!="-1"){
    		$condition .= " AND status='".$_POST['active_inactive_users']."'";
    	}
    	
    	if($urole!=1){
			$condition.=" AND (assigned_to='".$uid."' OR projectid IN (SELECT id FROM project WHERE ((accountmanager=".$uid.") or FIND_IN_SET( ".$uid.", projectmanager ))))";
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
    	if($data['active_inactive_users']=="-1"){
    		$data['percentage']['notstarted'] = (is_float(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[0]]*100)/$overalltotal."%";
	    	$data['percentage']['started'] = (is_float(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[1]]*100)/$overalltotal."%";
	    	$data['percentage']['inprogress'] = (is_float(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[2]]*100)/$overalltotal."%";
	    	$data['percentage']['onhold'] = (is_float(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[3]]*100)/$overalltotal."%";
	    	$data['percentage']['completed'] = (is_float(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[4]]*100)/$overalltotal."%";
    	}else{
    		if($data['active_inactive_users']==0){
    			$data['percentage']['notstarted'] = (is_float(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[0]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[0]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==1){
    			$data['percentage']['started'] = (is_float(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[1]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[1]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==2){
    			$data['percentage']['inprogress'] = (is_float(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[2]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[2]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==3){
    			$data['percentage']['onhold'] = (is_float(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[3]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[3]]*100)/$overalltotal."%";
    		}else if($data['active_inactive_users']==4){
    			$data['percentage']['completed'] = (is_float(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal))?number_format(($percentage_ticket[$arrstatus[4]]*100)/$overalltotal,2)."%":($percentage_ticket[$arrstatus[4]]*100)/$overalltotal."%";
    		}
    	}
    	
    	// now set the heading and graph title. if the start and end month is different, then title will be changed. Also set the X and Y data for graph
    	$start_month = date("m",strtotime($data['start_date']));
    	$end_month = date("m",strtotime($data['end_date']));
    	
    	if($end_month==$start_month){
    		$data['heading'] = "TOTAL TICKETS IN A MONTH";
    		$data['graph_title'] = "REPORTS FOR THE MONTH, ".strtoupper(date("F",strtotime($data['start_date'])));
    		
    		if($data['active_inactive_users']=="-1"){
    			$data['arrX'] = array('"Not-Started"', '"Started"', '"In-Progress"', '"On-Hold"', '"Completed"');
    		}else if($data['active_inactive_users']=="0"){
    			$data['arrX'] = array('"Not-Started"');
    		}else if($data['active_inactive_users']=="1"){
    			$data['arrX'] = array('"Started"');
    		}else if($data['active_inactive_users']=="2"){
    			$data['arrX'] = array('"In-Progress"');
    		}else if($data['active_inactive_users']=="3"){
    			$data['arrX'] = array('"On-Hold"');
    		}else if($data['active_inactive_users']=="4"){
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
    		if($data['active_inactive_users']=="-1"){
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
    		}else if($data['active_inactive_users']=="0"){
    			$data['arrY']['Not-Started']['color'] = "#b1b1b1";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Not-Started']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="1"){
    			$data['arrY']['Started']['color'] = "#ff64dd";
	    		
    			foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['Started']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="2"){
    			$data['arrY']['In-Progress']['color'] = "#64d4ff";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['In-Progress']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="3"){
    			$data['arrY']['On-Hold']['color'] = "#ffbf08";
    			
	    		foreach ($tmp_arrX as $eachmonth){
	    			$data['arrY']['On-Hold']['details'][$eachmonth] = 0;
	    		}
    		}else if($data['active_inactive_users']=="4"){
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
    	
    	$this->load->view('staff/ticketreport', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Messages module
     */
    public function messages(){
    	$this->common();
        $id = $this->session->userdata('sid');
        $srole = $this->session->userdata('srole');
        $internal_external = $this->session->userdata('internal_user_external_user');
        $where = array();
        $where = " to='".$id."'";
        /*if($internal_external==1 && $srole==2){
        	// if user is internal and not admin, then show only his blogs
        	$where = " created_by='".$id."'";
        }*/
        $details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        
        foreach ($details as $key=>$values){
        	//$details[$key]->from_email = $this->portalmodel->select_name('user', 'email', "id='".$values->created_by."'");
        	if($values->from_type=='C'){
        		$details[$key]->from_email = $this->portalmodel->select_username("companyname as name", 'customerid', $values->created_by, 'customer');
        	}else{
        		$details[$key]->from_email = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->created_by, 'user');
        	}
        }
        
        $data['details'] = $details;
        $this->load->view('staff/messages', $data);
        $this->load->view('templates/footer');
    }
    
	public function addmessage(){
        $this->common();
        
        $id = $this->session->userdata('sid');
        $srole = $this->session->userdata('srole');
        $internal_external = $this->session->userdata('internal_user_external_user');
        
        $cond = "CONCAT(first_name,' ',last_name) as name";
        $to_user = $this->portalmodel->get_dropdownuser_list('user', 'employeeid', $cond, "id!=".$id,'first_name','ASC');
        
        /*if($srole==1){
        	// no customer to admin
        	$to_customer = $this->portalmodel->get_dropdownuser_list('customer', 'username', "companyname AS name", '','companyname','ASC');
        }else*/
        if ($srole==2 && $internal_external==1){
        	// perticular account manager's customers
        	$to_customer = $this->portalmodel->get_dropdownuser_list('customer', 'username', "companyname AS name", "accountmanagerid='".$id."'",'companyname','ASC');
        }else{
        	// no customer to admin and tech
        	$to_customer = array();
        }
        
        $to_list = array_merge($to_user,$to_customer);
        foreach ($to_list as $key=>$value){
        	if(empty($key)){
        		unset($to_list[$key]);
        	}
        }
        asort($to_list);
        $data['to'] = array_merge(array(''=>"Please Select"),$to_list);
        
        $this->load->view('staff/addmessage',$data);
        $this->load->view('templates/footer');
    }
    
    public function insert_message(){
    	$this->load->model("portalmodel");
        $s_id           = $this->session->userdata('sid');
        $id             = $this->portalmodel->maxid('messages', 'id');
        $num            = $id[0]->id + 1;
        
        
        $to_id = 0;
        //$tmp_to_arr = explode("", $this->input->post('to'));
        $tmp_to_arr[0] = substr($this->input->post('to'), 0, 1);
        
        if($tmp_to_arr[0]=='E'){
        	// take to id from user
        	$to_id = $this->portalmodel->select_name('user', 'id', "employeeid='".$this->input->post('to')."'");
        	$to_type = 'U';
        }else{
        	// its customer
        	$to_id = $to_id = $this->portalmodel->select_name('customer', 'customerid', "username='".$this->input->post('to')."'");
        	$to_type = 'C';
        }
        
        $data           = array(
            'to' => $to_id,
            'subject' => $this->input->post('subject'),
            'message' => $this->input->post('message'),
            'created_by' => $s_id,
        	'from_type' => 'U',
        	'to_type' => $to_type
        );
        $result         = $this->portalmodel->insert_query_('messages', $data);
        
        // now also send email to the provided email for 'To'
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        
    	if($tmp_to_arr[0]=='E'){
        	// take to id from user
        	$to_email = $this->portalmodel->select_name('user', 'email', "employeeid='".$this->input->post('to')."'");
        	
        	// to business email
        	$to_business_email = $this->portalmodel->select_name('user', 'business_email', "employeeid='".$this->input->post('to')."'");
        }else{
        	// its customer
        	$to_email = $this->portalmodel->select_name('customer', 'emailid', "username='".$this->input->post('to')."'");
        	
        	// to business email
        	$to_business_email = $this->portalmodel->select_name('customer', 'business_email', "username='".$this->input->post('to')."'");
        }
        
        if(!empty($to_email) && $to_email!="N/A"){
        	$email = $this->email($to_email, $subject, $message);
        }
        
        if(!empty($to_business_email) && $to_business_email!='N/A'){
        	$email = $this->email($to_business_email, $subject, $message);
        }
        
        redirect("portal/messages");
    }
    
    public function messagedetails(){
    	$this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'messages', $where);
        $this->load->view('staff/messagedetails', $data);
        $this->load->view('templates/footer');
    }
    
    public function update_message(){
    	$this->load->model("portalmodel");
        $id             = $this->input->post('id');
        $s_id           = $this->session->userdata('sid');
        $to				= $this->input->post('to');
        $subject		= $this->input->post('subject');
        $message		= $this->input->post('message');
        $data           = array(
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'created_by' => $s_id
        );
        $result         = $this->portalmodel->update_query('messages', $data, $id, 'id');
        
        redirect("portal/messages");
    }
    
    public function viewmessagedetails(){
    	$this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $details = $this->portalmodel->select_where('', '', 'messages', $where);
    	foreach ($details as $key=>$values){
        	if($values->from_type=='C'){
        		$details[$key]->from_email = $this->portalmodel->select_username("companyname as name", 'customerid', $values->created_by, 'customer');
        	}else{
        		$details[$key]->from_email = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->created_by, 'user');
        	}
        }
        $data['details'] = $details;
        $this->load->view('staff/viewmessagedetails', $data);
        $this->load->view('templates/footer');
    }
    
    public function markMessageAsRead(){
    	$this->load->model("portalmodel");
        
    	$id = $this->input->post('id');
    	
    	$data=array('read_unread'=>'1');
        $result = $this->portalmodel->update_query('messages', $data, $id, 'id');
    }
    
    public function replyTo(){
    	$this->common();
        
    	$id = $this->input->get('id');
    	
    	$s_id           = $this->session->userdata('sid');
    	$srole = $this->session->userdata('srole');
        $internal_external = $this->session->userdata('internal_user_external_user');
    	
    	//$data['reply_to'] = $this->portalmodel->select_name('messages', 'created_by', "id='".$id."'");
    	$subject = $this->portalmodel->select_name('messages', 'subject', "id='".$id."'");
    	$data['subject'] = "Re: ".$subject;
    	
    	$cond = "CONCAT(first_name,' ',last_name) as name";
        $to_user = $this->portalmodel->get_dropdownuser_list('user', 'employeeid', $cond, '','first_name','ASC');
        
        /*if($srole==1){
        	// no customer to admin
        	$to_customer = $this->portalmodel->get_dropdownuser_list('customer', 'username', "companyname AS name", '','companyname','ASC');
        }else*/
        if ($srole==2 && $internal_external==1){
        	// perticular account manager's customers
        	$to_customer = $this->portalmodel->get_dropdownuser_list('customer', 'username', "companyname AS name", "accountmanagerid='".$s_id."'",'companyname','ASC');
        }else{
        	// no customer to admin and tech
        	$to_customer = array();
        }
        
        $to_list = array_merge($to_user,$to_customer);
        foreach ($to_list as $key=>$value){
        	if(empty($key)){
        		unset($to_list[$key]);
        	}
        }
        asort($to_list);
        $data['to'] = array_merge(array(''=>"Please Select"),$to_list);
        
        $created_by = $this->portalmodel->select_name('messages', 'created_by', "id='".$id."'");
        $from_type = $this->portalmodel->select_name('messages', 'from_type', "id='".$id."'");
        if($from_type=='C'){
        	$data['reply_to'] = $this->portalmodel->select_name('customer', 'username', "customerid='".$created_by."'");
        }else{
        	$data['reply_to'] = $this->portalmodel->select_name('user', 'employeeid', "id='".$created_by."'");
        }
        
        $this->load->view('staff/addmessage',$data);
        $this->load->view('templates/footer');
    }
    
    public function sentmessages(){
    	$this->common();
        $id = $this->session->userdata('sid');
        $srole = $this->session->userdata('srole');
        $internal_external = $this->session->userdata('internal_user_external_user');
        $where = array();
        $where = " created_by='".$id."' AND from_type='U'";
        
		$details = $this->portalmodel->select('', '', 'messages', $where,'created_on','DESC');
        
        foreach ($details as $key=>$values){
        	//$details[$key]->from_email = $this->portalmodel->select_name('user', 'email', "id='".$values->created_by."'");
        	if($values->to_type=='C'){
        		$details[$key]->to_name = $this->portalmodel->select_username("companyname as name", 'customerid', $values->to, 'customer');
        	}else{
        		$details[$key]->to_name = $this->portalmodel->select_username("CONCAT(first_name,' ',last_name) as name", 'id', $values->to, 'user');
        	}
        }
        
        $data['details'] = $details;
        $this->load->view('staff/sentmessages', $data);
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
        $this->load->view('staff/viewsentmessagedetails', $data);
        $this->load->view('templates/footer');
    
    }
    
    /**
     * Employees Related Functions
     */
    public function employees(){
    	$this->common();
        $id	= $this->session->userdata('sid');
        
        $condition = "1";
        
		$srole = $this->session->userdata('srole');
		$internal_external = $this->session->userdata('internal_user_external_user');
		
		// show internal employees only and not admin
		$condition.=" AND internal_user_external_user=1";
		
		// if not admin, then don't show the admin users to Employees/Internal Users
		if($internal_external==1 && $srole!=1){
			$condition.=" AND role!=1";
		}
		
        
        $active_inactive = (isset($_GET['active_inactive_users']))?$_GET['active_inactive_users']:0;
        if(isset($active_inactive) && !empty($active_inactive)){
        	$condition.=" AND status=".$active_inactive;
        }
        
        if(empty($condition) || $condition=="1"){
        	$condition = array();
        }
        
        $data['details'] = $this->portalmodel->select('', '', 'user', $condition,'first_name','ASC');
        $data['active_inactive']=$active_inactive;
        $this->load->view('staff/employees', $data);
        $this->load->view('templates/footer');
    }
    
    public function addemployee(){
    	$this->common();
        $this->load->view('staff/addemployee');
        $this->load->view('templates/footer');
    }
    
    public function insert_employee(){
    	if (!(isset($this->session->userdata['sid']))) {
            redirect('portal/logout');
        }
        $this->load->model("portalmodel");
        
        // first check if the user exists or not, and if not, then only add that user
        $email = $this->input->post('emailid');
        //$username = $this->input->post('userid');
        $existing_user_id = $this->portalmodel->select_name('user', 'id', "email='".$email."'");
        
		if(empty($existing_user_id) || $existing_user_id=="N/A"){
			$s_id       = $this->session->userdata('sid');
	        $id         = $this->portalmodel->maxid('user', 'id');
	        $num        = $id[0]->id + 1;
	        $employeeid = 'E00' . $num;
	        $data       = array(
	            'employeeid' => $employeeid,
	            'first_name' => $this->input->post('firstname'),
	            'last_name' => $this->input->post('lastname'),
	            //'username' => $this->input->post('userid'),
	            'email' => $this->input->post('emailid'),
	        	'business_email'=>$this->input->post('business_emailid'),
	            'phone' => $this->input->post('contactno'),
	            'password' => base64_encode($this->input->post('newpassword')),
	            'created_by' => $s_id,
	            'role' => $this->input->post('role'),
	            'status' => $this->input->post('status'),
	            'internal_user_external_user' => $this->input->post('inexuser')
	        );
	        $result     = $this->portalmodel->insert_query_('user', $data);
	        $filename   = strtotime("now");
	        $this->load->library('upload');
	        $number_of_files_uploaded = count($_FILES['files']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
	            $dir_path                       = './user/';
	            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = '/user/' . $filename . '.' . $ext;
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
	                    'userprofile' => $path
	                );
	                $result             = $this->portalmodel->update_query('user', $data1, $employeeid, 'employeeid');
	            endif;
	        endfor;
		}
        redirect("portal/employees");
    }
    
    public function employeedetails(){
    	$this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'user', $where);
        
        // get user files
        $where_files = "fk_user_id='".$id."'";
        $data['contracts_files'] = $this->portalmodel->select_where('', '', 'user_contracts', $where_files);
        $data['waivers_files'] = $this->portalmodel->select_where('', '', 'user_waivers', $where_files);
        $data['insurance_files'] = $this->portalmodel->select_where('', '', 'user_insurance', $where_files);
        $data['certifications_files'] = $this->portalmodel->select_where('', '', 'user_certifications', $where_files);
        $data['other_files'] = $this->portalmodel->select_where('', '', 'user_other', $where_files);
        
        $this->load->view('staff/employeedetails', $data);
        $this->load->view('templates/footer');
    }
    
    public function update_employee(){
    	$this->load->model("portalmodel");
        $id       = $this->input->post('id');
        $s_id     = $this->session->userdata('sid');
        $password = $this->input->post('newpassword');
        if ($password != "") {
            $p = base64_encode($this->input->post('newpassword'));
        } else {
            $p = $this->input->post('password');
        }
        $data     = array(
            'first_name' => $this->input->post('firstname'),
            'last_name' => $this->input->post('lastname'),
            'email' => $this->input->post('emailid'),
        	'business_email'=>$this->input->post('business_emailid'),
            'phone' => $this->input->post('contactno'),
            'created_by' => $s_id,
            'role' => $this->input->post('role'),
            'status' => $this->input->post('status'),
            'internal_user_external_user' => $this->input->post('inexuser'),
            'password' => $p/*,
        	'username'=>$this->input->post('username')*/
        );
        $result   = $this->portalmodel->update_query('user', $data, $id, 'id');
        $filename = strtotime("now");
        $this->load->library('upload');
        $path1 = $this->input->post('path');
        if (!empty($_FILES['files']['name'])) {
            if ($path1 != "") {
                unlink($path1);
            }
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
                    'userprofile' => $path
                );
                $result             = $this->portalmodel->update_query('user', $data1, $id, 'id');
            endif;
        }
        redirect("portal/employees");
    }
    
    /**
     * Tech Profiles Related Functions
     */
	public function techs(){
    	$this->common();
        $id	= $this->session->userdata('sid');
        
        $condition = "1";
        
		$srole = $this->session->userdata('srole');
		$internal_external = $this->session->userdata('internal_user_external_user');
		
		// show external employees/tech only
		$condition.=" AND internal_user_external_user=2";
		
		// if not admin, then don't show the admin users to Employees/Internal Users
		if($internal_external==1 && $srole!=1){
			$condition.=" AND role!=1";
		}
		
        
        $active_inactive = (isset($_GET['active_inactive_users']))?$_GET['active_inactive_users']:0;
        if(isset($active_inactive) && !empty($active_inactive)){
        	$condition.=" AND status=".$active_inactive;
        }
        
        if(empty($condition) || $condition=="1"){
        	$condition = array();
        }
        
        $data['details'] = $this->portalmodel->select('', '', 'user', $condition,'first_name','ASC');
        $data['active_inactive']=$active_inactive;
        $this->load->view('staff/techs', $data);
        $this->load->view('templates/footer');
    }
    
	public function addtech(){
    	$this->common();
        $this->load->view('staff/addtech');
        $this->load->view('templates/footer');
    }
    
    public function insert_tech(){
    	if (!(isset($this->session->userdata['sid']))) {
            redirect('portal/logout');
        }
        $this->load->model("portalmodel");
        
        // first check if the user exists or not, and if not, then only add that user
        $email = $this->input->post('emailid');
        //$username = $this->input->post('userid');
        $existing_user_id = $this->portalmodel->select_name('user', 'id', "email='".$email."'");
        
		if(empty($existing_user_id) || $existing_user_id=="N/A"){
			$s_id       = $this->session->userdata('sid');
	        $id         = $this->portalmodel->maxid('user', 'id');
	        $num        = $id[0]->id + 1;
	        $employeeid = 'E00' . $num;
	        $data       = array(
	            'employeeid' => $employeeid,
	            'first_name' => $this->input->post('firstname'),
	            'last_name' => $this->input->post('lastname'),
	            //'username' => $this->input->post('userid'),
	            'email' => $this->input->post('emailid'),
	        	'business_email'=>$this->input->post('business_emailid'),
	            'phone' => $this->input->post('contactno'),
	            'password' => base64_encode($this->input->post('newpassword')),
	            'created_by' => $s_id,
	            'role' => $this->input->post('role'),
	            'status' => $this->input->post('status'),
	            'internal_user_external_user' => $this->input->post('inexuser')
	        );
	        $result     = $this->portalmodel->insert_query_('user', $data);
	        $filename   = strtotime("now");
	        $this->load->library('upload');
	        $number_of_files_uploaded = count($_FILES['files']['name']);
	        //  upload calls to $_FILE
	        for ($i = 0; $i < $number_of_files_uploaded; $i++):
	            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
	            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
	            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
	            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
	            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
	            $dir_path                       = './user/';
	            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
	            $path                           = '/user/' . $filename . '.' . $ext;
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
	                    'userprofile' => $path
	                );
	                $result             = $this->portalmodel->update_query('user', $data1, $employeeid, 'employeeid');
	            endif;
	        endfor;
		}
        redirect("portal/techs");
    }
    
    public function techdetails(){
    	$this->common();
        $id              = $this->input->get('id');
        $where           = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'user', $where);
        
        // get user files
        $where_files = "fk_user_id='".$id."'";
        $data['contracts_files'] = $this->portalmodel->select_where('', '', 'user_contracts', $where_files);
        $data['waivers_files'] = $this->portalmodel->select_where('', '', 'user_waivers', $where_files);
        $data['insurance_files'] = $this->portalmodel->select_where('', '', 'user_insurance', $where_files);
        $data['certifications_files'] = $this->portalmodel->select_where('', '', 'user_certifications', $where_files);
        $data['other_files'] = $this->portalmodel->select_where('', '', 'user_other', $where_files);
        
        $this->load->view('staff/techdetails', $data);
        $this->load->view('templates/footer');
    }
    
    public function update_tech(){
    	$this->load->model("portalmodel");
        $id       = $this->input->post('id');
        $s_id     = $this->session->userdata('sid');
        $password = $this->input->post('newpassword');
        if ($password != "") {
            $p = base64_encode($this->input->post('newpassword'));
        } else {
            $p = $this->input->post('password');
        }
        $data     = array(
            'first_name' => $this->input->post('firstname'),
            'last_name' => $this->input->post('lastname'),
            'email' => $this->input->post('emailid'),
        	'business_email'=>$this->input->post('business_emailid'),
            'phone' => $this->input->post('contactno'),
            'created_by' => $s_id,
            'role' => $this->input->post('role'),
            'status' => $this->input->post('status'),
            'internal_user_external_user' => $this->input->post('inexuser'),
            'password' => $p/*,
        	'username'=>$this->input->post('username')*/
        );
        $result   = $this->portalmodel->update_query('user', $data, $id, 'id');
        $filename = strtotime("now");
        $this->load->library('upload');
        $path1 = $this->input->post('path');
        if (!empty($_FILES['files']['name'])) {
            if ($path1 != "") {
                unlink($path1);
            }
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
                    'userprofile' => $path
                );
                $result             = $this->portalmodel->update_query('user', $data1, $id, 'id');
            endif;
        }
        redirect("portal/techs");
    }
    
    public function tasknotifications(){
    	$this->common();
        
    	$id = $this->session->userdata('sid');
        $srole = $this->session->userdata('srole');
        $customer_or_employee_id = $this->session->userdata('customer_or_employee_id');
        $internal_external = $this->session->userdata('internal_user_external_user');
        
        $where = array();
        $where = " fk_customer_or_employee_id='".$customer_or_employee_id."' AND user_type='E'";
        
        $details = $this->portalmodel->select('', '', 'task_notifications', $where,'created_on','DESC');
        
    	foreach ($details as $key=>$values){
        	$details[$key]->ticket_id = $this->portalmodel->select_username("taskid as name", 'id', $values->fk_task_id, 'task');
        }
        
        $data['details'] = $details;
        
        $this->load->view('staff/tasknotifications', $data);
        $this->load->view('templates/footer');
    }
    
    public function markTicketNotificationAsRead(){
    	$this->load->model("portalmodel");
        
    	$id = $this->input->post('id');
    	
    	$data=array('read_unread'=>'1');
        $result = $this->portalmodel->update_query('task_notifications', $data, $id, 'id');
    }
    
    /**
     * INVOICES
     */
    public function invoices(){
		$this->common();
		$id = $this->session->userdata('sid');
		$sessionrole = $this->session->userdata('srole');
        
		$where = "created_by='".$id."'";
		$list  = $this->portalmodel->select_where_cond('', '', 'invoices', $where,"id","DESC");
        
        $li = array();
        if (!empty($list)) {
            foreach ($list as $d) {
                $row['id'] = $d->id;
                $row['invoice_number'] = $d->invoice_number;
                
                $customer = "customerid=".$d->fk_client_id;
                $row['customer']  = $this->portalmodel->select_name('customer', 'companyname', $customer);
                
                $row['fk_task_id'] = $d->fk_task_id;
                if(isset($d->fk_task_id) && !empty($d->fk_task_id)){
	                $condition_task_id = 'id='.$d->fk_task_id;
	                $row['ticket_id'] = $this->portalmodel->select_name('task', 'taskid', $condition_task_id);
	                $row['task'] = $this->portalmodel->select_name('task', 'title', $condition_task_id);
                }else {
                	$row['ticket_id'] = "";
                	$row['task'] = "";
                }
                
                //$row['task']      = $d->task;
                $row['invoice_date']    = date("d M, Y",strtotime($d->invoice_date));
                $row['amount'] = $d->amount;
                $row['remaining_amount'] = $d->remaining_amount;
                $row['invoice_status'] = ($d->invoice_status=="U")?"Un-paid":"Paid";
                $row['description'] = $d->description;
                
                
                $li[] = $row;
            }
        }
        $data['list'] = $li;
        $this->load->view('staff/invoices', $data);
        $this->load->view('templates/footer');
    }
    
    public function addinvoice(){
    	$this->common();
        
    	$uid        = $this->session->userdata('sid');
	    $urole      = $this->session->userdata('srole');
	    
	    
    	
    	$data['customer']  = $this->portalmodel->get_dropdown_list('customer', 'customerid', 'companyname', "accountmanagerid='".$uid."'",'companyname','ASC');
    	
        $this->load->view('staff/addinvoice', $data);
        $this->load->view('templates/footer');
    }
    
    public function insert_invoice(){
    	$this->load->model("portalmodel");
        $s_id      = $this->session->userdata('sid');
        
        $invoice_number = $this->input->post('invoice_number');
        $fk_client_id = $this->input->post('fk_client_id');
        $fk_task_id = $this->input->post('fk_task_id');
        //$task = $this->input->post('task');
        $invoice_date  = date('Y-m-d', strtotime($this->input->post('dob')));
        $amount = $this->input->post('amount');
        $remaining_amount = $this->input->post('amount');
        $invoice_status = $this->input->post('invoice_status');
        $description = $this->input->post('desc');
        
        $data     = array(
            'invoice_number' => $invoice_number,
            'fk_client_id' => $fk_client_id,
        	'fk_task_id' => $fk_task_id,
            //'task' => $task,
            'invoice_date' => $invoice_date,
            'amount' => $amount,
        	'remaining_amount' => $remaining_amount,
            'invoice_status' => $invoice_status,
            'description' => $description,
            'created_by' => $s_id
        );
        
        $result   = $this->portalmodel->insert_query_('invoices', $data);
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
            $dir_path                       = './projects/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/projects/' . $filename . $i . '.' . $ext;
            $config                         = array(
                'file_name' => $filename . $i,
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
                $data1              = array(
                    'fk_invoice_id' => $result,
                    'file' => $path,
                    'created_by' => $s_id
                );
                $result1            = $this->portalmodel->insert_query_('invoice_files', $data1);
            endif;
        endfor;
        redirect("portal/invoices");
    }
    
    public function invoicedetails(){
    	$this->common();
        
    	$uid        = $this->session->userdata('sid');
	    $urole      = $this->session->userdata('srole');
    	
    	$id = $this->input->get('id');
        $where = "id=$id";
        $data['details'] = $this->portalmodel->select_where('', '', 'invoices', $where);
        
        $where1 = "customerid=".$data['details'][0]->fk_client_id;
        $combined = "CONCAT(taskid,' - ',title) AS name";
        $data['tickets'] = $this->portalmodel->get_dropdownuser_list('task', 'id', $combined, $where1);
        
        $files = "fk_invoice_id=" . $data['details'][0]->id;
        $data['files']    = $this->portalmodel->select_where('', '', 'invoice_files', $files);
        
        $data['customer']  = $this->portalmodel->get_dropdown_list('customer', 'customerid', 'companyname', "accountmanagerid='".$uid."'",'companyname','ASC');
        
        // get received payments.
        $where_invoice_paid = "fk_invoice_id='".$data['details'][0]->id."'";
        $data['received_payments'] = $this->portalmodel->select_where_cond('', '', 'invoice_paid', $where_invoice_paid,"paid_date","DESC");
        
        
        $this->load->view('staff/invoicedetails', $data);
        $this->load->view('templates/footer');
    }
    
    public function update_invoice(){
    	$this->load->model("portalmodel");
    	
    	$s_id      = $this->session->userdata('sid');
        
    	$id = $this->input->post('id');
        $invoice_number = $this->input->post('invoice_number');
        $fk_client_id = $this->input->post('fk_client_id');
        $fk_task_id = $this->input->post('fk_task_id');
        //$task = $this->input->post('task');
        $invoice_date  = date('Y-m-d', strtotime($this->input->post('dob')));
        $amount = $this->input->post('amount');
        $remaining_amount = $this->input->post('remaining_amount');
        $invoice_status = $this->input->post('invoice_status');
        $description = $this->input->post('desc');
    	
        $data     = array(
            'invoice_number' => $invoice_number,
            'fk_client_id' => $fk_client_id,
        	'fk_task_id' => $fk_task_id,
            //'task' => $task,
            'invoice_date' => $invoice_date,
            'amount' => $amount,
        	'remaining_amount'=>$remaining_amount,
            'invoice_status' => $invoice_status,
            'description' => $description,
        	'modified_on'=>date("Y-m-d H:i:s"),
            'modified_by' => $s_id
        );
        
        $result = $this->portalmodel->update_query('invoices', $data, $id, 'id');
        
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
            $dir_path                       = './projects/';
            $ext                            = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION); //end(explode(".", $_FILES['files']['name'][$i]));
            $path                           = '/projects/' . $filename . $i . '.' . $ext;
            $config                         = array(
                'file_name' => $filename . $i,
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
                $data1              = array(
                    'fk_invoice_id' => $id,
                    'file' => $path,
                    'created_by' => $s_id
                );
                $result1    = $this->portalmodel->insert_query_('invoice_files', $data1);
            endif;
        endfor;
        
        // now also save payments added
        for ($i=0;$i<$_POST['num_payment'];$i++){
        	if((isset($_POST['payment_task'][$i]) && !empty($_POST['payment_task'][$i])) && (isset($_POST['payment_date'][$i]) && !empty($_POST['payment_date'][$i])) && (isset($_POST['payment_amount'][$i]) && !empty($_POST['payment_amount'][$i]))){
        		$data2 = array(
                    'fk_invoice_id' => $id,
                    'task' => $_POST['payment_task'][$i],
                    'paid_date' => date("Y-m-d",strtotime($_POST['payment_date'][$i])),
        			'amount'=>$_POST['payment_amount'][$i]
                );
                $result2 = $this->portalmodel->insert_query_('invoice_paid', $data2);
        	}
        }
        
        redirect("portal/invoices");
    }
    
    public function updateticketdropdown(){
    	$this->load->model("portalmodel");
        $customer_id = $this->input->post('customer_id');
        
        $where = "customerid=$customer_id";
        $combined = "CONCAT(taskid,' - ',title) AS name";
        $tickets = $this->portalmodel->get_dropdownuser_list('task', 'id', $combined, $where);
        
        echo form_dropdown('fk_task_id', $tickets,'','class="form-control" id="fk_task_id"');
    }
    
    public function paymenthistory(){
		$this->common();
		$id = $this->session->userdata('sid');
		$sessionrole = $this->session->userdata('srole');
        
		$where = array();
    	
		$data['fk_invoice_id'] = (isset($_GET['fk_invoice_id']))?$_GET['fk_invoice_id']:"";
        if(isset($data['fk_invoice_id']) && !empty($data['fk_invoice_id'])){
        	$where = "fk_invoice_id=".$data['fk_invoice_id'];
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
        $where_invoices = "created_by='".$id."'";
        $data['invoices'] = $this->portalmodel->get_dropdownuser_list('invoices', 'id', "invoice_number AS name", $where_invoices, "invoice_number","ASC");
        
        $this->load->view('staff/paymenthistory', $data);
        $this->load->view('templates/footer');
    }
}