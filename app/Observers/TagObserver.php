<?php

namespace App\Observers;

use App\Tag;

class TagObserver
{
    /**
     * Listen to the Tag created event.
     *
     * @param  \App\Tag  $tag
     * @return void
     */
    public function created(Tag $tag)
    {
        \Log::info('Created new tag: "${$tag->tag_name}"');
    }

    /**
     * Listen to the Tag deleting event.
     *
     * @param  \App\Tag  $tag
     * @return void
     */
    public function deleting(Tag $tag)
    {
        \Log::info('Deleted tag: "${$tag->tag_name}"');
    }
}