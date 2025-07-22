<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Models\Employee;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sites = Site::all();
        return view('pages.sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.sites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'no_of_guards' => 'required|integer|min:1',
            'no_day_shifts' => 'nullable|integer|min:0',
            'no_night_shifts' => 'nullable|integer|min:0',
            'site_shift_rate' => 'nullable|numeric|min:0',
            'guard_shift_rate' => 'nullable|numeric|min:0',
            'has_special_ot_hours' => 'boolean',
            'special_ot_rate' => 'nullable|numeric|min:0|required_if:has_special_ot_hours,true',
            'ranks' => 'sometimes|array',
            'ranks.*.rank' => 'required|string',
            'ranks.*.site_shift_rate' => 'required|numeric|min:0',
            'ranks.*.guard_shift_rate' => 'required|numeric|min:0',
        ]);

        try {
        $site =Site::create($validated);

        if (isset($validated['ranks'])) {
                foreach ($validated['ranks'] as $rankData) {
                    $site->rankRates()->create([
                        'rank' => $rankData['rank'],
                        'site_shift_rate' => $rankData['site_shift_rate'],
                        'guard_shift_rate' => $rankData['guard_shift_rate']
                    ]);
                }}
        return redirect()->route('sites.index')->with('success', 'Site created successfully.');
        } catch (\Exception $e) {
            \Log::error('Site creation failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to create site. Please try again.');
    }
    }
    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        return view('pages.sites.edit', compact('site'));
    }

    //To view site details
    public function view(Site $site)
    {
        return view('pages.sites.view', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'no_of_guards' => 'required|integer|min:1',
            'no_day_shifts' => 'nullable|integer|min:0',
            'no_night_shifts' => 'nullable|integer|min:0',
            'site_shift_rate' => 'nullable|numeric|min:0',
            'guard_shift_rate' => 'nullable|numeric|min:0',
            'has_special_ot_hours' => 'boolean',
            'special_ot_rate' => 'nullable|numeric|min:0|required_if:has_special_ot_hours,true',
            'ranks' => 'sometimes|array',
            'ranks.*.rank' => 'required|string',
            'ranks.*.site_shift_rate' => 'required|numeric|min:0',
            'ranks.*.guard_shift_rate' => 'required|numeric|min:0',
        ]);

        if (!isset($validated['has_special_ot_hours'])) {
            $validated['has_special_ot_hours'] = false;
            $validated['special_ot_rate'] = null;
        }
        $site->update($validated);
           // Update rank rates
        if (isset($validated['ranks'])) {
            //  delete existing rates
            $site->rankRates()->delete();

            // Add new rates
            foreach ($validated['ranks'] as $rankData) {
                $site->rankRates()->create([
                    'rank' => $rankData['rank'],
                    'site_shift_rate' => $rankData['site_shift_rate'],
                    'guard_shift_rate' => $rankData['guard_shift_rate']
                ]);
            }
        }

        return redirect()->route('sites.index')->with('success', 'Site updated successfully.');
    }

    public function assignGuards(Site $site)
    {
        $employees = Employee::orderBy('name')->get();
        $assigned = $site->employees->pluck('id')->toArray();

        return view('pages.sites.assign', compact('site', 'employees', 'assigned'));
    }

    public function storeAssignedGuards(Request $request, Site $site)
    {
        $validated = $request->validate([
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $site->employees()->sync($validated['employee_ids'] ?? []);

        return redirect()->route('sites.index')->with('success', 'Guards assigned successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted successfully.');
    }
}
