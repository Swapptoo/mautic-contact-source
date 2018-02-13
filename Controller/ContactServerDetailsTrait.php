<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactServerBundle\Controller;

use Mautic\CoreBundle\Entity\AuditLogRepository;
use Mautic\CoreBundle\Helper\Chart\ChartQuery;
use Mautic\CoreBundle\Helper\Chart\LineChart;
use Mautic\CoreBundle\Model\AuditLogModel;
use MauticPlugin\MauticContactServerBundle\Entity\ContactServer;
use MauticPlugin\MauticContactServerBundle\Model\ContactServerModel;

/**
 * Trait ContactServerDetailsTrait
 * @package MauticPlugin\MauticContactServerBundle\Controller
 */
trait ContactServerDetailsTrait
{

    /**
     * @param array $contactServers
     * @param array|null $filters
     * @param array|null $orderBy
     * @param int $page
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException
     */
    protected function getAllEngagements(
        array $contactServers,
        array $filters = null,
        array $orderBy = null,
        $page = 1,
        $limit = 25
    ) {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'mautic.plugin.timeline.filters',
                [
                    'search' => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }
        $filters = $this->sanitizeEventFilter(InputHelper::clean($this->request->get('filters', [])));

        if (null == $orderBy) {
            if (!$session->has('mautic.plugin.timeline.orderby')) {
                $session->set('mautic.plugin.timeline.orderby', 'timestamp');
                $session->set('mautic.plugin.timeline.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('mautic.plugin.timeline.orderby'),
                $session->get('mautic.plugin.timeline.orderbydir'),
            ];
        }

        // prepare result object
        $result = [
            'events' => [],
            'filters' => $filters,
            'order' => $orderBy,
            'types' => [],
            'total' => 0,
            'page' => $page,
            'limit' => $limit,
            'maxPages' => 0,
        ];

        // get events for each contact
        foreach ($contactServers as $contactServer) {
            //  if (!$contactServer->getEmail()) continue; // discard contacts without email

            /** @var ContactServerModel $model */
            $model = $this->getModel('contactServer');
            $engagements = $model->getEngagements($contactServer, $filters, $orderBy, $page, $limit);
            $events = $engagements['events'];
            $types = $engagements['types'];

            // inject contactServer into events
            foreach ($events as &$event) {
                $event['contactServerId'] = $contactServer->getId();
                $event['contactServerEmail'] = $contactServer->getEmail();
                $event['contactServerName'] = $contactServer->getName() ? $contactServer->getName(
                ) : $contactServer->getEmail();
            }

            $result['events'] = array_merge($result['events'], $events);
            $result['types'] = array_merge($result['types'], $types);
            $result['total'] += $engagements['total'];
        }

        $result['maxPages'] = ($limit <= 0) ? 1 : round(ceil($result['total'] / $limit));

        usort($result['events'], [$this, 'cmp']); // sort events by

        // now all events are merged, let's limit to   $limit
        array_splice($result['events'], $limit);

        $result['total'] = count($result['events']);

        return $result;
    }

    /**
     * Makes sure that the event filter array is in the right format.
     *
     * @param mixed $filters
     *
     * @return array
     *
     * @throws InvalidArgumentException if not an array
     */
    public function sanitizeEventFilter($filters)
    {
        if (!is_array($filters)) {
            throw new \InvalidArgumentException('filters parameter must be an array');
        }

        if (!isset($filters['search'])) {
            $filters['search'] = '';
        }

        if (!isset($filters['includeEvents'])) {
            $filters['includeEvents'] = [];
        }

        if (!isset($filters['excludeEvents'])) {
            $filters['excludeEvents'] = [];
        }

        return $filters;
    }

    /**
     * Get a list of places for the contactServer based on IP location.
     *
     * @param ContactServer $contactServer
     *
     * @return array
     */
    protected function getPlaces(ContactServer $contactServer)
    {
        // Get Places from IP addresses
        $places = [];
        if ($contactServer->getIpAddresses()) {
            foreach ($contactServer->getIpAddresses() as $ip) {
                if ($details = $ip->getIpDetails()) {
                    if (!empty($details['latitude']) && !empty($details['longitude'])) {
                        $name = 'N/A';
                        if (!empty($details['city'])) {
                            $name = $details['city'];
                        } elseif (!empty($details['region'])) {
                            $name = $details['region'];
                        }
                        $place = [
                            'latLng' => [$details['latitude'], $details['longitude']],
                            'name' => $name,
                        ];
                        $places[] = $place;
                    }
                }
            }
        }

        return $places;
    }

    /**
     * @param ContactServer $contactServer
     * @param \DateTime|null $fromDate
     * @param \DateTime|null $toDate
     *
     * @return mixed
     */
    protected function getEngagementData(
        ContactServer $contactServer,
        \DateTime $fromDate = null,
        \DateTime $toDate = null
    ) {
        $translator = $this->get('translator');

        if (null == $fromDate) {
            $fromDate = new \DateTime('first day of this month 00:00:00');
            $fromDate->modify('-6 months');
        }
        if (null == $toDate) {
            $toDate = new \DateTime();
        }

        $lineChart = new LineChart(null, $fromDate, $toDate);
        $chartQuery = new ChartQuery($this->getDoctrine()->getConnection(), $fromDate, $toDate);

        /** @var ContactServerModel $model */
        $model = $this->getModel('contactServer');
        $engagements = $model->getEngagementCount($contactServer, $fromDate, $toDate, 'm', $chartQuery);
        $lineChart->setDataset(
            $translator->trans('mautic.contactServer.graph.line.all_engagements'),
            $engagements['byUnit']
        );

        $pointStats = $chartQuery->fetchTimeData(
            'contactServer_points_change_log',
            'date_added',
            ['contactServer_id' => $contactServer->getId()]
        );
        $lineChart->setDataset($translator->trans('mautic.contactServer.graph.line.points'), $pointStats);

        return $lineChart->render();
    }

    /**
     * @param ContactServer $contactServer
     * @param array|null $filters
     * @param array|null $orderBy
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    protected function getAuditlogs(
        ContactServer $contactServer,
        array $filters = null,
        array $orderBy = null,
        $page = 1,
        $limit = 25
    ) {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'mautic.contactServer.'.$contactServer->getId().'.auditlog.filters',
                [
                    'search' => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }

        if (null == $orderBy) {
            if (!$session->has('mautic.contactServer.'.$contactServer->getId().'.auditlog.orderby')) {
                $session->set('mautic.contactServer.'.$contactServer->getId().'.auditlog.orderby', 'al.dateAdded');
                $session->set('mautic.contactServer.'.$contactServer->getId().'.auditlog.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('mautic.contactServer.'.$contactServer->getId().'.auditlog.orderby'),
                $session->get('mautic.contactServer.'.$contactServer->getId().'.auditlog.orderbydir'),
            ];
        }

        // Audit Log
        /** @var AuditLogModel $auditlogModel */
        $auditlogModel = $this->getModel('core.auditLog');

        $logs = $auditlogModel->getLogForObject('contactserver', $contactServer->getId(), $contactServer->getDateAdded());
        $logCount = count($logs);

        $types = [
            'delete' => $this->translator->trans('mautic.contactServer.event.delete'),
            'create' => $this->translator->trans('mautic.contactServer.event.create'),
            'identified' => $this->translator->trans('mautic.contactServer.event.identified'),
            'ipadded' => $this->translator->trans('mautic.contactServer.event.ipadded'),
            'merge' => $this->translator->trans('mautic.contactServer.event.merge'),
            'update' => $this->translator->trans('mautic.contactServer.event.update'),
        ];

        return [
            'events' => $logs,
            'filters' => $filters,
            'order' => $orderBy,
            'types' => $types,
            'total' => $logCount,
            'page' => $page,
            'limit' => $limit,
            'maxPages' => ceil($logCount / $limit),
        ];
    }

    /**
     * @param ContactServer $contactServer
     * @param array|null $filters
     * @param array|null $orderBy
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    protected function getEngagements(
        ContactServer $contactServer,
        array $filters = null,
        array $orderBy = null,
        $page = 1,
        $limit = 25
    ) {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'mautic.contactServer.'.$contactServer->getId().'.timeline.filters',
                [
                    'search' => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }

        if (null == $orderBy) {
            if (!$session->has('mautic.contactServer.'.$contactServer->getId().'.timeline.orderby')) {
                $session->set('mautic.contactServer.'.$contactServer->getId().'.timeline.orderby', 'timestamp');
                $session->set('mautic.contactServer.'.$contactServer->getId().'.timeline.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('mautic.contactServer.'.$contactServer->getId().'.timeline.orderby'),
                $session->get('mautic.contactServer.'.$contactServer->getId().'.timeline.orderbydir'),
            ];
        }
        /** @var ContactServerModel $model */
        $model = $this->getModel('contactServer');

        return $model->getEngagements($contactServer, $filters, $orderBy, $page, $limit);
    }

    /**
     * @param ContactServer $contactServer
     *
     * @return array
     */
    protected function getScheduledCampaignEvents(ContactServer $contactServer)
    {
        // Upcoming events from Campaign Bundle
        /** @var \Mautic\CampaignBundle\Entity\ContactServerEventLogRepository $contactServerEventLogRepository */
        $contactServerEventLogRepository = $this->getDoctrine()->getManager()->getRepository(
            'MauticCampaignBundle:ContactServerEventLog'
        );

        return $contactServerEventLogRepository->getUpcomingEvents(
            [
                'contactServer' => $contactServer,
                'eventType' => ['action', 'condition'],
            ]
        );
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function cmp($a, $b)
    {
        if ($a['timestamp'] === $b['timestamp']) {
            return 0;
        }

        return ($a['timestamp'] < $b['timestamp']) ? +1 : -1;
    }
}
