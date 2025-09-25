<?php

namespace App\Http\Controllers;

use App\Models\AddedPatients;
use App\Models\AppointmentDocs;
use App\Models\Appointments;
use App\Models\Constants;
use App\Models\Coupons;
use App\Models\DoctorEarningHistory;
use App\Models\DoctorPayoutHistory;
use App\Models\DoctorReviews;
use App\Models\Doctors;
use App\Models\DoctorWalletStatements;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use App\Models\PlatformData;
use App\Models\PlatformEarningHistory;
use App\Models\Prescriptions;
use App\Models\Users;
use Google\Service\CloudSearch\Id;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    //
    function fetchAcceptedPendingAppointmentsOfDoctorByDate(Request $request)
    {
        $rules = [
            'doctor_id' => 'required',
            'date' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return response()->json(['status' => false, 'message' => "Doctor doesn't exists!"]);
        }
        $appointments = Appointments::where('doctor_id', $request->doctor_id)
            ->where('date', $request->date)
            ->whereIn('status', [Constants::orderPlacedPending, Constants::orderAccepted])
            ->with(['user'])
            ->get();

        return GlobalFunction::sendDataResponse(true, 'Bookings fetched successfully', $appointments);
    }

    function viewAppointment($id)
    {
        $item = Appointments::where('id', $id)
            ->with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->first();

        $settings = GlobalSettings::first();

        // Generating Rating Bar
        $starDisabled = '<i class="fas fa-star starDisabled"></i>';
        $starActive = '<i class="fas fa-star starActive"></i>';

        $ratingBar = '';
        if ($item->rating != null) {
            for ($i = 0; $i < 5; $i++) {
                if ($item->rating->rating > $i) {
                    $ratingBar = $ratingBar . $starActive;
                } else {
                    $ratingBar = $ratingBar . $starDisabled;
                }
            }
        }
        // Having json object of appointment summary
        $orderSummary = json_decode($item->order_summary, true);
        $prescription = null;
        if ($item->prescription != null) {
            $prescription = json_decode($item->prescription->medicine, true);
        }
        // return $orderSummary;
        // dd('dd');
        return view('viewAppointment', [
            'appointment' => $item,
            'ratingBar' => $ratingBar,
            'settings' => $settings,
            'orderSummary' => $orderSummary,
            'prescription' => $prescription,
        ]);
    }
    function fetchDeclinedAppointmentsList(Request $request)
    {
        $totalData =  Appointments::where('status', Constants::orderDeclined)->count();
        $rows = Appointments::where('status', Constants::orderDeclined)->orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname',
            2 => 'identity',
            3 => 'username',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Appointments::where('status', Constants::orderDeclined)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Appointments::where('status', Constants::orderDeclined)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Appointments::where('status', Constants::orderDeclined)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {


            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }
            $user = "";
            if ($item->user != null) {
                $user = '<a href="' . route('viewUserProfile', $item->user->id) . '"><span class="badge bg-primary text-white">' . $item->user->fullname . '</span></a>';
            }

            $view = '<a href="' . route('viewAppointment', $item->id) . '" class="mr-2 btn btn-info text-white" rel="' . $item->id . '">
                        <i class="fa fa-eye"></i>
                    </a>';
            $status = GlobalFunction::returnAppointmentStatus($item->status);

            $action = $view;

            $dateTime =  $item->date . '<br>' . GlobalFunction::formateTimeString($item->time);
            $payableAmount = $settings->currency . $item->payable_amount;

            $data[] = array(
                $item->appointment_number,
                $user,
                $doctor,
                $status,
                $dateTime,
                $settings->currency . $item->service_amount,
                $settings->currency . $item->discount_amount,
                $settings->currency . $item->subtotal,
                $settings->currency . $item->total_tax_amount,
                $payableAmount,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
                $item->id
            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }
    function fetchCancelledAppointmentsList(Request $request)
    {
        $totalData =  Appointments::where('status', Constants::orderCancelled)->count();
        $rows = Appointments::where('status', Constants::orderCancelled)->orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname',
            2 => 'identity',
            3 => 'username',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Appointments::where('status', Constants::orderCancelled)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Appointments::where('status', Constants::orderCancelled)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Appointments::where('status', Constants::orderCancelled)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {


            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }
            $user = "";
            if ($item->user != null) {
                $user = '<a href="' . route('viewUserProfile', $item->user->id) . '"><span class="badge bg-primary text-white">' . $item->user->fullname . '</span></a>';
            }

            $view = '<a href="' . route('viewAppointment', $item->id) . '" class="mr-2 btn btn-info text-white" rel="' . $item->id . '">
                        <i class="fa fa-eye"></i>
                    </a>';
            $status = GlobalFunction::returnAppointmentStatus($item->status);

            $action = $view;

            $dateTime =  $item->date . '<br>' . GlobalFunction::formateTimeString($item->time);
            $payableAmount = $settings->currency . $item->payable_amount;

            $data[] = array(
                $item->appointment_number,
                $user,
                $doctor,
                $status,
                $dateTime,
                $settings->currency . $item->service_amount,
                $settings->currency . $item->discount_amount,
                $settings->currency . $item->subtotal,
                $settings->currency . $item->total_tax_amount,
                $payableAmount,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
                $item->id
            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }
    function fetchCompletedAppointmentsList(Request $request)
    {
        $totalData =  Appointments::where('status', Constants::orderCompleted)->count();
        $rows = Appointments::where('status', Constants::orderCompleted)->orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname',
            2 => 'identity',
            3 => 'username',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Appointments::where('status', Constants::orderCompleted)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Appointments::where('status', Constants::orderCompleted)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Appointments::where('status', Constants::orderCompleted)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {


            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }
            $user = "";
            if ($item->user != null) {
                $user = '<a href="' . route('viewUserProfile', $item->user->id) . '"><span class="badge bg-primary text-white">' . $item->user->fullname . '</span></a>';
            }

            $view = '<a href="' . route('viewAppointment', $item->id) . '" class="mr-2 btn btn-info text-white" rel="' . $item->id . '">
                        <i class="fa fa-eye"></i>
                    </a>';
            $status = GlobalFunction::returnAppointmentStatus($item->status);

            $action = $view;

            $dateTime =  $item->date . '<br>' . GlobalFunction::formateTimeString($item->time);
            $payableAmount = $settings->currency . $item->payable_amount;

            $data[] = array(
                $item->appointment_number,
                $user,
                $doctor,
                $status,
                $dateTime,
                $settings->currency . $item->service_amount,
                $settings->currency . $item->discount_amount,
                $settings->currency . $item->subtotal,
                $settings->currency . $item->total_tax_amount,
                $payableAmount,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
                $item->id
            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }
    function fetchAcceptedAppointmentsList(Request $request)
    {
        $totalData =  Appointments::where('status', Constants::orderAccepted)->count();
        $rows = Appointments::where('status', Constants::orderAccepted)->orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname',
            2 => 'identity',
            3 => 'username',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Appointments::where('status', Constants::orderAccepted)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Appointments::where('status', Constants::orderAccepted)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Appointments::where('status', Constants::orderAccepted)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {


            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }
            $user = "";
            if ($item->user != null) {
                $user = '<a href="' . route('viewUserProfile', $item->user->id) . '"><span class="badge bg-primary text-white">' . $item->user->fullname . '</span></a>';
            }

            $view = '<a href="' . route('viewAppointment', $item->id) . '" class="mr-2 btn btn-info text-white" rel="' . $item->id . '">
                        <i class="fa fa-eye"></i>
                    </a>';
            $status = GlobalFunction::returnAppointmentStatus($item->status);

            $action = $view;

            $dateTime =  $item->date . '<br>' . GlobalFunction::formateTimeString($item->time);
            $payableAmount = $settings->currency . $item->payable_amount;

            $data[] = array(
                $item->appointment_number,
                $user,
                $doctor,
                $status,
                $dateTime,
                $settings->currency . $item->service_amount,
                $settings->currency . $item->discount_amount,
                $settings->currency . $item->subtotal,
                $settings->currency . $item->total_tax_amount,
                $payableAmount,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
                $item->id
            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }
    function fetchPendingAppointmentsList(Request $request)
    {
        $totalData =  Appointments::where('status', Constants::orderPlacedPending)->count();
        $rows = Appointments::where('status', Constants::orderPlacedPending)->orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname',
            2 => 'identity',
            3 => 'username',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Appointments::where('status', Constants::orderPlacedPending)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Appointments::where('status', Constants::orderPlacedPending)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Appointments::where('status', Constants::orderPlacedPending)->where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {


            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }
            $user = "";
            if ($item->user != null) {
                $user = '<a href="' . route('viewUserProfile', $item->user->id) . '"><span class="badge bg-primary text-white">' . $item->user->fullname . '</span></a>';
            }

            $view = '<a href="' . route('viewAppointment', $item->id) . '" class="mr-2 btn btn-info text-white" rel="' . $item->id . '">
                        <i class="fa fa-eye"></i>
                    </a>';

            $status = GlobalFunction::returnAppointmentStatus($item->status);

            $action = $view;

            $dateTime =  $item->date . '<br>' . GlobalFunction::formateTimeString($item->time);
            $payableAmount = $settings->currency . $item->payable_amount;

            $data[] = array(
                $item->appointment_number,
                $user,
                $doctor,
                $status,
                $dateTime,
                $settings->currency . $item->service_amount,
                $settings->currency . $item->discount_amount,
                $settings->currency . $item->subtotal,
                $settings->currency . $item->total_tax_amount,
                $payableAmount,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
                $item->id
            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }
    function fetchAllAppointmentsList(Request $request)
    {
        $totalData =  Appointments::count();
        $rows = Appointments::orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname',
            2 => 'identity',
            3 => 'username',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Appointments::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Appointments::where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Appointments::where(function ($query) use ($search) {
                $query->Where('appointment_number', 'LIKE', "%{$search}%")
                    ->orWhere('payable_amount', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {

            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }
            $user = "";
            if ($item->user != null) {
                $user = '<a href="' . route('viewUserProfile', $item->user->id) . '"><span class="badge bg-primary text-white">' . $item->user->fullname . '</span></a>';
            }

            $view = '<a href="' . route('viewAppointment', $item->id) . '" class="mr-2 btn btn-info text-white" rel="' . $item->id . '">
                        <i class="fa fa-eye"></i>
                    </a>';
            $status = GlobalFunction::returnAppointmentStatus($item->status);

            $action = $view;

            $dateTime =  $item->date . '<br>' . GlobalFunction::formateTimeString($item->time);
            $payableAmount = $settings->currency . $item->payable_amount;

            $data[] = array(
                $item->appointment_number,
                $user,
                $doctor,
                $status,
                $dateTime,
                $settings->currency . $item->service_amount,
                $settings->currency . $item->discount_amount,
                $settings->currency . $item->subtotal,
                $settings->currency . $item->total_tax_amount,
                $payableAmount,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
                $item->id


            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
            'id'              => $item->id,
        );
        echo json_encode($json_data);
        exit();
    }


    function appointments(Request $request)
    {
        $users = Users::all();
        $doctors = Doctors::all();

        return view('appointments', [
            'users' => $users,
            'doctors' => $doctors,
        ]);
    }


    public function AddAppointmentsForAdmin(Request $request)
    {
        $rules = [
            'user_id'          => 'required|exists:users,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'date'             => 'required|date',
            'time'             => 'required',
            'type'             => 'required|in:0,1', // 0 = online, 1 = at clinic
            'session_type'     => 'nullable|in:voice,video,chat',
            'status'           => 'required|in:0,1,2,3,4,5',
            'is_urgent'        => 'required|in:0,1',
            'problem'          => 'required|string',
            // 'order_summary'    => 'required|string',
            'service_amount'   => 'required|numeric|min:0',
            'discount_amount'  => 'nullable|numeric|min:0',
            'total_tax_amount' => 'nullable|numeric|min:0',
            'subtotal'         => 'required|numeric|min:0',
            'payable_amount'   => 'required|numeric|min:0',
            'is_coupon_applied' => 'nullable|in:0,1',
            'coupon_title'     => 'nullable|string',
            'documents.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['status' => false, 'message' => $messages[0]]);
        }

        // create new appointment
        $appointment = new Appointments();
        $appointment->appointment_number = GlobalFunction::generateAppointmentNumber();
        $appointment->user_id          = $request->user_id;
        $appointment->doctor_id        = $request->doctor_id;
        $appointment->date             = $request->date;
        $appointment->time             = str_replace(':', '', $request->time);
        $appointment->type             = $request->type;
        $appointment->session_type     = $request->session_type ?? null;
        $appointment->status           = $request->status;
        $appointment->is_urgent        = $request->is_urgent;
        $appointment->problem          = $request->problem;
        // $appointment->order_summary = json_encode($request->order_summary); // تحول للمسلسل JSON
        $appointment->service_amount   = $request->service_amount;
        $appointment->discount_amount  = $request->discount_amount ?? 0;
        $appointment->total_tax_amount = $request->total_tax_amount ?? 0;
        $appointment->subtotal         = $request->subtotal;
        $appointment->payable_amount   = $request->payable_amount;
        $appointment->is_coupon_applied = $request->is_coupon_applied ?? 0;
        $appointment->coupon_title     = $request->coupon_title ?? null;
        $orderSummary = [
            'service_amount' => $request->service_amount,
            'discount_amount' => $request->discount_amount ?? 0,
            'subtotal' => $request->subtotal,
            'total_tax_amount' => $request->total_tax_amount ?? 0,
            'payable_amount' => $request->payable_amount,
            'coupon_apply' => $request->is_coupon_applied ?? 0,
            'coupon' => $request->is_coupon_applied == 1 ? [
                'id' => $request->coupon_id,
                'coupon' => $request->coupon_title,
                'percentage' => $request->coupon_percentage ?? 0,
                'max_discount_amount' => $request->coupon_max_discount ?? 0,
                'heading' => $request->coupon_heading ?? '',
                'description' => $request->coupon_description ?? '',
            ] : null,
            'taxes' => $request->taxes ?? [],
        ];

        $appointment->order_summary = json_encode($orderSummary);
        $appointment->save();

        // handle documents upload
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                $docs = new AppointmentDocs();
                $docs->appointment_id = $appointment->id;
                $docs->image = GlobalFunction::saveFileAndGivePath($document);
                $docs->save();
            }
        }
        // return redirect()->route('appointments')->with('success', 'Appointment added successfully');

        return GlobalFunction::sendDataResponse(true, 'data stored successfully', $appointment);

        // return redirect('fetchAllAppointmentsList')->with('success', 'Appointment added successfully');
    }

    public function getAppointmentForEdit($id)
    {
        // dd('dd');
        try {
            $appointment = Appointments::with(['user', 'doctor'])->findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment not found'
            ]);
        }
    }

    // Function للتحديث
    public function updateAppointmentForAdmin(Request $request, $id)
    {
        $rules = [
            'user_id'          => 'required|exists:users,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'date'             => 'required|date',
            'time'             => 'required',
            'type'             => 'required|in:0,1',
            'session_type'     => 'nullable|in:voice,video,chat',
            'status'           => 'required|in:0,1,2,3,4,5',
            'is_urgent'        => 'required|in:0,1',
            'problem'          => 'required|string',
            'service_amount'   => 'required|numeric|min:0',
            'discount_amount'  => 'nullable|numeric|min:0',
            'total_tax_amount' => 'nullable|numeric|min:0',
            'subtotal'         => 'required|numeric|min:0',
            'payable_amount'   => 'required|numeric|min:0',
            'is_coupon_applied' => 'nullable|in:0,1',
            'coupon_title'     => 'nullable|string',
            'documents.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['status' => false, 'message' => $messages[0]]);
        }

        try {
            // البحث عن الـ appointment
            $appointment = Appointments::findOrFail($id);

            // تحديث البيانات
            $appointment->user_id          = $request->user_id;
            $appointment->doctor_id        = $request->doctor_id;
            $appointment->date             = $request->date;
            $appointment->time             = str_replace(':', '', $request->time);
            $appointment->type             = $request->type;
            $appointment->session_type     = $request->session_type ?? null;
            $appointment->status           = $request->status;
            $appointment->is_urgent        = $request->is_urgent;
            $appointment->problem          = $request->problem;
            $appointment->service_amount   = $request->service_amount;
            $appointment->discount_amount  = $request->discount_amount ?? 0;
            $appointment->total_tax_amount = $request->total_tax_amount ?? 0;
            $appointment->subtotal         = $request->subtotal;
            $appointment->payable_amount   = $request->payable_amount;
            $appointment->is_coupon_applied = $request->is_coupon_applied ?? 0;
            $appointment->coupon_title     = $request->coupon_title ?? null;

            // تحديث الـ order_summary
            $orderSummary = [
                'service_amount' => $request->service_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'subtotal' => $request->subtotal,
                'total_tax_amount' => $request->total_tax_amount ?? 0,
                'payable_amount' => $request->payable_amount,
                'coupon_apply' => $request->is_coupon_applied ?? 0,
                'coupon' => $request->is_coupon_applied == 1 ? [
                    'id' => $request->coupon_id,
                    'coupon' => $request->coupon_title,
                    'percentage' => $request->coupon_percentage ?? 0,
                    'max_discount_amount' => $request->coupon_max_discount ?? 0,
                    'heading' => $request->coupon_heading ?? '',
                    'description' => $request->coupon_description ?? '',
                ] : null,
                'taxes' => $request->taxes ?? [],
            ];

            $appointment->order_summary = json_encode($orderSummary);
            $appointment->save();

            // handle documents upload
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $docs = new AppointmentDocs();
                    $docs->appointment_id = $appointment->id;
                    $docs->image = GlobalFunction::saveFileAndGivePath($document);
                    $docs->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Appointment updated successfully',
                'data' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteAppointmentForAdmin($id)
    {
        try {
            // البحث عن الـ appointment
            $appointment = Appointments::findOrFail($id);

            // حذف الوثائق المرتبطة بالموعد أولاً
            $appointmentDocs = AppointmentDocs::where('appointment_id', $id)->get();

            foreach ($appointmentDocs as $doc) {
                // حذف الملف من التخزين إذا كان موجوداً
                if ($doc->image && file_exists(public_path($doc->image))) {
                    unlink(public_path($doc->image));
                }
                // حذف السجل من قاعدة البيانات
                $doc->delete();
            }

            // حذف الموعد
            $appointment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    function fetchMyAppointments(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }


        $result = Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->Where('user_id', $request->user_id)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($result as $appointment) {
            $appointment->previous_appointments =
                Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
                ->Where('user_id', $request->user_id)
                ->Where('doctor_id', $appointment->doctor_id)
                ->WhereNotIn('id', [$appointment->id])
                ->WhereIn('status', [Constants::orderCompleted, Constants::orderCancelled, Constants::orderDeclined])
                ->get();
        }

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }
    function fetchMyPrescriptions(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }

        $items = Prescriptions::with(['user', 'appointment', 'appointment.doctor'])
            ->where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $items);
    }

    function addRating(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'user_id' => 'required',
            'comment' => 'required',
            'rating' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents', 'rating', 'prescription'])
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        if ($appointment->user_id != $request->user_id) {
            return response()->json(['status' => false, 'message' => "This appointment doesn't belong to this user"]);
        }
        if ($appointment->status != Constants::orderCompleted) {
            return response()->json(['status' => false, 'message' => "This appointment is not yet completed to rate!"]);
        }
        if ($appointment->is_rated == 1) {
            return response()->json(['status' => false, 'message' => "This appointment has been rated already!"]);
        }

        // Add rating
        $review = new DoctorReviews();
        $review->user_id = $appointment->user_id;
        $review->doctor_id = $appointment->doctor_id;
        $review->appointment_id = $appointment->id;
        $review->rating = $request->rating;
        $review->comment = GlobalFunction::cleanString($request->comment);
        $review->save();

        $appointment->is_rated = 1;
        $appointment->save();

        $doctor = $review->doctor;
        $doctor->rating = $doctor->avgRating();
        $doctor->save();

        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents', 'rating', 'prescription'])
            ->first();

        return GlobalFunction::sendDataResponse(true, 'appointment rated successfully!', $appointment);
    }

    function submitDoctorWithdrawRequest(Request $request)
    {
        $rules = [
            'doctor_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctor = Doctors::find($request->doctor_id);
        if ($doctor == null) {
            return response()->json(['status' => false, 'message' => "Doctor doesn't exists!"]);
        }
        $settings = GlobalSettings::first();
        if ($doctor->wallet < $settings->min_amount_payout_doctor) {
            return response()->json(['status' => false, 'message' => "Insufficient amount to withdraw!"]);
        }

        $item = new DoctorPayoutHistory();
        $item->request_number = GlobalFunction::generateDoctorWithdrawRequestNumber();
        $item->amount = $doctor->wallet;
        $item->doctor_id = $doctor->id;
        $item->save();

        $summary = 'Withdraw request :' . $item->request_number;
        // Adding wallet statement
        GlobalFunction::addDoctorStatementEntry(
            $doctor->id,
            null,
            $doctor->wallet,
            Constants::debit,
            Constants::doctorWalletWithdraw,
            $summary
        );

        //resetting users wallet
        $doctor->wallet = 0;
        $doctor->save();

        return GlobalFunction::sendSimpleResponse(true, 'Doctor withdraw request submitted successfully!');
    }

    function rescheduleAppointment(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'user_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }

        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents', 'rating', 'prescription'])
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        if ($appointment->user_id != $request->user_id) {
            return response()->json(['status' => false, 'message' => "This appointment doesn't belong to this user"]);
        }

        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->status = Constants::orderPlacedPending;
        $appointment->save();

        // Send Push to user
        $title = "Appointment :" . $appointment->appointment_number;
        $message = "Appointment has been rescheduled successfully!";
        GlobalFunction::sendPushToUser($title, $message, $user);

        return GlobalFunction::sendDataResponse(true, 'Appointment rescheduled successfully!', $appointment);
    }
    function checkPolicy(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'user_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $appointment = Appointments::where('id', $request->appointment_id)
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }
        if ($appointment->user_id != $request->user_id) {
            return response()->json(['status' => false, 'message' => "This appointment doesn't belong to this user"]);
        }
        if ($appointment->status == Constants::orderCancelled || $appointment->status == Constants::orderDeclined || $appointment->status == Constants::orderCompleted) {
            return response()->json(['status' => false, 'message' => "This appointment is not eligible to be cancelled!"]);
        }
        $today = date("Y-m-d");
        $dateBefor7Day = date('Y-m-d', strtotime($today . ' - 7 days'));
        $data = (object)array();

        $lastCancelledAppointment = Appointments::where('status', Constants::orderCancelled)->where('user_id', $request->user_id)->whereBetween('cancelled_date', [$dateBefor7Day, $today])
            ->first();
        // if($lastCancelledAppointment!==null){
        //  return response()->json(['status' => false, 'message' => "الحد الأقصى لإلغاء الجلسات المحجوزة هو جلسة واحدة في الأسبوع!"]);
        // }
        // echo date("h:i:sa",mktime($appointment->time)).">=".date("h:i:sa",strtotime(date("h:i"). ' + 12 hours'));
        // die();
        $time = substr($appointment->time, 0, 2) . ':' . substr($appointment->time, 2);
        $new_date = date("Y-m-d H:i", strtotime($appointment->date . $time));
        // if(strtotime($appointment->date)==strtotime($today)){
        if ($new_date >= date("Y-m-d H:i", strtotime(date("Y-m-d H:i") . ' + 12 hours'))) {
            $data->recovery_rate = 100;
            $data->recovery_amount = $appointment->subtotal;
            $data->message = "إلغاء الجلسة حتى 12 ساعة قبل الموعد يتم استرداد كامل السعر للجلسة";
        } elseif ($new_date >= date("Y-m-d H:i", strtotime(date("Y-m-d H:i") . ' + 6 hours'))) {
            $data->recovery_rate = 75;
            $data->recovery_amount = ($appointment->subtotal * 75) / 100;
            $data->message = "إلغاء الجلسة من ١٢ ساعة حتى ٦ ساعات قبل الموعد يتم استرداد ٧٥%  من سعر الجلسة";
        } elseif ($new_date >= date("Y-m-d H:i", strtotime(date("Y-m-d H:i") . ' + 3 hours'))) {
            $data->recovery_rate = 50;
            $data->recovery_amount = ($appointment->subtotal * 50) / 100;
            $data->message = "الغاء الجلسة من ٦ ساعات حنى ٣ ساعات قبل الموعد يتم استرداد ٥٠% من سعر الجلسة";
        } elseif ($new_date < date("Y-m-d H:i", strtotime(date("Y-m-d H:i") . ' + 3 hours'))) {
            $data->recovery_rate = 0;
            $data->recovery_amount = 0;
            $data->message = "لا يتم استرداد اي نسبة من سعر الجلسة في حالة الغاء الجلسة قبل الموعد بثلاث ساعات اواقل";
        } else {
            $data->recovery_rate = 0;
            $data->recovery_amount = 0;
            $data->message = "لا يتم استرداد اي نسبة من سعر الجلسة في حالة الغاء الجلسه بعد موعدها";
        }
        // }elseif($appointment->date>$today){

        //   $data->recovery_rate=100;
        //  $data->recovery_amount=$appointment->subtotal;
        //  $data->message="إلغاء الجلسة حتى 12 ساعة قبل الموعد يتم استرداد كامل السعر للجلسة";
        //   }else{
        // $data->recovery_rate=0;
        //$data->recovery_amount=0;
        //   $data->message="لا يتم استرداد اي نسبة من سعر الجلسة في حالة الغاء الجلسة قبل الموعد بثلاث ساعات اواقل";
        //  }
        return GlobalFunction::sendDataResponse(true, 'appointment policy!', $data);
    }
    function cancelAppointment(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'user_id' => 'required',
            'recovery_amount' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }
        if ($appointment->user_id != $request->user_id) {
            return response()->json(['status' => false, 'message' => "This appointment doesn't belong to this user"]);
        }
        if ($appointment->status == Constants::orderCancelled || $appointment->status == Constants::orderDeclined || $appointment->status == Constants::orderCompleted) {
            return response()->json(['status' => false, 'message' => "This appointment is not eligible to be cancelled!"]);
        }
        $appointment->status = Constants::orderCancelled;
        $appointment->cancelled_date = date("Y-m-d");
        $appointment->save();
        if ($appointment->is_urgent == 0) {
            // Refunding to user
            $user->wallet = $user->wallet + $request->recovery_amount;
            $user->save();
            // Adding statement entry
            $summary = 'Booking Cancelled By User: ' . $appointment->appointment_number . ' Refund';
            GlobalFunction::addUserStatementEntry($user->id, $appointment->appointment_number, $request->recovery_amount, Constants::credit, Constants::refund, $summary);
        }
        // Send Push to user
        $title = "Appointment :" . $appointment->appointment_number;
        $message = "Appointment has been cancelled successfully!";
        GlobalFunction::sendPushToUser($title, $message, $user);

        return GlobalFunction::sendDataResponse(true, 'appointment cancelled successfully!', $appointment);
    }

    function fetchDoctorPayoutHistory(Request $request)
    {
        $rules = [
            'doctor_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctor = Doctors::find($request->doctor_id);
        if ($doctor == null) {
            return response()->json(['status' => false, 'message' => "doctor doesn't exists!"]);
        }

        $history = DoctorPayoutHistory::where('doctor_id', $doctor->id)
            ->orderBy('id', 'DESC')
            ->get();

        return GlobalFunction::sendDataResponse(true, 'Payout history Data fetched successfully!', $history);
    }

    function completeAppointment(Request $request)
    {
        $rules = [
            'doctor_id' => 'required',
            'appointment_id' => 'required',
            // 'completion_otp' => 'required',
            'diagnosed_with' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $commissionAmount = 0;
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Doctor does not exists!');
        }
        $appointment = Appointments::where('id', $request->appointment_id)->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        if ($appointment->doctor_id != $request->doctor_id) {
            return response()->json(['status' => false, 'message' => "Appointment is not owned by this doctor!"]);
        }
        // if ($appointment->completion_otp != $request->completion_otp) {
        //     return response()->json(['status' => false, 'message' => "Completion OTP is incorrect!"]);
        // }
        if ($appointment->status == Constants::orderAccepted) {
            $appointment->status = Constants::orderCompleted;
            $appointment->diagnosed_with = $request->diagnosed_with;
            $appointment->save();

            // Commission calculation
            $earning = $appointment->subtotal;
            $settings = GlobalSettings::first();
            $commissionAmount = ($settings->comission / 100) * $earning;

            // Adding Earning statement
            $earningSummary = "Earning from appointment: " . $appointment->appointment_number;
            GlobalFunction::addDoctorStatementEntry($doctor->id, $appointment->appointment_number, $earning, Constants::credit, Constants::doctorWalletEarning, $earningSummary);

            // Adding Commission deduct statement
            $commissionSummary = "Commission of appointment: " . $appointment->appointment_number . " : (" . $settings->comission . "%)";
            GlobalFunction::addDoctorStatementEntry($doctor->id, $appointment->appointment_number, $commissionAmount, Constants::debit, Constants::doctorWalletCommission, $commissionSummary);

            // Adding earning to doctor wallet + count increase + lifetime earning increase
            $earningAfterCommission = $earning - $commissionAmount;
            $doctor->wallet = $doctor->wallet + $earningAfterCommission;
            $doctor->total_patients_cured = $doctor->total_patients_cured + 1;
            $doctor->lifetime_earnings = $doctor->lifetime_earnings + $earningAfterCommission;
            $doctor->save();

            // Adding Earning Logs Of Doctor
            $doctorEarningHistory = new DoctorEarningHistory();
            $doctorEarningHistory->doctor_id = $doctor->id;
            $doctorEarningHistory->appointment_id = $appointment->id;
            $doctorEarningHistory->earning_number = GlobalFunction::generateDoctorEarningHistoryNumber();
            $doctorEarningHistory->amount = $earningAfterCommission;
            $doctorEarningHistory->save();

            // Adding Earning Logs of Platform
            $platformEarningHistory = new PlatformEarningHistory();
            $platformEarningHistory->earning_number = GlobalFunction::generatePlatformEarningHistoryNumber();
            $platformEarningHistory->amount = $commissionAmount;
            if ($appointment->is_urgent == 1) {
                $platformEarningHistory->commission_percentage = $settings->urgent_comission;
            } else {
                $platformEarningHistory->commission_percentage = $settings->comission;
            }
            $platformEarningHistory->appointment_id = $appointment->id;
            $platformEarningHistory->doctor_id = $doctor->id;
            $platformEarningHistory->save();

            // Increasing total platform earning data
            $platformData = PlatformData::first();
            $platformData->lifetime_earnings = $platformData->lifetime_earnings ? $platformData->lifetime_earnings + $commissionAmount : $commissionAmount;
            $platformData->save();

            // Send Push to user
            $title = "Appointment :" . $appointment->appointment_number;
            $message = "Appointment has been completed by doctor!";
            GlobalFunction::sendPushToUser($title, $message, $appointment->user);

            return GlobalFunction::sendSimpleResponse(true, 'Appointment completed successfully');
        } else {
            return response()->json(['status' => false, 'message' => "This booking can't be completed!"]);
        }
    }
    function declineAppointment(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'doctor_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents'])
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Doctor does not exists!');
        }
        if ($appointment->doctor_id != $doctor->id) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment not owned by this doctor!');
        }
        if ($appointment->status == Constants::orderPlacedPending) {
            $appointment->status = Constants::orderDeclined;
            $appointment->save();

            // Refunding to user
            $user = $appointment->user;
            if ($appointment->is_urgent == 0) {
                $user->wallet = $user->wallet + $appointment->payable_amount;
                $user->save();

                // Adding statement entry
                $summary = 'Appointment Declined By Doctor : ' . $appointment->appointment_number . ' Refund';
                GlobalFunction::addUserStatementEntry($user->id, $appointment->appointment_number, $appointment->payable_amount, Constants::credit, Constants::refund, $summary);
            }
            // Send Push to user
            $title = "Appointment :" . $appointment->appointment_number;
            $message = "Appointment has been declined!";
            GlobalFunction::sendPushToUser($title, $message, $appointment->user);

            return GlobalFunction::sendSimpleResponse(true, 'Appointment declined successfully');
        } else {
            return response()->json(['status' => false, 'message' => "This appointment can't be declined!"]);
        }
    }
    function acceptAppointment(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'doctor_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents'])
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Doctor does not exists!');
        }
        if ($appointment->doctor_id != $doctor->id) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment not owned by this doctor!');
        }

        if ($appointment->status == Constants::orderPlacedPending) {
            $appointment->status = Constants::orderWittingPayment;
            $appointment->save();

            // Send Push to user
            $title = "Appointment :" . $appointment->appointment_number;
            $message = "Appointment has been accepted!";
            GlobalFunction::sendPushToUser($title, $message, $appointment->user);

            return GlobalFunction::sendSimpleResponse(true, 'Appointment accepted successfully');
        } else {
            return response()->json(['status' => false, 'message' => "This appointment can't be accepted!"]);
        }
    }

    function fetchAppointmentDetails(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $result = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating', 'rating'])
            ->first();
        if ($result == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }

        $result->previous_appointments =
            Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->Where('doctor_id', $result->doctor_id)
            ->Where('user_id', $result->user_id)
            ->WhereNotIn('id', [$result->id])
            ->WhereIn('status', [Constants::orderCompleted, Constants::orderCancelled, Constants::orderDeclined])
            ->get();

        return GlobalFunction::sendDataResponse(true, 'Data fetched successfully', $result);
    }

    function fetchDoctorWalletStatement(Request $request)
    {
        $rules = [
            'doctor_id' => 'required',
            'start' => 'required',
            'count' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctor = Doctors::find($request->doctor_id);
        if ($doctor == null) {
            return response()->json(['status' => false, 'message' => "Doctor doesn't exists!"]);
        }
        $statement = DoctorWalletStatements::where('doctor_id', $doctor->id)
            ->offset($request->start)
            ->limit($request->count)
            ->orderBy('id', 'DESC')
            ->get();

        return GlobalFunction::sendDataResponse(true, 'Statement Data fetched successfully!', $statement);
    }

    function fetchDoctorEarningHistory(Request $request)
    {
        $rules = [
            'doctor_id' => 'required',
            'month' => 'required',
            'year' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctor = Doctors::find($request->doctor_id);
        if ($doctor == null) {
            return response()->json(['status' => false, 'message' => "doctor doesn't exists!"]);
        }

        $statement = DoctorEarningHistory::where('doctor_id', $doctor->id)
            ->whereMonth('created_at', $request->month)
            ->whereYear('created_at', $request->year)
            ->orderBy('id', 'DESC')
            ->get();


        return GlobalFunction::sendDataResponse(true, 'Earning history Data fetched successfully!', $statement);
    }
    function fetchAcceptedAppointsByDate(Request $request)
    {
        $rules = [
            'date' => 'required',
            'doctor_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Doctor does not exists!');
        }

        $result = Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->Where('doctor_id', $request->doctor_id)
            ->Where('date', $request->date)
            ->where('status', Constants::orderAccepted)
            ->get();

        foreach ($result as $appointment) {
            $appointment->previous_appointments =
                Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
                ->Where('doctor_id', $request->doctor_id)
                ->WhereNotIn('id', [$appointment->id])
                ->WhereIn('status', [Constants::orderCompleted, Constants::orderCancelled, Constants::orderDeclined])
                ->where('status', Constants::orderPlacedPending)
                ->get();
        }

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }

    function fetchAppointmentHistory(Request $request)
    {
        $rules = [
            'start' => 'required',
            'count' => 'required',
            'doctor_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Doctor does not exists!');
        }
        $result = Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->Where('doctor_id', $request->doctor_id)
            ->offset($request->start)
            ->limit($request->count)
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($result as $appointment) {
            $appointment->previous_appointments =
                Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
                ->Where('doctor_id', $request->doctor_id)
                ->Where('user_id', $appointment->user_id)
                ->WhereNotIn('id', [$appointment->id])
                ->WhereIn('status', [Constants::orderCompleted, Constants::orderCancelled, Constants::orderDeclined])
                ->get();
        }

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }
    function fetchAppointmentRequests(Request $request)
    {
        $rules = [
            'start' => 'required',
            'count' => 'required',
            'doctor_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $doctor = Doctors::where('id', $request->doctor_id)->first();
        if ($doctor == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Doctor does not exists!');
        }
        $result = Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
            ->Where('doctor_id', $request->doctor_id)
            ->where('status', Constants::orderPlacedPending)
            ->offset($request->start)
            ->limit($request->count)
            ->get();

        foreach ($result as $appointment) {
            $appointment->previous_appointments =
                Appointments::with(['user', 'patient', 'doctor', 'documents', 'prescription', 'rating'])
                ->Where('doctor_id', $request->doctor_id)
                ->Where('user_id', $appointment->user_id)
                ->WhereNotIn('id', [$appointment->id])
                ->WhereIn('status', [Constants::orderCompleted, Constants::orderCancelled, Constants::orderDeclined])
                ->where('status', Constants::orderPlacedPending)
                ->get();
        }

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }

    function editPrescription(Request $request)
    {
        $rules = [
            'prescription_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $prescription = Prescriptions::where('id', $request->prescription_id)
            ->first();
        if ($prescription == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Prescription does not exists!');
        }
        $prescription->medicine = $request->medicine;
        $prescription->save();

        return GlobalFunction::sendSimpleResponse(true, 'Prescription edited successfully');
    }
    //
    function addPrescription(Request $request)
    {
        $rules = [
            'appointment_id' => 'required',
            'user_id' => 'required',
            'medicine' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $appointment = Appointments::where('id', $request->appointment_id)
            ->with(['user', 'patient', 'doctor', 'documents'])
            ->first();
        if ($appointment == null) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment does not exists!');
        }
        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return GlobalFunction::sendSimpleResponse(false, 'User does not exists!');
        }
        if ($appointment->user_id != $user->id) {
            return GlobalFunction::sendSimpleResponse(false, 'Appointment not owned by this user!');
        }
        $prescription = Prescriptions::where('user_id', $user->id)->where('appointment_id', $appointment->id)->first();
        if ($prescription != null) {
            return GlobalFunction::sendSimpleResponse(false, 'This appointment has prescription already!');
        }

        $prescription = new Prescriptions();
        $prescription->user_id = $user->id;
        $prescription->appointment_id = $appointment->id;
        $prescription->medicine = $request->medicine;
        $prescription->save();

        return GlobalFunction::sendSimpleResponse(true, 'Prescription added successfully');
    }

    function addAppointment(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'doctor_id' => 'required',
            'problem' => 'required',
            'date' => 'required',
            'time' => 'required',
            'type' => 'required',
            'order_summary' => 'required',
            'is_coupon_applied' => [Rule::in(1, 0)],
            'service_amount' => 'required',
            'discount_amount' => 'required',
            'subtotal' => 'required',
            'total_tax_amount' => 'required',
            'payable_amount' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $settings = GlobalSettings::first();

        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }

        $appointmentCount = Appointments::where('user_id', $user->id)
            ->where('status', Constants::orderPlacedPending)
            ->orWhere('status', Constants::orderAccepted)
            ->count();
        if ($appointmentCount >= $settings->max_order_at_once) {
            return response()->json(['status' => false, 'message' => "Maximum, at a time order limit, reached!"]);
        }

        $doctor = Doctors::find($request->doctor_id);
        if ($doctor == null) {
            return response()->json(['status' => false, 'message' => "Doctor doesn't exists!"]);
        }
        if ($doctor->on_vacation == 1) {
            return response()->json(['status' => false, 'message' => "this doctor is on vacation!"]);
        }
        if ($doctor->status != Constants::statusDoctorApproved) {
            return response()->json(['status' => false, 'message' => "this doctor is not active!"]);
        }

        // if ($user->wallet < $request->payable_amount) {
        //     return GlobalFunction::sendSimpleResponse(false, 'Insufficient balance in wallet');
        // }

        $appointment = new Appointments();
        if ($request->has('patient_id') && ($request->patient_id != 0 || $request->patient_id != '')) {
            $patient = AddedPatients::find($request->patient_id);
            if ($patient == null) {
                return response()->json(['status' => false, 'message' => "Patient doesn't exists!"]);
            }
            $appointment->patient_id = $request->patient_id;
        }

        $appointment->appointment_number = GlobalFunction::generateAppointmentNumber();
        $appointment->completion_otp = rand(1000, 9999);
        $appointment->user_id = $request->user_id;
        $appointment->doctor_id = $request->doctor_id;
        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->type = $request->type;
        if (isset($request->is_urgent) && $request->is_urgent == 1) {
            $appointment->status = 0;
        }
        if ($request->has('status')) {
            $appointment->status = $request->status;
        }
        $appointment->problem = GlobalFunction::cleanString($request->problem);
        $appointment->order_summary = $request->order_summary;
        $appointment->is_coupon_applied = $request->is_coupon_applied;

        $appointment->service_amount = $request->service_amount;
        $appointment->discount_amount = $request->discount_amount;
        $appointment->subtotal = $request->subtotal;
        $appointment->total_tax_amount = $request->total_tax_amount;
        $appointment->payable_amount = $request->payable_amount;
        $appointment->session_type = $request->session_type;
        $appointment->is_urgent = $request->is_urgent;

        if ($request->is_coupon_applied == 1) {
            $appointment->coupon_title = $request->coupon_title;
            // add coupon to used coupon
            $discounts = explode(',', $user->coupons_used);
            array_push($discounts, $request->coupon_id);
            $user->coupons_used = implode(',', $discounts);
        }

        $appointment->save();
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                $docs = new AppointmentDocs();
                $docs->appointment_id = $appointment->id;
                $docs->image = GlobalFunction::saveFileAndGivePath($document);
                $docs->save();
            }
        }
        // Deducting Money From Wallet
        if ($request->payment_type == 'wallet') {
            $user->wallet = $user->wallet - $request->payable_amount;
            $user->save();
        }
        // Send Push to user
        $title = "Appointment :" . $appointment->appointment_number;
        $message = "Appointment has been placed successfully!";
        GlobalFunction::sendPushToUser($title, $message, $user);

        // Send push to doctor
        $title = "New Appointment Request Received";
        $message = "Review the details and accept.";
        GlobalFunction::sendPushToDoctor($title, $message, $doctor);

        // Add statement entry
        if (isset($request->is_urgent) && $request->is_urgent == 0) {
            GlobalFunction::addUserStatementEntry(
                $user->id,
                $appointment->appointment_number,
                $appointment->payable_amount,
                Constants::debit,
                Constants::purchase,
                null,
            );
        }

        $appointment = Appointments::where('id', $appointment->id)->with(['user', 'doctor', 'patient', 'documents'])->first();

        return GlobalFunction::sendDataResponse(true, 'Appointment placed successfully', $appointment);
    }

    function addPayment(Request $request)
    {
        $appointment = Appointments::where('id', $request->appointment_id)->with(['user', 'doctor', 'patient', 'documents'])->first();
        if ($appointment->status == 5) {
            $user = Users::find($request->user_id);
            $user->wallet = $user->wallet - $request->payable_amount;
            $user->save();
            GlobalFunction::addUserStatementEntry(
                $user->id,
                $appointment->appointment_number,
                $request->payable_amount,
                Constants::debit,
                Constants::purchase,
                null,
            );
            $appointment->status = 1;
            $appointment->is_urgent = 0;
            $appointment->save();
            $doctor = Doctors::find($appointment->doctor_id);
            // Send push to doctor
            $title = "patient paid Appointment successfully";
            $message = "Review the details and start session.";
            GlobalFunction::sendPushToDoctor($title, $message, $doctor);


            return GlobalFunction::sendDataResponse(true, 'Appointment paid successfully', $appointment);
        } else {
            return GlobalFunction::sendDataResponse(false, 'The Appointment should be accepted by doctor', $appointment);
        }
    }
    function callpack_payment_success(Request $request)
    {
        $appointment = Appointments::where('id', $request->appointment_id)->with(['user', 'doctor', 'patient', 'documents'])->first();
        if (isset($appointment->status) && $appointment->status == 5) {
            $appointment->status = 1;
            $appointment->save();
        }
        $type = true;
        return view('success', ['type' => $type]);
    }
    function callpack_payment_failure(Request $request)
    {
        $appointment = Appointments::where('id', $request->appointment_id)->with(['user', 'doctor', 'patient', 'documents'])->first();
        if (isset($appointment->status) && $appointment->status == 5) {
            $appointment->status = 3;
            $appointment->save();
        }
        $type = false;
        return view('success', ['type' => $type]);
    }
    function fetchCoupons(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::find($request->user_id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists!"]);
        }
        $data = Coupons::whereNotIn('id', explode(',', $user->coupons_used))->orderBy('id', 'DESC')->get();
        return GlobalFunction::sendDataResponse(true, 'coupons fetched successfully', $data);
    }
}
