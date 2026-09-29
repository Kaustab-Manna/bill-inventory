<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\SaleModel;
use App\Models\PurchaseModel;
use App\Models\CustomerModel;
use App\Models\VendorModel;

class PaymentController extends BaseController
{
    protected $paymentModel;
    protected $saleModel;
    protected $purchaseModel;
    protected $db;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->saleModel = new SaleModel();
        $this->purchaseModel = new PurchaseModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $this->ensurePaymentSchema();

        try {
            $payments = $this->paymentModel
                        ->select('payments.*, customers.name as customer_name, vendors.name as vendor_name')
                        ->join('customers', 'customers.id = payments.customer_id', 'left')
                        ->join('vendors', 'vendors.id = payments.vendor_id', 'left')
                        ->orderBy('payments.id', 'DESC')
                        ->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'Payment index fetch error: ' . $e->getMessage());
            $payments = $this->paymentModel->orderBy('id', 'DESC')->findAll();
        }

        $data = [
            'pageTitle' => 'Payments',
            'payments'  => $payments
        ];
        return view('payments/index', $data);
    }

    public function create()
    {
        $this->ensurePaymentSchema();

        $customerModel = new CustomerModel();
        $vendorModel = new VendorModel();

        $data = [
            'pageTitle' => 'Record Payment',
            'customers' => $customerModel->where('is_active', 1)->findAll(),
            'vendors' => $vendorModel->where('is_active', 1)->findAll()
        ];
        return view('payments/create', $data);
    }

    public function store()
    {
        $this->ensurePaymentSchema();

        $post = $this->request->getPost();
        
        $this->db->transStart();

        $type = $post['type']; // 'in' or 'out'
        $amount = (float) $post['amount'];

        $paymentData = [
            'payment_no' => 'PAY-' . strtoupper(uniqid()),
            'payment_date' => $post['payment_date'] ?: date('Y-m-d'),
            'type' => $type,
            'amount' => $amount,
            'payment_method' => $post['payment_method'],
            'reference_no' => $post['reference_no'] ?? null,
            'notes' => $post['notes'] ?? null,
            'created_by' => session()->get('user_id')
        ];

        // For Money IN (Customer)
        if ($type === 'in' && !empty($post['customer_id'])) {
            $paymentData['customer_id'] = $post['customer_id'];
            
            // If they linked a specific invoice
            if (!empty($post['sale_id'])) {
                $paymentData['sale_id'] = $post['sale_id'];
                $this->updateSalePaymentStatus($post['sale_id'], $amount);
            }
        } 
        // For Money OUT (Vendor)
        elseif ($type === 'out' && !empty($post['vendor_id'])) {
            $paymentData['vendor_id'] = $post['vendor_id'];

            if (!empty($post['purchase_id'])) {
                $paymentData['purchase_id'] = $post['purchase_id'];
                $this->updatePurchasePaymentStatus($post['purchase_id'], $amount);
            }
        }

        $this->paymentModel->insert($paymentData);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to record payment.');
        }

        return redirect()->to('/payments')->with('success', 'Payment recorded successfully.');
    }

    private function updateSalePaymentStatus($saleId, $amountToAdd)
    {
        $sale = $this->saleModel->find($saleId);
        if ($sale) {
            $newPaid = $sale->paid_amount + $amountToAdd;
            $status = 'unpaid';
            if ($newPaid >= $sale->total_amount) {
                $status = 'paid';
                $newPaid = $sale->total_amount;
            } elseif ($newPaid > 0) {
                $status = 'partial';
            }
            $this->saleModel->update($saleId, ['paid_amount' => $newPaid, 'status' => $status]);
        }
    }

    private function updatePurchasePaymentStatus($purchaseId, $amountToAdd)
    {
        $purchase = $this->purchaseModel->find($purchaseId);
        if ($purchase) {
            $newPaid = $purchase->paid_amount + $amountToAdd;
            $status = 'unpaid';
            if ($newPaid >= $purchase->total_amount) {
                $status = 'paid';
                $newPaid = $purchase->total_amount;
            } elseif ($newPaid > 0) {
                $status = 'partial';
            }
            $this->purchaseModel->update($purchaseId, ['paid_amount' => $newPaid, 'payment_status' => $status]);
        }
    }

    public function delete($id)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Payment not found']);
            }
            return redirect()->to('/payments')->with('error', 'Payment not found.');
        }

        $this->db->transStart();

        // Revert Sale status
        if (!empty($payment->sale_id)) {
            $this->updateSalePaymentStatus($payment->sale_id, -$payment->amount);
        }

        // Revert Purchase status
        if (!empty($payment->purchase_id)) {
            $this->updatePurchasePaymentStatus($payment->purchase_id, -$payment->amount);
        }

        $this->paymentModel->delete($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete payment.']);
            }
            return redirect()->to('/payments')->with('error', 'Failed to delete payment.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Payment deleted successfully.']);
        }
        return redirect()->to('/payments')->with('success', 'Payment deleted successfully.');
    }

    protected function ensurePaymentSchema()
    {
        try {
            $db = $this->db;
            if (!$db->tableExists('payments')) {
                $this->ensureSchema($db);
                return;
            }

            if (!$db->fieldExists('customer_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `customer_id` BIGINT UNSIGNED NULL AFTER `payment_date`");
            }
            if (!$db->fieldExists('vendor_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `vendor_id` BIGINT UNSIGNED NULL AFTER `customer_id`");
            }
            if (!$db->fieldExists('sale_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `sale_id` BIGINT UNSIGNED NULL AFTER `vendor_id`");
            }
            if (!$db->fieldExists('purchase_id', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `purchase_id` BIGINT UNSIGNED NULL AFTER `sale_id`");
            }
            if (!$db->fieldExists('type', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `type` ENUM('in', 'out') DEFAULT 'in' AFTER `payment_date`");
            }
            if (!$db->fieldExists('payment_method', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `payment_method` VARCHAR(50) DEFAULT 'Cash' AFTER `amount`");
            }
            if (!$db->fieldExists('payment_date', 'payments')) {
                $db->query("ALTER TABLE `payments` ADD `payment_date` DATE NOT NULL AFTER `payment_no`");
            }

            if ($db->fieldExists('party_type', 'payments') && $db->fieldExists('party_id', 'payments')) {
                try {
                    $db->query("UPDATE `payments` SET `customer_id` = `party_id`, `type` = 'in' WHERE `customer_id` IS NULL AND `party_type` = 'customer'");
                    $db->query("UPDATE `payments` SET `vendor_id` = `party_id`, `type` = 'out' WHERE `vendor_id` IS NULL AND `party_type` = 'vendor'");
                } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {
            log_message('error', 'ensurePaymentSchema error: ' . $e->getMessage());
        }
    }
}
