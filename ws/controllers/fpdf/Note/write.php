<?php
include('function.php');

    $notes = findAllNote();

    $fp = fopen('note.csv', "a");

    $data_separator = ";";
    foreach($notes as $note){
        fputs($fp, $note['idNote'].$data_separator);
        fputs($fp, $note['UE'].$data_separator);
        fputs($fp, $note['Intitule'].$data_separator);
        fputs($fp, $note['credits'].$data_separator);
        fputs($fp, $note['Note'].$data_separator);
        fputs($fp, $note['Resultat'].$data_separator);
        fputs($fp, $note['Semestre']."\n");

    }

    fclose($fp);

    header('Location: index.php');
?>