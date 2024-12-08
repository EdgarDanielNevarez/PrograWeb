CREATE DATABASE sistema_chequeo;

USE sistema_chequeo;

CREATE TABLE Roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Roles(id_rol)
);

CREATE TABLE Documentos (
    id_documento INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL
);


-- Asegúrate de que los roles están definidos en la tabla Roles
INSERT INTO Roles (id_rol, nombre_rol) VALUES
(1, 'Administrador'),
(2, 'Lector');

-- Agregar los usuarios
INSERT INTO Usuarios (nombre, correo, contraseña, id_rol) VALUES
('Juan Pérez', 'admin@ejemplo.com', 'admin123', 1),
('Ana López', 'lector@ejemplo.com', 'lector123', 2);

SELECT Usuarios.nombre, Usuarios.correo, Roles.nombre_rol
FROM Usuarios
JOIN Roles ON Usuarios.id_rol = Roles.id_rol;
