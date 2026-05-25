<?php

namespace App\Controllers;
require_once(FCPATH . getenv('STRIPE_PATH'));
class Webhut_plugins extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->access_only_team_members();
        return $this->template->rander("webhut_plugins/index");
    }

    function list_data() {
        $this->access_only_team_members();

        $list_data = $this->Webhut_plugins_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_row($data) {
        // Icon display
        $icon = "";
        if ($data->icon) {
            $icon = "<img src='" . base_url("uploads/plugins/icons/" . $data->icon) . "' style='width:40px;height:40px;object-fit:cover;border-radius:6px;' />";
        }

        // Status badge
        $status = $data->status == "active"
            ? "<span class='badge bg-success'>" . app_lang('active') . "</span>"
            : "<span class='badge bg-danger'>" . app_lang('inactive') . "</span>";

        // Discount
        $discount = $data->discount_value
            ? ($data->discount_type == "percentage" ? $data->discount_value . "%" : "$" . $data->discount_value)
            : "-";

        return array(
            $icon,
            $data->name,
            $data->code,
            $data->version,
            $data->rate,
            $discount,
            $status,
            modal_anchor(get_uri("webhut_plugins/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array(
                "class" => "edit",
                "title" => app_lang('edit'),
                "data-post-id" => $data->id
            ))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array(
                'title' => app_lang('delete'),
                "class" => "delete",
                "data-id" => $data->id,
                "data-action-url" => get_uri("webhut_plugins/delete"),
                "data-action" => "delete"
            ))
        );
    }

    function modal_form() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Webhut_plugins_model->get_one($this->request->getPost('id'));
        return $this->template->view('webhut_plugins/modal_form', $view_data);
    }

    function save() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id"             => "numeric",
            "name"           => "required",
            "code"           => "required",
            "version"        => "required",
            "rate"           => "required|numeric",
            "discount_value" => "numeric",
        ));

        $id   = $this->request->getPost('id');
        $code = $this->request->getPost('code');
        $version = $this->request->getPost('version');
        $clean_code = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($code));
    
        // --- ZIP file upload ---
        $zip_file = $this->request->getPost('hidden_zip_file');
        $zip = $this->request->getFile('zip_file');

        if ($zip && $zip->isValid()) {
            
            // OLD ZIP DELETE
            if (!empty($zip_file)) {
                $old_zip_path = FCPATH . "uploads/plugins/zips/" . $zip_file;

                if (file_exists($old_zip_path)) {
                    unlink($old_zip_path);
                }
            }

            $clean_version = str_replace('.', '_', $version);
            $zip_name = $clean_code . "_" . $clean_version . ".zip";
            
            $zip->move(FCPATH . "uploads/plugins/zips/", $zip_name, true);
            $zip_file = $zip_name;
        }

        // --- Icon upload ---
        $icon = $this->request->getPost('hidden_icon');
        $icon_file = $this->request->getFile('icon');

        if ($icon_file && $icon_file->isValid()) {
            // OLD ICON  DELETE
            if (!empty($icon)) {
                $old_icon_path = FCPATH . "uploads/plugins/icons/" . $icon;

                if (file_exists($old_icon_path)) {
                    unlink($old_icon_path);
                }
            }
            
            $clean_version = str_replace('.', '_', $version);
            $icon_name = $clean_code . "_" . $clean_version . ".png";

            $icon_file->move(FCPATH . "uploads/plugins/icons/", $icon_name, true);
            $icon = $icon_name;
        }

        // --- Photos upload (multiple) ---
        $photos = json_decode($this->request->getPost('existing_photos') ?? '[]', true);
        $photo_files = $this->request->getFiles();

        if (!empty($photo_files['photos'])) {
            foreach ($photo_files['photos'] as $photo) {
                if ($photo->isValid()) {
                    $photo_name = time() . "_" . $photo->getName();
                    $photo->move(FCPATH . "uploads/plugins/photos/", $photo_name);
                    $photos[] = $photo_name;
                }
            }
        }

        // --- Final Data ---
        $plugin_data = array(
            "name"           => $this->request->getPost('name'),
            "description"    => $this->request->getPost('description'),
            "code"           => $clean_code,
            "discount_type"  => $this->request->getPost('discount_type'),
            "discount_value" => $this->request->getPost('discount_value') ?? 0,
            "status"         => $this->request->getPost('status') ?? "inactive",
            "rate"           => $this->request->getPost('rate'),
            "version"        => $version,
            "zip_file"       => $zip_file,
            "icon"           => $icon,
            "photos"         => json_encode($photos),
        );

        $plugin_id = $this->Webhut_plugins_model->ci_save($plugin_data, $id);

        if ($plugin_id) {
            $options = array("id" => $plugin_id);
            $plugin_info = $this->Webhut_plugins_model->get_details($options)->getRow();

            echo json_encode(array(
                "success" => true,
                "id"      => $plugin_info->id,
                "data"    => $this->_make_row($plugin_info),
                "message" => app_lang('record_saved')
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('error_occurred')
            ));
        }
    }

    function delete() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->request->getPost('undo')) {
            if ($this->Webhut_plugins_model->delete($id, true)) {
                $options = array("id" => $id);
                $plugin_info = $this->Webhut_plugins_model->get_details($options)->getRow();
                echo json_encode(array(
                    "success" => true,
                    "id"      => $plugin_info->id,
                    "data"    => $this->_make_row($plugin_info),
                    "message" => app_lang('record_undone')
                ));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang('error_occurred')));
            }
        } else {
            if ($this->Webhut_plugins_model->delete($id)) {
                $plugin_info = $this->Webhut_plugins_model->get_one($id);
                echo json_encode(array(
                    "success"  => true,
                    "id"       => $plugin_info->id,
                    "message"  => app_lang('record_deleted')
                ));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    public function plugin_details($id = null) {
        if(!$id) show_404();
                
        $options = array("id" => $id);
        $view_data['plugin_data'] = $this->Webhut_plugins_model->get_details($options)->getRow();
        if (!$view_data['plugin_data']) {
            show_404();
        }
   
        return $this->template->rander('webhut_plugins/plugin_details', $view_data);
    }

    public function checkout($id = null) {
        if(!$id) show_404();       

        $user_data = $this->login_user;

        $my_orders =  $this->Orders_model->my_orders($user_data->id)->getResult();        

        for( $i = 0; $i < count($my_orders); $i++){
            if($my_orders[$i]->is_community_set == 1 && $my_orders[$i]->deleted == 0 && $my_orders[$i]->is_domain_created == 1){
                $view_data['communities'][] = $my_orders[$i]->domain_name;
            }            
        }

        $view_data['plugin_data'] = $this->Webhut_plugins_model->get_details(array("id" => $id))->getRow();
        // echo "<pre>";print_r($view_data['communities']);echo "</pre>";die;

        if (!$view_data['plugin_data']) show_404();

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        $view_data['payment_setting'] = $payment_setting;

        return $this->template->rander("webhut_plugins/checkout", $view_data);
    }

    public function place_order(){

        $post_data = $this->request->getPost();

        $plugin_id = $post_data['plugin_id'] ?? null;
        $community = $post_data['community'] ?? null;

        if(!$plugin_id){
            echo json_encode(array(
                "success" => false,
                "message" => "Plugin is required."
            ));
            return;
        }

        if(!$community){
            echo json_encode(array(
                "success" => false,
                "message" => "Community is required."
            ));
            return;
        }

        $user_data = $this->login_user;
        
        $where = "domain_name = '".$community."' AND is_community_set = 1 AND deleted = 0 AND is_domain_created = 1";
        $is_community_exist =  $this->Orders_model->get_community_by_client($user_data->id, $where)->getResult();
        
        if(!$is_community_exist){
            echo json_encode(array(
                "success" => false,
                "message" => "Selected community does not exist."
            ));
            return;
        }

        $community_path = FCPATH . "../" . $community;

        if (!is_dir($community_path)) {
            echo json_encode(array(
                "success" => false,
                "message" => "Selected community folder does not exist on the server. Please contact admin."
            ));
            return;
        }

        $where = "plugin_id = $plugin_id AND user_id = $user_data->id AND community = '".$community."' AND payment_status = 'success' AND status = 'success'";
        $purchased_plugins = $this->Webhut_orders_model->get_plugin_purchased_by_client($where)->getResult();

        if(!empty($purchased_plugins)){
            echo json_encode(array(
                "success" => false,
                "message" => "You have already purchased this plugin for the selected community."
            ));
            return;
        }

        $get_plugin_data = $this->Webhut_plugins_model->get_details(array("id" => $plugin_id))->getRow();

        if(!$get_plugin_data){
            echo json_encode(array(
                "success" => false,
                "message" => "Plugin not found."
            ));
            return;
        }

        $zip_file = $get_plugin_data->zip_file;

        $source = FCPATH . "uploads/plugins/zips/" . $zip_file;

        if (!$zip_file || !file_exists($source)) {
            echo json_encode(array(
                "success" => false,
                "message" => "Plugin file is missing. Please contact admin."
            ));
            return;
        }

        $discount_type = $get_plugin_data->discount_type;
        $discount_value = $get_plugin_data->discount_value;
        
        $amount = $get_plugin_data->rate;

        if($discount_type === 'percentage'){
            $total_amount = $amount * (1 - $discount_value / 100);
        }else{
            $total_amount = $amount - $discount_value;
        }

        $order_data = [
            "user_id" => $user_data->id,
            "community" => $community,
            "plugin_id" => $plugin_id,
            "total_amount" => $total_amount,
            "payment_gateway" => "stripe",
        ];

        $order_id = $this->Webhut_orders_model->ci_save($order_data);

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        $amount_in_paise = (int) round($total_amount * 100);

        $successURL = getenv('STRIPE_REDIRECT_URL') . '/store-admin/index.php/Webhut_plugins/success?order_id=' . $order_id . '&session_id={CHECKOUT_SESSION_ID}';

        $cancelURL = getenv('STRIPE_REDIRECT_URL') . '/store-admin/index.php/Webhut_plugins/cancel?order_id=' . $order_id . '&session_id={CHECKOUT_SESSION_ID}';

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $get_plugin_data->name,
                    ],
                    'unit_amount' => $amount_in_paise,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successURL,
            'cancel_url' => $cancelURL,
            'metadata' => [
                'order_id' => $order_id,
                'user_id' => $user_data->id
            ]
        ]);

        echo json_encode(array(
            "success" => true,
            "message" => "Order placed successfully.",
            "session_id" => $session->id
        )); 
    }

    public function success()
    {
        $order_id = $this->request->getGet('order_id');
        $session_id = $this->request->getGet('session_id');

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        $session = \Stripe\Checkout\Session::retrieve($session_id);

        if (isset($session) && $session->payment_status == 'paid') {
            $order_data = [
                "payment_status"   => "success",
                "transaction_id"   => $session->payment_intent,
                "status"           => "success",
                "payment_response" => json_encode($session),
            ];

            $this->Webhut_orders_model->ci_save($order_data, $order_id);

            $order = $this->Webhut_orders_model
                ->get_details(["id" => $order_id])
                ->getRow();

            if (!$order) {
                log_message('error', 'Order not found: ' . $order_id);
                $order_data = [
                    "status" => "failed",
                ];

                $this->Webhut_orders_model->ci_save($order_data, $order_id);
                $view_data['message'] = "Order not found.";
                return $this->template->rander("webhut_plugins/success", $view_data); 
            }

            $plugin = $this->Webhut_plugins_model
                ->get_details(["id" => $order->plugin_id])
                ->getRow();

            if (!$plugin) {
                log_message('error', 'Plugin not found: ' . $order->plugin_id);
                $order_data = [
                    "status" => "failed",
                ];

                $this->Webhut_orders_model->ci_save($order_data, $order_id);
                $view_data['message'] = "Plugin not found.";
                return $this->template->rander("webhut_plugins/success", $view_data); 
            }

            $community = basename($order->community);

            $copied = $this->copy_plugin_zip($plugin->zip_file, $community);

            if ($copied) {
                $user_data = $this->login_user;
                $email = $user_data->email;

                $subject = "Plugin Purchase Successful - Webhut";
                $user_name = $user_data->first_name . ' ' . $user_data->last_name;
                $message = "
                    <h1>Hi, {$user_name}</h1>

                    <p>Your payment has been successfully completed.</p>

                    <p><strong>Order ID:</strong> #{$order_id}</p>
                    <p><strong>Plugin:</strong> {$plugin->name}</p>
                    <p><strong>Community:</strong> {$community}</p>

                    <p>The plugin has been successfully added to your account and is now ready for installation.</p>

                    <p>You can install and manage your plugin from your dashboard.</p>

                    <br>

                    <p><a href='" . base_url('Webhut_plugins/history') . "' 
                    style='padding:10px 15px; background:#28a745; color:#fff; text-decoration:none; border-radius:5px;'>
                    View Purchase History
                    </a></p>

                    <br>

                    <p>Thank you for choosing Webhut!</p>

                    <p>Best Regards,<br>
                    Webhut Team</p>
                    ";

                // send_app_mail($email, $subject, $message);
                $view_data['message'] = "Payment successful. Plugin ready for installation.";
            } else {
                $order_data = [
                    "status" => "failed",
                ];

                $this->Webhut_orders_model->ci_save($order_data, $order_id);
                $view_data['message'] = "Payment successful, but plugin file copy failed.";
            }
        }else{
            $order_data = [
                "payment_status" => "failed",
                "transaction_id" => isset($session->payment_intent) ? $session->payment_intent : null,
                "status" => "failed",
                "payment_response" => json_encode($session),
            ];

            $this->Webhut_orders_model->ci_save($order_data, $order_id);

            $view_data['message'] = "Payment failed or cancelled. Please try again.";
        }       

        return $this->template->rander("webhut_plugins/success", $view_data);
    }

    private function copy_plugin_zip($zip_file, $community)
    {
        $source = FCPATH . "uploads/plugins/zips/" . $zip_file;
        $destination_dir = FCPATH . "../" . $community . "/uploads/plugins/zips/";
        $destination = $destination_dir . $zip_file;

        // 🔹 Check source file
        if (!file_exists($source)) {
            log_message('error', 'Source zip not found: ' . $source);
            return false;
        }

        // 🔹 Create destination folder if not exists
        if (!is_dir($destination_dir)) {
            if (!mkdir($destination_dir, 0777, true)) {
                log_message('error', 'Failed to create directory: ' . $destination_dir);
                return false;
            }
        }

        // 🔹 Copy file
        if (!copy($source, $destination)) {
            log_message('error', 'Failed to copy zip file to: ' . $destination);
            return false;
        }

        log_message('info', 'Plugin zip copied successfully to: ' . $destination);

        return true;
    }

    public function cancel()
    {
        $order_id = $this->request->getGet('order_id');
        $session_id = $this->request->getGet('session_id');

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        $session = \Stripe\Checkout\Session::retrieve($session_id);


        if (isset($session) && $session->payment_status == 'unpaid') {
            $order_data = [
                "payment_status" => "failed",
                "transaction_id" => isset($session->payment_intent) ? $session->payment_intent : null,
                "status" => "cancelled",
                "payment_response" => json_encode($session),
            ];

            $this->Webhut_orders_model->ci_save($order_data, $order_id);

            $view_data['message'] = "Order cancelled.";
        }else{
            $order_data = [
                "payment_status" => "failed",
                "transaction_id" => isset($session->payment_intent) ? $session->payment_intent : null,
                "status" => "failed",
                "payment_response" => json_encode($session),
            ];

            $this->Webhut_orders_model->ci_save($order_data, $order_id);

            $view_data['message'] = "Payment failed or cancelled. Please try again.";
        }

        return $this->template->rander("webhut_plugins/cancel", $view_data);
    }

    public function history() {
        $user_data = $this->login_user;

        $orders = $this->Webhut_orders_model->get_orders_by_user($user_data->id)->getResult();

        $view_data['orders'] = $orders;

        return $this->template->rander("webhut_plugins/history", $view_data);
    }

    public function history_data() {
        $user_id = $this->login_user->id;

        $list_data = $this->Webhut_orders_model
            ->get_orders_by_user($user_id)
            ->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_history_row($data);
        }

        echo json_encode(array("data" => $result));
    }

    private function _make_history_row($data) {
        $icon = "";
        if (isset($data->plugin_icon) && $data->plugin_icon) {
            $icon = "<img src='" . base_url("uploads/plugins/icons/" . $data->plugin_icon) . "' style='width:40px;height:40px;object-fit:cover;border-radius:6px;' />";
        }

        $plugin_name = $data->plugin_name ? $data->plugin_name : "-";

        $community = $data->community ? $data->community : "-";

        $amount = number_format($data->total_amount, 2);

        $payment_status = $data->payment_status == "success"
            ? "<span class='badge bg-success'>" . app_lang('success') . "</span>"
            : "<span class='badge bg-danger'>" . app_lang('failed') . "</span>";

        if ($data->status == "success") {
            $status = "<span class='badge bg-success'>" . app_lang('completed') . "</span>";
        } elseif ($data->status == "cancelled") {
            $status = "<span class='badge bg-warning'>" . app_lang('cancelled') . "</span>";
        } else {
            $status = "<span class='badge bg-danger'>" . app_lang('failed') . "</span>";
        }

        $date = date("d M Y", strtotime($data->created_at));

        return array(
            $icon,
            $plugin_name,
            $community,
            $amount,
            $payment_status,
            $status,
            $date
        );
    }
}