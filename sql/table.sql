CREATE TABLE etablissement_financiere (
   id_etablissement INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_etablissement)
);

CREATE TABLE type_pret (
   id_type_pret INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   taux_interet INT NOT NULL,
   duree_max_mois INT NOT NULL,
   montant_min DECIMAL(15,0),
   montant_max DECIMAL(15,0) NOT NULL,
   age_min INT NOT NULL,
   PRIMARY KEY(id_type_pret)
);

CREATE TABLE mouvement_argent (
   id_mouvement INT AUTO_INCREMENT,
   montant DECIMAL(15,0) NOT NULL,
   date_ DATE NOT NULL,
   PRIMARY KEY(id_mouvement)
);

CREATE TABLE client (
   id_client INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   date_de_naissance DATE NOT NULL,
   revenu_mensuel DECIMAL(15,0) NOT NULL,
   PRIMARY KEY(id_client)
);

CREATE TABLE pret (
   id_pret INT AUTO_INCREMENT,
   montant DECIMAL(15,0) NOT NULL,
   date_debut DATE NOT NULL,
   duree_mois INT NOT NULL,
   id_type_pret INT NOT NULL,
   id_client INT NOT NULL,
   PRIMARY KEY(id_pret),
   FOREIGN KEY(id_type_pret) REFERENCES type_pret(id_type_pret),
   FOREIGN KEY(id_client) REFERENCES client(id_client)
);

CREATE TABLE mensualite (
   id_mensualite INT AUTO_INCREMENT,
   montant DECIMAL(15,0) NOT NULL,
   mois INT,
   annee INT,
   id_pret INT NOT NULL,
   PRIMARY KEY(id_mensualite),
   FOREIGN KEY(id_pret) REFERENCES pret(id_pret)
);
