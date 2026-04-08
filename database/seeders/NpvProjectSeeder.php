<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\NpvCalculatorService;
use App\Repositories\NpvRepository;

class NpvProjectSeeder extends Seeder
{
    public function __construct(
        private NpvCalculatorService $service,
        private NpvRepository $repository
    ) {}

    public function run(): void
    {
        $projects = [
            [
                'name'       => 'Construction of Unit B Factory',
                'investment' => 500_000_000,
                'rate'       => 10,
                'flows'      => [120_000_000, 150_000_000, 180_000_000, 200_000_000, 200_000_000],
            ],
            [
                'name'       => 'Logistics Warehouse Expansion',
                'investment' => 300_000_000,
                'rate'       => 12,
                'flows'      => [80_000_000, 90_000_000, 85_000_000, 70_000_000],
            ],
            [
                'name'       => 'Office Solar Panel Project',
                'investment' => 150_000_000,
                'rate'       => 8,
                'flows'      => [30_000_000, 35_000_000, 40_000_000, 40_000_000, 40_000_000, 40_000_000],
            ],
            [
                'name'       => 'Digitalization of Inventory System',
                'investment' => 200_000_000,
                'rate'       => 15,
                'flows'      => [50_000_000, 60_000_000, 55_000_000],
            ],
        ];

        foreach ($projects as $data) {
            $result = $this->service->calculate($data['investment'], $data['rate'], $data['flows']);
            $this->repository->saveProject($data['name'], $result);
        }

        $this->command->info(' 4 projects seeded successfully.');
    }
}