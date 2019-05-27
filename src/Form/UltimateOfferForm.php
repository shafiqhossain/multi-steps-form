<?php

/**
 * @file
 * Contains Drupal\ultimate_offer\Form\UltimateOfferForm.
 */

namespace Drupal\ultimate_offer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
  * Ultimate Offer multi-step form
  */
class UltimateOfferForm extends FormBase {

  /**
   * The Step Counter.
   *
   * @var int $step
   */
  protected $step = 1;

  /**
   * @var AccountInterface $account
   */
  protected $account;

  /**
   * The Entity Manager.
   *
   * @var EntityManagerInterface $manager
   */
  protected $manager;

  /**
   * The Entity Query.
   *
   * @var QueryFactory $queryFactory
   */
  protected $queryFactory;


  /**
   * {@inheritdoc}
   */
  public function __construct(QueryFactory $query_factory, EntityManagerInterface $manager, AccountInterface $account) {
    $this->queryFactory = $query_factory;
    $this->manager = $manager;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query'),
      $container->get('entity.manager'),
      $container->get('current_user')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'ultimate_offer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    //$form = parent::buildForm($form, $form_state);

	//Step 1: Zipcode
    if($this->step == 1) {
      $storage = &$form_state->getStorage();

  	  $form['header_text_1'] = array('#markup' => '<h3 class="header-text">'.$this->t('We Buy Houses in Houston and More').'</h3>');
  	  $form['header_text_2'] = array('#markup' => '<h4 class="header-text">'.$this->t('Get started here').'</h4>');
  	  $form['header_text_3'] = array('#markup' => '<p class="header-text header-text1">'.$this->t('We pay cash in days, you pick move out days').'</p>');
  	  $form['header_text_4'] = array('#markup' => '<p class="header-text header-text2">'.$this->t('Your Information is:').'</p>');
  	  $form['header_text_5'] = array('#markup' => '<div class="steps"><span class="step step1 active">'.$this->t('1').'</span><span class="step step2">'.$this->t('2').'</span><span class="step step3">'.$this->t('3').'</span><span class="step step4">'.$this->t('4').'</span></div>');

	  $form['zipcode'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Zipcode'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step1']['zipcode']) && !empty($storage['step1']['zipcode']) ? $storage['step1']['zipcode'] : ''),
		'#placeholder' => $this->t('Zip Code*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
      $form['offer_step'] = array('#type' => 'hidden', '#value' => $this->step);

	  $form['next'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Next'),
		'#id' => 'edit-next',
      	'#submit' => array('::nextSubmitForm'),
	  );
    }

	//Step 2: Full Name, Phone, Email
    if($this->step == 2) {
      $storage = &$form_state->getStorage();

  	  $form['header_text_1'] = array('#markup' => '<h3 class="header-text">'.$this->t('We Buy Houses in Houston and More').'</h3>');
  	  $form['header_text_2'] = array('#markup' => '<h4 class="header-text">'.$this->t('Get started here').'</h4>');
  	  $form['header_text_3'] = array('#markup' => '<p class="header-text header-text1">'.$this->t('Almost there. You will receive a FREE and Fair Cash Offer. With No Obligation.').'</p>');
  	  $form['header_text_4'] = array('#markup' => '<p class="header-text header-text2">'.$this->t('Your privacy is respected.').'</p>');
  	  $form['header_text_5'] = array('#markup' => '<div class="steps"><span class="step step1">'.$this->t('1').'</span><span class="step step2 active">'.$this->t('2').'</span><span class="step step3">'.$this->t('3').'</span><span class="step step4">'.$this->t('4').'</span></div>');
	  $form['full_name'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Full Name'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step2']['full_name']) && !empty($storage['step2']['full_name']) ? $storage['step2']['full_name'] : ''),
		'#placeholder' => $this->t('Full Name*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['phone'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Phone'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step2']['phone']) && !empty($storage['step2']['phone']) ? $storage['step2']['phone'] : ''),
		'#placeholder' => $this->t('Phone*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['email'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Email'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step2']['email']) && !empty($storage['step2']['email']) ? $storage['step2']['email'] : ''),
		'#placeholder' => $this->t('Email*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );

      $form['offer_step'] = array('#type' => 'hidden', '#value' => $this->step);
	  $form['prev'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Previous'),
		'#id' => 'edit-prev',
      	'#submit' => array('::prevSubmitForm'),
	  );
	  $form['next'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Next'),
		'#id' => 'edit-next',
      	'#submit' => array('::nextSubmitForm'),
	  );
    }

	//Step 3: Address, City
    if($this->step == 3) {
      $storage = &$form_state->getStorage();

  	  $form['header_text_1'] = array('#markup' => '<h3 class="header-text">'.$this->t('We Buy Houses in Houston and More').'</h3>');
  	  $form['header_text_2'] = array('#markup' => '<h4 class="header-text">'.$this->t('Get started here').'</h4>');
  	  $form['header_text_3'] = array('#markup' => '<p class="header-text header-text1">'.$this->t('We pay cash in days you pick move out days.').'</p>');
  	  $form['header_text_4'] = array('#markup' => '<p class="header-text header-text2">'.$this->t('Your privacy is respected.').'</p>');
  	  $form['header_text_5'] = array('#markup' => '<div class="steps"><span class="step step1">'.$this->t('1').'</span><span class="step step2">'.$this->t('2').'</span><span class="step step3 active">'.$this->t('3').'</span><span class="step step4">'.$this->t('4').'</span></div>');
	  $form['address'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Address'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step3']['address']) && !empty($storage['step3']['address']) ? $storage['step3']['address'] : ''),
		'#placeholder' => $this->t('Address*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['city'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('City'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step3']['city']) && !empty($storage['step3']['city']) ? $storage['step3']['city'] : ''),
		'#placeholder' => $this->t('City*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );

      $form['offer_step'] = array('#type' => 'hidden', '#value' => $this->step);
	  $form['prev'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Previous'),
		'#id' => 'edit-prev',
      	'#submit' => array('::prevSubmitForm'),
	  );
	  $form['next'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Next'),
		'#id' => 'edit-next',
      	'#submit' => array('::nextSubmitForm'),
	  );
    }

	//Step 4: Asking Price, Reason for Selling
    if($this->step == 4) {
      $storage = &$form_state->getStorage();

  	  $form['header_text_1'] = array('#markup' => '<h3 class="header-text">'.$this->t('We Buy Houses in Houston and More').'</h3>');
  	  $form['header_text_2'] = array('#markup' => '<h4 class="header-text">'.$this->t('Get started here').'</h4>');
  	  $form['header_text_3'] = array('#markup' => '<p class="header-text header-text1">'.$this->t('We pay cash in days you pick move out days.').'</p>');
  	  $form['header_text_4'] = array('#markup' => '<p class="header-text header-text2">'.$this->t('Your privacy is respected.').'</p>');
  	  $form['header_text_5'] = array('#markup' => '<div class="steps"><span class="step step1">'.$this->t('1').'</span><span class="step step2">'.$this->t('2').'</span><span class="step step3">'.$this->t('3').'</span><span class="step step4 active">'.$this->t('4').'</span></div>');
	  $form['asking_price'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Asking Price'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step4']['asking_price']) && !empty($storage['step4']['asking_price']) ? $storage['step4']['asking_price'] : ''),
		'#placeholder' => $this->t('Asking Price*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['selling_reason'] = array(
		'#type' => 'textarea',
		'#title' => $this->t('Reason for Selling'),
		'#rows' => 5,
		'#default_value' => (isset($storage['step4']['selling_reason']) && !empty($storage['step4']['selling_reason']) ? $storage['step4']['selling_reason'] : ''),
		'#placeholder' => $this->t('Reason for Selling*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );

      $form['offer_step'] = array('#type' => 'hidden', '#value' => $this->step);
	  $form['prev'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Previous'),
		'#id' => 'edit-prev',
      	'#submit' => array('::prevSubmitForm'),
	  );
	  $form['next'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Next'),
		'#id' => 'edit-next',
      	'#submit' => array('::nextSubmitForm'),
	  );
    }

	//Step 5: All Fields
    if($this->step == 5) {
      $storage = &$form_state->getStorage();

  	  $form['header_text_1'] = array('#markup' => '<h4 class="header-text">'.$this->t('Thank you for filling out the short form. If you could please provide us with this additional information it will be helpful in making the offer.').'</h4>');
	  $form['full_name'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Full Name'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step2']['full_name']) && !empty($storage['step2']['full_name']) ? $storage['step2']['full_name'] : ''),
		'#placeholder' => $this->t('Full Name*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['phone'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Phone'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step2']['phone']) && !empty($storage['step2']['phone']) ? $storage['step2']['phone'] : ''),
		'#placeholder' => $this->t('Phone*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['email'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Email'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step2']['email']) && !empty($storage['step2']['email']) ? $storage['step2']['email'] : ''),
		'#placeholder' => $this->t('Email*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['address'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Property Address'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step3']['address']) && !empty($storage['step3']['address']) ? $storage['step3']['address'] : ''),
		'#placeholder' => $this->t('Address*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['city'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('City'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step3']['city']) && !empty($storage['step3']['city']) ? $storage['step3']['city'] : ''),
		'#placeholder' => $this->t('City*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['state'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('State'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step5']['state']) && !empty($storage['step5']['state']) ? $storage['step5']['state'] : ''),
		'#placeholder' => $this->t('State'),
 		'#title_display' => 'invisible',
	  );
	  $form['zipcode'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Zipcode'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step1']['zipcode']) && !empty($storage['step1']['zipcode']) ? $storage['step1']['zipcode'] : ''),
		'#placeholder' => $this->t('Zip Code*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['asking_price'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Asking Price'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step4']['asking_price']) && !empty($storage['step4']['asking_price']) ? $storage['step4']['asking_price'] : ''),
		'#placeholder' => $this->t('Asking Price*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['selling_reason'] = array(
		'#type' => 'textarea',
		'#title' => $this->t('Reason for Selling'),
		'#rows' => 5,
		'#default_value' => (isset($storage['step4']['selling_reason']) && !empty($storage['step4']['selling_reason']) ? $storage['step4']['selling_reason'] : ''),
		'#placeholder' => $this->t('Reason for Selling*'),
 		'#title_display' => 'invisible',
 		'#required' => TRUE,
	  );
	  $form['mortgage_balance'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Current Mortgage Balance'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step5']['mortgage_balance']) && !empty($storage['step5']['mortgage_balance']) ? $storage['step5']['mortgage_balance'] : ''),
		'#placeholder' => $this->t('Current Mortgage Balance'),
 		'#title_display' => 'invisible',
	  );
	  $form['is_listed'] = array(
		'#type' => 'radios',
		'#title' => $this->t('Is your property listed with a Realtor?'),
		'#options' => array(
		  'Yes' => $this->t('Yes'),
		  'No' => $this->t('No'),
		),
		'#default_value' => (isset($storage['step5']['is_listed']) && !empty($storage['step5']['is_listed']) ? $storage['step5']['is_listed'] : ''),
 		'#title_display' => 'invisible',
	  );
	  $form['years_owned'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('How many years have you owned the property?'),
		'#size' => 40,
		'#maxlength' => 255,
		'#default_value' => (isset($storage['step5']['years_owned']) && !empty($storage['step5']['years_owned']) ? $storage['step5']['years_owned'] : ''),
		'#placeholder' => $this->t('How many years have you owned the property?'),
 		'#title_display' => 'invisible',
	  );
	  $form['how_quickly_sell'] = array(
		'#type' => 'select',
		'#title' => $this->t('How quickly will you sell it?'),
		'#options' => array(
		  '' => $this->t('Please select one'),
		  'Less than One Week' => $this->t('Less than One Week'),
		  '1 Week' => $this->t('1 Week'),
		  '2 Weeks' => $this->t('2 Weeks'),
		  '3 Weeks' => $this->t('3 Weeks'),
		  '1 Month' => $this->t('1 Month'),
		  '2 Months' => $this->t('2 Months'),
		  '3 Months' => $this->t('3 Months'),
		  'Longer' => $this->t('Longer'),
		),
		'#default_value' => (isset($storage['step5']['how_quickly_sell']) && !empty($storage['step5']['how_quickly_sell']) ? $storage['step5']['how_quickly_sell'] : ''),
		'#placeholder' => $this->t('How quickly will you sell it?'),
 		'#title_display' => 'invisible',
	  );
	  $form['learn_about_us'] = array(
		'#type' => 'select',
		'#title' => $this->t('How did you first learn about us?'),
		'#options' => array(
		  '' => $this->t('Please select one'),
		  'TV' => $this->t('TV'),
		  'Internet' => $this->t('Internet'),
		  'Mail' => $this->t('Mail'),
		  'Radio' => $this->t('Radio'),
		  'Word of mouth' => $this->t('Word of mouth'),
		  'Other' => $this->t('Other'),
		),
		'#default_value' => (isset($storage['step5']['learn_about_us']) && !empty($storage['step5']['learn_about_us']) ? $storage['step5']['learn_about_us'] : ''),
		'#placeholder' => $this->t('How did you first learn about us?'),
 		'#title_display' => 'invisible',
	  );
	  $form['comments'] = array(
		'#type' => 'textarea',
		'#title' => $this->t('Any Comments'),
		'#rows' => 5,
		'#default_value' => (isset($storage['step5']['comments']) && !empty($storage['step5']['comments']) ? $storage['step5']['comments'] : ''),
		'#placeholder' => $this->t('Comments'),
 		'#title_display' => 'invisible',
	  );

      $form['offer_step'] = array('#type' => 'hidden', '#value' => $this->step);
	  $form['save_offer'] = array(
		'#type' => 'submit',
		'#value' => $this->t('Get Offer in Few Days'),
		'#id' => 'edit-save-offer',
	  );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    return parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	//parent::submitForm($form, $form_state);
	$storage = $form_state->getStorage();
	$language = \Drupal::languageManager()->getCurrentLanguage()->getId();

	$offer_values = array(
	  'nid' => NULL,
	  'type' => 'ultimate_offers',
	  'title' => $form_state->getValue('full_name'),
	  'uid' => $this->account->id(),
	  'status' => TRUE,
	  'langcode' => $language,
	  'field_phone' => array($form_state->getValue('phone')),
	  'field_email' => array($form_state->getValue('email')),
	  'field_address' => array($form_state->getValue('address')),
	  'field_city' => array($form_state->getValue('city')),
	  'field_state' => array($form_state->getValue('state')),
	  'field_zipcode' => array($form_state->getValue('zipcode')),
	  'field_asking_price' => array($form_state->getValue('asking_price')),
	  'field_reason_for_selling' => [
		'summary' => '',
		'value' => $form_state->getValue('selling_reason'),
		'format' => 'plain_text',
	  ],
	  'field_current_mortgage_balance' => array($form_state->getValue('mortgage_balance')),
	  'field_is_your_proprety_listed' => array($form_state->getValue('is_listed')),
	  'field_how_many_years_have_you' => array($form_state->getValue('years_owned')),
	  'field_how_quickly_will_you_sell' => array($form_state->getValue('how_quickly_sell')),
	  'field_how_did_you_first_learn' => array($form_state->getValue('learn_about_us')),
	  'field_any_comments' => array($form_state->getValue('comments')),
	);
	//\Drupal::logger('quickguide')->notice(print_r($timeline_values, true));
	$offer_node = $this->manager->getStorage('node')->create($offer_values);
	$offer_node->save();

	//send email notification
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ultimate_offer';
    $key = 'offer_notification';

    $from = $form_state->getValue('email');
    $to = \Drupal::config('system.site')->get('mail');

	$params = array();
    $params['from'] = $from;
    $params['to'] = $to;

	$message = '';
	$message .= '<b>Full Name:</b> '.$form_state->getValue('full_name').PHP_EOL;
	$message .= 'Phone: '.$form_state->getValue('phone').PHP_EOL;
	$message .= 'Email: '.$form_state->getValue('email').PHP_EOL;
	$message .= 'Address: '.$form_state->getValue('address').PHP_EOL;
	$message .= 'City: '.$form_state->getValue('city').PHP_EOL;
	$message .= 'State: '.$form_state->getValue('state').PHP_EOL;
	$message .= 'Zipcode: '.$form_state->getValue('zipcode').PHP_EOL;
	$message .= 'Asking Price: '.$form_state->getValue('asking_price').PHP_EOL;
	$message .= 'Reason of Selling: '.$form_state->getValue('selling_reason').PHP_EOL;
	$message .= 'Current Mortgage Balance: '.$form_state->getValue('mortgage_balance').PHP_EOL;
	$message .= 'Is your property listed with a Realtor? '.$form_state->getValue('is_listed').PHP_EOL;
	$message .= 'How many years have you owned the property? '.$form_state->getValue('years_owned').PHP_EOL;
	$message .= 'How quickly will you sell it? '.$form_state->getValue('how_quickly_sell').PHP_EOL;
	$message .= 'How did you first learn about us? '.$form_state->getValue('learn_about_us').PHP_EOL;
	$message .= 'Comments: '.$form_state->getValue('comments').PHP_EOL;

    $params['message'] = $message;
    $params['title'] = 'Offer requested from: '.$form_state->getValue('full_name');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] != true) {
      $message = t('There was a problem sending email notification to @email.', array('@email' => $to));
      //drupal_set_message($message, 'error');
      \Drupal::logger('mail-log')->error($message);
    }
    else {
      $message = t('An email notification has been sent to @email ', array('@email' => $to));
      //drupal_set_message($message);
      \Drupal::logger('mail-log')->notice($message);
    }


	drupal_set_message($this->t('You have successfully submitted your request.'));

	$language = \Drupal::languageManager()->getCurrentLanguage()->getId();

	if($language == 'es') {
	  $page_url = Url::fromUri('internal:/es/node/115');
	}
	else {
	  $page_url = Url::fromUri('internal:/node/115');
	}
	$form_state->setRedirectUrl($page_url);
  }

  /**
   * {@inheritdoc}
   */
  public function prevSubmitForm(array &$form, FormStateInterface $form_state) {
	$storage = &$form_state->getStorage();
	$values = $form_state->getValues();
	$storage['step'.$this->step] = $values;
	$form_state->setStorage($storage);

    $form_state->setRebuild();
    $this->step--;
  }

  /**
   * {@inheritdoc}
   */
  public function nextSubmitForm(array &$form, FormStateInterface $form_state) {
	$storage = &$form_state->getStorage();
	$values = $form_state->getValues();
	$storage['step'.$this->step] = $values;
	$form_state->setStorage($storage);

    $form_state->setRebuild();
    $this->step++;
  }

}
