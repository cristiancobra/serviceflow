<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectsResource;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProjectsResource::collection(Project::all());
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
		$project = new Project;
		$project->account_id = 1;
		$project->user_id = 1;
		$project->contact_id = 1;
		$project->company_id = 1;
		$project->goal_id = $request->goal_id;
		$project->name = $request->name;
		$project->category = $request->category;
		$project->date_start = '2021-11-15 00:00:00';
		$project->date_due = '2021-11-19 00:00:00';
		$project->date_conclusion = $request->date_conclusion;
		$project->description = $request->description;
		$project->status = $request->status;
		$project->trash = 0;
        $project->save();
		
        return response()->json([
            'message' => "Projeto $request->name criado",
			'id' => $project->id,
			'name' => $project->name,
			'description' => $project->description,
			
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
		return new ProjectResource(Project::find($project->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        //
    }
	
	/**
	 * Retorna os valores possíveis para SITUAÇÃO / STATUS 
	 */
	public function getProjectsStatus() {
		return $status = Project::getStatus();
	}
	
}
