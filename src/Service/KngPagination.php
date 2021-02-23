<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class KngPagination
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack, PaginatorInterface $kngPaginator, SerializerInterface $serializer)
    {
        $this->requestStack = $requestStack;
        $this->kngPaginator = $kngPaginator;
        $this->serializer = $serializer;
    }

    public function showPagination($listItem, $numItemsPerPage, $groupsJmsSerializer){

        $request =  $this->requestStack->getCurrentRequest();
        $route = $request->server->get('SERVER_NAME') . $request->getPathInfo() . "?page=";

        // call kng pagination bundle
        $kngPagination = $this->kngPaginator->paginate(
            $listItem,
            $request->query->getInt('page', 1),
            $numItemsPerPage
        );
        

        $totalItem = $kngPagination->getTotalItemCount();   
        $listItems = $kngPagination->getItems();
        $currentPage = $kngPagination->getCurrentPageNumber();
        $maxPage = ceil($totalItem / $numItemsPerPage);

        $linkPagination = [
            "first" => $route . "1",
            "last" => $route . $maxPage
        ];


        if ($currentPage > 1 && $currentPage <= $maxPage) {

            $previous = ["previous" => $route . ($currentPage - 1)];
            $linkPagination = array_merge($previous, $linkPagination);
            
        }

        if ($currentPage < $maxPage) {
            $next= ["next" => $route . ($currentPage + 1)];
            $linkPagination = array_merge( $linkPagination, $next);
        }

        // create my own pagination with kng Pagination bundle
        $pagination = [
            "current_page_number" => $currentPage,
            "num_items_per_page" => $numItemsPerPage,
            "total_item" => $totalItem, 
            "items" =>  json_decode($this->serializer->serialize($kngPagination->getItems(), 'json', SerializationContext::create()->setGroups(array($groupsJmsSerializer))), true),
            "_links" => $linkPagination
            ];

        return $pagination;
    }
    
}