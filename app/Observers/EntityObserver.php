<?php

namespace App\Observers;

use App\Entity;

class EntityObserver
{
    /**
     * Listen to the Entity created event.
     *
     * @param  \App\Entity  $entity
     * @return void
     */
    public function created(Entity $entity)
    {
        \Log::info("Created new tag: '{$entity->entity_label}'");
    }

    /**
     * Listen to the Entity deleting event.
     *
     * @param  \App\Entity  $entity
     * @return void
     */
    public function deleting(Entity $entity)
    {
        \Log::info("Deleted tag: '{$entity->entity_label}'");
    }
}