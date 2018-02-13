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
$view['slots']->set('mauticContent', 'contactserver');

$header = ($entity->getId())
    ?
    $view['translator']->trans(
        'mautic.contactserver.edit',
        ['%name%' => $view['translator']->trans($entity->getName())]
    )
    :
    $view['translator']->trans('mautic.contactserver.new');
$view['slots']->set('headerTitle', $header);

echo $view['assets']->includeScript('plugins/MauticContactServerBundle/Assets/build/contactserver.min.js');
echo $view['assets']->includeStylesheet('plugins/MauticContactServerBundle/Assets/build/contactserver.min.css');

echo $view['form']->start($form);
?>

<!-- start: box layout -->
<div class="box-layout">

    <!-- tab container -->
    <div class="col-md-9 bg-white height-auto bdr-l contactserver-left">
        <div class="">
            <ul class="nav nav-tabs pr-md pl-md mt-10">
                <li class="active">
                    <a href="#details" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.details'); ?>
                    </a>
                </li>
                <li>
                    <a href="#duplicate" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.duplicate'); ?>
                    </a>
                </li>
                <li>
                    <a href="#exclusive" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.exclusive'); ?>
                    </a>
                </li>
                <li>
                    <a href="#filter" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.filter'); ?>
                    </a>
                </li>
                <li>
                    <a href="#limits" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.limits'); ?>
                    </a>
                </li>
                <li id="payload-tab" class="hide">
                    <a href="#payload" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.payload'); ?>
                    </a>
                </li>
                <li>
                    <a href="#attribution" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.attribution'); ?>
                    </a>
                </li>
                <li>
                    <a href="#schedule" role="tab" data-toggle="tab" class="contactserver-tab">
                        <?php echo $view['translator']->trans('mautic.contactserver.form.group.schedule'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <!-- pane -->
            <div class="tab-pane fade in active bdr-rds-0 bdr-w-0" id="details">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-md-5">
                                <?php echo $view['form']->row($form['name']); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo $view['form']->row($form['type']); ?>
                            </div>
                            <div class="col-md-5">
                                <?php echo $view['form']->row($form['website']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $view['form']->row($form['description']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="attribution">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-6">
                                <?php echo $view['form']->row($form['attribution_default']); ?>
                                <?php echo $view['form']->row($form['attribution_settings']); ?>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="duplicate">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $view['form']->row($form['duplicate']); ?>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="exclusive">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $view['form']->row($form['exclusive']); ?>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="filter">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $view['form']->row($form['filter']); ?>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="limits">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $view['form']->row($form['limits']); ?>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="payload">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row api_payload hide">
                            <div class="col-md-12">
                                <div id="api_payload_buttons" class="toolbar-form-buttons pull-right hide">
                                    <div class="btn-group hidden-xs">
                                        <button type="button" class="btn btn-default" id="api_payload_test" data-toggle="button" aria-pressed="false" autocomplete="off">
                                            <i class="fa fa-check-circle text-success"></i>
                                            <?php echo $view['translator']->trans('mautic.contactserver.form.test'); ?>
                                        </button>
                                        <button type="button" class="btn btn-default btn-nospin" id="api_payload_advanced" data-toggle="button" aria-pressed="false" autocomplete="off">
                                            <i class="fa fa-low-vision"></i>
                                            <?php echo $view['translator']->trans('mautic.contactserver.form.advanced'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?php echo $view['form']->row($form['api_payload']); ?>
                                <div id="api_payload_test_result" class="hide">
                                    <hr/>
                                    <strong><?php echo $view['translator']->trans('mautic.contactserver.form.test_results'); ?></strong>
                                    <div id="api_payload_test_result_yaml">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row file_payload hide">
                            <div class="col-md-12">
                                <?php echo $view['form']->row($form['file_payload']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade bdr-rds-0 bdr-w-0" id="schedule">
                <div class="pa-md">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $view['form']->row($form['schedule_timezone']); ?>
                                <?php echo $view['form']->row($form['schedule_hours']); ?>
                                <div id="contactserver_schedule_hours_widget"></div>
                                <?php echo $view['form']->row($form['schedule_exclusions']); ?>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                    </div>
                </div>
            </div>
            <!--/ #pane -->
        </div>
    </div>
    <!--/ tab container -->

    <!-- container -->
    <div class="col-md-3 bg-white height-auto contactserver-right">
        <div class="pr-lg pl-lg pt-md pb-md">
            <?php
            echo $view['form']->row($form['category']);
            echo $view['form']->row($form['isPublished']);
            echo $view['form']->row($form['publishUp']);
            echo $view['form']->row($form['publishDown']);
            ?>
        </div>
    </div>
    <!--/ container -->
</div>
<!--/ box layout -->

<?php echo $view['form']->end($form); ?>