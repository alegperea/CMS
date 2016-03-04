<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\Table(name="post")
 *
 * Defines the properties of the Post entity to represent the blog posts.
 * See http://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See http://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class Post
{
    
    const activo = 1;
    const papelera = -1;
    const tipoProducto = 2;
    const tipoNoticia = 1;
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See http://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    const NUM_ITEMS = 10;

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
    private $slug;

    /**
     * @ORM\Column(type="string")
     */
    private $alias;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="post.blank_summary")
     */
    private $introtext;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="post.blank_content")
     * @Assert\Length(min = "10", minMessage = "post.too_short_content")
     */
    private $body;

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

    /**
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="post",
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"publishedAt" = "DESC"})
     */
    private $comments;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category")
    */
    private $category;
    
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $precio;
    
    /**
     * @ORM\Column(type="string", nullable=true)
    */
    private $marca;
    
    /**
     * @ORM\Column(type="string", nullable=true)
    */
    private $equivalencias;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $fechaActualizacion;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="post.blank_summary")
     */
    private $images;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
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

    function getImages() {
        return $this->images;
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

    function setImages($images) {
        $this->images = $images;
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

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);
        $comment->setPost($this);
    }

    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
        $comment->setPost(null);
    }
    
    function getCategory() {
        return $this->category;
    }

    function getPrecio() {
        return $this->precio;
    }

    function getMarca() {
        return $this->marca;
    }

    function getEquivalencias() {
        return $this->equivalencias;
    }

    function getFechaActualizacion() {
        return $this->fechaActualizacion;
    }

    function setCategory($category) {
        $this->category = $category;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

    function setMarca($marca) {
        $this->marca = $marca;
    }

    function setEquivalencias($equivalencias) {
        $this->equivalencias = $equivalencias;
    }

    function setFechaActualizacion($fechaActualizacion) {
        $this->fechaActualizacion = $fechaActualizacion;
    }


    
    
}
