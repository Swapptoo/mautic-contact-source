<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (isset($tmpl) && 'index' == $tmpl) {
    $view->extend('MauticContactSourceBundle:Timeline:index.html.php');
}

$baseUrl = $view['router']->path(
    'mautic_contactsource_timeline_action',
    [
        'contactSourceId' => $contactSource->getId(),
    ]
);
?>

<!-- timeline -->
<div class="table-responsive">
    <table class="table table-hover table-bordered" id="contactsource-timeline" style="z-index: 2; position: relative;">
        <thead>
        <tr>
            <th class="timeline-icon">
                <a class="btn btn-sm btn-nospin btn-default" data-activate-details="all" data-toggle="tooltip"
                   title="<?php echo $view['translator']->trans(
                       'mautic.contactsource.timeline.toggle_all_details'
                   ); ?>">
                    <span class="fa fa-fw fa-level-down"></span>
                </a>
            </th>
            <?php
            echo $view->render(
                'MauticCoreBundle:Helper:tableheader.html.php',
                [
                    'orderBy'    => 'message',
                    'text'       => 'mautic.contactsource.timeline.message',
                    'class'      => 'timeline-name',
                    'sessionVar' => 'contactsource.'.$contactSource->getId().'.timeline',
                    'baseUrl'    => $baseUrl,
                    'target'     => '#timeline-table',
                ]
            );

            echo $view->render(
                'MauticCoreBundle:Helper:tableheader.html.php',
                [
                    'orderBy'    => 'contactId',
                    'text'       => 'mautic.contactsource.timeline.contact_id',
                    'class'      => 'visible-md visible-lg timeline-contact-id',
                    'sessionVar' => 'contactsource.'.$contactSource->getId().'.timeline',
                    'baseUrl'    => $baseUrl,
                    'target'     => '#timeline-table',
                ]
            );

            echo $view->render(
                'MauticCoreBundle:Helper:tableheader.html.php',
                [
                    'orderBy'    => 'eventType',
                    'text'       => 'mautic.contactsource.timeline.event_type',
                    'class'      => 'visible-md visible-lg timeline-type',
                    'sessionVar' => 'contactsource.'.$contactSource->getId().'.timeline',
                    'baseUrl'    => $baseUrl,
                    'target'     => '#timeline-table',
                ]
            );

            echo $view->render(
                'MauticCoreBundle:Helper:tableheader.html.php',
                [
                    'orderBy'    => 'timestamp',
                    'text'       => 'mautic.contactsource.timeline.event_timestamp',
                    'class'      => 'visible-md visible-lg timeline-timestamp',
                    'sessionVar' => 'contactsource.'.$contactSource->getId().'.timeline',
                    'baseUrl'    => $baseUrl,
                    'target'     => '#timeline-table',
                ]
            );
            ?>
        </tr>
        <tbody>
        <?php foreach ($events['events'] as $counter => $event): ?>
            <?php
            $counter += 1; // prevent 0
            $icon       = (isset($event['icon'])) ? $event['icon'] : 'fa-history';
            $eventLabel = (isset($event['eventLabel'])) ? $event['eventLabel'] : $event['eventType'];
            $message    = (isset($event['message'])) ? $event['message'] : null;
            $contact    = (isset($event['contactId'])) ? "<a href=\"/s/contacts/view/{$event['contactId']}\" data-toggle=\"ajax\">{$event['contactId']}</a>" : null;
            if (is_array($eventLabel)):
                $linkType   = empty($eventLabel['isExternal']) ? 'data-toggle="ajax"' : 'target="_new"';
                $eventLabel = isset($eventLabel['href']) ? "<a href=\"{$eventLabel['href']}\" $linkType>{$eventLabel['label']}</a>" : "{$eventLabel['label']}";
            endif;

            $details = '';
            if (isset($event['contentTemplate']) && $view->exists($event['contentTemplate'])):
                $details = trim(
                    $view->render($event['contentTemplate'], ['event' => $event, 'contactSource' => $contactSource])
                );
            endif;

            $rowStripe = (0 === $counter % 2) ? ' timeline-row-highlighted' : '';
            ?>
            <tr class="timeline-row<?php echo $rowStripe; ?><?php if (!empty($event['featured'])) {
                echo ' timeline-featured';
            } ?>">
                <td class="timeline-icon">
                    <a href="javascript:void(0);" data-activate-details="<?php echo $counter; ?>"
                       class="btn btn-sm btn-nospin btn-default<?php if (empty($details)) {
                echo ' disabled';
            } ?>" data-toggle="tooltip" title="<?php echo $view['translator']->trans(
                        'mautic.contactsource.timeline.toggle_details'
                    ); ?>">
                        <span class="fa fa-fw <?php echo $icon; ?>"></span>
                    </a>
                </td>
                <td class="timeline-message"><?php echo $message; ?></td>
                <td class="timeline-contact-id"><?php echo $contact; ?></td>
                <td class="timeline-type"><?php if (isset($event['eventType'])) {
                        echo $event['eventType'];
                    } ?></td>
                <td class="timeline-timestamp"><?php echo $view['date']->toText(
                        $event['timestamp'],
                        'local',
                        'Y-m-d H:i:s',
                        true
                    ); ?></td>
            </tr>
            <?php if (!empty($details)): ?>
                <tr class="timeline-row<?php echo $rowStripe; ?> timeline-details hide"
                    id="timeline-details-<?php echo $counter; ?>">
                    <td colspan="5">
                        <?php echo $details; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php echo $view->render(
    'MauticCoreBundle:Helper:pagination.html.php',
    [
        'page'       => $events['page'],
        'fixedPages' => $events['maxPages'],
        'fixedLimit' => true,
        'baseUrl'    => $baseUrl,
        'target'     => '#timeline-table',
        'totalItems' => $events['total'],
    ]
); ?>

<!--/ timeline -->