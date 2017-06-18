<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;


class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/user")
     *
     * Return the overall user list.
     *
     * @Secure(roles="ROLE_API")
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall User List",
     *   statusCodes = {
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return array
     */

    public function getAction()
    {
        $restresult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if ($restresult === null) {
            return new View("The user is not found", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }

    /**
     * @Rest\Get("/user/{id}")
     *
     *
     * Return an user identified by id.
     *
     * @Secure(roles="ROLE_API")
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an user identified by ID",
     *   statusCodes = {
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param integer $id user ID
     *
     * @return array
     */

    public function idAction($id)
    {
        $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($singleresult === null) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        return $singleresult;
    }

    /**
     * @Rest\Post("/user/")
     *
     * Create a User from the submitted data.<br/>
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new user from the submitted data.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     406 = "NULL VALUES ARE NOT ALLOWED"
     *   }
     * )
     *
     * @param Request $request Request
     *
     * @RequestParam(name="login", nullable=false, strict=true")
     * @RequestParam(name="name", nullable=false, strict=true,")
     *
     * @return View
     */
    public function postAction(Request $request)
    {
        $data = new User;
        $login = $request->get('login');
        $name = $request->get('name');
        if (empty($name) || empty($login)) {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setLogin($login);
        $data->setName($name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return new View("User Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/user/{id}")
     *
     *
     * Update a User from the submitted data by ID.<br/>
     *
     * @Secure(roles="ROLE_API")
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a user from the submitted data by ID.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param integer $id user ID
     * @param Request $request Request
     *
     * @RequestParam(name="id", nullable=false, strict=true)
     * @RequestParam(name="login", nullable=false, strict=true)
     * @RequestParam(name="name", nullable=false, strict=true)
     *
     * @return View
     */

    public function updateAction($id, Request $request)
    {
        $data = new User;
        $login = $request->get('login');
        $name = $request->get('name');
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        } elseif (!empty($login) && !empty($name)) {
            $user->setLogin($login);
            $user->setName($name);
            $sn->flush();
            return new View("User Updated Successfully", Response::HTTP_OK);
        } elseif (!empty($login) && empty($name)) {
            $user->setLogin($login);
            $sn->flush();
            return new View("Login Updated Successfully", Response::HTTP_OK);
        } elseif (empty($login) && !empty($name)) {
            $user->setName($name);
            $sn->flush();
            return new View("User Name Updated Successfully", Response::HTTP_OK);
        } else return new View("User name or login cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/user/{id}")
     *
     * Delete an user identified by ID.
     *
     * @Secure(roles="ROLE_API")
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete an user identified by ID",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $slug username or email
     *
     * @return View
     */

    public function deleteAction($id)
    {
        $data = new User;
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        } else {
            $sn->remove($user);
            $sn->flush();
        }
        return new View("deleted successfully", Response::HTTP_OK);
    }
}