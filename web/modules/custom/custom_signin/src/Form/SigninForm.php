<?php

namespace Drupal\custom_signin\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Egulias\EmailValidator\EmailValidator;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\user\Entity\User;

class SigninForm extends FormBase {

  /**
   * It contains the the RouteMatch variable
   * 
   * @var Drupal\Core\Session\AccountInterface
   * @var Drupal\Core\Messenger\MessengerInterface
   * @var Egulias\EmailValidator\EmailValidator
   * @var Drupal\Core\Routing\RouteMatchInterface
   * @var Drupal\Core\Mail\MailManagerInterface
   */
  protected $emailValidator;
  protected $account;
  protected $messenger;
  protected $route;
  protected $mailManager;

  /** 
   * Initailize the AccountInterface, MessageInterface and EmailValidator
   * 
   * @param Drupal\Core\Session\AccountInterface $account_interface
   * @param Drupal\Core\Messenger\MessengerInterface $add_messenger
   * @param Egulias\EmailValidator\EmailValidator $email_validator
   * @param Drupal\Core\Routing\RouteMatchInterface $route_match
   * @param Drupal\Core\Mail\MailManagerInterface $mail_manager
   */
  public function __construct(AccountInterface $account_interface, MessengerInterface $add_messenger, EmailValidator $email_validator, RouteMatchInterface $route_match, MailManagerInterface $mail_manager)
  {
    $this->emailValidator = $email_validator;
    $this->account = $account_interface;
    $this->messenger = $add_messenger;
    $this->route = $route_match;
    $this->mailManager = $mail_manager;
  }
  
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('current_user'),
      $container->get('messenger'),
      $container->get('email.validator'),
      $container->get('current_route_match'),
      $container->get('plugin.manager.mail'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'di_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  { 
    $form = [];
    $node = $this->route->getParameter('node');
    if ( !(is_null($node)) ) {
      $nid = $node->id();
    }  
    else {
      $nid = 0;
    }
    $form['fullname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];
    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#required' => TRUE,
    ];
    $form['role'] = [
      '#type' => 'radios',
      '#title' => $this->t('Choose a Role'),
      '#options' => [
        'blogger' => $this->t('Blogger'),
        'guest_blogger' => $this->t('Guest Blogger'),
      ],
      '#default_value' => 'blogger', 
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];  
    return $form;
  }
  
  /**
   * Email Validation
   */
  protected function validateEmail(array &$form, FormStateInterface $form_state){
    if (!$this->emailValidator->isValid($form_state->getValue('email'))){
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $email = $form_state->getValue('email');
    if(!empty($email)) {
      if(!$this->validateEmail($form, $form_state)){
        $form_state->setErrorByName('email',$this->t('Invalid email address'));
      }
    }
    else {
      $form_state->setErrorByName('email',$this->t("Please enter an email address"));
    }
    $form_state->setValue('email',$email);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValues();
    $this->messenger->addMessage('It is done');

    // Check if a user with the same username already exists.
    // if ($this->isUsernameTaken($values['fullname'])) {
    //   $this->messenger->addError($this->t('The username is already taken. Please choose a different username.'));
    //   return;
    // }
    
    $user = User::create();
    $user->setPassword($values['password']);
    $user->enforceIsNew();
    $user->setEmail($values['email']);
    $user->setUsername($values['fullname']);
    $user->addRole($values['role']);
    $user->block();
    $user->save();

    //sending admin notification
    // $this->adminNotification($user, $values['fullname'], $values['email'], $values['role']);
    //sending welcome email to the user
    // $this->sendWelcomeEmail($user);

    // private function isUsernameTaken($username) {
    //   $user = user_load_by_name($username);
    //   return !empty($user);
    // }
  }
}