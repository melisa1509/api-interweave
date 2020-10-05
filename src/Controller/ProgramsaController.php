<?php
/**
 * ProgramsaController.php
 *
 */
 
namespace App\Controller;
 
use App\Entity\ProgramSa;
use App\Entity\Task;
use App\Entity\Certificate;
use App\Form\ProgramSaUpdateRevisionType;
use App\Form\ProgramSaType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ProgramsaDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
 
/**
 * Class ProgramsaController
 *
 * @Route("/programsa")
 */
class ProgramsaController extends FOSRestController
{
    
 
    /**
     * @Rest\Get("/", name="programsa_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all programsa."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all programsa programsa."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Programsa ID"
     * )
     *
     *
     * @SWG\Tag(name="Programsa")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programsa = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$programsaId = $this->getProgramSa()->getId();
            $programsa = $em->getRepository("App:ProgramSa")->findBy(array(), array(), 20);
 
            if (is_null($programsa)) {
                $programsa = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Programsas - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programsa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/new", name="programsa_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Programsa was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Programsa was not successfully registered"
     * )
     *     
     *
     * @SWG\Tag(name="Programsa")
     */ 
    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programsa = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $programsa = $em->getRepository('App:ProgramSa')->findOneBy(array('student' => $request->request->get('id_student')));
 
            if (!is_null($programsa)) {
                $form = $this->createForm(ProgramSaType::class, $programsa);
                $form->submit($request->request->all());
                
                $generateGroups1 = explode(',', $request->request->get('generateGroups1'));
                $programsa->setGenerateGroups1($generateGroups1);
                $generateGroups3 = explode(',', $request->request->get('generateGroups3'));
                $programsa->setGenerateGroups3($generateGroups3);  
                $generateGroups5 = explode(',', $request->request->get('generateGroups5'));
                $programsa->setGenerateGroups5($generateGroups5);
                $rule9 = explode(',', $request->request->get('rule9'));
                $programsa->setRule9($rule9); 

                $programsa->setState("state.development");
                
 
                $em->persist($programsa);
                $em->flush();
 
            } else {
                $student = $em->getRepository('App:User')->find($request->request->get('id_student'));
                $programsa=new Programsa();
                $form = $this->createForm(ProgramSaType::class, $programsa);
                $form->submit($request->request->all());
                
                $programsa->setProcess1([""]);

                $programsa->setStudent($student);

                $programsa->setPaperwork3([""]);
                $generateGroups1 = explode(',', $request->request->get('generateGroups1'));
                $programsa->setGenerateGroups1($generateGroups1);
                $generateGroups3 = explode(',', $request->request->get('generateGroups3'));
                $programsa->setGenerateGroups3($generateGroups3);  
                $generateGroups5 = explode(',', $request->request->get('generateGroups5'));
                $programsa->setGenerateGroups5($generateGroups5);
                $rule9 = explode(',', $request->request->get('rule9'));

                $programsa->setRule9($rule9); 
                $programsa->setState("state.development");
                
 
                $em->persist($programsa);
                $em->flush();
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $student : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="programsa_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets programsa info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The programsa with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Programsa ID"
     * )
     *
     * @SWG\Tag(name="Programsa")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programsa = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $programsa_id = $id;
            $programsa = $em->getRepository("App:ProgramSa")->find($programsa_id);
 
            if (is_null($programsa)) {
                $code = 500;
                $error = true;
                $message = "The Programsa does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Programsa- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programsa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="programsa_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The programsa was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the programsa."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The programsa ID"
     * )
     *
     *
     *
     * @SWG\Tag(name="Programsa")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programsa = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $programsa = $em->getRepository('App:ProgramSa')->find($id);
 
            if (!is_null($programsa)) {
                $form = $this->createForm(ProgramSaType::class, $programsa);
                $form->submit($request->request->all());
                
                $generateGroups1 = explode(',', $request->request->get('generateGroups1'));
                $programsa->setGenerateGroups1($generateGroups1);
                $generateGroups3 = explode(',', $request->request->get('generateGroups3'));
                $programsa->setGenerateGroups3($generateGroups3);  
                $rule9 = explode(',', $request->request->get('rule9'));
                $generateGroups5 = explode(',', $request->request->get('generateGroups5'));
                $programsa->setGenerateGroups5($generateGroups5);
                $programsa->setRule9($rule9);           
                
 
                $em->persist($programsa);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programsa - Error: You must to provide fields programsa or the programsa id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programsa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Put("/revision", name="revision_programsa")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Project mbs approval was successful"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Project mbs was not successfully revision"
     * )
     * 
     * @SWG\Parameter(
     *     name="id",
     *     in="body",
     *     type="string",
     *     description="The id programsa",
     *     schema={}
     * )
     * @SWG\Tag(name="Programsa")
     */

    public function revisionAction(Request $request)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programSa = [];
        $message = ""; 

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get('id');
            $programSa = $em->getRepository('App:ProgramSa')->find($id);
 
            if (!is_null($programSa)) {
                $em = $this->getDoctrine()->getManager();

                
                $programSa->setState('state.revision');
                $em->persist($programSa);
                $em->flush();
                
                //$this->sendEmail("subject.approved_project", $programSa->getStudent());
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programsa - Error: You must to provide fields programsa or the programsa id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programSa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="programsa_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Programsa was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the programsa"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The programsa ID"
     * )
     *
     * @SWG\Tag(name="Programsa")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $programsa = $em->getRepository("App:ProgramSa")->find($id);
 
            if (!is_null($programsa)) {
                $em->remove($programsa);
                $em->flush();
 
                $message = "The programsa was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent programsa - Error: The programsa id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    

    /**
     * @Rest\Put("/update_revision/{id}", name="update_revision_programsa")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Revision was successfully update"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Revision was not successfully update"
     * )
     *
     * @SWG\Parameter(
     *     name="misionrevision",
     *     in="body",
     *     type="string",
     *     description="The mision description",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="generategroupstrevision",
     *     in="body",
     *     type="string",
     *     description="The generategroups description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="rulerevision",
     *     in="body",
     *     type="string",
     *     description="The rule description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="graduaterevision",
     *     in="body",
     *     type="string",
     *     description="The graduate description",
     *     schema={}
     * )
     *      
     * @SWG\Tag(name="Programsa")
     */

    public function updateRevisionAction(Request $request, $id)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programsa = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $programsa = $em->getRepository('App:ProgramSa')->find($id);
 
            if (!is_null($programsa)) {
                $form = $this->createForm(ProgramSaUpdateRevisionType::class, $programsa);
                $form->submit($request->request->all());
 
                $em->persist($programsa);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programsa - Error: You must to provide fields programsa or the programsa id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programsa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }


    /**
     * @Rest\Put("/approved", name="approved_programsa")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Project mbs approval was successful"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Project mbs was not successfully approved"
     * )
     * 
     * @SWG\Parameter(
     *     name="id",
     *     in="body",
     *     type="string",
     *     description="The id programsa",
     *     schema={}
     * )
     * @SWG\Tag(name="Programsa")
     */

    public function approvedAction(Request $request)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programSa = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get('id');
            $programSa = $em->getRepository('App:ProgramSa')->find($id);
            $user = $em->getRepository('App:User')->find($programSa->getStudent()->getId());
 
            if (!is_null($programSa)) {
                $em = $this->getDoctrine()->getManager();

               
                $programSa->setState('state.approved');
                $user->setRoles(["ROLE_EMBASSADOR"]);
                $programSa->setApprovalDate(new \DateTime('now', (new \DateTimeZone('America/New_York'))));
                if(!$programSa->getCode()){
                    $programSa->setCode($programSa->getStudent()->getCountry()."-".$this->getNumberCode($programSa, $request));
                    $user->setCode($programSa->getStudent()->getCountry()."-".$this->getNumberCode($programSa, $request));
                }

                $em->persist($programSa);
                $em->flush();

                $em->persist($user);
                $em->flush();
                
                //$this->sendEmail("subject.approved_project", $programSa->getStudent());
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programsa - Error: You must to provide fields programsa or the programsa id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programSa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/sendrevision", name="sendrevision_programsa")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Project mbs sendrevision was successful"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Project mbs was not successfully sendrevision"
     * )
     * 
     * @SWG\Parameter(
     *     name="id",
     *     in="body",
     *     type="string",
     *     description="The id programsa",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="revisionmision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="revisiongenerategroups",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="revisionrule",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="revisiongraduate",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="revisionsupport",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     *      
     * @SWG\Tag(name="Programsa")
     */

    public function sendRevisionAction(Request $request)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programSa = [];
        $message = ""; 

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get('id');
            $programSa = $em->getRepository('App:ProgramSa')->find($id);
 
            if (!is_null($programSa)) {
                $em = $this->getDoctrine()->getManager();

                $form = $this->createForm(ProgramSaUpdateRevisionType::class, $programSa);
                $form->submit($request->request->all());

                $programSa->setState('state.correction');
                //$this->sendEmail("subject.pending_correction", $programSa->getStudent());
            
                $em->persist($programSa);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to send revision Programsa - Error: You must to provide fields programsa or the programsa id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying send revision of programsa - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programSa : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    public function getNumberCode(ProgramSa $programSa){

        $em = $this->getDoctrine()->getManager();
        $number = $em->getRepository('App:Certificate')->findOneBy(array('country' => $programSa->getStudent()->getCountry(), 'program' => 'SA'));
        if($number){
          $number->setNumber($number->getNumber() + 1);
          $em->persist($number);
          $em->flush();
        }
        else{
          $number = new Certificate();
          $number->setCountry($programSa->getStudent()->getCountry());
          $number->setProgram("SA");
          $number->setNumber(1);
          $em->persist($number);
          $em->flush();
        }
  
        return $number->getNumber();
    }

    public function sendEmail($subject, $student){

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository('App:User')->userByRole("ROLE_ADMIN");
  
        if( $subject == "subject.pending_revision"){
  
           foreach ($admins as $admin ) {
               if($admin->getLanguage() == "en"){
                     $subjectEmail = $this->get('translator')->trans($subject, [] , null, "en");
                     $bodyEmail = $this->get('translator')->trans('message.pending_revision', [] , null, "en")." ".$student->getFirstName()." ".$student->getLastName();
  
                     $message = (new \Swift_Message($subjectEmail))
                      ->setFrom('myplatform@interweavesolutions.com')
                      ->setTo($admin->getUsername())
                      ->setBody($bodyEmail);
  
                      //$mailer->send($message);
                      $this->get('mailer')->send($message);
                }
                elseif($admin->getLanguage() == $student->getLanguage()){
                    $subjectEmail = $this->get('translator')->trans($subject, [] , null, $student->getLanguage());
                    $bodyEmail = $this->get('translator')->trans('message.pending_revision', [] , null, $student->getLanguage())." ".$student->getFirstName()." ".$student->getLastName();
  
                    $message = (new \Swift_Message($subjectEmail))
                     ->setFrom('myplatform@interweavesolutions.com')
                     ->setTo($admin->getUsername())
                     ->setBody($bodyEmail);
  
                     //$mailer->send($message);
                     $this->get('mailer')->send($message);
                }
           }
        }
  
        if($subject == "subject.approved_project") {
          $em = $this->getDoctrine()->getManager();
          $studentGroup = $student->getStudentgroup();
  
          // Send Notification to Ambbasador
          $subjectEmail = $this->get('translator')->trans($subject, [] , null, $studentGroup->getGroup()->getEmbassador()->getLanguage());
          $bodyEmail = $this->get('translator')->trans('message.approved_project', [] , null, $studentGroup->getGroup()->getEmbassador()->getLanguage())." ".$student->getFirstName()." ".$student->getLastName()." ".$student->getProgramSa()->getCode();
  
          $message = (new \Swift_Message($subjectEmail))
           ->setFrom('myplatform@interweavesolutions.com')
           ->setTo($studentGroup->getGroup()->getEmbassador()->getUsername())
           ->setBody($bodyEmail);
  
           //$mailer->send($message);
           $this->get('mailer')->send($message);
  
           // Sent Notification to Student
           $subjectEmail = $this->get('translator')->trans($subject, [] , null, $student->getLanguage());
           $bodyEmail = $this->get('translator')->trans('message.approved_project', [] , null, $student->getLanguage())." ".$student->getFirstName()." ".$student->getLastName()." ".$student->getProgramSa()->getCode();
  
           $message = (new \Swift_Message($subjectEmail))
            ->setFrom('myplatform@interweavesolutions.com')
            ->setTo($student->getUsername())
            ->setBody($bodyEmail);
  
            //$mailer->send($message);
            $this->get('mailer')->send($message);
  
            // Sent Notification to $admin
            foreach($admins as $admin){
              if($admin->getLanguage() == "en"){
                    $subjectEmail = $this->get('translator')->trans($subject, [] , null, "en");
                    $bodyEmail = $this->get('translator')->trans('message.approved_project_admin', [] , null, "en")." ".$student->getFirstName()." ".$student->getLastName()." whith the follow code ".$student->getProgramSa()->getCode();
  
                    $message = (new \Swift_Message($subjectEmail))
                     ->setFrom('myplatform@interweavesolutions.com')
                     ->setTo($admin->getUsername())
                     ->setBody($bodyEmail);
  
                     //$mailer->send($message);
                     $this->get('mailer')->send($message);
               }
            }
          }
  
          if($subject == "subject.pending_correction") {
            $em = $this->getDoctrine()->getManager();
            $studentGroup = $student->getStudentgroup();
  
            // Send Notification to Ambbasador
            $subjectEmail = $this->get('translator')->trans($subject, [] , null, $studentGroup->getGroup()->getEmbassador()->getLanguage());
            $bodyEmail = $this->get('translator')->trans('message.pending_correction', [] , null, $studentGroup->getGroup()->getEmbassador()->getLanguage())." ".$student->getFirstName()." ".$student->getLastName();
  
            $message = (new \Swift_Message($subjectEmail))
             ->setFrom('myplatform@interweavesolutions.com')
             ->setTo($studentGroup->getGroup()->getEmbassador()->getUsername())
             ->setBody($bodyEmail);
  
             //$mailer->send($message);
             $this->get('mailer')->send($message);
  
             // Sent Notification to Student
             $subjectEmail = $this->get('translator')->trans($subject, [] , null, $student->getLanguage());
             $bodyEmail = $this->get('translator')->trans('message.pending_correction', [] , null, $student->getLanguage())." ".$student->getFirstName()." ".$student->getLastName();
  
             $message = (new \Swift_Message($subjectEmail))
              ->setFrom('myplatform@interweavesolutions.com')
              ->setTo($student->getUsername())
              ->setBody($bodyEmail);
  
              //$mailer->send($message);
              $this->get('mailer')->send($message);
  
  
            }
      }

   
 
}