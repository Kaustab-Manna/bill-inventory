<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\VendorModel;
use App\Models\PurchaseReturnModel;

class PayableController extends BaseController
{
    public function index()
    {
        $purchaseModel = new PurchaseModel();
        $vendorModel = new VendorModel();
        $purchaseReturnModel = new PurchaseReturnModel();

        // Get all unpaid or partially paid purchase invoices
        $unpaidPurchases = $purchaseModel
            ->select('purchases.*, vendors.name as vendor_name')
            ->join('vendors', 'vendors.id = purchases.vendor_id', 'left')
            ->whereIn('purchases.payment_status', ['unpaid', 'partial'])
            ->orderBy('purchases.purchase_date', 'ASC')
            ->findAll();

        // Compute balances grouped by Vendor
        $vendors = $vendorModel->where('is_active', 1)->findAll();
        
        $allPurchases = $purchaseModel->findAll();
        $allReturns = $purchaseReturnModel->findAll();

        $vendorBalances = [];

        foreach ($vendors as $v) {
            $vendorTotalPurchases = 0;
            $vendorTotalPaid = 0;
            $vendorTotalReturns = 0;

            foreach ($allPurchases as $p) {
                if ($p->vendor_id == $v->id) {
                    $vendorTotalPurchases += (float)$p->total_amount;
                    $vendorTotalPaid += (float)$p->paid_amount;
                }
            }

            foreach ($allReturns as $r) {
                if ($r->vendor_id == $v->id) {
                    $vendorTotalReturns += (float)$r->total_amount;
                }
            }

            $outstanding = $vendorTotalPurchases - $vendorTotalPaid - $vendorTotalReturns;
            
            if ($outstanding > 0) {
                $vendorBalances[] = [
                    'vendor' => $v,
                    'outstanding' => $outstanding
                ];
            }
        }

        // Also account for unassigned / direct purchases with pending dues
        $directPurchases = 0;
        $directPaid = 0;
        foreach ($allPurchases as $p) {
            if (empty($p->vendor_id)) {
                $directPurchases += (float)$p->total_amount;
                $directPaid += (float)$p->paid_amount;
            }
        }
        $directOutstanding = $directPurchases - $directPaid;
        if ($directOutstanding > 0) {
            $vendorBalances[] = [
                'vendor' => (object)[
                    'id'    => 0,
                    'name'  => 'Direct / General Vendors',
                    'phone' => 'Direct Procurement'
                ],
                'outstanding' => $directOutstanding
            ];
        }

        // Sort by outstanding desc
        usort($vendorBalances, function($a, $b) {
            return $b['outstanding'] <=> $a['outstanding'];
        });

        // Compute Total Accounts Payable from all unpaid and partially paid purchase invoices
        $totalPayable = 0;
        foreach ($unpaidPurchases as $p) {
            $due = (float)$p->total_amount - (float)$p->paid_amount;
            if ($due > 0) {
                $totalPayable += $due;
            }
        }

        $data = [
            'pageTitle' => 'Accounts Payable',
            'unpaidPurchases' => $unpaidPurchases,
            'vendorBalances' => $vendorBalances,
            'totalPayable' => $totalPayable
        ];

        return view('finance/payables/index', $data);
    }
}
