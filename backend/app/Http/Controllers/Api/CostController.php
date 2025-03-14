<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Cost;
use App\Http\Resources\CostResource;
use App\Http\Requests\CostRequest;
use App\Http\Controllers\Controller;

class CostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $costs = Cost::where('account_id', auth()->user()->account_id)
        ->orderBy('name', 'asc')
        ->paginate(500);

    return CostResource::collection($costs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CostRequest $request)
    {
        try {
            $cost = new Cost;
            $cost->account_id = auth()->user()->account_id;
            $cost->fill($request->all());
            $cost->save();
            
            return new CostResource($cost);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cost  $cost
     * @return \Illuminate\Http\Response
     */
    public function show(Cost $cost)
    {
        return new CostResource($cost);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
