<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Util\ClassDetails;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowPagination
{
    public function showPagination($route, $page, $totalItem, $maxPage){

        $pagination = [
            "First page" => $route . "1",
            "Last Page" => $route . $maxPage
        ];
    
        if ($page > 1){
            $previous = $page - 1;

            $tabPre = ["Previous" => $route . $previous];
            $pagination = array_merge($tabPre, $pagination);
        }

        if ($page < $maxPage){
            $next = $page + 1;

            $tabNext = ["Next" => $route . $next];
            $pagination = array_merge( $pagination, $tabNext);
        }
        return $pagination;
    }
    
}