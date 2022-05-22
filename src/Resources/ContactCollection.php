<?php

namespace App\Resources;

class ContactCollection implements ICollection
{
    public function toArray($collection)
    {
        $result = [];
        foreach ($collection as $contact)
        {
            $result []= (new ContactResource($contact))->toArray();
        }
        return $result;
    }
}