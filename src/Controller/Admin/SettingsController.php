<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Controller\Admin;

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
        $webpFormDataHandler = $this->getWebpFormHandler();

        /** @var FormInterface<string, mixed> $generalForm */
        $generalForm = $generalFormDataHandler->getForm();
        $webpForm = $webpFormDataHandler->getForm();

        return $this->render('@Modules/is_themecore/views/templates/back/components/layouts/settings.html.twig', [
            'general_form' => $generalForm->createView(),
            'webp_form' => $webpForm->createView(),
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
    public function processWebpFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getWebpFormHandler(),
            'Webp'
        );
    }

    /**
     * @DemoRestricted(redirectRoute="is_themecore_module_settings")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \LogicException
     */
    public function processWebpEraseImages(Request $request)
    {
        $time_start = microtime(true);
        $eraser = $this->get('oksydan.module.is_themecore.core.webp.webp_files_eraser');

        switch ($request->get('type')) {
            case 'all':
                $eraser->setQuery(_PS_ROOT_DIR_);
                break;
            case 'product':
                $eraser->setQuery(_PS_PROD_IMG_DIR_);
                break;
            case 'module':
                $eraser->setQuery(_PS_MODULE_DIR_);
                break;
            case 'cms':
                $eraser->setQuery(_PS_IMG_DIR_ . 'cms/');
                break;
            case 'themes':
                $eraser->setQuery(_PS_ROOT_DIR_ . '/themes/');
                break;
            default:
                $eraser->setQuery(_PS_ROOT_DIR_);
                break;
        }

        $eraser->eraseFiles();

        $time_end = microtime(true);
        $execution_time = round($time_end - $time_start, 2);

        $this->addFlash('success', $this->trans('%1$s - webp images has been erased successfully in %2$ss', 'Modules.isthemecore.Admin', [$eraser->getFilesCount(), $execution_time]));

        return $this->redirectToRoute('is_themecore_module_settings');
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
    private function processForm(Request $request, FormHandlerInterface $formHandler)
    {
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $saveErrors = $formHandler->save($data);

                if (!empty($data['webp_enabled'])) {
                    $generator = $this->get('oksydan.module.is_themecore.core.htaccess.htaccess_generator');

                    $generator->generate((bool) $data['webp_enabled']);
                    $generator->writeFile();
                }

                if (0 === count($saveErrors)) {
                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
                } else {
                    $this->flashErrors($saveErrors);
                }
            }

            $formErrors = [];
            foreach ($form->getErrors(true) as $error) {
                $formErrors[] = $error->getMessage();
            }
            $this->flashErrors($formErrors);
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

    /**
     * @return FormHandlerInterface
     */
    private function getWebpFormHandler()
    {
        /** @var FormHandlerInterface */
        $formDataHandler = $this->get('oksydan.module.is_themecore.form.settings.webp_form_data_handler');

        return $formDataHandler;
    }
}
