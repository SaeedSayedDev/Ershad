<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GlobalFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    function articles()
    {
        return view('articles');
        //articles.blade.php
    }

    
    function fetchArticlesList(Request $request)
    {
        $totalData = Article::count();
        $rows = Article::orderBy('id', 'DESC')->get();
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
            $result = Article::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Article::where(function ($query) use ($search) {
                $query->Where('title', 'LIKE', "%{$search}%");
            })->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Article::where(function ($query) use ($search) {
                $query->Where('title', 'LIKE', "%{$search}%");
            })->count();
        }

        $data = array();
        foreach ($result as $item) {
            $edit = '<a data-heading="' . $item->image . '" data-title="' . $item->title . '" data-content="' . $item->content . '" href="" class="mr-2 btn btn-primary text-white edit" rel=' . $item->id . ' >' . __("Edit") . '</a>';
            $delete = '<a href="" class="mr-2 btn btn-danger text-white delete" rel=' . $item->id . ' >' . __("Delete") . '</a>';
            $action = $edit  . $delete;

            $imgUrl = GlobalFunction::createMediaUrl($item->image);
            $image = '<img src="' . $imgUrl . '" width="50" height="50">';

            $data[] = array(
                $item->title,
                $image,
                $item->created_at->diffForHumans(),
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

    function addArticle(Request $request)
    {
        $rules = [
            'title' => 'required',
            'content' => 'required',
            'image' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $path = GlobalFunction::saveFileAndGivePath($request->image);

        $article = new Article();
        $article->title = $request->title;
        $article->content = $request->content;
        $article->image = $path;
        $article->save();

        return GlobalFunction::sendSimpleResponse(true, 'coupon added successfully!');
    }

    function editArticle(Request $request)
    {
        $article = Article::find($request->id);
        $article->title = $request->title;
        $article->content = $request->content;

        if ($request->hasFile('image')) {
            GlobalFunction::deleteFile($article->image);
            $path = GlobalFunction::saveFileAndGivePath($request->image);
            $article->image = $path;
        }

        $article->save();

        return GlobalFunction::sendSimpleResponse(true, 'coupon edited successfully!');
    }

    function deletearticle($id)
    {
        $article = Article::find($id);
        GlobalFunction::deleteFile($article->image);
        $article->delete();
        return GlobalFunction::sendSimpleResponse(true, 'coupon deleted successfully!');
    }

    function fetchArticles(Request $request)
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

        $result = Article::orderBy('id', 'DESC')
            ->offset($request->start)
            ->limit($request->count)
            ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }
}
