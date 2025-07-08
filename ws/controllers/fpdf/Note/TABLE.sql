
-- Postgres
CREATE TABLE NOTE(
    idNote serial primary key,
    UE varchar(7),
    Intitule varchar(50),
    credits int,
    Note NUMERIC(10,2),
    Resultat varchar(2),
    Semestre int
);

-- MySQL
CREATE TABLE NOTE(
    idNote integer primary key auto_increment,
    UE varchar(7),
    Intitule varchar(50),
    credits integer,
    Note NUMERIC(10,2),
    Resultat varchar(2),
    Semestre integer
);

INSERT INTO NOTE (UE, Intitule, credits, Note, Resultat, Semestre) VALUES
('INF201', 'Programmation Orientée Objet', 6, 14.5, 'A', 2),
('INF202', 'Bases de Données Avancées', 5, 12.0, 'B', 2),
('INF203', 'Développement Web', 4, 16.0, 'A', 2),
('MTH201', 'Algèbre Linéaire', 5, 10.5, 'C', 2),
('MTH202', 'Statistiques et Probabilités', 4, 13.0, 'B', 2),
('ORG201', 'Gestion de Projet', 3, 15.0, 'A', 2),
('PHY201', 'Physique Appliquée', 3, 9.5, 'C', 2);
