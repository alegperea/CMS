<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the sample data to load in the database when running the unit and
 * functional tests. Execute this command to load the data:
 *
 *   $ php app/console doctrine:fixtures:load
 *
 * See http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class LoadFixtures implements FixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadPosts($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        $passwordEncoder = $this->container->get('security.password_encoder');

        $johnUser = new User();
        $johnUser->setUsername('john_user');
        $johnUser->setEmail('john_user@symfony.com');
        $encodedPassword = $passwordEncoder->encodePassword($johnUser, '1234');
        $johnUser->setPassword($encodedPassword);
        $manager->persist($johnUser);

        $annaAdmin = new User();
        $annaAdmin->setUsername('admin');
        $annaAdmin->setEmail('admin@symfony.com');
        $annaAdmin->setRoles(array('ROLE_ADMIN'));
        $encodedPassword = $passwordEncoder->encodePassword($annaAdmin, '1234');
        $annaAdmin->setPassword($encodedPassword);
        $manager->persist($annaAdmin);

        $manager->flush();
    }

    private function loadPosts(ObjectManager $manager)
    {
        foreach (range(1, 30) as $i) {
            $post = new Post();
            $post->setTitle('Sed ut perspiciatis unde');
            $post->setAlias('Sed ut perspiciatis unde');
            $post->setIntrotext('Sed ut perspicantium, tocto beatae vitae dicta sunt explicabo. ');
            $post->setSlug($this->container->get('slugger')->slugify($post->getTitle()));
            $post->setBody('Sed ut is iste uasi architecto beatae vitae dicta sunt explicabo. ');
            $post->setAuthorEmail('anna_admin@symfony.com');
            $post->setPublishedAt(new \DateTime('now - '.$i.'days'));
            $post->setState(1);
            $post->setImages('test.jpg');

            foreach (range(1, 5) as $j) {
                $comment = new Comment();

                $comment->setAuthorEmail('john_user@symfony.com');
                $comment->setPublishedAt(new \DateTime('now + '.($i + $j).'seconds'));
                $comment->setContent('Sed ut perspiciatis undedasdadasd');
                $comment->setPost($post);

                $manager->persist($comment);
                $post->addComment($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

  
}
