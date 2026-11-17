<?php
require_once 'config/conexion.php';

$resultado = $conexion->query("SELECT * FROM libros ORDER BY titulo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de libros</title>
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <h1>Libros registrados</h1>
  <p><a href="registro_libro.php">+ Registrar nuevo libro</a></p>

  <table>
    <tr>
      <th>ID</th><th>Título</th><th>Autor</th><th>ISBN</th>
      <th>Categoría</th><th>Cantidad</th><th>Estado</th><th>Acciones</th>
    </tr>
    <?php while ($libro = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= $libro['id'] ?></td>
        <td><?= htmlspecialchars($libro['titulo']) ?></td>
        <td><?= htmlspecialchars($libro['autor']) ?></td>
        <td><?= htmlspecialchars($libro['isbn']) ?></td>
        <td><?= htmlspecialchars($libro['categoria']) ?></td>
        <td><?= $libro['cantidad'] ?></td>
        <td><?= $libro['cantidad'] > 0 ? 'Disponible' : 'Agotado' ?></td>
        <td>
          <a href="editar_libro.php?id=<?= $libro['id'] ?>">Editar</a> |
          <a href="eliminar_libro.php?id=<?= $libro['id'] ?>"
             onclick="return confirm('¿Eliminar este libro?')">Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

<?php
require_once 'config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']);
    $autor     = trim($_POST['autor']);
    $isbn      = trim($_POST['isbn']);
    $categoria = trim($_POST['categoria']);
    $cantidad  = (int)$_POST['cantidad'];

    $stmt = $conexion->prepare(
        "UPDATE libros SET titulo=?, autor=?, isbn=?, categoria=?, cantidad=? WHERE id=?"
    );
    $stmt->bind_param("ssssii", $titulo, $autor, $isbn, $categoria, $cantidad, $id);
    $stmt->execute();
    header("Location: libros.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM libros WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) { die("Libro no encontrado."); }
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Editar libro</title></head>
<body>
  <h1>Editar libro</h1>
  <form method="POST">
    <label>Título: <input type="text" name="titulo" value="<?= htmlspecialchars($libro['titulo']) ?>" required></label><br>
    <label>Autor: <input type="text" name="autor" value="<?= htmlspecialchars($libro['autor']) ?>" required></label><br>
    <label>ISBN: <input type="text" name="isbn" value="<?= htmlspecialchars($libro['isbn']) ?>"></label><br>
    <label>Categoría: <input type="text" name="categoria" value="<?= htmlspecialchars($libro['categoria']) ?>"></label><br>
    <label>Cantidad: <input type="number" name="cantidad" value="<?= $libro['cantidad'] ?>" min="0" required></label><br>
    <button type="submit">Guardar cambios</button>
    <a href="libros.php">Cancelar</a>
  </form>
</body>
</html>

<?php
require_once 'config/conexion.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("DELETE FROM libros WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: libros.php");
exit;

