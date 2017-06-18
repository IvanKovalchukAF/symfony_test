<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Visit;

class VisitController extends FOSRestController
{
    /**
     * @Rest\Post("/visit/")
     *
     * Create a register user visit.
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a register user visit.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     406 = "NULL VALUES ARE NOT ALLOWED"
     *   }
     * )
     *
     * @param Request $request Request
     *
     * @RequestParam(name="userId", nullable=false, strict=true")
     *
     * @return View
     */
    public function postAction(Request $request)
    {
        $data = new Visit;
        $userId = $request->get('user_id');
        $visitedAt = new \DateTime();
        if(empty($userId))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setUserId($userId);
        $data->setVisitedAt($visitedAt);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return new View("Visit Saved Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/visit/")
     *
     * Get daily active users (DAU).
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a register user visit.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     406 = "NULL VALUES ARE NOT ALLOWED"
     *   }
     * )
     *
     * @param Request $request Request
     *
     * @RequestParam(name="userId", nullable=false, strict=true")
     *
     * @return integer
     */
    public function getAction(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        if(empty($dateFrom) or empty($dateTo)) {
            return new View("date_from or date_to was not set", Response::HTTP_BAD_REQUEST);
        }else{
        $visitorsCount = $this->getDoctrine()->getRepository('AppBundle:Visit')->getDau($dateFrom, $dateTo);

        return $visitorsCount;
        }
    }
}