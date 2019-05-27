<?php
/**
* @file
* Contains \Drupal\ultimate_offer\Plugin\Block\UltimateOfferBlock.
*/

namespace Drupal\ultimate_offer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
* Provides Ultimate Offer Block.
*
* @Block(
* id = "ultimate_offer_block",
* admin_label = @Translation("Ultimate Offer Block"),
* category = @Translation("Blocks")
* )
*/
class UltimateOfferBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $return_as_object = false) {
    //return $account->hasPermission('access content');
	if($account->hasPermission('access ultimate offer')) {
	  return AccessResult::allowed();
	}
	return AccessResult::forbidden();
  }


  /**
  * {@inheritdoc}
  */
  public function build() {
	$build = array();

	$build['#markup'] = '';
	$build['form'] = \Drupal::formBuilder()->getForm('Drupal\ultimate_offer\Form\UltimateOfferForm');

	return $build;
  }




}
