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
            $data['countoftask'] = $count;
            $data['open']=$open;
            $data['logged_in_username']= $this->session->userdata('first_name');
            $data['logged_in_userid'] = $uid;
            $data['current_number_of_comments'] = $this->session->userdata('current_number_of_comments');
            $data['current_number_of_workorder_comments'] = $this->session->userdata('current_number_of_workorder_comments');
            $data['internal_user_external_user'] = $this->session->userdata('internal_user_external_user');
            $data['urole'] = $urole;
            
            // now get project names for chat
	        if ($urole == 1) {
	            $list = $this->portalmodel->select('', '', 'project','','id','DESC');
	        } else {
	            $where = "(accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer ) ";
	            
	            // for newly added members condition
	            $where.=" OR id IN (SELECT fk_project_id FROM chat_project_members WHERE fk_user_id='".$uid."')";
	            
	            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"id","DESC");
	        }
	        
	        $chat_project_list = array();
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
            $this->session->set_flashdata('error_message', 'Please enter valid User Name or Password!'); //display the flashdata using session
            redirect('portal/index'); //user is not valid so goto login page again                  
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
        //$this->session->sess_destroy();
        $this->session->set_flashdata("logoutmsg", "You Have Been Successfully Logged Out.");
        redirect("portal/index");
    }
    public function email($to, $subject, $message)
    {
        $config['wordwrap'] = TRUE;
        $config['newline']  = '\n';
        $config['mailtype'] = 'html';
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('info@synergyit.ca', 'Synergy IT');
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
                $l['taskid']   = $d->taskid;
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
        if($internal_user_external_user==2){
        	// only assigned
        	$condition_workorder_comments = "fk_workorder_id IN (SELECT id_workorder FROM workorder WHERE fk_assigned_to='".$id."')";
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
                        $role = "Developer/Technician";
                    else
                        $role = $role . "," . "Developer/Technician";
                }
                $row['role'] = $role;
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
        $data['developer'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '');
        $data['customer']  = $this->portalmodel->get_dropdown_list('customer', 'customerid', 'companyname', '');
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
        $deveoper = implode(",", $deveoper);
        
        
        
        /*foreach ($this->input->post('developer') as $dev) {
            $deveoper = $dev . ',' . $deveoper;
        }*/
        $data     = array(
            'customerid' => $this->input->post('customer'),
            'title' => $this->input->post('title'),
            'accountmanager' => $this->input->post('accountmanager'),
            'projectmanager' => $this->input->post('projectmanager'),
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
        $data['developer'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '');
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
        $deveoper = implode(",", $deveoper);
        
        $data   = array(
            'title' => $this->input->post('title'),
            'projectmanager' => $this->input->post('projectmanager'),
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
        $list             = $this->portalmodel->select_where_cond('', '', 'task', $where,"taskid","desc");
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
		if($urole==1){
			if($ticket=="open"){
				$where      = '1'.$cond;
			}else{
				$where=array();
			}
			
		}
		else { 
			// also get tasks for only assigned projects
            //$condition_assigned_projects=" OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ) or FIND_IN_SET( $uid, developer ))) ";
            $condition_assigned_projects=" OR projectid IN (SELECT id FROM project WHERE ((accountmanager=" . $uid . ") or FIND_IN_SET( $uid, projectmanager ))) ";
            
			if($ticket=="open"){
				$where      = " assigned_to='".$uid."'".$condition_assigned_projects.$cond;
			}else{
				$where      = " assigned_to='".$uid."'".$condition_assigned_projects.$cond;
			}
		}
        $list             = $this->portalmodel->select_where_cond('', '', 'task', $where,"taskid","desc");
        

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
        $where8             = "id in (" . $prj[0]->developer . ")";
        if(isset($list[0]->assigned_to) && !empty($list[0]->assigned_to)){
        	$where8.=" OR id in (".$list[0]->assigned_to.")";
        }
        $data['assignedto'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $name, $where8);
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
							<div class="form-group">
                                            <label style="margin-left: 15px;">File input</label>
											<div class="ml-15" id="fileadd0">
											<input type="file" name="files[]" id="upload" class="pull-left">
	                                            <button class="btn btn-danger btn-xs" onclick="removefile(0)"><i class="fa fa-trash-o "></i></button> 
	                                        </div>
                                        </div>
                            <div  id="addfiles">
                                        </div>
                            <input type="hidden" name="num" id="num" value="1" >
                            <button type="button" class="btn btn-default btn-xs " onclick="addMoreFiles()">Add More</button><br/>';
        if ($show == "0") {
            $li .= ' <label>Show Customer</label>&nbsp;
                            <input type="radio" name="custshow" id="cust_show" value="0" checked="checked" >Yes &nbsp;
                            <input type="radio" name="custshow" id="cust_show" value="1" >No';
        }else{
        	$li .= '<input type="hidden" name="custshow" id="cust_show" value="1" >';	// defualting a comment to not show, as it gives error when the task is set to not show to customer
        }
        $li .= '  <div  class="text-center">
                                <button type="submit" name="submits" id="submits" class="btn btn-round btn-success btn-sm">Submit</button>
                            </div>
                        </div> 
                    </div>';
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
        $taskid    = 'S00' . $num;
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
        $where1       = 'id in ('.$accid.','.$pmid.')';//'id in ('.$devid.','.$accid.','.$pmid.')';
		
        $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if (!empty($tolist)) {
            foreach ($tolist as $d){ 
			//echo $d->email.'<br/>';
         $email=$this->email($d->email, $subject, $messagestaff);
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
        }
        
        if($status==4 && $this->input->post('cust_show')==0 && !empty($customerid)){
        	// get customer's email id
        	$cust_email = $this->portalmodel->select_name("customer","emailid","customerid='".$customerid."'");
        	if(!empty($cust_email)){
        		$subject = "Task Completed ".$taskid;
	        	$message = 'Dear Customer,<br/><br/>This ticket <b>' . $taskid . '</b> has been completed. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/><br/><br/>Thank you,<br/>- Synergy IT Team';
		        $email = $this->email($cust_email, $subject, $message);
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
                'allowed_types' => 'jpg|jpeg|png|gif',
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
        $message = 'Dear Customer,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';
        if ($show == 0) {
            $where = 'customerid=' . $task[0]->customerid;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
        }
		 $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
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
                'allowed_types' => 'jpg|jpeg|png|gif',
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
        $message = 'Dear Customer,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';
        if ($show == 0) {
            $where = 'customerid=' . $task[0]->customerid;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
        }
		        $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
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
            $list = $this->portalmodel->select('', '', 'customer','','customerid','DESC');
        } else {
            $where = "accountmanagerid= $id";
            //$list  = $this->portalmodel->select_where('', '', 'customer', $where);
            $list = $this->portalmodel->select('', '', 'customer',$where,'customerid','DESC');
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
                } else {
                    $row['status'] = 'In-Active';
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
        $data['accountmanager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '');
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
        $data   = array(
            'first_name' => $this->input->post('firstname'),
            'last_name' => $this->input->post('lastname'),
            'emailid' => $this->input->post('emailid'),
            'contactno' => $this->input->post('contactno'),
            'modified_by' => $s_id,
            'companyname' => $this->input->post('company'),
            'status' => $this->input->post('status'),
            'accountmanagerid' => $this->input->post('accountmanager'),
            'password' => $p
        );
        $result = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
		$data1=array('accountmanager'=>$this->input->post('accountmanager'));
		$result = $this->portalmodel->update_query('project ', $data1, $id, 'customerid');
		
        redirect("portal/customers");
    }
    function addcustomer()
    {
        $this->common();
        $cond                   = "CONCAT(first_name,' ',last_name) as name";
        $data['accountmanager'] = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, '');
        $this->load->view('staff/addcustomer', $data);
        $this->load->view('templates/footer');
    }
    function insert_customer()
    {
        $this->load->model("portalmodel");
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
            'contactno' => $this->input->post('contactno'),
            'password' => base64_encode($this->input->post('newpassword')),
            'created_by' => $s_id,
            'accountmanagerid' => $this->input->post('accountmanager'),
            'status' => $this->input->post('status')
        );
        $result     = $this->portalmodel->insert_query_('customer', $data);
        redirect("portal/customerdetails?id=" . $num);
    }
    function users()
    {
        $this->common();
        $id              = $this->session->userdata('sid');
        
        $condition = array();
        $active_inactive = (isset($_GET['active_inactive_users']))?$_GET['active_inactive_users']:0;
        if(isset($active_inactive) && !empty($active_inactive)){
        	$condition="status=".$active_inactive;
        }
        
        $data['details'] = $this->portalmodel->select('', '', 'user', $condition,'created_on','DESC');
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
            	'skills' => $this->input->post('skills'),
                'modified_by' => $this->session->userdata('sid')
            );
            $update = $this->portalmodel->update_query('user', $data, $id, 'id');
            $this->session->set_flashdata('success', 'Sucessfully updated');
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
	        	echo $to."<br />".$subject."<br />".$message; exit;
	        	$email = $this->email($to, $subject, $message);
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
        
		$where2             = "id in ($dev)";
        $cond               = "CONCAT(first_name,' ',last_name) as name";
        $developer  = $this->portalmodel->get_dropdownuser_list('user', 'id', $cond, $where2);
        
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
	    		$subject = "Portal : Reset Password!";
	    		$message = "Hello ".$user_name.",<br /><br />";
	    		$message .= "We have received your request to reset your password!<br /><br />";
	    		$message .= "Click on below link or copy and paste it in browser to reset your password.<br /><br />";
	    		$message .= "<a href=".$link." target='_blank'>".$link."</a><br /><br />";
	    		$message .= "Thank You<br />- Synergy IT Team.";
	    		
	    		$forgotpassword_send_email = $this->email($to, $subject, $message);
	    		
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'We have sent you an email to reset your password!'); //display the flashdata using session
	            redirect('portal/index'); //user is not valid so goto login page again
	    		
	    	}else{
	    		// set error message
	    		$this->session->set_flashdata('error_message', 'Email not registered!'); //display the flashdata using session
	            redirect('portal/index'); //user is not valid so goto login page again
	    	}
    	}else{
    		// set error message
    		$this->session->set_flashdata('error_message', 'Please enter valid Email!'); //display the flashdata using session
            redirect('portal/index'); //user is not valid so goto login page again
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
            redirect('portal/index'); //user is not valid so goto login page again
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
	        redirect('portal/index');
    	}
    }
    
	/**
     * following functin will add member to project chat
     */
    function add_customer_to_project_chat(){
    	$this->load->model("portalmodel");
    	
    	$project_id = $this->input->post('project_id');
    	$customer_email = $this->input->post('customer_email');
    	$customer_password = $this->input->post('customer_password');
    	
    	// check if record already exists
    	$id = $this->portalmodel->select_name("customer_user_project_chat","id","email='".$customer_email."'");
    	if(empty($id) || $id=="N/A"){
    		// add new record
    		$data_add_member = array(
	        	'project_ids'=>$project_id,
	        	'email'=>$customer_email,
    			'password'=>base64_encode($customer_password)
	        );
	        
	        $result = $this->portalmodel->insert_query_('customer_user_project_chat', $data_add_member);
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
}