<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\WorkflowTemplate;
use MailPoet\Automation\Integrations\MailPoet\Templates\WorkflowBuilder;

class PremiumWorkflowTemplates {


  private $builder;

  public function __construct(
    WorkflowBuilder $builder
  ) {
    $this->builder = $builder;
  }

  /**
   * @param WorkflowTemplate[] $templates
   * @return WorkflowTemplate[]
   */
  public function integrate(array $templates): array {
    return array_merge($templates, $this->templates());
  }

  /**
   * @return WorkflowTemplate[]
   */
  public function templates(): array {

    $welcomeEmailSequence = new WorkflowTemplate(
      'welcome-email-sequence',
      WorkflowTemplate::CATEGORY_WELCOME,
      "Automation template description is going to be here. Let's describe a lot of interesting ideas which incorporated into this beautiful and useful template",
      $this->builder->createFromSequence(
        __('Welcome email sequence', 'mailpoet-premium'),
        [
          'mailpoet:someone-subscribes',
          'core:delay',
          'mailpoet:send-email',
          'core:delay',
          'mailpoet:send-email',
        ]
      )
    );
    $advancedWelcomeEmailSequence = new WorkflowTemplate(
      'advanced-welcome-email-sequence',
      WorkflowTemplate::CATEGORY_WELCOME,
      "Automation template description is going to be here. Let's describe a lot of interesting ideas which incorporated into this beautiful and useful template",
      $this->builder->createFromSequence(
        __('Advanced welcome email sequence', 'mailpoet-premium'),
        [
          'mailpoet:someone-subscribes',
          'core:delay',
          'mailpoet:send-email',
          'core:delay',
          'mailpoet:send-email',
          'core:delay',
          'mailpoet:send-email',
        ]
      )
    );

    return [
      $welcomeEmailSequence,
      $advancedWelcomeEmailSequence,
    ];
  }
}
