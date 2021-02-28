<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class KnpPagination
{
    private $kngPaginator;
    private $serializer;

    public function __construct(PaginatorInterface $knpPaginator, SerializerInterface $serializer)
    {
        $this->knpPaginator = $knpPaginator;
        $this->serializer = $serializer;
    }

    public function showPagination($listItem, $numItemsPerPage, $groupsJmsSerializer, $defaultPage, $pathServer){

        // call kng pagination bundle
        $knpPagination = $this->knpPaginator->paginate(
            $listItem,
            $defaultPage,
            $numItemsPerPage
        );

        $totalItem = $knpPagination->getTotalItemCount();
        $currentPage = $knpPagination->getCurrentPageNumber();
        $maxPage = ceil($totalItem / $numItemsPerPage);
        $items = json_decode(
                        $this->serializer->serialize(
                            $knpPagination->getItems(),
                            'json',
                            SerializationContext::create()->setGroups(array($groupsJmsSerializer))
                        ),
                    true
                    );

        $linkPagination = [
            "first" => $pathServer . "1",
            "last" => $pathServer . $maxPage
        ];

        if ($currentPage > 1 && $currentPage <= $maxPage) {

            $previous = ["previous" => $pathServer . ($currentPage - 1)];
            $linkPagination = array_merge($previous, $linkPagination);
            
        }

        if ($currentPage < $maxPage) {
            $next= ["next" => $pathServer . ($currentPage + 1)];
            $linkPagination = array_merge( $linkPagination, $next);
        }

        // create my own pagination with kng Pagination bundle
        $pagination = [
            "current_page_number" => $currentPage,
            "num_items_per_page" => $numItemsPerPage,
            "total_item" => $totalItem, 
            "items" =>  $items,
            "_links" => $linkPagination
            ];

        return $pagination;
    }
}