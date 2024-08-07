<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Http\Resources\ProposalResource;
use App\Http\Requests\ProposalRequest;

class ProposalController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProposalRequest $request)
    {
        try {
            $proposal = new Proposal;
            $proposal->fill($request->all());
            $proposal->save();

            return ProposalResource::make($proposal);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Display a listing of the resource by proposal_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProposalsByOpportunityId(Request  $request)
    {
        $perPage = request()->get('per_page', 20);

        $proposals = Proposal::where('opportunity_id', $request->opportunity_id)
            ->orderBy('date_start', 'desc')
            ->paginate($perPage);

        $proposals->appends(['opportunity_id' => $request->opportunity_id]);

        return ProposalResource::collection($proposals);
    }
}
