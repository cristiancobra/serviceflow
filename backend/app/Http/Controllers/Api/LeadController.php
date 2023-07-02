<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Http\Resources\LeadsResource;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return LeadsResource::collection(Lead::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lead = new Lead;
		$lead->account_id = 1;
		$lead->user_id = 1;
		$lead->name = $request->name;
        $lead->email = $request->email;
        $lead->cel_phone = $request->cel_phone;
        $lead->comments = $request->comments;
        $lead->save();
		
        return response()->json([
            'message' => "Contato $request->name criado",
			'id' => $lead->id,
			'name' => $lead->name,
            'email' => $lead->email,
            'cel_phone' => $lead->cel_phone,
			'comments' => $lead->comments,
			
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);

        $lead->name = $request->input('name');
        $lead->email = $request->input('email');
        $lead->cel_phone = $request->input('cel_phone');
        $lead->save();

        return response()->json([
            'message' => "Contato $request->name atualizado",
			'id' => $lead->id,
			'name' => $lead->name,
            'email' => $lead->email,
            'cel_phone' => $lead->cel_phone,
			
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }
}
