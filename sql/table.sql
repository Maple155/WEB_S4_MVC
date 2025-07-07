CREATE TABLE etablissement_financiere (
   id_etablissement INT,
   nom VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_etablissement)
);

CREATE TABLE type_pret (
   id_type_pret INT,
   nom VARCHAR(50) NOT NULL,
   taux_interet INT NOT NULL,
   duree_max_mois INT NOT NULL,
   montant_min DECIMAL(15,0),
   montant_max DECIMAL(15,0) NOT NULL,
   age_min INT NOT NULL,
   id_etablissement INT NOT NULL,
   PRIMARY KEY(id_type_pret),
   FOREIGN KEY(id_etablissement) REFERENCES etablissement_financiere(id_etablissement)
);

CREATE TABLE mouvement_argent (
   id_mouvement INT,
   montant DECIMAL(15,0) NOT NULL,
   date_ DATE NOT NULL,
   id_etablissement INT NOT NULL,
   PRIMARY KEY(id_mouvement),
   FOREIGN KEY(id_etablissement) REFERENCES etablissement_financiere(id_etablissement)
);

CREATE TABLE client (
   id_client INT,
   nom VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   date_de_naissance DATE NOT NULL,
   revenu_mensuel DECIMAL(15,0) NOT NULL,
   PRIMARY KEY(id_client)
);

CREATE TABLE mouvement_argent_client (
   id_mouvement_client INT,
   montant DECIMAL(15,0) NOT NULL,
   date_ DATE NOT NULL,
   id_client INT NOT NULL,
   PRIMARY KEY(id_mouvement_client),
   FOREIGN KEY(id_client) REFERENCES client(id_client)
);

CREATE TABLE pret (
   id_pret INT,
   montant DECIMAL(15,0) NOT NULL,
   date_debut DATE NOT NULL,
   duree_mois INT NOT NULL,
   id_type_pret INT NOT NULL,
   id_client INT NOT NULL,
   PRIMARY KEY(id_pret),
   FOREIGN KEY(id_type_pret) REFERENCES type_pret(id_type_pret),
   FOREIGN KEY(id_client) REFERENCES client(id_client)
);

CREATE TABLE type_statut_pret (
   id_statut INT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_statut)
);

CREATE TABLE historique_statut_pret (
   id_historique INT,
   id_statut INT NOT NULL,
   id_pret INT NOT NULL,
   PRIMARY KEY(id_historique),
   FOREIGN KEY(id_statut) REFERENCES type_statut_pret(id_statut),
   FOREIGN KEY(id_pret) REFERENCES pret(id_pret)
);

CREATE TABLE statut_mensualite (
   id_statut_mensualite INT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_statut_mensualite)
);

CREATE TABLE mensualite (
   id_mensualite INT,
   montant DECIMAL(15,0) NOT NULL,
   date_echeance DATE NOT NULL,
   date_paiement VARCHAR(50) NOT NULL,
   id_statut_mensualite INT NOT NULL,
   id_pret INT NOT NULL,
   PRIMARY KEY(id_mensualite),
   FOREIGN KEY(id_statut_mensualite) REFERENCES statut_mensualite(id_statut_mensualite),
   FOREIGN KEY(id_pret) REFERENCES pret(id_pret)
);