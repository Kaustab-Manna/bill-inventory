<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SaleModel;
use App\Models\SaleReturnModel;

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $saleModel;
    protected $saleReturnModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->saleModel = new SaleModel();
        $this->saleReturnModel = new SaleReturnModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Customers',
            'customers' => $this->customerModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('customers/index', $data);
    }

    public function create()
    {
        return view('customers/create', ['pageTitle' => 'Add Customer']);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[150]',
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
            'email' => 'permit_empty|valid_email',
        ];
        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $this->customerModel->insert($data);
        return redirect()->to('/customers')->with('success', 'Customer added successfully.');
    }

    public function edit($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        return view('customers/edit', [
            'pageTitle' => 'Edit Customer',
            'customer' => $customer
        ]);
    }

    public function update($id)
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[150]',
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
            'email' => 'permit_empty|valid_email',
        ];
        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $this->customerModel->update($id, $data);
        return redirect()->to('/customers')->with('success', 'Customer updated successfully.');
    }

    public function view($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Customer not found.');
        }

        // Fetch Sales History
        $sales = $this->saleModel->where('customer_id', $id)->orderBy('sale_date', 'DESC')->findAll();
        
        // Fetch Sales Returns
        $returns = $this->saleReturnModel->where('customer_id', $id)->orderBy('return_date', 'DESC')->findAll();

        // Calculate Outstanding Receivables
        $totalSalesAmount = 0;
        $totalPaidAmount = 0;
        foreach ($sales as $s) {
            $totalSalesAmount += $s->total_amount;
            $totalPaidAmount += $s->paid_amount;
        }

        // A simple ledger: Merge sales and returns, sort by date
        $ledger = [];
        foreach ($sales as $s) {
            $ledger[] = [
                'date' => $s->sale_date,
                'type' => 'Invoice',
                'ref_no' => $s->invoice_no,
                'debit' => $s->total_amount, // Customer owes us
                'credit' => $s->paid_amount, // Customer paid us
                'balance_impact' => $s->total_amount - $s->paid_amount
            ];
        }
        foreach ($returns as $r) {
            $ledger[] = [
                'date' => $r->return_date,
                'type' => 'Return',
                'ref_no' => $r->return_no,
                'debit' => 0,
                'credit' => $r->total_amount, // We owe customer
                'balance_impact' => -$r->total_amount
            ];
        }

        // Sort ledger by date ascending
        usort($ledger, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Compute running balance
        $runningBalance = 0;
        foreach ($ledger as &$entry) {
            $runningBalance += $entry['balance_impact'];
            $entry['balance'] = $runningBalance;
        }

        $outstanding = $runningBalance; // final balance owed by customer

        // Fetch Documents
        $documentModel = new \App\Models\DocumentModel();
        $documents = $documentModel->where('entity_type', 'customer')->where('entity_id', $id)->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'pageTitle' => 'Customer Profile',
            'customer' => $customer,
            'sales' => $sales,
            'ledger' => $ledger,
            'outstanding' => $outstanding,
            'documents' => $documents
        ];

        return view('customers/view', $data);
    }
}
