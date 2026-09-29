<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\CustomerModel;
use App\Models\SaleReturnModel;

class ReceivableController extends BaseController
{
    public function index()
    {
        $saleModel = new SaleModel();
        $customerModel = new CustomerModel();
        $saleReturnModel = new SaleReturnModel();

        // Get all unpaid or partially paid invoices
        $unpaidInvoices = $saleModel
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->whereIn('sales.status', ['unpaid', 'partial'])
            ->orderBy('sales.sale_date', 'ASC')
            ->findAll();

        // Compute balances grouped by Customer
        $customers = $customerModel->where('is_active', 1)->findAll();
        
        $allSales = $saleModel->findAll();
        $allReturns = $saleReturnModel->findAll();

        $customerBalances = [];

        foreach ($customers as $c) {
            $customerTotalSales = 0;
            $customerTotalPaid = 0;
            $customerTotalReturns = 0;

            foreach ($allSales as $s) {
                if ($s->customer_id == $c->id) {
                    $customerTotalSales += (float)$s->total_amount;
                    $customerTotalPaid += (float)$s->paid_amount;
                }
            }

            foreach ($allReturns as $r) {
                if ($r->customer_id == $c->id) {
                    $customerTotalReturns += (float)$r->total_amount;
                }
            }

            $outstanding = $customerTotalSales - $customerTotalPaid - $customerTotalReturns;
            
            if ($outstanding > 0) {
                $customerBalances[] = [
                    'customer' => $c,
                    'outstanding' => $outstanding
                ];
            }
        }

        // Also account for Walk-in / unassigned customer sales with pending dues
        $walkInSales = 0;
        $walkInPaid = 0;
        $walkInReturns = 0;

        foreach ($allSales as $s) {
            if (empty($s->customer_id)) {
                $walkInSales += (float)$s->total_amount;
                $walkInPaid += (float)$s->paid_amount;
            }
        }

        foreach ($allReturns as $r) {
            if (empty($r->customer_id)) {
                $walkInReturns += (float)$r->total_amount;
            }
        }

        $walkInOutstanding = $walkInSales - $walkInPaid - $walkInReturns;
        if ($walkInOutstanding > 0) {
            $customerBalances[] = [
                'customer' => (object)[
                    'id'    => 0,
                    'name'  => 'Walk-in Customers',
                    'phone' => 'Direct / POS Sales'
                ],
                'outstanding' => $walkInOutstanding
            ];
        }

        // Sort by outstanding desc
        usort($customerBalances, function($a, $b) {
            return $b['outstanding'] <=> $a['outstanding'];
        });

        // Compute Total Accounts Receivable from all unpaid and partially paid invoices
        $totalReceivable = 0;
        foreach ($unpaidInvoices as $inv) {
            $due = (float)$inv->total_amount - (float)$inv->paid_amount;
            if ($due > 0) {
                $totalReceivable += $due;
            }
        }

        $data = [
            'pageTitle' => 'Accounts Receivable',
            'unpaidInvoices' => $unpaidInvoices,
            'customerBalances' => $customerBalances,
            'totalReceivable' => $totalReceivable
        ];

        return view('finance/receivables/index', $data);
    }
}
