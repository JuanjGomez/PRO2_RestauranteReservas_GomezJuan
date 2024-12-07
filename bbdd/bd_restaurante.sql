CREATE SCHEMA `db_restaurante` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE db_restaurante;

-- CREACIÓN TABLA ROLES
CREATE TABLE `db_restaurante`.`roles` (
  `id_rol` INT NOT NULL AUTO_INCREMENT,
  `nombre_rol` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA USUARIOS
CREATE TABLE `db_restaurante`.`usuarios` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL UNIQUE,
  `apellido` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(15) NOT NULL,
  `dni` CHAR(9) NOT NULL,
  `direccion` VARCHAR(255) NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `id_rol` INT NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA HISTORIAL
CREATE TABLE `db_restaurante`.`historial` (
  `id_historial` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `id_mesa` INT NOT NULL,
  `hora_inicio` DATETIME NOT NULL,
  `hora_fin` DATETIME NOT NULL,
  PRIMARY KEY (`id_historial`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA MESA
CREATE TABLE `db_restaurante`.`mesa` (
  `id_mesa` INT NOT NULL AUTO_INCREMENT,
  `id_sala` INT NOT NULL,
  `libre` TINYINT NOT NULL,
  `num_sillas` INT(2) NOT NULL,
  PRIMARY KEY (`id_mesa`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA SALA
CREATE TABLE `db_restaurante`.`sala` (
  `id_sala` INT NOT NULL AUTO_INCREMENT,
  `id_tipoSala` INT NOT NULL,
  `nombre_sala` VARCHAR(45) NOT NULL,
  `imagen_sala` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_sala`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA TIPO SALA
CREATE TABLE `db_restaurante`.`tipo_sala` (
  `id_tipoSala` INT NOT NULL AUTO_INCREMENT,
  `tipo_sala` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_tipoSala`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACION TABLA STOCK
CREATE TABLE `db_restaurante`.`stock` (
  `idStock` INT NOT NULL AUTO_INCREMENT,
  `sillas_stock` INT NOT NULL,
  `id_tipoSala` INT NOT NULL,
  PRIMARY KEY (`idStock`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACION TABLA RESERVAS
CREATE TABLE `db_restaurante`.`reservas` (
  `id_reserva` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `id_mesa` INT NOT NULL,
  `nombre_reserva`, VARCHAR(30) NOT NULL,
  `fecha_reserva` DATETIME NOT NULL,
  `hora_inicio_reserva` DATETIME NOT NULL,
  `hora_final_reserva` DATETIME NOT NULL,
  PRIMARY KEY (`id_reserva`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;


-- CREACIÓN FOREIGN KEYS


-- FOREIGN KEY en la tabla 'usuarios'
ALTER TABLE 
  `db_restaurante`.`usuarios`
ADD 
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `db_restaurante`.`roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

-- FOREIGN KEYS en la tabla 'historial'
ALTER TABLE 
  `db_restaurante`.`historial`
ADD 
  INDEX `fk_id_usuario_idx` (`id_usuario`),
ADD 
  INDEX `fk_id_mesa_idx` (`id_mesa`);

ALTER TABLE 
  `db_restaurante`.`historial`
ADD 
  CONSTRAINT `fk_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `db_restaurante`.`usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD 
  CONSTRAINT `fk_id_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `db_restaurante`.`mesa` (`id_mesa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- FOREIGN KEY en la tabla 'mesa'
ALTER TABLE 
  `db_restaurante`.`mesa`
ADD 
  INDEX `fk_id_Sala_idx` (`id_sala`);

ALTER TABLE 
  `db_restaurante`.`mesa`
ADD 
  CONSTRAINT `fk_id_Sala` FOREIGN KEY (`id_sala`) REFERENCES `db_restaurante`.`sala` (`id_sala`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- FOREIGN KEY en la tabla 'sala'
ALTER TABLE 
  `db_restaurante`.`sala`
ADD 
  INDEX `fk_id_tipoSala_idx` (`id_tipoSala`);

ALTER TABLE 
  `db_restaurante`.`sala`
ADD 
  CONSTRAINT `fk_id_tipoSala` FOREIGN KEY (`id_tipoSala`) REFERENCES `db_restaurante`.`tipo_sala` (`id_tipoSala`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- FOREIGN KEY en la tabla 'reservas'
ALTER TABLE 
  `db_restaurante`.`reservas`
ADD 
  CONSTRAINT `fk_reservas_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `db_restaurante`.`usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD 
  CONSTRAINT `fk_reservas_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `db_restaurante`.`mesa` (`id_mesa`) ON DELETE CASCADE ON UPDATE CASCADE;

-- FOREIGN KEY en la tabla 'stock'
ALTER TABLE 
  `db_restaurante`.`stock`
ADD
  CONSTRAINT `fk_stock_tipoSala` FOREIGN KEY (`id_tipoSala`) REFERENCES `db_restaurante`.`tipo_sala` (`id_tipoSala`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Insert roles
  INSERT INTO `db_restaurante`.`roles` (`nombre_rol`) VALUES ('Camarero');
  INSERT INTO `db_restaurante`.`roles` (`nombre_rol`) VALUES ('Gerente');
  INSERT INTO `db_restaurante`.`roles` (`nombre_rol`) VALUES ('Mantenimiento');

-- Insert usuarios
-- pwd: asdASD123
INSERT INTO
  `usuarios` (`id_usuario`, `nombre`, `usuario`, `apellido`, `telefono`, `dni`, `direccion`, `fecha_nacimiento`, `password`, `email` , `id_rol`)
VALUES
  (
    NULL,
    'Julio',
    'Julio',
    'Garcia',
    '658391834',
    '64857465T',
    'Calle 123',
    '2004-01-01',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS',
    'julio@gmail.com',
    1
  ),
  (
    NULL,
    'Marc M',
    'MarcM',
    'Martinez',
    '613948137',
    '83752948R',
    'Calle 456',
    '2005-01-01',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS',
    'marcM@gmail.com',
    1
  ),
  (
    NULL,
    'Marc C',
    'MarcC',
    'Colome',
    '641950938',
    '14567245F',
    'Calle 789',
    '2005-11-09',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS',
    'marcC@gmail.com',
    1
  ),
  (
    NULL,
    'Juanjo',
    'Juanjo',
    'Gomez',
    '651869583',
    '83746135Z',
    'Calle 287',
    '2005-04-09',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS',
    'juanjo@gmail.com',
    2
  );

  -- Insert tipo sala
  INSERT INTO `db_restaurante`.`tipo_sala` (`tipo_sala`) VALUES ('Terraza');
  INSERT INTO `db_restaurante`.`tipo_sala` (`tipo_sala`) VALUES ('Comedor');
  INSERT INTO `db_restaurante`.`tipo_sala` (`tipo_sala`) VALUES ('Sala privada');

  -- Insert salas
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('1', 'Terraza principal','img/Terraza principal.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('1', 'Terraza este','img/Terraza este.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('1', 'Terraza oeste','img/Terraza oeste.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('2', 'Comedor 1 PB','img/Comedor 1 PB.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('2', 'Comedor 2 P1','img/Comedor 2 P1.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('3', 'Sala privada PB','img/Sala privada PB.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('3', 'Sala privada 1 P1','img/Sala privada 1 P1.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('3', 'Sala privada 2 P1','img/Sala privada 2 P1.jpg');
  INSERT INTO `db_restaurante`.`sala` (`id_tipoSala`, `nombre_sala`, `imagen_sala`) VALUES ('3', 'Sala privada 3 P1','img/Sala privada 3 P1.jpg');

-- Insert mesas
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '4');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '2');
INSERT INTO `db_restaurante`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '2');
-- Insert stock
INSERT INTO `db_restaurante`.`stock` (`sillas_stock`,`id_tipoSala`) VALUES (30,1);
INSERT INTO `db_restaurante`.`stock` (`sillas_stock`,`id_tipoSala`) VALUES (30,2);
INSERT INTO `db_restaurante`.`stock` (`sillas_stock`,`id_tipoSala`) VALUES (30,3);