<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function contacts()
    {
        return view('contacts');
    }

    function fetchContactsList(Request $request)
    {
        $totalData = Contact::count();
        $rows = Contact::orderBy('id', 'DESC')->get();

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
            $result = Contact::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Contact::Where('type', 'LIKE', "%{$search}%")
                ->orWhere('value', 'LIKE', "%{$search}%")
                ->orWhere('link', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Contact::Where('type', 'LIKE', "%{$search}%")
            ->orWhere('value', 'LIKE', "%{$search}%")
            ->orWhere('link', 'LIKE', "%{$search}%")
                ->count();
        }
        
        $data = array();
        foreach ($result as $item) {
            $edit = '<a data-type="' . $item->type . '" data-value="' . $item->value . '" data-link="' . $item->link . '" href="" class="mr-2 btn btn-primary text-white edit" rel=' . $item->id . ' >' . __("Edit") . '</a>';
            $delete = '<a href="" class="mr-2 btn btn-danger text-white delete" rel=' . $item->id . ' >' . __("Delete") . '</a>';
            $action = $edit . $delete;
            $link = $item->link ? '<a href="' . $item->link . '" target="_blank">' . $item->value . '</a>' : $item->value;

            $data[] = array(
                $item->type,
                $link,
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

    public function addContact(Request $request)
    {
        $rules = [
            'type' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $contact = new Contact();
        $contact->type = $request->type;
        $contact->value = $request->value;
        $contact->link = $request->link;
        $contact->save();

        return response()->json(['status' => true, 'message' => 'Contact added successfully']);
    }

    public function editContact(Request $request)
    {
        $rules = [
            'type' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $contact = Contact::find($request->id);
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Contact not found']);
        }

        $contact->type = $request->type;
        $contact->value = $request->value;
        $contact->link = $request->link;
        $contact->save();

        return response()->json(['status' => true, 'message' => 'Contact updated successfully']);
    }


    public function deleteContact($id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['status' => false, 'message' => 'Contact not found']);
        }

        $contact->delete();

        return response()->json(['status' => true, 'message' => 'Contact deleted successfully']);
    }

    function fetchContacts(Request $request)
    {
        $rules = [
            'start' => 'required',
            'count' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $result = Contact::orderBy('id', 'DESC')
            ->offset($request->start)
            ->limit($request->count)
            ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }
}
