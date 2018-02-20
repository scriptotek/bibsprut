<?php

namespace App\Http\Controllers;

use App\Entity;
use App\EntityRelation;
use Illuminate\Http\Request;

class EntitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $qb = Entity::query()->with('videos');
        $entities = $qb->orderBy('entity_type')->get();
        $entity_types = [];

        foreach ($entities as $entity) {
            if (!isset($entity_types[$entity->entity_type])) {
                $entity_types[$entity->entity_type] = [];
            }
            $entity_types[$entity->entity_type][] = $entity;
        }

        return response()->view('entities.index', [
            'entity_types' => $entity_types,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function json(Request $request)
    {
        $qb = Entity::query();

        if ($request->input('q')) {
            $query = '%' . $request->input('q') . '%';
            $qb->where('entity_label', 'ILIKE', $query);
        }

        $entities = $qb->get()->map(function(Entity $item) {
            return $item->simpleRepresentation();
        });

        return response()->json($entities);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function show(Entity $entity)
    {
        // $entity = Entity::withTrashed()->find($id);

        return response()->view('entities.show', [
            'entity' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function edit(Entity $entity)
    {
        $entityTypes = \DB::table('entities')->select('entity_type')->distinct()->whereNotNull('entity_type')->get()->map(function($item) {
            return $item->entity_type;
        });

        // $entityTypes = EntityRelation::query()->get()->map(function($item) {
        //     return $item->label;
        // });

        return response()->view('entities.edit', [
            'entity' => $entity,
            'entityTypes' => $entityTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entity  $entity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Entity $entity)
    {
        $entity->entity_type = $request->entity_type;
        $entity->entity_label = $request->entity_label;

        // TODO: search-replace all videos

        $entity->save();

        return redirect()->action('EntitiesController@show', $entity->id)
            ->with('status', 'Oppdatert!');
    }

}
