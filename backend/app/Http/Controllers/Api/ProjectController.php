<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::orderBy('date_due', 'asc')
            ->paginate(50);

        return ProjectResource::collection($projects);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectRequest $request)
    {
        try {
            $project = Project::create($request->validated());
            $project->save();

            return ProjectResource::make($project);
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
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        try {
            $projectWithRelations = Project::with([
                'tasks' => function ($query) {
                    $query->orderByRaw('date_conclusion IS NOT NULL ASC')
                        ->orderBy('date_start', 'desc')
                        ->with('journeys');
                },
            ])->find($project->id);
    
            if (!$projectWithRelations) {
                return response()->json(['error' => 'Project not found'], 404);
            }
    
            return ProjectResource::make($projectWithRelations);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the project.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, Project $project)
    {
        try {
            $project->fill($request->validated());
            $project->save();

            return ProjectResource::make($project);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return response()->json([
                'message' => 'Projeto deletado com sucesso',
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Retorna os valores possíveis para SITUAÇÃO / STATUS 
     */
    public function getProjectsStatus()
    {
        return $status = Project::getStatus();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function prioritizedProjects()
    {
        $projects = Project::where('date_conclusion', null)
            ->orderBy('date_due', 'asc')
            ->paginate(20);

        return ProjectResource::collection(
            $projects
        );
    }
}
