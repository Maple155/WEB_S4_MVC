<?php
require_once __DIR__ . '/../models/Demande.php';
require_once __DIR__ . '/../helpers/Utils.php';
require 'fpdf/fpdf.php';

class DemandeController
{

    public static function getAllPrets()
    {
        $prets = Demande::getAllPrets();
        Flight::json($prets);
    }

    public static function getAllTypePrets()
    {
        $typePrets = Demande::getAllTypePrets();
        Flight::json($typePrets);
    }

    public static function getTypePretById($id)
    {
        $typePret = Demande::getTypePretById($id);
        Flight::json($typePret);
    }

    public static function createPret()
    {
        $data = Flight::request()->data;
        $message = Demande::createPret($data);
        Flight::json(['message' => $message['message']]);
    }

    public static function getCurrentClient()
    {
        $client = Demande::getCurrentClient();
        Flight::json($client);
    }

    public static function getAllClient()
    {
        $clients = Demande::getAllClient();
        Flight::json($clients);
    }

    public static function generatePDF($pret_id)
    {
        $db = getDB();
        $pret = Demande::findByIdPret($pret_id);
        $client = Demande::getCurrentClient($pret['id_client']);
        $typepret = Demande::getTypePretById($pret['id_type_pret']);

        if (!$pret || !$client || !$typepret) {
            Flight::halt(404, "Pret ou client ou type pret non trouve.");
        }

        // Calcul
        $montant = $pret['montant'];
        $taux = $typepret['taux_interet'];
        $mois = $pret['duree_mois'];
        $assurance = $pret['assurance'];
        $delai = $pret['delai_mois'] ?? 0;

        $mensualite = Demande::calculAnnuite($montant, $taux, $mois);
        $tableau = Demande::tableauAmortissement($montant, $taux, $mois, $assurance);

        // Calculs des totaux
        $total_interets = array_sum(array_column($tableau, 'interet'));
        $total_assurance = array_sum(array_column($tableau, 'assurance'));
        $total_general = $montant + $total_interets + $total_assurance;

        // GEnEration PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 12, 'CONTRAT DE PRET - ANNUITE CONSTANTE', 0, 1, 'C');
        $pdf->Ln(5);

        // Infos client
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'INFORMATIONS CLIENT', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, "Nom : " . $client['nom'], 0, 1);
        $pdf->Cell(0, 6, "Prenom : " . $client['prenom'], 0, 1);
        $pdf->Cell(0, 6, "Revenu mensuel : " . number_format($client['revenu_mensuel'], 0, '', ' ') . " Ar", 0, 1);
        $pdf->Cell(0, 6, "Date de demande : " . date('d/m/Y', strtotime($pret['date_debut'])), 0, 1);
        $pdf->Ln(8);

        // Infos prêt
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'DETAILS DU PRET', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, "Type de pret : " . $typepret['nom'], 0, 1);
        $pdf->Cell(0, 6, "Montant emprunte : " . number_format($montant, 0, '', ' ') . " Ar", 0, 1);
        $pdf->Cell(0, 6, "Taux d'interet : " . $taux . " % par an", 0, 1);
        $pdf->Cell(0, 6, "Duree : " . $mois . " mois", 0, 1);
        $pdf->Cell(0, 6, "Taux d'assurance : " . $assurance . " % par an", 0, 1);
        $pdf->Cell(0, 6, "Delai avant premier remboursement : " . $delai . " mois", 0, 1);
        $pdf->Ln(3);

        // Resume financier
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, "RESUME FINANCIER :", 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, "  Mensualite fixe (capital + interets) : " . number_format($mensualite, 0, '', ' ') . " Ar", 0, 1);
        $pdf->Cell(0, 5, "  Total des interets : " . number_format($total_interets, 0, '', ' ') . " Ar", 0, 1);
        $pdf->Cell(0, 5, "  Total de l'assurance : " . number_format($total_assurance, 0, '', ' ') . " Ar", 0, 1);
        $pdf->Cell(0, 5, "  Montant total a rembourser : " . number_format($total_general, 0, '', ' ') . " Ar", 0, 1);
        $pdf->Ln(8);

        // Tableau d'amortissement
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, "TABLEAU D'AMORTISSEMENT", 0, 1);
        $pdf->Ln(2);

        // En-têtes du tableau
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(12, 7, "Mois", 1, 0, 'C');
        $pdf->Cell(28, 7, "Interets", 1, 0, 'C');
        $pdf->Cell(28, 7, "Principal", 1, 0, 'C');
        $pdf->Cell(28, 7, "Mensualite", 1, 0, 'C');
        $pdf->Cell(28, 7, "Assurance", 1, 0, 'C');
        $pdf->Cell(28, 7, "Total", 1, 0, 'C');
        $pdf->Cell(28, 7, "Reste du", 1, 0, 'C');
        $pdf->Ln();

        // DonnEes du tableau
        $pdf->SetFont('Arial', '', 7);
        foreach ($tableau as $row) {
            $pdf->Cell(12, 5, $row['mois'], 1, 0, 'C');
            $pdf->Cell(28, 5, number_format($row['interet'], 0, '', ' '), 1, 0, 'R');
            $pdf->Cell(28, 5, number_format($row['principal'], 0, '', ' '), 1, 0, 'R');
            $pdf->Cell(28, 5, number_format($mensualite, 0, '', ' '), 1, 0, 'R');
            $pdf->Cell(28, 5, number_format($row['assurance'], 0, '', ' '), 1, 0, 'R');
            $pdf->Cell(28, 5, number_format($row['total'], 0, '', ' '), 1, 0, 'R');
            $pdf->Cell(28, 5, number_format($row['reste'], 0, '', ' '), 1, 0, 'R');
            $pdf->Ln();
        }

        // Ligne de totaux
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(12, 6, "TOTAL", 1, 0, 'C');
        $pdf->Cell(28, 6, number_format($total_interets, 0, '', ' '), 1, 0, 'R');
        $pdf->Cell(28, 6, number_format($montant, 0, '', ' '), 1, 0, 'R');
        $pdf->Cell(28, 6, number_format($mensualite * $mois, 0, '', ' '), 1, 0, 'R');
        $pdf->Cell(28, 6, number_format($total_assurance, 0, '', ' '), 1, 0, 'R');
        $pdf->Cell(28, 6, number_format($total_general, 0, '', ' '), 1, 0, 'R');
        $pdf->Cell(28, 6, "0", 1, 0, 'R');
        $pdf->Ln(10);

        // GEnEration du fichier
        $filename = 'Contrat_Pret_' . $client['nom'] . '_' . $pret['id_pret'] . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $pdf->Output('I', $filename);
    }

}
