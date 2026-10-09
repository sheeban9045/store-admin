<?php

namespace App\Controllers;

require_once(FCPATH . getenv('STRIPE_PATH'));

class Frontend_services extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

     public function index() {
        $view_data['services'] = $this->Services_model->get_details(array("status" => "active"))->getResult();
        return $this->template->rander("frontend_services/index", $view_data);
    }

    public function checkout($id = null) {
        $user_data = $this->login_user;
        if (!$user_data) {
            app_redirect("signin");
        }

        if (!$id) {
            show_404();
        }

        $service = $this->Services_model->get_details(["id" => $id, "status" => "active"])->getRow();
        if (!$service) {
            show_404();
        }

        $view_data['service'] = $service;

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        $view_data['payment_setting'] = $payment_setting;

        return $this->template->rander("frontend_services/checkout", $view_data);
    }

    public function place_order() {
        $post_data = $this->request->getPost();
        $service_id = $post_data['service_id'] ?? null;

        if (!$service_id) {
            echo json_encode(["success" => false, "message" => "No service selected."]);
            return;
        }

        $user_data = $this->login_user;
        if (!$user_data) {
            echo json_encode(["success" => false, "message" => "Please login first."]);
            return;
        }

        $service = $this->Services_model->get_details(["id" => $service_id, "status" => "active"])->getRow();
        if (!$service) {
            echo json_encode(["success" => false, "message" => "Service not found or inactive."]);
            return;
        }

        $amount = floatval($service->price);

        $order_data = [
            "user_id"          => $user_data->id,
            "service_id"       => $service_id,
            "total_amount"     => $amount,
            "payment_gateway"  => "stripe",
            "payment_status"   => "pending",
            "status"           => "pending",
            "created_at"       => get_current_utc_time(),
        ];
        $order_id = $this->Service_orders_model->ci_save($order_data);

        $line_items = [];
        $line_items[] = [
            'price_data' => [
                'currency'     => 'usd',
                'product_data' => ['name' => "Service: " . $service->title],
                'unit_amount'  => (int) round($amount * 100),
            ],
            'quantity' => 1,
        ];

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting     = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        $successURL = getenv('STRIPE_REDIRECT_URL') . '/store-admin/index.php/Frontend_services/success?order_id=' . $order_id . '&session_id={CHECKOUT_SESSION_ID}';
        $cancelURL  = getenv('STRIPE_REDIRECT_URL') . '/store-admin/index.php/Frontend_services/cancel?order_id=' . $order_id . '&session_id={CHECKOUT_SESSION_ID}';

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $line_items,
            'mode'                 => 'payment',
            'invoice_creation'     => ['enabled' => true],
            'success_url'          => $successURL,
            'cancel_url'           => $cancelURL,
            'metadata'             => [
                'order_id' => $order_id,
                'user_id'   => $user_data->id,
            ]
        ]);

        echo json_encode([
            "success"    => true,
            "message"    => "Order placed successfully.",
            "session_id" => $session->id,
        ]);
    }

    public function success() {
        $order_id = intval($this->request->getGet('order_id'));
        $session_id    = $this->request->getGet('session_id');

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting     = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);
        } catch (\Exception $e) {
            $session = null;
        }

        if (isset($session) && $session->payment_status == 'paid') {
            $order = $this->Service_orders_model->get_details(["id" => $order_id])->getRow();
            $user_data = $this->login_user;

            if ($order && $order->status !== 'success') {
                $data = [
                    "payment_status"   => "success",
                    "transaction_id"   => $session->payment_intent,
                    "status"           => "success",
                    "payment_response" => json_encode($session),
                    "updated_at"       => get_current_utc_time(),
                ];

                $this->Service_orders_model->ci_save($data, $order_id);

                $service = $this->Services_model->get_details(["id" => $order->service_id])->getRow();

                // Send emails
            $email     = $user_data->email;
            $user_name = $user_data->first_name . ' ' . $user_data->last_name;
            
            $customer_subject   = "Service Purchase Successful";
            $customer_message   = "
                <h1>Hi, {$user_name}</h1>
                <p>Your payment has been successfully completed for the service: <strong>{$service->title}</strong>.</p>
                <p><strong>Order ID:</strong> ORDER #{$order_id}</p>
                <p><strong>Amount Paid:</strong> $" . number_format($order->total_amount, 2) . "</p>
                <br>
                <p>Thank you for your purchase!</p>
            ";
            send_app_mail($email, $customer_subject, $customer_message);

            $admin_email = get_setting("admin_email");
            if (empty($admin_email)) {
                $admin_email = getenv('ADMIN_EMAIL');
            }
            $admin_subject   = "New Service Purchase";
            $admin_message   = "
                <h1>New Service Purchase</h1>
                <p><strong>Customer:</strong> {$user_name} ({$email})</p>
                <p><strong>Service:</strong> {$service->title}</p>
                <p><strong>Amount Paid:</strong> $" . number_format($order->total_amount, 2) . "</p>
                <p><strong>Order ID:</strong> ORDER #{$order_id}</p>
                <p>Payment was successful.</p>
            ";
            if ($admin_email) {
                send_app_mail($admin_email, $admin_subject, $admin_message);
            }


            if (empty($admin_email)) {
                $admin_users = $this->Users_model->get_all_where(["is_admin" => 1, "deleted" => 0])->getResult();
            } else {
                $admin_users = $this->Users_model->get_all_where(["email" => $admin_email, "deleted" => 0])->getResult();
            }
            foreach ($admin_users as $admin) {
                $message_data = array(
                    "from_user_id" => $user_data->id,
                    "name" => $user_data->first_name . ' ' . $user_data->last_name,
                    "email" => $user_data->email,
                    "to_user_id" => $admin->id,
                    "type" => "enquiry",
                    "subject" => "New Service Purchased: " . $service->title,
                    "message" => "Customer {$user_name} ({$email}) has purchased the service '{$service->title}'. Amount paid: $" . number_format($order->total_amount, 2) . ". Order ID: ORDER #{$order_id}.",
                    "created_at" => get_current_utc_time(),
                    "files" => "a:0:{}",
                    "deleted_by_users" => "",
                );
                $this->Messages_model->ci_save($message_data);
            }

            } else {
                if ($order) {
                    $service = $this->Services_model->get_details(["id" => $order->service_id])->getRow();
                }
            }

            $view_data['message'] = "Payment successful. You have purchased the service" . (isset($service) ? ": " . $service->title : ".");

        } else {
            $data = [
                "payment_status"   => "failed",
                "transaction_id"   => $session->payment_intent ?? null,
                "status"           => "failed",
                "payment_response" => json_encode($session),
                "updated_at"       => get_current_utc_time(),
            ];

            $this->Service_orders_model->ci_save($data, $order_id);
            $view_data['message'] = "Payment failed or cancelled. Please try again.";
        }

        return $this->template->rander("frontend_services/success", $view_data);
    }

    public function cancel() {
        $order_id = intval($this->request->getGet('order_id'));
        $session_id    = $this->request->getGet('session_id');

        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting     = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);
        } catch (\Exception $e) {
            $session = null;
        }

        $status = ($session->payment_status ?? '') == 'unpaid' ? 'cancelled' : 'failed';

        $order = $this->Service_orders_model->get_details(["id" => $order_id])->getRow();

        if ($order && $order->status !== $status && $order->status !== 'success') {
            $data = [
                "payment_status"   => "failed",
                "transaction_id"   => $session->payment_intent ?? null,
                "status"           => $status,
                "payment_response" => json_encode($session),
                "updated_at"       => get_current_utc_time(),
            ];

            $this->Service_orders_model->ci_save($data, $order_id);
        }

        $view_data['message'] = $status === 'cancelled'
            ? "Order cancelled."
            : "Payment failed or cancelled. Please try again.";

        return $this->template->rander("frontend_services/cancel", $view_data);
    }

    public function my_services() {
        if (!$this->login_user) {
            app_redirect("signin");
        }
        return $this->template->rander("frontend_services/my_services");
    }

    public function my_services_list_data() {
        if (!$this->login_user) {
            echo json_encode(["data" => []]);
            return;
        }

        $list_data = $this->Service_orders_model->get_orders_by_user($this->login_user->id)->getResult();

        $result = [];
        foreach ($list_data as $data) {
            if ($data->status !== 'success') {
                continue; // Only show successful purchases
            }
            $result[] = $this->_make_service_row($data);
        }

        echo json_encode(["data" => $result]);
    }

    private function _make_service_row($data) {
        $image_url = "";
        if ($data->service_image) {
            $images = @unserialize($data->service_image);
            if ($images && is_array($images) && count($images) > 0) {
                $image_url = get_source_url_of_file($images, get_setting("timeline_file_path"), "thumbnail");
                $image_url = "<img style='width:50px; height:50px; border-radius:4px;' src='{$image_url}' alt='{$data->service_name}' />";
            }
        }

        $title = modal_anchor(get_uri("Frontend_services/view_service/" . $data->id), $data->service_name, array("class" => "edit", "title" => "Service Details", "data-post-id" => $data->id));

        $order_id = "ORDER #" . $data->id;
        $amount = to_currency($data->total_amount, "$");
        $purchase_date = format_to_datetime($data->created_at);

        $status = "<span class='badge bg-success' style='background: #0abb87 !important;'>Success</span>";
        
        $action = '<div style="display: flex; gap: 8px;">';
        $action .= modal_anchor(get_uri("Frontend_services/view_service/" . $data->id), "<i data-feather='eye' class='icon-16'></i> <span style='font-size: 13px; font-weight: 500;'>Details</span>", array("title" => "Service Details", "data-post-id" => $data->id, "style" => "background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid #bae6fd; text-decoration: none;"));
        $action .= modal_anchor(get_uri("Frontend_services/view_invoice?order_id=" . $data->id), "<i data-feather='file-text' class='icon-16'></i> <span style='font-size: 13px; font-weight: 500;'>Invoice</span>", array("title" => "View Invoice", "data-post-id" => $data->id, "style" => "background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid #bbf7d0; text-decoration: none;"));
        $action .= '</div>';

        return [
            $image_url,
            $title,
            $order_id,
            $amount,
            $purchase_date,
            $status,
            $action
        ];
    }

    public function view_service($order_id) {
        if (!$this->login_user) {
            return;
        }

        $order = $this->Service_orders_model->get_details(["id" => $order_id, "user_id" => $this->login_user->id, "status" => "success"])->getRow();
        if (!$order) {
            echo "Access denied or order not found.";
            return;
        }

        $service = $this->Services_model->get_details(["id" => $order->service_id])->getRow();
        
        $view_data['order'] = $order;
        $view_data['service'] = $service;

        return $this->template->view("frontend_services/view_service", $view_data);
    }

    public function view_invoice() {
        if (!$this->login_user) {
            return false;
        }

        $order_id = $_GET['order_id'];
        
        // Ensure user is authorized to view this order
        $order_detail = $this->Service_orders_model->get_details(["id" => $order_id])->getRow();
        if (!$order_detail) {
            return false;
        }
        
        // If not admin, restrict to own orders
        if (!$this->login_user->is_admin && $order_detail->user_id != $this->login_user->id) {
            return false;
        }

        $service = $this->Services_model->get_details(["id" => $order_detail->service_id])->getRow();
        $user_detail = $this->Users_model->get_one($order_detail->user_id);
        
        if (empty($order_detail->payment_response)) {
            return false;
        }
        
        $view_data['user_detail'] = $user_detail;
        $view_data['order_detail'] = $order_detail;
        $view_data['service'] = $service;
        
        $stripeResponseObj = json_decode($order_detail->payment_response);
        
        $stripePaymentMethod = $this->Payment_methods_model->get_oneline_payment_method('stripe');
        $payment_setting = $this->Payment_methods_model->get_one_with_settings($stripePaymentMethod->id);

        if (empty($payment_setting->secret_key) || empty($payment_setting->publishable_key)) {
            return false;
        }
        \Stripe\Stripe::setApiKey($payment_setting->secret_key);

        $subscriptionId = isset($stripeResponseObj->subscription) ? $stripeResponseObj->subscription : '';
        $paymentIntentId = isset($stripeResponseObj->payment_intent) ? $stripeResponseObj->payment_intent : '';
        $invoiceId = isset($stripeResponseObj->invoice) ? $stripeResponseObj->invoice : '';

        $allInvoices = [];
        if (!empty($subscriptionId)) {
            $allInvoices = $this->getInvoicesBySubscriptionId($subscriptionId);
        } elseif (!empty($invoiceId)) {
            try {
                $invoice = \Stripe\Invoice::retrieve($invoiceId);
                $timestamp = isset($invoice->status_transitions->paid_at) ? $invoice->status_transitions->paid_at : $invoice->created;
                $allInvoices[] = [
                    'invoice_id' => $invoice->id,
                    'amount_due' => $invoice->amount_due,
                    'status' => $invoice->status,
                    'status_date' => date('Y-m-d H:i:s', $timestamp),
                    'interval' => 'one-time',
                    'url' => $invoice->hosted_invoice_url,
                ];
            } catch (\Exception $e) {
                // Ignore
            }
        } elseif (!empty($paymentIntentId)) {
            try {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                // In some cases, the payment intent itself has the invoice attached
                if (!empty($paymentIntent->invoice)) {
                    $invoice = \Stripe\Invoice::retrieve($paymentIntent->invoice);
                    $timestamp = isset($invoice->status_transitions->paid_at) ? $invoice->status_transitions->paid_at : $invoice->created;
                    $allInvoices[] = [
                        'invoice_id' => $invoice->id,
                        'amount_due' => $invoice->amount_due,
                        'status' => $invoice->status,
                        'status_date' => date('Y-m-d H:i:s', $timestamp),
                        'interval' => 'one-time',
                        'url' => $invoice->hosted_invoice_url,
                    ];
                } else if (isset($paymentIntent->latest_charge) && $paymentIntent->latest_charge) {
                    $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);
                    $allInvoices[] = [
                        'invoice_id' => $charge->id,
                        'amount_due' => $charge->amount,
                        'status' => $charge->status == 'succeeded' ? 'paid' : $charge->status,
                        'status_date' => date('Y-m-d H:i:s', $charge->created),
                        'interval' => 'one-time',
                        'url' => !empty($charge->receipt_url) ? $charge->receipt_url : '',
                    ];
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        if (!empty($allInvoices)) {
            foreach ($allInvoices as &$item) {
                $item['invoice_title'] = !empty($service) ? $service->title : 'null';
            }
            $view_data['all_invoices'] = $allInvoices;
        }
        
        return $this->template->view('frontend_services/view_invoice', $view_data);
    }

    protected function getInvoicesBySubscriptionId($subscriptionId) {
        try {
            $invoices = \Stripe\Invoice::all([
                'subscription' => $subscriptionId,
            ]);

            $invoiceDetails = [];
            
            foreach ($invoices->data as $invoice) {
                $timestamp = isset($invoice->status_transitions->paid_at) ? $invoice->status_transitions->paid_at : $invoice->created;
                $date = date('Y-m-d H:i:s', $timestamp);
                $invoiceDetails[] = [
                    'invoice_id' => $invoice->id,
                    'amount_due' => $invoice->amount_due,
                    'status' => $invoice->status,
                    'status_date' => $date,
                    'interval' => isset($invoice['lines']['data'][0]['plan']['interval']) ? $invoice['lines']['data'][0]['plan']['interval'] : 'one-time',
                    'url' => $invoice->hosted_invoice_url,
                ];
            }

            return $invoiceDetails;

        } catch (\Exception $e) {
            return [];
        }
    }

}
