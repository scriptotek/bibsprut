<?php

namespace App\Http\Controllers;

use App\EntityRelation;
use Illuminate\Http\Request;

class RelationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function json(Request $request)
    {
        $qb = EntityRelation::query();

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
        $qb = EntityRelation::query();
        $relations = $qb->get();

        return response()->view('relations.index', [
            'entityRelations' => $relations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('relations.edit', [
            'entityRelation' => new EntityRelation(),
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
        $relation = EntityRelation::create([
            'label' => $request->input('label'),
            'description' => $request->input('description'),
        ]);

        return redirect()->action('RelationController@show', $relation->id)
            ->with('status', 'Oppretta!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EntityRelation  $relation
     * @return \Illuminate\Http\Response
     */
    public function show(EntityRelation $relation)
    {
        return response()->view('relations.show', [
            'entityRelation' => $relation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EntityRelation  $relation
     * @return \Illuminate\Http\Response
     */
    public function edit(EntityRelation $relation)
    {
        return response()->view('relations.edit', [
            'entityRelation' => $relation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EntityRelation  $relation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EntityRelation $relation)
    {
        $relation->label = $request->input('label');
        $relation->description = $request->input('description');
        $relation->save();

        return redirect()->action('RelationController@show', $relation->id)
            ->with('status', 'Oppdatert!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EntityRelation  $relation
     * @return \Illuminate\Http\Response
     */
    public function destroy(EntityRelation $relation)
    {
        //
    }
}
