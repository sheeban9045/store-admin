<?php

namespace App\Controllers;

class pluginadmin extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");
        
    }


    //New function to change the plugin-Name
    function changePluginName(){
        if( isset($_GET['id']) && isset($_GET['name']) ) {
            $this->Order_status_model->get_pluginlist([
                'name' => $_GET['name'],
                'id' => $_GET['id']
            ]);
        }
        app_redirect("pluginadmin");
    }
    

    function index() {
       
        $this->check_access_to_store();

            // $view_data['pluginlist'] = $this->Order_status_model->get_pluginlist();
            $value = [];
            if(isset($_GET['status']) && isset($_GET['id'])){
               $value = [
                    'status' => $_GET['status'],
                    'id' => $_GET['id']
                ];
            }else{
                $value = '';
            }

            if (!empty($value)) {
               $view_data['pluginlist'] = $this->Order_status_model->get_pluginlist($value);
                $this->template->rander("pluginadmin/index.php", $view_data);
            //   header("Refresh: 0.2; http:///store-admin/index.php/pluginadmin");
             app_redirect("pluginadmin");
            }else{
                $view_data['pluginlist'] = $this->Order_status_model->get_pluginlist();
                return $this->template->rander("pluginadmin/index.php", $view_data);
            }
            
            // return $this->template->rander("pluginadmin/index.php", $view_data);
 
        }

    
    function file_preview($id = "", $key = "") {
        if ($id) {
            validate_numeric_value($id);
            $order_info = $this->Orders_model->get_one($id);
            $files = unserialize($order_info->files);
            $file = get_array_value($files, $key);

            $file_name = get_array_value($file, "file_name");
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");

            $view_data["file_url"] = get_source_url_of_file($file, get_setting("timeline_file_path"));
            $view_data["is_image_file"] = is_image_file($file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_name);
            $view_data["is_google_drive_file"] = ($file_id && $service_type == "google") ? true : false;

            return $this->template->view("orders/file_preview", $view_data);
        } else {
            show_404();
        }
    }

}

/* End of file orders.php */
/* Location: ./app/controllers/orders.php */