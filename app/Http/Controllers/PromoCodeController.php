<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Validator;

class PromoCodeController extends Controller
{
    public function promoCodes()
    {
        $settings = GlobalSettings::first();
        $users = User::select('id', 'fullname')->get();
        return view('promo_codes', [
            'settings' => $settings,
            'users' => $users
        ]);
    }

    public function fetchAllPromoCodesList(Request $request)
    {
        $totalData = PromoCode::count();
        $rows = PromoCode::orderBy('id', 'DESC')->get();
        $settings = GlobalSettings::first();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'code',
            2 => 'percentage',
            3 => 'max_discount_amount',
            4 => 'min_order_amount',
            5 => 'heading',
            6 => 'description',
            7 => 'user_id',
            8 => 'expired_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = PromoCode::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = PromoCode::where(function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('heading', 'LIKE', "%{$search}%")
                    ->orWhere('percentage', 'LIKE', "%{$search}%")
                    ->orWhere('max_discount_amount', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PromoCode::where(function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('heading', 'LIKE', "%{$search}%")
                    ->orWhere('percentage', 'LIKE', "%{$search}%")
                    ->orWhere('max_discount_amount', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            })->count();
        }
        $data = array();
        foreach ($result as $item) {
            $edit = '<a data-description="' . $item->description . '" data-heading="' . $item->heading . '" data-percentage="' . $item->percentage . '" data-maxDiscAmount="' . $item->max_discount_amount . '" data-code="' . $item->code . '" data-userid="' . $item->user_id . '" data-expiredat="' . $item->expired_at . '" href="" class="mr-2 btn btn-primary text-white edit" rel=' . $item->id . ' >' . __("Edit") . '</a>';
            $delete = '<a href="" class="mr-2 btn btn-danger text-white delete" rel=' . $item->id . ' >' . __("Delete") . '</a>';
            $action = $edit . $delete;

            $data[] = array(
                $item->code,
                $item->percentage . '%',
                $settings->currency . $item->max_discount_amount,
                $item->heading,
                $item->description,
                $item->user_id ? User::find($item->user_id)?->fullname : __('Not assigned'),
                $item->expired_at ? $item->expired_at : __('No expiry'),
                $action,
            );
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );
        echo json_encode($json_data);
        exit();
    }

    public function addPromoCodeItem(Request $request)
    {
        $rules = [
            'code' => 'required|unique:promo_codes',
            'max_discount_amount' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0',
            'heading' => 'nullable|string',
            'description' => 'nullable|string',
            'expired_at' => 'required|date|after_or_equal:' . date('Y-m-d'),
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return GlobalFunction::sendSimpleResponse(false, $validator->errors()->first());
        }

        $promoCode = new PromoCode();
        $promoCode->code = $request->code;
        $promoCode->max_discount_amount = $request->max_discount_amount;
        $promoCode->percentage = $request->percentage;
        $promoCode->heading = $request->heading;
        $promoCode->description = $request->description;
        $promoCode->expired_at = $request->expired_at;
        $promoCode->save();

        return GlobalFunction::sendSimpleResponse(true, 'Promo code added successfully!');
    }

    public function editPromoCodeItem(Request $request)
    {
        $promoCode = PromoCode::find($request->id);

        $rules = [
            'code' => 'required|unique:promo_codes,code,' . $promoCode->id,
            'max_discount_amount' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0',
            'heading' => 'nullable|string',
            'description' => 'nullable|string',
            // 'user_id' => 'required|exists:users,id',
            'expired_at' => 'required|date|after_or_equal:' . date('Y-m-d'),
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return GlobalFunction::sendSimpleResponse(false, $validator->errors()->first());
        }

        $promoCode->code = $request->code;
        $promoCode->percentage = $request->percentage;
        $promoCode->max_discount_amount = $request->max_discount_amount;
        $promoCode->min_order_amount = $request->min_order_amount;
        $promoCode->heading = $request->heading;
        $promoCode->description = $request->description;
        // $promoCode->user_id = $request->user_id;
        $promoCode->expired_at = $request->expired_at;
        $promoCode->save();

        return GlobalFunction::sendSimpleResponse(true, 'Promo code edited successfully!');
    }

    public function deletePromoCode($id)
    {
        $promoCode = PromoCode::find($id);
        $promoCode->delete();
        return GlobalFunction::sendSimpleResponse(true, 'Promo code deleted successfully!');
    }

    public function checkPromoCode(Request $request)
    {
        $rules = [
            // 'user_id' => 'required',
            'promo_code' => 'required|exists:promo_codes,code',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $data = PromoCode::where('code', $request->promo_code)
            ->where('expired_at', '>=', date('Y-m-d'))
            ->get();

        return GlobalFunction::sendDataResponse(true, 'coupons fetched successfully', $data);
    }
}
