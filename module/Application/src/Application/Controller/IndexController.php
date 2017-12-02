<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Application\Form\RegForm;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{

    public $count_comm = 0;
    public $user = 0;


    public function testAction(){
        echo 'test';
        die();
    }

    public function starsAction()
    {

        $sm = $this->getServiceLocator();
        $arr = [];
        $stars = $this->params()->fromQuery('stars');
        $id_book = $this->params()->fromQuery('id_book');
        $ip = $this->getIp();
        $arr['stars'] = $stars;
        $arr['ip'] = $ip;
        $arr['id_book'] = $id_book;
        $err = 1;


        try {

            $check = $sm->get('Application\Model\StarsTable')->fetchAll(
                false,
                false,
                [
                    'id_book' => $id_book,
                    'ip'      => $ip,
                ]
            );

            if ($check->count() == 0) {
                $sm->get('Application\Model\StarsTable')->save($arr);

            } else {
                $sm->get('Application\Model\StarsTable')->save(
                    $arr,
                    [
                        'id_book' => $id_book,
                        'ip'      => $ip,
                    ]
                );
            }

            $stars = $sm->get('Application\Model\StarsTable')->fetchAll(
                false,
                false,
                ['id_book' => $id_book]
            );


            $num_stars = 0;
            $count = 0;
            foreach ($stars as $v) {
                $count++;
                $num_stars += $v->stars;

            }

            $aver_value = (float)($num_stars / $count);

            $arr = [];
            $arr['stars'] = $aver_value;
            $arr['count_stars'] = $count;
            $err = 0;
            $sm->get('Application\Model\BookTable')->save(
                $arr,
                ['id' => $id_book]
            );

        } catch (\Exception $e) {
            //TODO
        }

        return new JsonModel(
            [
                'stars' => $aver_value,
                'count' => $count,
                'err'   => $err,
            ]
        );

    }

    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function searchAction()
    {

        $query = $this->params()->fromQuery();
        $where = $this->whereQuery($query);
        $sm = $this->getServiceLocator();
        $pag = false;
        $book = false;
        $page = $this->params()->fromRoute('page', 1);
        if (empty($page)) {
            $page = 1;
        }

        if ($where) {
            $book = $sm->get('Application\Model\BookTable')
                ->joinZhanr()
                ->joinMZhanr()
                ->joinMZhanrParent()
                ->joinAvtorLeft()
                ->joinMAvtorLeft()
                ->joinSeriiLeft()
                ->joinMSeriiLeft()
                ->joinTranslitLeft()
                ->joinMTranslitLeft()
                ->joinColumn(
                    [
                        new Expression('distinct book.id as id'),
                    ]

                )->fetchAll(false, false, $where, false);

            $count = count($book);

            $arraySort = $this->getServiceLocator()->get('arraySort');
            $order = "book.{$arraySort['default']['sort']} {$arraySort['default']['direction']}";
            $sort = $this->params()->fromQuery('sort', null);
            $direction = ($this->params()->fromQuery('direction', 'desc') == 'desc')
                ? 'desc' : 'asc';

            if ($sort and in_array(
                    $sort,
                    $arraySort['filters']
                )
            ) {
                $order = "book.$sort $direction";
                if ($sort == 'stars') {
                    $order = "book.$sort $direction , book.count_stars DESC";
                }

            }

            $book = $sm->get('Application\Model\BookTable')
                ->joinZhanr()
                ->joinMZhanr()
                ->joinMZhanrParent()
                ->joinAvtorLeft()
                ->joinMAvtorLeft()
                ->joinSeriiLeft()
                ->joinMSeriiLeft()
                ->joinTranslitLeft()
                ->joinMTranslitLeft()
                ->joinColumn(
                    [
                        new Expression('distinct book.id as id'),
                        new Expression('mz0.alias as n_alias_menu'),
                        new Expression('mz0.name as name_zhanr'),
                        new Expression('mz1.alias as n_s'),
                        new Expression('zhanr.id_menu as id_menu'),
                        'foto',
                        'alias',
                        'visit',
                        'name',
                        'text_small',
                        'stars',
                        'count_stars',
                        'date_add',
                        'kol_str',
                        'lang',
                    ]
                )->limit(24)->offset($page * 24 - 24)->fetchAll(
                    false,
                    $order,
                    $where
                );

            $pag = new \Zend\Paginator\Paginator(
                new \Zend\Paginator\Adapter\NullFill($count)
            );
            $pag->setCurrentPageNumber($page);
            $pag->setItemCountPerPage(24);
        }

        $this->seo("Поиск по сайту");

        $vm = new ViewModel(
            [
                'pag'  => $pag,
                'book' => $book
            ]
        );
        $vm->setTemplate('application/index/search-tempalte');

        return $vm;
    }

    public function whereQuery(array $query)
    {
        if (empty($query)) {
            return false;
        }
        $where = "book.vis = 1 ";
        $err = 1;
        foreach ($query as $k => $v) {
            if (empty($v)) {
                continue;
            }
            switch ($k) {
                case 'name':
                    $where .= 'and book.name ILIKE \'%'.htmlspecialchars($v)
                        .'%\'';
                    $err = 0;
                    break;
                case 'zhanr':
                    $where .= ' and mz0.name ILIKE \'%'.htmlspecialchars($v)
                        .'%\'';
                    $err = 0;
                    break;
                case 'avtor':
                    $where .= ' and m_avtor.name ILIKE \'%'.htmlspecialchars($v)
                        .'%\'';
                    $err = 0;
                    break;
                case 'serii':
                    $where .= ' and m_serii.name ILIKE \'%'.htmlspecialchars($v)
                        .'%\'';
                    break;
                case 'translit':
                    $where .= ' and m_translit.name ILIKE \'%'.htmlspecialchars(
                            $v
                        ).'%\'';
                    $err = 0;
                    break;
                case 'year':
                    $where .= ' and book.year = \''.htmlspecialchars($v).'\'';
                    $err = 0;
                    break;
                case 'isbn':
                    $where .= ' and book.isbn ILIKE \''.htmlspecialchars($v)
                        .'%\'';
                    $err = 0;
                    break;
                case 'city':
                    $where .= ' and book.city ILIKE \''.htmlspecialchars($v)
                        .'%\'';
                    $err = 0;
                    break;
                case 'lang':
                    $where .= ' and book.lang ILIKE \''.htmlspecialchars($v)
                        .'%\'';
                    $err = 0;
                    break;
                case 'kol_str':
                    $where .= ' and book.kol_str > \''.htmlspecialchars($v)
                        .'\'';
                    $err = 0;
                    break;
            }
        }
        if ($err) {
            return false;
        }

        return $where;
    }

    public function ajaxsearchAction()
    {
        $data = $this->params()->fromQuery('term');
        $dataBase = $this->querySearchForAjax($data);

        return new JsonModel($dataBase);
    }

    public function querySearchForAjax(array $data)
    {

        if (empty($data)) {
            return [];
        }
        $sm = $this->getServiceLocator();
        $arr = [];

        switch ($data['name']) {

            case 'name':
                $book = $sm->get('Application\Model\BookTable')->fetchAll(
                    false,
                    'name ASC',
                    'vis = 1 and name ILIKE \'%'.htmlspecialchars(
                        $data['value']
                    ).'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->name;
                    $arr[$v->id]['value'] = $v->name;
                    $arr[$v->id]['label'] = $v->name;
                }
                break;
            case 'zhanr':
                $book = $sm->get('Application\Model\MZhanrTable')->fetchAll(
                    false,
                    'name ASC',
                    'route = \'home/genre/one\' and  name ILIKE \''
                    .htmlspecialchars($data['value']).'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->id;
                    $arr[$v->id]['value'] = $v->name;
                    $arr[$v->id]['label'] = $v->name;
                }
                break;
            case 'avtor':
                $book = $sm->get('Application\Model\MAvtorTable')->fetchAll(
                    false,
                    'name ASC',
                    'name ILIKE \'%'.htmlspecialchars($data['value']).'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->name;
                    $arr[$v->id]['value'] = $v->name;
                    $arr[$v->id]['label'] = $v->name;
                }
                break;
            case 'serii':
                $book = $sm->get('Application\Model\MSeriiTable')->fetchAll(
                    false,
                    'name ASC',
                    'name ILIKE \'%'.htmlspecialchars($data['value']).'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->id;
                    $arr[$v->id]['value'] = $v->name;
                    $arr[$v->id]['label'] = $v->name;
                }
                break;
            case 'translit':
                $book = $sm->get('Application\Model\MTranslitTable')->fetchAll(
                    false,
                    'name ASC',
                    'name ILIKE \'%'.htmlspecialchars($data['value']).'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->name;
                    $arr[$v->id]['value'] = $v->name;
                    $arr[$v->id]['label'] = $v->name;
                }
                break;
            case 'year':
                $book = $sm->get('Application\Model\BookTable')->fetchAll(
                    false,
                    'name ASC',
                    'vis = 1 and year = \''.htmlspecialchars($data['value'])
                    .'\'',
                    10,
                    ' year '
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->year;
                    $arr[$v->id]['value'] = $v->year;
                    $arr[$v->id]['label'] = $v->year;
                }
                break;
            case 'isbn':
                $book = $sm->get('Application\Model\BookTable')->fetchAll(
                    false,
                    'name ASC',
                    'vis = 1 and isbn ILIKE \''.htmlspecialchars($data['value'])
                    .'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->isbn;
                    $arr[$v->id]['value'] = $v->isbn;
                    $arr[$v->id]['label'] = $v->isbn;
                }
                break;
            case 'city':
                $book = $sm->get('Application\Model\BookTable')->joinColumn(
                    [
                        new Expression(
                            'DISTINCT ON (book.city) book.city as city'
                        ),
                        'foto',
                        'alias',
                        'visit',
                        'name',
                        'text_small',
                        'stars',
                        'count_stars',
                        'date_add',
                    ]
                )->fetchAll(
                    false,
                    'city ASC',
                    'vis = 1 and city LIKE \''.htmlspecialchars($data['value'])
                    .'%\'',
                    10
                );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->city;
                    $arr[$v->id]['value'] = $v->city;
                    $arr[$v->id]['label'] = $v->city;
                }
                break;
            case 'lang':

                $book = $sm->get('Application\Model\BookTable')->joinColumn(
                        [
                            new Expression('DISTINCT ON (lang) lang as lang'),
                        ]
                    )->fetchAll(
                        false,
                        'lang ASC',
                        'vis = 1 and lang ILIKE \''.htmlspecialchars(
                            $data['value']
                        ).'%\'',
                        10
                    );
                foreach ($book as $v) {
                    $arr[$v->id]['id'] = $v->lang;
                    $arr[$v->id]['value'] = $v->lang;
                    $arr[$v->id]['label'] = $v->lang;
                }
                break;

        }


        return $arr;


    }

    public function citCommAction()
    {
        $sm = $this->getServiceLocator();
        $data = $this->params()->fromPost('data');
        parse_str($data, $data);
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if (!$user) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с добавлением, возможно вам нужно авторизоваться',
                ]
            );
        }
        $arr['text'] = strip_tags(
            $data['sample_wysiwyg'],
            '<a><b><i><u><ul><li><ol><img><br>'
        );
        $arr['id_user'] = $user->id;
        $arr['id_content'] = (int)$data['id_content'];
        $arr['id_parrent'] = $data['id_parrent'];
        $arr['id_menu'] = 1;
        $arr['datetime'] = date("Y-m-d H:i:s");
        $arr['ip'] = $_SERVER['REMOTE_ADDR'];
        $arr['browser'] = $_SERVER['HTTP_USER_AGENT'];
        $arr['ban'] = 0;
        $arr['vis'] = 1;
        if (mb_strlen($arr['text'], "UTF-8") < 3) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с добавлением, комментарий должен быть больше',
                ]
            );
        }
        $id = $sm->get('Application\Model\CommentsTable')->save(
            $arr,
            false,
            true
        );
        $text = $this->templateComm($arr['id_content']);

        return new JsonModel(
            [
                'error'      => 0,
                'text'       => $text,
                'count_comm' => $this->count_comm,
            ]
        );
    }

    public function redCommAction()
    {
        $sm = $this->getServiceLocator();
        $data = $this->params()->fromPost('data');
        parse_str($data, $data);
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if (!$user) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с добавлением, возможно вам нужно авторизоваться',
                ]
            );
        }
        $arr = [];
        $arr['text'] = strip_tags(
            $data['sample_wysiwyg'],
            '<a><b><i><u><ul><li><ol><img><br>'
        );
        if (mb_strlen($arr['text'], "UTF-8") < 3) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с добавлением, комментарий должен быть больше',
                ]
            );
        }
        $sm->get('Application\Model\CommentsTable')->save(
            $arr,
            ['id' => (int)$data['id']]
        );

        $comment = $sm->get('Application\Model\CommentsTable')->fetchAll(
            false,
            false,
            "id = '{$data['id']}'"
        );
        $comment = $comment->current();
        $text = $this->templateComm($comment->id_content);

        return new JsonModel(
            [
                'error'      => 0,
                'text'       => $text,
                'count_comm' => $this->count_comm,
            ]
        );
    }

    public function addAction()
    {
        $sm = $this->getServiceLocator();
        $data = $this->params()->fromPost('data');
        parse_str($data, $data);
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if (!$user) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с добавлением, возможно вам нужно авторизоваться',
                ]
            );
        }
        $arr = [];
        $arr['text'] = strip_tags(
            $data['sample_wysiwyg'],
            '<a><b><i><u><ul><li><ol><img><br>'
        );
        $arr['id_user'] = $user->id;
        $arr['id_content'] = (int)$data['id'];
        $arr['id_parrent'] = 0;
        $arr['id_menu'] = 1;
        $arr['datetime'] = date("Y-m-d H:i:s");
        $arr['ip'] = $_SERVER['REMOTE_ADDR'];
        $arr['browser'] = $_SERVER['HTTP_USER_AGENT'];
        $arr['ban'] = 0;
        $arr['vis'] = 1;
        if (mb_strlen($arr['text'], "UTF-8") < 3) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с добавлением, комментарий должен быть больше',
                ]
            );
        }
        $sm->get('Application\Model\CommentsTable')->save($arr);
        $text = $this->templateComm($arr['id_content']);

        return new JsonModel(
            [
                'error'      => 0,
                'text'       => $text,
                'count_comm' => $this->count_comm,
            ]
        );
    }

    public function delAction()
    {
        $id = $this->params()->fromPost('id');
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if (!$user) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с удаление, возможно вам нужно авторизоваться',
                ]
            );
        }
        $sm = $this->getServiceLocator();
        $where = "id_user = '{$user->id}' and comments.id = '{$id}'";
        $check = $sm->get('Application\Model\CommentsTable')->joinBogi()
            ->fetchAll(false, 'comments.id ASC', $where);
        if ($check->count() == 0 or $check->count() > 1) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблемы с удаление, возможно вам нужно авторизоваться',
                ]
            );
        }
        $sm->get('Application\Model\CommentsTable')->delete('id', $id);
        $check = $check->current();
        $text = $this->templateComm($check->id_content);

        return new JsonModel(
            [
                'error'      => 0,
                'text'       => $text,
                'count_comm' => $this->count_comm,
            ]
        );
    }

    public function onlineAction()
    {
        $id = $this->params()->fromPost('id');
        if (!is_numeric($id)) {
            return new JsonModel(
                [
                    'error' => 1,
                    'text'  => 'Проблема с отображением комментариев',
                ]
            );
        }
        $text = $this->templateComm($id);

        return new JsonModel(
            [
                'error'      => 0,
                'text'       => $text,
                'count_comm' => $this->count_comm,
            ]
        );
    }

    public function templateComm($id = false)
    {
        $this->user = $this->getServiceLocator()->get('AuthService')
            ->getIdentity();
        if (!$id) {
            return;
        }
        $comm = $this->genCommTemplate(0, $id);
        $sm = $this->getServiceLocator();
        if ($this->user) {
            $where = "comments.id_user = '{$this->user->id}' and comments.vis = '1'";
            $comments = $sm->get('Application\Model\CommentsTable')->fetchAll(
                false,
                false,
                $where
            );
            $data = [];
            $data['comments'] = $comments->count();
            $sm->get('Application\Model\BogiTable')->save(
                $data,
                ['id' => $this->user->id]
            );
            $this->getServiceLocator()->get('AuthService')->getStorage()->write(
                $sm->get('Application\Model\BogiTable')->fetchAll(
                    false,
                    false,
                    ['id' => $this->user->id]
                )->current()
            );
        }
        if (!$this->count_comm) {
            return false;
        }
        $text
            = '<div class="row" >
			<div class="col-md-12">
		
			  <div class="panel panel-primary">
		
				<div class="panel-heading">
				  <div class="panel-title">
					<h4>
					  Всего
					  <span class="badge badge-danger" >'.$this->count_comm.'</span>
					</h4>
				  </div>
				</div>
		
				<div class="panel-body no-padding"  id = "block_comm">
				'.$comm.'
				</div>
			  </div>
			</div>
  		</div>
		';

        return $text;
    }

    public function genCommTemplate(
        $parrent,
        $id_content,
        $all = false,
        $id_menu = 1
    ) {

        if (empty($all)) {
            $sm = $this->getServiceLocator();
            $where = "id_content = '{$id_content}' and id_menu = '$id_menu'";
            $all = $sm->get('Application\Model\CommentsTable')->joinBogi()
                ->fetchAll(false, 'comments.id ASC', $where);

            $all_arr = [];
            foreach ($all as $v) {
                $all_arr[] = (array)$v;
            }
            $all = $all_arr;
        }
        $text = "";
        if (!$all) {
            return false;
        }
        foreach ($all as $k => $v) {
            if ($parrent == $v['id_parrent']) {
                $this->count_comm++;
                $text .= '<div class="row comments-list" data-id = "'.$v['id'].'">
										<div class="col-xs-4 col-sm-1 ">
										<div class = "border-horizont"></div>
										';
                if ($v['foto'] == 'user.jpg') {
                    $fm = 'usergirl.jpg';
                    if ($v['sex'] == 'M') {
                        $fm = "userman.jpg";
                    }
                    $text .= '<img width="90%" class="img-circle" alt="" src="/templates/avatar/small/'
                        .$fm.'">';
                } else {
                    $text .= '<img width="90%" class="img-circle" alt="" src="/foto/small/'
                        .$v['foto'].'">';
                }
                $text
                    .= '</div>
									  <div class="col-xs-8 col-sm-11">
										<div class = "border-vertical"></div>
										<div class="comment-head">
										  <a href="#">'.$v['name_user'].'</a>  
										</div>
						
										<p class="comment-text comment-text-new">
											'.nl2br($v['text']).'
										</p>
						
										<div class="comment-footer">
						
										  <div class="comment-time">
											'.date(
                        "d.m.Y H:i",
                        strtotime($v['datetime'])
                    ).'
											</div>';
                if ($this->user) {
                    $text
                        .= '<div class="action-links text-left post-submit">
										  <a href="" class="delete" onclick = "open_cit(this, event)">
											<i class="entypo-comment"></i>
										
											</a>';
                    if ($this->user->id == $v['id_user']) {
                        $text
                            .= '<a href="" class="delete" onclick = "del_comm(this, event)"><i class="entypo-cancel"></i>
													
														</a>
														<a class="edit" onclick = "open_red(this, event)">
														  <i class="entypo-pencil"></i>
														
														</a>';
                    }
                    $text
                        .= '</div>
													<div class = "red-comm">
														<form method = "POST">
														<input name="id" value="'
                        .$v['id'].'" type="hidden">
														<textarea class="form-control wysihtml5" data-stylesheet-url="/assets/css/wysihtml5-color.css" name="sample_wysiwyg" class="sample_wysiwyg">'
                        .$v['text'].'</textarea>
														<div class="post-submit"><button type="button" class="btn btn-primary red-comment" onclick="red_comm(this, event)" >Сохранить</button></div>
														</form>
													</div>
													<div class = "cit-comm">
														<form method = "POST">
														<input name="id_parrent" value="'
                        .$v['id'].'" type="hidden">
														<input name="id_content" value="'
                        .$v['id_content'].'" type="hidden">
														<textarea class="form-control wysihtml5" data-stylesheet-url="/assets/css/wysihtml5-color.css" name="sample_wysiwyg" class="sample_wysiwyg"></textarea>
														<div class="post-submit"><button type="button" class="btn btn-primary red-comment" onclick="cit_comm(this, event)" >Сохранить</button></div>
														</form>
													</div>
										';
                }
                $text .= '</div>';
                $check = $this->genCommTemplate($v['id'], false, $all);
                if ($check) {
                    $text .= $check;
                }
                $text
                    .= "



					</div></div>";
            }
        }

        return $text;
    }

    public function editAction()
    {
        global $site;
        $sm = $this->getServiceLocator();
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        $form = new RegForm();
        $request = $this->getRequest();
        $fromFile = $this->params()->fromFiles('foto');
        if ($request->isPost()) {
            $foto = $sm->get('Main')->fotoSave($fromFile);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $arr = $this->params()->fromPost();
                if (!empty($foto)) {
                    $arr['foto'] = $foto;
                }
                $sm->get('Application\Model\BogiTable')->save(
                    $arr,
                    ['id' => $user->id]
                );
                $this->getServiceLocator()->get('AuthService')->getStorage()
                    ->write(
                        $sm->get('Application\Model\BogiTable')->fetchAll(
                            false,
                            false,
                            ['id' => $user->id]
                        )->current()
                    );

                return $this->redirect()->toRoute(
                    'home/edit',
                    ['subdomain' => $site]
                );

            }
        }

        return [
            'form' => $form,
            'user' => $user,
        ];
    }

    public function sitemapsAction()
    {


    }

    public function oldAction()
    {
        return $this->redirect()->toUrl('http://old.booklot.ru/');
    }

    public function rightholderAction()
    {


    }

    public function riderAction()
    {

        global $site;
        $rider = $this->params()->fromRoute('rider', 0);
        $sm = $this->getServiceLocator();
        if ($rider == 'zhanr') {
            return $this->redirect()->toRoute(
                'home/genre',
                ['subdomain' => $site]
            )->setStatusCode(301);
        } elseif ($rider == 'listbookread') {

            $id = $this->params()->fromQuery('id', 0);
            $avtor = $this->params()->fromQuery('avtor', 0);
            $zhanr = $this->params()->fromQuery('zhanr', 0);
            $serii = $this->params()->fromQuery('serii', 0);
            $translit = $this->params()->fromQuery('translit', 0);
            if ($id) {

                if ($avtor) {
                    $where = "m_avtor.id = '$id'";
                    $author = $sm->get('Application\Model\MAvtorTable')
                        ->fetchAll(false, false, $where);
                    if ($author->count() != 0) {
                        $author = $author->current();

                        return $this->redirect()->toRoute(
                            'home/authors/one',
                            [
                                'subdomain'  => $site,
                                'alias_menu' => $author->alias,
                            ]
                        )->setStatusCode(301);
                    }
                } elseif ($zhanr) {
                    $order = "id_main ASC";
                    $mZhanr = $sm->get('Application\Model\MZhanrTable')
                        ->fetchAll(false, $order, false);
                    $bookRoute['s'] = "";
                    $bookRoute['alias_menu'] = "";
                    $bookRoute['name'] = "";
                    $id = $id + 500;
                    foreach ($mZhanr as $k => $v) {
                        if ($v->id == $id) {
                            $bookRoute['alias_menu'] = $v->alias;
                            $bookRoute['name'] = $v->name;
                            foreach ($mZhanr as $k1 => $v1) {
                                if ($v1->id == $v->id_main) {
                                    $bookRoute['s'] = $v1->alias;
                                }
                            }
                        }
                    }

                    return $this->redirect()->toRoute(
                        'home/genre/one',
                        [
                            'subdomain'  => $site,
                            's'          => $bookRoute['s'],
                            'alias_menu' => $bookRoute['alias_menu'],
                        ]
                    )->setStatusCode(301);
                } elseif ($serii) {
                    $where = "m_serii.id = '$id'";
                    $serii = $sm->get('Application\Model\MSeriiTable')
                        ->fetchAll(false, false, $where);
                    if ($serii->count() != 0) {
                        $serii = $serii->current();

                        return $this->redirect()->toRoute(
                            'home/series/one',
                            [
                                'subdomain'  => $site,
                                'alias_menu' => $serii->alias,
                            ]
                        )->setStatusCode(301);
                    }
                } elseif ($translit) {
                    $where = "m_translit.id = '$id'";
                    $translit = $sm->get('Application\Model\MTranslitTable')
                        ->fetchAll(false, false, $where);
                    if ($translit->count() != 0) {
                        $translit = $translit->current();

                        return $this->redirect()->toRoute(
                            'home/translit/one',
                            [
                                'subdomain'  => $site,
                                'alias_menu' => $translit->alias,
                            ]
                        )->setStatusCode(301);
                    }
                }
            }

            return $this->redirect()->toRoute('home', ['subdomain' => $site])
                ->setStatusCode(301);
        } elseif ($rider == 'readbook') {

            $id = $this->params()->fromQuery('id', 0);
            if ($id) {


                $where = "book.id = '$id'";
                $book = $sm->get('Application\Model\BookTable')->joinZhanr()
                    ->joinMZhanr()->joinMZhanrParent()->fetchAll(
                    false,
                    false,
                    $where
                );

                if (count($book) != 0) {
                    $book = $book[0];
                    return $this->redirect()->toRoute(
                        'home/genre/one/book',
                        [
                            'subdomain'  => $site,
                            's'          => $book->n_s,
                            'alias_menu' => $book->n_alias_menu,
                            'book'       => $book->alias,
                        ]
                    )->setStatusCode(301);

                }
            }

        }

        return $this->redirect()->toRoute('home', ['subdomain' => $site])
            ->setStatusCode(301);

    }

    public function authorsAction()
    {

        $sm = $this->getServiceLocator();
        $search = trim(
            htmlspecialchars(strip_tags($this->params()->fromQuery('search')))
        );
        $where = false;
        if ($search) {
            $where = "name like '%$search%'";
        }
        $order = "m_avtor.name ASC";
        $authors = $sm->get('Application\Model\MAvtorTable')->fetchAll(
            true,
            $order,
            $where,
            false
        );
        $authors->setCurrentPageNumber(
            (int)$this->params()->fromRoute('page', 1)
        );
        $authors->setItemCountPerPage(200);

        $where = "route = 'home/authors'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $this->seo("Авторы", "Авторы", $menu->description, $menu->keywords);

        return new ViewModel(
            [
                'authors' => $authors,
                'menu'    => $menu,
            ]
        );
    }

    public function translitAction()
    {
        //$this->changeAvtor();die();
        $sm = $this->getServiceLocator();
        $search = trim(
            htmlspecialchars(strip_tags($this->params()->fromQuery('search')))
        );
        $where = false;
        if ($search) {
            $where = "name like '%$search%'";
        }
        $order = "m_translit.name ASC";
        $translit = $sm->get('Application\Model\MTranslitTable')->fetchAll(
            true,
            $order,
            $where,
            false
        );
        $translit->setCurrentPageNumber(
            (int)$this->params()->fromRoute('page', 1)
        );
        $translit->setItemCountPerPage(200);
        $where = "route = 'home/translit'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $this->seo(
            "Переводчики",
            "Переводчики",
            $menu->description,
            $menu->keywords
        );

        return new ViewModel(
            [
                'translit' => $translit,
                'menu'     => $menu,
            ]
        );
    }

    public function seriesoneAction()
    {

        $sm = $this->getServiceLocator();
        $alias_author = $this->params()->fromRoute('alias_menu');
        $s = $this->params()->fromRoute('s', 0);
        $order = "book.id DESC";

        $where = "m_serii.alias = '$alias_author'";
        $book = $sm->get('Application\Model\BookTable')
            ->joinZhanr()
            ->joinMZhanr()
            ->joinMZhanrParent()
            ->joinSerii()
            ->joinMSerii()
            ->joinColumn(
                [
                    new Expression('distinct book.id as id'),
                    new Expression('mz0.alias as n_alias_menu'),
                    new Expression('mz0.name as name_zhanr'),
                    new Expression('mz1.alias as n_s'),
                    new Expression('zhanr.id_menu as id_menu'),
                    'foto',
                    'alias',
                    'visit',
                    'name',
                    'text_small',
                    'stars',
                    'count_stars',
                    'date_add',
                    'kol_str',
                    'lang',
                ]
            )
            ->fetchAll(true, $order, $where);

        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        if (!empty($this->params()->fromRoute('page_series'))) {
            $this->noindex(true);
        } else {
            $this->noindex(false);
        }

        $book->setCurrentPageNumber(
            (int)$this->params()->fromRoute('page_series', 1)
        );
        $book->setItemCountPerPage(24);

        $where = "m_serii.alias = '$alias_author'";
        $series = $sm->get('Application\Model\MSeriiTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        $t = "Серия - ".$series->name;
        $this->seo($series->name, $series->name);

        return new ViewModel(
            [
                'book'  => $book,
                'title' => $t,
            ]
        );
    }

    public function seriesAction()
    {
        //$this->changeAvtor();die();
        $sm = $this->getServiceLocator();
        $search = trim(
            htmlspecialchars(strip_tags($this->params()->fromQuery('search')))
        );
        $where = false;
        if ($search) {
            $where = "name like '%$search%'";
        }
        $order = "m_serii.name ASC";
        $series = $sm->get('Application\Model\MSeriiTable')->fetchAll(
            true,
            $order,
            $where,
            false
        );
        $series->setCurrentPageNumber(
            (int)$this->params()->fromRoute('page', 1)
        );
        $series->setItemCountPerPage(200);
        $where = "route = 'home/series'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $this->seo("Серии", "Серии", $menu->description, $menu->keywords);

        return new ViewModel(
            [
                'series' => $series,
                'menu'   => $menu,
            ]
        );
    }

    public function checkAlias($alias, $table, $id)
    {

        $sm = $this->getServiceLocator();
        $where = "alias = '$alias' and id != '$id'";
        $check = $sm->get($table)->fetchAll(false, false, $where);
        if ($check->count()) {
            $alias = $alias."-";
            $alias = $this->checkAlias($alias, $table, $id);
        }

        return $alias;
    }

    public function authorAction()
    {
        //var_dump($this->params()->fromRoute());die();
        $sm = $this->getServiceLocator();
        $alias_author = $this->params()->fromRoute('alias_menu');
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $s = $this->params()->fromRoute('s', 0);
        $order = "book.id DESC";

        $where = "m_avtor.alias = '$alias_author'";
        $book = $sm->get('Application\Model\BookTable')
            ->joinZhanr()
            ->joinMZhanr()
            ->joinMZhanrParent()
            ->joinAvtor()
            ->joinMAvtor()
            ->joinColumn(
                [
                    new Expression('distinct book.id as id'),
                    new Expression('mz0.alias as n_alias_menu'),
                    new Expression('mz0.name as name_zhanr'),
                    new Expression('mz1.alias as n_s'),
                    new Expression('zhanr.id_menu as id_menu'),
                    'foto',
                    'alias',
                    'visit',
                    'name',
                    'text_small',
                    'stars',
                    'count_stars',
                    'date_add',
                    'kol_str',
                    'lang',
                ]
            )
            ->fetchAll(true, $order, $where);

        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        //var_dump($where);
        if (!empty($this->params()->fromRoute('page_author'))) {
            $this->noindex(true);
        } else {
            $this->noindex(false);
        }
        $book->setCurrentPageNumber(
            (int)$this->params()->fromRoute('page_author', 1)
        );
        $book->setItemCountPerPage(24);


        $where = "alias = '$alias_menu'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        $t = "Автор - ".$avtor->name;
        $this->seo($avtor->name, $avtor->name);

        return new ViewModel(
            [
                'book'  => $book,
                'title' => $t,
            ]
        );
    }

    public function translitoneAction()
    {
        //var_dump($this->params()->fromRoute());die();
        $sm = $this->getServiceLocator();
        $alias_author = $this->params()->fromRoute('alias_menu');
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $s = $this->params()->fromRoute('s', 0);
        $order = "book.id DESC";

        $where = "m_translit.alias = '$alias_author'";
        $book = $sm->get('Application\Model\BookTable')
            ->joinZhanr()
            ->joinMZhanr()
            ->joinMZhanrParent()
            ->joinTranslit()
            ->joinMTranslit()
            ->joinColumn(
                [
                    new Expression('distinct book.id as id'),
                    new Expression('mz0.alias as n_alias_menu'),
                    new Expression('mz0.name as name_zhanr'),
                    new Expression('mz1.alias as n_s'),
                    'foto',
                    'alias',
                    'visit',
                    'name',
                    'text_small',
                    'stars',
                    'count_stars',
                    'date_add',
                    'kol_str',
                    'lang',
                ]
            )
            ->fetchAll(true, $order, $where);
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if (!empty($this->params()->fromRoute('page_translit'))) {
            $this->noindex(true);
        } else {
            $this->noindex(false);
        }

        $book->setCurrentPageNumber(
            (int)$this->params()->fromRoute('page_translit', 1)
        );
        $book->setItemCountPerPage(24);


        $where = "alias = '$alias_menu'";
        $avtor = $sm->get('Application\Model\MTranslitTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        $t = "Переводчик - ".$avtor->name;
        $this->seo($avtor->name, $avtor->name);

        return new ViewModel(
            [
                'book'  => $book,
                'title' => $t,
            ]
        );
    }

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $page = $this->params()->fromRoute('paged', 1);
        if (empty($page)) {
            $page = 1;
        }
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }

        $arraySort = $this->getServiceLocator()->get('arraySort');
        $order = "book.{$arraySort['default']['sort']} {$arraySort['default']['direction']}";
        $sort = $this->params()->fromQuery('sort', null);
        $direction = ($this->params()->fromQuery('direction', 'desc') == 'desc')
            ? 'desc' : 'asc';


        if ($sort and in_array(
                $sort,
                $arraySort['filters']
            )
        ) {
            $order = "book.$sort $direction";
            if ($sort == 'stars') {
                $order = "book.$sort $direction , book.count_stars DESC";
            }

        }

        $where = "book.vis = 1";
        $sum = $sm->get('Application\Model\MZhanrTable')->columnSummTable()
            ->fetchAll(false);
        $sum = $sum->current();

        $book = $sm->get('Application\Model\BookTable')
            ->joinZhanr()
            ->joinMZhanr()
            ->joinMZhanrParent()
            ->joinColumn(
            [
                new Expression('distinct book.id as id'),
                new Expression('mz0.alias as n_alias_menu'),
                new Expression('mz0.name as name_zhanr'),
                new Expression('mz1.alias as n_s'),
                'foto',
                'alias',
                'visit',
                'name',
                'text_small',
                'stars',
                'count_stars',
                'date_add',
                'kol_str',
                'lang',
            ]
        )->limit(27)->offset($page * 27 - 27)->fetchAll(false, $order, $where);
        $pag = new \Zend\Paginator\Paginator(
            new \Zend\Paginator\Adapter\NullFill($sum->summBook)
        );
        $pag->setCurrentPageNumber($page);
        $pag->setItemCountPerPage(27);


        $where = "route = 'home'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $this->seo(
            "Читать книги бесплатно, скачать в разных форматах. Книга скачать бесплатно.",
            "Читать книги бесплатно, скачать в разных форматах. Книга скачать бесплатно.",
            $menu->description,
            $menu->keywords
        );

        $vm = new ViewModel(
            [
                'book' => $book,
                'pag'  => $pag,
            ]
        );

        $vm->setTemplate('application/index/index');

        return $vm;
    }

    public function noindex($n = true)
    {
        $renderer = $this->getServiceLocator()->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        if ($n) {

            $renderer->headMeta()->appendName('ROBOTS', 'NOINDEX,FOLLOW');
        } else {
            $renderer->headMeta()->appendName('ROBOTS', 'INDEX,FOLLOW');
        }
    }

    public function seo($name, $title = "", $discription = "", $keywords = "")
    {
        $title = (empty($title)) ? $name : $title;
        $discription = (empty($discription)) ? $title : $discription;
        $keywords = (empty($keywords)) ? $title : $keywords;
        $title = $title;
        $renderer = $this->getServiceLocator()->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        $renderer->headTitle($title);
        $renderer->headMeta()->appendName('description', $discription);
        $renderer->headMeta()->appendName('keywords', $keywords);

    }

    public function bookAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $where = "book.alias = '$alias_book'";

        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );


        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];

        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "avtor.id_main = '{$book['id']}'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->joinAvtor()
            ->fetchAll(false, false, $where);

        $where = "translit.id_main = '{$book['id']}'";
        $translit = $sm->get('Application\Model\MTranslitTable')->joinTranslit()
            ->fetchAll(false, false, $where);

        $where = "serii.id_main = '{$book['id']}'";
        $serii = $sm->get('Application\Model\MSeriiTable')->joinSerii()
            ->fetchAll(false, false, $where);

        $where = "comments_faik.id_book_litmir = '{$book['id_book_litmir']}'";
        $comments_faik = $sm->get('Application\Model\CommentsFaikTable')
            ->fetchAll(false, false, $where);

        $where = "id_book = '{$book['id']}'";
        $files = $sm->get('Application\Model\BookFilesTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "id_main = '{$book['id']}'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            'id ASC',
            $where
        );
        $where = "zhanr.id_main = '{$book['id']}'";
        $id_menu = $sm->get('Application\Model\ZhanrTable')->fetchAll(
            false,
            false,
            $where
        );

        $id_menu = $id_menu->current();

        $order = "id_main ASC";
        $mZhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            $order,
            false
        );


        //текст
        $where = "id_main = '{$book['id']}'";
        $count_text = $sm->get('Application\Model\TextTable')->fetchAll(
            false,
            false,
            $where,
            1
        );
        $count_text = $count_text->count();
        $bookRoute['s'] = "";
        $bookRoute['alias_menu'] = "";
        $bookRoute['name'] = "";
        $bookRoute['s_name'] = "";


        if ($id_menu) {
            foreach ($mZhanr as $k => $v) {
                if ($v->id == $id_menu->id_menu) {
                    $bookRoute['alias_menu'] = $v->alias;
                    $bookRoute['name'] = $v->name;
                    foreach ($mZhanr as $k1 => $v1) {
                        if ($v1->id == $v->id_main) {
                            $bookRoute['s'] = $v1->alias;
                            $bookRoute['s_name'] = $v1->name;
                        }
                    }
                }
            }
        }
        $t = "Книга ".$book['name'].". Жанр - ".$bookRoute['s_name']." - "
            .$bookRoute['name'];
        $this->seo($book['name'], $book['name'], $t, $t);
        $data = [];
        $data['visit'] = $book['visit'] + 1;
        $where = [];
        $where['id'] = $book['id'];
        $sm->get('Application\Model\BookTable')->save($data, $where);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "m_zhanr.alias = '$alias_menu' and zhanr.id_main != '{$book['id']}' and zhanr.id_main > '{$book['id']}'";
        $similar = $sm->get('Application\Model\MZhanrTable')
            ->joinZhanr()
            ->joinBook()
            ->fetchAll(false, false, $where, 3);
        $route_similar = "home/genre/one/book";

        $vm = new ViewModel(
            [
                'book'          => $book,
                'avtor'         => $avtor,
                'serii'         => $serii,
                'comments_faik' => $comments_faik,
                'files'         => $files,
                'soder'         => $soder,
                'bookRoute'     => $bookRoute,
                'title'         => $t,
                'translit'      => $translit,
                'similar'       => $similar,
                'count_text'    => $count_text,
                'route_similar' => $route_similar,
            ]
        );
        $vm->setTemplate('application/index/book');

        return $vm;
    }

    public function problemAvtorAction()
    {

        $alias_book = $this->params()->fromRoute('alias_menu', null);
        if ($alias_book === null) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $sm = $this->getServiceLocator();
        $where = "book.alias = '$alias_book' and book.vis = 0";

        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );

        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];

        $where = "avtor.id_main = '{$book['id']}'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->joinAvtor()
            ->fetchAll(false, false, $where);

        $where = "translit.id_main = '{$book['id']}'";
        $translit = $sm->get('Application\Model\MTranslitTable')->joinTranslit()
            ->fetchAll(false, false, $where);

        $where = "serii.id_main = '{$book['id']}'";
        $serii = $sm->get('Application\Model\MSeriiTable')->joinSerii()
            ->fetchAll(false, false, $where);

        $where = "comments_faik.id_book_litmir = '{$book['id_book_litmir']}'";
        $comments_faik = $sm->get('Application\Model\CommentsFaikTable')
            ->fetchAll(false, false, $where);

        $where = "id_book = '{$book['id']}'";
        $files = $sm->get('Application\Model\BookFilesTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "id_main = '{$book['id']}'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        );
        $where = "zhanr.id_main = '{$book['id']}'";
        $id_menu = $sm->get('Application\Model\ZhanrTable')->fetchAll(
            false,
            false,
            $where
        );

        $id_menu = $id_menu->current();

        $order = "id_main ASC";
        $mZhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            $order,
            false
        );

        $bookRoute['s'] = "";
        $bookRoute['alias_menu'] = "";
        $bookRoute['name'] = "";
        $bookRoute['s_name'] = "";


        if ($id_menu) {
            foreach ($mZhanr as $k => $v) {
                if ($v->id == $id_menu->id_menu) {
                    $bookRoute['alias_menu'] = $v->alias;
                    $bookRoute['name'] = $v->name;
                    foreach ($mZhanr as $k1 => $v1) {
                        if ($v1->id == $v->id_main) {
                            $bookRoute['s'] = $v1->alias;
                            $bookRoute['s_name'] = $v1->name;
                        }
                    }
                }
            }
        }
        $t = "Книга ".$book['name'].". Жанр - ".$bookRoute['s_name']." - "
            .$bookRoute['name'];
        $this->seo($book['name'], $book['name'], $t, $t);

        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "m_zhanr.alias = '$alias_menu' and zhanr.id_main != '{$book['id']}' and zhanr.id_main > '{$book['id']}'";
        $similar = $sm->get('Application\Model\MZhanrTable')
            ->joinZhanr()
            ->joinBook()
            ->fetchAll(false, false, $where, 3);


        return new ViewModel(
            [
                'book'          => $book,
                'avtor'         => $avtor,
                'serii'         => $serii,
                'comments_faik' => $comments_faik,
                'files'         => $files,
                'soder'         => $soder,
                'bookRoute'     => $bookRoute,
                'title'         => $t,
                'translit'      => $translit,
                'similar'       => $similar,
            ]
        );

    }

    public function sbookAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "avtor.id_main = '{$book['id']}'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->joinAvtor()
            ->fetchAll(false, false, $where);

        $where = "serii.id_main = '{$book['id']}'";
        $serii = $sm->get('Application\Model\MSeriiTable')->joinSerii()
            ->fetchAll(false, false, $where);

        $where = "translit.id_main = '{$book['id']}'";
        $translit = $sm->get('Application\Model\MTranslitTable')->joinTranslit()
            ->fetchAll(false, false, $where);

        $where = "comments_faik.id_book_litmir = '{$book['id_book_litmir']}'";
        $comments_faik = $sm->get('Application\Model\CommentsFaikTable')
            ->fetchAll(false, false, $where);

        $where = "id_book = '{$book['id']}'";
        $files = $sm->get('Application\Model\BookFilesTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "id_main = '{$book['id']}'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "zhanr.id_main = '{$book['id']}'";
        $id_menu = $sm->get('Application\Model\ZhanrTable')->fetchAll(
            false,
            false,
            $where
        );

        $id_menu = $id_menu->current();

        $order = "id_main ASC";
        $mZhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            $order,
            false
        );

        //текст
        $where = "id_main = '{$book['id']}'";
        $count_text = $sm->get('Application\Model\TextTable')->fetchAll(
            false,
            false,
            $where
        );
        $count_text = $count_text->count();

        $bookRoute['s'] = "";
        $bookRoute['alias_menu'] = "";
        $bookRoute['name'] = "";
        if ($id_menu) {
            foreach ($mZhanr as $k => $v) {
                if ($v->id == $id_menu->id_menu) {
                    $bookRoute['alias_menu'] = $v->alias;
                    $bookRoute['name'] = $v->name;
                    foreach ($mZhanr as $k1 => $v1) {
                        if ($v1->id == $v->id_main) {
                            $bookRoute['s'] = $v1->alias;
                        }
                    }
                }
            }
        }


        if ($serii->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $t = "Книга ".$book['name'].". Серия - ".$serii->current()->name;
        $this->seo(
            $book['name'].". Серия - ".$serii->current()->name,
            $book['name'].". Серия - ".$serii->current()->name,
            $t,
            $t
        );
        $data = [];
        $data['visit'] = $book['visit'] + 1;
        $where = [];
        $where['id'] = $book['id'];
        $sm->get('Application\Model\BookTable')->save($data, $where);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "m_serii.alias = '$alias_menu' and serii.id_main != '{$book['id']}' and serii.id_main> '{$book['id']}'";
        $similar = $sm->get('Application\Model\MSeriiTable')->joinSerii()
            ->joinBook()->fetchAll(false, false, $where, 3);
        $route_similar = "home/series/one/book";
        $vm = new ViewModel(
            [
                'book'          => $book,
                'avtor'         => $avtor,
                'serii'         => $serii,
                'comments_faik' => $comments_faik,
                'files'         => $files,
                'soder'         => $soder,
                'bookRoute'     => $bookRoute,
                'title'         => $t,
                'translit'      => $translit,
                'similar'       => $similar,
                'count_text'    => $count_text,
                'route_similar' => $route_similar,
            ]
        );
        $vm->setTemplate('application/index/book');

        return $vm;
    }

    public function abookAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );

        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "avtor.id_main = '{$book['id']}'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->joinAvtor()
            ->fetchAll(false, false, $where);

        $where = "serii.id_main = '{$book['id']}'";
        $serii = $sm->get('Application\Model\MSeriiTable')->joinSerii()
            ->fetchAll(false, false, $where);

        $where = "translit.id_main = '{$book['id']}'";
        $translit = $sm->get('Application\Model\MTranslitTable')->joinTranslit()
            ->fetchAll(false, false, $where);


        $where = "comments_faik.id_book_litmir = '{$book['id_book_litmir']}'";
        $comments_faik = $sm->get('Application\Model\CommentsFaikTable')
            ->fetchAll(false, false, $where);

        $where = "id_book = '{$book['id']}'";
        $files = $sm->get('Application\Model\BookFilesTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "id_main = '{$book['id']}'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "zhanr.id_main = '{$book['id']}'";
        $id_menu = $sm->get('Application\Model\ZhanrTable')->fetchAll(
            false,
            false,
            $where
        );

        $id_menu = $id_menu->current();

        $order = "id_main ASC";
        $mZhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            $order,
            false
        );

        //текст
        $where = "id_main = '{$book['id']}'";
        $count_text = $sm->get('Application\Model\TextTable')->fetchAll(
            false,
            false,
            $where
        );
        $count_text = $count_text->count();

        $bookRoute['s'] = "";
        $bookRoute['alias_menu'] = "";
        $bookRoute['name'] = "";

        if ($id_menu) {
            foreach ($mZhanr as $k => $v) {

                if ($v->id == $id_menu->id_menu) {
                    $bookRoute['alias_menu'] = $v->alias;
                    $bookRoute['name'] = $v->name;
                    foreach ($mZhanr as $k1 => $v1) {
                        if ($v1->id == $v->id_main) {
                            $bookRoute['s'] = $v1->alias;
                        }
                    }
                }
            }
        }

        if ($avtor->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $t = "Книга ".$book['name'].". Автор - ".$avtor->current()->name;
        $this->seo(
            $book['name'].". Автор - ".$avtor->current()->name,
            $book['name'].". Автор - ".$avtor->current()->name,
            $t,
            $t
        );

        $data = [];
        $data['visit'] = $book['visit'] + 1;
        $where = [];
        $where['id'] = $book['id'];
        $sm->get('Application\Model\BookTable')->save($data, $where);

        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "m_avtor.alias = '$alias_menu' and avtor.id_main != '{$book['id']}'  and avtor.id_main> '{$book['id']}'";
        $similar = $sm->get('Application\Model\MAvtorTable')->joinAvtor()
            ->joinBook()->fetchAll(false, false, $where, 3);

        $route_similar = "home/authors/one/book";
        $vm = new ViewModel(
            [
                'book'          => $book,
                'avtor'         => $avtor,
                'serii'         => $serii,
                'comments_faik' => $comments_faik,
                'files'         => $files,
                'soder'         => $soder,
                'bookRoute'     => $bookRoute,
                'title'         => $t,
                'translit'      => $translit,
                'similar'       => $similar,
                'count_text'    => $count_text,
                'route_similar' => $route_similar,
            ]
        );
        $vm->setTemplate('application/index/book');

        return $vm;
    }

    public function tbookAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "translit.id_main = '{$book['id']}'";
        $translit = $sm->get('Application\Model\MTranslitTable')->joinTranslit()
            ->fetchAll(false, false, $where);

        $where = "avtor.id_main = '{$book['id']}'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->joinAvtor()
            ->fetchAll(false, false, $where);

        $where = "serii.id_main = '{$book['id']}'";
        $serii = $sm->get('Application\Model\MSeriiTable')->joinSerii()
            ->fetchAll(false, false, $where);

        $where = "comments_faik.id_book_litmir = '{$book['id_book_litmir']}'";
        $comments_faik = $sm->get('Application\Model\CommentsFaikTable')
            ->fetchAll(false, false, $where);

        $where = "id_book = '{$book['id']}'";
        $files = $sm->get('Application\Model\BookFilesTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "id_main = '{$book['id']}'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        );

        $where = "zhanr.id_main = '{$book['id']}'";
        $id_menu = $sm->get('Application\Model\ZhanrTable')->fetchAll(
            false,
            false,
            $where
        );

        $id_menu = $id_menu->current();

        $order = "id_main ASC";
        $mZhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            $order,
            false
        );

        $bookRoute['s'] = "";
        $bookRoute['alias_menu'] = "";
        $bookRoute['name'] = "";

        //текст
        $where = "id_main = '{$book['id']}'";
        $count_text = $sm->get('Application\Model\TextTable')->fetchAll(
            false,
            false,
            $where
        );
        $count_text = $count_text->count();

        if ($id_menu) {
            foreach ($mZhanr as $k => $v) {
                if ($v->id == $id_menu->id_menu) {
                    $bookRoute['alias_menu'] = $v->alias;
                    $bookRoute['name'] = $v->name;
                    foreach ($mZhanr as $k1 => $v1) {
                        if ($v1->id == $v->id_main) {
                            $bookRoute['s'] = $v1->alias;
                        }
                    }
                }
            }
        }

        if ($translit->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $t = "Книга ".$book['name'].". Переводчик - ".$translit->current(
            )->name;
        $this->seo(
            $book['name'].". Переводчик - ".$translit->current()->name,
            $book['name'].". Переводчик - ".$translit->current()->name,
            $t,
            $t
        );

        $data = [];
        $data['visit'] = $book['visit'] + 1;
        $where = [];
        $where['id'] = $book['id'];
        $sm->get('Application\Model\BookTable')->save($data, $where);

        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "m_translit.alias = '$alias_menu' and translit.id_main != '{$book['id']}'  and translit.id_main> '{$book['id']}'";
        $similar = $sm->get('Application\Model\MTranslitTable')->joinTranslit()
            ->joinBook()->fetchAll(false, false, $where, 3);

        $route_similar = "home/translit/one/book";
        $vm = new ViewModel(
            [
                'book'          => $book,
                'avtor'         => $avtor,
                'serii'         => $serii,
                'translit'      => $translit,
                'comments_faik' => $comments_faik,
                'files'         => $files,
                'soder'         => $soder,
                'bookRoute'     => $bookRoute,
                'title'         => $t,
                'similar'       => $similar,
                'count_text'    => $count_text,
                'route_similar' => $route_similar,
            ]
        );
        $vm->setTemplate('application/index/book');

        return $vm;
    }

    public function genreAction()
    {
        $sm = $this->getServiceLocator();
        $where = "route = 'home/genre'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $this->seo(
            "книга читать жанры онлайн бесплатно",
            "книга жанры онлайн бесплатно",
            $menu->description,
            $menu->keywords
        );

        return new ViewModel(
            [
                'menu' => $menu,
            ]
        );

    }

    public function readAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);


        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        if (!$page_str) {
            $t = "Книга ".$book['name'].". Страницы:";
            $this->seo(
                $book['name'].". Страницы ",
                $book['name'].". Страницы".$page_str,
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );

        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Страница ".$page_str;
        $this->seo(
            $book['name'].". Страница ".$page_str,
            $book['name'].". Страница ".$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );
        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function treadAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = $this->params()->fromRoute('alias_menu');

        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";
        $translit = $sm->get('Application\Model\MTranslitTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        if (!$page_str) {
            $t = "Книга ".$book['name'].". Переводчик ".$translit->name
                .". Страницы:";
            $this->seo(
                $book['name'].". Переводчик ".$translit->name.". Страницы",
                $book['name'].". Переводчик ".$translit->name.". Страницы",
                $t,
                $t
            );


            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        //var_dump($where);
        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);
        //var_dump($text->count());
        //var_dump(get_class_methods($text));die();

        $t = "Книга ".$book['name'].". Переводчик ".$translit->name
            .". Страница ".$page_str;
        $this->seo(
            $book['name'].". Переводчик ".$translit->name.". Страница "
            .$page_str,
            $book['name'].". Переводчик ".$translit->name.". Страница "
            .$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function areadAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = $this->params()->fromRoute('alias_menu');

        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($avtor->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $avtor = $avtor->current();

        if (!$page_str) {
            $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Страницы:";
            $this->seo(
                $book['name'].". Автор ".$avtor->name.". Страницы",
                $book['name'].". Автор ".$avtor->name.". Страницы",
                $t,
                $t
            );


            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        //var_dump($where);
        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);
        //var_dump($text->count());
        //var_dump(get_class_methods($text));die();

        $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Страница "
            .$page_str;
        $this->seo(
            $book['name'].". Автор ".$avtor->name.". Страница ".$page_str,
            $book['name'].". Автор ".$avtor->name.". Страница ".$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function sreadAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";

        $serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($serii->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $serii = $serii->current();

        if (!$page_str) {

            $t = "Книга ".$book['name'].". Серия ".$serii->name.". Страницы: ";
            $this->seo(
                $book['name'].". Серия ".$serii->name.". Страницы",
                $book['name'].". Серия ".$serii->name.". Страницы",
                $t,
                $t
            );

            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        //var_dump($where);
        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Серия ".$serii->name.". Страница "
            .$page_str;
        $this->seo(
            $book['name'].". Серия ".$serii->name.". Страница ".$page_str,
            $book['name'].". Серия ".$serii->name.". Страница ".$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function contentAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Содержание:";
            $this->seo(
                $book['name']." - Содержание",
                $book['name']." - Содержание",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Содержание - ".$soder->name;
        $this->seo(
            $book['name']." - ".$soder->name,
            $book['name']." - ".$soder->name,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/genre/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function tcontentAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "alias = '$alias_menu'";
        $translit = $sm->get('Application\Model\MTranslitTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($translit->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $translit = $translit->current();

        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Переводчик ".$translit->name
                .". Содержание:";
            $this->seo(
                $book['name'].". Переводчик ".$translit->name.". Содержание",
                $book['name'].". Переводчик ".$translit->name.". Содержание",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Переводчик ".$translit->name
            .". Содержание - ".$soder->name;
        $this->seo(
            $book['name'].". Переводчик ".$translit->name.". Содержание - "
            .$soder->name,
            $book['name'].". Переводчик ".$translit->name.". Содержание - "
            .$soder->name,
            $t,
            $t
        );

        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/translit/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function acontentAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "alias = '$alias_menu'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($avtor->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $avtor = $avtor->current();

        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Содержание:";
            $this->seo(
                $book['name'].". Автор ".$avtor->name.". Содержание",
                $book['name'].". Автор ".$avtor->name.". Содержание",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Содержание - "
            .$soder->name;
        $this->seo(
            $book['name'].". Автор ".$avtor->name.". Содержание - "
            .$soder->name,
            $book['name'].". Автор ".$avtor->name.". Содержание - "
            .$soder->name,
            $t,
            $t
        );

        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/authors/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function scontentAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";
        $serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(
            false,
            false,
            $where
        );
        if ($serii->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $serii = $serii->current();

        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Серия ".$serii->name.". Содержание:";
            $this->seo(
                $book['name'].". Серия ".$serii->name.". Содержание.",
                $book['name'].". Серия ".$serii->name.". Содержание.",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();


        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);


        $t = "Книга ".$book['name'].". Серия ".$serii->name.". Содержание - "
            .$soder->name;
        $this->seo(
            $book['name'].". Серия ".$serii->name.". Содержание - "
            .$soder->name,
            $book['name'].". Серия ".$serii->name.". Содержание - "
            .$soder->name,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/series/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function oneGenreAction()
    {

        $sm = $this->getServiceLocator();
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $page = $this->params()->fromRoute('page', 1);

        if (empty($page)) {
            $page = 1;
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $s = $this->params()->fromRoute('s', 0);

        $arraySort = $this->getServiceLocator()->get('arraySort');
        $order = "book.{$arraySort['default']['sort']} {$arraySort['default']['direction']}";
        $sort = $this->params()->fromQuery('sort', null);
        $direction = ($this->params()->fromQuery('direction', 'desc') == 'desc')
            ? 'desc' : 'asc';

        if ($sort and in_array(
                $sort,
                $arraySort['filters']
            )
        ) {
            $order = "book.$sort $direction";
            if ($sort == 'stars') {
                $order = "book.$sort $direction , book.count_stars DESC";
            }
        }

        $sd = "";

        if (!$s) {
            $mZhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
                false,
                false,
                false
            );
            $id = "zhanr.id_menu in (";
            $sd = "m_zhanr.id in (";
            $check = 0;
            foreach ($mZhanr as $v) {
                if ($v->alias == $alias_menu) {
                    $id_main = $v->id;
                    $mZhanr1 = $sm->get('Application\Model\MZhanrTable')
                        ->fetchAll(false, false, false);
                    foreach ($mZhanr1 as $v1) {

                        if ($v1->id_main == $id_main) {
                            $check = 1;
                            $id .= " $v1->id , ";
                            $sd .= " $v1->id , ";
                        }
                    }
                }

            }
            $id = substr($id, 0, strlen($id) - 2).")";
            $sd = substr($sd, 0, strlen($sd) - 2).")";
            $where = $id;

        } else {
            $where = "mz0.alias = '$alias_menu'";
            $sd = "m_zhanr.alias = '$alias_menu'";
        }
//        if(!$check){
//            $this->getResponse()->setStatusCode(404);
//            return;
//        }

        $sum = $sm->get('Application\Model\MZhanrTable')->columnSummTable()
            ->fetchAll(false, false, $sd);
        $sum = $sum->current();
        $book = $sm->get('Application\Model\BookTable')
            ->joinZhanr()
            ->joinMZhanr()
            ->joinMZhanrParent()
            ->joinColumn(
                [
                    new Expression('distinct book.id as id'),
                    new Expression('mz0.alias as n_alias_menu'),
                    new Expression('mz0.name as name_zhanr'),
                    new Expression('mz1.alias as n_s'),
                    new Expression('zhanr.id_menu as id_menu'),
                    'foto',
                    'alias',
                    'visit',
                    'name',
                    'text_small',
                    'stars',
                    'count_stars',
                    'date_add',
                    'kol_str',
                    'lang',
                ]
            )->limit(24)->offset($page * 24 - 24)->fetchAll(
            false,
            $order,
            $where
        );

        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $pag = new \Zend\Paginator\Paginator(
            new \Zend\Paginator\Adapter\NullFill($sum->summBook)
        );
        $pag->setCurrentPageNumber($page);
        $pag->setItemCountPerPage(24);

        $where = "alias = '$alias_menu'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $this->seo(
            $menu->name.' читать онлайн',
            $menu->name.' читать онлайн',
            $menu->description,
            $menu->keywords
        );

        return new ViewModel(
            [
                'book' => $book,
                'menu' => $menu,
                'pag'  => $pag,
            ]
        );
    }

    public function notSearch($search)
    {

        $vm = new ViewModel(['search' => $search]);
        $vm->setTemplate('application/index/notsearch');

        return $vm;

    }

}
