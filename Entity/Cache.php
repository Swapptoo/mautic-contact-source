<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\LeadBundle\Entity\Lead as Contact;

/**
 * Class CacheEntity.
 *
 * The Cache entity is used to store recent (<30 day old) successful contact sends and some basic fields,
 * in order to identify exclusions/duplicates/limits with a finite list of field matching patterns.
 *
 * We'll have no foreign key constraints intentionally for performance.
 */
class Cache
{

    /** @var int $id */
    private $id;

    /** @var Contact $contact */
    private $contact;

    /** @var integer $contactServer */
    private $contactServer;

    /** @var integer $category */
    private $category;

    /** @var string $email */
    private $email;

    /** @var string $phone */
    private $phone;

    /** @var string $mobile */
    private $mobile;

    /** @var string $address1 */
    private $address1;

    /** @var string $address2 */
    private $address2;

    /** @var string $city */
    private $city;

    /** @var string $state */
    private $state;

    /** @var string $zipcode */
    private $zipcode;

    /** @var string $country */
    private $country;

    /** @var string $dateAdded */
    private $utmSource;

    /** @var \DateTime $dateAdded */
    private $dateAdded;

    /**
     * Cache constructor.
     */
    public function __construct()
    {
        $this->dateAdded = new \Datetime();
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->addId();

        $builder->setTable('contactserver_cache');

        $builder->addNullableField('email', 'string');

        $builder->addNullableField('phone', 'string');

        $builder->addNullableField('mobile', 'string');

        $builder->addNullableField('address1', 'string');

        $builder->addNullableField('address2', 'string');

        $builder->addNullableField('city', 'string');

        $builder->addNullableField('state', 'string');

        $builder->addNullableField('zipcode', 'string');

        $builder->addNullableField('country', 'string');

        $builder->addNamedField('utmSource', 'string', 'utm_source');

        $builder->addNamedField('contactServer', 'integer', 'contactserver_id');

        $builder->addNamedField('contact', 'integer', 'contact_id');

        $builder->addNamedField('category', 'integer', 'category_id');

        $builder->addDateAdded();

        $builder->addIndex(
            [
                'contact_id',
            ],
            'contactserver_cache_contact_id'
        );

        $builder->addIndex(
            [
                'contactserver_id',
            ],
            'contactserver_cache_contactserver_id'
        );

        $builder->addIndex(
            [
                'email',
            ],
            'contactserver_cache_email'
        );

        $builder->addIndex(
            [
                'phone',
                'mobile',
            ],
            'contactserver_cache_phone'
        );

        $builder->addIndex(
            [
                'address1',
                'address2',
            ],
            'contactserver_cache_address'
        );

        $builder->addIndex(
            [
                'city',
                'state',
                'zipcode',
            ],
            'contactserver_cache_city_state_zip'
        );

        $builder->addIndex(
            [
                'country',
            ],
            'contactserver_cache_country'
        );

        $builder->setCustomRepositoryClass('MauticPlugin\MauticContactServerBundle\Entity\CacheRepository');

    }

    /**
     * Clone entity.
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     *
     * @return $this
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     *
     * @return $this
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     *
     * @return $this
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param string $zipcode
     *
     * @return $this
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getUtmSource()
    {
        return $this->utmSource;
    }

    /**
     * @param string $utmSource
     *
     * @return $this
     */
    public function setUtmSource($utmSource)
    {
        $this->utmSource = $utmSource;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param int $contact
     *
     * @return $this
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return int
     */
    public function getContactServer()
    {
        return $this->contactServer;
    }

    /**
     * @param int $contactServer
     *
     * @return $this
     */
    public function setContactServer($contactServer)
    {
        $this->contactServer = $contactServer;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param integer $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTime $dateAdded
     *
     * @return $this
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

}
