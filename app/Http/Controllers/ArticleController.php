<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\GlobalFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    function articles()
    {
        $items = Article::with('interests')
            ->select('id', 'title', 'image', 'content', 'created_at')
            ->orderBy('id', 'DESC')
            ->paginate(10); // هنا خليتها 10 عناصر في الصفحة

        return view('articles', compact('items'));
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
            'interests' => 'required|array',
        ];

        $request->validate($rules);

        $path = GlobalFunction::saveFileAndGivePath($request->image);

        $article = new Article();
        $article->title = $request->title;
        $article->content = $request->content;
        $article->image = $path;
        $article->save();

        $article->interests()->sync($request->interests);

        return redirect()->route('articles')->with('success', 'Article added successfully!');
    }

    function editArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $article->title = $request->title;
        $article->content = $request->content;

        if ($request->hasFile('image')) {
            GlobalFunction::deleteFile($article->image);
            $path = GlobalFunction::saveFileAndGivePath($request->image);
            $article->image = $path;
        }

        $article->save();

        if ($request->has('interests')) {
            $article->interests()->sync($request->interests);
        }

        return redirect()->route('articles')->with('success', 'Article updated successfully!');
    }

    function deletearticle($id)
    {
        $article = Article::find($id);
        GlobalFunction::deleteFile($article->image);
        $article->delete();
        return back();
    }

    function fetchArticles(Request $request)
    {
        $rules = [
            'start' => 'required|integer|min:0',
            'count' => 'required|integer|min:1|max:100',
            'user_id' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $userId = $request->user_id;

        // Get user's interest IDs
        $userInterests = DB::table('user_interests')
            ->where('user_id', $userId)
            ->pluck('interest_id')
            ->toArray();

        // Build the query
        $query = Article::with('interests');

        // If user has interests, prioritize matching articles
        if (!empty($userInterests)) {
            // Get article IDs that match user's interests
            $matchingArticleIds = DB::table('article_interests')
                ->whereIn('interest_id', $userInterests)
                ->whereNotNull('article_id')
                ->distinct()
                ->pluck('article_id')
                ->toArray();

            if (!empty($matchingArticleIds)) {
                // Order by: matching articles first (using FIELD), then by newest
                $query->orderByRaw('FIELD(id, ' . implode(',', $matchingArticleIds) . ') DESC');
            }
        }

        // Always order by newest as secondary sort
        $result = $query
            ->orderBy('id', 'DESC')
            ->offset($request->start)
            ->limit($request->count)
            ->get();

        return GlobalFunction::sendDataResponse(true, 'data fetched successfully', $result);
    }
}
