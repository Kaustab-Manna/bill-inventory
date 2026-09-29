<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;

class VendorController extends BaseController
{
    protected $vendorModel;

    public function __construct()
    {
        $this->vendorModel = new VendorModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Vendors',
            'vendors' => $this->vendorModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('vendors/index', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Add Vendor'
        ];
        return view('vendors/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]'
        ];
        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->vendorModel->save([
            'name' => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'tax_number' => $this->request->getPost('tax_number'),
            'is_active' => $this->request->getPost('is_active') ?? 1
        ]);

        return redirect()->to('/vendors')->with('success', 'Vendor created successfully');
    }

    public function edit($id)
    {
        $vendor = $this->vendorModel->find($id);
        if (!$vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found');
        }

        $data = [
            'pageTitle' => 'Edit Vendor',
            'vendor' => $vendor
        ];
        return view('vendors/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|regex_match[/^[0-9]{10}$/]'
        ];
        $messages = [
            'phone' => [
                'regex_match' => 'Please enter a valid 10-digit mobile number.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->vendorModel->update($id, [
            'name' => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'tax_number' => $this->request->getPost('tax_number'),
            'is_active' => $this->request->getPost('is_active') ?? 0
        ]);

        return redirect()->to('/vendors')->with('success', 'Vendor updated successfully');
    }

    public function delete($id)
    {
        $this->vendorModel->delete($id);
        return redirect()->to('/vendors')->with('success', 'Vendor deleted successfully');
    }

    public function view($id)
    {
        $vendor = $this->vendorModel->find($id);
        if (!$vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found');
        }

        $purchaseModel = new \App\Models\PurchaseModel();
        $purchaseReturnModel = new \App\Models\PurchaseReturnModel();

        // Fetch Purchases
        $purchases = $purchaseModel->where('vendor_id', $id)->orderBy('purchase_date', 'DESC')->findAll();
        
        // Fetch Purchase Returns
        $returns = $purchaseReturnModel->where('vendor_id', $id)->orderBy('return_date', 'DESC')->findAll();

        $ledger = [];
        foreach ($purchases as $p) {
            $ledger[] = [
                'date' => $p->purchase_date,
                'type' => 'Purchase',
                'ref_no' => $p->invoice_no ?? $p->reference_no ?? ('PUR-' . $p->id),
                'debit' => (float)$p->paid_amount, // We paid supplier
                'credit' => (float)$p->total_amount, // We owe supplier
                'balance_impact' => (float)$p->total_amount - (float)$p->paid_amount // positive means we owe them
            ];
        }
        foreach ($returns as $r) {
            $ledger[] = [
                'date' => $r->return_date,
                'type' => 'Return',
                'ref_no' => $r->return_no ?? ('RET-' . $r->id),
                'debit' => (float)$r->total_amount, // Supplier owes us / returned goods
                'credit' => 0, 
                'balance_impact' => -(float)$r->total_amount // reduces what we owe
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

        $outstanding = $runningBalance; // Final balance we owe the supplier

        // Fetch Documents
        $documentModel = new \App\Models\DocumentModel();
        $documents = $documentModel->where('entity_type', 'vendor')->where('entity_id', $id)->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'pageTitle' => 'Supplier Ledger',
            'vendor' => $vendor,
            'purchases' => $purchases,
            'ledger' => $ledger,
            'outstanding' => $outstanding,
            'documents' => $documents
        ];

        return view('vendors/view', $data);
    }
}
