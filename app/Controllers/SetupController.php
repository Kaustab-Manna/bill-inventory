<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SetupController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $output = [];
        $status = 'success';
        $errorMsg = null;

        try {
            // Test DB Connection
            $db->initialize();
            $output[] = "Database connected successfully to: " . $db->getDatabase();

            // Run migrations
            $migrate = \Config\Services::migrations();
            $migrate->setNamespace(null);
            $migrate->latest();
            $output[] = "All database migrations executed successfully.";

            // Run seeders if users table is empty
            $builder = $db->table('users');
            $userCount = $builder->countAllResults();

            if ($userCount === 0) {
                $seeder = \Config\Database::seeder();
                
                try {
                    $seeder->call('App\Database\Seeds\CoreSeeder');
                    $output[] = "CoreSeeder executed (Admin account & permissions created).";
                } catch (\Throwable $e) {
                    $output[] = "CoreSeeder notice: " . $e->getMessage();
                }

                try {
                    $seeder->call('App\Database\Seeds\Phase2Seeder');
                    $output[] = "Phase2Seeder executed (Categories, Brands, Units, Branches).";
                } catch (\Throwable $e) {
                    $output[] = "Phase2Seeder notice: " . $e->getMessage();
                }

                try {
                    $seeder->call('App\Database\Seeds\Phase3Seeder');
                    $output[] = "Phase3Seeder executed (Sample Warehouses & Products).";
                } catch (\Throwable $e) {
                    $output[] = "Phase3Seeder notice: " . $e->getMessage();
                }
            } else {
                $output[] = "Database already initialized with {$userCount} users. Seeding skipped.";
            }

        } catch (\Throwable $e) {
            $status = 'error';
            $errorMsg = $e->getMessage();
        }

        return view('setup_success', [
            'status'   => $status,
            'logs'     => $output,
            'error'    => $errorMsg,
            'email'    => 'admin@billinventory.com',
            'password' => 'admin123',
        ]);
    }
}
