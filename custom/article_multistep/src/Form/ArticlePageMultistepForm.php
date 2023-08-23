<?php

namespace Drupal\article_multistep\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a multistep form for the ArticlePage content type.
 */
class ArticlePageMultistepForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ArticlePageMultistepForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   *
   * Use Dependency Injection to inject needed services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Returns a unique string identifying the form.
   */
  public function getFormId() {
    return 'article_page_multistep_form';
  }

  /**
   * {@inheritdoc}
   *
   * Define form structure and elements.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Determine the current step. Default to 1 if not set.
    $step = $form_state->get('step') ?? 1;

    // Define form fields based on the current step.
    switch ($step) {
      case 1:
        $form['field_one'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Field One'),
        ];
        break;

      case 2:
        $form['field_two'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Field Two'),
        ];
        break;
    }

    // Define form actions (e.g., buttons) based on the current step.
    $form['actions'] = ['#type' => 'actions'];
    if ($step == 1) {
      $form['actions']['next'] = [
        '#type' => 'submit',
        '#value' => $this->t('Next'),
        '#submit' => ['::nextStep'],
      ];
    } else {
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
    }

    return $form;
  }

  /**
   * Handler for the 'next' action.
   *
   * When moving to the next step, store the entered value for 'field_one'
   * to ensure it's available on the final submit.
   */
  public function nextStep(array &$form, FormStateInterface $form_state) {
    $form_state->set('field_one', $form_state->getValue('field_one'));

    // Move to the second step.
    $form_state->set('step', 2);
    // Rebuild the form to reflect changes.
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   *
   * The final submit handler. This method saves the data as a node.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the value for 'field_one' from form state or direct input.
    $field_one_value = $form_state->has('field_one') ? $form_state->get('field_one') : $form_state->getValue('field_one');
    $field_two_value = $form_state->getValue('field_two');

    // Utilize the entity type manager to work with nodes.
    $node_storage = $this->entityTypeManager->getStorage('node');

    // Load existing node if nid is available, otherwise create a new article node.
    if ($nid = $form_state->getValue('nid')) {
      $node = $node_storage->load($nid);
    } else {
      $node = $node_storage->create(['type' => 'article']);
    }

    // Set the title and field values for the node.
    $node->setTitle('Test title');
    $node->set('field_one', $field_one_value);
    $node->set('field_two', $field_two_value);

    // Save the node.
    $node->save();

    // Redirect user to the newly created/updated node.
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
  }
}
