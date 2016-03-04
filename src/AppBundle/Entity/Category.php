<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 *
 */
class Category
{
    
    const activo = 1;
    const papelera = -1;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $alias;
    
    /**
     * @ORM\Column(type="string")
     */
    private $slug;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="post.blank_summary")
     */
    private $introtext;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="post.blank_summary")
     */
    private $state;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\Email()
     */
    private $authorEmail;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $publishedAt;
    

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    function getId() {
        return $this->id;
    }

    function getTitle() {
        return $this->title;
    }

    function getAlias() {
        return $this->alias;
    }

    function getIntrotext() {
        return $this->introtext;
    }

    function getState() {
        return $this->state;
    }

    function getAuthorEmail() {
        return $this->authorEmail;
    }

    function getPublishedAt() {
        return $this->publishedAt;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setAlias($alias) {
        $this->alias = $alias;
    }

    function setIntrotext($introtext) {
        $this->introtext = $introtext;
    }

    function setState($state) {
        $this->state = $state;
    }

    function setAuthorEmail($authorEmail) {
        $this->authorEmail = $authorEmail;
    }

    function setPublishedAt($publishedAt) {
        $this->publishedAt = $publishedAt;
    }

    function getSlug() {
        return $this->slug;
    }

    function setSlug($slug) {
        $this->slug = $slug;
    }
    
     /**
     * Is the given User the author of this Post?
     *
     * @param User $user
     *
     * @return bool
     */
    public function isAuthor(User $user)
    {
        return $user->getEmail() == $this->getAuthorEmail();
    }

}
