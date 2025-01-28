<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Link;
use App\Http\Resources\LinksResource;
use App\Http\Requests\LinkRequest;
use Illuminate\Validation\ValidationException;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = Link::with([
            'project',
            'opportunity'
        ])
            ->where('account_id', auth()->user()->account_id)
            ->orderBy('date_due', 'desc')
            ->paginate(500);

        return LinksResource::collection($links);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LinkRequest $request;
     * @return \Illuminate\Http\Response
     */
    public function store(LinkRequest $request)
    {
        try {
            $link = new Link;
            $link->fill($request->validated());

            $link->save();

            return LinksResource::make($link->load('task', 'opportunity'));
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function show(Link $link)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Link $link)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Link  $link
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
        
        $link->delete();

        return response()->json([
            'message' => "Link excluído com sucesso",
        ], 200);
    }
}
