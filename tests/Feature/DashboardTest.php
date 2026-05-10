<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'USER']);
        $response = $this->actingAs($student, 'student')->get(route('admin.dashboard'));
        $response->assertRedirect();
    }

    public function test_admin_can_approve_request(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create(['role' => 'USER']);
        $book = Book::factory()->create(['status' => 'AVAILABLE']);
        $request = BookRequest::factory()->create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->withSession(['_token' => 'test'])
            ->post(route('admin.requests.approve', $request->id), [
                '_token' => 'test',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'status' => 'APPROVED',
        ]);
    }

    public function test_admin_can_mark_returned(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create(['role' => 'USER']);
        $book = Book::factory()->create(['status' => 'BORROWED']);
        $request = BookRequest::factory()->approved()->create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'status' => 'APPROVED',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->withSession(['_token' => 'test'])
            ->post(route('admin.requests.return', $request->id), [
                '_token' => 'test',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('requests', [
            'id' => $request->id,
            'status' => 'RETURNED',
        ]);
    }
}
