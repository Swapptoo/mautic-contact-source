<?php
/**
 * @Author      scottshipman
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactSourceBundle\Helper;

use Mautic\LeadBundle\Entity\Lead;

/**
 * Class UtmSourceHelper.
 */
class UtmSourceHelper
{
    public function getFirstUtmSource(Lead $contact)
    {
        $source = '';
        if (!empty($tags = $this->getSortedUtmTags($contact))) {
            $tag    = reset($tags);
            $source = $tag->getUtmSource();
        }

        return $source;
    }

    protected function getSortedUtmTags(Lead $contact)
    {
        $tags = [];

        if ($contact instanceof LEAD) {
            $utmTags = $contact->getUtmTags();
        }

        if ($utmTags) {
            $utmTags = $utmTags->toArray();
            foreach ($utmTags as $utmTag) {
                $tags[$utmTag->getDateAdded()->getTimestamp()] = $utmTag;
            }
            ksort($tags);
        }

        return $tags;
    }

    public function getLastUtmSource(Lead $contact)
    {
        $source = '';
        if (!empty($tags = $this->getSortedUtmTags($contact))) {
            $tag    = end($tags);
            $source = $tag->getUtmSource();
        }

        return $source;
    }
}
