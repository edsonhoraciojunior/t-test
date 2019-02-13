<?php

namespace Drupal\taller_chat_user\Plugin\GraphQL\Mutations;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\UserInterface;
use Drupal\graphql\Plugin\GraphQL\Mutations\MutationPluginBase;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Youshido\GraphQL\Execution\ResolveInfo;

/**
 * Login User.
 *
 * @MutationPluginBase(
 *   id = "user_logout",
 *   name = "userLogout",
 *   type = "User",
 *   secure = false,
 *   nullable = false,
 *   schema_cache_tags = {"user_logout"},
 *   arguments = {
 *     "name" = "String"
 *   }
 * )
 */
class UserLogout extends MutationPluginBase implements ContainerFactoryPluginInterface {
  use DependencySerializationTrait;
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  // @TODO Create a Trait for the user's methods.

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityTypeManagerInterface $entityTypeManager,
    AccountInterface $currentUser
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;

    parent::__construct($configuration, $pluginId, $pluginDefinition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Logs in a user.
   *
   * @return \Drupal\user\UserInterface
   *   The newly logged user.
   */
  public function resolve($value, array $args, ResolveInfo $info) {
    return $this->logout(\Drupal::request(), $args);
  }

  /**
   * Logs out a user.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @param $credentials
   *   The credentials to try logging in with.
   *
   * @return logout
   */
  public function logout(Request $request, $credentials) {
    if (!$this->currentUser->isAuthenticated()) {
      throw new BadRequestHttpException($this->t('The user is logged out.'));
    }

    return $this->logout();
  }
}
