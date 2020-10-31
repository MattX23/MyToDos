<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;
use Tests\Traits\TestSetupTrait;

class ToDoTest extends TestCase
{
    use RefreshDatabase;
    use TestSetupTrait;
    use MatchesSnapshots;

    public function setUp(): void
    {
        parent::setUp();

        $this
            ->getTestUser()
            ->addToDos(10, true);
    }

    public function testToDosAreOrderedByDueDate()
    {
        $dates = $this->user->toDos->pluck('due_date')->toArray();

        $isOrdered = true;

        for ($i = 1; $i < sizeof($dates); $i++) {
            if ($dates[$i - 1] > $dates[$i]) $isOrdered = false;
        }

        $this->assertTrue($isOrdered);
    }

    public function testGetUsersToDos()
    {
        $response = $this->get( '/api/get-to-dos/'.$this->user->id);

        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testStoreToDo()
    {
        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title' => 'Fresh To Do',
            'body'  => 'This is a fresh To Do',
        ]);

        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testStoreToDoWithMissingData()
    {
        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'body'  => 'This is a fresh To Do',
        ]);

        $response
            ->assertSessionHasErrors('title')
            ->assertStatus(Response::HTTP_FOUND);;
    }

    public function testStoreToDoWithAttachment()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('file.pdf');

        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title'      => 'To Do with Attachment',
            'attachment' => $file
        ]);

        $response->assertOk();
    }

    public function testStoreToDoWithOversizedAttachment()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('file.pdf')->size(5000);

        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title'      => 'To Do with Attachment',
            'attachment' => $file
        ]);

        $response
            ->assertSessionHasErrors('attachment')
            ->assertStatus(302);
    }

    public function testStoreToDoWithImage()
    {
        Storage::fake('local');
        $image = UploadedFile::fake()->create('image.jpg');

        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title' => 'To Do with Image',
            'image' => $image
        ]);

        $response->assertOk();
    }

    public function testStoreToDoWithOversizedImage()
    {
        Storage::fake('local');
        $image = UploadedFile::fake()->create('image.jpg')->size(5000);

        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title' => 'To Do with Image',
            'image' => $image
        ]);

        $response
            ->assertSessionHasErrors('image')
            ->assertStatus(302);
    }

    public function testGetReminderDays()
    {
        $response = $this->get( '/api/get-reminder-days');

        $this->assertMatchesJsonSnapshot($response->getContent());
    }
}
