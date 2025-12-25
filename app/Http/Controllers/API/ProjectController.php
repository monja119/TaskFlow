<?php

namespace App\Http\Controllers\API;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\ProjectIndexRequest;
use App\Http\Resources\ProjectResource;
use App\DataTransferObjects\ProjectFilterData;
use App\Services\Project\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projects)
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ProjectIndexRequest $request)
    {
        $filters = ProjectFilterData::fromRequest($request);

        $projects = $this->projects->listForUser($filters, $request->user());

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projects->create($request->validated());

        return (new ProjectResource($project->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return new ProjectResource($project->load(['user', 'tasks']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $updated = $this->projects->update($project, $request->validated());

        return new ProjectResource($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
