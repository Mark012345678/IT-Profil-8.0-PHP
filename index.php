<?php
// načtení a dekódování JSON dat
$json = @file_get_contents('profile.json');
$data = $json ? json_decode($json, true) : [];
$name = isset($data['name']) ? $data['name'] : 'Neznámý uživatel';
$skills = isset($data['skills']) && is_array($data['skills']) ? $data['skills'] : [];
$projects = isset($data['projects']) && is_array($data['projects']) ? $data['projects'] : [];

// připravíme proměnné pro notifikace
$message = '';
$messageType = '';

// zpracování formuláře pro přidání zájmu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_interest'])) {
    $new = trim($_POST['new_interest']);
    if ($new === '') {
        $message = 'Pole nesmí být prázdné.';
        $messageType = 'error';
    } else {
        // porovnáme bez diakritiky/velikosti písmen
        $lowered = strtolower($new);
        $existing = array_map('strtolower', $projects);
        if (in_array($lowered, $existing, true)) {
            $message = 'Tento zájem už existuje.';
            $messageType = 'error';
        } else {
            // přidáme a uložíme zpět do JSON
            $projects[] = $new;
            $data['projects'] = $projects;
            file_put_contents('profile.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $message = 'Zájem byl úspěšně přidán.';
            $messageType = 'success';
        }
    }
}
?>
<!doctype html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>IT Profil - <?php echo htmlspecialchars($name); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1><?php echo htmlspecialchars($name); ?></h1>
  </header>
  <main>
    <section>
      <h2>Dovednosti</h2>
      <?php if (!empty($skills)): ?>
        <ul>
          <?php foreach ($skills as $skill): ?>
            <li><?php echo htmlspecialchars($skill); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné dovednosti nebyly uvedeny.</p>
      <?php endif; ?>
    </section>
    <section>
      <h2>Projekty / Zájmy</h2>
      <?php if (!empty($projects)): ?>
        <ul>
          <?php foreach ($projects as $p): ?>
            <li><?php echo htmlspecialchars($p); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné projekty nebo zájmy nebyly uvedeny.</p>
      <?php endif; ?>

      <!-- zpráva o výsledku formuláře -->
      <?php if (!empty($message)): ?>
        <p class="<?php echo htmlspecialchars($messageType); ?>">
          <?php echo htmlspecialchars($message); ?>
        </p>
      <?php endif; ?>

      <!-- formulář pro nový zájem -->
      <form method="POST">
        <input type="text" name="new_interest" required>
        <button type="submit">Přidat zájem</button>
      </form>
    </section>
  </main>
</body>
</html>
