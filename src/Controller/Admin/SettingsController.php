<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Controller\Admin;

use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsController
 *
 * @ModuleActivated(moduleName="is_themecore", redirectRoute="admin_module_manage")
 */
class SettingsController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     message="You do not have permission to access this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $generalFormDataHandler = $this->getGeneralFormHandler();

        /** @var FormInterface<string, mixed> $generalForm */
        $generalForm = $generalFormDataHandler->getForm();

        return $this->render('@Modules/is_themecore/views/templates/back/components/layouts/settings.html.twig', [
            'general_form' => $generalForm->createView(),
        ]);
    }

    /**
     * @AdminSecurity(
     *      "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *      message="You do not have permission to update this.",
     *      redirectRoute="is_themecore_module_settings"
     * )
     *
     * @DemoRestricted(redirectRoute="is_themecore_module_settings")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \LogicException
     */
    public function processGeneralFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getGeneralFormHandler(),
            'General'
        );
    }

    /**
     * Process form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return RedirectResponse
     */
    private function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionthemecore' . get_class($this) . 'PostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook(
            'actionthemecore' . get_class($this) . 'PostProcessBefore',
            ['controller' => $this]
        );

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if (is_array($data)) {
                $saveErrors = $formHandler->save($data);

                if (0 === count($saveErrors)) {
                    $this->getCommandBus()->handle(
                        new UpdateTabStatusByClassNameCommand(
                            'AdminShopGroup',
                            $this->configuration->getBoolean('PS_MULTISHOP_FEATURE_ACTIVE')
                        )
                    );

                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                } else {
                    $this->flashErrors($saveErrors);
                }
            }
        }

        return $this->redirectToRoute('is_themecore_module_settings');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getGeneralFormHandler()
    {
        /** @var FormHandlerInterface */
        $formDataHandler = $this->get('oksydan.module.is_themecore.form.settings.general_form_data_handler');

        return $formDataHandler;
    }
}
