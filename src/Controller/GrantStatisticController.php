<?php

 
namespace App\Controller;
 
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\UserDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\SerializationContext;
 
/**
 * Class GrantStatisticController
 *
 * @Route("/grantstatistic")
 */
class GrantStatisticController extends FOSRestController
{
    
    /**
     * @Rest\Get("/", name="grants_statistic_index")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all grants."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all grants."
     * )
     *   
     *
     *
     * @SWG\Tag(name="GrantStatistic")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant_list = [];
        $total_amount = 0;
        $total_groups = 0;
        $total_participants = 0;
        $total_applications = 0;
       
        $message = "";
 
        try {
            $code = 200;
            $error = false;
            
            $grants = $em->getRepository("App:Grant")->findBy(array(), array("state" => "ASC"));
            foreach ($grants as $gt) {
                
                $grantsambassador = $em->getRepository("App:GrantAmbassador")->findBy(array("state" => "state.approved", "grant" => $gt->getId()));

                $grant = [];
                $total_grant_amount = 0;
                $total_grant_groups = 0;
                $total_grant_participants = 0;
                $total_grant_applications = count($grantsambassador);
                foreach ($grantsambassador as $ga) {
                    $grantgroups = $em->getRepository("App:GrantGroup")->findBy(array("grantambassador" => $ga->getId()));
                    $total_grant_groups = $total_grant_groups + count($grantgroups);
                    $total_grant_amount = $total_grant_amount + $ga->getAmount();
                    $total_grant_participants = $total_grant_participants + $ga->getNumber();
                }
                $grant = [
                    "id" => $gt->getId(),
                    "title" => $gt->getTitle(),
                    "type"  => $gt->getType(),
                    "total_grant_groups" => $total_grant_groups,
                    "total_grant_amount" => $total_grant_amount,
                    "total_grant_participants" => $total_grant_participants,
                    "total_grant_applications" => $total_grant_applications
                ];
                $grant_list[] = $grant;

                $total_amount = $total_amount + $total_grant_amount;
                $total_groups = $total_groups + $total_grant_groups;
                $total_participants = $total_participants + $total_grant_participants;
                $total_applications = $total_applications + $total_grant_applications;

            }

            $total_list = [
                "total_amount" => $total_amount,
                "total_groups" => $total_groups,
                "total_participants" => $total_participants,
                "total_applications" => $total_applications
            ];

            $grant_statistics = [
                "grant_list" => $grant_list,
                "total_list" => $total_list
            ];
          
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all GrantStatistic - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant_statistics : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    
 
}