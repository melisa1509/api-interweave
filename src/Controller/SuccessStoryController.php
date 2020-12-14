<?php
/**
 * SuccessStoryController.php
 *
 * API Controller
 * 
 */
 
namespace App\Controller;
 
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditType;
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
 * Class SuccessStoryController
 *
 * @Route("/successstory")
 */
class SuccessStoryController extends FOSRestController
{
    
    /**
     * @Rest\Post("/", name="success_story_index")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all available success stories."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all success stories."
     * )
     *
     * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="path",
     *     type="string",
     *     description="The ID of Ambassador"
     * )
     * 
     * @SWG\Parameter(
     *     name="role",
     *     in="path",
     *     type="string",
     *     description="The role of user"
     * )
     *
     *
     * @SWG\Tag(name="SuccessStory")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $students = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $role = $request->request->get('role');
            $id = $request->request->get('id_ambassador');
            $setGroup = 'student_group';

            if($role == 'ROLE_ADMIN' or $role == 'ROLE_LANGUAGE_ADMIN'){
                $students = $em->getRepository('App:User')->userSuccessStory();
                $setGroup = 'student_list';
            }
            else if($role == 'ROLE_EMBASSADOR' or $role == 'ROLE_STUDENT_EMBASSADOR'){
                $students = $em->getRepository('App:StudentGroup')->successStoryByEmbassador($id);
                $setGroup = 'student_group';
            }
          
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all SuccessStory - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $students : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array($setGroup))));
    }

}