<?php 
require('../fpdf.php');

class PDF extends FPDF{
    function LoadData($file){
        $lines = file($file);
        $data = array();
        foreach($lines as $line){
            $row = explode(';', trim($line));
            $semestre = $row[count($row) - 1]; 
            $data[$semestre][] = $row; 
        }
        return $data;
    }

    function Header() {
        // Logo
        $this->Image('logo.jpg', 10, 10, 40);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(190, 10, utf8_decode('RELEVÉ DE NOTES ET RÉSULTATS'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Fait à Antananarivo, le ') . date('d/m/Y'), 0, 0, 'C');
    }

    function ImprovedTable($header, $data){
        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 10, utf8_decode('Nom : ROBINSON'));
        $this->Ln();
        $this->Cell(60, 10, utf8_decode('Prénom(s) : Solomampionona Randy'));
        $this->Ln();
        $this->Cell(80, 10, utf8_decode('Né le : 10/09/2005 à Ankadifotsy'));
        $this->Ln();
        $this->Cell(60, 10, utf8_decode("N° d'inscription : ETU003227"));
        $this->Ln();
        $this->Cell(60, 10, utf8_decode('Inscrit en : M1-Informatique'));
        $this->Ln(10);
    
        $w = array(40, 20, 70, 20, 20, 20, 20);
    
        $this->SetFont('Arial', 'B', 10);
        for($i = 1; $i < count($header) - 1; $i++) {
            $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();
    
        $this->SetFont('Arial', '', 10);
    
        $totalCredits = 0;
        $totalNote = 0;
        $semestres = ['1', '2'];
    
        foreach ($semestres as $semestre) {
            if (!isset($data[$semestre])) continue;
    
            $creditsSemestre = 0;
            $noteSemestre = 0;
    
            foreach ($data[$semestre] as $row) {
                for($i = 1; $i < count($w) - 1; $i++) {
                    $this->Cell($w[$i], 6, utf8_decode($row[$i]), 1, 0, 'C');
                }
                $this->Ln();
    
                if (is_numeric($row[3]) && is_numeric($row[4])) {
                    $creditsSemestre += $row[3];
                    $noteSemestre += $row[3] * $row[4];
                }
            }
    
            $moyenneSemestre = $creditsSemestre > 0 ? $noteSemestre / $creditsSemestre : 0;
            $totalCredits += $creditsSemestre;
            $totalNote += $noteSemestre;
    
            // Affichage de la moyenne par semestre
            $this->SetFont('Arial', 'B', 10);
            $this->Cell($w[0], 6, '', 0, 0);
            $this->Cell($w[1], 6, utf8_decode("SEMESTRE $semestre"), 1, 0 , 'C');
            $this->Cell($w[2], 6, $creditsSemestre, 1, 0, 'C'); 
            $this->Cell($w[3], 6, number_format($moyenneSemestre, 2), 1, 0, 'C'); 
            $this->Ln(10);
            $this->SetFont('Arial', '', 10);
        }
    
        $moyenneGenerale = $totalCredits > 0 ? $totalNote / $totalCredits : 0;
    
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 6, utf8_decode("Résultat Général :"), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 6, utf8_decode("Crédits : ").$totalCredits, 0, 1, 'L');
        $this->Cell(50, 6, utf8_decode("Moyenne Générale : ").number_format($moyenneGenerale, 2), 0, 1, 'L');
    
        $mention = "Passable";
        if($moyenneGenerale < 10){
            $mention = "Ajourné";
        } else if($moyenneGenerale >= 12 && $moyenneGenerale < 14){
            $mention = "Assez bien";
        } else if($moyenneGenerale >= 14 && $moyenneGenerale < 16){
            $mention = "Bien";
        } else if($moyenneGenerale >= 16){
            $mention = "Très bien";
        }
    
        $this->Cell(50, 6, utf8_decode("Mention : ").utf8_decode($mention), 0, 1, 'L');
        $this->Ln();
    }
}

$pdf = new PDF();
$header = array('idNote', 'UE', 'Intitule', 'credits', 'Note/20', 'Resultat', 'Semestre');
$data = $pdf->LoadData('note.csv');
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$pdf->ImprovedTable($header, $data);
$pdf->Output();
?>
