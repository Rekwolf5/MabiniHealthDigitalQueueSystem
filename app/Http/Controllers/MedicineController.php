<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use Carbon\Carbon;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        // Get search query
        $search = $request->query('search');
        
        // Query medicines with optional search filter
        $medicinesQuery = Medicine::query();
        
        if ($search) {
            $medicinesQuery->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('dosage', 'LIKE', "%{$search}%")
                      ->orWhere('type', 'LIKE', "%{$search}%");
            });
        }
        
        $medicinesData = $medicinesQuery->get();
        
        $medicines = $medicinesData->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'stock' => $medicine->stock ?? 0,
                'expiry_date' => $medicine->expiry_date 
                    ? Carbon::parse($medicine->expiry_date)->format('M d, Y') 
                    : 'No expiry date',
                'status' => $medicine->status,
                'status_color' => $medicine->status_color,
            ];
        });

        return view('medicines.index', compact('medicines'));
    }
    
    // Show create medicine form
    public function create()
    {
        return view('medicines.create');
    }
    
    // Store new medicine
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dosage' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:5|max:100',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        Medicine::create($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully!');
    }
    
    // Show medicine details
    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('medicines.show', compact('medicine'));
    }
    
    // Show edit medicine form
    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('medicines.edit', compact('medicine'));
    }
    
    // Update medicine
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dosage' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:5|max:100',
            'expiry_date' => 'nullable|date',
        ]);

        $medicine->update($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully!');
    }
    
    // Delete medicine
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully!');
    }
}
