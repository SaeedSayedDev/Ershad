<?php

namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\DoctorCategories;
use App\Models\GlobalFunction;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AiController extends Controller
{
    public function ai()
    {
        $cats = DoctorCategories::where('is_deleted', 0)->get();
        return view('ai', ['cats' => $cats]);
    }

    function fetchQuestionsList(Request $request)
    {
        $totalData = Question::count();
        $rows = Question::orderBy('id', 'DESC')->get();

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
            $result = Question::with('choices')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Question::with('choices')
                ->Where('question', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Question::Where('question', 'LIKE', "%{$search}%")
                ->count();
        }
        
        $data = array();
        foreach ($result as $item) {
            $edit = '<a data-cat="' . $item->category_id . '" data-question="' . $item->question . '" data-choices="' . implode('|', $item->choices->pluck('choice')->toArray()) . '" href="" class="mr-2 btn btn-primary text-white edit" rel=' . $item->id . ' >' . __("Edit") . '</a>';
            $delete = '<a href="" class="mr-2 btn btn-danger text-white delete" rel=' . $item->id . ' >' . __("Delete") . '</a>';
            $action = $edit . $delete;
            $category = '<span class="badge bg-primary text-white">' . $item->category->title . '</span>';
            $data[] = array(
                $item->question,
                $category,
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

    function addQuestion(Request $request)
    {
        $rules = [
            'question' => 'required',
            'category_id' => 'required',
            'choices.0' => 'required',
            'choices.1' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        
        $question = new Question();
        $question->question = $request->question;
        $question->category_id = $request->category_id;
        $question->save();

        foreach ($request->choices as $choice) {
            if (!empty($choice)) {
                $question->choices()->create(['choice' => $choice]);
            }
        }

        return GlobalFunction::sendSimpleResponse(true, 'Question added successfully');
    }

    function editQuestion(Request $request)
    {
        $rules = [
            'question' => 'required',
            'category_id' => 'required',
            'choices.0' => 'required',
            'choices.1' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $question = Question::find($request->id);
        if (!$question) {
            return response()->json(['status' => false, 'message' => 'Question not found']);
        }

        $question->question = $request->question;
        $question->category_id = $request->category_id;
        $question->save();

        $question->choices()->delete();
        foreach ($request->choices as $choice) {
            if (!empty($choice)) {
                $question->choices()->create(['choice' => $choice]);
            }
        }

        return GlobalFunction::sendSimpleResponse(true, 'Question edited successfully');
    }

    function deleteQuestion($id)
    {
        $question = Question::find($id);
        $question->choices()->delete();
        $question->delete();
        return GlobalFunction::sendSimpleResponse(true, 'Faq deleted successfully');
    }

    function fetchQuestions(Request $request)
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

        $result = Question::with('choices')
            ->orderBy('id', 'DESC')
            ->offset($request->start)
            ->limit($request->count)
            ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }
}
