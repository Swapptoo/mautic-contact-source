<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactServerBundle\Model;

use Mautic\LeadBundle\Entity\Lead as Contact;
use MauticPlugin\MauticContactServerBundle\Entity\ContactServer;
use MauticPlugin\MauticContactServerBundle\Helper\JSONHelper;
use MauticPlugin\MauticContactServerBundle\Model\ApiPayload;

/**
 * Class Attribution
 * @package MauticPlugin\MauticContactServerBundle\Model
 */
class Attribution
{

    /** @var ContactServer $contactServer */
    protected $contactServer;

    /** @var ApiPayload */
    protected $payload;

    /** @var Contact */
    protected $contact;

    /** @var double */
    protected $newAttribution;

    /**
     * Attribution constructor.
     * @param ContactServer $contactServer
     * @param Contact $contact
     */
    public function __construct(ContactServer $contactServer, Contact $contact)
    {
        $this->contactServer = $contactServer;
        $this->contact = $contact;
    }

    /**
     * @param ApiPayload $payload
     */
    public function setPayload(ApiPayload $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Apply attribution to the current contact based on payload and settings of the Contact Server.
     * This assumes the Payload was successfully delivered (valid = true).
     *
     * @return bool
     * @throws \Exception
     */
    public function applyAttribution()
    {
        $update = false;
        $originalAttribution = $this->contact->getFieldValue('attribution');
        $originalAttribution = !empty($originalAttribution) ? $originalAttribution : 0;
        $newAttribution = 0;

        if ($this->payload) {

            $jsonHelper = new JSONHelper();
            $attributionSettings = $jsonHelper->decodeObject($this->contactServer->getAttributionSettings(), 'AttributionSettings');

            if ($attributionSettings && is_object(
                    $attributionSettings->mode
                ) && !empty($attributionSettings->mode->key)) {
                // Dynamic mode.
                $key = $attributionSettings->mode->key;

                // Attempt to get this field value from the response operations.
                $responseFieldValue = $this->payload->getAggregateResponseFieldValue($key);
                if (!empty($responseFieldValue) && is_numeric($responseFieldValue)) {

                    // We have a value, apply sign.
                    $sign = isset($attributionSettings->mode->sign) ? $attributionSettings->mode->sign : '+';
                    if ($sign == '+') {
                        $newAttribution = abs($responseFieldValue);
                    } elseif ($sign == '-') {
                        $newAttribution = abs($responseFieldValue) * -1;
                    } else {
                        $newAttribution = $responseFieldValue;
                    }

                    // Apply maths.
                    $math = isset($attributionSettings->mode->math) ? $attributionSettings->mode->math : null;
                    if ($math == '/100') {
                        $newAttribution = $newAttribution / 100;
                    } elseif ($math == '*100') {
                        $newAttribution = $newAttribution * 100;
                    }
                    $update = true;
                }
            }
        }

        // If we were not able to apply a dynamic cost/attribution, then fall back to the default (if set).
        if (!$update) {
            $attributionDefault = $this->contactServer->getAttributionDefault();
            if (!empty($attributionDefault) && is_numeric($attributionDefault)) {
                $newAttribution = $attributionDefault;
                $update = true;
            }
        }

        if ($update && $newAttribution) {
            $this->setNewAttribution($newAttribution);
            $this->contact->addUpdatedField(
                'attribution',
                $originalAttribution + $newAttribution,
                $originalAttribution
            );
            // Unsure if we should keep this next line for BC.
            $this->contact->addUpdatedField('attribution_date', (new \DateTime())->format('Y-m-d H:i:s'));
        }

        return $update;
    }

    /**
     * @return float
     */
    public function getNewAttribution()
    {
        return $this->newAttribution;
    }

    /**
     * @param $newAttribution
     * @return $this
     */
    public function setNewAttribution($newAttribution)
    {
        $this->newAttribution = $newAttribution;

        return $this;
    }

    /**
     * @return Contact|null
     */
    public function getContact()
    {
        return $this->contact;
    }
}