<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\LoginForm;
use Application\Form\RegForm;
use Application\Model\Bogi;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManager;

class AuthController extends AbstractActionController
{
    protected $form;
    protected $storage;
    protected $authservice;

    /**
     * @var null|ServiceManager
     */
    public $sm = null;

    public function __construct(ServiceManager $servicemanager)
    {
        $this->sm = $servicemanager;
    }

    public function getServiceLocator(){
        return $this->sm;
    }

	public function confirmAction(){
	 
		$sm = $this->getServiceLocator();
		$confirm = $this->params()->fromRoute('confirm');
		$where = "confirm = '{$confirm}' and vis = '0'";
		$check = $sm->get( 'Application\Model\BogiTable' )->fetchAll(false, false, $where);
		$status = 1;
	
		if($check->count() == 1){
			$bogi = $check->current();
			$status = 2;
			$arr = array();
			$arr['vis'] = 1;
			$where = array();
			$where['id'] = $bogi->id;
			$sm->get( 'Application\Model\BogiTable' )->save($arr, $where);
		}
		return new ViewModel(array(
				'status'   => $status,
		));
		
	}
	
	public function regAction(){
		 //if already login, redirect to success page
		$mainController = new MainController();
		$sm = $this->getServiceLocator();
        $form = $this->getFormReg();
		$redirect = 'reg';
		$request = $this->getRequest();
		$status = array('login_status' => "invalid", "err_text" => 'возникла ошибка, проверьте правильность ввода', "redirect_url" => $redirect);
		if ($request->isPost()){
			$form->setData($request->getPost());
 			///ini_set('display_errors',1);

            try {
                if ($form->isValid()) {


                    $err = $this->validateForm($request->getPost());
                    if (!$err) {
                        $bogi = new Bogi();
                        $name = htmlspecialchars($request->getPost('name'));
                        $email = htmlspecialchars($request->getPost('email'));
                        $birth = htmlspecialchars($request->getPost('birth'));
                        $password = htmlspecialchars($request->getPost('password'));
                        $sex = htmlspecialchars($request->getPost('sex'));
                        $arr = array();
                        $arr['name'] = $name;
                        $arr['password'] = $password;
                        $arr['email'] = $email;
                        $birth = \DateTime::createFromFormat('Y-m-d', $birth);
                        $birth = $birth->format('Y-m-d');
                        $arr['birth'] = $birth;
                        $arr['sex'] = ($sex == 'M') ? 'M' : 'F';
                        $arr['foto'] = 'user.jpg';
                        $arr['comments'] = 0;
                        $arr['datetime_reg'] = date('Y-m-d H:i:s');
                        $arr['datetime_log'] = date('Y-m-d H:i:s');
                        $confirm = md5(date('Y-m-d H:i:s'));
                        $arr['confirm'] = $confirm;
                        $arr['vis'] = 0;
                        $sm->get('Application\Model\BogiTable')->save($arr);
                        $status = array('login_status' => "valid", "redirect_url" => 'login');
                        $title = "Регистрация на сайте booklot.org, код подтверждения";
                        $to = $email;
                        $from = "mcbnn123@gmail.com";
                        $html = '<h1>Спасибо за регистрацию на сайте booklot.org</h1>';
                        $html .= '<p>Вы зарегистрировались в электронной библиотеке, у нас представлен большой выбор литературы разных жанров, вы можете убедиться <a href = "http://www.booklot.org/genre/">тут</a>.</p>';
                        $html .= '<p>Каждый день происходит пополнение книжек, ресурс развивается и если вам понравилась книга то комментируйте.</p>';
                        $html .= '<p>Для подтверждение регистрации вам нужно пройти по <a href = "http://www.booklot.org/confirm/' . $confirm . '/">http://www.booklot.org/confirm/' . $confirm . '/</a></p>';
                        $html .= '<p>Если у вас есть вопросы или предложения пишите <a href = "mailto:mc_bnn@mail.ru">mc_bnn@mail.ru</a></p>';
                        $html .= '<p>С уважением Администратор сайта <a href = "http://www.booklot.org/">www.booklot.org</a></p>';
                        var_dump($mainController->email4('gmail', $title, $to, $from, $html));
                    } else {
                        $status = array('login_status' => "invalid", "err_text" => $err);
                    }
                }
            }
            catch (\Exception $e){
                $status['getLine'] = $e->getLine();
                $status['getMessage'] = $e->getMessage();
                $status['trace'] = $e->getTraceAsString();
                $status['all'] = $e;
            }

			echo json_encode($status);die();

		}

        return   array(
            'form'     => $form,
            'messages' => $this->flashmessenger()->getMessages()
        );
	}
	
	
    public function validateForm($obj){
		$sm = $this->getServiceLocator();
		$err = false;
		if(!isset($obj->name) or empty($obj->name)){
			$err ['name'] = "Не указано имя";
		}
		if(!isset($obj->password) or empty($obj->password) or mb_strlen($obj->password,'utf-8')  <  5){
			$err ['password'] = "Не указан пароль/либо маловат";
		}
		if(!isset($obj->email) or empty($obj->email) or !filter_var($obj->email, FILTER_VALIDATE_EMAIL)){
			$err ['email'] = "Не указана почта/неверный формат";
		}
		else{
		$where = "email = '{$obj->email}'";
		$check = $sm->get( 'Application\Model\BogiTable' )->fetchAll(false, false, $where);
		if($check->count() != 0){
			$err ['email2'] = "такая почта уже занята";
		}
		}
		if(empty($obj->birth) or !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/isu', $obj->birth)){
			$err ['birth'] = "не корректна указана дата рождения";
		}
		
		if(!empty($err)){

			return $err;
		}
		return false;
	}
	
    public function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()
                ->get('AuthService');
        }

        return $this->authservice;
    }

    public function getSessionStorage()
    {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get('Application\Model\MyAuthStorage');
        }
        return $this->storage;
    }

    public function getForm()
    {
        if (!$this->form) {
            $this->form  = new LoginForm();
        }

        return $this->form;
    }
	
	 public function getFormReg()
    {
        if (!$this->form) {
            $this->form  = new RegForm();
        }

        return $this->form;
    }

	
    public function loginAction()
    {
        //if already login, redirect to success page

        if ($this->getAuthService()->hasIdentity()) {
	        return $this->redirect()->toRoute( 'home', array( 'action' => 'index' ) );
        }
        $form = $this->getForm();

        return   array(
            'form'     => $form,
            'messages' => $this->flashmessenger()->getMessages()
        );
    }

    public function authenticateAction()
    {
            $redirect = 'login';
            $status = array('login_status' => "invalid", "redirect_url" => $redirect);
            try {

                $form = $this->getForm();
                $request = $this->getRequest();
                if ($request->isPost()) {
                    $form->setData($request->getPost());

                    if ($form->isValid()) {
                        $this->getAuthService()->getAdapter()->setIdentity($request->getPost('username'))->setCredential($request->getPost('password'));
                        $result = $this->getAuthService()->authenticate();
                        foreach ($result->getMessages() as $message) {
                            $this->flashmessenger()->addMessage($message);
                        }


                        if ($result->isValid()) {
                            $redirect = 'admin';
                            $session = $this->getSessionStorage();
                            $session->setRememberMe(true);
                            $this->getAuthService()->setStorage($session);
                            $userInfo = $this->getAuthService()->getAdapter()->getResultRowObject(['id', 'name', 'password', 'email', 'birth', 'sex', 'foto', 'comments', 'datetime_reg', 'datetime_log', 'my_book', 'role', 'count_status_book']);
                            $this->getAuthService()->getStorage()->write($userInfo);
                            $status = array('login_status' => "success", "redirect_url" => $redirect);
                        }
                    }
                }
            }
            catch (\Exception $e){
                $status['error'] = $e->getMessage();
            }
			echo json_encode($status);die();
    }

    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        return $this->redirect()->toRoute('home');
    }
}