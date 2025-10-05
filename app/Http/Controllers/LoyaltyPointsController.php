<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoyaltyPointsController extends Controller
{
    /**
     * Display a listing of settings (points & value per point)
     */
    public function index(Request $request)
    {
        // Get settings
        $pointsPerAppointment = Setting::where('key', 'pointsPerAppointment')->value('value') ?? 0;
        $valueOfEachPoint = Setting::where('key', 'valueOfEachPoint')->value('value') ?? 0;
        
        return view('loyaltyPoints', compact(
            'pointsPerAppointment',
            'valueOfEachPoint'
        ));
    }

    /**
     * Update points and value of each point
     */
    public function updateLoyaltyPoints(Request $request)
    {
        $request->validate([
            'pointsPerAppointment' => 'required|integer|min:0',
            'valueOfEachPoint' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            Setting::updateOrCreate(
                ['key' => 'pointsPerAppointment'],
                ['value' => $request->pointsPerAppointment]
            );

            Setting::updateOrCreate(
                ['key' => 'valueOfEachPoint'],
                ['value' => $request->valueOfEachPoint]
            );

            DB::commit();

            return redirect()->route('loyaltyPoints.index')
                ->with('success', __('Settings updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to update settings'))
                ->withInput();
        }
    }
}
