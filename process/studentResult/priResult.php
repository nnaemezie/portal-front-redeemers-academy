<?php

session_start();
/* Caveat: I'm not a PHP programmer, so this may or may
 * not be the most idiomatic code...
 *
 * FPDF is a free PHP library for creating PDFs:
 * http://www.fpdf.org/
 */
$term = $_GET['term'];
$admno = $_GET['admno'];
$card = $_GET['card'];
$class = $_GET['class'];

include "../config/configTraco.php";
$conn = new mysqli(db_host, db_user, db_pass, db_name);

$student_data = "SELECT * FROM student_data where admno = '$admno'  ";
$result_student = $conn->query($student_data);

if ($result_student->num_rows > 0) {
    $row = $result_student->fetch_assoc();
    $_SESSION['sd'] = $row;
}

require("fpdf/fpdf.php");

$failSub = 0;
$passSub = 0;

class PDF extends FPDF {
    const DPI = 150;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 210;
    const A4_WIDTH = 297;
    /*const Legal_HEIGHT = 215;
    const Legal_WIDTH = 350;*/
    // tweak these values (in pixels)
    const MAX_WIDTH = 1150;
    const MAX_HEIGHT = 1650;
    function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
    }
    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }

    function centreImage($img) {
        list($width, $height) = $this->resizeToFit($img);
        // you will probably want to swap the width/height
        // around depending on the page's orientation
        $this->Image(
            $img, (self::A4_HEIGHT - $width) / 2,
            (self::A4_WIDTH - $height) / 2,
            $width,
            $height
        );
    }

    function calAv($conn){
        global $class;
        global $term;
        global $admno;
        $avTotal = 0;
        $cnt=0;
        $sql = "SELECT * FROM published_result WHERE class = '".$class."' AND term = '".$term."' AND admno = '".$admno."' ";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $total = (float)$row['ca1'] + (float)$row['ca2'] + (float)$row['ca3'] + (float)$row['exam'];
            $avTotal = $avTotal + $total;
            $cnt++;
        }
        $splitAv = $avTotal/$cnt;
        $splitAv = round($splitAv,3);
        return $splitAv .'%';
    }

    function principalRemark($conn){
        global $class;
        global $term;
        global $admno;
        $avTotal = 0;
        $cnt=0;
        $sql = "SELECT * FROM published_result WHERE class = '".$class."' AND term = '".$term."' AND admno = '".$admno."' ";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $total = (float)$row['ca1'] + (float)$row['ca2'] + (float)$row['ca3'] + (float)$row['exam'];
            $avTotal = $avTotal + $total;
            $cnt++;
        }
        $splitAv = $avTotal/$cnt;
        $splitAv = round($splitAv,3);

        if ($splitAv >= 70) {
           return 'EXCELLENT PERFORMANCE.';
        }elseif ($splitAv >= 60) {
           return 'VERY GOOD RESULT.';
        }elseif ($splitAv >= 50) {
           return 'GOOD RESULT.';
        }elseif ($splitAv >= 40) {
           return 'FAIR RESULT.';
        }elseif ($splitAv < 40) {
           return 'POOR RESULT, KEEP TRYING';
        }
    }

    function formTeacherRemark($conn){
        global $class;
        global $term;
        global $admno;
        $avTotal = 0;
        $cnt=0;
        $sql = "SELECT * FROM published_result WHERE class = '".$class."' AND term = '".$term."' AND admno = '".$admno."' ";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $total = (float)$row['ca1'] + (float)$row['ca2'] + (float)$row['ca3'] + (float)$row['exam'];
            $avTotal = $avTotal + $total;
            $cnt++;
        }
        $splitAv = $avTotal/$cnt;
        $splitAv = round($splitAv,3);

        if ($splitAv >= 70) {
           return 'EXCELLENT RESULT, KEEP IT UP.';
        }elseif ($splitAv >= 60) {
           return 'VERY GOOD RESULT, KEEP TRYING.';
        }elseif ($splitAv >= 50) {
           return 'GOOD RESULT, PUT MORE EFFORT.';
        }elseif ($splitAv >= 40) {
           return 'FAIR RESULT, WORK MORE HARD.';
        }elseif ($splitAv < 40) {
           return 'POOR RESULT, MORE EFFORT IS NEEDED.';
        }
    }

    function getPosition($conn){
        global $class;
        global $term;
        global $admno;
         $sql17 = " SELECT * FROM student_data WHERE class = '".$class."' ";
         $result17 = mysqli_query($conn, $sql17);
         while ($row17 = mysqli_fetch_array($result17)) {
             $admnoGotten = $row17['admno'];
             $sql18 = " SELECT * FROM published_result WHERE term = '".$term."' AND class = '".$class."' AND admno = '".$admnoGotten."' ";
             $result18 = mysqli_query($conn, $sql18);

             $counter = 0;
             $avg_current = 0;
             while ($row18 = mysqli_fetch_array($result18)) {
                 $avg_current = $avg_current + $row18['ca1'] + $row18['ca2'] + $row18['ca3'] + $row18['exam'];
                 $counter++;
             }

             $confirm_avg = $avg_current/$counter;

             $sql202 = "INSERT INTO class_pos_temp(admno, session, class, position) VALUES ('".$admnoGotten."', '".$term. "', '" .$class. "', '".$confirm_avg."' )";
             mysqli_query($conn, $sql202);    
         }

         $sql203 = "SELECT * FROM class_pos_temp ORDER BY position DESC";
         $result203 = mysqli_query($conn, $sql203);
         while ($row203 = mysqli_fetch_array($result203)) {
             $sql204 = "INSERT INTO class_pos_temp_2(admno, position) VALUES ('".$row203['admno']."', '".$row203['position']."')";
             mysqli_query($conn, $sql204);
         }

         /*$sql205 = "SELECT * FROM class_pos_temp_2 where admno = '".$admno."' ";
         $result205 = mysqli_query($conn, $sql205);
         while ($row205 = mysqli_fetch_array($result205)) {
             $posit = $row205['id'];
         }*/

         /*$sql206 = "TRUNCATE TABLE class_pos_temp_2";
         mysqli_query($conn, $sql206);

         $sql40 = "TRUNCATE TABLE class_pos_temp";
         mysqli_query($conn, $sql40);*/
    }

    function displayPosition($conn){
        global $admno;
        $admno;
        $sql205 = "SELECT * FROM class_pos_temp_2 where admno = '".$admno."' ";
        $result205 = mysqli_query($conn, $sql205);
        $row205 = mysqli_fetch_array($result205);
        $position = $row205['id'];
        return $position;
    }

    function clearPosition($conn){
        $sql206 = "TRUNCATE TABLE class_pos_temp_2";
         mysqli_query($conn, $sql206);

        $sql40 = "TRUNCATE TABLE class_pos_temp";
        mysqli_query($conn, $sql40);
    }

    function header(){
        global $avTotal;
        global $conn;
        global $term;
        global $class;
        /*$this->Image('bg.jpg',0,0,300,300);*/
        /*$this->centreImage('bg.jpg',0,0);*/
        $this->SetFont('Arial','B',10);
        $this->cell(200,5,'THE REDEMER'."'".'S ACADEMY CRECHE NURSERY/PRIMARY SCHOOL',0,0,'C');
        $this->Ln(1);
        /*$this->Image('sign/traco.png',10,10,10);*/
        $this->SetFont('Arial','',8);
        /* $this->cell(70,20,$this->Image('http://www.traco.tracoportal.com/user/sign/traco.png',10,10,22),0,0,'C');
        $this->cell(50,20,$this->Image('http://www.traco.tracoportal.com/user/file/profile/'.$_SESSION['sd']['photo'],180,10,24),0,0,'C'); */
        $this->Ln(6);
        $this->cell(190,5,'No_2_Mcc/Uratta_Road,',0,0,'C');
        $this->Ln(4);
        $this->cell(190,5,'Behind Olive Hotels,',0,0,'C');
        $this->Ln(4);
        $this->cell(190,5,'Ebikoro Uratta Owerri,',0,0,'C');
        $this->Ln(4);
        $this->cell(190,5,'IMO STATE',0,0,'C');

        /*$this->cell(70,20,$this->Image('sign/traco.png',10,250,20),0,0,'C');*/
        $this->Ln(15);

        $this->SetFont('Times','B',10);
        $this->cell(12,6,'Name',1,0,'L');
        $this->SetFont('Times','',10);
        $this->cell(70,6,$_SESSION['sd']['surname'].' '.$_SESSION['sd']['lastname'].' '.$_SESSION['sd']['middlename'],1,0,'L');
        $this->SetFont('Times','B',10);
        $this->cell(25,6,'Admission No',1,0,'L');
        $this->SetFont('Times','',10);
        $this->cell(50,6,$_SESSION['sd']['admno'],1,0,'L');
        $this->SetFont('Times','B',10);
        $this->cell(12,6,'Class',1,0,'L');
        $this->SetFont('Times','',10);
        $this->cell(25,6,$class,1,0,'L');

        $this->Ln();

        $this->SetFont('Times','B',10);
        $this->cell(25,6,'Term',1,0,'L');
        $this->SetFont('Times','',10);
        $this->cell(75,6,$term,1,0,'L');
        $this->SetFont('Times','B',10);
        $this->cell(25,6,'Avarage',1,0,'L');
        $this->SetFont('Times','',10);
        $this->cell(69,6,$this->calAv($conn),1,0,'L');
        /*$this->SetFont('Times','B',10);
        $this->cell(25,6,'Position',1,0,'L');
        $this->SetFont('Times','',10);
        $this->getPosition($conn);
        $this->cell(29,6,$this->displayPosition($conn),1,0,'L');
        $this->clearPosition($conn);*/

        $this->Ln(10);


    }

    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        // $this->cell(10,10,$this->Image('http://www.traco.tracoportal.com/user/sign/sign-2.png',55,270,40),0,0,'C');
        /*$this->cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');*/
        /*$this->cell(0,10,'Nice Result Work Harder',0,0,'C');*/
    }

     function headerTable(){
        $this->SetFont('Times','B',8);
        $this->cell(7,5,'S/N',1,0,'C');
        $this->cell(55,5,'Subject',1,0,'C');
        $this->cell(20,5,'1st Test 10%',1,0,'C');
        $this->cell(20,5,'2nd Test 10%',1,0,'C');
        $this->cell(27,5,'Mid Term Test 20%',1,0,'C');
        $this->cell(16,5,'Exam 60%',1,0,'C');
        $this->cell(15,5,'Total 100%',1,0,'C');
        $this->cell(12,5,'Grade',1,0,'C');
        $this->cell(22,5,'Remark',1,0,'C');
        $this->Ln();
    }

    function getGrade($total) {
        if ($total < 40 ) {
            $total_f = 'F';
        }elseif ($total < 49) {
            $total_f = 'P';
        }elseif ($total < 69) {
            $total_f = 'C';
        }elseif ($total <= 100) {
            $total_f = 'A';
        }
        return $total_f;
    }

    function getRemark($total) {
        if ($total < 40 ) {
            $total_f = 'FAIL';
        }elseif ($total < 49) {
            $total_f = 'PASS';
        }elseif ($total < 69) {
            $total_f = 'CREDIT';
        }elseif ($total <= 100) {
            $total_f = 'EXCELLENT';
        }
        return $total_f;
    }  
    

    function getPass($passG){
        if($passG == 'F'){
            global $failSub;
            $failSub++;
        }else{
            global $passSub;
            $passSub++;
        }
    }

    /*function getAv($total){
        global $avTotal;
        $avTotal = $avTotal + $total;
    }

    function displayAv(){
        global $avTotal;
        $var = $avTotal;
        return $var;
    }*/

    function displayPass(){
        global $passSub;
        $var = $passSub;
        return $var;
    }

    function displayFail(){
        global $failSub;
        $var = $failSub;
        return $var;
    }

    function updateCard($conn){
        global $class;
        global $admno;
        global $card;
        global $term;
        $sql = "UPDATE card SET matchid = '$admno', class = '$class', term = '$term' WHERE pin = '$card' ";
        $result = $conn->query($sql);
    }

    function viewTable($conn){
        global $class;
        global $term;
        global $admno;
        $sql = "SELECT * FROM published_result WHERE class = '".$class."' AND term = '".$term."' AND admno = '".$admno."' ";
        $result = $conn->query($sql);
        $cnt=1;
        while ($row = $result->fetch_assoc()) {
            $this->SetFont('Times','',9);
            $total = (float)$row['ca1'] + (float)$row['ca2'] + (float)$row['ca3'] + (float)$row['exam'];
            $this->cell(7,6,$cnt++,1,0,'C');
            $this->cell(55,6,$row['subject'],1,0,'L');
            $this->cell(20,6,(float)$row['ca1'],1,0,'C');
            $this->cell(20,6,(float)$row['ca2'],1,0,'C');
            $this->cell(27,6,(float)$row['ca3'],1,0,'C');
            $this->cell(16,6,(float)$row['exam'],1,0,'C');
            $this->cell(15,6,$total,1,0,'C');
            $this->cell(12,6,$passG = $this->getGrade($total),1,0,'C');
            $this->getPass($passG);
            $this->SetFont('Times','',9);
            $this->cell(22,6,$this->getRemark($total),1,0,'C');
            $this->Ln();
        }
            $cnt = $cnt-1;
            $this->SetFont('Times','',9);
            $this->Ln(5);
            $this->cell(194,4,'RATINGS : 70-100(A), 50-69(C), 45-49(P), 0-44(F)',1,0,'C');
            $this->Ln();

            $this->cell(64,6,'Number of Subjects Offered : '.$cnt,1,0,'L');
            $this->cell(65,6,'Number of Subjects Passed : '.$this->displayPass(),1,0,'L');
            $this->cell(65,6,'Number of Subjects Failed : '.$this->displayFail(),1,0,'L');
            /*$this->cell(100,25,'',1,0,'C');
            $this->cell(94,8,'Number of Subjects Offered : '.$cnt,1,0,'L');
            $this->Ln();
            $this->cell(100,25,'',0,0,'C');
            $this->cell(94,8,'Number of Subjects Passed : '.$this->displayPass(),1,0,'L');
            $this->Ln();
            $this->cell(100,25,'',0,0,'C');
            $this->cell(94,9,'Number of Subjects Failed : '.$this->displayFail(),1,0,'L');
            $this->Ln();*/
            $this->Ln(10);
            $this->SetFont('Times','B',9);
            $this->cell(97,6,'FORM TEACHER REMARK',1,0,'C');
            $this->cell(97,6,'HEAD TEACHER REMARK',1,0,'C');
            $this->Ln();
            $this->SetFont('Times','',8);
            $this->cell(97,6,$this->formTeacherRemark($conn),1,0,'C');
            $this->cell(97,6,$this->principalRemark($conn),1,0,'C');

/*            $this->Ln(10);
            $this->SetFont('Times','B',9);
            $this->cell(64,6,'Bank Name',1,0,'C');
            $this->cell(65,6,'Account Name',1,0,'C');
            $this->cell(65,6,'Account Number',1,0,'C');
            $this->Ln();
            $this->SetFont('Times','',8);
            $this->cell(64,6,'FCMB',1,0,'C');
            $this->cell(65,6,'Sisters of Jesus Crucified',1,0,'C');
            $this->cell(65,6,'4568319012',1,0,'C');*/

/*            $this->Ln(10);
            $this->SetFont('Times','B',9);
            $this->cell(48,6,'Tuition Fee (#)',1,0,'C');
            $this->cell(48,6,'Lab Fee (#)',1,0,'C');
            $this->cell(49,6,'Portal Charge (#)',1,0,'C');
            $this->cell(49,6,'Total (#)',1,0,'C');
            $this->Ln();
            $this->SetFont('Times','',8);
            $this->cell(48,6,$tuit = 14500,1,0,'C');
            $this->cell(48,6,5000,1,0,'C');
            $this->cell(49,6,500,1,0,'C');
            $this->cell(49,6,$tuit + 5000 + 500,1,0,'C');*/
    }

}
// usage:
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4',0);
// $pdf->Image('http://www.traco.tracoportal.com/user/sign/traco_bg.png',10,10,189);
$pdf->headerTable();
$pdf->viewTable($conn);
$pdf->updateCard($conn);
$pdf->Output();
?>