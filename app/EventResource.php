<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventResource extends Model
{
    protected $upload_dir = 'uploads';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['original_url', 'original_filename', 'filetype', 'mime', 'width', 'height', 'role', 'license', 'attribution'];

    public function url($filesystem='local')
    {
        if ($filesystem == 'webdav') {
            return 'https://www.ub.uio.no/om/aktuelt/arrangementer/ureal/bilder/' . $this->filename;
        } else {
            return url(rtrim($this->upload_dir, '/') . '/' . $this->filename);
        }
    }
}
