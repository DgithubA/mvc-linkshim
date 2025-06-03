<?php

namespace Lms\app\Http\Controllers\api;


use Lms\app\Http\Controllers\baseController;
use Lms\app\Models\Course;
use Lms\Core\Request;
use Lms\Core\Response;

class articlesApiController extends baseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->has('offset')){
            $offset = $request->input('offset');
            if(!is_numeric($offset) or $offset < 0){
                Response::error('offset must be int');
            }
        }
        if ($request->has('limit')) {
            $limit = $request->input('limit');
            if(!is_numeric($limit) or $limit < 0){
                Response::error('limit must be int');
            }
            if ($limit > 30) {
                Response::error('limit must be letter than 30');
            }
        }
        Course::all(offset: $offset ?? null,limit: $limit ?? null);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
