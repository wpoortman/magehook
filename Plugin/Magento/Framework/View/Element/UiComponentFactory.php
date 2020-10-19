<?php
/**
 * @author MageHook <info@magehook.com>
 * @package MageHook_Hook
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MageHook\Hook\Plugin\Magento\Framework\View\Element;

use MageHook\Hook\Registry\ActionHookEventSelect as ActionHookEventSelectRegistry;
use MageHook\Hook\Ui\DataProvider\Form\Webhook as HookFormDataProvider;
use Magento\Framework\Config\DataInterface;
use Magento\Framework\Config\DataInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentFactory as Subject;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form as FormComponent;
use Psr\Log\LoggerInterface;

/**
 * Class UiComponentFactory
 *
 * @package MageHook\Hook\Plugin\Magento\Framework\View\Element
 */
class UiComponentFactory
{
    public const HOOK_FORM_COMPONENT = 'magehook_webhook_form';

    /** @var ActionHookEventSelectRegistry $actionHookEventSelectRegistry */
    protected $actionHookEventSelectRegistry;

    /** @var LoggerInterface $loggerInterface */
    protected $loggerInterface;

    /** @var DataInterfaceFactory $configFactory */
    protected $configFactory;

    /**
     * UiComponentFactory constructor.
     *
     * @param ActionHookEventSelectRegistry $actionHookEventSelectRegistry
     * @param LoggerInterface               $loggerInterface
     * @param DataInterfaceFactory          $configFactory
     */
    public function __construct(
        ActionHookEventSelectRegistry $actionHookEventSelectRegistry,
        LoggerInterface $loggerInterface,
        DataInterfaceFactory $configFactory
    ) {
        $this->actionHookEventSelectRegistry = $actionHookEventSelectRegistry;
        $this->loggerInterface = $loggerInterface;
        $this->configFactory = $configFactory;
    }

    /**
     * Injects the selection event options UI Component
     * configuration into the form UI Component. A default
     * will be set if no ../adminhtml/ui_component/*.xml exists.
     *
     * Note: Extensive research has been carried out for a solution
     * to dynamic loading of UI components.
     *
     * @todo Should maybe work like the Comments History (tab) AJAX-load works within a admin Sales Order View
     *
     * @param Subject              $subject
     * @param UiComponentInterface $component
     *
     * @return UiComponentInterface
     */
    public function afterCreate(Subject $subject, UiComponentInterface $component): UiComponentInterface
    {
        if ($this->actionHookEventSelectRegistry->has() && $component->getName() === self::HOOK_FORM_COMPONENT) {
            /** @var string $identifier */
            $identifier = $this->actionHookEventSelectRegistry->get();
            /** @var DataInterface $componentData */
            $componentData = $this->configFactory->create(['componentName' => $identifier]);

            if ($componentData->get($identifier) === null) {
                $identifier = $this->actionHookEventSelectRegistry->getDefault(true);
            }

            /** @var array $childComponents */
            $childComponents = $component->getChildComponents();

            if (isset($childComponents[HookFormDataProvider::AREA_OPTIONS])) {
                try {
                    /** @var FormComponent $optionsContainer */
                    $optionsContainer = $subject->create($identifier);
                    /** @var FormComponent\Fieldset $parentContainer */
                    $parentContainer = $childComponents[HookFormDataProvider::AREA_OPTIONS];

                    foreach ($optionsContainer->getChildComponents() as $child) {
                        $parentContainer->addComponent($child->getName(), $child);
                    }
                } catch (LocalizedException $exception) {
                    $this->loggerInterface->error($exception->getMessage());
                }
            }
        }

        return $component;
    }
}
