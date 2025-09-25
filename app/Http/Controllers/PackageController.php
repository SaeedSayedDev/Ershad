<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\Taxes;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function packages()
    {
        return view('packages');
    }

    public function fetchPackagesList(Request $request)
    {
        $totalData = Package::count();
        $rows = Package::orderBy('id', 'DESC')->get();

        $result = $rows;
        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'days',
            3 => 'price',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Package::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Package::where('name', 'LIKE', "%{$search}%")
                ->orWhere('days', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Package::where('name', 'LIKE', "%{$search}%")
                ->orWhere('days', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = array();
        foreach ($result as $item) {
            $edit = '<a data-name="' . $item->name . '" data-days="' . $item->days . '" data-price="' . $item->price . '" data-description="' . $item->description . '" href="" class="mr-2 btn btn-primary text-white edit" rel=' . $item->id . ' >Edit</a>';
            $delete = '<a href="" class="mr-2 btn btn-danger text-white delete" rel=' . $item->id . ' >Delete</a>';
            $action = $edit . $delete;

            $data[] = array(
                $item->name,
                $item->days,
                $item->price,
                $action,
            );
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );

        return response()->json($json_data);
    }

    public function addPackage(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'days' => 'required|integer|gt:0',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $package = new Package();
        $package->name = $request->name;
        $package->days = $request->days;
        $package->price = $request->price;
        $package->description = $request->description;
        $package->save();

        return response()->json(['status' => true, 'message' => 'Package added successfully!']);
    }

    public function editPackage(Request $request)
    {
        $rules = [
            'id' => 'required|exists:packages,id',
            'name' => 'required|string',
            'days' => 'required|integer|gt:0',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $package = Package::find($request->id);
        $package->name = $request->name;
        $package->days = $request->days;
        $package->price = $request->price;
        $package->description = $request->description;
        $package->save();

        return response()->json(['status' => true, 'message' => 'Package edited successfully!']);
    }

    public function deletePackage($id)
    {
        $package = Package::find($id);
        if ($package) {
            $package->delete();
            return response()->json(['status' => true, 'message' => 'Package deleted successfully!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Package not found.']);
        }
    }

    function fetchPackages(Request $request)
    {
        $rules = [
            'number_days' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $result = Package::orderBy('id', 'DESC')
            ->whereDays($request->number_days)
            ->first();
            if($result){
            $tax=Taxes::where('status', 1)->where('tax_title','adds tax')->first();
            if($result->type==0){
            $totaltax=($result->price*$tax->value)/100;
            }else{
                 $totaltax=$tax->value;
            }
           $result->taxPrice= $totaltax;
           $result->totalPrice= $totaltax+$result->price;

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
            }else{
                return response()->json(['status' => false, 'message' => "no data found!"]);
            }
    }

}
