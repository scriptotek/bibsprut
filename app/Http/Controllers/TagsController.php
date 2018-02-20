<?php

namespace App\Http\Controllers;

use App\Tag;
use App\TagRole;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $qb = Tag::query()->with('videos');
        $entities = $qb->orderBy('tag_type')->get();
        $entity_types = [];

        foreach ($entities as $entity) {
            if (!isset($entity_types[$entity->tag_type])) {
                $entity_types[$entity->tag_type] = [];
            }
            $entity_types[$entity->tag_type][] = $entity;
        }

        return response()->view('tags.index', [
            'tag_types' => $entity_types,
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
        $qb = Tag::query();

        if ($request->input('q')) {
            $query = '%' . $request->input('q') . '%';
            $qb->where('tag_name', 'ILIKE', $query);
        }

        $entities = $qb->get()->map(function($item) {
            return $item->simpleRepresentation();
        });

        return response()->json($entities);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $entity
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $entity)
    {
        // $entity = Tag::withTrashed()->find($id);

        return response()->view('tags.show', [
            'tag' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tag  $entity
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $entity)
    {
        $entityTypes = \DB::table('tags')->select('tag_type')->distinct()->whereNotNull('tag_type')->get()->map(function($item) {
            return $item->tag_type;
        });

        // $entityTypes = TagRole::query()->get()->map(function($item) {
        //     return $item->label;
        // });

        return response()->view('tags.edit', [
            'tag' => $entity,
            'tagTypes' => $entityTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $entity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $entity)
    {
        $entity->tag_type = $request->tag_type;
        $entity->tag_name = $request->tag_name;

        // TODO: search-replace all videos

        $entity->save();

        return redirect()->action('TagsController@show', $entity->id)
            ->with('status', 'Oppdatert!');
    }

}
