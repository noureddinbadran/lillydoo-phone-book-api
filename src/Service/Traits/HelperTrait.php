<?php

namespace App\Service\Traits;

use App\Resources\ICollection;

trait HelperTrait
{
    /**
     * This method is responsible for paginating any collection and preparing the pagination info
     * @param $query
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function paginate($query, $page = 1, $limit = 5, ICollection $collection)
    {
        $pagination  = $this->paginator->paginate(
            $query,
            // Define the page parameter
            (int)$page,
            // Item per page
            (int)$limit);

        // Finalize the result set
        $pagerResult = [
            'count' => $pagination->getTotalItemCount(),
            'items' => $collection->toArray($pagination->getItems()),
            'limit' => $pagination->getItemNumberPerPage(),
            'current' => $pagination->getCurrentPageNumber()
        ];

        return $pagerResult;
    }
}