<?php

namespace App\Repositories;

use App\Models\NpvProject;
use App\Models\NpvCashFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

/**
 *  NPV REPOSITORY
 *  Layer  : Data Access / Repository Layer
 */
class NpvRepository
{
    /**

     * @param  string  $projectName
     * @param  array   $result  
     * @return NpvProject  
     */
    public function saveProject(string $projectName, array $result): NpvProject
    {
        return DB::transaction(function () use ($projectName, $result) {


            $project = NpvProject::create([
                'project_name'        => $projectName,
                'initial_investment'  => $result['initial_investment'],
                'discount_rate'       => $result['discount_rate'],
                'total_present_value' => $result['total_present_value'],
                'npv'                 => $result['npv'],
                'decision'            => $result['decision'],
                'decision_class'      => $result['decision_class'],
                'is_feasible'         => $result['is_feasible'],
            ]);


            $cashFlowRows = array_map(fn($row) => [
                'npv_project_id' => $project->id,
                'year'           => $row['year'],
                'cash_flow'      => $row['cash_flow'],
                'discount_factor'=> $row['discount_factor'],
                'present_value'  => $row['present_value'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ], $result['yearly_details']);

            NpvCashFlow::insert($cashFlowRows); 

            return $project;
        });
    }

    /**
     *
     * @param  int  $perPage 
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllProjectsPaginated(int $perPage = 10)
    {
        return NpvProject::withCount('cashFlows')   
                         ->orderByDesc('created_at')
                         ->paginate($perPage);
    }

    /**
     * @param  int  $id
     * @return NpvProject
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithCashFlows(int $id): NpvProject
    {
        return NpvProject::with('cashFlows')  // Eager load 
                         ->findOrFail($id);
    }

    /**
     *
     * @param  int  $id
     * @return void
     */
    public function delete(int $id): void
    {
        NpvProject::findOrFail($id)->delete();
    }

    /**
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total'      => NpvProject::count(),
            'feasible'   => NpvProject::where('is_feasible', true)->count(),
            'infeasible' => NpvProject::where('is_feasible', false)->count(),
            'avg_npv'    => NpvProject::avg('npv') ?? 0,
        ];
    }
}