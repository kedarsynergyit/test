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
            $where      = " customerid='$uid' and show_customer=0";
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
            if($is_customer){
	            $where = "customerid=$uid";
	            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
	            $chat_project_list = array();
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
            }else{
            	// its a user only for chat
            	$project_ids = $this->portalmodel->select_name("customer_user_project_chat","project_ids","id='".$uid."'");
            	if(!empty($project_ids) && $project_ids!="N/A"){
            		$where = "id IN ($project_ids)";
		            $list  = $this->portalmodel->select_where_cond('', '', 'project', $where,"created_on","DESC");
		            $chat_project_list = array();
		            
		            if (!empty($list)) {
			        	foreach ($list as $d) {
			        		$row = array();
			        		
			        		$row['project_id'] = $d->id;
			        		$row['project_name'] = $d->title;
			        		
			        		$chat_project_list[] = $row;
			        	}
		            }
            	}
            }
            
	        $data['chat_project_list'] = $chat_project_list;
	        /* getting chat project list ends */
            
	        $data['is_customer'] = $is_customer;
            $data['countoftask'] = $count;
             $data['open']=$open;
             $data['logged_in_clientname']= $this->session->userdata('customer_name');
             $data['logged_in_userid'] = $uid;
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
            redirect('customerportal/dashboard'); //user is valid and role=0 so goto dashboard
        }
    }
    public function logout()
    {
    	
    	// delete this user from onlinemembers table.
    	$this->load->model("portalmodel");
        $uid = $this->session->userdata('id');
        $this->portalmodel->deleteid("onlinecustomers","customer_id",$uid);
    	
        $this->session->unset_userdata('name');
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('current_number_of_comments_client');
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
        $id                 = $this->session->userdata('id');
        $where              = " customerid='$id'";
        $accmgr             = $this->portalmodel->select_name('customer', 'accountmanagerid', $where);
        $cond               = "id='$accmgr'";
        $data['accdetails'] = $this->portalmodel->select_where_cond('', '', 'user', $cond, '', '');
        $where1             = " customerid='$id'";
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
        
        $this->load->view('dashboard', $data);
    }
    function projects()
    {
        $this->common();
        $id    = $this->session->userdata('id');
        $where = "customerid=$id";
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
        
        // show open / completed task based on the passed parameter
        if(null!=$this->input->get('show')){
        	if($this->input->get('show')=="open"){
        		$where.=" AND status IN (0,1,2,3)";
        	}else if($this->input->get('show')=="completed"){
        		$where.=" AND status IN (4)";
        	}
        }
        
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
                $li[] = $row;
            }
        }
        $data['list'] = $li;
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
         $id             = $this->session->userdata('id');
        $where            = "customerid='".$id."' and show_customer=0".$cond;
        $list             = $this->portalmodel->select_where_cond('', '', 'task', $where,"taskid","desc");
        
        
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
                $li[] = $row;
            }
        }
        $data['list'] = $li;
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
                            <button type="button" class="btn btn-default btn-xs " onclick="addMoreFiles()">Add More</button>
                    
                            <div  class="text-center">
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
        $where2           = "customerid=$s_id";
        $data['project']  = $this->portalmodel->get_dropdown_list('project', 'id', 'title', $where2);
        $s_id             = $this->session->userdata('id'); //session ID
        $where2           = "customerid=$s_id";
        $data['priority'] = $this->portalmodel->get_dropdown_list('taskpriority', 'id', 'priority', '');
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
        $taskid   = 'S00' . $num;
        $data     = array(
            'taskid' => $taskid,
            'projectid' => $prjid,
            'customerid' => $s_id,
			'expected_date' => $datetime,
            'expected_end' => $datetime1,
            'title' => $this->input->post('title'),
            'priority' => $this->input->post('priority'),
            'description' => $this->input->post('desc'),
            'status' => 0,
            'show_customer' => 0,
            'c_by' => 0,
            'created_by' => $s_id
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
		
		  $subject = "New Task " . $taskid;
        
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
        
        $messagestaff = 'Hi there,<br/><br/>We have received support request ticket ID of <b>' . $taskid . '</b> .Please respond to ticket shortly.<br/>
<br/><br/>
Thank you,<br/>

- Synergy IT Team
';

// now get accountmanager and projectmanager from $prjid;
$where1 = "id=$prjid";
$prj = $this->portalmodel->select_where('', '', 'project', $where1);
        
$accid=$prj[0]->accountmanager;//$this->input->post('accountmanager');
$pmid=$prj[0]->projectmanager;//$this->input->post('projectmanager');
        $where1       = 'id in ('.$accid.','.$pmid.')';
		
        $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if (!empty($tolist)) {
            foreach ($tolist as $d){ 
			//echo $d->email.'<br/>';
         $email=$this->email($d->email, $subject, $messagestaff);
		 }
		}
		
		
		
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
        $message = 'Dear Customer,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';
      
            $where = 'customerid=' . $s_id;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
       
		 $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';

$devid=$this->input->post('assignedto');
$accid=$this->input->post('accountmanager');
$pmid=$this->input->post('projectmanager');
if($devid==''||$devid=="N/A")
{
	$where1       = 'id in ('.$accid.','.$pmid.')';
}
else{
	$where1       = 'id in ('.$devid.','.$accid.','.$pmid.')';
}

        
		 $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if (!empty($tolist)) {
            foreach ($tolist as $d){ 
			//echo $d->email.'<br/>';
         $email=$this->email($d->email, $subject, $messagestaff);
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
                'allowed_types' => 'jpg|jpeg|png|gif|pdf|docx',
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
                    'created_by' => $this->session->userdata('id')
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
      
            $where = 'customerid=' . $s_id;
            $to    = $this->portalmodel->select_name('customer', 'emailid', $where);
            if ($to != "") {
                $email = $this->email($to, $subject, $message);
            }
       
		 $messagestaff = 'Hi there,<br/><br/>This ticket <b>' . $task[0]->taskid . '</b> has been updated. Please login to the portal for the details.<a href="http://portal.synergyit.ca">http://portal.synergyit.ca</a><br/>
<br/><br/>
Thank you,<br/>
- Synergy IT Team
';

$devid=$this->input->post('assignedto');
$accid=$this->input->post('accountmanager');
$pmid=$this->input->post('projectmanager');
if($devid==''||$devid=="N/A")
{
	$where1       = 'id in ('.$accid.','.$pmid.')';
}
else{
	$where1       = 'id in ('.$devid.','.$accid.','.$pmid.')';
}

        
		 $tolist = $this->portalmodel->select_where('','','user',  $where1);
		if (!empty($tolist)) {
            foreach ($tolist as $d){ 
			//echo $d->email.'<br/>';
         $email=$this->email($d->email, $subject, $messagestaff);
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
                'modified_by' => $this->session->userdata('id')
            );
            $update = $this->portalmodel->update_query('customer', $data, $id, 'customerid');
            $this->session->set_flashdata('success', 'Sucessfully updated');
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
            }
            $this->session->set_flashdata('failed', 'Please enter correct current password');
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
}