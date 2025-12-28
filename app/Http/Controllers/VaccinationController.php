<?php

namespace App\Http\Controllers;

use App\Models\Vaccination;
use App\Models\Animal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VaccinationController extends Controller
{
    public function index()
    {
        $animals = Animal::all();

        // Calculate stats
        $thisMonth = Vaccination::whereMonth('date_administered', now()->month)
            ->whereYear('date_administered', now()->year)
            ->count();

        $dueThisWeek = Vaccination::whereBetween('next_due_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        $overdue = Vaccination::where('next_due_date', '<', now()->startOfDay())
            ->where('status', '!=', 'completed')
            ->count();

        return view('vaccination.index', compact('animals', 'thisMonth', 'dueThisWeek', 'overdue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'vaccine_name' => 'required|string|max:255',
            'date_administered' => 'required|date',
            'next_due_date' => 'nullable|date|after:date_administered',
            'batch_number' => 'nullable|string|max:255',
            'veterinarian' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $vaccination = Vaccination::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vaccination record added successfully!',
            'vaccination' => $vaccination->load('animal')
        ]);
    }

    public function getVaccinations(Request $request)
    {
        $vaccinations = $this->filteredQuery($request)->paginate(20);

        $data = collect($vaccinations->items())->map(function ($vaccination) {
            /** @var \App\Models\Vaccination $vaccination */
            return [
                'id' => $vaccination->id,
                'date_administered' => $vaccination->date_administered->format('Y-m-d'),
                'animal_id' => $vaccination->animal ? $vaccination->animal->tag_number : 'N/A',
                'animal_name' => $vaccination->animal ? $vaccination->animal->name : 'N/A',
                'vaccine_name' => $vaccination->vaccine_name,
                'batch_number' => $vaccination->batch_number ?? 'N/A',
                'veterinarian' => $vaccination->veterinarian,
                'next_due_date' => $vaccination->next_due_date ? $vaccination->next_due_date->format('Y-m-d') : 'N/A',
                'status' => $vaccination->status,
                'notes' => $vaccination->notes
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'total' => $vaccinations->total(),
                'per_page' => $vaccinations->perPage(),
                'current_page' => $vaccinations->currentPage(),
                'last_page' => $vaccinations->lastPage(),
                'from' => $vaccinations->firstItem(),
                'to' => $vaccinations->lastItem()
            ]
        ]);
    }

    public function getVaccination($id)
    {
        /** @var \App\Models\Vaccination $vaccination */
        $vaccination = Vaccination::with('animal')->findOrFail($id);

        return response()->json([
            'id' => $vaccination->id,
            'animal_id' => $vaccination->animal_id,
            'animal_tag' => $vaccination->animal ? $vaccination->animal->tag_number : 'N/A',
            'animal_name' => $vaccination->animal ? $vaccination->animal->name : 'N/A',
            'vaccine_name' => $vaccination->vaccine_name,
            'date_administered' => $vaccination->date_administered->format('Y-m-d'),
            'next_due_date' => $vaccination->next_due_date ? $vaccination->next_due_date->format('Y-m-d') : '',
            'batch_number' => $vaccination->batch_number ?? '',
            'veterinarian' => $vaccination->veterinarian,
            'notes' => $vaccination->notes ?? '',
            'status' => $vaccination->status
        ]);
    }

    public function update(Request $request, $id)
    {
        $vaccination = Vaccination::findOrFail($id);

        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'vaccine_name' => 'required|string|max:255',
            'date_administered' => 'required|date',
            'next_due_date' => 'nullable|date|after:date_administered',
            'batch_number' => 'nullable|string|max:255',
            'veterinarian' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $vaccination->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vaccination record updated successfully!',
            'vaccination' => $vaccination->load('animal')
        ]);
    }

    public function destroy($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        $vaccination->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vaccination record deleted successfully!'
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = Vaccination::with('animal');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('vaccine')) {
            $query->where('vaccine_name', 'LIKE', '%' . $request->string('vaccine') . '%');
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            $today = now();
            if ($range === 'last-7-days') {
                $query->where('date_administered', '>=', $today->copy()->subDays(7));
            } elseif ($range === 'last-30-days') {
                $query->where('date_administered', '>=', $today->copy()->subDays(30));
            } elseif ($range === 'last-90-days') {
                $query->where('date_administered', '>=', $today->copy()->subDays(90));
            } elseif ($range === 'this-year') {
                $query->whereYear('date_administered', $today->year);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function ($q) use ($term) {
                $q->whereHas('animal', function ($sq) use ($term) {
                    $sq->whereRaw('LOWER(tag_number) LIKE ?', ['%' . $term . '%'])
                      ->orWhereRaw('LOWER(name) LIKE ?', ['%' . $term . '%']);
                })->orWhereRaw('LOWER(vaccine_name) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->orderBy('date_administered', 'desc');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $vaccinations = $this->filteredQuery($request)->get();
        $html = view('vaccination.print', compact('vaccinations'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('vaccination_records_export.pdf');
    }

    public function print(Request $request)
    {
        $vaccinations = $this->filteredQuery($request)->get();
        return view('vaccination.print', compact('vaccinations'));
    }
}

