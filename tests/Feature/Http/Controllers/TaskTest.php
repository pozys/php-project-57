<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Task;
use Tests\ControllerTestCase;

class TaskTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->user);
    }
    public function testIndex(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
        $response->assertSee(__('task.id'));
        $response->assertSee(__('task.status'));
        $response->assertSee(__('task.name'));
        $response->assertSee(__('task.created_by'));
        $response->assertSee(__('task.assigned_to'));
        $response->assertSee(__('task.created_at'));
        $response->assertSee(__('task.index.actions'));
    }

    public function testCreate(): void
    {
        $task = Task::factory()->make();
        $taskData = $task->only($task->getFillable());
        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function testUpdate(): void
    {
        $task = $this->createTask();
        $taskParams = $task->only($task->getFillable());
        $taskParams['name'] = $this->faker->name;

        $response = $this->put(route('tasks.update', compact('task')), $taskParams);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $taskParams));
    }

    public function testDestroy(): void
    {
        $task = $this->createTask();

        $response = $this->delete(
            route('tasks.destroy', compact('task'))
        );

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect();

        $this->assertSoftDeleted($task);
    }

    private function createTask(): Task
    {
        return Task::factory()->create([
            'created_by_id' => $this->user->id,
        ]);
    }
}
