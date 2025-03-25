<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325164130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auxiliar (id INT AUTO_INCREMENT NOT NULL, num_trabajador VARCHAR(10) DEFAULT NULL, nombre VARCHAR(50) DEFAULT NULL, apellidos VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE constantes_vitales (id INT AUTO_INCREMENT NOT NULL, ta_sistolica NUMERIC(3, 1) DEFAULT NULL, ta_diastolica NUMERIC(3, 1) DEFAULT NULL, frecuencia_respiratoria NUMERIC(3, 1) DEFAULT NULL, pulso NUMERIC(3, 1) DEFAULT NULL, temperatura NUMERIC(3, 1) DEFAULT NULL, saturacion_oxigeno NUMERIC(3, 1) DEFAULT NULL, peso NUMERIC(3, 1) DEFAULT NULL, talla NUMERIC(3, 1) DEFAULT NULL, diuresis NUMERIC(3, 1) DEFAULT NULL, deposiciones VARCHAR(45) DEFAULT NULL, stp NUMERIC(3, 1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detalle_diagnostico (id INT AUTO_INCREMENT NOT NULL, o2 INT DEFAULT NULL, o2_descripcion LONGTEXT DEFAULT NULL, panales INT DEFAULT NULL, descripcion LONGTEXT DEFAULT NULL, sv INT DEFAULT NULL, sv_tipo LONGTEXT DEFAULT NULL, sv_debito LONGTEXT DEFAULT NULL, sr INT DEFAULT NULL, sr_debito LONGTEXT DEFAULT NULL, sng VARCHAR(45) DEFAULT NULL, sng_descripcion LONGTEXT DEFAULT NULL, diagnostico_id_id INT DEFAULT NULL, INDEX IDX_D78E393B9EB8F283 (diagnostico_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE diagnostico (id INT AUTO_INCREMENT NOT NULL, diagnostico LONGTEXT DEFAULT NULL, motivo LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dieta (id INT AUTO_INCREMENT NOT NULL, dia INT DEFAULT NULL, toma VARCHAR(1) DEFAULT NULL, autonomo INT DEFAULT NULL, protesi INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dieta_has_tipo_dieta (id INT AUTO_INCREMENT NOT NULL, dieta_id_id INT DEFAULT NULL, tipo_dieta_id_id INT DEFAULT NULL, INDEX IDX_60479CE8F1E9B454 (dieta_id_id), INDEX IDX_60479CE8C4CD9AAC (tipo_dieta_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE drenaje (id INT AUTO_INCREMENT NOT NULL, debito LONGTEXT DEFAULT NULL, tipo_drenaje_id_id INT DEFAULT NULL, INDEX IDX_C58BD4A270522D78 (tipo_drenaje_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE habitacion (id INT AUTO_INCREMENT NOT NULL, observaciones VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE movilizacion (id INT AUTO_INCREMENT NOT NULL, sedestacion INT DEFAULT NULL, ayuda_deambulacion INT DEFAULT NULL, ayuda_descripcion VARCHAR(255) DEFAULT NULL, cambios VARCHAR(255) DEFAULT NULL, decubitos VARCHAR(45) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE observacion (id INT AUTO_INCREMENT NOT NULL, descripcion LONGTEXT DEFAULT NULL, fecha DATETIME DEFAULT NULL, paciente_id_id INT DEFAULT NULL, INDEX IDX_8B8B4C68AA1655E (paciente_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE paciente (id INT AUTO_INCREMENT NOT NULL, num_historial INT DEFAULT NULL, nombre VARCHAR(50) DEFAULT NULL, apellidos VARCHAR(150) DEFAULT NULL, fecha_nacimiento DATE DEFAULT NULL, direccion_completa VARCHAR(255) DEFAULT NULL, lengua_materna VARCHAR(45) DEFAULT NULL, antecedentes LONGTEXT DEFAULT NULL, alergias LONGTEXT DEFAULT NULL, nombre_cuidador VARCHAR(150) DEFAULT NULL, telefono_cuidador VARCHAR(9) DEFAULT NULL, fecha_ingreso DATETIME DEFAULT NULL, timestamp DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE paciente_has_habitaciones (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME DEFAULT NULL, paciente_id_id INT DEFAULT NULL, habitacion_id_id INT DEFAULT NULL, INDEX IDX_C4D69A798AA1655E (paciente_id_id), INDEX IDX_C4D69A795303719C (habitacion_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE registro (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME DEFAULT NULL, observaciones LONGTEXT DEFAULT NULL, auxiliar_id_id INT DEFAULT NULL, paciente_id_id INT DEFAULT NULL, tipo_higiene_id_id INT DEFAULT NULL, dieta_id_id INT DEFAULT NULL, drenaje_id_id INT DEFAULT NULL, movilizacion_id_id INT DEFAULT NULL, diagnostico_id_id INT DEFAULT NULL, constantes_vitales_id_id INT DEFAULT NULL, INDEX IDX_397CA85B767EE6F6 (auxiliar_id_id), INDEX IDX_397CA85B8AA1655E (paciente_id_id), INDEX IDX_397CA85BF474203F (tipo_higiene_id_id), INDEX IDX_397CA85BF1E9B454 (dieta_id_id), INDEX IDX_397CA85B70E17E26 (drenaje_id_id), INDEX IDX_397CA85B522214C8 (movilizacion_id_id), INDEX IDX_397CA85B9EB8F283 (diagnostico_id_id), INDEX IDX_397CA85B1DA1E444 (constantes_vitales_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_dieta (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_drenaje (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_higiene (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_textura (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE detalle_diagnostico ADD CONSTRAINT FK_D78E393B9EB8F283 FOREIGN KEY (diagnostico_id_id) REFERENCES diagnostico (id)');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta ADD CONSTRAINT FK_60479CE8F1E9B454 FOREIGN KEY (dieta_id_id) REFERENCES dieta (id)');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta ADD CONSTRAINT FK_60479CE8C4CD9AAC FOREIGN KEY (tipo_dieta_id_id) REFERENCES tipo_dieta (id)');
        $this->addSql('ALTER TABLE drenaje ADD CONSTRAINT FK_C58BD4A270522D78 FOREIGN KEY (tipo_drenaje_id_id) REFERENCES tipo_drenaje (id)');
        $this->addSql('ALTER TABLE observacion ADD CONSTRAINT FK_8B8B4C68AA1655E FOREIGN KEY (paciente_id_id) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE paciente_has_habitaciones ADD CONSTRAINT FK_C4D69A798AA1655E FOREIGN KEY (paciente_id_id) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE paciente_has_habitaciones ADD CONSTRAINT FK_C4D69A795303719C FOREIGN KEY (habitacion_id_id) REFERENCES habitacion (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B767EE6F6 FOREIGN KEY (auxiliar_id_id) REFERENCES auxiliar (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B8AA1655E FOREIGN KEY (paciente_id_id) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BF474203F FOREIGN KEY (tipo_higiene_id_id) REFERENCES tipo_higiene (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BF1E9B454 FOREIGN KEY (dieta_id_id) REFERENCES dieta (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B70E17E26 FOREIGN KEY (drenaje_id_id) REFERENCES drenaje (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B522214C8 FOREIGN KEY (movilizacion_id_id) REFERENCES movilizacion (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B9EB8F283 FOREIGN KEY (diagnostico_id_id) REFERENCES diagnostico (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B1DA1E444 FOREIGN KEY (constantes_vitales_id_id) REFERENCES constantes_vitales (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_diagnostico DROP FOREIGN KEY FK_D78E393B9EB8F283');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta DROP FOREIGN KEY FK_60479CE8F1E9B454');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta DROP FOREIGN KEY FK_60479CE8C4CD9AAC');
        $this->addSql('ALTER TABLE drenaje DROP FOREIGN KEY FK_C58BD4A270522D78');
        $this->addSql('ALTER TABLE observacion DROP FOREIGN KEY FK_8B8B4C68AA1655E');
        $this->addSql('ALTER TABLE paciente_has_habitaciones DROP FOREIGN KEY FK_C4D69A798AA1655E');
        $this->addSql('ALTER TABLE paciente_has_habitaciones DROP FOREIGN KEY FK_C4D69A795303719C');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B767EE6F6');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B8AA1655E');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BF474203F');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BF1E9B454');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B70E17E26');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B522214C8');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B9EB8F283');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B1DA1E444');
        $this->addSql('DROP TABLE auxiliar');
        $this->addSql('DROP TABLE constantes_vitales');
        $this->addSql('DROP TABLE detalle_diagnostico');
        $this->addSql('DROP TABLE diagnostico');
        $this->addSql('DROP TABLE dieta');
        $this->addSql('DROP TABLE dieta_has_tipo_dieta');
        $this->addSql('DROP TABLE drenaje');
        $this->addSql('DROP TABLE habitacion');
        $this->addSql('DROP TABLE movilizacion');
        $this->addSql('DROP TABLE observacion');
        $this->addSql('DROP TABLE paciente');
        $this->addSql('DROP TABLE paciente_has_habitaciones');
        $this->addSql('DROP TABLE registro');
        $this->addSql('DROP TABLE tipo_dieta');
        $this->addSql('DROP TABLE tipo_drenaje');
        $this->addSql('DROP TABLE tipo_higiene');
        $this->addSql('DROP TABLE tipo_textura');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
