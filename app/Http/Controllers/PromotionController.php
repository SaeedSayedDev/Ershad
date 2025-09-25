<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\DoctorPromotion;
use App\Models\GlobalFunction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    function askDoctorForPromotion(Request $request)
    {
        $rules = [
            'doctor_id' => 'required|exists:doctors,id',
            'days' => 'required|numeric|gt:0',
            'price' => 'required|numeric|gt:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $available = DoctorPromotion::where('doctor_id', $request->doctor_id)
        ->where(function ($query) {
            $query->where('status', 0)
            ->orWhere(function ($query) {
                $query->where('status', 1)->where('expiration_date', '>', Carbon::now());
            });
        })->exists();
        if ($available) {
            return Globalfunction::sendSimpleResponse(false, 'Promotion already exists!');
        }

        # TODO: pay

        $doctorPromotion = new DoctorPromotion();
        $doctorPromotion->doctor_id = $request->doctor_id;
        $doctorPromotion->days = $request->days;
        $doctorPromotion->price = $request->price;
        $doctorPromotion->save();

        return Globalfunction::sendDataResponse(true, 'Promotion added successfully', $doctorPromotion);
    }

    function fetchDoctorPromotion(Request $request)
    {
        $rules = [
            'doctor_id' => 'required|exists:doctors,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctorPromotion = DoctorPromotion::where('doctor_id', $request->doctor_id)->get();

        return Globalfunction::sendDataResponse(true, 'Promotion details fetched successfully', $doctorPromotion);
    }

    function fetchAllDoctorPromotions(Request $request)
    {
        $rules = [
            'start' => 'required|numeric|min:0',
            'count' => 'required|numeric|gt:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctorPromotions = DoctorPromotion::offset($request->start)->limit($request->count)->get();

        return GlobalFunction::sendDataResponse(true, 'Promotions details fetched successfully', $doctorPromotions);
    }

    function fetchApprovedDoctorPromotions(Request $request)
    {
        $rules = [
            'start' => 'required|numeric|min:0',
            'count' => 'required|numeric|gt:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $doctorPromotions = DoctorPromotion::where('status', Constants::promotionApproved)->offset($request->start)->limit($request->count)->get();

        return GlobalFunction::sendDataResponse(true, 'Promotions details fetched successfully', $doctorPromotions);
    }

    public function promotions()
    {
        return view('promotions');
    }

    public function approvePromotion($id)
    {
        $promotion = DoctorPromotion::find($id);
        if ($promotion->status != Constants::promotionPending) {
            return GlobalFunction::sendSimpleResponse(false, 'Promotion already approved or rejected!');
        }

        $promotion->status = Constants::promotionApproved;
        $promotion->expiration_date = Carbon::now()->addDays($promotion->days);
        $promotion->save();

        return GlobalFunction::sendSimpleResponse(true, 'Promotion approved successfully!');
    }

    public function rejectPromotion(Request $request)
    {
        $promotion = DoctorPromotion::find($request->id);
        if ($promotion->status != Constants::promotionPending) {
            return GlobalFunction::sendSimpleResponse(false, 'Promotion already approved or rejected!');
        }
        
        $promotion->status = Constants::promotionRejected;
        $promotion->rejection_reason = $request->rejection_reason;
        $promotion->save(); 

        return GlobalFunction::sendSimpleResponse(true, 'Promotion rejected successfully!');
    }
    
    function fetchAllPromotionsList(Request $request)
    {
        $totalData = DoctorPromotion::count();
        $rows = DoctorPromotion::orderBy('id', 'DESC')->get();

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
            $result = DoctorPromotion::with('doctor')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  DoctorPromotion::with('doctor')
                ->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = DoctorPromotion::with('doctor')
                ->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {
            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }

            $status = GlobalFunction::returnPromotionStatus($item->status);
            $days = $item->days;
            $price = $item->price;
            
            $data[] = array(
                $doctor,
                $status,
                $days,
                $price,
                GlobalFunction::formateTimeString($item->created_at),
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

    function fetchPendingPromotionsList(Request $request)
    {
        $totalData = DoctorPromotion::where('status', Constants::promotionPending)->count();
        $rows = DoctorPromotion::where('status', Constants::orderPlacedPending)->orderBy('id', 'DESC')->get();

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
            $result = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionPending)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionPending)->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionPending)->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {
            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }

            $approve = '<a href="" class="mr-2 btn btn-success text-white approve" rel=' . $item->id . ' >' . __("Approve") . '</a>';
            $reject = '<a href="" class="mr-2 btn btn-danger text-white reject" rel=' . $item->id . ' >' . __("Reject") . '</a>';
            $action = $item->status == 0 ? $approve . $reject : '';

            $status = GlobalFunction::returnPromotionStatus($item->status);

            $days = $item->days;
            $price = $item->price;

            $data[] = array(
                $doctor,
                $status,
                $days,
                $price,
                GlobalFunction::formateTimeString($item->created_at),
                $action,
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

    function fetchApprovedPromotionsList(Request $request)
    {
        $totalData = DoctorPromotion::where('status', Constants::promotionApproved)->count();
        $rows = DoctorPromotion::where('status', Constants::promotionApproved)->orderBy('id', 'DESC')->get();

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
            $result = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionApproved)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionApproved)->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionApproved)->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {
            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }

            $expiration_date = $item->expiration_date;
            $status = GlobalFunction::returnPromotionStatus($item->status);
            
            $data[] = array(
                $doctor,
                $status,
                $item->days,
                $item->price,
                $expiration_date,
                GlobalFunction::formateTimeString($item->created_at),
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

    function fetchRejectedPromotionsList(Request $request)
    {
        $totalData = DoctorPromotion::where('status', Constants::promotionRejected)->count();
        $rows = DoctorPromotion::where('status', Constants::promotionRejected)->orderBy('id', 'DESC')->get();

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
            $result = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionRejected)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionRejected)->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = DoctorPromotion::with('doctor')
                ->where('status', Constants::promotionRejected)->where(function ($query) use ($search) {
                $query->Where('price', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {
            $doctor = "";
            if ($item->doctor != null) {
                $doctor = '<a href="' . route('viewDoctorProfile', $item->doctor->id) . '"><span class="badge bg-primary text-white">' . $item->doctor->name . '</span></a>';
            }

            $rejected_reason = $item->rejection_reason;
            $status = GlobalFunction::returnPromotionStatus($item->status);

            $data[] = array(
                $doctor,
                $status,
                $item->days,
                $item->price,
                $rejected_reason,
                GlobalFunction::formateTimeString($item->created_at),
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
}
