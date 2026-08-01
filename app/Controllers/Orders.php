<?php

namespace App\Controllers;
// require('/var/www/html/stripe/init.php');

class Orders extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");
        $myView = 0;
    }

    function index() {

        $this->check_access_to_store();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("orders", $this->login_user->is_admin, $this->login_user->user_type);
        
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("orders", $this->login_user->is_admin, $this->login_user->user_type);

        if ($this->login_user->user_type === "staff") {
            $view_data['order_statuses'] = $this->Order_status_model->get_details()->getResult();
             //$view_data['orders'] = $this->list_data();
            return $this->template->rander("orders/index", $view_data);
        } else {
            //client view
            $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            return $this->template->rander("clients/orders/client_portal", $view_data);
        }
    }

    function process_order() {
        $this->check_access_to_store();
        $view_data = get_order_making_data();
        $view_data["cart_items_count"] = count($this->Order_items_model->get_all_where(array("created_by" => $this->login_user->id, "order_id" => 0, "deleted" => 0))->getResult());

        $view_data['clients_dropdown'] = "";
        if ($this->login_user->user_type == "staff") {
            $view_data['clients_dropdown'] = $this->_get_clients_dropdown();
        }

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("orders", 0, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        $view_data['companies_dropdown'] = $this->_get_companies_dropdown();
        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        $view_data['payment_setting'] = $payment_setting;
        return $this->template->rander("orders/process_order", $view_data);
    }

    function item_list_data_of_login_user() {
        $this->check_access_to_store();
        $options = array("created_by" => $this->login_user->id, "processing" => true);
        $list_data = $this->Order_items_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }

        echo json_encode(array("data" => $result));
    }

    /* prepare a row of order item list table */

    private function _make_item_row($data) {
        $item = "<div class='item-row strong mb5' data-id='$data->id'><div class='float-start move-icon'><i data-feather='menu' class='icon-16'></i></div> $data->title</div>";
        if ($data->description) {
            $item .= "<span>" . nl2br($data->description) . "</span>";
        }
        $type = $data->unit_type ? $data->unit_type : "";
        $limit = get_setting('limit');

        return array(
            $data->sort,
            $item,
           to_decimal_format($data->quantity) . $type,
            // $limit . " " ."months",
            to_currency($data->rate),
            to_currency($data->total),
             // modal_anchor(get_uri("orders/item_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id, "data-post-order_id" => $data->order_id)) .
                //  js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("orders/delete_item"), "data-action" => "delete"))
        );
    }

    /* load item modal */

    function item_modal_form() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $model_info = $this->Order_items_model->get_one($id);
        if ($id) { //check permission only for existing item
            $this->check_access_to_this_order_item($model_info);
        }

        $view_data['model_info'] = $model_info;
        $view_data['order_id'] = $this->request->getPost('order_id');

        return $this->template->view('orders/item_modal_form', $view_data);
    }

    /* add or edit an order item */

    function save_item() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $item_id = $this->request->getPost("item_id");

        if ($id) { //item added to order items
            $item_info = $this->Order_items_model->get_one($id);
            $this->check_access_to_this_order_item($item_info);
        } else { //item not added to order items yet
            $item_info = $this->Items_model->get_one($item_id);
            $this->check_access_to_this_item($item_info);
        }

        $quantity = unformat_currency($this->request->getPost('order_item_quantity'));

        $order_item_data = array(
            "description" => $this->request->getPost('order_item_description'),
            "quantity" => $quantity,
            "created_by" => $this->login_user->id,
            "item_id" => isset($item_info->item_id) ? $item_info->item_id : $item_id
        );

        if ($this->login_user->user_type === "staff") {
            //when it's adding by team members, they could change terms
            $rate = unformat_currency($this->request->getPost('order_item_rate'));
            $order_item_data["title"] = $this->request->getPost('order_item_title');
            $order_item_data["unit_type"] = $this->request->getPost('order_unit_type');
            $order_item_data["rate"] = unformat_currency($this->request->getPost('order_item_rate'));
            $order_item_data["total"] = $rate * $quantity;
        } else {
            //adding by clients, they can't change terms
            $order_item_data["title"] = $item_info->title;
            $order_item_data["unit_type"] = $item_info->unit_type;
            $order_item_data["rate"] = $item_info->rate;
            $order_item_data["total"] = $item_info->rate * $quantity;
        }

        $order_id = $this->request->getPost("order_id");
        if ($order_id) { //order created already, add order id
            $order_item_data["order_id"] = $order_id;
        }

        $order_item_id = $this->Order_items_model->ci_save($order_item_data, $id);
        if ($order_item_id) {

            //check if the add_new_item flag is on, if so, add the item to libary. 
            $add_new_item_to_library = $this->request->getPost('add_new_item_to_library');
            if ($add_new_item_to_library) {
                $library_item_data = array(
                    "title" => $this->request->getPost('order_item_title'),
                    "description" => $this->request->getPost('order_item_description'),
                    "unit_type" => $this->request->getPost('order_unit_type'),
                    "rate" => unformat_currency($this->request->getPost('order_item_rate'))
                );
                $this->Items_model->ci_save($library_item_data);
            }

            $options = array("id" => $order_item_id);
            $item_info = $this->Order_items_model->get_details($options)->getRow();

            echo json_encode(array("success" => true, "order_id" => $item_info->order_id, "data" => $this->_make_item_row($item_info), "order_total_view" => $this->_get_order_total_view($item_info->order_id), 'id' => $order_item_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //update the sort value for order item
    function update_item_sort_values($id = 0) {
        $this->check_access_to_store();
        $sort_values = $this->request->getPost("sort_values");
        if ($sort_values) {

            //extract the values from the comma separated string
            $sort_array = explode(",", $sort_values);

            //update the value in db
            foreach ($sort_array as $value) {
                $sort_item = explode("-", $value); //extract id and sort value

                $id = get_array_value($sort_item, 0);
                $sort = get_array_value($sort_item, 1);

                $data = array("sort" => $sort);
                $this->Order_items_model->ci_save($data, $id);
            }
        }
    }

    /* delete or undo an order item */

    function delete_item() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $order_item_info = $this->Order_items_model->get_one($id);
        $this->check_access_to_this_order_item($order_item_info);

        if ($this->request->getPost('undo')) {
            if ($this->Order_items_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Order_items_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "order_id" => $item_info->order_id, "data" => $this->_make_item_row($item_info), "order_total_view" => $this->_get_order_total_view($item_info->order_id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Order_items_model->delete($id)) {
                $item_info = $this->Order_items_model->get_one($id);
                echo json_encode(array("success" => true, "order_id" => $item_info->order_id, "order_total_view" => $this->_get_order_total_view($item_info->order_id), 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* order total section */

    private function _get_order_total_view($order_id = 0) {
        if ($order_id) {
            $view_data["order_total_summary"] = $this->Orders_model->get_order_total_summary($order_id);
            $view_data["order_id"] = $order_id;
            return $this->template->view('orders/order_total_section', $view_data);
        } else {
            $view_data = get_order_making_data();
            return $this->template->view('orders/processing_order_total_section', $view_data);
        }
    }



    function place_order() {
        
        $order_total_summary = get_order_making_data();
        $qty_min_order = get_setting('qty_min_order');
        $qty_max_order = get_setting('qty_max_order');
        $domain_name = $this->request->getPost('domain_name').".webhut.net";
        $domain_type = $this->request->getPost('domain_type'); // "self" or "webhut"

        if ($domain_type === 'self') {
            $domain_name = trim($this->request->getPost('self_domain_name'));
        } else {
            $domain_name = trim($this->request->getPost('domain_name')) . ".webhut.net";
        }
        // $client_id = $this->request->getPost('client_id');
        $invoice_labels = make_labels_view_data('label', true, true);
        $client_id = !empty($client_id)? $client_id: $this->login_user->client_id;
        // Validate the domain name
        if (!empty($domain_name)) {
            $isDomainExist = $this->Orders_model->is_domain_exists($domain_name);
            if (!empty($isDomainExist)) {
                echo json_encode(array("success" => false, 'message' => 'Domain already exist in database, please choose other domain name!'));
                exit;
            } else {
                $raw_input = ($domain_type === 'self') ? $domain_name : $this->request->getPost('domain_name');
                $pattern = ($domain_type === 'self') ? '/^[a-zA-Z0-9\-\.]+$/' : '/^[a-zA-Z0-9\-]+$/';

                if (!preg_match($pattern, $raw_input)) {
                    echo json_encode(array("success" => false, 'message' => 'Invalid domain format, please choose other domain name(Not Include "_"," ")!'));
                    exit;
                }
            }
        } else {
            echo json_encode(array("success" => false, 'message' => 'Domain could not be empty!'));
            exit;
        }

         $clientDueValue = get_setting("type_payment_" . $client_id);
         $clientDueValue = !empty($clientDueValue)? $clientDueValue: 'monthly';
        if( !empty($clientDueValue) && ($clientDueValue == 'monthly') ) {
            $firstDateOfNextMonth =strtotime('first day of next month') ;

            $dateType = 'Monthly';
            $dueDate = date('Y/m/d', $firstDateOfNextMonth);
        }else if( !empty($clientDueValue) && ($clientDueValue == 'weekly') ) {
            $firstDateOfNextMonth =strtotime('next monday') ;
            
            $dateType = 'Weekly';
            $dueDate = date('Y/m/d', $firstDateOfNextMonth);

        }


        //Put condition for next day delivery time
        // if( $temp_delivery_date_timestamp <= $tomorrowDateTimestamp ) {
        //  $selectedTime = str_replace(":", "", $temp_delivery_date_array[1]);
        //     $nextDayTimeLimit = str_replace(":", "", get_setting("time_limit_for_delivery_next_day"));

        //     if( $selectedTime > $nextDayTimeLimit ) {
        //         echo json_encode(array("success" => false, 'message' => sprintf(app_lang('deliver_time_not_available_error_msg'), get_setting("time_limit_for_delivery_next_day"))));
        //         return;
        //     }
        // }
        


        // // Put the condition for Vacations
        // if( empty($client_id) && !empty($this->login_user) && !empty($this->login_user->is_admin) ) {
        //     // In case of Admin
        //     $holidays = get_setting("holidays");
        //     if( !empty($holidays) ) {
        //         $holidayArray = json_decode($holidays, true);
        //         if( !empty($holidayArray) ) {
        //             foreach( $holidayArray as $holiday ) {
        //                 $vacation_start_timestamp = strtotime($holiday['start']);
        //                 $vacation_end_timestamp = strtotime($holiday['end']);

        //                 if( ($temp_delivery_date_timestamp >= $vacation_start_timestamp) && ($temp_delivery_date_timestamp < $vacation_end_timestamp) ) {
        //                     $vacation_title = $holiday['title'];
        //                     $is_delivery_date_available = false;
        //                     break;
        //                 }

        //             }
        //         }
        //     }

        // }else {
        //     // In case of User
        //     $user_id = !empty($client_id)? $client_id: $this->login_user->client_id;
        //     $getClientHoliday = $this->Client_vacations_model->get_client_holidays($user_id);

        //     if( !empty($getClientHoliday) ) {
        //         foreach($getClientHoliday as $holiday) {
        //             $vacation_start_timestamp = strtotime($holiday->start_date);
        //             $vacation_end_timestamp = strtotime($holiday->end_date);

        //             if( ($temp_delivery_date_timestamp >= $vacation_start_timestamp) && ($temp_delivery_date_timestamp < $vacation_end_timestamp) ) {
        //                 $vacation_title = $holiday->title;
        //                 $is_delivery_date_available = false;
        //                 break;
        //             }
        //         }
        //     }
        // }

        // if( empty($is_delivery_date_available) ) {
        //     echo json_encode(array("success" => false, 'message' => sprintf(app_lang('deliver_date_not_available_error_msg'), $vacation_title)));
        //     return;
        // }


        $this->check_access_to_store();

        $order_items = $this->Order_items_model->get_all_where(array("created_by" => $this->login_user->id, "order_id" => 0, "deleted" => 0))->getResult();
        if (!$order_items) {
            echo json_encode(array("success" => false, 'message' => app_lang('no_items_text')));
            exit;
        }

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "order");

        $order_data = array(
            "client_id" => $this->request->getPost("client_id") ? $this->request->getPost("client_id") : $this->login_user->client_id,
             "order_date" => !empty($this->request->getPost('start_date'))? $this->request->getPost('start_date'): date('Y-m-d H:i:s'), // get_today_date(),
            "note" => $this->request->getPost('order_note'),
            "limit" =>  get_setting('limit'),
            // "delivery_date" => str_replace('T', ' ', $delivery_date),
             "domain_name" => $domain_name,
            "created_by" => $this->login_user->id,
            "status_id" => $this->Order_status_model->get_first_status(),
            "tax_id" => get_setting('order_tax_id') ? get_setting('order_tax_id') : 0,
            "tax_id2" => get_setting('order_tax_id2') ? get_setting('order_tax_id2') : 0,
            "company_id" => $this->request->getPost('company_id') ? $this->request->getPost('company_id') : get_default_company_id(),
            "self" => ($domain_type === 'self') ? 1 : 0,
        );

         //  echo'<pre>';
         //  print_r($order_data);
         // die();
       

        $order_data["files"] = $files_data;
        $order_id = $this->Orders_model->ci_save($order_data);
        $recently_order = $this->Orders_model->my_recent_order($client_id)->getRow();

        // // @RIYA: START INVOICE CREATION WORK
        // $invoice_data = array(
        //     "client_id" => 0,
        //     "project_id" => 0, // $this->request->getPost('invoice_project_id') ? $this->request->getPost('invoice_project_id') : 0,
        //     "order_id" => $order_id,
        //     "bill_date" => get_my_local_time(), // $bill_date,
        //     "due_date" => $dueDate, // $this->request->getPost('invoice_due_date'),
        //     "tax_id" => 0, // $this->request->getPost('tax_id') ? $this->request->getPost('tax_id') : 0,
        //     "tax_id2" => 0, //$this->request->getPost('tax_id2') ? $this->request->getPost('tax_id2') : 0,
        //     "tax_id3" => 0, //$this->request->getPost('tax_id3') ? $this->request->getPost('tax_id3') : 0,
        //     "company_id" => $this->request->getPost('company_id') ? $this->request->getPost('company_id') : get_default_company_id(),
        //     "recurring" => 0, // $recurring,
        //     "repeat_every" => 0, //$repeat_every ? $repeat_every : 0,
        //     "repeat_type" => NULL, // $repeat_type ? $repeat_type : NULL,
        //     "no_of_cycles" => 0, // $no_of_cycles ? $no_of_cycles : 0,
        //     "note" => '',//$this->request->getPost('invoice_note'),
        //     "labels" =>  $order_id, //$this->request->getPost('labels').
        //     "status" =>  $invoice_labels ,// 'not paid',
        // );
        // //  echo '<pre>';
        // // print_r($invoice_data);
        // // die;
        // $invoice_id = $this->Invoices_model->ci_save($invoice_data, 0);

        // $copy_items = $order_items; // $this->Order_items_model->get_details(array("order_id" => $order_id))->getResult();
        // foreach ($copy_items as $data) {
        //     $invoice_item_data = array(
        //         "invoice_id" => $invoice_id,
        //         "order_item_id" => $data->id ? $data->id : 0,
        //         "title" => $data->title ? $data->title : "",
        //         "description" => $data->description ? $data->description : "",
        //         "quantity" => $data->quantity ? $data->quantity : 0,
        //         "unit_type" => $data->unit_type ? $data->unit_type : "",
        //         "rate" => $data->rate ? $data->rate : 0,
        //         "total" => $data->total ? $data->total : 0,
            
        //     );

        //     // echo '<pre>';
        //     // print_r($invoice_item_data);
        //     // die;
        //     $this->Invoice_items_model->ci_save($invoice_item_data);
        // }
        // //  END INVOICE CREATION WORK

        if ($order_id) {
            save_custom_fields("orders", $order_id, $this->login_user->is_admin, $this->login_user->user_type);

            //save items to this order
            foreach ($order_items as $order_item) {
                $order_item_data = array("order_id" => $order_id);
                $this->Order_items_model->ci_save($order_item_data, $order_item->id);
                                $item =  $this->Items_model->get_one($order_item->item_id);
                if(empty($item->stripe_product_id) || empty($item->stripe_price_id)) {
                    $getProductandPriceid = $this->_checkProductExistsByNameAndPrice($item);
                    if(!empty($getProductandPriceid)) {                        
                        $updated_item_stripe_data = $this->Items_model->update_where(array('stripe_product_id'=>$getProductandPriceid['productID'],'stripe_price_id'=>$getProductandPriceid['priceID']),array('id'=>$item->id));
                        $item = $this->Items_model->get_one($order_item->item_id);
                    }

                }
                $item_obj[] = $item;
            }
 
              $redirect_to = get_uri("orders/view/$order_id");
                 // $redirect_to = get_uri("invoices/views/");


            if ($this->login_user->user_type == "client") {
                $redirect_to = get_uri("orders/preview/$order_id");
                    // $redirect_to = get_uri("invoices/view/");
            }
            //send notification
            log_notification("new_order_received", array("order_id" => $order_id));

            echo json_encode(array("success" => true, "redirect_to" => $redirect_to, 'message' => app_lang('record_saved'),'order_id' => $order_id ,'products'=>$item_obj));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /*Function created by anuj for manage the stripe plans*/
    private function createStripeProductAndPrice($item_obj) {
    if(empty($item_obj) || !is_object($item_obj))
        return false;

    try {
        // Create a new product
        $product = \Stripe\Product::create([
            'name' => $item_obj->title,
            'description' => $item_obj->title,
        ]);
        $paymentType = $item_obj->payment_type;
        if ($paymentType === 'subscription') {
            // Create a new price for subscription
            $price = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => $item_obj->rate*100,
                'currency' => 'usd',
                'recurring' => ['interval' => 'month']
            ]);
        } else {
            // Create a new price for one-time payment
            $price = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => $item_obj->rate*100,
                'currency' => 'usd',
                // No recurring attribute for one-time payments
            ]);
        }
        // Return the new product and price IDs
        return [
            'productID' => $product->id,
            'priceID' => $price->id
        ];
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle error
        error_log($e->getMessage());
        return false;
    }
}

    private function _checkProductExistsByNameAndPrice($item_obj) {
    if(empty($item_obj) || !is_object($item_obj))
        return false;
    require_once(APPPATH . "ThirdParty/Stripe/vendor/autoload.php");
    $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
    $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
    \Stripe\Stripe::setApiKey($payment_setting->secret_key);
    $productName = $item_obj->title;
    $priceAmount = $item_obj->rate;     
        try {
            // Retrieve all products
            $priceObj = array();
            $priceAmount = $priceAmount*100;
            $products = \Stripe\Product::all();
            foreach ($products->data as $product) {
                if ($product->name === $productName) {
                    // Retrieve the prices for the product
                    $prices = \Stripe\Price::all(['product' => $product->id]);
                    foreach ($prices->data as $price) {
                        if ($price->unit_amount == $priceAmount) {
                           $priceObj =  [
                                'productID' => $price->product,
                                'priceID' => $price->id
                           ];
                        }
                    }
                }
            }
           if(empty($priceObj)) {
            $priceObj = $this->createStripeProductAndPrice($item_obj);
           }
            if(!empty($priceObj))
                return $priceObj;
            else 
                return false;
        } catch (Stripe\Exception\ApiErrorException $e) {
            echo 'Error retrieving products: ' . $e->getMessage();
            return false;
        }
    }
    /*Function created by anuj for manage the stripe plans*/



    /* list of orders, prepared for datatable  */

    public function list_data() {
        $this->access_only_allowed_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("orders", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "status_id" => $this->request->getPost("status_id"),
            "domain_name" => $this->request->getPost("domain_name"),
            "order_date" => $this->request->getPost("start_date"),
            "deadline" => $this->request->getPost("end_date"),
            "custom_fields" => $custom_fields,
             "client_id" => $this->request->getPost('client_id'),

            "custom_field_filter" => $this->prepare_custom_field_filter_values("orders", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $list_data = $this->Orders_model->get_details($options)->getResult();

        $clients = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));

        $result = array();
        $list_data = array_reverse($list_data);
        // echo "<pre>";print_R($list_data);die;
        
        if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'production') ) {
            foreach ($list_data as $data) { 
                 if(empty($data->delivery_date))
                     continue;

                 $temp_delivery_date = date("Y-m-d", strtotime($data->delivery_date));
                 $temp_tomorrow_date = date("Y-m-d", strtotime('+1 day'));

                 if( $temp_delivery_date != $temp_tomorrow_date )
                     continue;
                $result[] = $this->_make_row($data, $custom_fields);
            }
        }else {
            foreach ($list_data as $data) {

                $result[] = $this->_make_row($data, $custom_fields);
            }
        }
    
        echo json_encode(array("data" => $result));
    }


    // Harsh's Code for setup community
    function modal_community(){ 
        
        $view_data = array();
        $view_data['order_id'] = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
        $order_detail =  $this->Orders_model->get_one($view_data['order_id']);
        $user_detail = $this->Users_model ->get_one($order_detail->client_id);
        $view_data['view'] = isset($_GET['view']) ? $_GET['view'] : 0;
        $view_data['user_email'] = $user_detail->email;
        $view_data['domain'] = $this->Orders_model->get_domain($view_data['order_id']);
        return $this->template->view('orders/setup_community',$view_data);
    }

    function view_invoices(){ 
        $order_id = $_GET['order_id'];
        $order_detail =  $this->Orders_model->get_one($_GET['order_id']);
        $order_item =  $this->Order_items_model->get_details(array('order_id'=>$_GET['order_id']))->getRow();
        $user_detail = $this->Users_model ->get_one($order_detail->client_id);
        if(empty($order_detail->stripe_response)){
            return false;
        }
        $view_data['user_detail'] = $user_detail;
        $view_data['order_detail'] = $order_detail;
        $stripe_response = $order_detail->stripe_response;
        $stripeResponseObj = json_decode(str_replace("StripeCheckoutSession JSON: ","",$order_detail->stripe_response));
        $subscriptionId = isset($stripeResponseObj->subscription)?$stripeResponseObj->subscription:'';
        if(empty($subscriptionId))
            return false;

        // Get Stripe keys
        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);

        if(empty($payment_setting->secret_key) || empty($payment_setting->publishable_key))
            return false;
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);
        $allInvoices = $this->getInvoicesBySubscriptionId($subscriptionId);

        if(!empty($allInvoices)){
            foreach ($allInvoices as &$item) {
                $item['invoice_title'] = !empty($order_item)?$order_item->title:'null';
            }
            $view_data['all_invoices'] = $allInvoices;
        }
        return $this->template->view('orders/view_invoices',$view_data);
    }
    
    protected function getInvoicesBySubscriptionId($subscriptionId) {
        try {
            // Retrieve all invoices for the given subscription ID
            $invoices = \Stripe\Invoice::all([
                'subscription' => $subscriptionId,
            ]);

            $invoiceDetails = [];
            
            foreach ($invoices->data as $invoice) {
                $timestamp = $invoice->status_transitions->paid_at;
                $date = date('Y-m-d H:i:s', $timestamp);
                $invoiceDetails[] = [
                    'invoice_id' => $invoice->id,
                    'amount_due' => $invoice->amount_due,
                    'status' => $invoice->status,
                    'status_date' => $date,
                    'interval' => $invoice['lines']['data'][0]['plan']['interval'],
                    'url' => $invoice->hosted_invoice_url,
                ];
            }

            return $invoiceDetails;

        } catch (\Exception $e) {
            // Handle exceptions
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    function setup_community_post(){
        $order_id = $_GET['order_id'];
        $order_detail =  $this->Orders_model->get_one($_GET['order_id']);
        $user_detail = $this->Users_model ->get_one($order_detail->client_id);
        $view_data = array();
        $view_data['order_id'] = $order_id;
        $view_data['user_email'] = $user_detail->email;
        $view_data['domain'] = $this->Orders_model->get_domain($order_id);
        return $this->template->view('orders/loader',$view_data);
    }
    
    function install_community(){
        $order_id = $_GET['order_id'];
        $this->Orders_model->set_community($order_id);
        $domain = $this->Orders_model->get_domain($order_id);
        $setup = $this->create_folder('','',$this->Orders_model->get_domain($order_id),$order_id);
        if(!empty($setup)) {
            echo json_encode(array("success" => true));
        } else {
            $order_obj = $this->Orders_model->get_one($order_id);
            if(!empty($order_obj->is_domain_created)) {
                echo json_encode(array("success" => true));    
            } else {
                echo json_encode(array("success" => false));
            }
        }
    }

    function create_folder($sourceFolderPath = "", $destinationFolderPath = "",$domain = "",$order_id = 0){

       if(empty($sourceFolderPath)&& empty($destinationFolderPath)) {
          $sourceFolderPath = "/var/www/html/dummy-community";
          
          $destinationFolderPath = '/var/www/html/';
          

          //$newFolderName = $this->login_user->first_name."_".$this->login_user->id;
          $newFolderName = $domain;
          $destinationFolderPath = $destinationFolderPath . $newFolderName;
          
          
              // Ensure source folder exists
            if (!is_dir($sourceFolderPath)) {
                return false;
            }
            // Generate a new name for the destination folder
            $destination = $destinationFolderPath;
            $source = $sourceFolderPath;
        
            // Ensure destination folder does not already exist to prevent overwriting
            if (is_dir($destination)) {
               return false;
            }
            // Escape shell arguments to prevent command injection
            $escapedSource = escapeshellarg($source);
            $escapedDestination = escapeshellarg($destination);
        
            // Shell command to copy the folder
            $command = "cp -r $escapedSource $escapedDestination  2>&1";
            // Execute the shell command
            $output = null;
            $retval = null;
            exec($command, $output, $retval);
            // Check if the command was successful
            if ($retval == 0) {
                exec("chmod -R 777 " . escapeshellarg($destination), $output, $retval);
                
                //If directory copied successfully now we have to change the path in cache
                $filePath = $destination.'/application/settings/cache.php';
                $newCachePath = $destination.'/temporary/cache';
                $update_cache_path = $this->_changeCachePaths($filePath, $newCachePath);

                // CODE FOR DATABASE SETUP
                $db_name = str_replace("-","_",$domain);
                $db_name = "webhut96_".str_replace(".webhut.net","", $db_name);
                # SQL command to create a new database
                $sql_command ="CREATE DATABASE IF NOT EXISTS $db_name;";

                # Execute the SQL command
                
                $command_create_database = "mysql -u community_admin  -e "."'$sql_command'  2>&1";
                // Execute the shell command
                $output = null;
                $retval = null;
                exec($command_create_database, $output, $retval);
                if($retval == 0) {
                    $command_import_database = "mysql -u community_admin $db_name < /var/www/html/webhut96_dummy_community.sql";
                    // Execute the shell command
                    $output = null;
                    $retval = null;
                    exec($command_import_database, $output, $retval);
                    if($retval == 0) {
                        // CHANGE THE DATABASE NAME IN COMMUNITY
                        $filePath = $destination.'/application/settings/database.php';
                        $update_database_name = $this->_changeDatabaseName($filePath, $db_name);
                        if(!empty($update_database_name)) {
                            exec("systemctl restart mysql", $output, $retval);
                            exec("systemctl restart apache2", $output, $retval);

                            // CODE FOR CREATING SUBDOMAIN                          
                            $apiToken = getenv('DO_TOKEN');
                            $domain_main = 'webhut.net';
                            $subdomain = str_replace(".webhut.net","", $domain);
                            $dropletIp = '143.198.73.14';
                            $createSubDomain = $this->_createSubdomain($apiToken,$domain_main, $subdomain, $dropletIp);

                            if(!empty($createSubDomain)) {
                                $order_update['is_domain_created'] = 1;
                                $this->Orders_model->ci_save($order_update, $order_id);
                                $order_obj = $this->Orders_model->get_one($order_id);
                                
                                // Update the client email start
                                // $this->Orders_model->update_user_email($order_id, $db_name);
                                // Update the client email end

                                $subdomain = $domain;
                                $documentRoot = "/var/www/html/".$domain;
                                $output = $this->_createApacheConfig($subdomain, $documentRoot);
                                return true;
                            } else {
                                return false;
                            }

                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
                
            } else {
                return false;
            }
          
          
        }

        
    }
    
    // Function for update the cache file 
    private function _changeCachePaths($filePath, $newCachePath) {
        // Ensure the file exists
        if (!file_exists($filePath)) {
            return false;
        }
    
        // Read the file contents
        $fileContents = file_get_contents($filePath);
    
        // Define the patterns to match the cache paths
        $patternCacheDir = '/(\'cache_dir\'\s*=>\s*[\'"]).*?(["\'],)/';
        $patternDefaultFilePath = '/(\'default_file_path\'\s*=>\s*[\'"]).*?(["\'],)/';
    
        // Replace the old cache paths with the new cache path
        $replacementCacheDir = '${1}' . addslashes($newCachePath) . '${2}';
        $replacementDefaultFilePath = '${1}' . addslashes($newCachePath) . '${2}';
        
        $newContents = preg_replace($patternCacheDir, $replacementCacheDir, $fileContents);
        $newContents = preg_replace($patternDefaultFilePath, $replacementDefaultFilePath, $newContents);
    
        // Check if the replacements were successful
        if ($newContents === null) {
           return false;
        }
    
        // Write the new contents back to the file
        if (file_put_contents($filePath, $newContents) === false) {
            return false;
        }
       return true;
    }

    private function _changeDatabaseName($filePath, $dbname) {
        // Ensure the file exists
        if (!file_exists($filePath)) {
            return false;
        }
    
        // Read the file contents
        $fileContent = file_get_contents($filePath);

        // Use a regular expression to find and replace the dbname
        $pattern = "/'dbname' => '.*?'/";
        $replacement = "'dbname' => '$dbname'";

        // Replace the dbname in the file content
        $newFileContent = preg_replace($pattern, $replacement, $fileContent);

        // Write the modified content back to the file
        if (file_put_contents($filePath, $newFileContent) === false) {
            return false;
        }
        return true;
    }




    private function _makeApiRequest($apiToken,$url, $method, $data = null) {
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiToken,
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }

    private function _createSubdomain($apiToken,$domain, $subdomain, $dropletIp) {
        $url = "https://api.digitalocean.com/v2/domains/$domain/records";
        $data = [
            'type' => 'A',
            'name' => $subdomain,
            'data' => $dropletIp,
            'ttl' => 1800,
        ];
        
        $response = $this->_makeApiRequest($apiToken,$url, 'POST', $data);
        
        if (isset($response['domain_record'])) {
            return true;
        } else {
            return false;
        }
    }

    private function _createApacheConfig($subdomain, $documentRoot)
    {
        $configContent = <<<EOL
    <VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName $subdomain
        DocumentRoot $documentRoot

        ErrorLog \${APACHE_LOG_DIR}/error.log
        CustomLog \${APACHE_LOG_DIR}/access.log combined

        <Directory $documentRoot>
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
            Header set Access-Control-Allow-Origin '*'
            Header set Access-Control-Allow-Methods 'GET, POST, OPTIONS'
            Header set Access-Control-Allow-Headers 'Origin, Content-Type, Accept, Authorization'
        </Directory>
    </VirtualHost>
    EOL;

        $configFilePath = "/etc/apache2/sites-available/$subdomain.conf";
        $scriptPath = "/tmp/create_apache_config.sh";

        // Create the shell script content
        $scriptContent = <<<EOL
    #!/bin/bash
    echo "$configContent" > $configFilePath
    a2ensite $subdomain.conf
    systemctl restart apache2
    systemctl restart mysql
    EOL;

        // Write the shell script to a temporary file
        file_put_contents($scriptPath, $scriptContent);

        // Make the shell script executable
        chmod($scriptPath, 0755);
        if (file_exists($scriptPath)){
            shell_exec("dos2unix $scriptPath 2>&1");
            // Execute the shell script using sudo
            $logFile = '/tmp/create_apache_config.log';
            shell_exec("nohup sudo bash $scriptPath >> $logFile 2>&1 &");
            return true;
        }
        
        
    }


 




// Harsh Code end here for community setup

    /* prepare a row of order list table */

    private function _new_make_row($data, $custom_fields) {
        $order_url = "";
        $order_id = $data->id;
        if ($this->login_user->user_type == "staff") {
            $order_url = anchor(get_uri("orders/view/" . $data->id), get_order_id($data->id));
        } else {
            //for client
            $order_url = anchor(get_uri("orders/preview/" . $data->id), get_order_id($data->id));
        }

        $client = anchor(get_uri("clients/view/" . $data->client_id), $data->company_name);
        $total_quantity = get_order_making_data($data->id);

        $temp_total_qty = !empty($total_quantity['order_total_summary']->total_quantity)? $total_quantity['order_total_summary']->total_quantity: 0;

              // $checkmark = js_anchor("<span class='checkbox-blank mr15 float-start' id 'mycheck'></span>", );

        if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'yearly') )
            $checkmark = js_anchor("<input type='checkbox' name='yearly_checkbox_order_ids' id 'checkbox_$order_id' value='$order_id' />", );
        else if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'production') )
            $checkmark = js_anchor("<input type='checkbox' name='production_checkbox_order_ids' id 'checkbox_$order_id' value='$order_id' />", );
        else
            $checkmark = js_anchor("<input type='checkbox' name='monthly_checkbox_order_ids' id 'checkbox_$order_id' value='$order_id' />", );


            

        $temp_delivery_date = !empty($data->delivery_date)? format_to_date($data->delivery_date, false): '-';

        
        // $row_data = array(
        //      $checkmark,
          
        //      $order_url,
        //     $client,
        //     $data->order_date,
        //     format_to_date($data->order_date, false),
        //     "abc",
            
        //    $temp_total_qty,
        //     to_currency($data->order_value)
        // );
        $row_data = array(
            $checkmark, 
            $order_url,
            $client_id,
            $data->order_date,
             $temp_delivery_date,
            format_to_date($data->order_date, false),
            to_currency($data->order_value)
        );

        if ($this->login_user->user_type == "staff") {
            $row_data[] = js_anchor($data->order_status_title, array("style" => "background-color: $data->order_status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-order-status"));
        } else {
            $row_data[] = "<span style='background-color: $data->order_status_color;' class='badge'>$data->order_status_title</span>";
        }

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = modal_anchor(get_uri("orders/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_order'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_order'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("orders/delete"), "data-action" => "delete"));

        return $row_data;
       
    }

    /* prepare a row of order list table */

    private function _make_row($data, $custom_fields) {
        $order_url = "";
        if ($this->login_user->user_type == "staff") {
            $order_url = anchor(get_uri("orders/view/" . $data->id), get_order_id($data->id));
        } else {
            //for client
            $order_url = anchor(get_uri("orders/preview/" . $data->id), get_order_id($data->id));
        }
        if($data->company_name === null){
            $data->company_name = "";
        }
        $client = anchor(get_uri("clients/view/" . $data->client_id), $data->company_name);
        $total_quantity = get_order_making_data($data->id);

        $temp_total_qty = !empty($total_quantity['order_total_summary']->total_quantity)? $total_quantity['order_total_summary']->total_quantity: 0;

            // $checkmark = js_anchor("<span class='checkbox-blank mr15 float-start' id 'mycheck'></span>", );

        // if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'yearly') )
        //     $checkmark = js_anchor("<input type='checkbox' name='yearly_checkbox_order_ids' id 'checkbox_$order_id' value='$order_id' />", );
        // else if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'production') )
        //     $checkmark = js_anchor("<input type='checkbox' name='production_checkbox_order_ids' id 'checkbox_$order_id' value='$order_id' />", );
        // else
        //     $checkmark = js_anchor("<input type='checkbox' name='monthly_checkbox_order_ids' id 'checkbox_$order_id' value='$order_id' />", );


            
         $temp_delivery_date = !empty($data->delivery_date)? format_to_date($data->delivery_date, false): '-';
          $checkmark = js_anchor("<input type='checkbox' name='monthly_checkbox_order_ids' id 'checkbox_ $data->id' value=' $data->id' />", );
          $limit = get_setting("limit");
          $domain_name = $data->domain_name;
          $is_webhut_domain = (substr($domain_name, -11) === '.webhut.net');
          $client_id = $data->client_id;
        $order_date = $data->order_date;
        
         

             $futureDate=date('Y-m-d', strtotime("+$limit months", strtotime($order_date)));
        //      echo'<pre>';
        // pritnt_r($futureDate);
        // die;

             
         
 
        if ($this->login_user->user_type == "staff") {
            $row_data = array(
               $checkmark, 
                $order_url,
                $data->company_name,
                $order_date,
                 format_to_date($order_date, false),
                 format_to_date($order_date),
                  format_to_date($futureDate),
                 
    
                 $domain_name,
                // $limit . ' ' . 'months',
                 // $limit . ' ' . 'months',
               
                to_currency($data->order_value)
            );
        } else {
            $row_data = array(
                $order_url,
                $data->company_name,
                $order_date,
                 format_to_date($order_date, false),
                 format_to_date($order_date),
                  format_to_date($futureDate),
                 
    
                 $domain_name,
                // $limit . ' ' . 'months',
                 // $limit . ' ' . 'months',
               
                to_currency($data->order_value)
            );
        }

        if ($this->login_user->user_type == "staff") {
            $row_data[] = js_anchor($data->order_status_title, array("style" => "background-color: $data->order_status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-order-status"));
        } else {
            $row_data[] = "<span style='background-color: $data->order_status_color;' class='badge'>$data->order_status_title</span>";
        }
        
        
        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }
        
        if ($this->login_user->user_type == "staff") {
            $row_data[] = modal_anchor(get_uri("orders/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_order'), "data-post-id" => $data->id))
                    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_order'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("orders/delete"), "data-action" => "delete"));
        }
        if ($is_webhut_domain) {
            if(isset($data->is_community_set) && $data->is_community_set == 1 ){
                $row_data[] = modal_anchor(get_uri("orders/modal_community?view=1&order_id=".($data->id)), "View", array("class" => "edit btn btn-success ", "title" => "View Community")); 
            }else{        
                $row_data[] = modal_anchor(get_uri("orders/modal_community?order_id=".($data->id)), "Setup", array("class" => "edit btn btn-primary", "title" => "Setup Community"));  
            }
        } else {
            $download_btn = anchor(
                get_uri("orders/download_community_files?order_id=" . $data->id),
                "<i data-feather='download' class='icon-16'></i>",
                array("class" => "btn btn-primary btn-sm m-1", "title" => "Download Community Files")
            );

            $email_btn = js_anchor(
                "<i data-feather='mail' class='icon-16'></i>",
                array(
                    "class"      => "btn btn-warning btn-sm m-1",
                    "title"      => "Email admin for self community setup",
                    "data-id"    => $data->id,
                    "data-act"   => "send-self-community-email"
                )
            );

            $row_data[] = $download_btn . $email_btn;
        }
        if(!empty($data->stripe_response)) {
            $row_data[] = modal_anchor(get_uri("orders/view_invoices?order_id=".($data->id)), "View Invoice", array("class" => "edit btn btn-success", "title" => "View Invoices"));
        } else {
            $row_data[] = "<a href = 'javascript:void(0)' title = 'No Invoice Available' class = 'btn btn-secondary'>View Invoice</a>";
        }
        return $row_data;
    }

    function download_community_files() {
        $this->check_access_to_store();

        $order_id = $this->request->getGet('order_id');
        validate_numeric_value($order_id);

        $order = $this->Orders_model->get_one($order_id);
        if (empty($order->id)) {
            show_404();
        }

        $zip_source_file = "/var/www/html/dummy-community.zip";
        $sql_source_file = "/var/www/html/webhut96_dummy_community.sql";

        if (!file_exists($zip_source_file) || !file_exists($sql_source_file)) {
            echo "Required files not found on server.";
            exit;
        }

        $final_zip_name = "community_" . $order->domain_name . "_" . date('YmdHis') . ".zip";
        $final_zip_path = "/var/www/html/uploads/" . $final_zip_name;

        $zip = new ZipArchive();
        if ($zip->open($final_zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            $zip->addFile($zip_source_file, basename($zip_source_file));

            $zip->addFile($sql_source_file, basename($sql_source_file));

            $zip->close();
        } else {
            echo "Zip creation failed";
            exit;
        }

        if (file_exists($final_zip_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $final_zip_name . '"');
            header('Content-Length: ' . filesize($final_zip_path));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            readfile($final_zip_path);
            flush();
            @unlink($final_zip_path);
            exit;
        } else {
            echo "File not found";
            exit;
        }
    }

    function send_self_community_email() {
        $this->check_access_to_store();

        $order_id = $this->request->getPost('order_id');
        validate_numeric_value($order_id);

        $order = $this->Orders_model->get_one($order_id);
        if (empty($order->id)) {
            echo json_encode(array("success" => false, "message" => "Order not found"));
            exit;
        }

        $client = $this->Clients_model->get_one($order->client_id);

        $admin_email = get_setting("email_sent_from_address");

        $subject = "Self Community Setup Request - Order #" . $order->id;
        $message = "A client has requested self-domain community setup.<br><br>";
        $message .= "Order ID: #" . $order->id . "<br>";
        $message .= "Client: " . $client->company_name . "<br>";
        $message .= "Domain: " . $order->domain_name . "<br>";
        $message .= "Please setup the community manually for this order.";

        $sent = send_app_mail($admin_email, $subject, $message);

        if ($sent) {
            echo json_encode(array("success" => true, "message" => "Email sent successfully to the team."));
        } else {
            echo json_encode(array("success" => false, "message" => "Failed to send email. Please try again."));
        }
        exit;
    }
    //load the yearly view of order list
    function yearly() {
        return $this->template->view("orders/yearly_orders");
    }

    function production() {
        return $this->template->view("orders/production");
    }


    /* load new order modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "numeric"
        ));

        $client_id = $this->request->getPost('client_id');
        $view_data['model_info'] = $this->Orders_model->get_one($this->request->getPost('id'));

        //make the drodown lists
        $view_data['taxes_dropdown'] = array("" => "-") + $this->Taxes_model->get_dropdown_list(array("title"));
        $view_data['clients_dropdown'] = $this->_get_clients_dropdown();

        $view_data['order_statuses'] = $this->Order_status_model->get_details()->getResult();

        $view_data['client_id'] = $client_id;

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("orders", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        $view_data['companies_dropdown'] = $this->_get_companies_dropdown();
        if (!$view_data['model_info']->company_id) {
            $view_data['model_info']->company_id = get_default_company_id();
        }

        return $this->template->view('orders/modal_form', $view_data);
    }

    private function _get_clients_dropdown() {
        $clients_dropdown = array("" => "-");
        $clients = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        foreach ($clients as $key => $value) {
            $clients_dropdown[$key] = $value;
        }
        return $clients_dropdown;
    }

    /* add, edit or clone an order */

    function save() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "order_client_id" => "required|numeric",
            "order_date" => "required",
            "status_id" => "required"
        ));

        $client_id = $this->request->getPost('order_client_id');
        $id = $this->request->getPost('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "order");
        $new_files = unserialize($files_data);

        $order_data = array(
            "client_id" => $client_id,
            "order_date" => $this->request->getPost('order_date'),
            "tax_id" => $this->request->getPost('tax_id') ? $this->request->getPost('tax_id') : 0,
            "tax_id2" => $this->request->getPost('tax_id2') ? $this->request->getPost('tax_id2') : 0,
            "company_id" => $this->request->getPost('company_id') ? $this->request->getPost('company_id') : get_default_company_id(),
            "note" => $this->request->getPost('order_note'),
            "status_id" => $this->request->getPost('status_id'),
              "domain_name" => $this->request->getPost('domain_name')
            
        );

       
    

        //check if the status has been changed,
        //if so, send notification
        $order_info = $this->Orders_model->get_one($id);
        if ($order_info->status_id !== $this->request->getPost('status_id')) {
            log_notification("order_status_updated", array("order_id" => $id));
        }

        //is editing? update the files if required
        if ($id) {
            $timeline_file_path = get_setting("timeline_file_path");
            $new_files = update_saved_files($timeline_file_path, $order_info->files, $new_files);
        }

        $order_data["files"] = serialize($new_files);

        $order_id = $this->Orders_model->ci_save($order_data, $id);
        if ($order_id) {
            save_custom_fields("orders", $order_id, $this->login_user->is_admin, $this->login_user->user_type);

            echo json_encode(array("success" => true, "data" => $this->_row_data($order_id), 'id' => $order_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an order */

    function delete() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Orders_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Orders_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* load order details view */

    function view($order_id = 0) {
        $this->access_only_allowed_members();

        if ($order_id) {

            validate_numeric_value($order_id);

            $view_data = get_order_making_data($order_id);

            // @Riya - Apply the discount
            if( isset($view_data['order_info']->client_id) && !empty($view_data['order_info']->client_id) ) {
                $data = array(
                    "discount_type" => get_setting('discount_type_' . $view_data['order_info']->client_id),
                    "discount_amount" => get_setting('discount_' . $view_data['order_info']->client_id),
                    "discount_amount_type" => 'percentage'
                );

                $data = clean_data($data);

                $save_data = $this->Orders_model->ci_save($data, $order_id);
            }

            validate_numeric_value($order_id);
            $view_data = get_order_making_data($order_id);
            

            if ($view_data) {
                $access_info = $this->get_access_info("invoice");
                $view_data["show_invoice_option"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;

                $access_info = $this->get_access_info("estimate");
                $view_data["show_estimate_option"] = (get_setting("module_estimate") && $access_info->access_type == "all") ? true : false;

                $view_data["can_create_projects"] = $this->can_create_projects();

                $view_data["order_id"] = $order_id;

                $view_data['order_statuses'] = $this->Order_status_model->get_details()->getResult();

                return $this->template->rander("orders/view", $view_data);
            } else {
                show_404();
            }
        }
    }

    private function check_access_to_this_order($order_data) {
        //check for valid order
        if (!$order_data) {
            show_404();
        }

        //check for security
        $order_info = get_array_value($order_data, "order_info");
        if ($this->login_user->user_type == "client") {
            if ($this->login_user->client_id != $order_info->client_id) {
                app_redirect("forbidden");
            }
        }
    }

    function download_pdf($order_id = 0, $mode = "download") {
        if ($order_id) {
            validate_numeric_value($order_id);
            $order_data = get_order_making_data($order_id);
            $this->check_access_to_store();
            $this->check_access_to_this_order($order_data);

            if (@ob_get_length())
                @ob_clean();
            //so, we have a valid order data. Prepare the view.

            prepare_order_pdf($order_data, $mode);
        } else {
            show_404();
        }
    }

    //view html is accessable to client only.
    function preview($order_id = 0, $show_close_preview = false) {
        $this->check_access_to_store();

        if ($order_id) {
            validate_numeric_value($order_id);
            $order_data = get_order_making_data($order_id);
            $this->check_access_to_this_order($order_data);

            $order_data['order_info'] = get_array_value($order_data, "order_info");

            $view_data['order_preview'] = prepare_order_pdf($order_data, "html");

            //show a back button
            $view_data['show_close_preview'] = $show_close_preview && $this->login_user->user_type === "staff" ? true : false;

            $view_data['order_id'] = $order_id;

            return $this->template->rander("orders/order_preview", $view_data);
        } else {
            show_404();
        }
    }

    /* prepare suggestion of order item */

    function get_order_item_suggestion() {
        $key = $_REQUEST["q"];
        $suggestion = array();

        $items = $this->Invoice_items_model->get_item_suggestion($key, $this->login_user->user_type);

        foreach ($items as $item) {
            $suggestion[] = array("id" => $item->title, "text" => $item->title);
        }

        if ($this->login_user->user_type === "staff") {
            $suggestion[] = array("id" => "+", "text" => "+ " . app_lang("create_new_item"));
        }

        echo json_encode($suggestion);
    }

    function get_order_item_info_suggestion() {
        $item = $this->Invoice_items_model->get_item_info_suggestion($this->request->getPost("item_name"), $this->login_user->user_type);
        if ($item) {
            $item->rate = $item->rate ? to_decimal_format($item->rate) : "";
            echo json_encode(array("success" => true, "item_info" => $item));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    function save_order_status($id = 0) {
        validate_numeric_value($id);
        $this->access_only_allowed_members();
        if (!$id) {
            show_404();
        }

        $data = array(
            "status_id" => $this->request->getPost('value')
        );

        $save_id = $this->Orders_model->ci_save($data, $id);

        if ($save_id) {
            log_notification("order_status_updated", array("order_id" => $id));
            $order_info = $this->Orders_model->get_details(array("id" => $id))->getRow();
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => app_lang('record_saved'), "order_status_color" => $order_info->order_status_color));
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /* return a row of order list table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("orders", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Orders_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* load discount modal */

    function discount_modal_form() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "order_id" => "required|numeric"
        ));

        $order_id = $this->request->getPost('order_id');

        $view_data['model_info'] = $this->Orders_model->get_one($order_id);

        return $this->template->view('orders/discount_modal_form', $view_data);
    }

    /* save discount */

    function save_discount() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "order_id" => "required|numeric",
            "discount_type" => "required",
            "discount_amount" => "numeric",
            "discount_amount_type" => "required"
        ));

        $order_id = $this->request->getPost('order_id');

        $data = array(
            "discount_type" => $this->request->getPost('discount_type'),
            "discount_amount" => $this->request->getPost('discount_amount'),
            "discount_amount_type" => $this->request->getPost('discount_amount_type')
        );

        $data = clean_data($data);

        $save_data = $this->Orders_model->ci_save($data, $order_id);
        if ($save_data) {
            echo json_encode(array("success" => true, "order_total_view" => $this->_get_order_total_view($order_id), 'message' => app_lang('record_saved'), "order_id" => $order_id));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of order items, prepared for datatable  */

    function item_list_data($order_id = 0) {
        validate_numeric_value($order_id);
        $this->access_only_allowed_members();

        $list_data = $this->Order_items_model->get_details(array("order_id" => $order_id))->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of order of a specific client, prepared for datatable  */

    function order_list_data_of_client($client_id) {
        validate_numeric_value($client_id);
        $this->check_access_to_store();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("orders", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("client_id" => $client_id, "custom_fields" => $custom_fields, "custom_field_filter" => $this->prepare_custom_field_filter_values("orders", $this->login_user->is_admin, $this->login_user->user_type));

        $list_data = $this->Orders_model->get_details($options)->getResult();
        //echo "<pre>";print_r($list_data);die;
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for orders */

    function validate_orders_file() {
        return validate_post_file($this->request->getPost("file_name"));
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
    
    // Harsh's code for after checkout operations.
    function success_page(){
        $subscription_id = $this->get_subscription($_GET['session_id'], $_GET['order_id']);
        
        $invoice_labels = make_labels_view_data('label', true, true);
        $client_id = $this->login_user->client_id;
        $order = $this->Orders_model->my_recent_order($client_id)->getRow();
        // print_r($order);die;
        
        $clientDueValue = get_setting("type_payment_" . $client_id);
        $clientDueValue = !empty($clientDueValue)? $clientDueValue: 'monthly';
        if( !empty($clientDueValue) && ($clientDueValue == 'monthly') ) {
            $firstDateOfNextMonth =strtotime('first day of next month') ;

            $dateType = 'Monthly';
            $dueDate = date('Y/m/d', $firstDateOfNextMonth);
        }else if( !empty($clientDueValue) && ($clientDueValue == 'weekly') ) {
            $firstDateOfNextMonth =strtotime('next monday') ;
            
            $dateType = 'Weekly';
            $dueDate = date('Y/m/d', $firstDateOfNextMonth);

        }
        
        $invoice_data = array(
            "client_id" => $client_id,
            "project_id" => 0, // $this->request->getPost('invoice_project_id') ? $this->request->getPost('invoice_project_id') : 0,
            "order_id" => $order->id,
            "bill_date" => get_my_local_time(), // $bill_date,
            "due_date" => $dueDate, // $this->request->getPost('invoice_due_date'),
            "tax_id" => 0, // $this->request->getPost('tax_id') ? $this->request->getPost('tax_id') : 0,
            "tax_id2" => 0, //$this->request->getPost('tax_id2') ? $this->request->getPost('tax_id2') : 0,
            "tax_id3" => 0, //$this->request->getPost('tax_id3') ? $this->request->getPost('tax_id3') : 0,
            "company_id" => $order->company_id ? $order->company_id : get_default_company_id(),
            "recurring" => 0, // $recurring,
            "repeat_every" => 0, //$repeat_every ? $repeat_every : 0,
            "repeat_type" => NULL, // $repeat_type ? $repeat_type : NULL,
            "no_of_cycles" => 0, // $no_of_cycles ? $no_of_cycles : 0,
            "note" => '',//$this->request->getPost('invoice_note'),
            "labels" =>  $order->id, //$this->request->getPost('labels').
            "status" =>  $invoice_labels ,// 'not paid',
        );
        $view_data = $invoice_data;
        $view_data['is_community_set'] = $order->is_community_set;

        //  echo '<pre>';
        // print_r($invoice_data);
        // die;
        $invoice_id = $this->Invoices_model->ci_save($invoice_data, 0);

        $copy_items = $this->Order_items_model->get_all_where(array("created_by" => $this->login_user->id, "order_id" => 0, "deleted" => 0))->getResult();

        foreach ($copy_items as $data) {
            $invoice_item_data = array(
                "invoice_id" => $invoice_id,
                "order_item_id" => $data->id ? $data->id : 0,
                "title" => $data->title ? $data->title : "",
                "description" => $data->description ? $data->description : "",
                "quantity" => $data->quantity ? $data->quantity : 0,
                "unit_type" => $data->unit_type ? $data->unit_type : "",
                "rate" => $data->rate ? $data->rate : 0,
                "total" => $data->total ? $data->total : 0,
            
            );

            // echo '<pre>';
            // print_r($invoice_item_data);
            // die;
            $this->Invoice_items_model->ci_save($invoice_item_data);
        }

        return $this->template->rander("/orders/success_page",$view_data);
    }

    function error_page(){
        $subscription_id = $this->get_subscription($_GET['session_id']);
        return $this->template->rander("/orders/error_page");
    }
    
    function cancel_subscription(){
            \Stripe\Stripe::setApiKey('your_stripe_secret_key');
        
            $subscriptionID = 'sub_1234567890'; // Replace with the actual subscription ID
        
            try {
                // Retrieve the subscription
                $subscription = \Stripe\Subscription::retrieve($subscriptionID);
                $subscription->cancel();
        
                // Optionally, you can access the canceled subscription's status
                echo "Subscription canceled: " . $subscription->status;
                
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Handle any errors that may occur
                echo "Error: " . $e->getMessage();
            }
    }

    function get_subscription($session_id,$order_id){
        // Subscription Id fetching from stripe response 

        
        // \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET'));
        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        // Retrieve the session ID from the URL query parameters
        $sessionID = $session_id;

        try {
            // Retrieve the payment session data from Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionID);
            $this->Orders_model->save_response($session,$order_id);
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any errors that may occur
            echo "Error: " . $e->getMessage();
        }
    }

}

/* End of file orders.php */
/* Location: ./app/controllers/orders.php */