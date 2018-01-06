<?php

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;


class ButtonSearch extends AbstractHelper
{
    protected $sm;

    public function __construct(ServiceManager $sm)
    {
        /** @var  sm \Zend\ServiceManager\ServiceManager */
        $this->sm = $sm;
    }

    public function __invoke()
    {
        $arrayWhere = $this->sm->get('arrayWhere');
        /** @var  $getQuery \Zend\Stdlib\Parameters */
        $getQuery = $this->sm->get('request')->getQuery();
        foreach($arrayWhere['params'] as $k => &$where){
            if($value = $getQuery->get($k)){
                $where['value'] = $value;
                $arrayWhere['collapsed'] = 0;
            }
        }
        return $this->getView()->render('application/index/search-form',
            [
                'where' => $arrayWhere
            ]
        );
    }


}