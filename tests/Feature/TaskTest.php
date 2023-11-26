<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Helpers\ResponseStatusCodes;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    // INDEX endpoints
    public function test_endpoint_index_returns_a_successful_response(): void
    {
        $task = $this->createTask();

        $user = $task->user;

        $response = $this->withBasicAuth($user->email, "password")
            ->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);
        $response->assertJsonCount(1, "data");
    }

    public function test_endpoint_index_returns_a_unauthenticated_response(): void
    {
        $task = $this->createTask();

        $user = $task->user;

        $response = $this->withBasicAuth($user->email, "empty")
            ->get(route('tasks.index'));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'data'      => null,
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    // STORE endpoints
    public function test_endpoint_store_returns_a_successful_response(): void
    {
        $user = $this->createUser(role: UserRole::Admin);

        $data = [
            'user_id'      => $user->id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->post(route('tasks.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);

        $this->assertDatabaseHas('tasks', [
            'user_id'       => $user->id,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'status'        => $data['status'],
        ]);
    }

    public function test_endpoint_store_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser();

        $data = [
            'user_id'      => $user->id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ];

        $response = $this->withBasicAuth($user->email, "empty")
            ->get(route('tasks.store'), $data);

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    public function test_endpoint_store_returns_a_validation_errors_response(): void
    {
        $user = $this->createUser();

        $data = [
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->post(route('tasks.store'), $data);

        $response->assertStatus(400);
        $response->assertJson(function(AssertableJson $json) {
            return $json->where('status', 'error')
                ->where('message',  ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['message'])
                ->where('code',     ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['code'])
                ->hasAll('data.user_id', 'data.title')
                ->etc();
        });
    }

    // SHOW endpoints
    public function test_endpoint_show_returns_a_successful_response(): void
    {
        $task = $this->createTask();
        $user = $task->user;

        $response = $this->withBasicAuth($user->email, "password")
            ->get(route('tasks.show', $task->id));

        $response->assertStatus(200);
        $response->assertJson(function(AssertableJson $json) use($task, $user){
            return $json->where('status', 'ok')
                ->where('message', "")
                ->where('code', 200)
                ->where('data.id', $task->id)
                ->where('data.user.id', $user->id)
                ->etc();
        });
    }

    public function test_endpoint_show_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser();

        $response = $this->withBasicAuth($user->email, "empty")
            ->get(route('tasks.show', 1));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'data'      => null,
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    public function test_endpoint_show_returns_a_unauthorized_response(): void
    {
        $task = $this->createTask();

        $otherUser = $this->createUser();
        Task::factory()->create([
            'user_id'       => $otherUser->id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ]);

        $response = $this->withBasicAuth($otherUser->email, "password")
            ->get(route('tasks.show', $task->id));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['code'],
        ]);
    }

    // UPDATE endpoints
    public function test_endpoint_update_returns_a_successful_response(): void
    {
        $task = $this->createTask();
        $user = $task->user;

        $data = [
            'user_id'       => $user->id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->put(route('tasks.update', $task->id), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);

        $this->assertDatabaseHas('tasks', [
            'id'            => $task->id,
            'user_id'       => $user->id,
            'title'         => $data['title'],
            'description'   => $data['description'],
        ]);
    }

    public function test_endpoint_update_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser();

        $response = $this->withBasicAuth($user->email, "empty")
            ->put(route('tasks.update', 1), []);

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'data'      => null,
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    public function test_endpoint_update_returns_a_unauthorized_response(): void
    {
        $task = $this->createTask();

        $data = [
            'user_id'       => $task->user_id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
        ];

        $otherUser = $this->createUser();
        Task::factory()->create([
            'user_id'       => $otherUser->id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ]);

        $response = $this->withBasicAuth($otherUser->email, "password")
            ->put(route('tasks.update', $task->id), $data);

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['code'],
        ]);
    }

    public function test_endpoint_update_returns_a_validation_errors_response(): void
    {
        $task = $this->createTask();
        $user = $task->user;

        $data = [
            'description' => fake()->text(),
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->put(route('tasks.update', $task->id), $data);

        $response->assertStatus(400);
        $response->assertJson(function(AssertableJson $json) {
            return $json->where('status', 'error')
                ->where('message',  ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['message'])
                ->where('code',     ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['code'])
                ->hasAll('data.user_id', 'data.title')
                ->etc();
        });
    }

    // DELETE endpoints
    public function test_endpoint_destroy_returns_a_successful_response(): void
    {
        $task = $this->createTask();
        $user = $task->user;

        $response = $this->withBasicAuth($user->email, "password")
            ->delete(route('tasks.destroy', $task->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_endpoint_destroy_returns_a_unauthenticated_response(): void
    {
        $task = $this->createTask();
        $user = $task->user;

        $response = $this->withBasicAuth($user->email, "empty")
            ->delete(route('tasks.destroy', $task->id));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'data'      => null,
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    public function test_endpoint_destroy_returns_a_unauthorized_response(): void
    {
        $task = $this->createTask();

        $otherUser = $this->createUser();
        Task::factory()->create([
            'user_id'       => $otherUser->id,
            'title'         => fake()->words(fake()->numberBetween(6, 20), true),
            'description'   => fake()->text(),
            'status'        => fake()->randomElement(TaskStatus::valuesAsArray()),
        ]);

        $response = $this->withBasicAuth($otherUser->email, "password")
            ->delete(route('tasks.destroy', $task->id));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['code'],
        ]);
    }

    /**
     * @param string|null $email
     * @param UserRole $role
     * @return User
     */
    private function createUser(?string $email = null, UserRole $role = UserRole::User): User{
        return User::factory()->create([
            'name'      => 'Jan Kowalski',
            'role'      => $role->value,
            'email'     => $email ?? fake()->email,
            'password'  => Hash::make('password'),
        ]);
    }

    private function createTask(): Task{
        return Task::factory()->create();
    }
}
