<?php

namespace Drupal\owners_details\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "owners_details",
 *   admin_label = @Translation("Owners details"),
 * )
 */
class OwnersDetails extends BlockBase {

    public $user;
    public $user_id;

    public function __construct() {
        $userCurrent = \Drupal::currentUser();
        $this->user_id = $userCurrent->id();
        $this->user = \Drupal\user\Entity\User::load($userCurrent->id());

    }

    /**
     * {@inheritdoc}
     */
    public function build() {

        return [
            '#markup' => '<p>'.$this->t('Your name') . ': ' . $this->getUserName() .' <a href="/user/'.$this->user_id.'/edit">edit</a></p>'.
                '<p>'.$this->t('Organization') . ': ' . $this->getUserOrganizations() .' <a href="/user/'.$this->user_id.'/edit">edit</a></p>'.
                '<p>'.$this->t('Count products') . ': ' .  $this->getCountUserItems(). ' <a href="/cart/">see</a></p>',
        ];
    }

    protected function getUserName() {

        return $this->user->getUsername();
    }

    protected function getUserOrganizations() {

        return $this->user->field_nazvanie_organizacii->entity->label();
    }



    protected function getCountUserItems() {
        $store_id = 1;
        $order_type = 'default';
        $cart_manager = \Drupal::service('commerce_cart.cart_manager');
        $cart_provider = \Drupal::service('commerce_cart.cart_provider');
        $entity_manager = \Drupal::entityManager();
        $store = $entity_manager->getStorage('commerce_store')->load($store_id);
        $cart = $cart_provider->getCart($order_type, $store);
        return count($cart-> getItems());

    }

    protected function getUserOrganization($nid) {
        $node = null;
        if (!empty($nid)) {
            $node = Node::load($nid);
            // the rest of your code here
        }
        return $node;
    }


    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account) {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $config = $this->getConfiguration();

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->configuration['owners_details_settings'] = $form_state->getValue('owners_details_settings');
    }
    
}