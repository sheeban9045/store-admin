<?php

namespace App\Controllers;
use App\Models\Community_plugin_model;


class Manage_PlanAndPlugin extends Security_Controller {
    protected $Community_plugin_model;
    function __construct() {
        parent::__construct();
        $this->access_only_admin();
        $this->Community_plugin_model = new Community_plugin_model();
    }
    
    //Function for individual Plugin Page
    function plugins(){
        echo "Hi";die;
        $view_data['plugins']=$this->Order_status_model->getPlugins();
        return rander("plugins.php",$view_data);
    }

    //Index Page for manage plugins
   function index_Plugins(){
        // $plugins=$this->Order_status_model->getPlugins();
        $plugins=$this->Community_plugin_model->getPlugins();
        $plans = array();

        foreach($plugins as $plugin){
            $result = $this->Order_status_model->get_Plan_Plugin($plugin->plugin_name);
            $plans[$plugin->plugin_name]=$result;
        }
       $view_data['plugins']=$plugins;
       $view_data['plans']=$plans;
       $view_data['login_user'] = $this->login_user;
        // echo "<pre>";print_r($view_data);die;

        return $this->template->rander("manage_Plugins/index.php",$view_data);
    }

    
    
    //GET FOR ADDING PLANS
    function addToPlan(){

        //Just for inserting plugins nno relation to add plan
        // $order_model = model("App\Models\Order_status_model");
        // $order_model->insertPlugins();


        // $view_data['plugin_id']=$_GET['plugin_id'];
        $view_data['plugin_name']=$_GET['plugin_name'];
        $view_data['plugin_title'] = $_GET['name'];
        $view_data['plans'] =$this->Order_status_model->getPlans();
        $view_data['myPlans'] = $this->Order_status_model->myPlans($_GET['plugin_name']);
        
        return $this->template->rander("manage_Plugins/addToPlan.php",$view_data);

     }

     //POST FOR ADDING PLAN AND PLUGINS INTO PLAN MAPPING TABLE
     function insertPlan(){
        $choosedPlan=$_GET['choosedPlan'];
        // $plugin_id= $_GET['plugin_id'];
        $plugin_name = $_GET['plugin_name'];
        $this->Order_status_model->deletePlans($_GET['plugin_name']);

        foreach($choosedPlan as $plan){
            $this->Order_status_model->mapPlansPLugins($plan,$plugin_name);
        }
        app_redirect('Manage_PlanAndPlugin/index_Plugins');
     }



     
     


    // #Code For Screenshots
    function addScreenshots(){
        // $view_data['plugin_id']=$_GET['plugin_id'];
        $view_data['plugin_name']=$_GET['plugin_name'];
        return $this->template->rander("manage_Plugins/screenshot.php",$view_data);
    }

    function insertScreenshots(){
        //echo "<pre>";print_r($_FILES);die;
        
        $filename = $_POST['plugin_name'].$_FILES["image"]["name"];
        
        $tempname = $_FILES["image"]["tmp_name"];
        $folder = $_SERVER['DOCUMENT_ROOT']."/store-admin/files/screenshots/".$filename;

        copy($tempname, $folder);

        $values = array(
            "plugin_name" => $_POST['plugin_name'],
            "image" => $filename
        );
         
        $this->Order_status_model->insertScreenshot($values);
        app_redirect('Manage_PlanAndPlugin/index_Plugins');
    }

    // #Code For Change Log
    function addLog(){
        // $view_data['plugin_id'] = $_GET['plugin_id'];
        $view_data['plugin_name'] = $_GET['plugin_name'];
        $view_data['plugin_title'] = $_GET['name'];
        $view_data['logs'] = $this->Order_status_model->getLogs($_GET['plugin_name']);
        return $this->template->rander("manage_Plugins/changeLog.php",$view_data);
    }
    function updateLog(){
        $text = $_GET['text'];
        $version = $_GET['version'];
        // $plugin_id  = $_GET['plugin_id'];
        $plugin_name  = $_GET['plugin_name'];
        $this->Order_status_model->insertLog($text,$version,$plugin_name);
    }

    // #Code for edit plugins
    function editGet(){
        // $view_data['plugin_id'] = $_GET['plugin_id'];
        $view_data['plugin_name'] = $_GET['plugin_name'];
        $view_data['plugin_title'] = $_GET['name'];
        $view_data['plugin'] = $this->Community_plugin_model->getPlugin($_GET['plugin_name']);
        return $this->template->rander("manage_Plugins/edit.php",$view_data);
    }

    function editPost(){
        
        $title = $_POST['title'];
        $body = $_POST['body'];
        $status  = $_POST['status'];
        // $plugin_id = $_POST['plugin_id'];
        $plugin_name = $_POST['name'];

        if(!empty($_FILES['image']['name'])){
            $filename = $title."_profile.png";
            $tempname = $_FILES["image"]["tmp_name"];
            $folder = $_SERVER['DOCUMENT_ROOT']."/store-admin/files/plugin_profiles/".$filename;
            copy($tempname, $folder);
        }
        else{
            $filename="";
        }
        $value = array(
            'body' => $body,
            'title' => $title,
            'status' => $status,
            // 'plugin_id' => $plugin_id,
            'plugin_name' => $plugin_name,
            // 'image' => $filename
        );
        $this->Community_plugin_model->editPlugin($value);
        app_redirect('Manage_PlanAndPlugin/index_Plugins');
    }


    // #All Code for plan


     function activePlugins(){
        $plan_id=$_GET['plan_id'];
        $view_data['plugins']=$this->Order_status_model->getActivePlugins($plan_id);
        return $this->template->rander("manage_Plans/activePlugins.php",$view_data);
    }


   function index_Plans(){
       $view_data['plans'] =$this->Order_status_model->getPlans();
       return $this->template->rander("manage_Plans/index.php",$view_data);
        
    }

    function singlePlugin(){
        $plugin =$this->Order_status_model->getPlugin($_GET['plugin_id']);
        $view_data['plugin'] = $plugin[0];
        return $this->template->rander("manage_Plans/singlePlugin.php",$view_data);
    }


    //Heading and Option code
    function editPlans(){
        $headings = $this->Order_status_model->getHeadings();

        $options = array();
        $data = array();
        foreach( $headings as $heading){
            $options[$heading->heading_id] = $this->Order_status_model->getOptions($heading->heading_id);
            $data[$heading->heading_id] = $heading;
        }
        

        $view_data['headings'] =  $data ;
        $view_data['options'] = $options ;
        $view_data['plans'] = $this->Order_status_model->getPlans();
        return $this->template->rander("manage_Plans/editPlans.php",$view_data);
    }

    function editPlansPost(){

        if(!empty($_POST['heading'])){

            $heading_id = $_POST['heading_id'];
            
            $heading = array(
                'heading_id' => $heading_id,
                'title' => $_POST['heading'],
            );
            $this->Order_status_model->insertHeading($heading);

            $options = array();        
            for($i=1;$i<=10;$i++){

                if(!empty($_POST['option_'.$i])){
                    
               
                    $plan1=$plan2=$plan3=$plan4=0;

                    if(isset($_POST['checkbox'.$i])){

                        $plans = $_POST['checkbox'.$i];
                        foreach( $plans as $plan){
                            if($plan == 1){
                                $plan1 = 1;
                            }else if($plan == 2){
                                $plan2 = 1;
                            }else if($plan == 3){
                                $plan3 = 1;
                            }else if($plan == 4){
                                $plan4 = 1;
                            }
                        }
                    }

                   
                    $option=array(
                        'title' =>$_POST['option_'.$i] ,
                        'heading_id' => $heading_id,
                        'plan_1' => $plan1,
                        'plan_2' => $plan2,
                        'plan_3' => $plan3,
                        'plan_4' => $plan4,
                    );
                    
                    $options[]=$option;
                }
                
            }
            // echo"<pre>";print_r($options);die;
            $this->Order_status_model->insertOptions($options,$heading_id);
        }else{
            $heading_id = $_POST['heading_id'];
            $this->Order_status_model->deleteHeading($heading_id);
        }

        app_redirect('Manage_PlanAndPlugin/index_Plans');
    }

   // #All code for Downloads 
    function getDownloads(){
         return $this->template->rander("manage_Plans/downloads.php");
    }

}
?>

