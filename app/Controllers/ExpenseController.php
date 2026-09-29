<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;

class ExpenseController extends BaseController
{
    protected $expenseModel;
    protected $db;

    public function __construct()
    {
        $this->expenseModel = new ExpenseModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $this->ensureExpenseSchema();

        try {
            $expenses = $this->expenseModel->orderBy('expense_date', 'DESC')->orderBy('id', 'DESC')->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'Expense index error: ' . $e->getMessage());
            $expenses = [];
        }

        $data = [
            'pageTitle' => 'Expenses',
            'expenses'  => $expenses
        ];
        return view('finance/expenses/index', $data);
    }

    public function create()
    {
        $this->ensureExpenseSchema();

        $data = [
            'pageTitle' => 'Add Expense'
        ];
        return view('finance/expenses/create', $data);
    }

    public function store()
    {
        $this->ensureExpenseSchema();

        $post = $this->request->getPost();

        $expenseData = [
            'expense_date'   => $post['expense_date'] ?: date('Y-m-d'),
            'category'       => $post['category'],
            'amount'         => (float)$post['amount'],
            'payment_method' => $post['payment_method'] ?? 'Cash',
            'reference_no'   => $post['reference_no'] ?? null,
            'notes'          => $post['notes'] ?? null,
            'created_by'     => session()->get('user_id')
        ];

        // Ensure expense_no is provided if the column exists in the table
        if ($this->db->fieldExists('expense_no', 'expenses')) {
            $expenseData['expense_no'] = 'EXP-' . strtoupper(uniqid());
        }

        if ($this->expenseModel->insert($expenseData)) {
            return redirect()->to('/expenses')->with('success', 'Expense added successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to add expense.');
    }

    public function delete($id)
    {
        if ($this->expenseModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Expense deleted successfully.']);
            }
            return redirect()->to('/expenses')->with('success', 'Expense deleted successfully.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete expense.']);
        }
        return redirect()->to('/expenses')->with('error', 'Failed to delete expense.');
    }

    protected function ensureExpenseSchema()
    {
        try {
            $db = $this->db;
            if (!$db->tableExists('expenses')) {
                $this->ensureSchema($db);
                return;
            }

            if (!$db->fieldExists('payment_method', 'expenses')) {
                if ($db->fieldExists('paid_by', 'expenses')) {
                    $db->query("ALTER TABLE `expenses` CHANGE `paid_by` `payment_method` VARCHAR(50) DEFAULT 'Cash'");
                } else {
                    $db->query("ALTER TABLE `expenses` ADD `payment_method` VARCHAR(50) DEFAULT 'Cash' AFTER `amount`");
                }
            }
            if ($db->fieldExists('expense_no', 'expenses')) {
                try {
                    $db->query("ALTER TABLE `expenses` MODIFY `expense_no` VARCHAR(50) NULL");
                } catch (\Throwable $e) {}
            }
            if (!$db->fieldExists('category', 'expenses')) {
                $db->query("ALTER TABLE `expenses` ADD `category` VARCHAR(100) NOT NULL AFTER `expense_date`");
            }
            if (!$db->fieldExists('amount', 'expenses')) {
                $db->query("ALTER TABLE `expenses` ADD `amount` DECIMAL(15,2) DEFAULT 0 AFTER `category`");
            }
            if (!$db->fieldExists('expense_date', 'expenses')) {
                $db->query("ALTER TABLE `expenses` ADD `expense_date` DATE NOT NULL AFTER `id`");
            }
        } catch (\Throwable $e) {
            log_message('error', 'ensureExpenseSchema error: ' . $e->getMessage());
        }
    }
}
