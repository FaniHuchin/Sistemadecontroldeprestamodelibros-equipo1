<?php
require_once 'config/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $correo    = trim($_POST['correo'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $matricula === '') {
        $mensaje = 'Nombre y matrícula son obligatorios.';
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO usuarios (nombre, matricula, correo, telefono) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $matricula, $correo, $telefono);

        if ($stmt->execute()) {
            $mensaje = 'Usuario registrado correctamente.';
        } elseif ($conexion->errno === 1062) {
            $mensaje = 'Esa matrícula ya está registrada.';
        } else {
            $mensaje = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Registro de usuarios</title></head>
<body>
  <h1>Registrar usuario</h1>
  <?php if ($mensaje): ?><p><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>

  <form method="POST">
    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <label>Matrícula: <input type="text" name="matricula" required></label><br>
    <label>Correo: <input type="email" name="correo"></label><br>
    <label>Teléfono: <input type="text" name="telefono"></label><br>
    <button type="submit">Registrar</button>
    <a href="usuarios.php">Ver usuarios</a>
  </form>
</body>
</html>

<?php
require_once 'config/conexion.php';
$resultado = $conexion->query("SELECT * FROM usuarios ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios</title>
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <h1>Usuarios registrados</h1>
  <p><a href="registro_usuario.php">+ Registrar usuario</a></p>

  <table>
    <tr><th>ID</th><th>Nombre</th><th>Matrícula</th><th>Correo</th><th>Teléfono</th><th>Acciones</th></tr>
    <?php while ($u = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['nombre']) ?></td>
        <td><?= htmlspecialchars($u['matricula']) ?></td>
        <td><?= htmlspecialchars($u['correo']) ?></td>
        <td><?= htmlspecialchars($u['telefono']) ?></td>
        <td>
          <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a> |
          <a href="eliminar_usuario.php?id=<?= $u['id'] ?>"
             onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

<?php
require_once 'config/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']);
    $matricula = trim($_POST['matricula']);
    $correo    = trim($_POST['correo']);
    $telefono  = trim($_POST['telefono']);

    $stmt = $conexion->prepare(
        "UPDATE usuarios SET nombre=?, matricula=?, correo=?, telefono=? WHERE id=?"
    );
    $stmt->bind_param("ssssi", $nombre, $matricula, $correo, $telefono, $id);
    $stmt->execute();
    header("Location: usuarios.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
if (!$u) { die("Usuario no encontrado."); }
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Editar usuario</title></head>
<body>
  <h1>Editar usuario</h1>
  <form method="POST">
    <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" required></label><br>
    <label>Matrícula: <input type="text" name="matricula" value="<?= htmlspecialchars($u['matricula']) ?>" required></label><br>
    <label>Correo: <input type="email" name="correo" value="<?= htmlspecialchars($u['correo']) ?>"></label><br>
    <label>Teléfono: <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono']) ?>"></label><br>
    <button type="submit">Guardar cambios</button>
    <a href="usuarios.php">Cancelar</a>
  </form>
</body>
</html>

