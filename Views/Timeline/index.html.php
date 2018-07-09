<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<?php
$orderBy          = isset($events['filters']['order']) && !empty($events['filters']['order'][0]) ? $events['filters']['order'][0] : 'date_added';
$orderByDirection = isset($events['filters']['order']) && !empty($events['filters']['order'][1]) ? $events['filters']['order'][1] : 'DESC';
$page             = isset($events['page']) && !empty($events['page']) ? $events['page'] : 1;

?>

<!-- filter form -->
<h4><?php echo $view['translator']->trans('mautic.contactsource.search.header'); ?></h4>
<form method="post" action="<?php echo $view['router']->path(
    'mautic_contactsource_timeline_action',
    ['contactSourceId' => $contactSource->getId()]
); ?>" class="panel" id="timeline-filters">
    <div class="col-xs-8 col-lg-10 va-m form-inline">
        <div class="input-group col-xs-8">
            <input type="text" class="form-control bdr-w-1 search tt-input" name="search" id="search"
                   placeholder="<?php echo $view['translator']->trans('mautic.contactsource.search.placeholder'); ?>"
                   value="<?php echo $events['filters']['search']; ?>">

            <div class="input-group-btn">
                <button type="submit" id="contactSourceTimelineFilterApply" name="contactSourceTimelineFilterApply"
                        class="btn btn-default btn-search btn-nospin">
                    <i class="the-icon fa fa-search fa-fw"></i>
                </button>
            </div>

            <?php /* @todo - export action. Doesn't yet have a router/controller config.
             * <div class="col-sm-2">
             * <a class="btn btn-default btn-block" href="<?php echo
             * $view['router']->generate('mautic_contactclient_timeline_export_action', ['contactClientId' =>
             * $contactClient->getId()]); ?>" data-toggle="download">
             * <span>
             * <i class="fa fa-download"></i> <span class="hidden-xs hidden-sm"><?php echo
             * $view['translator']->trans('mautic.core.export'); ?></span>
             * </span>
             * </a>
             * </div>*/ ?>
        </div>
    </div>


    <input type="hidden" name="contactSourceId" id="contactSourceId" value="<?php echo $contactSource->getId(); ?>"/>
    <input type="hidden" name="orderBy" id="orderBy" value="<?php echo $orderBy; ?>:<?php echo $orderByDirection; ?>"/>
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>"/>
</form>

<script>
    mauticLang['showMore'] = '<?php echo $view['translator']->trans('mautic.core.more.show'); ?>';
    mauticLang['hideMore'] = '<?php echo $view['translator']->trans('mautic.core.more.hide'); ?>';
</script>

<div id="timeline-table">
    <?php $view['slots']->output('_content'); ?>
</div>
<script>
    mQuery(function () {
        var filterForm = mQuery('#timeline-filters');
        var dateFrom = document.createElement('input');
        dateFrom.type = 'hidden';
        dateFrom.name = 'dateFrom';
        dateFrom.value = mQuery('#chartfilter_date_from').val();

        var dateTo = document.createElement('input');
        dateTo.type = 'hidden';
        dateTo.name = 'dateTo';
        dateTo.value = mQuery('#chartfilter_date_to').val();

        filterForm.append(dateFrom);
        filterForm.append(dateTo);

        filterForm.submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting via the browser
            var form = $(this);
            mQuery.ajax({
                type: form.attr('method'),
                url: mauticAjaxUrl,
                data: {
                    action: 'plugin:mauticContactSource:ajaxTimeline',
                    filters: form.serializeArray()
                }
            }).done(function (data) {
                mQuery('div#timeline-table').html(data);
                if (mQuery('#contactsource-timeline').length) {
                    Mautic.contactsourceTimelineOnLoad();
                }
            }).fail(function (data) {
                // Optionally alert the user of an error here...
                alert('Ooops! Something went wrong');
            });
        });
    });
</script>