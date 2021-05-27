<?php
/**
 * ProgrammbsController.php
 *
 */
 
namespace App\Controller;
 
use App\Entity\ProgramMbs;
use App\Entity\Task;
use App\Entity\Certificate;
use App\Form\ProgramMbsUpdateRevisionType;
use App\Form\ProgramMbsType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ProgrammbsDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
 
/**
 * Class ProgrammbsController
 *
 * @Route("/programmbs")
 */
class ProgrammbsController extends FOSRestController
{
    
 
    /**
     * @Rest\Get("/", name="programmbss_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all programmbss."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all programmbs programmbss."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Programmbs ID"
     * )
     *
     *
     * @SWG\Tag(name="Programmbs")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programmbss = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$programmbsId = $this->getProgrammbs()->getId();
            $programmbss = $em->getRepository("App:ProgramMbs")->findBy(array(), array(), 20);
 
            if (is_null($programmbss)) {
                $programmbss = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Programmbss - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbss : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/new", name="programmbs_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Programmbs was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Programmbs was not successfully registered"
     * )
     *     
     *
     * @SWG\Tag(name="Programmbs")
     */ 
    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programmbs = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $programmbs = $em->getRepository('App:ProgramMbs')->findOneBy(array('student' => $request->request->get('id_student')));
 
            if (!is_null($programmbs)) {
                $progressOrigin = $this->mbsProgress($programmbs);
                $form = $this->createForm(ProgramMbsType::class, $programmbs);
                $form->submit($request->request->all());
                
                $process1 = explode(',', $request->request->get('process1'));
                $programmbs->setProcess1($process1);

                $paperwork3 = explode(',', $request->request->get('paperwork3'));
                $programmbs->setPaperwork3($paperwork3);
                $programmbs->setPaperwork4(json_decode($programmbs->getPaperwork4()));
                $programmbs->setPaperwork5(json_decode($programmbs->getPaperwork5()));
                $programmbs->setPaperwork6(json_decode($programmbs->getPaperwork6()));
                $programmbs->setPaperwork7(json_decode($programmbs->getPaperwork7()));
                $programmbs->setPaperwork8(json_decode($programmbs->getPaperwork8()));

                $programmbs->setState("state.development");
                
                if($this->mbsProgress($programmbs) >= $progressOrigin ){
                    $em->persist($programmbs);
                    $em->flush();
                }
                else{
                    $code = 500;
                    $error = true;
                    $message = "An error has occurred trying to edit the programmbs";
                }
                              
 
            } else {
                $student = $em->getRepository('App:User')->find($request->request->get('id_student'));
                $programmbs=new Programmbs();
                $form = $this->createForm(ProgramMbsType::class, $programmbs);
                $form->submit($request->request->all());
                
                $process1 = explode(',', $request->request->get('process1'));
                $programmbs->setProcess1($process1);
                
                $programmbs->setStudent($student);
                $paperwork3 = explode(',', $request->request->get('paperwork3'));
                $programmbs->setPaperwork3($paperwork3);
                $programmbs->setPaperwork4(json_decode($programmbs->getPaperwork4()));
                $programmbs->setPaperwork5(json_decode($programmbs->getPaperwork5()));
                $programmbs->setPaperwork6(json_decode($programmbs->getPaperwork6()));
                $programmbs->setPaperwork7(json_decode($programmbs->getPaperwork7()));
                $programmbs->setPaperwork8(json_decode($programmbs->getPaperwork8()));

                $programmbs->setState("state.development");
                
 
                $em->persist($programmbs);
                $em->flush();
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/newfile", name="programmbs_new_file")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Programmbs was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Programmbs was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_student",
     *     in="body",
     *     type="string",
     *     description="The id student",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="file_name",
     *     in="body",
     *     type="string",
     *     description="The name of file",
     *     schema={}
     * )
     *     
     *
     * @SWG\Tag(name="Programmbs")
     */ 
    public function newFileAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";        
        $id = $request->request->get('id_student');
        $filename = $request->request->get('file_name');
 
        try {
            $code = 200;
            $error = false;
           
            $student = $em->getRepository("App:User")->find($id);
            $programmbs = $em->getRepository("App:Programmbs")->findOneBy(array('student' => $id));

            if(is_null($programmbs)){
                $programmbs=new Programmbs();
            }            
 
            if (is_null($student)) {
                $code = 500;
                $error = true;
                $message = "The Student does not exist";
            }
            else{
                $programmbs->setFilestudent($filename);
                $programmbs->setStudent($student);
                $programmbs->setModality("option.modality1");
                $programmbs->setUploadDateStudent(new \DateTime('now', (new \DateTimeZone('America/New_York') ) ) ); 

                $programmbs->setState("state.revision");
                //$this->sendEmail("subject.pending_revision", $programmbs->getStudent());
                $programmbs->setProcess1([""]);
                $programmbs->setPaperwork3([""]);
                $programmbs->setPaperwork4([""]);
                $programmbs->setPaperwork5([""]);
                $programmbs->setPaperwork6([""]);
                $programmbs->setPaperwork7([""]);
                $programmbs->setPaperwork8([""]);
                $em->persist($programmbs);
                $em->flush();

            }            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="course_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets course info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The course with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Programmbs ID"
     * )
     *
     * @SWG\Tag(name="Programmbs")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programmbs = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $programmbs_id = $id;
            $programmbs = $em->getRepository("App:ProgramMbs")->find($programmbs_id);
 
            if (is_null($programmbs)) {
                $code = 500;
                $error = true;
                $message = "The Programmbs does not exist";
            }
            else{
                $cvDir = $this->container->getparameter('kernel.project_dir').'/web/file/'.$programmbs->getHistory2();
                if(!file_exists($cvDir) || $programmbs->getHistory2() == ""){
                    $programmbs->setHistory2("undefined");
                }
            }
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Programmbs- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Delete("/delete/{id}.{_format}", name="programmbs_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Programmbs was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the programmbs"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The programmbs ID"
     * )
     *
     * @SWG\Tag(name="Programmbs")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $programmbs = $em->getRepository("App:ProgramMbs")->find($id);
 
            if (!is_null($programmbs)) {
                $em->remove($programmbs);
                $em->flush();
 
                $message = "The programmbs was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent programmbs - Error: The programmbs id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Get("/search/{key}.{_format}", name="programmbs_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets programmbs info based on passed key parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The programmbs with the passed key parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="key",
     *     in="path",
     *     type="string",
     *     description="The programmbs key"
     * )
     *
     *
     * @SWG\Tag(name="Programmbs")
     */
    public function searchAction(Request $request, $key ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programmbs = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            
            $programmbs = $em->getRepository("App:ProgramMbs")->programmbsSearch($key);
 
            if (is_null($programmbs)) {
                $code = 500;
                $error = true;
                $message = "The programmbs does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/active_programmbs", name="active_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Programmbs was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Programmbs was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="state",
     *     in="body",
     *     type="string",
     *     description="The state",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language",
     * )
     * @SWG\Tag(name="Programmbs")
     */ 

    public function activeprogrammbsAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
 
            $programmbs = $this->getProgrammbs();
            $em = $this->getDoctrine()->getManager();
            $programmbs = $em->getRepository("App:ProgramMbs")->find($programmbs->getId());
            
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}", name="edit_programmbs")
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
     *     name="planrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="productrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="pricerevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="promotionrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="paperworkrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="processrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * @SWG\Tag(name="Programmbs")
     */

    public function editAction(Request $request, $id)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programmbs = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $programmbs = $em->getRepository('App:ProgramMbs')->find($id);
 
            if (!is_null($programmbs)) {
                $progressOrigin = $this->mbsProgress($programmbs);
                $form = $this->createForm(ProgramMbsType::class, $programmbs);
                $form->submit($request->request->all());
                
                $process1 = explode(',', $request->request->get('process1'));
                $programmbs->setProcess1($process1);
                //$programmbs->setProcess1([$process1]);
                $paperwork3 = explode(',', $request->request->get('paperwork3'));
                $programmbs->setPaperwork3($paperwork3);
                $programmbs->setPaperwork4(json_decode($programmbs->getPaperwork4()));
                $programmbs->setPaperwork5(json_decode($programmbs->getPaperwork5()));
                $programmbs->setPaperwork6(json_decode($programmbs->getPaperwork6()));
                $programmbs->setPaperwork7(json_decode($programmbs->getPaperwork7()));
                $programmbs->setPaperwork8(json_decode($programmbs->getPaperwork8()));
                
 
                if($this->mbsProgress($programmbs) >= $progressOrigin ){
                    $em->persist($programmbs);
                    $em->flush();
                }
                else{
                    $code = 500;
                    $error = true;
                    $message = "An error has occurred trying to edit the programmbs";
                }
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programmbs - Error: You must to provide fields programmbs or the programmbs id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/update_revision/{id}", name="update_revision_programmbs")
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
     *     name="planrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="productrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="pricerevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="promotionrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="paperworkrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="processrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * @SWG\Tag(name="Programmbs")
     */

    public function updateRevisionAction(Request $request, $id)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programmbs = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $programmbs = $em->getRepository('App:ProgramMbs')->find($id);
 
            if (!is_null($programmbs)) {
                $form = $this->createForm(ProgramMbsUpdateRevisionType::class, $programmbs);
                $form->submit($request->request->all());
 
                $em->persist($programmbs);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programmbs - Error: You must to provide fields programmbs or the programmbs id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programmbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/approved", name="approved_programmbs")
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
     *     description="The id programmbs",
     *     schema={}
     * )
     * @SWG\Tag(name="Programmbs")
     */

    public function approvedAction(Request $request)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programMb = [];
        $message = ""; 

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get('id');
            $programMb = $em->getRepository('App:ProgramMbs')->find($id);
 
            if (!is_null($programMb)) {
                $em = $this->getDoctrine()->getManager();
                
                $programMb->setState('state.approved');
                $programMb->setApprovalDate(new \DateTime('now', (new \DateTimeZone('America/New_York'))));
                if(!$programMb->getCode()){
                    if($programMb->getStudent()->getStudentGroup()->getGroup()->getProgram() == "option.program4"){
                        $programMb->setCode($programMb->getStudent()->getCountry()."-".$this->getNumberCodeJr($programMb, $request));
                    } 
                    else{
                        $programMb->setCode($programMb->getStudent()->getCountry()."-".$this->getNumberCode($programMb, $request));
                    }                   
                }

                $em->persist($programMb);
                $em->flush();
                
                //$this->sendEmail("subject.approved_project", $programMb->getStudent());
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programmbs - Error: You must to provide fields programmbs or the programmbs id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programMb : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/revision", name="revision_programmbs")
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
     *     description="The id programmbs",
     *     schema={}
     * )
     * @SWG\Tag(name="Programmbs")
     */

    public function revisionAction(Request $request)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programMb = [];
        $message = ""; 

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get('id');
            $programMb = $em->getRepository('App:ProgramMbs')->find($id);
 
            if (!is_null($programMb)) {
                $em = $this->getDoctrine()->getManager();

                
                $programMb->setState('state.revision');
                $em->persist($programMb);
                $em->flush();
                
                //$this->sendEmail("subject.approved_project", $programMb->getStudent());
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Programmbs - Error: You must to provide fields programmbs or the programmbs id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programMb : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/sendrevision", name="sendrevision_programmbs")
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
     *     description="The id programmbs",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="planrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="productrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="pricerevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="promotionrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="paperworkrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="processrevision",
     *     in="body",
     *     type="string",
     *     description="The revision description",
     *     schema={}
     * )
     * @SWG\Tag(name="Programmbs")
     */

    public function sendRevisionAction(Request $request)
    {

        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $programMb = [];
        $message = ""; 

        try {
            $code = 200;
            $error = false;
            $id = $request->request->get('id');
            $programMb = $em->getRepository('App:ProgramMbs')->find($id);
 
            if (!is_null($programMb)) {
                $em = $this->getDoctrine()->getManager();

                $form = $this->createForm(ProgramMbsUpdateRevisionType::class, $programMb);
                $form->submit($request->request->all());

                $programMb->setState('state.correction');
                //$this->sendEmail("subject.pending_correction", $programMb->getStudent());
            
                $em->persist($programMb);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to send revision Programmbs - Error: You must to provide fields programmbs or the programmbs id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying send revision of programmbs - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $programMb : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    public function getNumberCode(ProgramMbs $programMbs){

        $em = $this->getDoctrine()->getManager();
        $number = $em->getRepository('App:Certificate')->findOneBy(array('country' => $programMbs->getStudent()->getCountry(), 'program' => 'MBS'));
        if($number){
          $number->setNumber($number->getNumber() + 1);
          $em->persist($number);
          $em->flush();
        }
        else{
          $number = new Certificate();
          $number->setCountry($programMbs->getStudent()->getCountry());
          $number->setProgram("MBS");
          $number->setNumber(1);
          $em->persist($number);
          $em->flush();
        }
  
        return $number->getNumber();
    }

    public function getNumberCodeJr(ProgramMbs $programMbs){

        $em = $this->getDoctrine()->getManager();
        $number = $em->getRepository('App:Certificate')->findOneBy(array('country' => $programMbs->getStudent()->getCountry(), 'program' => 'JR'));
        if($number){
          $number->setNumber($number->getNumber() + 1);
          $em->persist($number);
          $em->flush();
        }
        else{
          $number = new Certificate();
          $number->setCountry($programMbs->getStudent()->getCountry());
          $number->setProgram("JR");
          $number->setNumber(1);
          $em->persist($number);
          $em->flush();
        }
  
        return $number->getNumber();
    }

    

    public function mbsProgress($programMbs){

        $plan = 0;
        $product = 0;
        $process = 0;
        $promotion = 0;
        $paperwork = 0;
        $price = 0;
        $service = 0;
        $quality = 0;

        $paperwork4 = json_decode(json_encode($programMbs->getPaperwork4()), true);
        $paperwork5 = json_decode(json_encode($programMbs->getPaperwork5()), true);
        $paperwork6 = json_decode(json_encode($programMbs->getPaperwork6()), true);
        $paperwork7 = json_decode(json_encode($programMbs->getPaperwork7()), true);
        $paperwork8 = json_decode(json_encode($programMbs->getPaperwork8()), true);

        if( $programMbs->getPlan1() )              { $plan = $plan + 50; }
        if( $programMbs->getPlan2() )              { $plan = $plan + 50; }
        if( $programMbs->getProduct1() )           { $product = $product + 14; }
        if( $programMbs->getProduct2() )           { $product = $product + 14; }
        if( $programMbs->getProduct3() )           { $product = $product + 14; }
        if( $programMbs->getProduct4() )           { $product = $product + 14; }
        if( $programMbs->getProduct5() )           { $product = $product + 14; }
        if( $programMbs->getProduct6() )           { $product = $product + 14; }
        if( $programMbs->getProduct7() )           { $product = $product + 16; }
        if( $programMbs->getProcess1()[0] != "" )  { $process = $process + 25; }
        if( $programMbs->getProcess2() )           { $process = $process + 25; }
        if( $programMbs->getProcess3() )           { $process = $process + 25; }
        if( $programMbs->getProcess4() )           { $process = $process + 25; }
        if( $programMbs->getPrice1() )             { $price = $price + 25; }
        if( $programMbs->getPrice2() )             { $price = $price + 25; }
        if( $programMbs->getPrice3() )             { $price = $price + 25; }
        if( $programMbs->getPrice4() )             { $price = $price + 25; }
        if( $programMbs->getPaperwork1() )         { $paperwork = $paperwork + 14; }
        if( $programMbs->getPaperwork3()[0] != "" )      { $paperwork = $paperwork + 14; }
        if( $paperwork4['p4_balance']  != "" )     { $paperwork = $paperwork + 14; }
        if( $paperwork5['p5_income']   != "" )       { $paperwork = $paperwork + 14; }
        if( $paperwork6['p6_balance']  != "" )      { $paperwork = $paperwork + 14; }
        if( $paperwork7['p7_income'][0]   != "" )       { $paperwork = $paperwork + 14; }
        if( $paperwork8['p8_balance'][0]  != "" )      { $paperwork = $paperwork + 16; }
        if( $programMbs->getService1() )             { $service = $service + 20; }
        if( $programMbs->getService2() )             { $service = $service + 20; }
        if( $programMbs->getService3() )             { $service = $service + 20; }
        if( $programMbs->getService4() )             { $service = $service + 20; }
        if( $programMbs->getService5() )             { $service = $service + 20; }
        if( $programMbs->getPromotion1() )           { $promotion = $promotion + 25; }
        if( $programMbs->getPromotion2() )           { $promotion = $promotion + 25; }
        if( $programMbs->getPromotion3() )           { $promotion = $promotion + 25; }
        if( $programMbs->getPromotion4() )           { $promotion = $promotion + 25; }
        if( $programMbs->getQualityP1() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP2() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP3() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP4() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP5() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP6() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP7() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityP8() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG1() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG2() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG3() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG4() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG5() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG6() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG7() )            { $quality = $quality + 6; }
        if( $programMbs->getQualityG8() )            { $quality = $quality + 10; }

        return $plan + $product + $process +  $promotion +  $paperwork +  $price +  $service + $quality = 0;

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
          $bodyEmail = $this->get('translator')->trans('message.approved_project', [] , null, $studentGroup->getGroup()->getEmbassador()->getLanguage())." ".$student->getFirstName()." ".$student->getLastName()." ".$student->getProgrammbs()->getCode();
  
          $message = (new \Swift_Message($subjectEmail))
           ->setFrom('myplatform@interweavesolutions.com')
           ->setTo($studentGroup->getGroup()->getEmbassador()->getUsername())
           ->setBody($bodyEmail);
  
           //$mailer->send($message);
           $this->get('mailer')->send($message);
  
           // Sent Notification to Student
           $subjectEmail = $this->get('translator')->trans($subject, [] , null, $student->getLanguage());
           $bodyEmail = $this->get('translator')->trans('message.approved_project', [] , null, $student->getLanguage())." ".$student->getFirstName()." ".$student->getLastName()." ".$student->getProgrammbs()->getCode();
  
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
                    $bodyEmail = $this->get('translator')->trans('message.approved_project_admin', [] , null, "en")." ".$student->getFirstName()." ".$student->getLastName()." whith the follow code ".$student->getProgrammbs()->getCode();
  
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