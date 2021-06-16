<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Models\User;
use App\Models\FileGroup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

$factory->define(App\Models\File::class, function (Faker $faker) {
    $storage = Storage::fake('local');
    $file = UploadedFile::fake()->image($faker->firstName.".jpg");
    $filePath = $file->store('local');
    $user = factory(User::class)->create();
    $fileGroup = factory(FileGroup::class)->create();
    return [
      'name' => config('filesystems.disks.local.root').'/'.$faker->uuid . '.jpg',
      'uploaded_by' => $user->id,
      'file_group_id' => $fileGroup->id,
      'file_type' => Storage::disk('local')->getMimetype($filePath),
      'extension' => pathinfo(config('filesystems.disks.local.root').'/'.$filePath, PATHINFO_EXTENSION),
      'size' => Storage::disk('local')->getSize($filePath),
      'path' => $filePath,
    ];
});
