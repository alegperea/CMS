<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use \Firebase\JWT\JWT;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class BlogController extends Controller
{
    
    
    /**
     * @Route("/", name="accion_login", defaults={"page" = 1})
     * @Route("/acciones/login/", name="accion_login") 
     */
    public function loginAction(Request $request){
      	    
	$user = $request->get('_username');
	$pass = $request->get('_password');

	$decode = JWT::decode($pass, 'password', array('HS256'));
	
	print_r('USUARIO:'.$user);
	print_r('PASSWORD:'.$decode);	
	exit();
	
        $em = $this->getDoctrine()->getManager();
	$posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
        $objUsuario = $em->getRepository('AppBundle:User')->findOneByUsername($user);
	
	foreach($posts as $post):
	    
	      $objeto[] = array("estado" => "OK", "idusuario" => $objUsuario->getId(), "titulo" => $post->getTitle(), "descripcion" => $post->getIntrotext());
	    
	endforeach;
	
        //VERIFICAR USUARIO
        if (!$user || !$pass) {
            return new Response(json_encode(array("estado" => "ERROR: URL mal formulada.")));
        }
       
        if (!$objUsuario) {
            return new Response(json_encode(array("estado" => "ERROR: El usuario o la contraseña es incorrecta.")));
        }


        $encoder = $this->get('security.encoder_factory')->getEncoder($objUsuario);
	//$entity->setSalt(md5(time()));
	$passwordCodificado = $encoder->encodePassword(
                        $pass, $objUsuario->getSalt()
                );
	
        if ($passwordCodificado != $objUsuario->getPassword()) {
            return new Response(json_encode(array("estado" => "ERROR: El usuario o la contraseña es incorrecta.")));
        }
               
        return new Response(json_encode($objeto));
    }
    
    /**
     * @Route("/", name="accion_json", defaults={"page" = 1})
     * @Route("/acciones/json", name="accion_json") 
     */
    public function jsonAction(){

	$posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
        	
	foreach($posts as $post):
	    
	      $objeto[] = array("titulo" => $post->getTitle(), "descripcion" => $post->getIntrotext());
	    
	endforeach;
	
        
        return new Response(json_encode($objeto));
    }
    
    
    /**
     * @Route("/", name="blog_index", defaults={"page" = 1})
     * @Route("/page/{page}", name="blog_index_paginated", requirements={"page" : "\d+"})
     * @Cache(smaxage="10")
     */
    public function indexAction($page)
    {
       
        $query = $this->getDoctrine()->getRepository('AppBundle:Post')->queryLatest();

        $paginator = $this->get('knp_paginator');
        $posts = $paginator->paginate($query, $page, Post::NUM_ITEMS);
        $posts->setUsedRoute('blog_index_paginated');

        return $this->render('blog/index.html.twig', array('posts' => $posts));
    }

    /**
     * @Route("/posts/{slug}", name="blog_post")
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     */
    public function postShowAction(Post $post)
    {
        return $this->render('blog/post_show.html.twig', array('post' => $post));
    }

    /**
     * @Route("/comment/{postSlug}/new", name = "comment_new")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function commentNewAction(Request $request, Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthorEmail($this->getUser()->getEmail());
            $comment->setPost($post);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_post', array('slug' => $post->getSlug()));
        }

        return $this->render('blog/comment_form_error.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');

        return $this->render('blog/_comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
}
