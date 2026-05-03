<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\City;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller {
    public function index(Request $request) {
        $specialties = Specialty::all();
        $cities = City::all();
        
        $query = Doctor::with(['user.city', 'specialty', 'schedules' => function($q) {
            $q->where('is_booked', false)->orderBy('date', 'asc');
        }]);

        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->filled('city_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        $doctors = $query->get();
        return view('patient.index', compact('doctors', 'specialties', 'cities'));
    }

    public function book(Request $request) {
        try {
            DB::transaction(function () use ($request) {
                $schedule = DoctorSchedule::lockForUpdate()->findOrFail($request->schedule_id);
                if ($schedule->is_booked) throw new \Exception("This schedule is already booked!");

                $schedule->is_booked = true; 
                $schedule->save();

                Appointment::create([
                    'patient_id' => Auth::id(),
                    'doctor_id' => $request->doctor_id,
                    'schedule_id' => $schedule->id,
                    'status' => 'Confirmed'
                ]);
            });
            return back()->with('success', 'Appointment booked successfully! The doctor has been notified.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function appointments() {
        $appointments = Appointment::where('patient_id', Auth::id())
                                   ->with('doctor.user', 'schedule')
                                   ->orderBy('created_at', 'desc')->get();
        return view('patient.appointments', compact('appointments'));
    }

    public function cancel($id) {
        $appointment = Appointment::where('id', $id)->where('patient_id', Auth::id())->firstOrFail();
        if($appointment->status == 'Cancelled') return back()->with('error', 'Appointment is already cancelled.');
        
        $appointment->status = 'Cancelled';
        $appointment->save();
        
        // Free up the schedule
        $schedule = DoctorSchedule::find($appointment->schedule_id);
        if($schedule) {
            $schedule->is_booked = false;
            $schedule->save();
        }
        return back()->with('success', 'Appointment cancelled successfully.');
    }
}