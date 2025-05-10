<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'object_id',
        'object_type',
        'type',
        'name',
        'path',
        'size',
        'width',
        'height',
        'mime_type',
    ];

    public function object(): MorphTo
    {
        return $this->morphTo('object', 'object_type', 'object_id');
    }

    public function getFullPathAttribute()
    {
        return Storage::url($this->path);
    }

    public function getFileAttribute()
    {
        return Storage::get($this->full_path);
    }

    public static function storeImage($file, int $objectId, string $objectType, string $type, $disk = 'public')
    {
        return DB::transaction(function () use ($file, $objectId, $objectType, $type, $disk) {
            $path = sprintf('%s/%s/%s/%s/%s', date('Y'), date('m'), date('d'), $type, $objectId);
            $path = Storage::disk($disk)->putFile($path, $file);

            return self::create([
                'object_id' => $objectId,
                'object_type' => $objectType,
                'type' => $type,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'width' => getimagesize($file)[0],
                'height' => getimagesize($file)[1],
                'mime_type' => $file->getClientMimeType(),
            ]);
        });
    }

    public static function deleteImage(Collection|array|int|self $images)
    {
        DB::transaction(function () use ($images) {
            if ($images instanceof self) {
                $imageIds = [$images->id];
            } else if ($images instanceof Collection) {
                $imageIds = $images->pluck('id');
            } else if (!is_array($images)) {
                $imageIds = [$images];
            }

            $images = self::whereIn('id', $imageIds)->get();

            foreach ($images as $image) {
                Storage::delete($image->path);
                $image->delete();
            }
        });
    }
}
