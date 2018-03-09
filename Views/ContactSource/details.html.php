<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'contactsource');
$view['slots']->set('headerTitle', $item->getName());

echo $view['assets']->includeScript('plugins/MauticContactSourceBundle/Assets/build/contactsource.min.js');
echo $view['assets']->includeStylesheet('plugins/MauticContactSourceBundle/Assets/build/contactsource.min.css');

$view['slots']->set(
    'actions',
    $view->render(
        'MauticCoreBundle:Helper:page_actions.html.php',
        [
            'item'            => $item,
            'templateButtons' => [
                'edit'   => $view['security']->hasEntityAccess(
                    $permissions['plugin:contactsource:items:editown'],
                    $permissions['plugin:contactsource:items:editother'],
                    $item->getCreatedBy()
                ),
                'clone'  => $permissions['plugin:contactsource:items:create'],
                'delete' => $view['security']->hasEntityAccess(
                    $permissions['plugin:contactsource:items:deleteown'],
                    $permissions['plugin:contactsource:items:deleteother'],
                    $item->getCreatedBy()
                ),
                'close'  => $view['security']->isGranted('plugin:contactsource:items:view'),
            ],
            'routeBase'       => 'contactsource',
            'langVar'         => 'mautic.contactsource',
        ]
    )
);

$token = $item->getToken();

?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- left section -->
    <div class="col-md-9 bg-white height-auto">
        <div class="bg-auto">
            <!-- form detail header -->
            <div class="pr-md pl-md pt-lg pb-lg">
                <div class="box-layout">
                    <div class="col-xs-10">
                        <div class="text-muted"><?php echo $item->getDescription(); ?></div>
                    </div>
                    <div class="col-xs-2 text-right">
                        <?php echo $view->render(
                            'MauticCoreBundle:Helper:publishstatus_badge.html.php',
                            ['entity' => $item]
                        ); ?>
                    </div>
                </div>
            </div>
            <!--/ form detail header -->

            <!-- form detail collapseable -->
            <div class="collapse" id="contactsource-details">
                <div class="pr-md pl-md pb-md">
                    <div class="panel shd-none mb-0">
                        <table class="table table-bordered table-striped mb-0">
                            <tbody>
                            <?php echo $view->render(
                                'MauticCoreBundle:Helper:details.html.php',
                                ['entity' => $item]
                            ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ form detail collapseable -->
        </div>

        <div class="bg-auto bg-dark-xs">
            <!-- form detail collapseable toggler -->
            <div class="hr-expand nm">
                <span data-toggle="tooltip" title="<?php echo $view['translator']->trans('mautic.core.details'); ?>">
                    <a href="javascript:void(0)" class="arrow text-muted collapsed" data-toggle="collapse"
                       data-target="#contactsource-details"><span
                                class="caret"></span> <?php echo $view['translator']->trans(
                            'mautic.core.details'
                        ); ?></a>
                </span>
            </div>
            <!--/ form detail collapseable toggler -->

            <!-- stats -->
            <div class="pa-md">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel">
                            <div class="panel-body box-layout">
                                <div class="col-xs-4 va-m">
                                    <h5 class="text-white dark-md fw-sb mb-xs">
                                        <span class="fa fa-line-chart"></span>
                                        <?php echo $view['translator']->trans('mautic.contactsource.graph.stats'); ?>
                                    </h5>
                                </div>
                                <div class="col-xs-8 va-m">
                                    <?php echo $view->render(
                                        'MauticContactSourceBundle:Helper:graph_chartfilter.html.php',
                                        ['chartFilterForm' => $chartFilterForm, 'class' => 'pull-right']
                                    ); ?>
                                </div>
                            </div>
                            <div class="pt-0 pl-15 pb-10 pr-15">
                                <?php echo $view->render(
                                    'MauticCoreBundle:Helper:chart.html.php',
                                    ['chartData' => $stats, 'chartType' => 'line', 'chartHeight' => 300]
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ stats -->

            <!-- tabs controls -->
            <ul class="nav nav-tabs pr-md pl-md mt-10">
                <li class="active">
                    <a href="#timeline-container" role="tab" data-toggle="tab">
                        <span class="label label-primary mr-sm" id="TimelineCount">
                            <?php echo $events['total']; ?>
                        </span>
                        <?php echo $view['translator']->trans('mautic.contactsource.timeline.events'); ?>
                    </a>
                </li>
                <?php /*
                <li class="">
                    <a href="#auditlog-container" role="tab" data-toggle="tab">
                    <span class="label label-primary mr-sm" id="AuditLogCount">
                        <?php echo $auditlog['total']; ?>
                    </span>
                        <?php echo $view['translator']->trans('mautic.lead.lead.tab.auditlog'); ?>
                    </a>
                </li> */ ?>

                <?php echo $view['content']->getCustomContent('tabs', $mauticTemplateVars); ?>
            </ul>
            <!--/ tabs controls -->

            <!-- start: tab-content -->
            <div class="tab-content pa-md">
                <!-- #history-container -->
                <div class="tab-pane fade in active bdr-w-0" id="timeline-container">
                    <?php echo $view->render(
                        'MauticContactSourceBundle:Timeline:list.html.php',
                        [
                            'events'        => $events,
                            'contactSource' => $item,
                            'tmpl'          => 'index',
                        ]
                    ); ?>
                </div>
                <!--/ #history-container -->

                <!-- #auditlog-container -->
<!--                <div class="tab-pane fade bdr-w-0" id="auditlog-container">-->
<!--                    --><?php //echo $view->render(
//                         'MauticLeadBundle:Auditlog:list.html.php',
//                         [
//                             'events' => $auditlog,
//                             'lead'   => $item,
//                             'tmpl'   => 'index',
//                         ]
//                     );?>
<!--                </div>-->
                <!--/ #auditlog-container -->

                <!-- custom content -->
                <?php echo $view['content']->getCustomContent('tabs.content', $mauticTemplateVars); ?>
                <!-- end: custom content -->

            </div>
            <!--/ end: tab-content -->
        </div>
    </div>
    <!--/ left section -->

    <!-- right section -->
    <div class="col-md-3 bg-white bdr-l height-auto">
        <!-- form HTML -->
        <?php if ($token): ?>
            <div class="pa-md">
                <div class="panel bg-info bg-light-lg bdr-w-0 mb-0">
                    <div class="panel-body">
                        <h5 class="fw-sb mb-sm"><?php echo $view['translator']->trans(
                                'mautic.contactsource.form.token'
                            ); ?></h5>
                        <p class="mb-sm contactsource_token"><?php echo $token; ?></p>
                    </div>
                </div>
            </div>

            <hr class="hr-w-2" style="width:50%">
        <?php endif; ?>
        <!--/ form HTML -->

        <div class="panel bg-transparent shd-none bdr-rds-0 bdr-w-0 mb-0">

            <!-- recent activity -->
            <?php echo $view->render(
                'MauticCoreBundle:Helper:recentactivity.html.php',
                ['logs' => $auditlog['events']]
            ); ?>

        </div>
    </div>
    <!--/ right section -->
</div>
<!--/ end: box layout -->

<input type="hidden" name="entityId" id="entityId" value="<?php echo $item->getId(); ?>"/>
