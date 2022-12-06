<?php
// script for sending weekly report
// automated using Windows Task Scheduler

include_once __DIR__.'/../includes/db_connect.php';
include_once __DIR__.'/../includes/utils.php';
include_once __DIR__.'/../includes/fpdf182/fpdf.php';

//using FPDF to generate the PDF
class PDF extends FPDF {
    // Page header
    function Header() {
        // Logo
        $this->Image(__DIR__.'/../public/img/report_background.png', 0, 0, 210);
        $this->Image(__DIR__.'/../public/img/logo.png', 10, 5, 70);
    }
    
    // Page footer
    function Footer() {
        // Position at 2.0 cm from bottom
        $this->SetY(-20);
        // Times italic 10
        $this->SetFont('Times', 'I', 10);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
        
    }
}

$lastSat = date('Y-m-d',strtotime('-1 day'));
$lastSun = date('Y-m-d',strtotime('-7 days'));
$pdfDir = __DIR__.'/../public/pdf';

if (!is_dir($pdfDir)) {
    mkdir($pdfDir);
}

//retrieve the information of all users who want to receive weekly report
$sql = "SELECT * FROM user WHERE receive_weekly_report = TRUE";    
$users = $mysqli->query($sql);

while ($user = $users->fetch_assoc()) {
    $username = $user['username'];

    $pdf = new PDF();
    
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Times','B', 13);

    // Move to the right
    $pdf->Cell(80,38,'',0,1,'C');

    // Title
    $pdf->Cell(109,10,"User: $username", 0, 1, 'L');
    $pdf->Cell(109,10,"Week: $lastSun to $lastSat", 0, 0, 'L');

    // Line break
    $pdf->Ln(20);
    
    $pdf->SetFont('Times','B',12);
    $pdf->Cell(94, 6, 'Goal name', 1, 0);
    $pdf->Cell(55, 6, "Streaks as of ".$lastSat, 1, 1);
    // the first number add up to 189 = a line
    
    // get the goals that were on progress in the previous week
    $sql = "SELECT * FROM goal WHERE username = ? AND goal_end_date >= ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $username, $lastSun);
    $stmt->execute();
    $goals = $stmt->get_result();
    
    //display the goal details
    while ($goal = $goals->fetch_assoc()) {
        $pdf->Cell(94,10,$goal['goal_name'], 1, 0);
        $pdf->Cell(55,10,$goal['streak_last_week'], 1, 1);
    }
    
    $pdfFile = __DIR__.'/../public/pdf/weekly_report.pdf';
    $file = $pdf->Output($pdfFile, 'F');

    $message = '<img src="cid:logo" width="200">';
    $message .= "<p>Hello {$user['username']},</p>";
    $message .= '<p>Your weekly report is out! Check out your progress so far this week in <a href="localhost/user/index.php">Habitracker</a>!</p>';
    $message .= '<p>The chains of habit are too weak to be felt until they are too strong to be broken. ―Samuel Johnson</p>';
    $message .= '<p>Keep up with your goals and track your habits today!</p>';
    $message .= '<p>Want to set yourself a new challenge this week?</p>';
    $message .= '<p>Do not hesitate and create your new habit in ';
    $message .= '<a href="http://localhost/user/goal/create_goal.php">Habitracker</a>!</p>';
    $message .= '<p>Please send an email to habitracker.noreply@gmail.com if you have any queries.</p>';
    
    sendEmail("[Habitracker] Weekly Report", $message, $user['email'], __DIR__.'/../public/img/logo.png', $pdfFile);
    
    unlink($pdfFile); // delete weely report file
}
?>
