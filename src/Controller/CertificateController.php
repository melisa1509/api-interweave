<?php
namespace App\Controller;
 
use App\Entity\User;
use App\Entity\Task;
use App\Form\UserType;
use App\Form\UserEditType;
use Doctrine\Common\Collections\ArrayCollection;
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
 * Class CertificateController
 *
 * @Route("/certificate")
 */
class CertificateController extends FOSRestController
{
    
    /**
     * @Rest\Get("/list/{id}.{_format}", name="certificate_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     *
     * @SWG\Tag(name="Certificate")
     */
    public function listAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $studentList = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $group_id = $id;
            $studentMbsList = $em->getRepository("App:StudentGroup")->findBy(array('group' => $group_id));
            $studentAmbassadorList = $em->getRepository("App:StudentAmbassadorGroup")->findBy(array('group' => $group_id));

            $studentList = new ArrayCollection(
                array_merge($studentMbsList, $studentAmbassadorList )
            );
            
 
            if (is_null($studentList)) {
                $code = 500;
                $error = true;
                $message = "The group not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the list students - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $studentList : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('student_group'))));
    }

    /**
     * @Rest\Get("/mbs/student/{id}.{_format}", name="certificate_mbs_student", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     * @SWG\Tag(name="Certificate")
     */
    public function mbsStudentAction(Request $request, $id ) {

      $em = $this->getDoctrine()->getManager(); 

      $student = $em->getRepository('App:User')->find($id);

      $pdf = $this->get('white_october.tcpdf.public')->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      
      $pdf->SetAuthor('Interweave Solutions');
      $pdf->SetTitle(('MBS Certificate'));
      $pdf->SetSubject('MBS Certificate');
      $pdf->setFontSubsetting(true);

      
      $pdf->SetFont("snellb", '', 8);



        $arr_code = str_split($student->getProgramMbs()->getCode(), 4);
        $code = $student->getProgramMbs()->getCode();
        if(strlen($arr_code[1]) == 1){
          $code = $arr_code[0]."00".$arr_code[1];
        }
        elseif(strlen($arr_code[1]) == 2){
          $code = $arr_code[0]."0".$arr_code[1];
        }

        $interweaveLocal = "";
        $autorizationCode = "";

        if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
            $autorizationCode = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
        }
        else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
        }
        else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
        
        }
        else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
        }
        else{
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
            $autorizationCode = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
        }


        $html = '<h2></h2>
        <table border="0"  >
            <tr>
                <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:280pt;"></th>
            </tr>
            <tr>
                <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:107pt;">'.ucwords(mb_strtolower($student->getFirstName().' '.$student->getLastName(),'UTF-8')).'</th>
            </tr>
            <tr>
                <td  align="center" width="13%"></td>
                <td  align="center" width="22%"></td>
                <td  align="center" width="10%"></td>
                <td  align="center" width="10%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="10%" ></td>
                <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:33pt;">'.$interweaveLocal.'</td>
                <td  align="center" width="10%"></td>
            </tr>


        </table>';

        $html2 = '<h2></h2>
        <table border="0" >
        <tr>
            <td  align="center" width="13%"></td>
            <td  align="center" width="22%" style="height:10pt;font-size:13pt;"> MBS '.$code.' </td>
            <td  align="center" width="10%"></td>
            <td  align="center" width="10%"></td>
            <td  align="center" width="1%"></td>
            <td  align="center" width="10%" ></td>
            <td  align="center" width="22%" style="height:10pt;font-size:13pt;">'.$autorizationCode.'</td>
            <td  align="center" width="10%"></td>
        </tr>

        </table>';




          // add a page
          $resolution= array(279, 216);
          $pdf->AddPage('L', $resolution);

          // get the current page break margin
          $bMargin = $pdf->getBreakMargin();
          // get current auto-page-break mode
          $auto_page_break = $pdf->getAutoPageBreak();
          // disable auto-page-break
          $pdf->SetAutoPageBreak(false, 0);
          // set bacground image

          //$img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_GraduateGreen01.png';

          //$pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);

          if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_mbs_certificate_all_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }
          else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_mbs_certificate_one_line.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }
          else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_mbs_certificate_no_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }
          else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_mbs_certificate_logo.png';
            $img_logo = $this->container->getparameter('kernel.project_dir').'/web/file/'.$student->getStudentGroup()->getGroup()->getNameImage();
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            $pdf->Image($img_logo, 196, 167, '', 24, '', '', '', false, 600, '', false, false, 0);
          }
          else{
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_mbs_certificate_all_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }

          

          
          // restore auto-page-break status
          $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
          // set the starting point for the page content
          $pdf->setPageMark();


          // Print a text
          // Print a text
          $pdf->writeHTML($html, true, false, true, false, '');
          $pdf->SetFont("times", '', 6);
          $pdf->writeHTML($html2, true, false, true, false, '');
          $pdf->SetFont("snellb", '', 8);


      //Close and output PDF document
      $pdf->Output('certificate_mbs.pdf', 'I');
    }

    /**
     * @Rest\Get("/mbs/list/{id}.{_format}", name="certificate_mbs_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     *
     * @SWG\Tag(name="Certificate")
     */
    public function mbsListAction(Request $request, $id ) {

        $em = $this->getDoctrine()->getManager(); 
  
        $students = $em->getRepository('App:StudentGroup')->studentsMbsStateByGroup($id, "state.approved");
  
        $pdf = $this->get('white_october.tcpdf.public')->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetAuthor('Interweave Solutions');
        $pdf->SetTitle(('MBS Certificate'));
        $pdf->SetSubject('MBS Certificate');
        $pdf->setFontSubsetting(true);
  
        
        $pdf->SetFont("snellb", '', 8);
  
  
        foreach ($students as $student) {
  
          $arr_code = str_split($student->getStudent()->getProgramMbs()->getCode(), 4);
          $code = $student->getStudent()->getProgramMbs()->getCode();
          if(strlen($arr_code[1]) == 1){
            $code = $arr_code[0]."00".$arr_code[1];
          }
          elseif(strlen($arr_code[1]) == 2){
            $code = $arr_code[0]."0".$arr_code[1];
          }
  
          $interweaveLocal = "";
          $autorizationCode = "";
  
          if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
              $autorizationCode = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
          }
          else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
          }
          else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
          
          }
          else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
          }
          else{
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
              $autorizationCode = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
          }
  
  
          $html = '<h2></h2>
          <table border="0"  >
              <tr>
                  <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:280pt;"></th>
              </tr>
              <tr>
                  <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:107pt;">'.ucwords(mb_strtolower($student->getStudent()->getFirstName().' '.$student->getStudent()->getLastName(),'UTF-8')).'</th>
              </tr>
              <tr>
                  <td  align="center" width="13%"></td>
                  <td  align="center" width="22%"></td>
                  <td  align="center" width="10%"></td>
                  <td  align="center" width="10%"></td>
                  <td  align="center" width="1%"></td>
                  <td  align="center" width="10%" ></td>
                  <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:33pt;">'.$interweaveLocal.'</td>
                  <td  align="center" width="10%"></td>
              </tr>
  
  
          </table>';
  
          $html2 = '<h2></h2>
          <table border="0" >
          <tr>
              <td  align="center" width="13%"></td>
              <td  align="center" width="22%" style="height:10pt;font-size:13pt;"> MBS '.$code.' </td>
              <td  align="center" width="10%"></td>
              <td  align="center" width="10%"></td>
              <td  align="center" width="1%"></td>
              <td  align="center" width="10%" ></td>
              <td  align="center" width="22%" style="height:10pt;font-size:13pt;">'.$autorizationCode.'</td>
              <td  align="center" width="10%"></td>
          </tr>
  
          </table>';
  
  
  
  
            // add a page
            $resolution= array(279, 216);
            $pdf->AddPage('L', $resolution);
  
            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // set bacground image
  
            //$img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_GraduateGreen01.png';
  
            //$pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
  
            if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_mbs_certificate_all_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
            else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_mbs_certificate_one_line.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
            else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_mbs_certificate_no_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
            else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_mbs_certificate_logo.png';
              $img_logo = $this->container->getparameter('kernel.project_dir').'/web/file/'.$student->getStudent()->getStudentGroup()->getGroup()->getNameImage();
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
              $pdf->Image($img_logo, 196, 167, '', 24, '', '', '', false, 600, '', false, false, 0);
            }
            else{
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_mbs_certificate_all_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
  
            
  
            
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $pdf->setPageMark();
  
  
            // Print a text
            // Print a text
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->SetFont("times", '', 6);
            $pdf->writeHTML($html2, true, false, true, false, '');
            $pdf->SetFont("snellb", '', 8);
  
  
        }
        //Close and output PDF document
        $pdf->Output('certificate_list_mbs.pdf', 'I');
      }

    /**
     * @Rest\Get("/ambassador/student/{id}.{_format}", name="certificate_ambassador_student", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     * @SWG\Tag(name="Certificate")
     */
    public function ambassadorStudentAction(Request $request, $id ) {

      $em = $this->getDoctrine()->getManager();
      $student = $em->getRepository('App:User')->find($id);

      $ambassador = $em->getRepository('App:ProgramSa')->findOneBy(array( 'student' => $id));

      $arr_code = str_split($ambassador->getCode(), 4);
      $code = $ambassador->getCode();
      if(strlen($arr_code[1]) == 1){
        $code = $arr_code[0]."00".$arr_code[1];
      }
      elseif(strlen($arr_code[1]) == 2){
        $code = $arr_code[0]."0".$arr_code[1];
      }

      if( $student->getStudentAmbassadorGroup() ){
        $interweaveLocal = $student->getStudentAmbassadorGroup()->getGroup()->getInterweaveLocal();
        $authorizationCode = $student->getStudentAmbassadorGroup()->getGroup()->getAuthorizationCode();
      }
      else{
        $interweaveLocal = $student->getStudentGroup()->getGroup()->getInterweaveLocal();
        $authorizationCode = $student->getStudentGroup()->getGroup()->getAuthorizationCode();
      }

      if($ambassador){

          $pdf = $this->get("white_october.tcpdf.public")->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
          $pdf->SetAuthor('Interweave Solutions');
          $pdf->SetTitle(('Ambassador Certificate'));
          $pdf->SetSubject('Ambassador Certificate');
          $pdf->setFontSubsetting(true);
          // set font
          $pdf->SetFont("snellb", '', 8);
          $html = '<h2></h2>
          <table border="0"  >
              <tr>
                  <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:280pt;"></th>
              </tr>
              <tr>
                  <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:100pt;">'.ucwords(mb_strtolower($student->getFirstName().' '.$student->getLastName(),'UTF-8')).'</th>
              </tr>
              <tr>
                  <td  align="center" width="13%"></td>
                  <td  align="center" width="22%"></td>
                  <td  align="center" width="10%"></td>
                  <td  align="center" width="10%"></td>
                  <td  align="center" width="1%"></td>
                  <td  align="center" width="9%" ></td>
                  <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:28pt;">'.ucwords(mb_strtolower($interweaveLocal,'UTF-8')).'</td>
                  <td  align="center" width="13%"></td>
              </tr>


          </table>';

          $html2 = '<h2></h2>
          <table border="0" >
          <tr>
              <td  align="center" width="13%"></td>
              <td  align="center" width="22%" style="height:10pt;font-size:13pt;"> SA '.$code.' </td>
              <td  align="center" width="10%"></td>
              <td  align="center" width="10%"></td>
              <td  align="center" width="1%"></td>
              <td  align="center" width="9%" ></td>
              <td  align="center" width="22%" style="height:10pt;font-size:13pt;">'.$authorizationCode.'</td>
              <td  align="center" width="13%"></td>
          </tr>

          </table>';


          // add a page
          $resolution= array(279, 216);
          $pdf->AddPage('L', $resolution);

          // get the current page break margin
          $bMargin = $pdf->getBreakMargin();
          // get current auto-page-break mode
          $auto_page_break = $pdf->getAutoPageBreak();
          // disable auto-page-break
          $pdf->SetAutoPageBreak(false, 0);
          // set bacground image

          $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_ambassador.png';

          $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          // restore auto-page-break status
          $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
          // set the starting point for the page content
          $pdf->setPageMark();


          // Print a text
          $pdf->writeHTML($html, true, false, true, false, '');
          $pdf->SetFont("times", '', 6);
          $pdf->writeHTML($html2, true, false, true, false, '');

          //Close and output PDF document
          $pdf->Output('certificate_ambassador.pdf', 'I');
      }
    }

    /**
     * @Rest\Get("/ambassador/list/{id}.{_format}", name="certificate_ambassador_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     *
     * @SWG\Tag(name="Certificate")
     */
    public function ambassadorListAction(Request $request, $id ) {

       
      $em = $this->getDoctrine()->getManager();

      $students = $em->getRepository('App:StudentGroup')->studentsAmbassadorStateByGroup($id, "state.approved");

      $pdf = $this->get("white_october.tcpdf.public")->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      $pdf->SetAuthor('Interweave Solutions');
      $pdf->SetTitle(('MBS Certificate'));
      $pdf->SetSubject('MBS Certificate');
      $pdf->setFontSubsetting(true);
      // set font
      $pdf->SetFont("snellb", '', 8);

      foreach ($students as $student) {

        $arr_code = str_split($student->getStudent()->getProgramSa()->getCode(), 4);
        $code = $student->getStudent()->getProgramSa()->getCode();
        if(strlen($arr_code[1]) == 1){
          $code = $arr_code[0]."00".$arr_code[1];
        }
        elseif(strlen($arr_code[1]) == 2){
          $code = $arr_code[0]."0".$arr_code[1];
        }

        if( $student->getStudent()->getStudentAmbassadorGroup() ){
          $interweaveLocal = $student->getStudent()->getStudentAmbassadorGroup()->getGroup()->getInterweaveLocal();
          $authorizationCode = $student->getStudent()->getStudentAmbassadorGroup()->getGroup()->getAuthorizationCode();
        }
        else{
          $interweaveLocal = $student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal();
          $authorizationCode = $student->getStudent()->getStudentGroup()->getGroup()->getAuthorizationCode();
        }

        $html = '<h2></h2>
        <table border="0"  >
            <tr>
                <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:280pt;"></th>
            </tr>
            <tr>
                <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:100pt;">'.ucwords(mb_strtolower($student->getStudent()->getFirstName().' '.$student->getStudent()->getLastName(),'UTF-8')).'</th>
            </tr>
            <tr>
                <td  align="center" width="13%"></td>
                <td  align="center" width="22%"></td>
                <td  align="center" width="10%"></td>
                <td  align="center" width="10%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="9%" ></td>
                <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:28pt;">'.ucwords(mb_strtolower($interweaveLocal,'UTF-8')).'</td>
                <td  align="center" width="13%"></td>
            </tr>


        </table>';

        $html2 = '<h2></h2>
        <table border="0" >
        <tr>
            <td  align="center" width="13%"></td>
            <td  align="center" width="22%" style="height:10pt;font-size:13pt;"> SA '.$code.' </td>
            <td  align="center" width="10%"></td>
            <td  align="center" width="10%"></td>
            <td  align="center" width="1%"></td>
            <td  align="center" width="9%" ></td>
            <td  align="center" width="22%" style="height:10pt;font-size:13pt;">'.$authorizationCode.'</td>
            <td  align="center" width="13%"></td>
        </tr>

        </table>';


          // add a page
          $resolution= array(279, 216);
          $pdf->AddPage('L', $resolution);

          // get the current page break margin
          $bMargin = $pdf->getBreakMargin();
          // get current auto-page-break mode
          $auto_page_break = $pdf->getAutoPageBreak();
          // disable auto-page-break
          $pdf->SetAutoPageBreak(false, 0);
          // set bacground image

          $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_ambassador.png';

          $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          // restore auto-page-break status
          $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
          // set the starting point for the page content
          $pdf->setPageMark();


          // Print a text
          $pdf->writeHTML($html, true, false, true, false, '');
          $pdf->SetFont("times", '', 6);
          $pdf->writeHTML($html2, true, false, true, false, '');
          $pdf->SetFont("snellb", '', 8);


      }
      //Close and output PDF document
      $pdf->Output('certificate_list_ambassador.pdf', 'I');

      }

      /**
     * @Rest\Get("/jr/student/{id}.{_format}", name="certificate_jr_student", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     * @SWG\Tag(name="Certificate")
     */
    public function jrStudentAction(Request $request, $id ) {

      $em = $this->getDoctrine()->getManager(); 

      $student = $em->getRepository('App:User')->find($id);

      $pdf = $this->get('white_october.tcpdf.public')->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      
      $pdf->SetAuthor('Interweave Solutions');
      $pdf->SetTitle(('MBS Certificate'));
      $pdf->SetSubject('MBS Certificate');
      $pdf->setFontSubsetting(true);

      
      $pdf->SetFont("snellb", '', 8);



        $arr_code = str_split($student->getProgramMbs()->getCode(), 4);
        $code = $student->getProgramMbs()->getCode();
        if(strlen($arr_code[1]) == 1){
          $code = $arr_code[0]."00".$arr_code[1];
        }
        elseif(strlen($arr_code[1]) == 2){
          $code = $arr_code[0]."0".$arr_code[1];
        }

        $interweaveLocal = "";
        $autorizationCode = "";

        if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
            $autorizationCode = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
        }
        else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
        }
        else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
        
        }
        else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
        }
        else{
            $interweaveLocal = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
            $autorizationCode = ucwords(mb_strtolower($student->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
        }


        $html = '<h2></h2>
        <table border="0"  >
            <tr>
                <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:180pt;"></th>
            </tr>
            <tr>
                <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:207pt;">'.ucwords(mb_strtolower($student->getFirstName().' '.$student->getLastName(),'UTF-8')).'</th>
            </tr>
            <tr>
                <td  align="center" width="12%"></td>
                <td  align="center" width="2%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="5%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="10%" ></td>
                <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:33pt;">'.$interweaveLocal.'</td>
                <td  align="center" width="45%"></td>
            </tr>


        </table>';

        $html2 = '<h2></h2>
        <table border="0" >
        <tr>
            <td  align="center" width="9%"></td>
            <td  align="center" width="12%" style="height:10pt;font-size:13pt;"> MBS '.$code.' </td>
            <td  align="center" width="10%"></td>
            <td  align="center" width="10%"></td>
            <td  align="center" width="1%"></td>
            <td  align="center" width="10%" ></td>
            <td  align="center" width="22%" style="height:10pt;font-size:13pt;">'.$autorizationCode.'</td>
            <td  align="center" width="20%"></td>
        </tr>

        </table>';




          // add a page
          $resolution= array(279, 216);
          $pdf->AddPage('L', $resolution);

          // get the current page break margin
          $bMargin = $pdf->getBreakMargin();
          // get current auto-page-break mode
          $auto_page_break = $pdf->getAutoPageBreak();
          // disable auto-page-break
          $pdf->SetAutoPageBreak(false, 0);
          // set bacground image

          //$img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_GraduateGreen01.png';

          //$pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);

          if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_jr_certificate_all_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }
          else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_jr_certificate_all_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }
          else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_jr_certificate_all_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }
          else if($student->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_jr_certificate_all_lines.png';
            $img_logo = $this->container->getparameter('kernel.project_dir').'/web/file/'.$student->getStudentGroup()->getGroup()->getNameImage();
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            $pdf->Image($img_logo, 196, 167, '', 24, '', '', '', false, 600, '', false, false, 0);
          }
          else{
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getLanguage().'_jr_certificate_all_lines.png';
            $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
          }

          

          
          // restore auto-page-break status
          $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
          // set the starting point for the page content
          $pdf->setPageMark();


          // Print a text
          // Print a text
          $pdf->writeHTML($html, true, false, true, false, '');
          $pdf->SetFont("times", '', 6);
          $pdf->writeHTML($html2, true, false, true, false, '');
          $pdf->SetFont("snellb", '', 8);


      //Close and output PDF document
      $pdf->Output('certificate_jr.pdf', 'I');
    }

    /**
     * @Rest\Get("/jr/list/{id}.{_format}", name="certificate_jr_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     *
     * @SWG\Tag(name="Certificate")
     */
    public function jrListAction(Request $request, $id ) {

        $em = $this->getDoctrine()->getManager(); 
  
        $students = $em->getRepository('App:StudentGroup')->studentsMbsStateByGroup($id, "state.approved");
  
        $pdf = $this->get('white_october.tcpdf.public')->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetAuthor('Interweave Solutions');
        $pdf->SetTitle(('MBS Certificate'));
        $pdf->SetSubject('MBS Certificate');
        $pdf->setFontSubsetting(true);
  
        
        $pdf->SetFont("snellb", '', 8);
  
  
        foreach ($students as $student) {
  
          $arr_code = str_split($student->getStudent()->getProgramMbs()->getCode(), 4);
          $code = $student->getStudent()->getProgramMbs()->getCode();
          if(strlen($arr_code[1]) == 1){
            $code = $arr_code[0]."00".$arr_code[1];
          }
          elseif(strlen($arr_code[1]) == 2){
            $code = $arr_code[0]."0".$arr_code[1];
          }
  
          $interweaveLocal = "";
          $autorizationCode = "";
  
          if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
              $autorizationCode = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
          }
          else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
          }
          else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
          
          }
          else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
          }
          else{
              $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));
              $autorizationCode = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getAuthorizationCode(),'UTF-8'));
          }
  
  
          $html = '<h2></h2>
          <table border="0"  >
              <tr>
                  <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:180pt;"></th>
              </tr>
              <tr>
                  <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:207pt;">'.ucwords(mb_strtolower($student->getStudent()->getFirstName().' '.$student->getStudent()->getLastName(),'UTF-8')).'</th>
              </tr>
              <tr>
                <td  align="center" width="12%"></td>
                <td  align="center" width="2%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="5%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="10%" ></td>
                <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:33pt;">'.$interweaveLocal.'</td>
                <td  align="center" width="45%"></td>
            </tr>
  
  
          </table>';
  
          $html2 = '<h2></h2>
          <table border="0" >
          <tr>
              <td  align="center" width="9%"></td>
              <td  align="center" width="12%" style="height:10pt;font-size:13pt;"> MBS '.$code.' </td>
              <td  align="center" width="10%"></td>
              <td  align="center" width="10%"></td>
              <td  align="center" width="1%"></td>
              <td  align="center" width="10%" ></td>
              <td  align="center" width="22%" style="height:10pt;font-size:13pt;">'.$autorizationCode.'</td>
              <td  align="center" width="20%"></td>
          </tr>
  
          </table>';
  
  
  
  
            // add a page
            $resolution= array(279, 216);
            $pdf->AddPage('L', $resolution);
  
            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // set bacground image
  
            //$img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_GraduateGreen01.png';
  
            //$pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
  
            if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 1){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_jr_certificate_all_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
            else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 2){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_jr_certificate_all_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
            else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 3){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_jr_certificate_all_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
            else if($student->getStudent()->getStudentGroup()->getGroup()->getNumberStudentsGraduated() == 4){
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_jr_certificate_all_lines.png';
              $img_logo = $this->container->getparameter('kernel.project_dir').'/web/file/'.$student->getStudent()->getStudentGroup()->getGroup()->getNameImage();
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
              $pdf->Image($img_logo, 196, 167, '', 24, '', '', '', false, 600, '', false, false, 0);
            }
            else{
              $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_jr_certificate_all_lines.png';
              $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);
            }
  
            
  
            
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $pdf->setPageMark();
  
  
            // Print a text
            // Print a text
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->SetFont("times", '', 6);
            $pdf->writeHTML($html2, true, false, true, false, '');
            $pdf->SetFont("snellb", '', 8);
  
  
        }
        //Close and output PDF document
        $pdf->Output('certificate_list_jr.pdf', 'I');
      }

     /**
     * @Rest\Get("/attendance/list/{id}.{_format}", name="certificate_attendance_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets students of group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The students with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     *
     * @SWG\Tag(name="Certificate")
     */
    public function attendanceListAction(Request $request, $id ) {

      $em = $this->getDoctrine()->getManager(); 

      $students = $em->getRepository('App:StudentGroup')->studentsDifferentStateByGroup($id, "state.approved");

      $pdf = $this->get('white_october.tcpdf.public')->create('horizontal', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      
      $pdf->SetAuthor('Interweave Solutions');
      $pdf->SetTitle(('MBS Certificate'));
      $pdf->SetSubject('MBS Certificate');
      $pdf->setFontSubsetting(true);

      
      $pdf->SetFont("snellb", '', 8);


      foreach ($students as $student) {

        
        $interweaveLocal = ucwords(mb_strtolower($student->getStudent()->getStudentGroup()->getGroup()->getInterweaveLocal(),'UTF-8'));


        $html = '<h2></h2>
        <table border="0"  >
            <tr>
                <th colspan="8" align="right" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:273pt;"></th>
            </tr>
            <tr>
                <th colspan="8" align="center" style="color:black;text-align:center;font-weight:bold;font-size:30pt;height:135pt;">'.ucwords(mb_strtolower($student->getStudent()->getFirstName().' '.$student->getStudent()->getLastName(),'UTF-8')).'</th>
            </tr>
            <tr>
                <td  align="center" width="13%"></td>
                <td  align="center" width="22%"></td>
                <td  align="center" width="10%"></td>
                <td  align="center" width="10%"></td>
                <td  align="center" width="1%"></td>
                <td  align="center" width="10%" ></td>
                <td  align="center" width="22%" style="height:10pt;font-size:13pt;height:33pt;">'.$interweaveLocal.'</td>
                <td  align="center" width="10%"></td>
            </tr>


        </table>';


          // add a page
          $resolution= array(279, 216);
          $pdf->AddPage('L', $resolution);

          // get the current page break margin
          $bMargin = $pdf->getBreakMargin();
          // get current auto-page-break mode
          $auto_page_break = $pdf->getAutoPageBreak();
          // disable auto-page-break
          $pdf->SetAutoPageBreak(false, 0);
          // set bacground image



          
          $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/'.$student->getStudent()->getLanguage().'_attendance.jpg';
          $pdf->Image($img_file, 0, 0, 279, 216, '', '', '', false, 600, '', false, false, 0);

          

          
          // restore auto-page-break status
          $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
          // set the starting point for the page content
          $pdf->setPageMark();


          // Print a text
          $pdf->writeHTML($html, true, false, true, false, '');
          $pdf->SetFont("times", '', 6);
          $pdf->SetFont("snellb", '', 8);
          

      }
      //Close and output PDF document
      $pdf->Output('certificate_list_mbs.pdf', 'I');
    }

 
}