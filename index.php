<?php
// Conexão com Firebird usando PDO
$host = 'localhost';
$path = 'C:/Ello/Dados/TARUMAS.ELLO'; // Caminho deve ter /, não \.
$user = 'SYSDBA';
$pass = 'masterkey';
$pesquisa = isset($_GET['descricao']) ? strtoupper($_GET['descricao']) : '';

try {
    $pdo = new PDO("firebird:dbname=$host:$path;charset=UTF8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta com LIKE e FIRST
    $sql = "SELECT FIRST 20 
            P.IDPRODUTO, 
            P.DESCRICAO, 
            P.VAREJOPRECO,
            F.FOTO
        FROM TESTPRODUTO P
        LEFT JOIN TFOTOS F ON F.IDPRODUTO = P.IDPRODUTO
        WHERE P.DESCRICAO LIKE :desc";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':desc' => "%$pesquisa%"]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
<head>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<!-- HTML -->
<form method="get">
    <label for="descricao" class="descricao">Descrição:</label>
    <input type="text" name="descricao" id="descricao" class="descricao" placeholder="Pesquisar produto..." value="<?= htmlspecialchars($pesquisa) ?>">
    <button type="submit" class="buscar">Buscar</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Descrição</th>
        <th>Preço</th>
        <th>Foto</th>
    </tr>
    <?php if (!empty($produtos)): ?>
        <?php foreach ($produtos as $row): ?>
            <tr>
                <td class="center"><?= htmlspecialchars($row['IDPRODUTO']) ?></td>
                <td><?= htmlspecialchars($row['DESCRICAO']) ?></td>
                <td class="center">R$ <?= number_format($row['VAREJOPRECO'], 2, ',', '.') ?></td>
                <td class="center">
                    <?php if (!empty($row['FOTO'])): ?>
                        <img class="foto" src="data:image/jpeg;base64,<?= base64_encode($row['FOTO']) ?>" width="80" height="80">
                    <?php else: ?>
                        Sem imagem
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="2">Nenhum resultado encontrado.</td></tr>
    <?php endif; ?>
</table>
