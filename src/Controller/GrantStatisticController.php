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
                    $participants = 0;
                    $grantgroups = $em->getRepository("App:GrantGroup")->findBy(array("grantambassador" => $ga->getId()));
                    $total_grant_groups = $total_grant_groups + count($grantgroups);
                    $total_grant_amount = $total_grant_amount + $ga->getAmount();
                    foreach ($grantgroups as $gr) {
                        $studentGroups = $em->getRepository("App:StudentGroup")->findBy(array("group" => $gr->getGroup()->getId())); 
                        $participants = $participants + count($studentGroups);
                    }
                    $total_grant_participants = $total_grant_participants + $participants;
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

    /**
     * @Rest\Get("/showgroup/{id}.{_format}", name="grant_statistic_show_group", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant ambassador info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant ambassador with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant  ID"
     * )
     *
     * @SWG\Tag(name="GrantStatistic")
     */
    public function showGroupAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $groups = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $grant_id = $id;
            $grantgroups = $em->getRepository("App:GrantGroup")->grantGroups($grant_id);
            foreach ($grantgroups as $ga) {
                $groups[]= $ga->getGroup();
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant Ambassador- Error: {$ex->getMessage()}";
        }

        $list = [
            'groups' => $groups,
            'grantgroups' => $grantgroups
        ];
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $list : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    
 
}