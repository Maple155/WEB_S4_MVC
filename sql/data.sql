-- 1) Établissement financier
INSERT INTO etablissement_financiere (id_etablissement, nom, mdp) VALUES 
(1, 'MicroFinance ITU', 'admin123');

-- 2) Mouvements d’argent de l’établissement
INSERT INTO mouvement_argent (id_mouvement, montant, date_, id_etablissement) VALUES
(1, 10000000, '2025-07-01', 1);

-- 3) Types de prêt
INSERT INTO type_pret (id_type_pret, nom, taux_interet, duree_max_mois, montant_min, montant_max, age_min, id_etablissement) VALUES
(1, 'Prêt Consommation', 8, 24, 100000, 3000000, 18, 1),
(2, 'Prêt Immobilier', 5, 240, 1000000, 5000000, 21, 1),
(3, 'Prêt Auto', 6, 60, 500000, 4000000, 20, 1);

-- 4) Clients
INSERT INTO client (id_client, nom, mdp, prenom, date_de_naissance, revenu_mensuel) VALUES
(1, 'rakoto', 'pass123', 'Jean', '1999-04-15', 800000),
(2, 'randria', 'pass456', 'Hery', '2001-09-20', 600000);