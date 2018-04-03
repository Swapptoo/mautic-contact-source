Mautic.contactsourceTimelineOnLoad = function (container, response) {

    var sortedColumn = mQuery('#contactsource-timeline a[data-sort=' + sortField + '] i');
    sortedColumn.addClass('fa-sort-amount-' + sortDirection);
    sortedColumn.removeClass('fa-sort');

    var codeMirror = function ($el) {
        if (!$el.hasClass('codemirror-active')) {
            var $textarea = $el.find('textarea.codeMirror-yaml');
            if ($textarea.length) {
                CodeMirror.fromTextArea($textarea[0], {
                    mode: 'yaml',
                    theme: 'material',
                    gutters: [],
                    lineNumbers: false,
                    lineWrapping: true,
                    readOnly: true
                });
            }
            $el.addClass('codemirror-active');
        }
    };
    mQuery('#contactsource-timeline a[data-activate-details=\'all\']').on('click', function () {
        if (mQuery(this).find('span').first().hasClass('fa-level-down')) {
            mQuery('#contactsource-timeline a[data-activate-details!=\'all\']').each(function () {
                var detailsId = mQuery(this).data('activate-details'),
                    $details = mQuery('#timeline-details-' + detailsId);
                if (detailsId && $details.length) {
                    $details.removeClass('hide');
                    codeMirror($details);
                    mQuery(this).addClass('active');
                }
            });
            mQuery(this).find('span').first().removeClass('fa-level-down').addClass('fa-level-up');
        }
        else {
            mQuery('#contactsource-timeline a[data-activate-details!=\'all\']').each(function () {
                var detailsId = mQuery(this).data('activate-details'),
                    $details = mQuery('#timeline-details-' + detailsId);
                if (detailsId && $details.length) {
                    $details.addClass('hide');
                    mQuery(this).removeClass('active');
                }
            });
            mQuery(this).find('span').first().removeClass('fa-level-up').addClass('fa-level-down');
        }
    });
    mQuery('#contactsource-timeline a[data-activate-details!=\'all\']').on('click', function () {
        var detailsId = mQuery(this).data('activate-details');
        if (detailsId && mQuery('#timeline-details-' + detailsId).length) {
            var activateDetailsState = mQuery(this).hasClass('active'),
                $details = mQuery('#timeline-details-' + detailsId);

            if (activateDetailsState) {
                $details.addClass('hide');
                mQuery(this).removeClass('active');
            }
            else {
                $details.removeClass('hide');
                codeMirror($details);
                mQuery(this).addClass('active');
            }
        }
    });


    mQuery('#contactsource-timeline a.timeline-header-sort').on('click', function () {
        var column = mQuery(this).data('sort');
        var newDirection;
        if(column!=sortField){
            newDirection = 'DESC';
        } else {
            newDirection = sortDirection=='desc' ? 'ASC' : 'DESC';
        }
        mQuery('#orderBy').val(column + ':' + newDirection);
        // trigger a form submit
        mQuery('#timeline-filters').submit();

    });

    mQuery('#timeline-table:first .pagination:first a').off('click').on('click', function (e) {
        e.preventDefault();
        var urlbase = this.href.split('?')[0];
        var page = urlbase.split('/')[4];
        mQuery('#page').val(page);
        // trigger a form submit
        mQuery('#timeline-filters').submit();
    });

    if (response && typeof response.timelineCount !== 'undefined') {
        mQuery('#TimelineCount').html(response.timelineCount);
    }
};
