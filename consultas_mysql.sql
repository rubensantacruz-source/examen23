USE PERSONAL;

-- Consulta A: todos los departamentos
SELECT * FROM DEPARTAMENTO;

-- Consulta B: todos los empleados
SELECT * FROM EMPLEADOS;

-- Consulta C: empleados de apellido López
SELECT * FROM EMPLEADOS WHERE Apellidos = 'López';

-- Consulta D: empleados de apellido López o Pérez
SELECT * FROM EMPLEADOS WHERE Apellidos IN ('López', 'Pérez');

-- Consulta E: nombre y clave de empleados del departamento 4
SELECT Nombre, Clave_empleado
FROM EMPLEADOS
WHERE Clave_Depto = 4;

-- Consulta F: empleados de los departamentos 1 o 3, ordenados por nombre
SELECT *
FROM EMPLEADOS
WHERE Clave_Depto IN (1, 3)
ORDER BY Nombre;

-- Consulta G: nombre y apellidos que comienzan con H
SELECT Nombre, Apellidos
FROM EMPLEADOS
WHERE Apellidos LIKE 'H%';

-- Consulta H: departamentos con presupuesto entre 50000 y 70000
SELECT *
FROM DEPARTAMENTO
WHERE Presupuesto BETWEEN 50000 AND 70000;

-- Consulta I: empleados del departamento de Contabilidad
SELECT EMPLEADOS.Clave_empleado, EMPLEADOS.Nombre, EMPLEADOS.Apellidos
FROM EMPLEADOS
INNER JOIN DEPARTAMENTO
    ON EMPLEADOS.Clave_Depto = DEPARTAMENTO.Clave_Depto
WHERE DEPARTAMENTO.Nombre = 'Contabilidad';

-- Consulta J: empleados y el nombre de su departamento
SELECT EMPLEADOS.Nombre, EMPLEADOS.Apellidos,
       DEPARTAMENTO.Nombre AS Nombre_Departamento
FROM EMPLEADOS
INNER JOIN DEPARTAMENTO
    ON EMPLEADOS.Clave_Depto = DEPARTAMENTO.Clave_Depto;
