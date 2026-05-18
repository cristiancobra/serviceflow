<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $departments = Department::where('account_id', $user->account_id)
            ->orderBy('order')
            ->get();

        return DepartmentResource::collection($departments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'slug')->where('account_id', $user->account_id),
            ],
            'color' => 'required|string|max:7',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $validated['account_id'] = $user->account_id;

        $department = Department::create($validated);

        return (new DepartmentResource($department))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $user = $request->user();
        
        // Verifica se o departamento pertence à conta do usuário
        if ($department->account_id !== $user->account_id) {
            return response()->json([
                'message' => 'Acesso negado',
            ], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'slug')
                    ->ignore($department->id)
                    ->where('account_id', $user->account_id),
            ],
            'color' => 'sometimes|required|string|max:7',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $department->update($validated);

        return new DepartmentResource($department);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Department $department)
    {
        $user = $request->user();
        
        // Verifica se o departamento pertence à conta do usuário
        if ($department->account_id !== $user->account_id) {
            return response()->json([
                'message' => 'Acesso negado',
            ], 403);
        }
        
        // Verifica se há tarefas associadas
        if ($department->tasks()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir um departamento com tarefas associadas',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'message' => 'Departamento excluído com sucesso',
        ], 200);
    }
}
