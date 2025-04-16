<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415113744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auxiliar (id INT AUTO_INCREMENT NOT NULL, num_trabajador VARCHAR(10) DEFAULT NULL, nombre VARCHAR(50) DEFAULT NULL, apellidos VARCHAR(150) DEFAULT NULL, contraseña VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE balance_hidrico (id INT AUTO_INCREMENT NOT NULL, diuresis INT DEFAULT NULL, deposicion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE constantes_vitales (id INT AUTO_INCREMENT NOT NULL, ta_sistolica VARCHAR(7) DEFAULT NULL, ta_diastolica VARCHAR(7) DEFAULT NULL, frecuencia_respiratoria NUMERIC(3, 1) DEFAULT NULL, pulso NUMERIC(3, 1) DEFAULT NULL, temperatura NUMERIC(3, 1) DEFAULT NULL, saturacion_oxigeno NUMERIC(3, 1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detalle_diagnostico (id INT AUTO_INCREMENT NOT NULL, o2 INT DEFAULT NULL, o2_descripcion LONGTEXT DEFAULT NULL, panales INT DEFAULT NULL, panales_descripcion LONGTEXT DEFAULT NULL, sv LONGTEXT DEFAULT NULL, sr LONGTEXT DEFAULT NULL, sng LONGTEXT DEFAULT NULL, avd VARCHAR(255) DEFAULT NULL, diagnostico_id_id INT DEFAULT NULL, INDEX IDX_D78E393B9EB8F283 (diagnostico_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE diagnostico (id INT AUTO_INCREMENT NOT NULL, diagnostico LONGTEXT DEFAULT NULL, motivo LONGTEXT DEFAULT NULL, fecha DATETIME DEFAULT NULL, toma VARCHAR(1) DEFAULT NULL, paciente_id_id INT DEFAULT NULL, auxiliar_id_id INT DEFAULT NULL, INDEX IDX_9B91D4488AA1655E (paciente_id_id), INDEX IDX_9B91D448767EE6F6 (auxiliar_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dieta (id INT AUTO_INCREMENT NOT NULL, autonomo INT DEFAULT NULL, protesi INT DEFAULT NULL, tipo_textura_id_id INT DEFAULT NULL, INDEX IDX_D3447AEE43060ACC (tipo_textura_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE dieta_has_tipo_dieta (id INT AUTO_INCREMENT NOT NULL, dieta_id_id INT DEFAULT NULL, tipo_dieta_id_id INT DEFAULT NULL, INDEX IDX_60479CE8F1E9B454 (dieta_id_id), INDEX IDX_60479CE8C4CD9AAC (tipo_dieta_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE drenaje (id INT AUTO_INCREMENT NOT NULL, descripcion LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE habitacion (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(5) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE higiene (id INT AUTO_INCREMENT NOT NULL, descripcion LONGTEXT, tipo_id INT DEFAULT NULL, INDEX IDX_EF484A8FA9276E6C (tipo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE movilizacion (id INT AUTO_INCREMENT NOT NULL, sedestacion LONGTEXT, ayuda_deambulacion INT DEFAULT NULL, ayuda_descripcion LONGTEXT, cambios_posturales LONGTEXT, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE observacion (id INT AUTO_INCREMENT NOT NULL, descripcion LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE paciente (id INT AUTO_INCREMENT NOT NULL, num_historial INT DEFAULT NULL, nombre VARCHAR(50) DEFAULT NULL, apellidos VARCHAR(150) DEFAULT NULL, fecha_nacimiento DATE DEFAULT NULL, direccion_completa VARCHAR(255) DEFAULT NULL, lengua_materna VARCHAR(45) DEFAULT NULL, antecedentes LONGTEXT DEFAULT NULL, alergias LONGTEXT DEFAULT NULL, nombre_cuidador VARCHAR(150) DEFAULT NULL, telefono_cuidador VARCHAR(9) DEFAULT NULL, fecha_ingreso DATETIME DEFAULT NULL, timestamp DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE paciente_has_habitaciones (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME DEFAULT NULL, paciente_id_id INT DEFAULT NULL, habitacion_id_id INT DEFAULT NULL, INDEX IDX_C4D69A798AA1655E (paciente_id_id), INDEX IDX_C4D69A795303719C (habitacion_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE registro (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME DEFAULT NULL, toma VARCHAR(1) DEFAULT NULL, auxiliar_id_id INT DEFAULT NULL, paciente_id_id INT DEFAULT NULL, observacion_id_id INT DEFAULT NULL, dieta_id_id INT DEFAULT NULL, drenaje_id_id INT DEFAULT NULL, movilizacion_id_id INT DEFAULT NULL, constantes_vitales_id_id INT DEFAULT NULL, balance_hidrico_id_id INT DEFAULT NULL, sueroterapia_id_id INT DEFAULT NULL, higiene_id_id INT DEFAULT NULL, INDEX IDX_397CA85B767EE6F6 (auxiliar_id_id), INDEX IDX_397CA85B8AA1655E (paciente_id_id), UNIQUE INDEX UNIQ_397CA85B15F167B (observacion_id_id), UNIQUE INDEX UNIQ_397CA85BF1E9B454 (dieta_id_id), UNIQUE INDEX UNIQ_397CA85B70E17E26 (drenaje_id_id), UNIQUE INDEX UNIQ_397CA85B522214C8 (movilizacion_id_id), UNIQUE INDEX UNIQ_397CA85B1DA1E444 (constantes_vitales_id_id), UNIQUE INDEX UNIQ_397CA85B496611F6 (balance_hidrico_id_id), UNIQUE INDEX UNIQ_397CA85BE7CBC1E1 (sueroterapia_id_id), UNIQUE INDEX UNIQ_397CA85BF4C77361 (higiene_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sueroterapia (id INT AUTO_INCREMENT NOT NULL, dosis NUMERIC(3, 1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_dieta (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_higiene (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tipo_textura (id INT AUTO_INCREMENT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE detalle_diagnostico ADD CONSTRAINT FK_D78E393B9EB8F283 FOREIGN KEY (diagnostico_id_id) REFERENCES diagnostico (id)');
        $this->addSql('ALTER TABLE diagnostico ADD CONSTRAINT FK_9B91D4488AA1655E FOREIGN KEY (paciente_id_id) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE diagnostico ADD CONSTRAINT FK_9B91D448767EE6F6 FOREIGN KEY (auxiliar_id_id) REFERENCES auxiliar (id)');
        $this->addSql('ALTER TABLE dieta ADD CONSTRAINT FK_D3447AEE43060ACC FOREIGN KEY (tipo_textura_id_id) REFERENCES tipo_textura (id)');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta ADD CONSTRAINT FK_60479CE8F1E9B454 FOREIGN KEY (dieta_id_id) REFERENCES dieta (id)');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta ADD CONSTRAINT FK_60479CE8C4CD9AAC FOREIGN KEY (tipo_dieta_id_id) REFERENCES tipo_dieta (id)');
        $this->addSql('ALTER TABLE higiene ADD CONSTRAINT FK_EF484A8FA9276E6C FOREIGN KEY (tipo_id) REFERENCES tipo_higiene (id)');
        $this->addSql('ALTER TABLE paciente_has_habitaciones ADD CONSTRAINT FK_C4D69A798AA1655E FOREIGN KEY (paciente_id_id) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE paciente_has_habitaciones ADD CONSTRAINT FK_C4D69A795303719C FOREIGN KEY (habitacion_id_id) REFERENCES habitacion (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B767EE6F6 FOREIGN KEY (auxiliar_id_id) REFERENCES auxiliar (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B8AA1655E FOREIGN KEY (paciente_id_id) REFERENCES paciente (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B15F167B FOREIGN KEY (observacion_id_id) REFERENCES observacion (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BF1E9B454 FOREIGN KEY (dieta_id_id) REFERENCES dieta (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B70E17E26 FOREIGN KEY (drenaje_id_id) REFERENCES drenaje (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B522214C8 FOREIGN KEY (movilizacion_id_id) REFERENCES movilizacion (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B1DA1E444 FOREIGN KEY (constantes_vitales_id_id) REFERENCES constantes_vitales (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85B496611F6 FOREIGN KEY (balance_hidrico_id_id) REFERENCES balance_hidrico (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BE7CBC1E1 FOREIGN KEY (sueroterapia_id_id) REFERENCES sueroterapia (id)');
        $this->addSql('ALTER TABLE registro ADD CONSTRAINT FK_397CA85BF4C77361 FOREIGN KEY (higiene_id_id) REFERENCES higiene (id)');

        // SEEDERS
        // 1. Tipos de higiene
        $this->addSql("INSERT INTO tipo_higiene (descripcion) VALUES ('Baño completo'), ('Higiene parcial'), ('Cambio de ropa')");

        // 2. Tipos de textura
        $this->addSql("INSERT INTO tipo_textura (descripcion) VALUES ('Sólida'), ('Puré'), ('Líquida')");

        // 3. Tipos de dieta
        $this->addSql("INSERT INTO tipo_dieta (descripcion) VALUES ('Baja en sal'), ('Diabética'), ('Alta en fibra')");

        // 4. Auxiliares (3 auxiliares con contraseñas hasheadas usando bcrypt)
        $this->addSql("INSERT INTO auxiliar (num_trabajador, nombre, apellidos, contraseña) VALUES 
            ('A001', 'Ana', 'García López', '$2y$13\$W8XzK6qQz8z1y2b3n4m5pO6r7s8t9u0v1w2x3y4z5A6B7C8D9E0F'),
            ('A002', 'Luis', 'Martínez Pérez', '$2y$13\$X9Y0Z1a2b3c4d5e6f7g8hI9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x'),
            ('A003', 'María', 'Rodríguez Sánchez', '$2y$13\$Y0Z1A2B3C4D5E6F7G8H9I0J1K2L3M4N5O6P7Q8R9S0T1U2V3W4X5')");

        // 5. Habitaciones (15 habitaciones)
        $this->addSql("INSERT INTO habitacion (codigo) VALUES 
            ('H001'), ('H002'), ('H003'), ('H004'), ('H005'), ('H006'), ('H007'), 
            ('H008'), ('H009'), ('H010'), ('H011'), ('H012'), ('H013'), ('H014'), ('H015')");

        // 6. Pacientes (7 pacientes)
        $this->addSql("INSERT INTO paciente (num_historial, nombre, apellidos, fecha_nacimiento, direccion_completa, lengua_materna, antecedentes, alergias, nombre_cuidador, telefono_cuidador, fecha_ingreso, timestamp) VALUES
            (1001, 'Juan', 'Pérez García', '1975-03-15', 'Calle Mayor 12, Madrid', 'Español', 'Hipertensión, diabetes tipo 2', 'Penicilina', 'Laura Pérez', '612345678', '2025-04-10 08:00:00', '2025-04-10 08:00:00'),
            (1002, 'Carmen', 'López Fernández', '1980-07-22', 'Avenida Sol 45, Barcelona', 'Español', 'Asma', 'Ninguna', 'Pedro López', '623456789', '2025-04-11 09:00:00', '2025-04-11 09:00:00'),
            (1003, 'Miguel', 'Gómez Ruiz', '1965-11-30', 'Plaza España 3, Valencia', 'Español', 'Infarto previo', 'Mariscos', 'Sofía Gómez', '634567890', '2025-04-12 10:00:00', '2025-04-12 10:00:00'),
            (1004, 'Isabel', 'Martínez Díaz', '1990-02-18', 'Calle Luna 8, Sevilla', 'Español', 'Ninguno', 'Polen', 'Antonio Martínez', '645678901', '2025-04-13 11:00:00', '2025-04-13 11:00:00'),
            (1005, 'Carlos', 'Sánchez Moreno', '1978-09-05', 'Paseo Mar 20, Málaga', 'Español', 'Colesterol alto', 'Ninguna', 'Elena Sánchez', '656789012', '2025-04-14 12:00:00', '2025-04-14 12:00:00'),
            (1006, 'Laura', 'Hernández Torres', '1985-04-12', 'Calle Río 15, Bilbao', 'Español', 'Migrañas crónicas', 'Aspirina', 'Javier Hernández', '667890123', '2025-04-15 13:00:00', '2025-04-15 13:00:00'),
            (1007, 'Pedro', 'Romero Castillo', '1970-12-25', 'Avenida Paz 30, Zaragoza', 'Español', 'Artritis', 'Ninguna', 'Clara Romero', '678901234', '2025-04-16 14:00:00', '2025-04-16 14:00:00')");

        // 7. Asignaciones de pacientes a habitaciones (7 asignaciones)
        $this->addSql("INSERT INTO paciente_has_habitaciones (timestamp, paciente_id_id, habitacion_id_id) VALUES
            ('2025-04-10 08:00:00', 1, 1),
            ('2025-04-11 09:00:00', 2, 2),
            ('2025-04-12 10:00:00', 3, 3),
            ('2025-04-13 11:00:00', 4, 4),
            ('2025-04-14 12:00:00', 5, 5),
            ('2025-04-15 13:00:00', 6, 6),
            ('2025-04-16 14:00:00', 7, 7)");

        // 8. Diagnósticos (2-3 por paciente, total 17)
        $this->addSql("INSERT INTO diagnostico (diagnostico, motivo, fecha, toma, paciente_id_id, auxiliar_id_id) VALUES
            ('Neumonía', 'Fiebre y dificultad respiratoria', '2025-04-10 09:00:00', 'M', 1, 1),
            ('Hipertensión no controlada', 'Dolor de cabeza y mareos', '2025-04-11 10:00:00', 'T', 1, 2),
            ('Asma agudizada', 'Sibilancias y opresión torácica', '2025-04-11 09:30:00', 'M', 2, 2),
            ('Infección respiratoria', 'Tos y fiebre', '2025-04-12 11:00:00', 'T', 2, 3),
            ('Infarto de miocardio', 'Dolor torácico', '2025-04-12 10:30:00', 'M', 3, 1),
            ('Arritmia', 'Palpitaciones', '2025-04-13 12:00:00', 'T', 3, 2),
            ('Gastritis', 'Dolor abdominal', '2025-04-13 11:30:00', 'M', 4, 3),
            ('Deshidratación', 'Debilidad general', '2025-04-14 13:00:00', 'T', 4, 1),
            ('Infección urinaria', 'Disuria y fiebre', '2025-04-14 12:30:00', 'M', 5, 2),
            ('Cistitis', 'Dolor pélvico', '2025-04-15 14:00:00', 'T', 5, 3),
            ('Migraña severa', 'Dolor de cabeza intenso', '2025-04-15 13:30:00', 'M', 6, 1),
            ('Cefalea tensional', 'Estrés', '2025-04-16 15:00:00', 'T', 6, 2),
            ('Artritis reumatoide', 'Dolor articular', '2025-04-16 14:30:00', 'M', 7, 3),
            ('Gota', 'Inflamación en pie', '2025-04-16 16:00:00', 'T', 7, 1),
            ('Neumonía', 'Tos productiva', '2025-04-10 11:00:00', 'T', 1, 3),
            ('Asma controlada', 'Mejoría clínica', '2025-04-12 09:00:00', 'M', 2, 1),
            ('Gastritis crónica', 'Náuseas', '2025-04-14 11:00:00', 'M', 4, 2)");

        // 9. Detalles de diagnóstico (uno por diagnóstico, total 17)
        $this->addSql("INSERT INTO detalle_diagnostico (o2, o2_descripcion, panales, panales_descripcion, sv, sr, sng, avd, diagnostico_id_id) VALUES
            (2, 'Oxígeno a 2 L/min', 2, 'Cambio cada 4 horas', 'Sonda vesical colocada', 'No aplica', 'No aplica', 'Dependiente', 1),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 2),
            (3, 'Oxígeno a 3 L/min', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Parcialmente dependiente', 3),
            (0, 'No requiere oxígeno', 1, 'Cambio cada 6 horas', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 4),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Dependiente', 5),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 6),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'Sonda nasogástrica', 'Parcialmente dependiente', 7),
            (0, 'No requiere oxígeno', 3, 'Cambio cada 3 horas', 'No aplica', 'No aplica', 'No aplica', 'Dependiente', 8),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'Sonda vesical', 'No aplica', 'No aplica', 'Independiente', 9),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 10),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 11),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 12),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Parcialmente dependiente', 13),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 14),
            (2, 'Oxígeno a 2 L/min', 2, 'Cambio cada 4 horas', 'No aplica', 'No aplica', 'No aplica', 'Dependiente', 15),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'No aplica', 'Independiente', 16),
            (0, 'No requiere oxígeno', 0, 'No usa pañales', 'No aplica', 'No aplica', 'Sonda nasogástrica', 'Dependiente', 17)");

        // 10. Dietas (una por registro, total 49)
        $this->addSql("INSERT INTO dieta (autonomo, protesi, tipo_textura_id_id) VALUES
            (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1),
            (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2),
            (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3),
            (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1),
            (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2), (1, 1, 2),
            (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3), (0, 0, 3),
            (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1), (1, 0, 1)");

        // 11. Relación dieta_has_tipo_dieta (asignar tipos de dieta a cada dieta, total 49)
        $this->addSql("INSERT INTO dieta_has_tipo_dieta (dieta_id_id, tipo_dieta_id_id) VALUES
            (1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1),
            (8, 2), (9, 2), (10, 2), (11, 2), (12, 2), (13, 2), (14, 2),
            (15, 3), (16, 3), (17, 3), (18, 3), (19, 3), (20, 3), (21, 3),
            (22, 1), (23, 1), (24, 1), (25, 1), (26, 1), (27, 1), (28, 1),
            (29, 2), (30, 2), (31, 2), (32, 2), (33, 2), (34, 2), (35, 2),
            (36, 3), (37, 3), (38, 3), (39, 3), (40, 3), (41, 3), (42, 3),
            (43, 1), (44, 1), (45, 1), (46, 1), (47, 1), (48, 1), (49, 1)");

        // 12. Constantes vitales (una por registro, total 49: 7 pacientes x 7 registros)
        $this->addSql("INSERT INTO constantes_vitales (ta_sistolica, ta_diastolica, frecuencia_respiratoria, pulso, temperatura, saturacion_oxigeno) VALUES
            ('120', '80', 16.0, 72.0, 36.5, 98.0), ('130', '85', 18.0, 80.0, 37.0, 97.0), ('125', '82', 17.0, 75.0, 36.8, 96.0), ('122', '78', 16.0, 70.0, 36.6, 98.0), ('128', '83', 18.0, 78.0, 37.1, 95.0), ('123', '80', 17.0, 73.0, 36.7, 97.0), ('121', '79', 16.0, 71.0, 36.5, 98.0),
            ('110', '70', 20.0, 85.0, 37.2, 94.0), ('115', '75', 19.0, 82.0, 37.0, 95.0), ('112', '72', 21.0, 88.0, 37.3, 93.0), ('108', '68', 20.0, 84.0, 37.1, 94.0), ('114', '74', 19.0, 83.0, 37.0, 95.0), ('111', '71', 20.0, 86.0, 37.2, 94.0), ('109', '69', 19.0, 85.0, 37.1, 95.0),
            ('140', '90', 14.0, 68.0, 36.4, 99.0), ('145', '95', 15.0, 70.0, 36.5, 98.0), ('142', '92', 14.0, 69.0, 36.3, 99.0), ('138', '88', 15.0, 67.0, 36.4, 98.0), ('143', '93', 14.0, 68.0, 36.5, 99.0), ('141', '91', 15.0, 69.0, 36.4, 98.0), ('139', '89', 14.0, 68.0, 36.5, 99.0),
            ('118', '78', 18.0, 76.0, 37.0, 96.0), ('120', '80', 17.0, 74.0, 36.9, 97.0), ('116', '76', 18.0, 75.0, 37.1, 96.0), ('119', '79', 17.0, 73.0, 37.0, 97.0), ('117', '77', 18.0, 74.0, 36.9, 96.0), ('115', '75', 17.0, 75.0, 37.0, 97.0), ('118', '78', 18.0, 76.0, 36.9, 96.0),
            ('130', '85', 16.0, 80.0, 36.8, 95.0), ('132', '87', 17.0, 82.0, 36.9, 94.0), ('128', '83', 16.0, 81.0, 36.7, 95.0), ('131', '86', 17.0, 83.0, 36.8, 94.0), ('129', '84', 16.0, 80.0, 36.9, 95.0), ('130', '85', 17.0, 82.0, 36.8, 94.0), ('132', '87', 16.0, 81.0, 36.9, 95.0),
            ('112', '72', 19.0, 78.0, 37.2, 93.0), ('114', '74', 18.0, 77.0, 37.1, 94.0), ('110', '70', 19.0, 79.0, 37.3, 93.0), ('113', '73', 18.0, 78.0, 37.2, 94.0), ('111', '71', 19.0, 77.0, 37.1, 93.0), ('112', '72', 18.0, 78.0, 37.2, 94.0), ('114', '74', 19.0, 79.0, 37.1, 93.0),
            ('125', '80', 15.0, 70.0, 36.6, 98.0), ('127', '82', 16.0, 72.0, 36.7, 97.0), ('123', '78', 15.0, 71.0, 36.6, 98.0), ('126', '81', 16.0, 73.0, 36.7, 97.0), ('124', '79', 15.0, 70.0, 36.6, 98.0), ('125', '80', 16.0, 72.0, 36.7, 97.0), ('127', '82', 15.0, 71.0, 36.6, 98.0)");

        // 13. Balance hídrico (una por registro, total 49)
        $this->addSql("INSERT INTO balance_hidrico (diuresis, deposicion) VALUES
            (500, 'Normal'), (450, 'Blanda'), (600, 'Normal'), (550, 'Dura'), (400, 'Normal'), (500, 'Blanda'), (450, 'Normal'),
            (300, 'Normal'), (350, 'Blanda'), (400, 'Normal'), (320, 'Dura'), (360, 'Normal'), (340, 'Blanda'), (380, 'Normal'),
            (700, 'Normal'), (650, 'Blanda'), (720, 'Normal'), (680, 'Dura'), (700, 'Normal'), (660, 'Blanda'), (690, 'Normal'),
            (400, 'Normal'), (420, 'Blanda'), (450, 'Normal'), (430, 'Dura'), (410, 'Normal'), (440, 'Blanda'), (420, 'Normal'),
            (550, 'Normal'), (500, 'Blanda'), (520, 'Normal'), (510, 'Dura'), (530, 'Normal'), (540, 'Blanda'), (500, 'Normal'),
            (350, 'Normal'), (360, 'Blanda'), (340, 'Normal'), (370, 'Dura'), (350, 'Normal'), (380, 'Blanda'), (360, 'Normal'),
            (600, 'Normal'), (620, 'Blanda'), (610, 'Normal'), (630, 'Dura'), (600, 'Normal'), (640, 'Blanda'), (620, 'Normal')");

        // 14. Drenaje (una por registro, total 49)
        $this->addSql("INSERT INTO drenaje (descripcion) VALUES
            ('Drenaje vesical: 200 ml, claro'), ('Sin drenaje'), ('Drenaje vesical: 180 ml, claro'), ('Sin drenaje'), ('Drenaje vesical: 210 ml, claro'), ('Sin drenaje'), ('Drenaje vesical: 190 ml, claro'),
            ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'),
            ('Drenaje torácico: 150 ml, seroso'), ('Sin drenaje'), ('Drenaje torácico: 140 ml, seroso'), ('Sin drenaje'), ('Drenaje torácico: 160 ml, seroso'), ('Sin drenaje'), ('Drenaje torácico: 145 ml, seroso'),
            ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'),
            ('Drenaje vesical: 220 ml, claro'), ('Sin drenaje'), ('Drenaje vesical: 200 ml, claro'), ('Sin drenaje'), ('Drenaje vesical: 230 ml, claro'), ('Sin drenaje'), ('Drenaje vesical: 210 ml, claro'),
            ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'),
            ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje'), ('Sin drenaje')");

        // 15. Movilización (una por registro, total 49)
        $this->addSql("INSERT INTO movilizacion (sedestacion, ayuda_deambulacion, ayuda_descripcion, cambios_posturales) VALUES
            ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'),
            ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'),
            ('Sedestación en silla', 1, 'Silla de ruedas', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Silla de ruedas', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Silla de ruedas', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Silla de ruedas', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Silla de ruedas', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Silla de ruedas', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Silla de ruedas', 'Cada 2 horas'),
            ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 3 horas'),
            ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'),
            ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'), ('Sin sedestación', 0, 'No requiere ayuda', 'Cada 4 horas'),
            ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas'), ('Sedestación en cama', 1, 'Andador', 'Cada 2 horas'), ('Sedestación en silla', 1, 'Bastón', 'Cada 2 horas')");

        // 16. Higiene (una por registro, total 49)
        $this->addSql("INSERT INTO higiene (descripcion, tipo_id) VALUES
            ('Baño completo realizado', 1), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Baño completo realizado', 1),
            ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2),
            ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1),
            ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2),
            ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1),
            ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2), ('Cambio de ropa', 3), ('Higiene parcial en cama', 2),
            ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1), ('Cambio de ropa', 3), ('Baño completo realizado', 1)");

        // 17. Observación (una por registro, total 49)
        $this->addSql("INSERT INTO observacion (descripcion) VALUES
            ('Paciente estable, responde al tratamiento'), ('Leve mejoría observada'), ('Sin cambios relevantes'), ('Paciente estable, responde al tratamiento'), ('Leve mejoría observada'), ('Sin cambios relevantes'), ('Paciente estable, responde al tratamiento'),
            ('Dificultad respiratoria leve'), ('Sin cambios relevantes'), ('Dificultad respiratoria leve'), ('Sin cambios relevantes'), ('Dificultad respiratoria leve'), ('Sin cambios relevantes'), ('Dificultad respiratoria leve'),
            ('Paciente estable, controlado'), ('Sin cambios relevantes'), ('Paciente estable, controlado'), ('Sin cambios relevantes'), ('Paciente estable, controlado'), ('Sin cambios relevantes'), ('Paciente estable, controlado'),
            ('Dolor abdominal controlado'), ('Sin cambios relevantes'), ('Dolor abdominal controlado'), ('Sin cambios relevantes'), ('Dolor abdominal controlado'), ('Sin cambios relevantes'), ('Dolor abdominal controlado'),
            ('Infección controlada con antibióticos'), ('Sin cambios relevantes'), ('Infección controlada con antibióticos'), ('Sin cambios relevantes'), ('Infección controlada con antibióticos'), ('Sin cambios relevantes'), ('Infección controlada con antibióticos'),
            ('Dolor de cabeza controlado'), ('Sin cambios relevantes'), ('Dolor de cabeza controlado'), ('Sin cambios relevantes'), ('Dolor de cabeza controlado'), ('Sin cambios relevantes'), ('Dolor de cabeza controlado'),
            ('Dolor articular controlado'), ('Sin cambios relevantes'), ('Dolor articular controlado'), ('Sin cambios relevantes'), ('Dolor articular controlado'), ('Sin cambios relevantes'), ('Dolor articular controlado')");

        // 18. Sueroterapia (una por registro, total 49)
        $this->addSql("INSERT INTO sueroterapia (dosis) VALUES
            (500.0), (0.0), (500.0), (0.0), (500.0), (0.0), (500.0),
            (0.0), (0.0), (0.0), (0.0), (0.0), (0.0), (0.0),
            (1000.0), (0.0), (1000.0), (0.0), (1000.0), (0.0), (1000.0),
            (0.0), (0.0), (0.0), (0.0), (0.0), (0.0), (0.0),
            (500.0), (0.0), (500.0), (0.0), (500.0), (0.0), (500.0),
            (0.0), (0.0), (0.0), (0.0), (0.0), (0.0), (0.0),
            (0.0), (0.0), (0.0), (0.0), (0.0), (0.0), (0.0)");

        // 19. Registros (7 por paciente, total 49)
        $this->addSql("INSERT INTO registro (fecha, toma, auxiliar_id_id, paciente_id_id, observacion_id_id, dieta_id_id, drenaje_id_id, movilizacion_id_id, constantes_vitales_id_id, balance_hidrico_id_id, sueroterapia_id_id, higiene_id_id) VALUES
            ('2025-04-10 08:00:00', 'M', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
            ('2025-04-10 16:00:00', 'T', 2, 1, 2, 2, 2, 2, 2, 2, 2, 2),
            ('2025-04-11 08:00:00', 'M', 3, 1, 3, 3, 3, 3, 3, 3, 3, 3),
            ('2025-04-11 16:00:00', 'T', 1, 1, 4, 4, 4, 4, 4, 4, 4, 4),
            ('2025-04-12 08:00:00', 'M', 2, 1, 5, 5, 5, 5, 5, 5, 5, 5),
            ('2025-04-12 16:00:00', 'T', 3, 1, 6, 6, 6, 6, 6, 6, 6, 6),
            ('2025-04-13 08:00:00', 'M', 1, 1, 7, 7, 7, 7, 7, 7, 7, 7),
            ('2025-04-11 08:00:00', 'M', 2, 2, 8, 8, 8, 8, 8, 8, 8, 8),
            ('2025-04-11 16:00:00', 'T', 3, 2, 9, 9, 9, 9, 9, 9, 9, 9),
            ('2025-04-12 08:00:00', 'M', 1, 2, 10, 10, 10, 10, 10, 10, 10, 10),
            ('2025-04-12 16:00:00', 'T', 2, 2, 11, 11, 11, 11, 11, 11, 11, 11),
            ('2025-04-13 08:00:00', 'M', 3, 2, 12, 12, 12, 12, 12, 12, 12, 12),
            ('2025-04-13 16:00:00', 'T', 1, 2, 13, 13, 13, 13, 13, 13, 13, 13),
            ('2025-04-14 08:00:00', 'M', 2, 2, 14, 14, 14, 14, 14, 14, 14, 14),
            ('2025-04-12 08:00:00', 'M', 3, 3, 15, 15, 15, 15, 15, 15, 15, 15),
            ('2025-04-12 16:00:00', 'T', 1, 3, 16, 16, 16, 16, 16, 16, 16, 16),
            ('2025-04-13 08:00:00', 'M', 2, 3, 17, 17, 17, 17, 17, 17, 17, 17),
            ('2025-04-13 16:00:00', 'T', 3, 3, 18, 18, 18, 18, 18, 18, 18, 18),
            ('2025-04-14 08:00:00', 'M', 1, 3, 19, 19, 19, 19, 19, 19, 19, 19),
            ('2025-04-14 16:00:00', 'T', 2, 3, 20, 20, 20, 20, 20, 20, 20, 20),
            ('2025-04-15 08:00:00', 'M', 3, 3, 21, 21, 21, 21, 21, 21, 21, 21),
            ('2025-04-13 08:00:00', 'M', 1, 4, 22, 22, 22, 22, 22, 22, 22, 22),
            ('2025-04-13 16:00:00', 'T', 2, 4, 23, 23, 23, 23, 23, 23, 23, 23),
            ('2025-04-14 08:00:00', 'M', 3, 4, 24, 24, 24, 24, 24, 24, 24, 24),
            ('2025-04-14 16:00:00', 'T', 1, 4, 25, 25, 25, 25, 25, 25, 25, 25),
            ('2025-04-15 08:00:00', 'M', 2, 4, 26, 26, 26, 26, 26, 26, 26, 26),
            ('2025-04-15 16:00:00', 'T', 3, 4, 27, 27, 27, 27, 27, 27, 27, 27),
            ('2025-04-16 08:00:00', 'M', 1, 4, 28, 28, 28, 28, 28, 28, 28, 28),
            ('2025-04-14 08:00:00', 'M', 2, 5, 29, 29, 29, 29, 29, 29, 29, 29),
            ('2025-04-14 16:00:00', 'T', 3, 5, 30, 30, 30, 30, 30, 30, 30, 30),
            ('2025-04-15 08:00:00', 'M', 1, 5, 31, 31, 31, 31, 31, 31, 31, 31),
            ('2025-04-15 16:00:00', 'T', 2, 5, 32, 32, 32, 32, 32, 32, 32, 32),
            ('2025-04-16 08:00:00', 'M', 3, 5, 33, 33, 33, 33, 33, 33, 33, 33),
            ('2025-04-16 16:00:00', 'T', 1, 5, 34, 34, 34, 34, 34, 34, 34, 34),
            ('2025-04-17 08:00:00', 'M', 2, 5, 35, 35, 35, 35, 35, 35, 35, 35),
            ('2025-04-15 08:00:00', 'M', 3, 6, 36, 36, 36, 36, 36, 36, 36, 36),
            ('2025-04-15 16:00:00', 'T', 1, 6, 37, 37, 37, 37, 37, 37, 37, 37),
            ('2025-04-16 08:00:00', 'M', 2, 6, 38, 38, 38, 38, 38, 38, 38, 38),
            ('2025-04-16 16:00:00', 'T', 3, 6, 39, 39, 39, 39, 39, 39, 39, 39),
            ('2025-04-17 08:00:00', 'M', 1, 6, 40, 40, 40, 40, 40, 40, 40, 40),
            ('2025-04-17 16:00:00', 'T', 2, 6, 41, 41, 41, 41, 41, 41, 41, 41),
            ('2025-04-18 08:00:00', 'M', 3, 6, 42, 42, 42, 42, 42, 42, 42, 42),
            ('2025-04-16 08:00:00', 'M', 1, 7, 43, 43, 43, 43, 43, 43, 43, 43),
            ('2025-04-16 16:00:00', 'T', 2, 7, 44, 44, 44, 44, 44, 44, 44, 44),
            ('2025-04-17 08:00:00', 'M', 3, 7, 45, 45, 45, 45, 45, 45, 45, 45),
            ('2025-04-17 16:00:00', 'T', 1, 7, 46, 46, 46, 46, 46, 46, 46, 46),
            ('2025-04-18 08:00:00', 'M', 2, 7, 47, 47, 47, 47, 47, 47, 47, 47),
            ('2025-04-18 16:00:00', 'T', 3, 7, 48, 48, 48, 48, 48, 48, 48, 48),
            ('2025-04-19 08:00:00', 'M', 1, 7, 49, 49, 49, 49, 49, 49, 49, 49)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_diagnostico DROP FOREIGN KEY FK_D78E393B9EB8F283');
        $this->addSql('ALTER TABLE diagnostico DROP FOREIGN KEY FK_9B91D4488AA1655E');
        $this->addSql('ALTER TABLE diagnostico DROP FOREIGN KEY FK_9B91D448767EE6F6');
        $this->addSql('ALTER TABLE dieta DROP FOREIGN KEY FK_D3447AEE43060ACC');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta DROP FOREIGN KEY FK_60479CE8F1E9B454');
        $this->addSql('ALTER TABLE dieta_has_tipo_dieta DROP FOREIGN KEY FK_60479CE8C4CD9AAC');
        $this->addSql('ALTER TABLE higiene DROP FOREIGN KEY FK_EF484A8FA9276E6C');
        $this->addSql('ALTER TABLE paciente_has_habitaciones DROP FOREIGN KEY FK_C4D69A798AA1655E');
        $this->addSql('ALTER TABLE paciente_has_habitaciones DROP FOREIGN KEY FK_C4D69A795303719C');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B767EE6F6');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B8AA1655E');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B15F167B');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BF1E9B454');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B70E17E26');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B522214C8');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B1DA1E444');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85B496611F6');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BE7CBC1E1');
        $this->addSql('ALTER TABLE registro DROP FOREIGN KEY FK_397CA85BF4C77361');
        $this->addSql('DROP TABLE auxiliar');
        $this->addSql('DROP TABLE balance_hidrico');
        $this->addSql('DROP TABLE constantes_vitales');
        $this->addSql('DROP TABLE detalle_diagnostico');
        $this->addSql('DROP TABLE diagnostico');
        $this->addSql('DROP TABLE dieta');
        $this->addSql('DROP TABLE dieta_has_tipo_dieta');
        $this->addSql('DROP TABLE drenaje');
        $this->addSql('DROP TABLE habitacion');
        $this->addSql('DROP TABLE higiene');
        $this->addSql('DROP TABLE movilizacion');
        $this->addSql('DROP TABLE observacion');
        $this->addSql('DROP TABLE paciente');
        $this->addSql('DROP TABLE paciente_has_habitaciones');
        $this->addSql('DROP TABLE registro');
        $this->addSql('DROP TABLE sueroterapia');
        $this->addSql('DROP TABLE tipo_dieta');
        $this->addSql('DROP TABLE tipo_higiene');
        $this->addSql('DROP TABLE tipo_textura');
        $this->addSql('DROP TABLE messenger_messages');
    }
}