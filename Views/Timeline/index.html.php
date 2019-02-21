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
<div class="bg-white panel pt-md pb-md">
    <!-- Export button -->
    <div class="btn-group col-xs-2 pb-md">
        <?php // Currently causes exception without patch: if ($view['security']->isAdmin() || !$view['security']->isGranted('contactsource:export:disable', 'MATCH_ONE')): ?>
        <a class="btn btn-default"
           onclick="Mautic.contactSourceTimelineExport();">
             <span>
                <i class="fa fa-download"></i><span class="hidden-xs hidden-sm">Export</span>
             </span>
        </a>
        <?php // endif; ?>
        <a id="transactions-filter-btn"
           class="btn btn-default">
            <span>
                <i class="fa fa-filter"></i>
            </span>
        </a>
    </div>
    <div id="sourceTransactions-builder-overlay">
        <div style="position: relative; top: 33%; left: 33%; index: 1024;display:inline-block; opacity: .5;">
            <i class="fa fa-spinner fa-spin fa-4x"></i>
        </div>
    </div>

    <div id="timeline-table" class="bg-whiter">

    </div>
</div>
