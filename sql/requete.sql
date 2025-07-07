SELECT 
    YEAR(p.date_debut) AS annee,
    MONTH(p.date_debut) AS mois,
    DATE_FORMAT(p.date_debut, '%Y-%m') AS periode,
    COUNT(p.id_pret) AS nombre_prets,
    SUM(p.montant) AS capital_total,
    ROUND(SUM(p.montant * (tp.taux_interet/100) / 12), 2) AS interets_mensuels
FROM 
    pret p
JOIN 
    type_pret tp ON p.id_type_pret = tp.id_type_pret
WHERE 
    p.date_debut BETWEEN :date_debut AND :date_fin
GROUP BY 
    YEAR(p.date_debut), MONTH(p.date_debut)
ORDER BY 
    annee, mois;



SELECT 
    YEAR(p.date_debut) AS annee,
    MONTH(p.date_debut) AS mois,
    DATE_FORMAT(p.date_debut, '%Y-%m') AS periode,
    COUNT(p.id_pret) AS nombre_prets,
    SUM(p.montant) AS capital_total,
    ROUND(SUM(p.montant * (tp.taux_interet/100) / 12), 2) AS interets_mensuels
FROM pret p
JOIN type_pret tp ON p.id_type_pret = tp.id_type_pret
WHERE p.date_debut BETWEEN '2024-01-01' AND '2025-12-31'
GROUP BY YEAR(p.date_debut), MONTH(p.date_debut)
ORDER BY annee, mois;
