<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactSourceBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactSourceController.
 */
class ContactSourceController extends FormController
{
    use ContactSourceDetailsTrait;

    public function __construct()
    {
        $this->setStandardParameters(
            'contactsource',
            'plugin:contactsource:items',
            'mautic_contactsource',
            'mautic_contactsource',
            'mautic.contactsource',
            'MauticContactSourceBundle:ContactSource',
            null,
            'contactsource'
        );
    }

    /**
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($page = 1)
    {
        // When the user inserts a numeric value, assume they want to find the entity by ID.
        $session = $this->get('session');
        $search  = $this->request->get('search', $session->get('mautic.'.$this->getSessionBase().'.filter', ''));
        if (isset($search) && is_numeric(trim($search))) {
            $search          = '%'.trim($search).'% OR ids:'.trim($search);
            $query           = $this->request->query->all();
            $query['search'] = $search;
            $this->request   = $this->request->duplicate($query);
            $session->set('mautic.'.$this->getSessionBase().'.filter', $search);
        } elseif (false === strpos($search, '%') && strlen($search) > 0 && false === strpos($search, 'OR ids:')) {
            $search          = '%'.trim($search, ' \t\n\r\0\x0B"%').'%';
            $search          = strpos($search, ' ') ? '"'.$search.'"' : $search;
            $query           = $this->request->query->all();
            $query['search'] = $search;
            $this->request   = $this->request->duplicate($query);
            $session->set('mautic.'.$this->getSessionBase().'.filter', $search);
        }

        return parent::indexStandard($page);
    }

    /**
     * Generates new form and processes post data.
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function newAction()
    {
        return parent::newStandard();
    }

    /**
     * Generates edit form and processes post data.
     *
     * @param      $objectId
     * @param bool $ignorePost
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function editAction($objectId, $ignorePost = false)
    {
        return parent::editStandard($objectId, $ignorePost);
    }

    /**
     * Displays details on a ContactSource.
     *
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function viewAction($objectId)
    {
        return parent::viewStandard($objectId, 'contactsource', 'plugin.contactsource');
    }

    /**
     * Clone an entity.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        return parent::cloneStandard($objectId);
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        return parent::deleteStandard($objectId);
    }

    /**
     * Deletes a group of entities.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        return parent::batchDeleteStandard();
    }

    /**
     * @param $args
     * @param $view
     *
     * @return array
     */
    public function customizeViewArguments($args, $view)
    {
        if ('view' == $view) {
            $session = $this->get('session');

            /** @var \MauticPlugin\MauticContactSourceBundle\Entity\ContactSource $item */
            $item = $args['viewParameters']['item'];

            // For line graphs in the view
            if ('POST' == $this->request->getMethod() && $this->request->request->has('sourcechartfilter')) {
                $chartFilterValues = $this->request->get('sourcechartfilter');
            } else {
                $chartFilterValues = $session->get('mautic.contactsource.'.$item->getId().'.sourcechartfilter')
                    ? $session->get('mautic.contactsource.'.$item->getId().'.sourcechartfilter')
                    : [
                        'date_from' => $this->get('mautic.helper.core_parameters')->getParameter('default_daterange_filter', 'midnight -1 month'),
                        'date_to'   => 'midnight tomorrow -1 second',
                        'type'      => '',
                    ];
            }

            if ($this->request->query->has('campaign')) {
                $chartFilterValues['campaign'] = $this->request->query->get('campaign');
            }
            if (!isset($chartFilterValues['campaign']) || empty($chartFilterValues['campaign'])) {
                $chartFilterValues['campaign'] = null;
            }

            $session->set('mautic.contactsource.'.$item->getId().'.sourcechartfilter', $chartFilterValues);

            $chartFilterForm = $this->get('form.factory')->create(
                'sourcechartfilter',
                $chartFilterValues,
                [
                    'action' => $this->generateUrl(
                        'mautic_contactsource_action',
                        [
                            'objectAction' => 'view',
                            'objectId'     => $item->getId(),
                        ]
                    ),
                ]
            );

            /** @var \MauticPlugin\MauticContactSourceBundle\Model\ContactSourceModel $model */
            $model = $this->getModel('contactsource');

            // fix dates
            $dateFrom = new \DateTime($chartFilterValues['date_from']);
            $dateTo   = new \DateTime($chartFilterValues['date_to']);

            if (in_array($chartFilterValues['type'], ['All Events', null, ''])) {
                $stats = $model->getStats(
                    $item,
                    null,
                    $dateFrom,
                    $dateTo,
                    $chartFilterValues['campaign']
                );
            } else {
                $stats = $model->getStatsByCampaign(
                    $item,
                    null,
                    $chartFilterValues['type'],
                    $dateFrom,
                    $dateTo,
                    $chartFilterValues['campaign']
                );
            }
            $limits = [];
            try {
                $limits = $model->evaluateAllCampaignLimits($item);
            } catch (\Exception $e) {
            }

            $args['viewParameters']['auditlog']        = $this->getAuditlogs($item);
            $args['viewParameters']['stats']           = $stats;
            $args['viewParameters']['chartFilterForm'] = $chartFilterForm->createView();
            $args['viewParameters']['limits']          = $limits;
        }

        return $args;
    }

    /**
     * @param array $args
     * @param       $action
     *
     * @return array
     */
    protected function getPostActionRedirectArguments(array $args, $action)
    {
        $updateSelect = ('POST' == $this->request->getMethod())
            ? $this->request->request->get('contactsource[updateSelect]', false, true)
            : $this->request->get(
                'updateSelect',
                false
            );
        if ($updateSelect) {
            switch ($action) {
                case 'new':
                case 'edit':
                    $passthrough             = $args['passthroughVars'];
                    $passthrough             = array_merge(
                        $passthrough,
                        [
                            'updateSelect' => $updateSelect,
                            'id'           => $args['entity']->getId(),
                            'name'         => $args['entity']->getName(),
                        ]
                    );
                    $args['passthroughVars'] = $passthrough;
                    break;
            }
        }

        return $args;
    }

    /**
     * @return array
     */
    protected function getEntityFormOptions()
    {
        $updateSelect = ('POST' == $this->request->getMethod())
            ? $this->request->request->get('contactsource[updateSelect]', false, true)
            : $this->request->get(
                'updateSelect',
                false
            );
        if ($updateSelect) {
            return ['update_select' => $updateSelect];
        }
    }

    /**
     * Return array of options update select response.
     *
     * @param string $updateSelect HTML id of the select
     * @param object $entity
     * @param string $nameMethod   name of the entity method holding the name
     * @param string $groupMethod  name of the entity method holding the select group
     *
     * @return array
     */
    protected function getUpdateSelectParams(
        $updateSelect,
        $entity,
        $nameMethod = 'getName',
        $groupMethod = 'getLanguage'
    ) {
        $options = [
            'updateSelect' => $updateSelect,
            'id'           => $entity->getId(),
            'name'         => $entity->$nameMethod(),
        ];

        return $options;
    }
}
