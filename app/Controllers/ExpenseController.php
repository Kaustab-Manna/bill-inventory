<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;

class ExpenseController extends BaseController
{
    protected $expenseModel;

    public function __construct()
    {
        $this->expenseModel = new ExpenseModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Expenses',
            'expenses' => $this->expenseModel->orderBy('expense_date', 'DESC')->orderBy('id', 'DESC')->findAll()
        ];
        return view('finance/expenses/index', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Add Expense'
        ];
        return view('finance/expenses/create', $data);
    }

    public function store()
    {
        $post = $this->request->getPost();

        $expenseData = [
            'expense_date' => $post['expense_date'] ?: date('Y-m-d'),
            'category' => $post['category'],
            'amount' => $post['amount'],
            'payment_method' => $post['payment_method'],
            'reference_no' => $post['reference_no'],
            'notes' => $post['notes'],
            'created_by' => session()->get('user_id')
        ];

        if ($this->expenseModel->insert($expenseData)) {
            return redirect()->to('/expenses')->with('success', 'Expense added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add expense.');
    }

    public function delete($id)
    {
        if ($this->expenseModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Expense deleted successfully.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete expense.']);
    }
}
