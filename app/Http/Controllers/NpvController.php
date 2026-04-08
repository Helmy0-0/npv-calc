<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NpvCalculatorService;
use App\Repositories\NpvRepository;

/**
 *  NPV CONTROLLER  (v2)
 *  Layer  : HTTP / Controller Layer
 */
class NpvController extends Controller
{
    public function __construct(
        private readonly NpvCalculatorService $npvService,
        private readonly NpvRepository        $npvRepository
    ) {}

    /** GET /npv — Form input */
    public function index()
    {
        return view('npv.index');
    }

    /** POST /npv/calculate — Validasi → Hitung → Simpan → Redirect */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'project_name'       => 'required|string|max:100',
            'initial_investment' => 'required|numeric|min:0',
            'discount_rate'      => 'required|numeric|min:0|max:100',
            'cash_flows'         => 'required|array|min:1',
            'cash_flows.*'       => 'required|numeric',
        ], [
            'project_name.required'       => 'The project name is required.',
            'initial_investment.required' => 'Initial capital is required.',
            'initial_investment.min'      => 'Initial capital cannot be negative.',
            'discount_rate.required'      => 'Discount rate is required.',
            'discount_rate.max'           => 'The discount rate may not exceed 100%.',
            'cash_flows.required'         => 'Minimum one year of cash flow must be filled in.',
            'cash_flows.*.required'       => 'All cash flow columns must be filled in.',
            'cash_flows.*.numeric'        => 'Cash flow must be a number.',
        ]);

        $result  = $this->npvService->calculate(
            (float) $validated['initial_investment'],
            (float) $validated['discount_rate'],
            array_map('floatval', $validated['cash_flows'])
        );

        // Save to DB
        $project = $this->npvRepository->saveProject($validated['project_name'], $result);

        return redirect()->route('npv.show', $project->id)
                         ->with('success', 'Calculation saved successfully!');
    }

    // GET /npv/{id} 
    public function show(int $id)
    {
        $project = $this->npvRepository->findWithCashFlows($id);
        return view('npv.result', compact('project'));
    }

    // GET /npv/history
    public function history()
    {
        $projects = $this->npvRepository->getAllProjectsPaginated(10);
        $stats    = $this->npvRepository->getStats();
        return view('npv.history', compact('projects', 'stats'));
    }

    // DELETE /npv/{id}
    public function destroy(int $id)
    {
        $this->npvRepository->delete($id);
        return redirect()->route('npv.history')
                         ->with('success', 'The project was successfully deleted.');
    }
}