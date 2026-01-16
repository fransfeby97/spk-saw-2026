<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Employee;
use App\Models\Period;
use App\Models\Rating;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $periods = Period::orderBy('name', 'desc')->get();
        $selectedPeriod = $request->get('period_id')
            ? Period::find($request->get('period_id'))
            : Period::getActive();

        if (!$selectedPeriod && $periods->count() > 0) {
            $selectedPeriod = $periods->first();
        }

        $employees = Employee::all();
        $criteria = Criteria::orderBy('code')->get();

        // Get existing ratings for the selected period
        $ratings = [];
        if ($selectedPeriod) {
            $ratingRecords = Rating::where('period_id', $selectedPeriod->id)->get();
            foreach ($ratingRecords as $rating) {
                $ratings[$rating->employee_id][$rating->criteria_id] = $rating->value;
            }
        }

        return view('assessment.index', compact('periods', 'selectedPeriod', 'employees', 'criteria', 'ratings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:periods,id',
            'ratings' => 'required|array',
            'ratings.*.*' => 'required|numeric|min:0',
        ]);

        $periodId = $request->input('period_id');
        $ratingsData = $request->input('ratings');

        foreach ($ratingsData as $employeeId => $criteriaRatings) {
            foreach ($criteriaRatings as $criteriaId => $value) {
                Rating::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'criteria_id' => $criteriaId,
                        'period_id' => $periodId,
                    ],
                    ['value' => $value]
                );
            }
        }

        return redirect()->route('assessment.result', ['period_id' => $periodId])
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    public function result(Request $request)
    {
        $periods = Period::orderBy('name', 'desc')->get();
        $selectedPeriod = $request->get('period_id')
            ? Period::find($request->get('period_id'))
            : Period::getActive();

        if (!$selectedPeriod && $periods->count() > 0) {
            $selectedPeriod = $periods->first();
        }

        if (!$selectedPeriod) {
            return view('assessment.result', [
                'periods' => $periods,
                'selectedPeriod' => null,
                'results' => [],
                'criteria' => [],
                'rawMatrix' => [],
                'maxMinValues' => [],
                'employees' => [],
            ]);
        }

        $employees = Employee::all();
        $criteria = Criteria::orderBy('code')->get();

        // Get ratings for selected period
        $ratings = Rating::where('period_id', $selectedPeriod->id)->get();

        // Build matrix [employee_id][criteria_id] = value
        $matrix = [];
        foreach ($ratings as $rating) {
            $matrix[$rating->employee_id][$rating->criteria_id] = $rating->value;
        }

        // Calculate SAW with detailed steps
        $sawData = $this->calculateSawDetailed($matrix, $criteria, $employees);

        return view('assessment.result', [
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
            'results' => $sawData['results'],
            'criteria' => $criteria,
            'rawMatrix' => $sawData['rawMatrix'],
            'maxMinValues' => $sawData['maxMinValues'],
            'employees' => $employees,
        ]);
    }

    private function calculateSawDetailed(array $matrix, $criteria, $employees): array
    {
        if (empty($matrix)) {
            return [
                'results' => [],
                'rawMatrix' => [],
                'maxMinValues' => [],
            ];
        }

        // Find max/min for each criteria
        $maxValues = [];
        $minValues = [];

        foreach ($criteria as $c) {
            $values = [];
            foreach ($matrix as $employeeRatings) {
                if (isset($employeeRatings[$c->id])) {
                    $values[] = $employeeRatings[$c->id];
                }
            }
            if (!empty($values)) {
                $maxValues[$c->id] = max($values);
                $minValues[$c->id] = min($values);
            }
        }

        // Normalize and calculate final score
        $results = [];

        foreach ($employees as $employee) {
            if (!isset($matrix[$employee->id])) {
                continue;
            }

            $normalizedScores = [];
            $weightedScores = [];
            $finalScore = 0;

            foreach ($criteria as $c) {
                if (!isset($matrix[$employee->id][$c->id])) {
                    continue;
                }

                $value = $matrix[$employee->id][$c->id];
                $max = $maxValues[$c->id] ?? 1;
                $min = $minValues[$c->id] ?? 1;

                // Normalize based on type
                if ($c->type === 'benefit') {
                    $normalized = $max > 0 ? $value / $max : 0;
                } else {
                    // Cost - lower is better
                    $normalized = $value > 0 ? $min / $value : 0;
                }

                $normalizedScores[$c->id] = round($normalized, 4);
                $weighted = $normalized * $c->weight;
                $weightedScores[$c->id] = round($weighted, 4);
                $finalScore += $weighted;
            }

            $results[] = [
                'employee' => $employee,
                'raw' => $matrix[$employee->id],
                'normalized' => $normalizedScores,
                'weighted' => $weightedScores,
                'score' => round($finalScore, 4),
            ];
        }

        // Sort by score descending
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        // Add rank
        $rank = 1;
        foreach ($results as &$result) {
            $result['rank'] = $rank++;
        }

        return [
            'results' => $results,
            'rawMatrix' => $matrix,
            'maxMinValues' => [
                'max' => $maxValues,
                'min' => $minValues,
            ],
        ];
    }

    public function printPdf(Request $request, Employee $employee)
    {
        $periodId = $request->get('period_id');
        $period = $periodId ? Period::find($periodId) : Period::getActive();

        if (!$period) {
            return back()->with('error', 'Periode tidak ditemukan.');
        }

        $criteria = Criteria::orderBy('code')->get();

        // Get ratings for this employee in this period
        $ratings = Rating::where('employee_id', $employee->id)
            ->where('period_id', $period->id)
            ->get()
            ->keyBy('criteria_id');

        // Calculate normalized and final score
        $allRatings = Rating::where('period_id', $period->id)->get();
        $matrix = [];
        foreach ($allRatings as $r) {
            $matrix[$r->employee_id][$r->criteria_id] = $r->value;
        }

        // Find max/min
        $maxValues = [];
        $minValues = [];
        foreach ($criteria as $c) {
            $values = [];
            foreach ($matrix as $empRatings) {
                if (isset($empRatings[$c->id])) {
                    $values[] = $empRatings[$c->id];
                }
            }
            if (!empty($values)) {
                $maxValues[$c->id] = max($values);
                $minValues[$c->id] = min($values);
            }
        }

        // Calculate for this employee
        $normalizedScores = [];
        $finalScore = 0;
        foreach ($criteria as $c) {
            if (!isset($matrix[$employee->id][$c->id]))
                continue;

            $value = $matrix[$employee->id][$c->id];
            $max = $maxValues[$c->id] ?? 1;
            $min = $minValues[$c->id] ?? 1;

            if ($c->type === 'benefit') {
                $normalized = $max > 0 ? $value / $max : 0;
            } else {
                $normalized = $value > 0 ? $min / $value : 0;
            }

            $normalizedScores[$c->id] = round($normalized, 4);
            $finalScore += $normalized * $c->weight;
        }

        $data = [
            'employee' => $employee,
            'period' => $period,
            'criteria' => $criteria,
            'ratings' => $ratings,
            'normalizedScores' => $normalizedScores,
            'finalScore' => round($finalScore, 4),
        ];

        $pdf = Pdf::loadView('assessment.pdf', $data);
        return $pdf->stream('penilaian-' . $employee->name . '-' . $period->name . '.pdf');
    }
}
