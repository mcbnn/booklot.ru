<?php

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;


class ButtonSort extends AbstractHelper
{
    protected $sm;

    public function __construct(ServiceManager $sm)
    {
        /** @var  sm \Zend\ServiceManager\ServiceManager */
        $this->sm = $sm;
    }

    public function __invoke($params, $route)
    {
        $arraySort = $this->sm->get('arraySort');
        /** @var  $getQuery \Zend\Stdlib\Parameters */
        $getQuery = $this->sm->get('request')->getQuery();
        $sortGet = $getQuery->get('sort');
        $directionGet = $getQuery->get('direction');
        $sortGet = ($sortGet == null)?$arraySort['default']['sort']:$sortGet;
        $directionGet = ($directionGet == null)?$arraySort['default']['direction']:$directionGet;
        $url = $this->sm->get('ViewHelperManager')->get('url');

        foreach($arraySort['params'] as $k => &$param){
            $arrayGet = $getQuery->toArray();
            unset($arrayGet['sort']);
            unset($arrayGet['direction']);
            $direction = 'desc';
            $class = "";
            if($sortGet == $k){
                $direction = ($directionGet == 'asc')?'desc':'asc';
                $class = "current $direction";
            }
            $param['class'] = $class;
            $arrayGet['sort'] = $k;
            $arrayGet['direction'] = $direction;
            $param['href'] =  $url($route, $params, [
                'query' => $arrayGet
            ]
            );
        }

        return $this->getView()->render('application/index/sort-template',
            [
                'sort' => $arraySort['params']
            ]
        );
    }


}