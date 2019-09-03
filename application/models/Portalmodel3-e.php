<?php

Class Portalmodel extends CI_Model
{

    function login_validate()
    {
         $customername = $this->input->post('userid');
        $customerpassword = $this->input->post('pswd');
        $this->db->select("*");
        $this->db->from("customer");
        $this->db->where('emailid', $customername);
        $this->db->where('password', base64_encode($customerpassword));
        $this->db->where('status', '1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $data = array(
                'name' => $row->username,
            	'customer_name' => $row->first_name,
                'id' => $row->customerid,
                'user_email' => $row->emailid,
            );
            
            // now add this customer's id to online customers to show them with green dot for chat.
            $onlinemembers = array("customer_id"=>$row->customerid);
            $this->insert_query_('onlinecustomers', $onlinemembers);
            
        	// also get count of current number of comments
        	$condition_customer_projects = " project_id IN (SELECT id FROM project WHERE customerid = '".$row->customerid."') AND show_customer=0";
        	$this->db->select("count(id) AS number_of_comments");
	        $this->db->from("taskcomments");
	        $this->db->where($condition_customer_projects);
	        $query = $this->db->get();
	        $query->num_rows();
	        if ($query->num_rows() > 0) {
	        	$row = $query->result();
	        	$tmpdata= $row[0]->number_of_comments;
	            $data['current_number_of_comments_client'] = $tmpdata;
	        }
            
	        $data['is_customer']=1;
	        
            $this->session->set_userdata($data);
            return true;
        } else {
        	
        	// check for project chat customer user
        	$customeremail = $this->input->post('userid');
	        $customerpassword = $this->input->post('pswd');
	        $this->db->select("*");
	        $this->db->from("customer_user_project_chat");
	        $this->db->where('email', $customeremail);
	        $this->db->where('password', base64_encode($customerpassword));
	        $query = $this->db->get();
	        if ($query->num_rows() > 0) {
	        	$row = $query->row();
	            $data = array(
	                'name' => $row->email,
	            	'customer_name' => $row->email,
	                'id' => $row->id,
	                'user_email' => $row->email,
	            );
	            
	            $data['is_customer']=0;
	            
	            $this->session->set_userdata($data);
            	return true;
	        }else{
        		return false;
	        }
        }
    }
    
    function login_staff() {
         $username = $this->input->post('userid');
         $password = $this->input->post('pswd');
        $this->db->select("*");
        $this->db->from("user");
        $this->db->where('username', $username);
        $this->db->where('password', base64_encode($password));
        $this->db->where('status', '1');
          $query = $this->db->get();
           $query->num_rows();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $data = array(
                'sname' => $row->username,
            	'first_name' => $row->first_name,
                'sid' => $row->id,
                'srole' => $row->role,
                'suser_email' => $row->email,
                'internal_user_external_user' => $row->internal_user_external_user
            );
            
            // now add this user's id to online members to show them with green dot for chat.
            $onlinemembers = array("user_id"=>$row->id);
            $this->insert_query_('onlinemembers', $onlinemembers);
            
            if($row->role>1){
            	// get current assigned projects
            	$this->db->select("id");
		        $this->db->from("project");
		        $where = " ((accountmanager=" . $row->id . ") or FIND_IN_SET( $row->id, projectmanager ) or FIND_IN_SET( $row->id, developer ))";
		        $this->db->where($where);
		        $query = $this->db->get();
		        $query->num_rows();
		        $tmpdata=array();
		        if ($query->num_rows() > 0) {
		        	foreach ($query->result() as $row) {
		                $tmpdata[] = $row->id;
		            }
		            
		            $data['current_assigned_projects'] = $tmpdata;
		        }
            }
            
            // also get count of current number of comments
        	$this->db->select("count(id) AS number_of_comments");
	        $this->db->from("taskcomments");
	        $query = $this->db->get();
	        $query->num_rows();
	        if ($query->num_rows() > 0) {
	        	$row = $query->result();
	        	$tmpdata= $row[0]->number_of_comments;
	            $data['current_number_of_comments'] = $tmpdata;
	        }
	        
        	// also get count of current number of workorder comments
        	$this->db->select("count(id) AS number_of_comments");
	        $this->db->from("workordercomments");
	        $query = $this->db->get();
	        $query->num_rows();
	        if ($query->num_rows() > 0) {
	        	$row = $query->result();
	        	$tmpdata= $row[0]->number_of_comments;
	            $data['current_number_of_workorder_comments'] = $tmpdata;
	        }
            $session = $this->session->set_userdata($data);
           return 'yes';
        } else {
            return 'fail';
        }
    }
    function deleteid($table,$cond,$id)
    {
    $this->db->where($cond,$id);
   $this->db->delete($table);
   return true;
 // return $this->output->enable_profiler(TRUE);;
    }
  
    public function record_count($tablename) {
        return $this->db->count_all($tablename);
    }
    public function record_count_where($tablename,$where) {
        if($where !='' && $where!='1'){
        $this->db->where($where);
        }$this->db->from($tablename);
        $count = $this->db->count_all_results();
        return $count;
    }
    public function select($limit, $start,$tablename,$where="",$orderby="",$order="") {
        $this->db->limit($limit, $start);
        if(!empty($where)){
        	$this->db->where($where);
        }
        
        if(!empty($orderby)){
        	$this->db->order_by($orderby,$order);
        }
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
   }
   public function select_group($table,$names,$groupby,$where)
   {
       $this->db->select($names); 
	   if($where!=""){
	   $this->db->where($where);    }
         $this->db->group_by($groupby); 
        $query = $this->db->get($table);
       if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $query->result() ;
       }
      return false;
   
   }

   public function select_where_cond($limit, $start,$tablename,$where,$orderby,$order){
      $this->db->limit($limit, $start);
       $this->db->where($where);       
        $this->db->order_by($orderby,$order);
        
        $query = $this->db->get($tablename); 
       
       if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $query->result() ;
       }
      return false;
   }
    public function select_where($limit, $start,$tablename,$where) {
        $this->db->limit($limit, $start);
        $this->db->where($where);
         $this->db->order_by("","asc");
        $query = $this->db->get($tablename);
       if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $query->result() ;
       }
      return false;
    }
    function get_dropdown_list($table,$id,$name,$where)
    {
        $select=$id.','.$name;
        $this->db->select($select); //change this to the two main values you want to use
        $this->db->from($table);
    if($where!=""){
        $this->db->where($where);
    }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
        $dropdowns = $query->result();
        $data[''] = 'Please Select'; 
            foreach($query->result_array() as $row){
                $data[$row[$id]]=$row[$name];
                }
            return $data;
        } else { return false;    }     // return false if no items for dropdown         
    }
     function get_dropdownuser_list($table, $id, $name, $where) {
        $select = $id . ',' . $name;
        $this->db->select($select); 
        $this->db->from($table);
        if ($where != "") {
            $this->db->where($where);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $dropdowns = $query->result();
            $data[''] = 'Please Select';
            foreach ($query->result_array() as $row) {
                $data[$row[$id]] = $row['name'];
            }
            return $data;
        } else {
            return false;
        }     // return false if no items for dropdown         
    }
	function get_dropdownuser_list_without_blank($table, $id, $name, $where) {
        $select = $id . ',' . $name;
        $this->db->select($select); 
        $this->db->from($table);
        if ($where != "") {
            $this->db->where($where);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $dropdowns = $query->result();
            foreach ($query->result_array() as $row) {
                $data[$row[$id]] = $row['name'];
            }
            return $data;
        } else {
            return false;
        }     // return false if no items for dropdown         
    }
     function get_dropdown_list_search($table,$id,$name,$where)
    {
        $select=$id.','.$name;
        $this->db->select($select); //change this to the two main values you want to use
        $this->db->from($table);
    if($where!=""){
        $this->db->where($where);
    }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
        $dropdowns = $query->result();
        $data[''] = 'All'; 
            foreach($query->result_array() as $row){
                $data[$row[$id]]=$row[$name];
                }
            return $data;
        } else { return false;    }     // return false if no items for dropdown         
    }
    function insert_query($table,$data,$data1)
    {
        $this->db->where($data1);
        $query = $this->db->get($table);
        $count_row = $query->num_rows();
         if ($count_row > 0) {
            return FALSE;
         } else {
            $this->db->insert($table, $data);
          return  $id=$this->db->insert_id();          
        }
    }
    function insert_query_($table,$data)
    {
        $this->db->insert($table, $data);
        $id=$this->db->insert_id(); 
        if($id==""){
            $id='False';
        }
        return $id;
        
    }
    function update_query($table,$data,$id,$cond){
        $this->db->where($cond, $id);
        $this->db->update($table, $data);
        //return true;
    }

    function list_select($tablename,$cond,$id)
    {
        $result = $this->db->from($tablename)
            ->where($cond,$id)
            ->get();
        return $result->result_array();  
    }
    function list_select1($tablename)
    {
        $result = $this->db->from($tablename)     
                ->get();
        return $result->result();  
    }


    public function todaycalender($tablename,$date,$users) {
        $cond="";
            if($users!="" ){  
        $cond.="and counselor in ($users)";
            }
        // $where="app_date ='$date' and $users";
     $where="app_date ='$date' $cond";
        $this->db->where($where);
        $query = $this->db->get($tablename);
        /*if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }*/
            return $query->result();
        //}
        //return false;
    } 
   public function calenders($tablename,$year,$month,$users) {
             $cond="";
             if($users!="" ){            
             $cond.="and counselor in ($users)";
             } 
        $where="YEAR(app_date)='$year' AND MONTH(app_date) ='$month' $cond";// GROUP BY app_date";
      //  $this->db->select(" GROUP_CONCAT(time,  '-', notes, '/') as notes,app_date");
        $this->db->where($where);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    } 
 
     public function maxid( $table,$id) {
       
        $this->db->select("max($id) as id");
        $this->db->from($table);
        $query = $this->db->get();
        return $query->result();
    }
      function select_name($table,$name,$where,$limit=NULL, $start=NULL)
    { 
        $this->db->select($name);   
        $this->db->where($where);
        $this->db->from($table);
        
        if($limit!=NULL){
        	$this->db->limit($limit, $start);
        }
        
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $q=$query->result();
            return $q[0]->$name;
        }
        else{ return 'N/A';}
    }
    
       function select_username($name,$id,$uid,$table)
    { 
          /// if($name==""){$name="CONCAT(first_name,' ',last_name) as name";}
        $this->db->select($name);   
        $this->db->where($id, $uid);
        $this->db->from($table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $q=$query->result();
            return $q[0]->name;
        }
        else{ return 'N/A';}
    }
	function getWorkorderList($loggedinuserid,$internal_user_external_user){
		$this->db->select('wo.id_workorder,wo.workorder_number,wo.fk_customer_id,wo.customer_name,wo.fk_assigned_to,wo.created_by,wo.created_on,wo.status,wof.path,c.companyname,u.first_name,u.last_name,cb.first_name as cb_first_name,cb.last_name as cb_last_name,swo.file AS signed_wo,wti.file AS tech_invoice,wai.file AS added_info');
    	$this->db->from('workorder wo');
    	$this->db->join('workorderfiles wof','wo.id_workorder = wof.fk_workorder_id','left');
    	$this->db->join('customer c','wo.fk_customer_id = c.customerid','left');
    	$this->db->join('user u','wo.fk_assigned_to = u.id','left');
    	$this->db->join('user cb','wo.created_by = cb.id','left');
    	$this->db->join('wo_signed swo','swo.fk_workorder_id = wo.id_workorder','left');
    	$this->db->join('wo_tech_invoice wti','wti.fk_workorder_id = wo.id_workorder','left');
    	$this->db->join('wo_added_info wai','wai.fk_workorder_id = wo.id_workorder','left');
    	
    	// don't show completed workorders to all
    	$where = " wo.status!='4'";
    	
    	// if internal user (1) then display all, if external user (2) then only display assigned
    	if($internal_user_external_user!=1){
    		// also do not display the completed workkorder to external user
    		$where.=" and fk_assigned_to = '".$loggedinuserid."'";
    	}
    	
    	$this->db->where($where);
    	
    	$this->db->order_by("wo.id_workorder","DESC");
    	
    	$this->db->group_by("wo.id_workorder");
    	
    	$this->response=$this->db->get();
    	//echo $this->db->last_query(); exit;
    	$resultset = $this->response->result_array();
    	//echo '<pre>'; print_r($resultset); exit;
    	return $resultset;
    }
    
    function getCompletedWorkorderList($loggedinuserid,$internal_user_external_user){
		$this->db->select('wo.id_workorder,wo.workorder_number,wo.fk_customer_id,wo.customer_name,wo.fk_assigned_to,wo.created_by,wo.created_on,wo.status,wo.completed_on,wof.path,c.companyname,u.first_name,u.last_name,cb.first_name as cb_first_name,cb.last_name as cb_last_name,swo.file AS signed_wo,wti.file AS tech_invoice,wai.file AS added_info');
    	$this->db->from('workorder wo');
    	$this->db->join('workorderfiles wof','wo.id_workorder = wof.fk_workorder_id','left');
    	$this->db->join('customer c','wo.fk_customer_id = c.customerid','left');
    	$this->db->join('user u','wo.fk_assigned_to = u.id','left');
    	$this->db->join('user cb','wo.created_by = cb.id','left');
    	$this->db->join('wo_signed swo','swo.fk_workorder_id = wo.id_workorder','left');
    	$this->db->join('wo_tech_invoice wti','wti.fk_workorder_id = wo.id_workorder','left');
    	$this->db->join('wo_added_info wai','wai.fk_workorder_id = wo.id_workorder','left');
    	
    	// show completed workorders to all
    	$where = " wo.status='4'";
    	
    	// if internal user (1) then display all, if external user (2) then only display assigned
    	$sessionrole=$this->session->userdata('srole');
    	if($internal_user_external_user==1 && $sessionrole!=1){	// if internal user, then display only their created by workorders
    		// also do not display the completed workkorder to external user
    		$where.=" AND wo.created_by = '".$loggedinuserid."'";
    	}
    	
    	// if external tech, then display completed workorders for last 30 days only
    	if($internal_user_external_user==2){
    		$current_date = date("Y-m-d H:i:s");
    		$minus_thirty_days = date("Y-m-d H:i:s",strtotime("-30 days"));
    		$where.=" AND wo.completed_on BETWEEN '".$minus_thirty_days."' AND '".$current_date."'";
    	}
    	
    	$this->db->where($where);
    	
    	$this->db->order_by("wo.id_workorder","DESC");
    	
    	$this->db->group_by("wo.id_workorder");
    	
    	$this->response=$this->db->get();
    	//echo $this->db->last_query(); exit;
    	$resultset = $this->response->result_array();
    	//echo '<pre>'; print_r($resultset); exit;
    	return $resultset;
    }
    
    /**
     * following function will get workorder details for the specified workorder id
     */
    function getWorkorderDetails($id_workorder,$sid,$internal_user_external_user){
    	$this->db->select('wo.id_workorder,wo.workorder_number,wo.fk_customer_id,wo.customer_name,wo.fk_assigned_to,wo.status,c.companyname,u.first_name,u.last_name');
    	$this->db->from('workorder wo');
    	$this->db->join('customer c','wo.fk_customer_id = c.customerid','left');
    	$this->db->join('user u','wo.fk_assigned_to = u.id','left');
    	$this->db->where("id_workorder = '".$id_workorder."'");
    	
    	$this->response=$this->db->get();
    	$resultset = $this->response->result_array();
    	
    	// now get files
    	$this->db->select('wof.path');
    	$this->db->from('workorderfiles wof');
    	$this->db->where("fk_workorder_id = '".$id_workorder."'");
    	$this->response=$this->db->get();
    	$files = $this->response->result_array();
    	foreach ($files as $each_file){
    		$resultset[0]['files'][] = $each_file['path'];
    	}
    	
    	// now get signed w/o file
    	$this->db->select('id,file');
    	$this->db->from('wo_signed');
    	$this->db->where("fk_workorder_id = '".$id_workorder."'");
    	$this->response=$this->db->get();
    	$files = $this->response->result_array();
    	foreach ($files as $each){
    		$resultset[0]['signed_workorder_file'][$each['id']] = $each['file'];
    	}
    	//$resultset[0]['signed_workorder_file'] = (isset($files[0]['file']))?$files[0]['file']:'';
    	
    	// now get tech invoice file
    	$this->db->select('id,file');
    	$this->db->from('wo_tech_invoice');
    	$this->db->where("fk_workorder_id = '".$id_workorder."'");
    	$this->response=$this->db->get();
    	$files = $this->response->result_array();
    	foreach ($files as $each){
    		$resultset[0]['tech_invoice_file'][$each['id']] = $each['file'];
    	}
    	//$resultset[0]['tech_invoice_file'] = (isset($files[0]['file']))?$files[0]['file']:'';
    	
    	// now get tech invoice file
    	$this->db->select('id,file');
    	$this->db->from('wo_added_info');
    	$this->db->where("fk_workorder_id = '".$id_workorder."'");
    	$this->response=$this->db->get();
    	$files = $this->response->result_array();
    	foreach ($files as $each){
    		$resultset[0]['added_info_file'][$each['id']] = $each['file'];
    	}
    	//$resultset[0]['added_info_file'] = (isset($files[0]['file']))?$files[0]['file']:'';
    	
    	return $resultset[0];
    }
    
    /**
     * following function will get list of project chat
     */
    function getProjectChat($projectid,$txt_search=NULL){
    	$this->db->select('cp.chat_id,cp.fk_project_id,cp.fk_user_id,cp.fk_customer_id,cp.fk_customer_user_id,cp.message,cp.created_on,p.title,u.first_name,u.last_name,c.companyname,cupc.email,cpf.filepath');
    	$this->db->from('chat_projects cp');
    	$this->db->join('user u','cp.fk_user_id = u.id','left');
    	$this->db->join('customer c','cp.fk_customer_id = c.customerid','left');
    	$this->db->join('customer_user_project_chat cupc','cp.fk_customer_user_id = cupc.id','left');
    	$this->db->join('project p','cp.fk_project_id = p.id','left');
    	$this->db->join('chat_project_files cpf','cp.chat_id = cpf.fk_chat_id','left');
    	
    	$conditoin = "cp.fk_project_id = '".$projectid."'";
    	if(!empty($txt_search)){
    		$conditoin.=" AND cp.message LIKE '%".$txt_search."%'";
    	}
    	
    	$this->db->where($conditoin);
    	
    	$this->db->order_by("cp.chat_id","ASC");
    	
    	$this->response=$this->db->get();
    	//echo $this->db->last_query(); exit;
    	$resultset = $this->response->result_array();
    	
    	return $resultset;
    }
}
?>