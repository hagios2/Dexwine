<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testStoreTodo()
    {
        $payload = [
            'title' => $this->faker->title ,
            'details' => $this->faker->sentence,
        ];

        $response = $this->postJson('/api/todos', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'title',
                         'details',
                         'status',
                         'created_at',
                     ],
                 ]);

        Arr::set($payload, 'status', 'in progress');
        $response->assertJson([
            'message' => 'Created todo successfully',
            'data' => $payload
        ]);

        $this->assertDatabaseHas('todos', $payload);
    }

    public function testFetchTodos()
    {
        Todo::factory(5)->create();

        $response = $this->getJson('/api/todos');

        $response->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     '*' => [
                         'id',
                         'title',
                         'details',
                         'status',
                         'created_at',
                     ],
                 ],
                 'links',
                 'meta',
             ])
             ->assertJsonCount(5, 'data');

        $this->assertSame('fetched todo list', $response->json('message'));
    }

    #[DataProvider('statusData')] public function testCanFilterByStatus($count, $query_status, $missing_status1, $missing_status2)
    {
          Todo::factory($count)->create(['status' => $query_status]);
          Todo::factory(3)->create(['status' => $missing_status1]);
          Todo::factory(5)->create(['status' => $missing_status2]);

        $response = $this->getJson("/api/todos?status=$query_status");

        $response->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['status' => $query_status])
            ->assertJsonMissing(['status' => $missing_status1])
            ->assertJsonMissing(['status' => $missing_status2]);
    }

    public function testFilterByKeyword()
    {
         Todo::factory(3)->create();
         Todo::factory()->create([
            'title' => 'Learn Laravel API tests',
            'details' => 'Understand the basics of Laravel framework',
         ]);

         Todo::factory()->create([
            'title' => 'Learn Testing',
            'details' => 'Write unit and feature tests',
        ]);

        Todo::factory()->create([
            'title' => 'Build API',
            'details' => 'Build REST API with Laravel',
        ]);
        $response = $this->getJson('/api/todos?keyword=tests');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'title' => 'Learn Laravel API tests',
                'details' => 'Understand the basics of Laravel framework',
            ])
            ->assertJsonFragment([
                'title' => 'Learn Testing',
                'details' => 'Write unit and feature tests',
            ])
            ->assertJsonMissing([
                'title' => 'Build API',
                'details' => 'Build REST API with Laravel',
            ]);
    }
     public function testShowTodo()
    {
        $todo = Todo::factory()->create();

        $response = $this->getJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Fetched Todo successfully',
                 'data' => [
                     'id' => $todo->id,
                     'title' => $todo->title,
                     'details' => $todo->details,
                     'status' => $todo->status,
                 ],
             ]);
    }

    public function testUpdateTodo()
    {
        $todo = Todo::factory()->create(['status' => 'in progress']);

        $payload = [
            'title' => $this->faker->title ,
            'details' => $this->faker->sentence,
            'status' => 'completed'
        ];

        $response = $this->putJson("/api/todos/{$todo->id}", $payload);

        //add id to payload
        Arr::set($payload, 'id', $todo->id);

        //assert payload is return
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Todo updated successfully',
                     'data' => $payload,
                 ]);

        $this->assertDatabaseHas('todos', $payload);
    }

    public function testDestroyTodo()
    {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson("/api/todos/{$todo->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Todo deleted successfully',
                     'data' => [],
                 ]);

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public static function statusData(): array
    {
        return [
            [
                'count' => 2,
                'query_status' => 'completed',
                'missing_status1' => 'in progress',
                'missing_status2' => 'not started',
            ],
            [
                'count' => 3,
                'query_status' => 'in progress',
                'missing_status1' => 'completed',
                'missing_status2' => 'not started',
            ],
            [
                'count' => 1,
                'query_status' => 'not started',
                'missing_status1' => 'completed',
                'missing_status2' => 'in progress',
            ],
        ];
    }
}
