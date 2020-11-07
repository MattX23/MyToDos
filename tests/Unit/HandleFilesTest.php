<?php

namespace Tests\Unit;

use App\Http\Contracts\HandleFilesContract;
use App\ToDo;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function testStoreAttachment()
    {
        /** @var HandleFilesContract $handleFilesService */
        $handleFilesService = $this->app->make(HandleFilesContract::class);

        $file = UploadedFile::fake()->create('test_file.pdf');

        $handleFilesService->storeAttachment($file, $this->user->toDos->first());

        Storage::disk('local')->assertExists('public/attachments/'.md5('test_file.pdf'.$this->user->id).'.pdf');
    }

    public function testStoreImage()
    {
        /** @var HandleFilesContract $handleFilesService */
        $handleFilesService = $this->app->make(HandleFilesContract::class);

        $image = UploadedFile::fake()->create('test_image.jpg');

        $handleFilesService->storeImage($image, $this->user);

        Storage::disk('local')->assertExists('public/images/'.md5('test_image.jpg'.$this->user->id).'.jpg');
    }

    public function testRemoveFile()
    {
        /** @var HandleFilesContract $handleFilesService */
        $handleFilesService = $this->app->make(HandleFilesContract::class);

        $image = UploadedFile::fake()->create('test_image.jpg');

        $this->user->toDos->first()->update([
            'image' => '/images/test_image.jpg',
        ]);

        Storage::putFileAs('public/images/', $image, 'test_image.jpg');

        $handleFilesService->removeFile($this->user->toDos->first(), ToDo::IMAGE);

        Storage::disk('local')->assertMissing('public/images/test_image.jpg');
    }
}