<?php

namespace App\Http\Controllers\FrontOffice;

use App\Actions\Registration\GenerateMedicalRecordNumberAction;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display a listing of patients with search.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $patients = Patient::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('medical_record_number', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('front-office.patients.index', compact('patients', 'search'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(GenerateMedicalRecordNumberAction $generateRmAction): View
    {
        $generatedRmNumber = $generateRmAction->execute();

        return view('front-office.patients.create', compact('generatedRmNumber'));
    }

    /**
     * Store a newly created patient in database.
     */
    public function store(Request $request, GenerateMedicalRecordNumberAction $generateRmAction): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'digits:16', 'unique:patients,nik'],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'blood_type' => ['nullable', 'string', 'max:5'],
        ]);

        $validated['medical_record_number'] = $generateRmAction->execute();

        $patient = Patient::create($validated);

        return redirect()->route('patients.index')->with('status', "Pasien {$patient->name} berhasil didaftarkan dengan No. RM {$patient->medical_record_number}.");
    }
}
