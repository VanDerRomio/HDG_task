<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Helpers\ResponseStatusCodes;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // INDEX endpoints
    public function test_endpoint_index_returns_a_successful_response(): void
    {
        $user = $this->createUser(role: UserRole::Admin);

        $response = $this->withBasicAuth($user->email, "password")
            ->get(route('users.index'));

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
        $user = $this->createUser();

        $response = $this->withBasicAuth($user->email, "empty")
            ->get(route('users.index'));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'data'      => null,
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    public function test_endpoint_index_returns_a_unauthorized_response(): void
    {
        $user = $this->createUser();

        $response = $this->withBasicAuth($user->email, "password")
            ->get(route('users.index'));

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'data'      => null,
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['code'],
        ]);
    }

    // STORE endpoints
    public function test_endpoint_store_returns_a_successful_response(): void
    {
        $user = $this->createUser(role: UserRole::Admin);

        $data = [
            'name'      => 'Jan Kowalski',
            'role'      => UserRole::User->value,
            'email'     => "jan.kowalski@gmail.com",
            'password'  => 'password',
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->post(route('users.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => $data['name'],
            'role'  => $data['role'],
            'email' => $data['email'],
        ]);
    }

    public function test_endpoint_store_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser(role: UserRole::Admin);

        $data = [
            'name'      => 'Jan Kowalski',
            'role'      => UserRole::User->value,
            'email'     => "jan.kowalski@gmail.com",
            'password'  => 'password',
        ];

        $response = $this->withBasicAuth($user->email, "empty")
            ->get(route('users.store'), $data);

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1001['code'],
        ]);
    }

    public function test_endpoint_store_returns_a_unauthorized_response(): void
    {
        $user = $this->createUser();

        $data = [
            'name'      => 'Jan Kowalski',
            'role'      => UserRole::User->value,
            'email'     => "jan.kowalski@gmail.com",
            'password'  => 'password',
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->post(route('users.store'), $data);

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['code'],
        ]);
    }

    public function test_endpoint_store_returns_a_validation_errors_response(): void
    {
        $user = $this->createUser();

        $data = [
            'name'      => 'Jan Kowalski',
            'role'      => "unknown",
            'email'     => $user->email,
            'password'  => 'password',
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->post(route('users.store'), $data);

        $response->assertStatus(400);
        $response->assertJson(function(AssertableJson $json) {
            return $json->where('status', 'error')
                ->where('message',  ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['message'])
                ->where('code',     ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['code'])
                ->hasAll('data.email', 'data.role')
                ->etc();
        });
    }

    // SHOW endpoints
    public function test_endpoint_show_returns_a_successful_response(): void
    {
        $user = $this->createUser();

        $response = $this->withBasicAuth($user->email, "password")
            ->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertJson(function(AssertableJson $json) use($user){
            return $json->where('status', 'ok')
                ->where('message', "")
                ->where('code', 200)
                ->where('data.id', $user->id)
                ->where('data.email', $user->email)
                ->etc();
        });
    }

    public function test_endpoint_show_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser();

        $response = $this->withBasicAuth($user->email, "empty")
            ->get(route('users.show', $user->id));

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
        $userAsUser     = $this->createUser();
        $userAsAdmin    = $this->createUser(role: UserRole::Admin);

        $response = $this->withBasicAuth($userAsUser->email, "password")
            ->get(route('users.show', $userAsAdmin->id));

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
        $user = $this->createUser();

        $data = [
            'name'  => 'Jon Kowalski',
            'email' => "new_" . $user->email,
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->put(route('users.update', $user->id), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public function test_endpoint_update_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser();

        $data = [
            'name'  => 'Jon Kowalski',
            'email' => "new_" . $user->email,
        ];

        $response = $this->withBasicAuth($user->email, "empty")
            ->put(route('users.update', $user->id), $data);

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
        $userAsUser     = $this->createUser();
        $userAsAdmin    = $this->createUser(role: UserRole::Admin);

        $data = [
            'name'  => 'Jon Kowalski',
            'email' => "new_" . $userAsAdmin->email,
        ];

        $response = $this->withBasicAuth($userAsUser->email, "password")
            ->put(route('users.update', $userAsAdmin->id), $data);

        $response->assertStatus(400);
        $response->assertJson([
            'status'    => 'error',
            'message'   => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['message'],
            'code'      => ResponseStatusCodes::RESPONSE_STATUS_CODE_1003['code'],
        ]);
    }

    public function test_endpoint_update_returns_a_validation_errors_response(): void
    {
        $user = $this->createUser();

        $data = [
            'role' => 'unknown',
        ];

        $response = $this->withBasicAuth($user->email, "password")
            ->put(route('users.update', $user->id), $data);

        $response->assertStatus(400);
        $response->assertJson(function(AssertableJson $json) {
            return $json->where('status', 'error')
                ->where('message',  ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['message'])
                ->where('code',     ResponseStatusCodes::RESPONSE_STATUS_CODE_1008['code'])
                ->hasAll('data.name', 'data.email', 'data.role')
                ->etc();
        });
    }

    // DELETE endpoints
    public function test_endpoint_destroy_returns_a_successful_response(): void
    {
        $user = $this->createUser(role: UserRole::Admin);

        $response = $this->withBasicAuth($user->email, "password")
            ->delete(route('users.destroy', $user->id));

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 'ok',
            'message'   => "",
            'code'      => 200,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_endpoint_destroy_returns_a_unauthenticated_response(): void
    {
        $user = $this->createUser(role: UserRole::Admin);

        $response = $this->withBasicAuth($user->email, "empty")
            ->delete(route('users.destroy', $user->id));

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
        $userAsUser     = $this->createUser();
        $userAsAdmin    = $this->createUser(role: UserRole::Admin);

        $response = $this->withBasicAuth($userAsUser->email, "password")
            ->delete(route('users.destroy', $userAsAdmin->id));

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
}
