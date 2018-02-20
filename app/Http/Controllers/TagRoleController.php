<?php

namespace App\Http\Controllers;

use App\TagRole;
use Illuminate\Http\Request;

class TagRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function json(Request $request)
    {
        $qb = TagRole::query();

        if ($request->input('q')) {
            $query = '%' . $request->input('q') . '%';
            $qb->where('label', 'ILIKE', $query);
        }

        $relations = $qb->get();

        return response()->json($relations);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qb = TagRole::query();
        $relations = $qb->get();

        return response()->view('tag-roles.index', [
            'tagRoles' => $relations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('tag-roles.edit', [
            'tagRole' => new TagRole(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $relation = TagRole::create([
            'label' => $request->input('label'),
            'description' => $request->input('description'),
        ]);

        return redirect()->action('TagRoleController@show', $relation->id)
            ->with('status', 'Oppretta!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TagRole  $relation
     * @return \Illuminate\Http\Response
     */
    public function show(TagRole $relation)
    {
        return response()->view('tag-roles.show', [
            'tagRole' => $relation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TagRole  $relation
     * @return \Illuminate\Http\Response
     */
    public function edit(TagRole $relation)
    {
        return response()->view('tag-roles.edit', [
            'tagRole' => $relation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TagRole  $relation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TagRole $relation)
    {
        $relation->label = $request->input('label');
        $relation->description = $request->input('description');
        $relation->save();

        return redirect()->action('TagRoleController@show', $relation->id)
            ->with('status', 'Oppdatert!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TagRole  $relation
     * @return \Illuminate\Http\Response
     */
    public function destroy(TagRole $relation)
    {
        //
    }
}
