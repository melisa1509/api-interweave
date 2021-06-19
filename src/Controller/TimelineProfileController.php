<?php

namespace App\Controller;
 
use App\Entity\TimelineProfile;
use App\Form\TimelineProfileType;
use App\Form\TimelineProfileEditType;
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
 
/**
 * Class TimelineProfileController
 *
 * @Route("/timelineprofile")
 */
class TimelineProfileController extends FOSRestController
{
    
    /**
     * @Rest\Get("/", name="timeline_profile_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all timeline_profile."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all timeline_profile."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="TimelineProfile ID"
     * )
     *
     *
     * @SWG\Tag(name="TimelineProfile")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $timeline_profile = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$userId = $this->getUser()->getId();
            $timeline_profile = $em->getRepository("App:TimelineProfile")->findAll();
 
            if (is_null($timeline_profile)) {
                $timeline_profile = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all TimelineProfile - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $timeline_profile : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/new", name="timeline_profile_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="TimelineProfile was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="TimelineProfile was not successfully registered"
     * )
     *
     * 
     * @SWG\Parameter(
     *     name="id_user",
     *     in="body",
     *     type="string",
     *     description="Id User",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description of Grant",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="string",
     *     description="The file",
     *     schema={}
     * )
     * 
     * @SWG\Tag(name="TimelineProfile")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_user = $request->request->get('id_user');
        $id_admin = $request->request->get('id_admin');
        $timelineprofile=new TimelineProfile();
        //Create a form
        $form=$this->createForm(TimelineProfileType::class, $timelineprofile);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $user = $em->getRepository("App:User")->find($id_user);
            $admin = $em->getRepository("App:User")->find($id_admin);
 
            if (is_null($user)) {
                $code = 500;
                $error = true;
                $message = "The User does not exist";
            }            
            else{
                $timelineprofile->setUser($user);   
                $timelineprofile->setAdmin($admin);              
                $em->persist($timelineprofile);
                $em->flush();
            }

            
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the TimelineProfile - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $timelineprofile : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="timeline_profile_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets timeline_profile info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The timeline_profile with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The TimelineProfile ID"
     * )
     *
     * @SWG\Tag(name="TimelineProfile")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $timeline_profile = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $timeline_profile_id = $id;
            $timeline_profile = $em->getRepository("App:TimelineProfile")->findBy( array('user' => $timeline_profile_id), array('createdAt' => 'DESC'));
 
             
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current TimelineProfile- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $timeline_profile : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="timeline_profile_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The timeline_profile was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the timeline_profile."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The timeline_profile ID"
     * )
     *
     * @SWG\Tag(name="TimelineProfile")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $timeline_profile = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $timeline_profile = $em->getRepository("App:TimelineProfile")->find($id);
 
            if (!is_null($timeline_profile)) {
                $form = $this->createForm(TimelineProfileEditType::class, $timeline_profile);
                $form->submit($request->request->all());
 
                $em->persist($timeline_profile);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a timeline_profile - Error: You must to provide fields user or the timeline_profile id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the timeline_profile - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $timeline_profile : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="timeline_profile_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="timeline_profile was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the timeline_profile"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The TimelineProfile ID"
     * )
     *
     * @SWG\Tag(name="TimelineProfile")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $timeline_profile = $em->getRepository("App:TimelineProfile")->find($id);
 
            if (!is_null($timeline_profile)) {
                $em->remove($timeline_profile);
                $em->flush();
 
                $message = "The timeline_profile was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent timeline_profile - Error: The timeline_profile id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current timeline_profile - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

   
 
}