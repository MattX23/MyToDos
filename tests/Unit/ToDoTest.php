<?php

namespace Tests\Unit;

use App\ToDo;
use Carbon\Carbon;
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

        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testAttachmentIsUploaded()
    {
        Storage::fake('local');

        $this->json('POST', '/api/store-to-do/'.$this->user->id, [
            'title'      => 'Test To Do',
            'attachment' => UploadedFile::fake()->create('file.pdf')
        ]);

        Storage::disk('local')->assertExists('public/attachments/'.md5('file.pdf'.$this->user->id).'.pdf');
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

        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testImageIsUploaded()
    {
        Storage::fake('local');

        $this->json('POST', '/api/store-to-do/'.$this->user->id, [
            'title' => 'Test To Do',
            'image' => UploadedFile::fake()->create('image.png')
        ]);

        Storage::disk('local')->assertExists('public/images/'.md5('image.png'.$this->user->id).'.png');
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

    public function testStoreWithDueDateInPast()
    {
        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title'   => 'To Do with Due Date',
            'dueDate' => Carbon::now()->subDays(1)->format('Y-m-d'),
        ]);

        $response
            ->assertSessionHasErrors('dueDate')
            ->assertStatus(302);
    }

    public function testStoreWithReminderBeforeDueDate()
    {
        $response = $this->post( '/api/store-to-do/'.$this->user->id, [
            'title'    => 'To Do with Reminder',
            'dueDate'  => Carbon::now()->addDays(2)->format('Y-m-d'),
            'remindAt' => 1,
        ]);

        $response
            ->assertSessionHasErrors( 'remindAt')
            ->assertStatus(302);
    }

    public function testEditToDo()
    {
        $toDo = ToDo::create([
            'user_id' => $this->user->id,
            'title'   => 'Original title',
            'body'    => 'The original To Do body',
        ]);

        $response = $this->put( '/api/edit-to-do/'.$toDo->id.'/'.$this->user->id, [
            'title'    => 'A new To Do Title',
            'body'     => 'A new To Do body',
        ]);

        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testUserCannotEditAnotherUsersToDo()
    {
        $newUser = $this->getNewTestUser();

        $toDo = ToDo::create([
            'user_id' => $newUser->id,
            'title'   => 'New Users To Do',
        ]);

        $response = $this->put( '/api/edit-to-do/'.$toDo->id.'/'.$this->user->id, [
            'title'    => 'A new To Do Title',
            'body'     => 'A new To Do body',
        ]);

        $response->assertStatus(403);
    }

    public function testImageIsRemovedWhenNewImageUploaded()
    {
        Storage::fake('local');

        $this->json('POST', '/api/store-to-do/'.$this->user->id, [
            'title' => 'Test Image Removal To Do',
            'image' => UploadedFile::fake()->create('image.png')
        ]);

        $toDo = ToDo::where('title', '=', 'Test Image Removal To Do')->first();

        $this->json('PUT', '/api/edit-to-do/'.$toDo->id.'/'.$this->user->id, [
            'title' => 'Test To Do',
            'image' => UploadedFile::fake()->create('image2.png')
        ]);

        Storage::disk('local')->assertMissing('public/images/'.md5('image.png'.$this->user->id).'.png');
    }

    public function testImageIsRemovedWhenWhenUserDeletesImage()
    {
        Storage::fake('local');

        $this->json('POST', '/api/store-to-do/'.$this->user->id, [
            'title' => 'Test Image Removal To Do',
            'image' => UploadedFile::fake()->create('image.jpg')
        ]);

        $toDo = ToDo::where('title', '=', 'Test Image Removal To Do')->first();

        $this->json('PUT', '/api/edit-to-do/'.$toDo->id.'/'.$this->user->id, [
            'title'       => 'Test To Do',
            'deleteImage' => true
        ]);

        Storage::disk('local')->assertMissing('public/attachments/'.md5('image.jpg'.$this->user->id).'.jpg');
    }

    public function testAttachmentIsRemovedWhenNewAttachmentUploaded()
    {
        Storage::fake('local');

        $this->json('POST', '/api/store-to-do/'.$this->user->id, [
            'title'      => 'Test Attachment Removal To Do',
            'attachment' => UploadedFile::fake()->create('file.pdf')
        ]);

        $toDo = ToDo::where('title', '=', 'Test Attachment Removal To Do')->first();

        $this->json('PUT', '/api/edit-to-do/'.$toDo->id.'/'.$this->user->id, [
            'title'      => 'Test To Do',
            'attachment' => UploadedFile::fake()->create('file2.pdf')
        ]);

        Storage::disk('local')->assertMissing('public/attachments/'.md5('file.pdf'.$this->user->id).'.pdf');
    }

    public function testAttachmentIsRemovedWhenWhenUserDeletesAttachment()
    {
        Storage::fake('local');

        $this->json('POST', '/api/store-to-do/'.$this->user->id, [
            'title'      => 'Test Attachment Removal To Do',
            'attachment' => UploadedFile::fake()->create('file.pdf')
        ]);

        $toDo = ToDo::where('title', '=', 'Test Attachment Removal To Do')->first();

        $this->json('PUT', '/api/edit-to-do/'.$toDo->id.'/'.$this->user->id, [
            'title'            => 'Test To Do',
            'deleteAttachment' => true
        ]);

        Storage::disk('local')->assertMissing('public/attachments/'.md5('file.pdf'.$this->user->id).'.pdf');
    }

    public function testDeleteToDo()
    {
        $toDo = ToDo::first();

        $response = $this->delete( '/api/delete-to-do/'.$toDo->id.'/'.$this->user->id);

        $this->assertMatchesJsonSnapshot($response->getContent());
    }

    public function testUserCannotDeleteAnotherUsersToDo()
    {
        $newUser = $this->getNewTestUser();

        $toDo = ToDo::create([
            'user_id' => $newUser->id,
            'title'   => 'New Users To Do',
        ]);

        $response = $this->delete( '/api/delete-to-do/'.$toDo->id.'/'.$this->user->id);

        $response->assertStatus(403);
    }

    public function testMarkToDoAsComplete()
    {
        $toDo = ToDo::where('is_complete', '=', false)->first();

        $this->post( '/api/toggle-to-do/'.$toDo->id.'/'.$this->user->id, [
            'complete' => true,
        ]);

        $toDo->refresh();

        $this->assertEquals(true, $toDo->is_complete);
    }

    public function testMarkToDoAsIncomplete()
    {
        $toDo = ToDo::where('is_complete', '=', true)->first();

        $this->post( '/api/toggle-to-do/'.$toDo->id.'/'.$this->user->id, [
            'complete' => false,
        ]);

        $toDo->refresh();

        $this->assertEquals(false, $toDo->is_complete);
    }
}
